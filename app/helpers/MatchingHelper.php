<?php
namespace App\Helpers;

/**
 * Helper class for calculating resume match percentages.
 * All methods are static so they can be called without instantiation.
 */
class MatchingHelper
{
    /**
     * Calculate how well a candidate's skills match a job's required skills.
     * Both parameters are comma‑separated strings.
     *
     * @param string $requirements Job required skills
     * @param string $skills Candidate skills
     * @return int Percentage (0‑100)
     */
    public static function calculateMatch(string $requirements, string $skills): int
    {
        $required = array_filter(array_map('trim', explode(',', strtolower($requirements))));
        $candidate = array_filter(array_map('trim', explode(',', strtolower($skills))));
        if (empty($required)) {
            return 0;
        }
        $matched = 0;
        foreach ($required as $skill) {
            if (in_array($skill, $candidate, true)) {
                $matched++;
            }
        }
        return (int) round(($matched / count($required)) * 100);
    }
}
