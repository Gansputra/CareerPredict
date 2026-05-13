<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Questionnaire;
use App\Models\Skill;
use App\Models\Interest;
use App\Models\Recommendation;
use App\Models\RecommendationHistory;
use App\Services\CertaintyFactorService;
use Illuminate\Support\Facades\Auth;

class ExpertSystemController extends Controller
{
    protected $cfService;

    public function __construct(CertaintyFactorService $cfService)
    {
        $this->cfService = $cfService;
    }

    public function index()
    {
        $skills = Skill::all();
        $interests = Interest::all();
        $questions = Questionnaire::all();

        return view('expert.index', compact('skills', 'interests', 'questions'));
    }

    public function calculate(Request $request)
    {
        $user = Auth::user();

        // 1. Update user skills
        if ($request->has('skills')) {
            $user->skills()->detach();
            foreach ($request->skills as $skillId => $level) {
                if ($level > 0) {
                    $user->skills()->attach($skillId, ['level' => $level]);
                }
            }
        }

        // 2. Update user interests
        if ($request->has('interests')) {
            $user->interests()->sync($request->interests);
        }

        // 3. Run CF Engine
        $results = $this->cfService->calculate($user);

        // 4. Save results to recommendations table
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

        // 5. Save to history
        RecommendationHistory::create([
            'user_id' => $user->id,
            'results_data' => $results,
        ]);

        return redirect()->route('expert.results');
    }

    public function results()
    {
        $recommendations = Recommendation::with('job.category')
            ->where('user_id', Auth::id())
            ->orderBy('score', 'desc')
            ->get();

        return view('expert.results', compact('recommendations'));
    }
}
