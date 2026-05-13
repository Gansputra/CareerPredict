<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            ['name' => 'PHP', 'type' => 'technical'],
            ['name' => 'Laravel', 'type' => 'technical'],
            ['name' => 'JavaScript', 'type' => 'technical'],
            ['name' => 'React', 'type' => 'technical'],
            ['name' => 'Python', 'type' => 'technical'],
            ['name' => 'SQL', 'type' => 'technical'],
            ['name' => 'UI Design', 'type' => 'technical'],
            ['name' => 'UX Research', 'type' => 'technical'],
            ['name' => 'Data Analysis', 'type' => 'technical'],
            ['name' => 'Project Management', 'type' => 'soft'],
            ['name' => 'Communication', 'type' => 'soft'],
            ['name' => 'Leadership', 'type' => 'soft'],
            ['name' => 'Problem Solving', 'type' => 'soft'],
            ['name' => 'Graphic Design', 'type' => 'technical'],
            ['name' => 'SEO', 'type' => 'technical'],
            ['name' => 'Copywriting', 'type' => 'technical'],
            ['name' => 'Public Speaking', 'type' => 'soft'],
            ['name' => 'Critical Thinking', 'type' => 'soft'],
            ['name' => 'Teamwork', 'type' => 'soft'],
            ['name' => 'Financial Literacy', 'type' => 'technical'],
        ];

        foreach ($skills as $skill) {
            \App\Models\Skill::create($skill);
        }
    }
}
