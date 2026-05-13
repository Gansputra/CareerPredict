<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\JobListing;
use App\Models\Recommendation;
use App\Models\Application;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_jobs' => JobListing::count(),
            'total_recommendations' => Recommendation::count(),
            'total_applications' => Application::count(),
        ];

        $driver = DB::getDriverName();
        $dateField = $driver === 'sqlite' 
            ? 'strftime("%m", created_at) as month' 
            : 'DATE_FORMAT(created_at, "%M") as month';

        $userGrowth = User::select(DB::raw('count(*) as count'), DB::raw($dateField))
            ->where('role', 'user')
            ->groupBy('month')
            ->get();

        $categoryPopularity = JobListing::select('job_categories.name', DB::raw('count(*) as count'))
            ->join('job_categories', 'job_listings.category_id', '=', 'job_categories.id')
            ->groupBy('job_categories.name')
            ->get();

        return view('admin.dashboard', compact('stats', 'userGrowth', 'categoryPopularity'));
    }
}
