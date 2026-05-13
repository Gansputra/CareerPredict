<?php

namespace App\Services;

use App\Models\JobListing;
use App\Models\Skill;
use App\Models\User;
use App\Models\PersonalityScore;

class CertaintyFactorService
{
    /**
     * Calculate recommendation scores for a user.
     * 
     * @param User $user
     * @return array
     */
    public function calculate(User $user): array
    {
        $userSkills = $user->skills;
        $userInterests = $user->interests->pluck('name')->toArray();
        $personalityScores = $user->personalityScores->load('category');
        
        $jobs = JobListing::with('category')->where('is_active', true)->get();
        $recommendations = [];

        foreach ($jobs as $job) {
            $score = $this->calculateJobScore($job, $userSkills, $userInterests, $personalityScores);
            
            if ($score > 0) {
                $recommendations[] = [
                    'job_id' => $job->id,
                    'job_title' => $job->title,
                    'category' => $job->category->name,
                    'score' => round($score, 4),
                    'confidence' => round($score * 100, 2),
                    'explanation' => $this->generateExplanation($job, $userSkills, $userInterests, $personalityScores)
                ];
            }
        }

        // Sort by score descending
        usort($recommendations, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $recommendations;
    }

    /**
     * Calculate CF for a specific job.
     */
    private function calculateJobScore($job, $userSkills, $userInterests, $personalityScores): float
    {
        $cf_total = 0;
        $evidence_found = false;

        // 1. Skill Matching (Expert CF: 0.8)
        foreach ($userSkills as $skill) {
            if ($this->isSkillRelevant($job, $skill)) {
                $cf_user = $skill->pivot->level / 5; 
                $cf_expert = 0.8;
                $cf_current = $cf_expert * $cf_user;

                $cf_total = $this->combineCF($cf_total, $cf_current);
                $evidence_found = true;
            }
        }

        // 2. Interest Matching (Expert CF: 0.6)
        foreach ($userInterests as $interest) {
            if ($this->isInterestRelevant($job, $interest)) {
                $cf_user = 0.9;
                $cf_expert = 0.6;
                $cf_current = $cf_expert * $cf_user;

                $cf_total = $this->combineCF($cf_total, $cf_current);
                $evidence_found = true;
            }
        }

        // 3. Personality Matching (Expert CF: 0.7)
        foreach ($personalityScores as $pScore) {
            if ($this->isPersonalityRelevant($job, $pScore)) {
                $cf_user = $pScore->score / 5; // Normalize 1-5 to 0.2-1.0
                $cf_expert = 0.7;
                $cf_current = $cf_expert * $cf_user;

                $cf_total = $this->combineCF($cf_total, $cf_current);
                $evidence_found = true;
            }
        }

        return $evidence_found ? $cf_total : 0;
    }

    private function combineCF($cf1, $cf2): float
    {
        if ($cf1 >= 0 && $cf2 >= 0) {
            return $cf1 + $cf2 * (1 - $cf1);
        } elseif ($cf1 < 0 && $cf2 < 0) {
            return $cf1 + $cf2 * (1 + $cf1);
        } else {
            return ($cf1 + $cf2) / (1 - min(abs($cf1), abs($cf2)));
        }
    }

    private function isSkillRelevant($job, $skill): bool
    {
        $searchable = strtolower($job->title . ' ' . $job->description . ' ' . $job->requirements);
        return str_contains($searchable, strtolower($skill->name));
    }

    private function isInterestRelevant($job, $interest): bool
    {
        $searchable = strtolower($job->title . ' ' . $job->description);
        return str_contains($searchable, strtolower($interest));
    }

    private function isPersonalityRelevant($job, $pScore): bool
    {
        $mapping = [
            'analytical' => ['data', 'analyst', 'research', 'engineer', 'developer', 'science'],
            'creative' => ['design', 'ui', 'ux', 'creative', 'marketing', 'content'],
            'technical' => ['developer', 'engineer', 'it', 'software', 'backend', 'frontend'],
            'communication' => ['manager', 'lead', 'marketing', 'sales', 'support'],
            'leadership' => ['manager', 'lead', 'director', 'senior', 'head'],
        ];

        $slug = $pScore->category->slug;
        if (!isset($mapping[$slug])) return false;

        $searchable = strtolower($job->title . ' ' . $job->description);
        foreach ($mapping[$slug] as $keyword) {
            if (str_contains($searchable, $keyword)) return true;
        }

        return false;
    }

    private function generateExplanation($job, $userSkills, $userInterests, $personalityScores): string
    {
        $reasons = [];
        foreach ($userSkills as $skill) {
            if ($this->isSkillRelevant($job, $skill)) {
                $reasons[] = "High proficiency in " . $skill->name;
            }
        }
        
        foreach ($personalityScores as $pScore) {
            if ($this->isPersonalityRelevant($job, $pScore) && $pScore->score >= 4) {
                $reasons[] = "Strong " . $pScore->category->name . " traits detected";
            }
        }

        if (count($reasons) === 0) {
            return "This role aligns with your general profile and interests.";
        }

        return implode(', ', array_slice($reasons, 0, 2)) . " matching this role's core requirements.";
    }
}
