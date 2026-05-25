<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Teknologi', 'slug' => 'teknologi'],
            ['name' => 'Kesehatan', 'slug' => 'kesehatan'],
            ['name' => 'Keuangan', 'slug' => 'keuangan'],
            ['name' => 'Pendidikan', 'slug' => 'pendidikan'],
            ['name' => 'Pemasaran', 'slug' => 'pemasaran'],
            ['name' => 'Desain & Kreatif', 'slug' => 'desain-kreatif'],
            ['name' => 'Rekayasa & Teknik', 'slug' => 'rekayasa-teknik'],
            ['name' => 'Penjualan', 'slug' => 'penjualan'],
            ['name' => 'Sumber Daya Manusia', 'slug' => 'sumber-daya-manusia'],
            ['name' => 'Manajemen', 'slug' => 'manajemen'],
        ];

        foreach ($categories as $category) {
            \App\Models\JobCategory::firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );
        }
    }
}
