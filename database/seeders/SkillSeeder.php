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
            ['name' => 'Desain UI', 'type' => 'technical'],
            ['name' => 'Riset UX', 'type' => 'technical'],
            ['name' => 'Analisis Data', 'type' => 'technical'],
            ['name' => 'Manajemen Proyek', 'type' => 'soft'],
            ['name' => 'Komunikasi', 'type' => 'soft'],
            ['name' => 'Kepemimpinan', 'type' => 'soft'],
            ['name' => 'Pemecahan Masalah', 'type' => 'soft'],
            ['name' => 'Desain Grafis', 'type' => 'technical'],
            ['name' => 'SEO', 'type' => 'technical'],
            ['name' => 'Copywriting', 'type' => 'technical'],
            ['name' => 'Public Speaking', 'type' => 'soft'],
            ['name' => 'Berpikir Kritis', 'type' => 'soft'],
            ['name' => 'Kerja Sama Tim', 'type' => 'soft'],
            ['name' => 'Literasi Keuangan', 'type' => 'technical'],
        ];

        foreach ($skills as $skill) {
            \App\Models\Skill::create($skill);
        }
    }
}
