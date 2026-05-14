<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobListing;
use App\Models\JobCategory;

class SalaryInsightsController extends Controller
{
    public function index()
    {
        // Get salary data from real job listings grouped by category
        $categories = JobCategory::withCount('jobs')->get();

        $salaryByCategory = [];
        foreach ($categories as $cat) {
            $jobs = JobListing::where('category_id', $cat->id)
                ->where('is_active', true)
                ->get();

            if ($jobs->isEmpty()) continue;

            // Parse salary ranges to get numeric values
            $salaries = $jobs->map(function ($job) {
                return $this->parseSalary($job->salary_range);
            })->filter();

            if ($salaries->isEmpty()) continue;

            $salaryByCategory[] = [
                'category' => $cat->name,
                'icon' => $this->getCategoryIcon($cat->name),
                'color' => $this->getCategoryColor($cat->name),
                'jobs_count' => $jobs->count(),
                'min' => $salaries->min('min'),
                'max' => $salaries->max('max'),
                'avg' => round($salaries->avg('avg')),
                'median' => $this->getMedian($salaries->pluck('avg')->toArray()),
            ];
        }

        // Sort by average salary descending
        usort($salaryByCategory, fn($a, $b) => $b['avg'] <=> $a['avg']);

        // Top paying roles (individual jobs)
        $topJobs = JobListing::with('category')
            ->where('is_active', true)
            ->get()
            ->map(function ($job) {
                $salary = $this->parseSalary($job->salary_range);
                return $salary ? array_merge($salary, [
                    'title' => $job->title,
                    'company' => $job->company_name,
                    'location' => $job->location,
                    'category' => $job->category->name ?? 'General',
                    'slug' => $job->slug,
                ]) : null;
            })
            ->filter()
            ->sortByDesc('avg')
            ->take(10)
            ->values();

        // Stats
        $allSalaries = collect($salaryByCategory);
        $overallStats = [
            'highest_avg' => $allSalaries->max('avg') ?? 0,
            'lowest_avg' => $allSalaries->min('avg') ?? 0,
            'categories' => $allSalaries->count(),
            'total_jobs' => JobListing::where('is_active', true)->count(),
        ];

        return view('salary.index', compact('salaryByCategory', 'topJobs', 'overallStats'));
    }

    private function parseSalary(?string $range): ?array
    {
        if (!$range) return null;

        // Clean string
        $clean = strtolower(str_replace(['rp', 'idr', '.', ',', ' '], '', $range));

        // Try to find numbers
        preg_match_all('/(\d+)/', $clean, $matches);

        if (empty($matches[1])) return null;

        $numbers = array_map('intval', $matches[1]);

        // Detect if numbers are in millions (less than 1000 probably means "juta")
        $numbers = array_map(function ($n) {
            return $n < 1000 ? $n * 1000000 : ($n < 1000000 ? $n * 1000 : $n);
        }, $numbers);

        $min = min($numbers);
        $max = max($numbers);
        $avg = ($min + $max) / 2;

        if ($avg < 1000000) return null; // Too low, probably parsing error

        return ['min' => $min, 'max' => $max, 'avg' => $avg];
    }

    private function getMedian(array $values): int
    {
        sort($values);
        $count = count($values);
        if ($count === 0) return 0;
        $mid = floor($count / 2);
        return $count % 2 !== 0 ? (int)$values[$mid] : (int)(($values[$mid - 1] + $values[$mid]) / 2);
    }

    private function getCategoryIcon(string $name): string
    {
        $icons = [
            'Technology' => 'fa-laptop-code',
            'Design' => 'fa-pen-nib',
            'Marketing' => 'fa-bullhorn',
            'Finance' => 'fa-chart-line',
            'Healthcare' => 'fa-heartbeat',
            'Education' => 'fa-graduation-cap',
            'Engineering' => 'fa-cogs',
            'Sales' => 'fa-handshake',
            'Management' => 'fa-users-cog',
        ];

        foreach ($icons as $key => $icon) {
            if (str_contains(strtolower($name), strtolower($key))) return $icon;
        }
        return 'fa-briefcase';
    }

    private function getCategoryColor(string $name): string
    {
        $colors = [
            'Technology' => 'blue',
            'Design' => 'purple',
            'Marketing' => 'amber',
            'Finance' => 'emerald',
            'Healthcare' => 'rose',
            'Education' => 'indigo',
            'Engineering' => 'cyan',
            'Sales' => 'orange',
        ];

        foreach ($colors as $key => $color) {
            if (str_contains(strtolower($name), strtolower($key))) return $color;
        }
        return 'slate';
    }
}
