<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\Skill;
use App\Models\UserSkill;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->only(['name', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update or Create Profile
        $profileData = $request->safe()->only(['phone', 'headline', 'bio']);
        
        if ($request->hasFile('cv')) {
            $file = $request->file('cv');
            $path = $file->store('cvs', 'public');
            $profileData['cv_path'] = $path;

            // Extract Text if PDF
            if ($file->getClientOriginalExtension() === 'pdf') {
                try {
                    $parser = new Parser();
                    $pdf = $parser->parseFile($file->getRealPath());
                    $text = $pdf->getText();
                    
                    // Basic Skill Extraction Logic
                    $skillCount = $this->extractSkillsFromText($user, $text);
                    session()->flash('skill_count', $skillCount);
                } catch (\Exception $e) {
                    // Log error or ignore if failed
                }
            }
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        $message = 'Profile updated successfully.';
        $skillCount = session('skill_count', 0);
        
        if ($skillCount > 0) {
            $message .= ' We also detected ' . $skillCount . ' skills from your CV!';
        } elseif ($request->hasFile('cv')) {
            $message .= ' CV uploaded, but no matching skills were detected. Make sure your skills are listed clearly.';
        }

        return Redirect::route('profile.edit')->with('success', $message);
    }

    private function extractSkillsFromText($user, $text)
    {
        $allSkills = Skill::all();
        $detectedSkillIds = [];
        // Normalize text: remove extra spaces and newlines
        $normalizedText = strtolower(preg_replace('/\s+/', ' ', $text));
        $count = 0;

        foreach ($allSkills as $skill) {
            $skillName = strtolower($skill->name);
            // Search for whole word or exact phrase
            if (preg_match('/\b' . preg_quote($skillName, '/') . '\b/i', $normalizedText)) {
                $detectedSkillIds[$skill->id] = ['level' => 3]; 
                $count++;
            }
        }

        if (!empty($detectedSkillIds)) {
            $user->skills()->syncWithoutDetaching($detectedSkillIds);
        }

        return $count;
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
