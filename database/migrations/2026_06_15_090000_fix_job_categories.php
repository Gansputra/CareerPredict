<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\JobListing;
use App\Models\JobCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $mapping = [
            'teknologi' => [
                'name' => 'Teknologi',
                'keywords' => ['software', 'developer', 'engineer', 'programming', 'backend', 'frontend', 'fullstack', 'devops', 'cloud', 'data', 'ai', 'python', 'javascript', 'php', 'web', 'mobile', 'sysadmin', 'informatiker', 'security', 'tech support']
            ],
            'kesehatan' => [
                'name' => 'Kesehatan',
                'keywords' => ['healthcare', 'medical', 'medicine', 'health', 'nurse', 'doctor', 'clinic', 'dentist', 'apoteker', 'gizi', 'dietary', 'dokter', 'perawat', 'medis', 'pflege', 'therapist']
            ],
            'keuangan' => [
                'name' => 'Keuangan',
                'keywords' => ['finance', 'accounting', 'financial', 'tax', 'audit', 'banking', 'bookkeeping', 'akuntan', 'pajak', 'audit', 'billing', 'payables', 'treasury']
            ],
            'pendidikan' => [
                'name' => 'Pendidikan',
                'keywords' => ['education', 'teacher', 'tutor', 'training', 'teaching', 'school', 'guru', 'dosen', 'konselor', 'trainer', 'instructional']
            ],
            'pemasaran' => [
                'name' => 'Pemasaran',
                'keywords' => ['marketing', 'seo', 'content', 'social media', 'brand', 'advertising', 'public relations', 'copywriter', 'copywriting', 'writer', 'paid ads', 'newsletter']
            ],
            'desain-kreatif' => [
                'name' => 'Desain & Kreatif',
                'keywords' => ['creative & design', 'design', 'ux', 'ui', 'creative', 'art', 'figma', 'graphic', 'desainer', 'fotografer', 'video editor', 'illustrator']
            ],
            'rekayasa-teknik' => [
                'name' => 'Rekayasa & Teknik',
                'keywords' => ['engineering', 'mechanical', 'electrical', 'civil', 'structural', 'technical', 'teknisi', 'arsitek', 'konstruksi', 'architect']
            ],
            'penjualan' => [
                'name' => 'Penjualan',
                'keywords' => ['sales', 'account executive', 'business development', 'customer', 'telemarketing', 'retail', 'salesman', 'telesales', 'barista', 'server', 'account manager']
            ],
            'sumber-daya-manusia' => [
                'name' => 'Sumber Daya Manusia',
                'keywords' => ['human resources', 'hr', 'recruiter', 'talent', 'recruitment', 'people operations']
            ],
            'manajemen' => [
                'name' => 'Manajemen',
                'keywords' => ['management', 'manager', 'director', 'lead', 'head of', 'product', 'project', 'operations', 'supervisor', 'analyst', 'consultant', 'advisor']
            ],
        ];

        $jobs = JobListing::all();
        foreach ($jobs as $job) {
            $title = strtolower($job->title);
            
            foreach ($mapping as $slug => $data) {
                foreach ($data['keywords'] as $keyword) {
                    if (str_contains($title, $keyword)) {
                        $cat = JobCategory::firstOrCreate(
                            ['slug' => $slug],
                            ['name' => $data['name']]
                        );
                        $job->category_id = $cat->id;
                        $job->save();
                        continue 3; // match found, move to next job
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
