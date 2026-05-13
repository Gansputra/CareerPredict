<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\JobListing;
use App\Models\User;
use App\Models\JobCategory;

class LandingController extends Controller
{
    public function index()
    {
        $stats = [
            'jobs' => JobListing::count(),
            'users' => User::count(),
            'categories' => JobCategory::count(),
            'placements' => 1250, // Dummy stat
        ];

        $featuredJobs = JobListing::with('category')->latest()->limit(6)->get();

        return view('welcome', compact('stats', 'featuredJobs'));
    }
}
