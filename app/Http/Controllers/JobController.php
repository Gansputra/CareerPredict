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
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('company_name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        $jobs = $query->latest()->paginate(12)->withQueryString();
        $categories = JobCategory::all();
        $locations = JobListing::where('is_active', true)
            ->select('location')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        return view('jobs.index', compact('jobs', 'categories', 'locations'));
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
