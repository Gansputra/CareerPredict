<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $interests = [
            ['name' => 'Artificial Intelligence'],
            ['name' => 'Web Development'],
            ['name' => 'Mobile Apps'],
            ['name' => 'Data Science'],
            ['name' => 'Digital Marketing'],
            ['name' => 'Entrepreneurship'],
            ['name' => 'Creative Writing'],
            ['name' => 'Environmental Sustainability'],
            ['name' => 'Social Work'],
            ['name' => 'Finance & Investment'],
        ];

        foreach ($interests as $interest) {
            \App\Models\Interest::create($interest);
        }
    }
}
