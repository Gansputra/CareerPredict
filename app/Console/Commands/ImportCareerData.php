<?php

namespace App\Console\Commands;

use App\Models\JobListing;
use App\Models\JobCategory;
use App\Models\Skill;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportCareerData extends Command
{
    protected $signature = 'app:import-career-data';
    protected $description = 'Import career data from Kaggle XLSX file';

    public function handle()
    {
        $filePath = base_path('imports/career_dataset_large.xlsx');
        $this->info("Loading file: {$filePath}");

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        $header = array_shift($rows);
        $this->info("Starting import of " . count($rows) . " records...");

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            // 1. Get or Create Category (Specialization)
            $categoryName = $data['Specialization'] ?? 'General';
            $category = \App\Helpers\CategoryResolver::resolve($categoryName, $data['Recommended Career']);

            // 2. Create Job Listing (Recommended Career)
            $jobTitle = $data['Recommended Career'];
            $job = JobListing::firstOrCreate(
                ['title' => $jobTitle, 'category_id' => $category->id],
                [
                    'company_name' => 'Kaggle Career Dataset',
                    'location' => 'Flexible',
                    'salary_range' => 'Competitive',
                    'type' => 'Full-time',
                    'description' => "Career path for {$jobTitle}. Requirements include specialization in {$categoryName}. Certifications: {$data['Certifications']}.",
                    'requirements' => $data['Skills'],
                    'slug' => Str::slug($jobTitle . '-' . Str::random(5)),
                    'is_active' => true,
                ]
            );

            // 3. Extract and Seed Skills
            $skills = explode(',', $data['Skills']);
            foreach ($skills as $skillName) {
                $skillName = trim($skillName);
                if (!empty($skillName)) {
                    Skill::firstOrCreate(
                        ['name' => $skillName],
                        ['category' => $category->name]
                    );
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nImport completed successfully!");
    }
}
