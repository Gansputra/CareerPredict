<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\CategoryResolver;
use App\Models\JobCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_resolves_categories_correctly_based_on_category_name()
    {
        // Category names directly matching or containing keywords
        $categories = [
            'healthcare' => 'Kesehatan',
            'medical' => 'Kesehatan',
            'finance' => 'Keuangan',
            'accounting' => 'Keuangan',
            'technology' => 'Teknologi',
            'software dev' => 'Teknologi',
            'sales' => 'Penjualan',
            'marketing' => 'Pemasaran',
            'creative & design' => 'Desain & Kreatif',
            'engineering' => 'Rekayasa & Teknik',
            'human resources' => 'Sumber Daya Manusia',
            'management' => 'Manajemen',
            'education' => 'Pendidikan',
        ];

        foreach ($categories as $input => $expectedName) {
            $resolved = CategoryResolver::resolve($input, '');
            $this->assertEquals($expectedName, $resolved->name);
        }
    }

    public function test_it_resolves_categories_correctly_based_on_job_title()
    {
        // Job titles containing keywords
        $jobs = [
            'Junior Software Engineer' => 'Teknologi',
            'React Backend Developer' => 'Teknologi',
            'Registered Nurse Practitioner' => 'Kesehatan',
            'General Doctor' => 'Kesehatan',
            'Tax Consultant & Accountant' => 'Keuangan',
            'English Teacher' => 'Pendidikan',
            'SEO & Content Specialist' => 'Pemasaran',
            'UI/UX Product Designer' => 'Desain & Kreatif',
            'Civil Engineering Intern' => 'Rekayasa & Teknik',
            'Business Development & Sales Lead' => 'Penjualan',
            'Restaurant Barista & Cook' => 'Penjualan',
            'HR Generalist' => 'Sumber Daya Manusia',
            'Project Manager' => 'Manajemen',
        ];

        foreach ($jobs as $title => $expectedName) {
            $resolved = CategoryResolver::resolve('', $title);
            $this->assertEquals($expectedName, $resolved->name, "Failed mapping title '{$title}' to '{$expectedName}'");
        }
    }

    public function test_it_prioritizes_job_title_keywords_over_generic_category_names()
    {
        // Cases where job title is specific (e.g. Account Payable) but category is generic (e.g. Healthcare)
        $cases = [
            [
                'category' => 'healthcare',
                'title' => 'Assistant Account Payable',
                'expected' => 'Keuangan'
            ],
            [
                'category' => 'management',
                'title' => 'Staff Product Engineer',
                'expected' => 'Teknologi'
            ],
            [
                'category' => 'technology',
                'title' => 'Senior Recruiter',
                'expected' => 'Sumber Daya Manusia'
            ],
            [
                'category' => 'healthcare',
                'title' => 'Pharmaceutical Sales Representative',
                'expected' => 'Penjualan'
            ]
        ];

        foreach ($cases as $case) {
            $resolved = CategoryResolver::resolve($case['category'], $case['title']);
            $this->assertEquals($case['expected'], $resolved->name, "Failed prioritizing title '{$case['title']}' with category '{$case['category']}' to '{$case['expected']}'");
        }
    }

    public function test_it_falls_back_to_teknologi_when_cannot_resolve()
    {
        // Unknown category and title should fallback to Teknologi
        $resolved = CategoryResolver::resolve('Random Unknown Category', 'Some Job Title');
        $this->assertEquals('Teknologi', $resolved->name);
    }
}
