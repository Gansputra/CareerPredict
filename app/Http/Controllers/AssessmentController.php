<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AssessmentCategory;
use App\Models\AssessmentQuestion;
use App\Models\UserAssessmentAnswer;
use App\Models\PersonalityScore;
use App\Services\CertaintyFactorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    public function index()
    {
        $categories = AssessmentCategory::with('questions')->get();
        return view('expert.assessment', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $answers = $request->input('answers'); // [question_id => score]

        DB::transaction(function () use ($user, $answers) {
            // Remove old answers if retaking
            UserAssessmentAnswer::where('user_id', $user->id)->delete();
            PersonalityScore::where('user_id', $user->id)->delete();

            foreach ($answers as $qId => $score) {
                UserAssessmentAnswer::create([
                    'user_id' => $user->id,
                    'question_id' => $qId,
                    'score' => $score
                ]);
            }

            // Calculate Personality Scores (Average per category)
            $categories = AssessmentCategory::all();
            foreach ($categories as $category) {
                $avgScore = UserAssessmentAnswer::where('user_id', $user->id)
                    ->whereHas('question', function ($query) use ($category) {
                        $query->where('category_id', $category->id);
                    })
                    ->average('score');

                if ($avgScore) {
                    PersonalityScore::create([
                        'user_id' => $user->id,
                        'category_id' => $category->id,
                        'score' => $avgScore
                    ]);
                }
            }
        });

        // Trigger recommendation calculation
        $cfService = new CertaintyFactorService();
        $results = $cfService->calculate($user);

        // Store top results in recommendation_histories if needed
        
        return redirect()->route('dashboard')->with('success', 'Assessment completed! Your personality profile and recommendations have been updated.');
    }
}
