<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $categories = \App\Models\JobCategory::all();

        for ($i = 0; $i < 50; $i++) {
            $title = $faker->jobTitle;
            $company = $faker->company;
            \App\Models\JobListing::create([
                'category_id' => $categories->random()->id,
                'title' => $title,
                'slug' => \Illuminate\Support\Str::slug($title . '-' . \Illuminate\Support\Str::random(5)),
                'description' => $faker->paragraphs(3, true),
                'requirements' => $faker->paragraphs(2, true),
                'location' => $faker->city . ', ' . $faker->country,
                'salary_range' => '$' . $faker->numberBetween(3000, 8000) . ' - $' . $faker->numberBetween(9000, 15000),
                'type' => $faker->randomElement(['Full-time', 'Part-time', 'Contract', 'Remote']),
                'company_name' => $company,
                'is_active' => true,
            ]);
        }
    }
}
