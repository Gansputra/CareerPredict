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
                'description' => 'Logic, problem solving, and data-driven decision making.',
                'icon' => 'fa-microchip',
                'questions' => [
                    ['q' => 'I enjoy solving complex logical puzzles and brainteasers.', 'w' => 1.2],
                    ['q' => 'I like identifying patterns and trends in datasets.', 'w' => 1.0],
                    ['q' => 'I prefer making decisions based on data rather than intuition.', 'w' => 1.1],
                    ['q' => 'I find it satisfying to break down large problems into smaller parts.', 'w' => 1.0],
                    ['q' => 'I enjoy working with spreadsheets, statistics, or mathematical models.', 'w' => 0.9],
                    ['q' => 'I am curious about how systems and processes work internally.', 'w' => 0.8],
                ]
            ],
            [
                'name' => 'Creative',
                'description' => 'Visual design, artistic expression, and innovative thinking.',
                'icon' => 'fa-palette',
                'questions' => [
                    ['q' => 'I enjoy designing user interfaces, layouts, or visual compositions.', 'w' => 1.2],
                    ['q' => 'I often come up with original ideas or unconventional solutions.', 'w' => 1.1],
                    ['q' => 'I am drawn to aesthetics, color theory, and visual harmony.', 'w' => 1.0],
                    ['q' => 'I like creating visual content such as illustrations, photos, or videos.', 'w' => 0.9],
                    ['q' => 'I enjoy brainstorming and ideation sessions with creative teams.', 'w' => 0.8],
                    ['q' => 'I pay close attention to branding and product design details.', 'w' => 1.0],
                ]
            ],
            [
                'name' => 'Technical',
                'description' => 'Software development, systems architecture, and engineering.',
                'icon' => 'fa-code',
                'questions' => [
                    ['q' => 'I enjoy writing code or building software applications.', 'w' => 1.2],
                    ['q' => 'I am fascinated by how computer systems, networks, and APIs work.', 'w' => 1.0],
                    ['q' => 'I like learning new programming languages or frameworks.', 'w' => 1.1],
                    ['q' => 'I enjoy debugging and troubleshooting technical issues.', 'w' => 0.9],
                    ['q' => 'I am comfortable working with databases, servers, or cloud platforms.', 'w' => 1.0],
                    ['q' => 'I like automating repetitive tasks using scripts or tools.', 'w' => 0.8],
                ]
            ],
            [
                'name' => 'Communication',
                'description' => 'Public speaking, writing, persuasion, and social interaction.',
                'icon' => 'fa-comments',
                'questions' => [
                    ['q' => 'I enjoy presenting ideas to groups or speaking publicly.', 'w' => 1.2],
                    ['q' => 'I am good at explaining complex concepts in simple terms.', 'w' => 1.1],
                    ['q' => 'I like writing articles, reports, or documentation.', 'w' => 0.9],
                    ['q' => 'I find it natural to build relationships and network with others.', 'w' => 1.0],
                    ['q' => 'I enjoy negotiating, persuading, or influencing others.', 'w' => 1.0],
                    ['q' => 'I am comfortable mediating conflicts or facilitating discussions.', 'w' => 0.8],
                ]
            ],
            [
                'name' => 'Leadership',
                'description' => 'Team management, strategic planning, and organizational leadership.',
                'icon' => 'fa-users-gear',
                'questions' => [
                    ['q' => 'I enjoy leading teams and guiding people towards a shared goal.', 'w' => 1.2],
                    ['q' => 'I am comfortable making high-stakes decisions under pressure.', 'w' => 1.1],
                    ['q' => 'I like organizing workflows, setting milestones, and tracking progress.', 'w' => 1.0],
                    ['q' => 'I enjoy mentoring or coaching others to improve their skills.', 'w' => 0.9],
                    ['q' => 'I think strategically about long-term goals and vision.', 'w' => 1.0],
                    ['q' => 'I am energized by taking ownership and responsibility for outcomes.', 'w' => 0.8],
                ]
            ],
        ];

        foreach ($categories as $cat) {
            $category = AssessmentCategory::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'icon' => $cat['icon'],
                ]
            );

            // Remove old questions for this category before re-seeding
            AssessmentQuestion::where('category_id', $category->id)->delete();

            foreach ($cat['questions'] as $q) {
                AssessmentQuestion::create([
                    'category_id' => $category->id,
                    'question' => $q['q'],
                    'weight' => $q['w'],
                ]);
            }
        }
    }
}
