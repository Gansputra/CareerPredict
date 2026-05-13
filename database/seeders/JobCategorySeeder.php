<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology'],
            ['name' => 'Healthcare', 'slug' => 'healthcare'],
            ['name' => 'Finance', 'slug' => 'finance'],
            ['name' => 'Education', 'slug' => 'education'],
            ['name' => 'Marketing', 'slug' => 'marketing'],
            ['name' => 'Creative & Design', 'slug' => 'creative-design'],
            ['name' => 'Engineering', 'slug' => 'engineering'],
            ['name' => 'Sales', 'slug' => 'sales'],
            ['name' => 'Human Resources', 'slug' => 'human-resources'],
            ['name' => 'Management', 'slug' => 'management'],
        ];

        foreach ($categories as $category) {
            \App\Models\JobCategory::create($category);
        }
    }
}
