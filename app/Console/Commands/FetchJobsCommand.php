<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobListing;
use App\Models\JobCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class FetchJobsCommand extends Command
{
    protected $signature = 'jobs:fetch 
                            {--source=all : Source to fetch from (remotive, arbeitnow, jsearch, all)}
                            {--limit=50 : Maximum number of jobs to fetch per source}
                            {--clear : Clear existing jobs before fetching}';

    protected $description = 'Fetch real job listings from free public job APIs (no API key needed)';

    private int $imported = 0;
    private int $skipped = 0;

    public function handle(): int
    {
        $this->info('🚀 CareerPredict Job Fetcher');
        $this->info('=============================');

        if ($this->option('clear')) {
            if ($this->confirm('This will delete ALL existing job listings. Continue?')) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                JobListing::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                $this->warn('🗑️  All existing jobs cleared.');
            }
        }

        $source = $this->option('source');
        $limit = (int) $this->option('limit');

        if ($limit <= 0) {
            $this->info('📊 Total jobs in database: ' . JobListing::count());
            return Command::SUCCESS;
        }

        $sources = match ($source) {
            'remotive' => ['remotive'],
            'arbeitnow' => ['arbeitnow'],
            'jsearch' => ['jsearch'],
            'all' => ['remotive', 'arbeitnow', 'jsearch'],
            default => ['remotive', 'arbeitnow', 'jsearch'],
        };

        foreach ($sources as $src) {
            $this->newLine();
            $this->info("📡 Fetching from: " . strtoupper($src));

            match ($src) {
                'remotive' => $this->fetchRemotive($limit),
                'arbeitnow' => $this->fetchArbeitnow($limit),
                'jsearch' => $this->fetchJSearch($limit),
            };
        }

        $this->newLine();
        $this->info('=============================');
        $this->info("✅ Import complete! {$this->imported} jobs imported, {$this->skipped} skipped (duplicates).");
        $this->info("📊 Total jobs in database: " . JobListing::count());

        return Command::SUCCESS;
    }

    /**
     * Fetch jobs from Remotive API (100% free, no key needed)
     * https://remotive.com/api/remote-jobs
     */
    private function fetchRemotive(int $limit): void
    {
        try {
            $response = Http::withoutVerifying()->timeout(30)->get('https://remotive.com/api/remote-jobs', [
                'limit' => $limit,
            ]);

            if (!$response->successful()) {
                $this->error("  ❌ Remotive API returned status: {$response->status()}");
                return;
            }

            $data = $response->json();
            $jobs = $data['jobs'] ?? [];

            $bar = $this->output->createProgressBar(count($jobs));
            $bar->start();

            foreach ($jobs as $job) {
                $this->saveJob([
                    'title' => $job['title'] ?? 'Untitled',
                    'company_name' => $job['company_name'] ?? 'Unknown Company',
                    'category' => $this->mapCategory($job['category'] ?? 'Other'),
                    'location' => $job['candidate_required_location'] ?? 'Remote',
                    'type' => $this->mapJobType($job['job_type'] ?? 'full_time'),
                    'salary_range' => $this->extractSalary($job['salary'] ?? ''),
                    'description' => $this->cleanHtml($job['description'] ?? ''),
                    'requirements' => $this->extractRequirements($job['description'] ?? ''),
                    'url' => $job['url'] ?? null,
                ]);

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("  ✓ Processed " . count($jobs) . " jobs from Remotive");

        } catch (\Exception $e) {
            $this->error("  ❌ Remotive error: " . $e->getMessage());
        }
    }

    /**
     * Fetch jobs from Arbeitnow API (100% free, no key needed)
     * https://www.arbeitnow.com/api/job-board-api
     */
    private function fetchArbeitnow(int $limit): void
    {
        try {
            $allJobs = [];
            $page = 1;
            $maxPages = ceil($limit / 100);

            while (count($allJobs) < $limit && $page <= $maxPages) {
                $response = Http::withoutVerifying()->timeout(30)->get("https://www.arbeitnow.com/api/job-board-api", [
                    'page' => $page,
                ]);

                if (!$response->successful()) {
                    $this->error("  ❌ Arbeitnow API returned status: {$response->status()}");
                    return;
                }

                $data = $response->json();
                $jobs = $data['data'] ?? [];

                if (empty($jobs))
                    break;

                $allJobs = array_merge($allJobs, $jobs);
                $page++;
            }

            $allJobs = array_slice($allJobs, 0, $limit);

            $bar = $this->output->createProgressBar(count($allJobs));
            $bar->start();

            foreach ($allJobs as $job) {
                $this->saveJob([
                    'title' => $job['title'] ?? 'Untitled',
                    'company_name' => $job['company_name'] ?? 'Unknown Company',
                    'category' => $this->mapCategory($this->guessCategory($job['tags'] ?? [], $job['title'] ?? '')),
                    'location' => $job['location'] ?? 'Remote',
                    'type' => $job['remote'] ? 'Remote' : 'Full-time',
                    'salary_range' => 'Competitive',
                    'description' => $this->cleanHtml($job['description'] ?? ''),
                    'requirements' => $this->extractRequirements($job['description'] ?? ''),
                    'url' => $job['url'] ?? null,
                ]);

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("  ✓ Processed " . count($allJobs) . " jobs from Arbeitnow");

        } catch (\Exception $e) {
            $this->error("  ❌ Arbeitnow error: " . $e->getMessage());
        }
    }

    /**
     * Save a job to the database with deduplication
     */
    private function saveJob(array $data): void
    {
        $category = JobCategory::firstOrCreate(
            ['slug' => Str::slug($data['category'])],
            ['name' => $data['category']]
        );

        $existing = JobListing::where('title', $data['title'])
            ->where('company_name', $data['company_name'])
            ->first();

        if ($existing) {
            $this->skipped++;
            return;
        }

        JobListing::create([
            'category_id' => $category->id,
            'title' => Str::limit($data['title'], 250),
            'slug' => Str::slug($data['title'] . '-' . Str::random(5)),
            'description' => Str::limit($data['description'], 10000),
            'requirements' => Str::limit($data['requirements'], 5000),
            'location' => Str::limit($data['location'], 250),
            'salary_range' => $data['salary_range'] ?: 'Competitive',
            'type' => $data['type'],
            'company_name' => Str::limit($data['company_name'], 250),
            'url' => $data['url'] ?? null,
            'is_active' => true,
        ]);

        $this->imported++;
    }

    /**
     * Map API category names to our categories
     */
    private function mapCategory(string $category): string
    {
        $map = [
            'software-dev' => 'Technology',
            'software_dev' => 'Technology',
            'dev' => 'Technology',
            'data' => 'Technology',
            'devops' => 'Technology',
            'qa' => 'Technology',
            'engineering' => 'Engineering',
            'design' => 'Creative & Design',
            'product' => 'Management',
            'marketing' => 'Marketing',
            'sales' => 'Sales',
            'customer-support' => 'Sales',
            'customer_support' => 'Sales',
            'finance' => 'Finance',
            'finance-legal' => 'Finance',
            'finance_legal' => 'Finance',
            'hr' => 'Human Resources',
            'human-resources' => 'Human Resources',
            'medical' => 'Healthcare',
            'health' => 'Healthcare',
            'healthcare' => 'Healthcare',
            'education' => 'Education',
            'teaching' => 'Education',
            'writing' => 'Creative & Design',
            'business' => 'Management',
            'management' => 'Management',
            'all others' => 'Technology',
        ];

        $key = Str::slug($category, '_');
        return $map[$key] ?? $map[strtolower($category)] ?? ucfirst($category);
    }

    /**
     * Map API job type to our types
     */
    private function mapJobType(string $type): string
    {
        return match (strtolower(str_replace(['-', '_'], '', $type))) {
            'fulltime', 'full time' => 'Full-time',
            'parttime', 'part time' => 'Part-time',
            'contract' => 'Contract',
            'freelance' => 'Contract',
            'internship' => 'Part-time',
            default => 'Full-time',
        };
    }

    /**
     * Guess a category from job tags and title
     */
    private function guessCategory(array $tags, string $title): string
    {
        $combined = strtolower(implode(' ', $tags) . ' ' . $title);

        $categoryKeywords = [
            'Technology' => ['software', 'developer', 'engineer', 'programming', 'backend', 'frontend', 'fullstack', 'devops', 'cloud', 'data', 'machine learning', 'ai', 'python', 'javascript', 'java', 'php', 'react', 'node', 'aws', 'azure', 'api', 'ios', 'android', 'mobile'],
            'Creative & Design' => ['design', 'ux', 'ui', 'graphic', 'creative', 'art', 'illustration', 'figma', 'photoshop'],
            'Marketing' => ['marketing', 'seo', 'content', 'social media', 'growth', 'brand', 'copywriting', 'ads'],
            'Finance' => ['finance', 'accounting', 'financial', 'tax', 'audit', 'banking', 'investment'],
            'Sales' => ['sales', 'account executive', 'business development', 'customer', 'support'],
            'Healthcare' => ['health', 'medical', 'nurse', 'doctor', 'clinical', 'pharma'],
            'Education' => ['education', 'teacher', 'tutor', 'instructor', 'training', 'learning'],
            'Human Resources' => ['hr', 'human resources', 'recruiter', 'recruitment', 'people ops', 'talent'],
            'Engineering' => ['mechanical', 'electrical', 'civil', 'structural', 'hardware'],
            'Management' => ['manager', 'director', 'lead', 'head of', 'vp', 'chief', 'product', 'project'],
        ];

        foreach ($categoryKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($combined, $keyword)) {
                    return $category;
                }
            }
        }

        return 'Technology';
    }

    /**
     * Clean HTML tags from description
     */
    private function cleanHtml(string $html): string
    {
        // Convert common HTML elements to readable text
        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = preg_replace('/<\/?(p|div|h[1-6]|li|ul|ol)[^>]*>/i', "\n", $text);
        $text = preg_replace('/<[^>]+>/', '', $text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        return trim($text);
    }

    /**
     * Extract requirements section from job description
     */
    private function extractRequirements(string $html): string
    {
        $text = $this->cleanHtml($html);

        // Try to find a requirements/qualifications section
        $patterns = [
            '/(?:requirements|qualifications|what we.+look|what you.+need|you should have|must have)[\s:]*\n(.+?)(?:\n\n|$)/is',
            '/(?:skills|experience)[\s:]*\n(.+?)(?:\n\n|$)/is',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return trim($matches[1]);
            }
        }

        // Fallback: take first 500 chars
        return Str::limit($text, 500);
    }

    /**
     * Extract salary from various formats
     */
    private function extractSalary(string $salary): string
    {
        if (empty($salary) || $salary === '' || $salary === 'null') {
            return 'Competitive';
        }
        return $salary;
    }

    /**
     * Fetch jobs from JSearch API (via RapidAPI)
     */
    private function fetchJSearch(int $limit): void
    {
        $apiKey = env('RAPIDAPI_KEY');
        if (empty($apiKey)) {
            $this->error("  ❌ JSearch API Key (RAPIDAPI_KEY) is not configured in your .env file!");
            return;
        }

        try {
            $allJobs = [];
            $page = 1;
            $itemsPerPage = 10;
            $maxPages = ceil($limit / $itemsPerPage);

            $this->info("  🔍 Querying JSearch for Indonesian jobs...");

            while (count($allJobs) < $limit && $page <= $maxPages) {
                if ($page > 1) {
                    sleep(2);
                }

                $response = Http::withoutVerifying()->withHeaders([
                    'X-RapidAPI-Key' => $apiKey,
                    'X-RapidAPI-Host' => 'jsearch.p.rapidapi.com'
                ])->timeout(30)->get('https://jsearch.p.rapidapi.com/search', [
                    'query' => 'jobs in new york',
                    'page' => $page,
                    'num_pages' => 1,
                    'country' => 'us',
                ]);

                if (!$response->successful()) {
                    $this->error("  ❌ JSearch API returned status: {$response->status()} - " . $response->body());
                    break;
                }

                $data = $response->json();
                $jobs = $data['data']['jobs'] ?? $data['data'] ?? [];

                if (empty($jobs))
                    break;

                $allJobs = array_merge($allJobs, $jobs);
                $page++;
            }

            $allJobs = array_slice($allJobs, 0, $limit);

            $bar = $this->output->createProgressBar(count($allJobs));
            $bar->start();

            foreach ($allJobs as $job) {
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
                $salary_range = 'Competitive';
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

                $this->saveJob([
                    'title' => $job['job_title'] ?? 'Untitled',
                    'company_name' => $job['employer_name'] ?? 'Unknown Company',
                    'category' => $this->mapCategory($this->guessCategory([], $job['job_title'] ?? '')),
                    'location' => $location,
                    'type' => $type,
                    'salary_range' => $salary_range,
                    'description' => $job['job_description'] ?? '',
                    'requirements' => $this->extractRequirements($job['job_description'] ?? ''),
                    'url' => $job['job_apply_link'] ?? null,
                ]);

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("  ✓ Processed " . count($allJobs) . " jobs from JSearch");

        } catch (\Exception $e) {
            $this->error("  ❌ JSearch error: " . $e->getMessage());
        }
    }
}
