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
                    $this->extractSkillsFromText($user, $text);
                } catch (\Exception $e) {
                    // Log error or ignore if failed
                }
            }
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    private function extractSkillsFromText($user, $text)
    {
        $allSkills = Skill::all();
        $detectedSkillIds = [];
        $text = strtolower($text);

        foreach ($allSkills as $skill) {
            if (str_contains($text, strtolower($skill->name))) {
                $detectedSkillIds[$skill->id] = ['level' => 3]; // Default level 3 for detected skills
            }
        }

        if (!empty($detectedSkillIds)) {
            // Sync detected skills (without detaching existing ones if they weren't in CV? 
            // Better to use syncWithoutDetaching)
            $user->skills()->syncWithoutDetaching($detectedSkillIds);
        }
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
