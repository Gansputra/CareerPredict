<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobListing;
use App\Models\JobCategory;
use Illuminate\Support\Facades\DB;

class MarketTrendsController extends Controller
{
    public function index()
    {
        $jobs = JobListing::with('category')->where('is_active', true)->get();
        $totalJobs = $jobs->count();

        // ── Section 1: Hero Stats ─────────────────────────────────────────────
        $totalCategories = JobCategory::whereHas('jobs', fn($q) => $q->where('is_active', true))->count();

        // Top city
        $cityCount = [];
        foreach ($jobs as $job) {
            $city = $this->parseCity($job->location);
            if ($city) {
                $cityCount[$city] = ($cityCount[$city] ?? 0) + 1;
            }
        }
        arsort($cityCount);
        $topCity    = array_key_first($cityCount) ?? '-';
        $topCityCount = reset($cityCount) ?: 0;

        // Top skill (across all jobs)
        $skillFrequency = $this->aggregateSkills($jobs);
        arsort($skillFrequency);
        $topSkill = array_key_first($skillFrequency) ?? '-';

        $heroStats = [
            'total_jobs'       => $totalJobs,
            'total_categories' => $totalCategories,
            'top_city'         => $topCity,
            'top_city_count'   => $topCityCount,
            'top_skill'        => $topSkill,
        ];

        // ── Section 2: Jobs per Category (Donut) ─────────────────────────────
        $categoryDist = [];
        foreach ($jobs->groupBy('category_id') as $catId => $catJobs) {
            $cat = $catJobs->first()->category;
            if (!$cat) continue;
            $categoryDist[] = [
                'name'    => $cat->name,
                'count'   => $catJobs->count(),
                'percent' => $totalJobs > 0 ? round(($catJobs->count() / $totalJobs) * 100, 1) : 0,
                'color'   => $this->getCategoryColor($cat->name),
            ];
        }
        usort($categoryDist, fn($a, $b) => $b['count'] <=> $a['count']);

        // ── Section 3: Top Skills (Bar) ───────────────────────────────────────
        $topSkills = array_slice($skillFrequency, 0, 15, true);

        // ── Section 4: Job Type Distribution (Pie) ───────────────────────────
        $typeDist = [];
        foreach ($jobs->groupBy('type') as $type => $typeJobs) {
            if (!$type) continue;
            $typeDist[] = [
                'type'    => $type ?: 'Tidak Diketahui',
                'count'   => $typeJobs->count(),
                'percent' => $totalJobs > 0 ? round(($typeJobs->count() / $totalJobs) * 100, 1) : 0,
            ];
        }
        usort($typeDist, fn($a, $b) => $b['count'] <=> $a['count']);

        // ── Section 5: Top Cities (Ranking) ──────────────────────────────────
        $topCities = array_slice($cityCount, 0, 10, true);
        $maxCityCount = !empty($topCities) ? max($topCities) : 1;

        // ── Section 6: Monthly Trend (Line Chart) ─────────────────────────────
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $label = $date->format('M Y');
            $count = JobListing::where('is_active', true)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyTrend[] = ['label' => $label, 'count' => $count];
        }

        // ── Section 7: Top Hiring Companies ──────────────────────────────────
        $companyCounts = [];
        foreach ($jobs as $job) {
            $company = trim($job->company_name);
            if ($company) {
                $companyCounts[$company] = ($companyCounts[$company] ?? 0) + 1;
            }
        }
        arsort($companyCounts);
        $topCompanies = array_slice($companyCounts, 0, 9, true);

        // ── Section 8: Salary vs Demand Matrix ───────────────────────────────
        $salaryDemand = [];
        foreach (JobCategory::all() as $cat) {
            $catJobs = $jobs->where('category_id', $cat->id);
            if ($catJobs->isEmpty()) continue;

            $salaries = $catJobs->map(fn($j) => $this->parseSalary($j->salary_range))->filter();
            if ($salaries->isEmpty()) continue;

            $avgSalary = round($salaries->avg('avg') / 1_000_000, 1); // in Juta
            $demand    = $catJobs->count();

            $salaryDemand[] = [
                'category'   => $cat->name,
                'avg_salary' => $avgSalary,
                'demand'     => $demand,
                'color'      => $this->getCategoryColor($cat->name),
            ];
        }

