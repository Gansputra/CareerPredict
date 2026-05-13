<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\JobListing;
use App\Models\JobCategory;
use Illuminate\Support\Str;

class JobImportController extends Controller
{
    public function index()
    {
        return view('admin.jobs.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ',');

        $imported = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            $row = array_combine($header, $data);

            // Find or create category
            $category = JobCategory::firstOrCreate(
                ['slug' => Str::slug($row['category'])],
                ['name' => $row['category']]
            );

            JobListing::updateOrCreate(
                ['title' => $row['job_title'], 'company_name' => $row['company_name'] ?? 'Unknown'],
                [
                    'category_id' => $category->id,
                    'location' => $row['location'],
                    'salary_range' => $row['salary'] ?? 'Competitive',
                    'description' => $row['description'],
                    'requirements' => $row['skills'],
                    'slug' => Str::slug($row['job_title'] . '-' . Str::random(5)),
                    'type' => 'Full-time',
                    'is_active' => true,
                ]
            );
            $imported++;
        }

        fclose($handle);

        return back()->with('success', "Successfully imported $imported jobs from CSV.");
    }
}
