<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobListing;
use App\Models\JobCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;

class JobImportController extends Controller
{
    public function index()
    {
        $stats = [
            'total_jobs' => JobListing::count(),
            'active_jobs' => JobListing::where('is_active', true)->count(),
            'categories' => JobCategory::count(),
        ];

        return view('admin.jobs.import', compact('stats'));
    }

    /**
     * Import jobs from CSV dataset file.
     */
    public function importCsv(Request $request)
    {
        set_time_limit(180);
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 10000, ',');

        // Normalize headers (trim, lowercase)
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        $imported = 0;
        $skipped = 0;

        while (($data = fgetcsv($handle, 10000, ',')) !== FALSE) {
            if (count($data) !== count($header)) {
                $skipped++;
                continue;
            }

            $row = array_combine($header, $data);

            // Flexible column mapping
            $title = $row['job_title'] ?? $row['title'] ?? $row['position'] ?? $row['role'] ?? null;
            $company = $row['company_name'] ?? $row['company'] ?? $row['employer'] ?? 'Unknown';
            $location = $row['location'] ?? $row['city'] ?? $row['job_location'] ?? 'Remote';
            $salary = $row['salary'] ?? $row['salary_range'] ?? $row['compensation'] ?? 'Negotiable';
            $description = $row['description'] ?? $row['job_description'] ?? $row['details'] ?? '-';
            $requirements = $row['skills'] ?? $row['requirements'] ?? $row['qualifications'] ?? '-';
            $category = $row['category'] ?? $row['industry'] ?? $row['department'] ?? 'General';
            $type = $row['type'] ?? $row['job_type'] ?? $row['employment_type'] ?? 'Full-time';

            if (!$title) {
                $skipped++;
                continue;
            }

            $cat = \App\Helpers\CategoryResolver::resolve($category, $title);

            JobListing::updateOrCreate(
                ['title' => $title, 'company_name' => $company],
                [
                    'category_id' => $cat->id,
                    'location' => $location,
                    'salary_range' => $salary,
                    'description' => $description,
                    'requirements' => $requirements,
                    'slug' => Str::slug($title . '-' . Str::random(5)),
                    'type' => $type,
                    'is_active' => true,
                ]
            );
            $imported++;
        }

        fclose($handle);

