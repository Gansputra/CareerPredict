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
                'name' => 'Analitis',
                'description' => 'Logika, pemecahan masalah, dan pengambilan keputusan berbasis data.',
                'icon' => 'fa-microchip',
                'questions' => [
                    ['q' => 'Saya senang memecahkan teka-teki logika yang kompleks.', 'w' => 1.2],
                    ['q' => 'Saya suka mengidentifikasi pola dan tren dalam kumpulan data.', 'w' => 1.0],
                    ['q' => 'Saya lebih suka mengambil keputusan berdasarkan data daripada intuisi.', 'w' => 1.1],
                    ['q' => 'Saya merasa puas ketika bisa memecah masalah besar menjadi bagian-bagian kecil.', 'w' => 1.0],
                    ['q' => 'Saya senang bekerja dengan spreadsheet, statistik, atau model matematika.', 'w' => 0.9],
                    ['q' => 'Saya penasaran tentang bagaimana sistem dan proses bekerja secara internal.', 'w' => 0.8],
                ]
            ],
            [
                'name' => 'Kreatif',
                'description' => 'Desain visual, ekspresi artistik, dan pemikiran inovatif.',
                'icon' => 'fa-palette',
                'questions' => [
                    ['q' => 'Saya senang mendesain antarmuka pengguna, tata letak, atau komposisi visual.', 'w' => 1.2],
                    ['q' => 'Saya sering menemukan ide-ide orisinal atau solusi yang tidak konvensional.', 'w' => 1.1],
                    ['q' => 'Saya tertarik dengan estetika, teori warna, dan harmoni visual.', 'w' => 1.0],
                    ['q' => 'Saya suka membuat konten visual seperti ilustrasi, foto, atau video.', 'w' => 0.9],
                    ['q' => 'Saya menikmati sesi brainstorming dan ideasi bersama tim kreatif.', 'w' => 0.8],
                    ['q' => 'Saya memperhatikan detail branding dan desain produk dengan cermat.', 'w' => 1.0],
                ]
            ],
            [
                'name' => 'Teknis',
                'description' => 'Pengembangan perangkat lunak, arsitektur sistem, dan rekayasa.',
                'icon' => 'fa-code',
                'questions' => [
                    ['q' => 'Saya senang menulis kode atau membangun aplikasi perangkat lunak.', 'w' => 1.2],
                    ['q' => 'Saya tertarik dengan cara kerja sistem komputer, jaringan, dan API.', 'w' => 1.0],
                    ['q' => 'Saya suka mempelajari bahasa pemrograman atau framework baru.', 'w' => 1.1],
                    ['q' => 'Saya menikmati proses debugging dan pemecahan masalah teknis.', 'w' => 0.9],
                    ['q' => 'Saya nyaman bekerja dengan database, server, atau platform cloud.', 'w' => 1.0],
                    ['q' => 'Saya suka mengotomatisasi tugas berulang menggunakan skrip atau alat bantu.', 'w' => 0.8],
                ]
            ],
            [
                'name' => 'Komunikasi',
                'description' => 'Berbicara di depan umum, menulis, persuasi, dan interaksi sosial.',
                'icon' => 'fa-comments',
                'questions' => [
                    ['q' => 'Saya senang mempresentasikan ide kepada kelompok atau berbicara di depan umum.', 'w' => 1.2],
                    ['q' => 'Saya pandai menjelaskan konsep yang kompleks dengan bahasa yang sederhana.', 'w' => 1.1],
                    ['q' => 'Saya suka menulis artikel, laporan, atau dokumentasi.', 'w' => 0.9],
                    ['q' => 'Saya merasa mudah membangun relasi dan berjejaring dengan orang lain.', 'w' => 1.0],
                    ['q' => 'Saya menikmati negosiasi, membujuk, atau memengaruhi orang lain.', 'w' => 1.0],
                    ['q' => 'Saya nyaman memediasi konflik atau memfasilitasi diskusi.', 'w' => 0.8],
                ]
            ],
            [
                'name' => 'Kepemimpinan',
                'description' => 'Manajemen tim, perencanaan strategis, dan kepemimpinan organisasi.',
                'icon' => 'fa-users-gear',
                'questions' => [
                    ['q' => 'Saya senang memimpin tim dan mengarahkan orang menuju tujuan bersama.', 'w' => 1.2],
                    ['q' => 'Saya nyaman mengambil keputusan penting di bawah tekanan.', 'w' => 1.1],
                    ['q' => 'Saya suka mengatur alur kerja, menetapkan target, dan melacak perkembangan.', 'w' => 1.0],
                    ['q' => 'Saya menikmati membimbing atau melatih orang lain untuk meningkatkan keterampilan mereka.', 'w' => 0.9],
                    ['q' => 'Saya berpikir secara strategis tentang tujuan dan visi jangka panjang.', 'w' => 1.0],
                    ['q' => 'Saya bersemangat ketika mengambil kepemilikan dan tanggung jawab atas hasil.', 'w' => 0.8],
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
