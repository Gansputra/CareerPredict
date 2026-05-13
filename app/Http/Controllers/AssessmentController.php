<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AssessmentCategory;
use App\Models\AssessmentQuestion;
use App\Models\UserAssessmentAnswer;
use App\Models\PersonalityScore;
use App\Models\Skill;
use App\Models\Interest;
use App\Models\Recommendation;
use App\Models\RecommendationHistory;
use App\Services\CertaintyFactorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    public function index()
    {
        $categories = AssessmentCategory::with('questions')->get();
        $skills = Skill::all();
        $interests = Interest::all();

        // Check if user has previous answers (for showing "retake" state)
        $hasHistory = UserAssessmentAnswer::where('user_id', Auth::id())->exists();

        return view('expert.assessment', compact('categories', 'skills', 'interests', 'hasHistory'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        try {
            DB::transaction(function () use ($user, $request) {
                // 1. Save personality assessment answers
                $answers = $request->input('answers', []);

                // Remove old data if retaking
                UserAssessmentAnswer::where('user_id', $user->id)->delete();
                PersonalityScore::where('user_id', $user->id)->delete();

                foreach ($answers as $qId => $score) {
                    UserAssessmentAnswer::create([
                        'user_id' => $user->id,
                        'question_id' => $qId,
                        'score' => $score
                    ]);
                }

                // 2. Calculate weighted personality scores per category
                $categories = AssessmentCategory::with('questions')->get();
                foreach ($categories as $category) {
                    $totalWeightedScore = 0;
                    $totalWeight = 0;

                    foreach ($category->questions as $question) {
                        if (isset($answers[$question->id])) {
                            $totalWeightedScore += $answers[$question->id] * $question->weight;
                            $totalWeight += $question->weight;
                        }
                    }

                    if ($totalWeight > 0) {
                        PersonalityScore::create([
                            'user_id' => $user->id,
                            'category_id' => $category->id,
                            'score' => round($totalWeightedScore / $totalWeight, 2)
                        ]);
                    }
                }

                // 3. Save user skills
                if ($request->has('skills')) {
                    $user->skills()->detach();
                    foreach ($request->skills as $skillId => $level) {
                        if ($level > 0) {
                            $user->skills()->attach($skillId, ['level' => $level]);
                        }
                    }
                }

                // 4. Save user interests
                if ($request->has('interests')) {
                    $user->interests()->sync($request->interests);
                }
            });

            // 5. Run CF Engine outside transaction for performance
            $cfService = new CertaintyFactorService();
            $results = $cfService->calculate($user);

            // 6. Save top 10 recommendations
            Recommendation::where('user_id', $user->id)->delete();
            foreach (array_slice($results, 0, 10) as $res) {
                Recommendation::create([
                    'user_id' => $user->id,
                    'job_id' => $res['job_id'],
                    'score' => $res['score'],
                    'confidence' => $res['confidence'],
                    'explanation' => $res['explanation'],
                ]);
            }

            // 7. Save to history
            RecommendationHistory::create([
                'user_id' => $user->id,
                'results_data' => $results,
            ]);

            return redirect()->route('expert.results')
                ->with('success', 'Your Career DNA analysis is complete! Here are your personalized recommendations.');

        } catch (\Exception $e) {
            Log::error('Assessment processing failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Something went wrong while processing your assessment. Please try again.');
        }
    }

    public function reset()
    {
        $user = Auth::user();

        DB::transaction(function () use ($user) {
            UserAssessmentAnswer::where('user_id', $user->id)->delete();
            PersonalityScore::where('user_id', $user->id)->delete();
            Recommendation::where('user_id', $user->id)->delete();
            $user->skills()->detach();
            $user->interests()->detach();
        });

        return redirect()->route('assessment.index')
            ->with('success', 'Your Career DNA data has been reset. You can now retake the assessment.');
    }
}
