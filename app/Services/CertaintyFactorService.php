<?php

namespace App\Services;

use App\Models\JobListing;
use App\Models\Skill;
use App\Models\User;

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
        
        $jobs = JobListing::with('category')->where('is_active', true)->get();
        $recommendations = [];

        foreach ($jobs as $job) {
            $score = $this->calculateJobScore($job, $userSkills, $userInterests);
            
            if ($score > 0) {
                $recommendations[] = [
                    'job_id' => $job->id,
                    'job_title' => $job->title,
                    'category' => $job->category->name,
                    'score' => round($score, 4),
                    'confidence' => round($score * 100, 2),
                    'explanation' => $this->generateExplanation($job, $userSkills, $userInterests)
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
    private function calculateJobScore($job, $userSkills, $userInterests): float
    {
        $cf_total = 0;
        $evidence_found = false;

        // 1. Skill Matching (Expert CF: 0.8)
        foreach ($userSkills as $skill) {
            if ($this->isSkillRelevant($job, $skill)) {
                $cf_user = $skill->pivot->level / 5; // Normalize level 1-5 to 0.2-1.0
                $cf_expert = 0.8;
                $cf_current = $cf_expert * $cf_user;

                $cf_total = $this->combineCF($cf_total, $cf_current);
                $evidence_found = true;
            }
        }

        // 2. Interest Matching (Expert CF: 0.6)
        foreach ($userInterests as $interest) {
            if ($this->isInterestRelevant($job, $interest)) {
                $cf_user = 0.9; // Assume high interest value if user selected it
                $cf_expert = 0.6;
                $cf_current = $cf_expert * $cf_user;

                $cf_total = $this->combineCF($cf_total, $cf_current);
                $evidence_found = true;
            }
        }

        return $evidence_found ? $cf_total : 0;
    }

    /**
     * CF Combination Formula: CF_combined = CF1 + CF2 * (1 - CF1)
     */
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

    /**
     * Simple relevance check (could be more sophisticated).
     */
    private function isSkillRelevant($job, $skill): bool
    {
        $keywords = explode(' ', strtolower($job->title . ' ' . $job->description . ' ' . $job->requirements));
        return in_array(strtolower($skill->name), $keywords);
    }

    private function isInterestRelevant($job, $interest): bool
    {
        $keywords = explode(' ', strtolower($job->title . ' ' . $job->description));
        return stripos($job->description, $interest) !== false || stripos($job->title, $interest) !== false;
    }

    private function generateExplanation($job, $userSkills, $userInterests): string
    {
        $reasons = [];
        foreach ($userSkills as $skill) {
            if ($this->isSkillRelevant($job, $skill)) {
                $reasons[] = "You have proficiency in " . $skill->name;
            }
        }
        foreach ($userInterests as $interest) {
            if ($this->isInterestRelevant($job, $interest)) {
                $reasons[] = "This matches your interest in " . $interest;
            }
        }

        return implode('. ', array_slice($reasons, 0, 3)) . '.';
    }
}
