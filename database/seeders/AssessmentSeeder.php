<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\AssessmentCategory;
use App\Models\AssessmentQuestion;
use Illuminate\Support\Str;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Analytical',
                'description' => 'Logic, problem solving, and data analysis.',
                'icon' => 'fa-microchip',
                'questions' => [
                    'Do you enjoy solving complex logical puzzles?',
                    'Are you interested in identifying patterns in data?',
                    'Do you like working with spreadsheets and numbers?',
                ]
            ],
            [
                'name' => 'Creative',
                'description' => 'Visual design, art, and creative thinking.',
                'icon' => 'fa-palette',
                'questions' => [
                    'Do you enjoy designing user interfaces or layouts?',
                    'Do you like creating visual content or art?',
                    'Are you interested in branding and aesthetics?',
                ]
            ],
            [
                'name' => 'Technical',
                'description' => 'Coding, software engineering, and systems.',
                'icon' => 'fa-code',
                'questions' => [
                    'Do you enjoy writing code to solve problems?',
                    'Are you interested in how computer systems work?',
                    'Do you like learning new programming languages?',
                ]
            ],
            [
                'name' => 'Communication',
                'description' => 'Public speaking, writing, and social interaction.',
                'icon' => 'fa-comments',
                'questions' => [
                    'Do you enjoy public speaking or presenting?',
                    'Are you good at explaining complex ideas to others?',
                    'Do you like writing articles or documentation?',
                ]
            ],
            [
                'name' => 'Leadership',
                'description' => 'Management, strategy, and team leading.',
                'icon' => 'fa-users-gear',
                'questions' => [
                    'Do you enjoy leading teams towards a goal?',
                    'Are you comfortable making difficult decisions?',
                    'Do you like organizing tasks and projects?',
                ]
            ],
        ];

        foreach ($categories as $cat) {
            $category = AssessmentCategory::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'description' => $cat['description'],
                'icon' => $cat['icon'],
            ]);

            foreach ($cat['questions'] as $q) {
                AssessmentQuestion::create([
                    'category_id' => $category->id,
                    'question' => $q,
                    'weight' => 1,
                ]);
            }
        }
    }
}
