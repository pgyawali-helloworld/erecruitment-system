<?php

namespace App\Helpers;

/**
 * Helper class for calculating resume match percentages.
 */
class MatchingHelper
{
    /**
     * Calculate skills match percentage.
     */
    public static function calculateMatch(
        string $requirements,
        string $skills
    ): int {

        $required = array_filter(
            array_map(
                'trim',
                explode(',', strtolower($requirements))
            )
        );

        $candidate = array_filter(
            array_map(
                'trim',
                explode(',', strtolower($skills))
            )
        );

        // No skill requirement
        if (empty($required)) {
            return 0;
        }

        $matched = 0;

        foreach ($required as $skill) {
            if (in_array($skill, $candidate, true)) {
                $matched++;
            }
        }

        return (int) round(
            ($matched / count($required)) * 100
        );
    }

    /**
     * Calculate experience match percentage.
     *
     * Required = 2 years
     * Candidate = 1 year
     * Result = 50%
     *
     * Equal or greater experience = 100%.
     */
    public static function calculateExperienceMatch(
        float $requiredExperience,
        float $candidateExperience
    ): int {

        // No experience requirement
        if ($requiredExperience <= 0) {
            return 100;
        }

        // Candidate has zero experience
        if ($candidateExperience <= 0) {
            return 0;
        }

        $percentage = (
            $candidateExperience /
            $requiredExperience
        ) * 100;

        return (int) round(
            min($percentage, 100)
        );
    }

    /**
     * Calculate final overall match.
     *
     * Rules:
     *
     * 1. Skills + Experience requirement
     *    = average of both scores.
     *
     * 2. Skills requirement only
     *    = skill score.
     *
     * 3. Experience requirement only
     *    = experience score.
     *
     * 4. Neither requirement exists
     *    = 0%.
     */
    public static function calculateOverallMatch(
        int $skillMatch,
        int $experienceMatch,
        bool $hasSkillRequirement,
        bool $hasExperienceRequirement
    ): int {

        // Both requirements exist
        if ($hasSkillRequirement && $hasExperienceRequirement) {

            return (int) round(
                ($skillMatch + $experienceMatch) / 2
            );
        }

        // Skills requirement only
        if ($hasSkillRequirement) {
            return (int) round(
                min($skillMatch, 100)
            );
        }

        // Experience requirement only
        if ($hasExperienceRequirement) {
            return (int) round(
                min($experienceMatch, 100)
            );
        }

        // No requirements
        return 0;
    }
}
?>