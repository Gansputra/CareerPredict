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
            ['name' => 'Kecerdasan Buatan (AI)'],
            ['name' => 'Pengembangan Web'],
            ['name' => 'Aplikasi Mobile'],
            ['name' => 'Sains Data'],
            ['name' => 'Pemasaran Digital'],
            ['name' => 'Kewirausahaan'],
            ['name' => 'Penulisan Kreatif'],
            ['name' => 'Keberlanjutan Lingkungan'],
            ['name' => 'Pekerjaan Sosial'],
            ['name' => 'Keuangan & Investasi'],
        ];

        foreach ($interests as $interest) {
            \App\Models\Interest::create($interest);
        }
    }
}
