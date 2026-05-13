<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            ['question' => 'How comfortable are you with backend development?', 'category' => 'technical', 'options' => ['Very Uncomfortable', 'Uncomfortable', 'Neutral', 'Comfortable', 'Expert']],
            ['question' => 'Do you enjoy designing user interfaces?', 'category' => 'technical', 'options' => ['Not at all', 'Slightly', 'Moderately', 'Very much', 'Extremely']],
            ['question' => 'How would you rate your public speaking skills?', 'category' => 'soft_skills', 'options' => ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent']],
            ['question' => 'How well do you work under tight deadlines?', 'category' => 'soft_skills', 'options' => ['Very Poorly', 'Poorly', 'Moderately', 'Well', 'Very Well']],
            ['question' => 'Are you interested in working with Big Data?', 'category' => 'career_path', 'options' => ['No Interest', 'Slight Interest', 'Interested', 'Very Interested', 'Passionate']],
            ['question' => 'Do you prefer leading teams or working independently?', 'category' => 'soft_skills', 'options' => ['Independently', 'Mostly Independently', 'Neutral', 'Mostly Leading', 'Leading']],
        ];

        foreach ($questions as $question) {
            \App\Models\Questionnaire::create($question);
        }
    }
}
