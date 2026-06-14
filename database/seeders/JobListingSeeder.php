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
        $faker = \Faker\Factory::create('id_ID');
        $categories = \App\Models\JobCategory::all();

        $jobTitles = [
            'Software Engineer', 'Web Developer', 'Mobile Developer', 'UI/UX Designer', 'Data Analyst', 'DevOps Specialist',
            'Dokter Umum', 'Perawat Medis', 'Apoteker', 'Spesialis Gizi', 'Analis Lab Medis',
            'Akuntan Junior', 'Analis Keuangan', 'Manajer Keuangan', 'Konsultan Pajak', 'Audit Internal',
            'Guru Matematika', 'Dosen Universitas', 'Tutor Online', 'Guru Bahasa Inggris', 'Konselor Pendidikan',
            'Digital Marketer', 'Content Writer', 'SEO Specialist', 'Social Media Specialist', 'Brand Manager',
            'Desainer Grafis', 'Video Editor', 'Copywriter Kreatif', 'Fotografer Studio', 'Creative Director',
            'Teknisi Sipil', 'Project Manager Konstruksi', 'Mechanical Engineer', 'Electrical Engineer', 'Arsitek',
            'Sales Executive', 'Account Manager', 'Business Development', 'Customer Success', 'telesales',
            'HR Specialist', 'Recruiter', 'HR Generalist', 'Training & Development',
            'Manajer Operasional', 'General Manager', 'Supervisor Produksi', 'Business Analyst'
        ];

        for ($i = 0; $i < 50; $i++) {
            $baseTitle = $faker->randomElement($jobTitles);
            $level = $faker->randomElement(['Senior', 'Junior', 'Lead', '']);
            $title = trim($level . ' ' . $baseTitle);
            
            $company = $faker->company;
            
            $minVal = $faker->numberBetween(4, 15);
            $maxVal = $faker->numberBetween($minVal + 2, $minVal + 20);
            $salary_range = $minVal . '.000.000 - ' . $maxVal . '.000.000';

            \App\Models\JobListing::create([
                'category_id' => $categories->random()->id,
                'title' => $title,
                'slug' => \Illuminate\Support\Str::slug($title . '-' . \Illuminate\Support\Str::random(5)),
                'description' => 'Kami mencari profesional yang berdedikasi untuk bergabung dengan tim kami sebagai ' . $title . '. Di peran ini, Anda akan bertanggung jawab untuk memimpin inisiatif utama, berkolaborasi dengan tim lintas divisi, dan mendorong hasil bisnis yang sukses. Kami menawarkan lingkungan kerja yang dinamis, jalur karir yang jelas, dan kompensasi yang sangat kompetitif di pasar.',
                'requirements' => "• Pengalaman kerja minimal 2-5 tahun di bidang relevan.\n• Memiliki keterampilan komunikasi yang kuat dalam Bahasa Indonesia dan Inggris.\n• Mampu bekerja dalam tim maupun secara mandiri.\n• Memiliki pemecahan masalah yang baik dan perhatian terhadap detail.\n• Gelar Sarjana (S1) di bidang terkait.",
                'location' => $faker->city . ', Indonesia',
                'salary_range' => $salary_range,
                'type' => $faker->randomElement(['Penuh Waktu', 'Paruh Waktu', 'Kontrak', 'Jarak Jauh (Remote)']),
                'company_name' => $company,
                'url' => 'https://www.google.com/search?q=' . urlencode('Lowongan Kerja ' . $title . ' ' . $company),
                'is_active' => true,
                'is_dummy' => true,
            ]);
        }
    }
}
