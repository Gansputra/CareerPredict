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

        $lower = strtolower($range);

        // 1. Detect if it's USD or has dollar sign
        $isUsd = str_contains($lower, '$') || str_contains($lower, 'usd');

        if ($isUsd) {
            // Remove commas (thousand separators in USD)
            $clean = str_replace(',', '', $lower);
            
            // Normalize 'k' to '000' (handling decimal 'k' like 12.5k -> 12500)
            $clean = preg_replace_callback('/(\d+(?:\.\d+)?)\s*k/i', function($m) {
                return (string)((float)$m[1] * 1000);
            }, $clean);

            // Clean other text except numbers and decimal dots
            $clean = str_replace(['usd', ' '], '', $clean);

            // Extract float numbers
            preg_match_all('/(\d+(?:\.\d+)?)/', $clean, $matches);
        } else {
            // IDR logic: remove dots and commas completely
            $clean = str_replace(['rp', 'idr', '.', ',', ' '], '', $lower);

            // Extract integer numbers
            preg_match_all('/(\d+)/', $clean, $matches);
        }

        if (empty($matches[1])) return null;

        $numbers = array_map('floatval', $matches[1]);
        $min = min($numbers);
        $max = max($numbers);

        if ($isUsd) {
            $isHourly = str_contains($lower, '/hr') || str_contains($lower, '/hour') || str_contains($lower, 'hour') || str_contains($lower, 'hr');
            $isYearly = str_contains($lower, '/year') || str_contains($lower, '/yr') || str_contains($lower, 'year') || str_contains($lower, 'yr') || str_contains($lower, 'annual') || $max > 2000;

            if ($isHourly) {
                // Convert hourly to monthly (assuming 160 hours per month)
                $minMonthlyUsd = $min * 160;
                $maxMonthlyUsd = $max * 160;
            } elseif ($isYearly) {
                // Convert yearly to monthly (divide by 12)
                $minMonthlyUsd = $min / 12;
                $maxMonthlyUsd = $max / 12;
            } else {
                // Assume already monthly
                $minMonthlyUsd = $min;
                $maxMonthlyUsd = $max;
            }

            // Convert to IDR (using 1 USD = 16,000 IDR)
            $exchangeRate = 16000;
            $min = $minMonthlyUsd * $exchangeRate;
            $max = $maxMonthlyUsd * $exchangeRate;
        } else {
            // IDR logic: scale up if written in abbreviated form (e.g. 10 - 15 Juta or 10.000 - 15.000)
            $numbers = array_map(function ($n) {
                return $n < 1000 ? $n * 1000000 : ($n < 1000000 ? $n * 1000 : $n);
            }, [$min, $max]);
            $min = min($numbers);
            $max = max($numbers);
        }

        $avg = ($min + $max) / 2;

        if ($avg < 1000000) return null; // Too low, probably parsing error

        return ['min' => (int)$min, 'max' => (int)$max, 'avg' => (int)$avg];
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
