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
            ['question' => 'Seberapa nyaman Anda dengan pengembangan backend?', 'category' => 'technical', 'options' => ['Sangat Tidak Nyaman', 'Tidak Nyaman', 'Netral', 'Nyaman', 'Sangat Mahir']],
            ['question' => 'Apakah Anda menikmati mendesain antarmuka pengguna?', 'category' => 'technical', 'options' => ['Tidak Sama Sekali', 'Sedikit', 'Cukup', 'Sangat Suka', 'Sangat Antusias']],
            ['question' => 'Bagaimana Anda menilai kemampuan berbicara di depan umum?', 'category' => 'soft_skills', 'options' => ['Buruk', 'Kurang', 'Cukup Baik', 'Baik', 'Sangat Baik']],
            ['question' => 'Seberapa baik Anda bekerja di bawah tenggat waktu yang ketat?', 'category' => 'soft_skills', 'options' => ['Sangat Buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat Baik']],
            ['question' => 'Apakah Anda tertarik bekerja dengan Big Data?', 'category' => 'career_path', 'options' => ['Tidak Tertarik', 'Sedikit Tertarik', 'Tertarik', 'Sangat Tertarik', 'Sangat Antusias']],
            ['question' => 'Apakah Anda lebih suka memimpin tim atau bekerja mandiri?', 'category' => 'soft_skills', 'options' => ['Mandiri', 'Lebih Suka Mandiri', 'Netral', 'Lebih Suka Memimpin', 'Memimpin']],
        ];

        foreach ($questions as $question) {
            \App\Models\Questionnaire::create($question);
        }
    }
}
