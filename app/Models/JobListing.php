<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
    protected $fillable = [
        'category_id', 'title', 'slug', 'description', 'requirements',
        'location', 'salary_range', 'type', 'company_name', 'company_logo', 'url', 'is_active', 'is_dummy'
    ];

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'job_id');
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class, 'job_id');
    }

    public function scopeActive($query)
    {
        $query->where('is_active', true);
        if (env('HIDE_DUMMY_JOBS', app()->environment('production'))) {
            $query->where('is_dummy', false);
        }
        return $query;
    }

    /**
     * Get the job's salary range, removing the "Rp" prefix dynamically.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getSalaryRangeAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        // Remove "Rp" and any following spaces (case-insensitive)
        return preg_replace('/Rp\s*/i', '', $value);
    }

    /**
     * Dapatkan daftar skill yang diperlukan untuk lowongan ini.
     * Menggunakan ekstraksi kata kunci teks, dengan fallback berbasis kategori.
     *
     * @return array
     */
    public function getRequiredSkills()
    {
        $skillKeywords = [
            'php' => 'PHP', 'laravel' => 'Laravel', 'javascript' => 'JavaScript',
            'typescript' => 'JavaScript', 'python' => 'Python', 'java' => 'Java',
            'c++' => 'C++', 'c#' => 'C#', 'ruby' => 'Ruby', 'go' => 'Go',
            'golang' => 'Go', 'rust' => 'Rust', 'swift' => 'Swift',
            'kotlin' => 'Kotlin', 'dart' => 'Dart', 'scala' => 'Scala',
            'r programming' => 'R',

            // Frontend
            'react' => 'React', 'reactjs' => 'React', 'react.js' => 'React',
            'vue' => 'Vue.js', 'vuejs' => 'Vue.js', 'angular' => 'Angular',
            'next.js' => 'Next.js', 'nextjs' => 'Next.js', 'nuxt' => 'Nuxt.js',
            'tailwind' => 'Tailwind CSS', 'bootstrap' => 'Bootstrap',
            'html' => 'HTML/CSS', 'css' => 'HTML/CSS', 'sass' => 'HTML/CSS',

            // Backend / DevOps
            'node' => 'Node.js', 'nodejs' => 'Node.js', 'express' => 'Node.js',
            'django' => 'Django', 'flask' => 'Flask', 'spring' => 'Spring',
            'docker' => 'Docker', 'kubernetes' => 'Kubernetes', 'k8s' => 'Kubernetes',
            'aws' => 'AWS', 'azure' => 'Azure', 'gcp' => 'Google Cloud',
            'ci/cd' => 'CI/CD', 'jenkins' => 'CI/CD', 'terraform' => 'Terraform',
            'linux' => 'Linux', 'nginx' => 'Linux', 'apache' => 'Linux',

            // Data & AI
            'sql' => 'SQL', 'mysql' => 'SQL', 'postgresql' => 'SQL',
            'mongodb' => 'MongoDB', 'redis' => 'Redis', 'elasticsearch' => 'Elasticsearch',
            'machine learning' => 'Machine Learning', 'deep learning' => 'Deep Learning',
            'tensorflow' => 'Machine Learning', 'pytorch' => 'Machine Learning',
            'pandas' => 'Data Analysis', 'numpy' => 'Data Analysis',
            'data analysis' => 'Data Analysis', 'data science' => 'Data Science',
            'power bi' => 'Data Analysis', 'tableau' => 'Data Analysis',

            // Design
            'figma' => 'UI Design', 'sketch' => 'UI Design', 'adobe xd' => 'UI Design',
            'photoshop' => 'Graphic Design', 'illustrator' => 'Graphic Design',
            'ui design' => 'UI Design', 'ux design' => 'UX Research',
            'ui/ux' => 'UI Design', 'user experience' => 'UX Research',
            'graphic design' => 'Graphic Design',

            // Soft Skills
            'leadership' => 'Leadership', 'team lead' => 'Leadership',
            'project management' => 'Project Management', 'agile' => 'Project Management',
            'scrum' => 'Project Management', 'jira' => 'Project Management',
            'communication' => 'Communication', 'public speaking' => 'Public Speaking',
            'problem solving' => 'Problem Solving', 'critical thinking' => 'Critical Thinking',
            'teamwork' => 'Teamwork',

            // Marketing & Business
            'seo' => 'SEO', 'sem' => 'SEO', 'google analytics' => 'SEO',
            'digital marketing' => 'Digital Marketing', 'social media' => 'Digital Marketing',
            'copywriting' => 'Copywriting', 'content writing' => 'Copywriting',
            'sales' => 'Sales', 'crm' => 'Sales', 'hubspot' => 'Sales',
            'financial' => 'Financial Literacy', 'accounting' => 'Financial Literacy',
            'excel' => 'Data Analysis',

            // Mobile
            'flutter' => 'Flutter', 'react native' => 'React Native',
            'ios' => 'iOS Development', 'android' => 'Android Development',

            // Other
            'git' => 'Git', 'github' => 'Git', 'gitlab' => 'Git',
            'api' => 'API Development', 'rest' => 'API Development', 'graphql' => 'GraphQL',
        ];

        $text = strtolower(($this->requirements ?? '') . ' ' . ($this->description ?? ''));
        $detected = [];

        foreach ($skillKeywords as $keyword => $skillName) {
            // Gunakan pencocokan batas kata untuk kata kunci pendek (<= 3 huruf)
            if (strlen($keyword) <= 3) {
                if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $text)) {
                    if (!in_array($skillName, $detected)) {
                        $detected[] = $skillName;
                    }
                }
            } else {
                if (str_contains($text, $keyword)) {
                    if (!in_array($skillName, $detected)) {
                        $detected[] = $skillName;
                    }
                }
            }
        }

        // Fallback jika tidak ada skill terdeteksi, gunakan skill bawaan kategori
        if (empty($detected) && $this->category) {
            $catName = strtolower($this->category->name);
            if (str_contains($catName, 'teknologi')) {
                $detected = ['PHP', 'Laravel', 'JavaScript', 'React', 'Python', 'SQL', 'Git'];
            } elseif (str_contains($catName, 'keuangan')) {
                $detected = ['Literasi Keuangan', 'Analisis Data', 'SQL', 'Berpikir Kritis'];
            } elseif (str_contains($catName, 'pemasaran')) {
                $detected = ['SEO', 'Copywriting', 'Komunikasi', 'Analisis Data'];
            } elseif (str_contains($catName, 'desain') || str_contains($catName, 'kreatif')) {
                $detected = ['UI Design', 'UX Research', 'Desain Grafis', 'Figma', 'Komunikasi'];
            } elseif (str_contains($catName, 'sumber daya manusia') || str_contains($catName, 'sdm')) {
                $detected = ['Komunikasi', 'Kepemimpinan', 'Kerja Sama Tim', 'Manajemen Proyek'];
            } elseif (str_contains($catName, 'manajemen')) {
                $detected = ['Kepemimpinan', 'Manajemen Proyek', 'Komunikasi', 'Pemecahan Masalah'];
            } elseif (str_contains($catName, 'rekayasa') || str_contains($catName, 'teknik')) {
                $detected = ['Pemecahan Masalah', 'Berpikir Kritis', 'Kerja Sama Tim', 'Python', 'SQL'];
            } elseif (str_contains($catName, 'pendidikan')) {
                $detected = ['Komunikasi', 'Public Speaking', 'Kepemimpinan'];
            } elseif (str_contains($catName, 'penjualan')) {
                $detected = ['Komunikasi', 'Public Speaking', 'Sales'];
            } elseif (str_contains($catName, 'kesehatan')) {
                $detected = ['Pemecahan Masalah', 'Komunikasi', 'Kerja Sama Tim'];
            }
        }

        return $detected;
    }
}
