<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Recommendation;
use App\Models\Application;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $stats = [
            'recommendations' => Recommendation::where('user_id', $user->id)->count(),
            'applications' => Application::where('user_id', $user->id)->count(),
            'skills' => $user->skills()->count(),
        ];

        $recentRecommendations = Recommendation::with('job')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $recentApplications = Application::with('job')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recentRecommendations', 'recentApplications'));
    }
}
