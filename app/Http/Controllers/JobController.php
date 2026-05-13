<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\JobListing;
use App\Models\JobCategory;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = JobListing::with('category')->where('is_active', true);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $jobs = $query->latest()->paginate(12);
        $categories = JobCategory::all();

        return view('jobs.index', compact('jobs', 'categories'));
    }

    public function show($slug)
    {
        $job = JobListing::with('category')->where('slug', $slug)->firstOrFail();
        $relatedJobs = JobListing::where('category_id', $job->category_id)
            ->where('id', '!=', $job->id)
            ->limit(3)
            ->get();

        return view('jobs.show', compact('job', 'relatedJobs'));
    }
}
