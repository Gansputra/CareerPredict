<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\JobListing;
use App\Models\JobCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobSearchTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private JobCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->category = JobCategory::create([
            'name' => 'Teknologi',
            'slug' => 'teknologi',
        ]);
    }

    public function test_can_access_jobs_page(): void
    {
        $response = $this->actingAs($this->user)->get('/jobs');
        $response->assertStatus(200);
    }

    public function test_can_filter_jobs_by_search_and_category(): void
    {
        JobListing::create([
            'category_id' => $this->category->id,
            'title' => 'Laravel Developer',
            'slug' => 'laravel-developer',
            'description' => 'Laravel Developer description',
            'requirements' => 'Requirements Laravel',
            'location' => 'Jakarta, Indonesia',
            'salary_range' => '5.000.000 - 8.000.000',
            'type' => 'Penuh Waktu',
            'company_name' => 'A Perusahaan',
            'is_active' => true,
        ]);

        JobListing::create([
            'category_id' => $this->category->id,
            'title' => 'Python Developer',
            'slug' => 'python-developer',
            'description' => 'Python Developer description',
            'requirements' => 'Requirements Python',
            'location' => 'Bandung, Indonesia',
            'salary_range' => '10.000.000 - 15.000.000',
            'type' => 'Penuh Waktu',
            'company_name' => 'B Perusahaan',
            'is_active' => true,
        ]);

        // Search "Laravel"
        $response = $this->actingAs($this->user)->get('/jobs?search=Laravel');
        $response->assertStatus(200);
        $response->assertSee('Laravel Developer');
        $response->assertDontSee('Python Developer');
    }

    public function test_can_sort_jobs_by_company_name(): void
    {
        JobListing::create([
            'category_id' => $this->category->id,
            'title' => 'Dev 1',
            'slug' => 'dev-1',
            'description' => 'description 1',
            'requirements' => 'Requirements',
            'location' => 'Jakarta',
            'salary_range' => '5.000.000 - 8.000.000',
            'company_name' => 'Alfamart',
            'is_active' => true,
        ]);

        JobListing::create([
            'category_id' => $this->category->id,
            'title' => 'Dev 2',
            'slug' => 'dev-2',
            'description' => 'description 2',
            'requirements' => 'Requirements',
            'location' => 'Jakarta',
            'salary_range' => '10.000.000 - 15.000.000',
            'company_name' => 'Zalora',
            'is_active' => true,
        ]);

        // Sort A-Z
        $response = $this->actingAs($this->user)->get('/jobs?sort=company_asc');
        $response->assertStatus(200);
        
        $jobs = $response->viewData('jobs');
        $this->assertEquals('Alfamart', $jobs->first()->company_name);
        $this->assertEquals('Zalora', $jobs->last()->company_name);

        // Sort Z-A
        $response = $this->actingAs($this->user)->get('/jobs?sort=company_desc');
        $response->assertStatus(200);
        
        $jobs = $response->viewData('jobs');
        $this->assertEquals('Zalora', $jobs->first()->company_name);
        $this->assertEquals('Alfamart', $jobs->last()->company_name);
    }

    public function test_can_sort_jobs_by_salary(): void
    {
        JobListing::create([
            'category_id' => $this->category->id,
            'title' => 'Dev Low',
            'slug' => 'dev-low',
            'description' => 'description 1',
            'requirements' => 'Requirements',
            'location' => 'Jakarta',
            'salary_range' => '5.000.000 - 8.000.000',
            'company_name' => 'Company A',
            'is_active' => true,
        ]);

        JobListing::create([
            'category_id' => $this->category->id,
            'title' => 'Dev High',
            'slug' => 'dev-high',
            'description' => 'description 2',
            'requirements' => 'Requirements',
            'location' => 'Jakarta',
            'salary_range' => '20.000.000 - 30.000.000',
            'company_name' => 'Company B',
            'is_active' => true,
        ]);

        JobListing::create([
            'category_id' => $this->category->id,
            'title' => 'Dev Negotiable',
            'slug' => 'dev-negotiable',
            'description' => 'description 3',
            'requirements' => 'Requirements',
            'location' => 'Jakarta',
            'salary_range' => 'Negotiable',
            'company_name' => 'Company C',
            'is_active' => true,
        ]);

        // Sort Salary High to Low
        $response = $this->actingAs($this->user)->get('/jobs?sort=salary_desc');
        $response->assertStatus(200);
        
        $jobs = $response->viewData('jobs');
        $this->assertEquals('Dev High', $jobs->first()->title);
        $this->assertEquals('Dev Negotiable', $jobs->last()->title);

        // Sort Salary Low to High
        $response = $this->actingAs($this->user)->get('/jobs?sort=salary_asc');
        $response->assertStatus(200);
        
        $jobs = $response->viewData('jobs');
        $this->assertEquals('Dev Negotiable', $jobs->first()->title);
        $this->assertEquals('Dev High', $jobs->last()->title);
    }
}
