<?php

namespace App\Helpers;

use App\Models\JobCategory;
use Illuminate\Support\Str;

class CategoryResolver
{
    /**
     * Resolve Indonesian category for a given category name and/or job title.
     *
     * @param string|null $categoryName
     * @param string|null $jobTitle
     * @return JobCategory
     */
    public static function resolve(?string $categoryName, ?string $jobTitle = ''): JobCategory
    {
        $categoryName = strtolower(trim($categoryName ?? ''));
        $jobTitle = strtolower(trim($jobTitle ?? ''));

        $mapping = [
            'teknologi' => [
                'name' => 'Teknologi',
                'keywords' => [
                    'software', 'developer', 'engineer', 'programming', 'backend', 'frontend', 
                    'fullstack', 'devops', 'cloud', 'data', 'ai', 'python', 'javascript', 
                    'php', 'web', 'mobile', 'sysadmin', 'informatiker', 'security', 
                    'tech support', 'technology', 'it', 'qa', 'test', 'network', 'database', 
                    'computer', 'coding', 'cyber', 'system', 'programmer', 'webdev', 'linux',
                    'dev', 'software-dev', 'software_dev'
                ]
            ],
            'kesehatan' => [
                'name' => 'Kesehatan',
                'keywords' => [
                    'healthcare', 'medical', 'medicine', 'health', 'nurse', 'doctor', 
                    'clinic', 'dentist', 'apoteker', 'gizi', 'dietary', 'dokter', 'perawat', 
                    'medis', 'pflege', 'therapist', 'pharmacy', 'clinical', 'physician', 
                    'patient', 'hospital', 'dental', 'veterinarian'
                ]
            ],
            'keuangan' => [
                'name' => 'Keuangan',
                'keywords' => [
                    'finance', 'accounting', 'financial', 'tax', 'audit', 'banking', 
                    'bookkeeping', 'akuntan', 'pajak', 'billing', 'payables', 'payable',
                    'receivable', 'receivables', 'invoice', 'invoices', 'ledger', 'ledgers',
                    'treasury', 'keuangan', 'accountant', 'controller', 'payment', 'payments'
                ]
            ],
            'pendidikan' => [
                'name' => 'Pendidikan',
                'keywords' => [
                    'education', 'teacher', 'tutor', 'training', 'teaching', 'school', 
                    'guru', 'dosen', 'konselor', 'trainer', 'instructional', 'academic', 
                    'pedagogy', 'curriculum', 'learning'
                ]
            ],
            'pemasaran' => [
                'name' => 'Pemasaran',
                'keywords' => [
                    'marketing', 'seo', 'content', 'social media', 'brand', 'advertising', 
                    'public relations', 'copywriter', 'copywriting', 'writer', 'paid ads', 
                    'newsletter', 'pemasaran', 'pr', 'creative writer', 'copy'
                ]
            ],
            'desain-kreatif' => [
                'name' => 'Desain & Kreatif',
                'keywords' => [
                    'creative & design', 'design', 'ux', 'ui', 'creative', 'art', 
                    'figma', 'graphic', 'desainer', 'fotografer', 'video editor', 
                    'illustrator', 'desain', 'designer', 'artist', 'animator', 'photo', 'video'
                ]
            ],
            'rekayasa-teknik' => [
                'name' => 'Rekayasa & Teknik',
                'keywords' => [
                    'engineering', 'mechanical', 'electrical', 'civil', 'structural', 
                    'technical', 'teknisi', 'arsitek', 'konstruksi', 'architect', 'teknik', 
                    'technician', 'mechanics', 'electrician'
                ]
            ],
            'penjualan' => [
                'name' => 'Penjualan',
                'keywords' => [
                    'sales', 'account executive', 'business development', 'customer', 
                    'telemarketing', 'retail', 'salesman', 'telesales', 'barista', 'server', 
                    'account manager', 'penjualan', 'cashier', 'waiter', 'waitress', 'host', 
                    'hostess', 'chef', 'cook', 'restaurant', 'food', 'beverage', 'kitchen', 
                    'culinary', 'service', 'hospitality', 'store'
                ]
            ],
            'sumber-daya-manusia' => [
                'name' => 'Sumber Daya Manusia',
                'keywords' => [
                    'human resources', 'hr', 'recruiter', 'talent', 'recruitment', 
                    'people operations', 'sdm', 'payroll', 'employee', 'hr business partner'
                ]
            ],
            'manajemen' => [
                'name' => 'Manajemen',
                'keywords' => [
                    'management', 'manager', 'director', 'lead', 'head of', 'product', 
                    'project', 'operations', 'supervisor', 'analyst', 'consultant', 
                    'advisor', 'manajemen', 'executive', 'vp', 'chief', 'admin', 
                    'administrative', 'assistant', 'coordinator'
                ]
            ],
        ];

        // 1. Try to match job title against keywords (with word boundaries) first.
        // The job title is more specific to the actual role than a generic category name from an API.
        if (!empty($jobTitle)) {
            foreach ($mapping as $slug => $data) {
                foreach ($data['keywords'] as $keyword) {
                    $pattern = '/\b' . preg_quote($keyword, '/') . '\b/i';
                    if (preg_match($pattern, $jobTitle)) {
                        return JobCategory::firstOrCreate(
                            ['slug' => $slug],
                            ['name' => $data['name']]
                        );
                    }
                }
            }
        }

        // 2. If title did not match, try to match category name against keywords/slugs
        if (!empty($categoryName)) {
            // First check direct slug or name matches
            foreach ($mapping as $slug => $data) {
                if ($categoryName === $slug || $categoryName === strtolower($data['name'])) {
                    return JobCategory::firstOrCreate(
                        ['slug' => $slug],
                        ['name' => $data['name']]
                    );
                }
            }

            // Next check keyword matches in category name (with word boundaries)
            foreach ($mapping as $slug => $data) {
                foreach ($data['keywords'] as $keyword) {
                    $pattern = '/\b' . preg_quote($keyword, '/') . '\b/i';
                    if (preg_match($pattern, $categoryName)) {
                        return JobCategory::firstOrCreate(
                            ['slug' => $slug],
                            ['name' => $data['name']]
                        );
                    }
                }
            }
        }

        // 3. Fallback: default to 'teknologi'
        $defaultSlug = 'teknologi';
        $defaultName = 'Teknologi';
        
        return JobCategory::firstOrCreate(
            ['slug' => $defaultSlug],
            ['name' => $defaultName]
        );
    }
}