        return back()->with('success', "✅ CSV Import complete! $imported jobs imported, $skipped skipped.");
    }

    /**
     * Fetch jobs from public APIs (Remotive, Arbeitnow).
     */
    public function importApi(Request $request)
    {
        set_time_limit(180);
        $source = $request->input('source', 'all');
        $limit = (int) $request->input('limit', 30);

        $imported = 0;
        $errors = [];

        // Fetch from Remotive
        if ($source === 'all' || $source === 'remotive') {
            try {
                $response = Http::withoutVerifying()
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                    ])
                    ->withOptions([
                        'curl' => [
                            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
                        ]
                    ])
                    ->timeout(30)
                    ->get('https://remotive.com/api/remote-jobs', ['limit' => $limit]);
                if ($response->successful()) {
                    $jobs = $response->json('jobs') ?? [];
                    foreach (array_slice($jobs, 0, $limit) as $job) {
                        $cat = \App\Helpers\CategoryResolver::resolve($job['category'] ?? 'Technology', $job['title']);

                        JobListing::updateOrCreate(
                            ['title' => $job['title'], 'company_name' => $job['company_name'] ?? 'Unknown'],
                            [
                                'category_id' => $cat->id,
                                'location' => $job['candidate_required_location'] ?? 'Remote',
                                'salary_range' => $job['salary'] ?: 'Negotiable',
                                'description' => strip_tags($job['description'] ?? '-'),
                                'requirements' => strip_tags($job['description'] ?? '-'),
                                'slug' => Str::slug($job['title'] . '-' . Str::random(5)),
                                'type' => $job['job_type'] ?? 'Full-time',
                                'url' => $job['url'] ?? null,
                                'is_active' => true,
                            ]
                        );
                        $imported++;
                    }
                }
            } catch (\Exception $e) {
                $errors[] = 'Remotive: ' . $e->getMessage();
            }
        }

        // Fetch from Arbeitnow
        if ($source === 'all' || $source === 'arbeitnow') {
            try {
                $response = Http::withoutVerifying()
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                    ])
                    ->withOptions([
                        'curl' => [
                            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
                        ]
                    ])
                    ->timeout(30)
                    ->get('https://www.arbeitnow.com/api/job-board-api');
                if ($response->successful()) {
                    $jobs = $response->json('data') ?? [];
                    foreach (array_slice($jobs, 0, $limit) as $job) {
                        $catName = !empty($job['tags']) ? $job['tags'][0] : 'Technology';
                        $cat = \App\Helpers\CategoryResolver::resolve($catName, $job['title']);

                        JobListing::updateOrCreate(
                            ['title' => $job['title'], 'company_name' => $job['company_name'] ?? 'Unknown'],
                            [
                                'category_id' => $cat->id,
                                'location' => $job['location'] ?? 'Remote',
                                'salary_range' => 'Negotiable',
                                'description' => strip_tags($job['description'] ?? '-'),
                                'requirements' => implode(', ', $job['tags'] ?? []),
                                'slug' => Str::slug($job['title'] . '-' . Str::random(5)),
                                'type' => ($job['remote'] ?? false) ? 'Remote' : 'Full-time',
                                'url' => $job['url'] ?? null,
                                'is_active' => true,
                            ]
                        );
                        $imported++;
                    }
                }
            } catch (\Exception $e) {
                $errors[] = 'Arbeitnow: ' . $e->getMessage();
            }
        }

        // Fetch from JSearch (Indonesia)
        if ($source === 'all' || $source === 'jsearch') {
            $apiKey = env('RAPIDAPI_KEY');
            if (empty($apiKey)) {
                $errors[] = 'JSearch: RAPIDAPI_KEY is not configured in .env';
            } else {
                try {
                    $allJobs = [];
                    $page = 1;
                    $itemsPerPage = 10;
                    $maxPages = ceil($limit / $itemsPerPage);

                    while (count($allJobs) < $limit && $page <= $maxPages) {
                        if ($page > 1) {
                            sleep(2);
                        }

                        $response = Http::withoutVerifying()
                            ->withHeaders([
                                'X-RapidAPI-Key' => $apiKey,
                                'X-RapidAPI-Host' => 'jsearch.p.rapidapi.com',
                                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                            ])
                            ->withOptions([
                                'curl' => [
                                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
                                ]
                            ])
                            ->timeout(30)
                            ->get('https://jsearch.p.rapidapi.com/search', [
                            'query' => 'jobs in new york',
                            'page' => $page,
                            'num_pages' => 1,
                            'country' => 'us',
                        ]);

                        if ($response->successful()) {
                            $jobs = $response->json('data.jobs') ?? $response->json('data') ?? [];
                            if (empty($jobs))
                                break;
                            $allJobs = array_merge($allJobs, $jobs);
                            $page++;
                        } else {
                            $errors[] = 'JSearch response failed with status: ' . $response->status();
                            break;
                        }
                    }

                    $allJobs = array_slice($allJobs, 0, $limit);

                    foreach ($allJobs as $job) {
                        $title = $job['job_title'] ?? 'Untitled';
                        $cat = \App\Helpers\CategoryResolver::resolve(null, $title);

                        // Map employment type
                        $type = 'Full-time';
                        if (isset($job['job_employment_type'])) {
                            $type = match (strtoupper($job['job_employment_type'])) {
                                'FULLTIME' => 'Full-time',
                                'PARTTIME' => 'Part-time',
                                'CONTRACT' => 'Contract',
                                'INTERN' => 'Part-time',
                                default => 'Full-time',
                            };
                        }

                        // Format salary range
                        $salary_range = 'Negotiable';
                        if (!empty($job['job_min_salary']) && !empty($job['job_max_salary'])) {
                            $currency = $job['job_salary_currency'] ?? 'IDR';
                            if ($currency === 'IDR') {
                                $salary_range = number_format($job['job_min_salary'], 0, ',', '.') . ' - ' . number_format($job['job_max_salary'], 0, ',', '.');
                            } else {
                                $salary_range = $currency . ' ' . number_format($job['job_min_salary']) . ' - ' . number_format($job['job_max_salary']);
                            }
                        }

                        $location = ($job['job_city'] && $job['job_state']) ? $job['job_city'] . ', ' . $job['job_state'] : ($job['job_city'] ?: ($job['job_location'] ?? ''));
                        $country = $job['job_country'] ?? '';
                        if (!empty($country)) {
                            $location = !empty($location) ? $location . ', ' . $country : $country;
                        }

                        JobListing::updateOrCreate(
                            ['title' => $title, 'company_name' => $job['employer_name'] ?? 'Unknown'],
                            [
                                'category_id' => $cat->id,
                                'location' => $location,
                                'salary_range' => $salary_range,
                                'description' => strip_tags($job['job_description'] ?? '-'),
                                'requirements' => strip_tags($job['job_description'] ?? '-'),
                                'slug' => Str::slug($title . '-' . Str::random(5)),
                                'type' => $type,
                                'url' => $job['job_apply_link'] ?? null,
                                'is_active' => true,
                            ]
                        );
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $errors[] = 'JSearch: ' . $e->getMessage();
                }
            }
        }

        $message = "✅ API Fetch complete! $imported jobs imported.";
        if (!empty($errors)) {
            $message .= ' ⚠️ Errors: ' . implode(', ', $errors);
        }

        return back()->with('success', $message);
    }

    /**
     * Clear all existing job listings.
     */
    public function clearJobs(Request $request)
    {
        JobListing::query()->delete();

        // Optionally delete categories that are no longer used
        $categoriesWithJobs = JobListing::pluck('category_id')->unique();
        JobCategory::whereNotIn('id', $categoriesWithJobs)->delete();

        return back()->with('success', '🗑️ All job listings have been successfully deleted.');
    }
}
