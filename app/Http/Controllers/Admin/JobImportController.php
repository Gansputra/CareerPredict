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

            $cat = JobCategory::firstOrCreate(
                ['slug' => Str::slug($category)],
                ['name' => $category]
            );

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
        $source = $request->input('source', 'all');
        $limit = (int) $request->input('limit', 30);

        $imported = 0;
        $errors = [];

        // Fetch from Remotive
        if ($source === 'all' || $source === 'remotive') {
            try {
                $response = Http::timeout(30)->get('https://remotive.com/api/remote-jobs', ['limit' => $limit]);
                if ($response->successful()) {
                    $jobs = $response->json('jobs') ?? [];
                    foreach (array_slice($jobs, 0, $limit) as $job) {
                        $catName = $job['category'] ?? 'Technology';
                        $cat = JobCategory::firstOrCreate(
                            ['slug' => Str::slug($catName)],
                            ['name' => $catName]
                        );

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
                $response = Http::timeout(30)->get('https://www.arbeitnow.com/api/job-board-api');
                if ($response->successful()) {
                    $jobs = $response->json('data') ?? [];
                    foreach (array_slice($jobs, 0, $limit) as $job) {
                        $catName = !empty($job['tags']) ? $job['tags'][0] : 'Technology';
                        $cat = JobCategory::firstOrCreate(
                            ['slug' => Str::slug($catName)],
                            ['name' => $catName]
                        );

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
