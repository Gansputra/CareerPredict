<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Recommendation;
use App\Models\Application;
use App\Models\PersonalityScore;
use App\Models\UserAssessmentAnswer;
use App\Services\CertaintyFactorService;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Basic Stats
        $stats = [
            'recommendations' => Recommendation::where('user_id', $user->id)->count(),
            'applications' => Application::where('user_id', $user->id)->count(),
            'skills' => $user->skills()->count(),
        ];

        // Personality Data for Chart
        $personalityScores = PersonalityScore::with('category')
            ->where('user_id', $user->id)
            ->get();

        // Assessment Progress
        $totalQuestions = \App\Models\AssessmentQuestion::count();
        $answeredQuestions = UserAssessmentAnswer::where('user_id', $user->id)->count();
        $progress = $totalQuestions > 0 ? ($answeredQuestions / $totalQuestions) * 100 : 0;

        // Smart Recommendations — only if user has taken the DNA test
        $topMatches = [];
        $recentRecommendations = collect();

        if ($answeredQuestions > 0) {
            $cfService = new CertaintyFactorService();
            $topMatches = array_slice($cfService->calculate($user), 0, 5);

            // Recent Recommendations from DB
            $recentRecommendations = Recommendation::with('job')
                ->where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();
        }

        $recentApplications = Application::with('job')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $activeSponsors = \App\Models\SponsorBanner::where('is_active', true)->orderBy('order', 'asc')->get();

        return view('dashboard', compact('stats', 'personalityScores', 'progress', 'topMatches', 'recentRecommendations', 'recentApplications', 'activeSponsors'));
    }
}