        return view('market-trends.index', compact(
            'heroStats',
            'categoryDist',
            'topSkills',
            'typeDist',
            'topCities',
            'maxCityCount',
            'monthlyTrend',
            'topCompanies',
            'salaryDemand',
            'totalJobs'
        ));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function parseCity(?string $location): ?string
    {
        if (!$location) return null;
        $parts = explode(',', $location);
        $city  = trim($parts[0]);
        return $city ?: null;
    }

    private function aggregateSkills($jobs): array
    {
        $freq = [];
        foreach ($jobs as $job) {
            $skills = $job->getRequiredSkills();
            foreach ($skills as $skill) {
                $freq[$skill] = ($freq[$skill] ?? 0) + 1;
            }
        }
        return $freq;
    }

    private function parseSalary(?string $range): ?array
    {
        if (!$range) return null;
        $lower  = strtolower($range);
        $isUsd  = str_contains($lower, '$') || str_contains($lower, 'usd');

        if ($isUsd) {
            $clean = str_replace(',', '', $lower);
            $clean = preg_replace_callback('/(\d+(?:\.\d+)?)\s*k/i', fn($m) => (string)((float)$m[1] * 1000), $clean);
            $clean = str_replace(['usd', ' '], '', $clean);
            preg_match_all('/(\d+(?:\.\d+)?)/', $clean, $matches);
        } else {
            $clean = str_replace(['rp', 'idr', '.', ',', ' '], '', $lower);
            preg_match_all('/(\d+)/', $clean, $matches);
        }

        if (empty($matches[1])) return null;
        $numbers = array_map('floatval', $matches[1]);
        $min = min($numbers);
        $max = max($numbers);

        if ($isUsd) {
            $isYearly = str_contains($lower, '/year') || str_contains($lower, 'annual') || $max > 2000;
            $isHourly = str_contains($lower, '/hr') || str_contains($lower, 'hour');
            $min = $isHourly ? $min * 160 : ($isYearly ? $min / 12 : $min);
            $max = $isHourly ? $max * 160 : ($isYearly ? $max / 12 : $max);
            $min *= 16000; $max *= 16000;
        } else {
            $scale = fn($n) => $n < 1000 ? $n * 1_000_000 : ($n < 1_000_000 ? $n * 1000 : $n);
            $min = $scale($min); $max = $scale($max);
        }

        $avg = ($min + $max) / 2;
        if ($avg < 1_000_000) return null;
        return ['min' => (int)$min, 'max' => (int)$max, 'avg' => (int)$avg];
    }

    private function getCategoryColor(string $name): string
    {
        $map = [
            'teknologi'  => '#3b82f6',
            'technology' => '#3b82f6',
            'keuangan'   => '#10b981',
            'finance'    => '#10b981',
            'pemasaran'  => '#f59e0b',
            'marketing'  => '#f59e0b',
            'desain'     => '#a855f7',
            'design'     => '#a855f7',
            'kreatif'    => '#a855f7',
            'kesehatan'  => '#ef4444',
            'health'     => '#ef4444',
            'pendidikan' => '#6366f1',
            'education'  => '#6366f1',
            'rekayasa'   => '#06b6d4',
            'engineering'=> '#06b6d4',
            'teknik'     => '#06b6d4',
            'penjualan'  => '#f97316',
            'sales'      => '#f97316',
            'sumber daya'=> '#8b5cf6',
            'manajemen'  => '#14b8a6',
            'management' => '#14b8a6',
        ];
        $lower = strtolower($name);
        foreach ($map as $key => $color) {
            if (str_contains($lower, $key)) return $color;
        }
        return '#64748b';
    }
}
