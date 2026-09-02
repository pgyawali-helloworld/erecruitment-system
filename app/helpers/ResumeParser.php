<?php

namespace App\Helpers;

use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;

class ResumeParser
{
    /**
     * Extract text from PDF resume
     */
    public function extractPDF($filePath)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);

        return $pdf->getText();
    }

    /**
     * Extract text from DOCX resume
     */
    public function extractDOCX($filePath)
    {
        $phpWord = IOFactory::load($filePath);

        $text = "";

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {

                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . " ";
                }
            }
        }

        return $text;
    }

    /**
     * Extract text from TXT resume
     */
    public function extractTXT($filePath)
    {
        return file_get_contents($filePath);
    }

    /**
     * Detect skills from resume text
     */
    public function extractSkills($text)
    {
        $skillList = [
            "PHP",
            "Java",
            "SQL",
            "MySQL",
            "HTML",
            "CSS",
            "JavaScript",
            "React",
            "Bootstrap",
            "Git"
        ];

        $foundSkills = [];

        foreach ($skillList as $skill) {
            if (stripos($text, $skill) !== false) {
                $foundSkills[] = $skill;
            }
        }

        return $foundSkills;
    }

    /**
     * Extract experience section and calculate total experience.
     */
    public function extractExperience($text)
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        $headings = [
            "WORK EXPERIENCE",
            "PROFESSIONAL EXPERIENCE",
            "WORK HISTORY",
            "EMPLOYMENT HISTORY",
            "EMPLOYMENT EXPERIENCE",
            "EXPERIENCE"
        ];

        $startPosition = false;
        $matchedHeading = '';

        foreach ($headings as $heading) {
            $position = stripos($text, $heading);

            if ($position !== false) {
                if ($startPosition === false || $position < $startPosition) {
                    $startPosition = $position;
                    $matchedHeading = $heading;
                }
            }
        }

        if ($startPosition === false) {
            return [
                'section' => '',
                'total_years' => 0,
                'formatted' => 'No experience found'
            ];
        }

        $experienceText = substr(
            $text,
            $startPosition + strlen($matchedHeading)
        );

        /*
         * Stop when another major resume section starts.
         */
        $nextHeadings = [
            "EDUCATION",
            "ACADEMIC QUALIFICATIONS",
            "SKILLS",
            "TECHNICAL SKILLS",
            "PROJECTS",
            "PERSONAL PROJECTS",
            "CERTIFICATIONS",
            "CERTIFICATES",
            "ACHIEVEMENTS",
            "LANGUAGES",
            "INTERESTS",
            "REFERENCES"
        ];

        $endPosition = strlen($experienceText);

        foreach ($nextHeadings as $heading) {
            $position = stripos($experienceText, $heading);

            if ($position !== false && $position < $endPosition) {
                $endPosition = $position;
            }
        }

        $experienceText = substr(
            $experienceText,
            0,
            $endPosition
        );

        $experienceText = trim($experienceText);

        /*
         * Calculate experience from explicit durations.
         *
         * Examples:
         * 2 years
         * 2 years 6 months
         * 6 months
         * 1 year 3 months
         */
        $totalMonths = 0;

        preg_match_all(
            '/(\d+(?:\.\d+)?)\s*(?:years?|yrs?)\s*(?:and\s*)?(?:(\d+)\s*(?:months?|mos?))?/i',
            $experienceText,
            $yearMatches,
            PREG_SET_ORDER
        );

        foreach ($yearMatches as $match) {
            $years = (float)$match[1];
            $months = isset($match[2]) && $match[2] !== ''
                ? (int)$match[2]
                : 0;

            $totalMonths += ($years * 12) + $months;
        }

        /*
         * If no year duration was found,
         * check for month-only durations.
         */
        if ($totalMonths === 0) {
            preg_match_all(
                '/(\d+)\s*(?:months?|mos?)/i',
                $experienceText,
                $monthMatches
            );

            foreach ($monthMatches[1] as $months) {
                $totalMonths += (int)$months;
            }
        }

        /*
         * Also support date ranges such as:
         *
         * Jan 2023 - Dec 2024
         * January 2022 - Present
         *
         * Only use date ranges if explicit duration
         * was not already detected.
         */
        if ($totalMonths === 0) {

            $currentDate = new \DateTime();

            preg_match_all(
                '/\b(January|February|March|April|May|June|July|August|September|October|November|December|Jan|Feb|Mar|Apr|Jun|Jul|Aug|Sep|Sept|Oct|Nov|Dec)?\.?\s*(20\d{2})\s*[-–]\s*(Present|Current|Now|(?:January|February|March|April|May|June|July|August|September|October|November|December|Jan|Feb|Mar|Apr|Jun|Jul|Aug|Sep|Sept|Oct|Nov|Dec)?\.?\s*20\d{2})/i',
                $experienceText,
                $dateRanges,
                PREG_SET_ORDER
            );

            foreach ($dateRanges as $range) {

                $startMonth = $range[1] ?: 'January';
                $startYear = $range[2];

                $startString = $startMonth . ' ' . $startYear;

                try {
                    $startDate = new \DateTime($startString);

                    if (
                        preg_match(
                            '/^(Present|Current|Now)$/i',
                            trim($range[3])
                        )
                    ) {
                        $endDate = $currentDate;
                    } else {

                        $endString = trim($range[3]);

                        if (
                            preg_match(
                                '/^20\d{2}$/',
                                $endString
                            )
                        ) {
                            $endString = 'January ' . $endString;
                        }

                        $endDate = new \DateTime($endString);
                    }

                    $months = (
                        ($endDate->format('Y') - $startDate->format('Y')) * 12
                    ) + (
                        $endDate->format('n') - $startDate->format('n')
                    );

                    if ($months > 0) {
                        $totalMonths += $months;
                    }

                } catch (\Exception $e) {
                    // Ignore invalid date ranges.
                }
            }
        }

        $totalYears = round($totalMonths / 12, 2);

        /*
         * Human-readable experience.
         */
        if ($totalMonths <= 0) {
            $formatted = 'No experience found';
        } else {

            $years = floor($totalMonths / 12);
            $months = $totalMonths % 12;

            $parts = [];

            if ($years > 0) {
                $parts[] = $years . ' ' .
                    ($years == 1 ? 'year' : 'years');
            }

            if ($months > 0) {
                $parts[] = $months . ' ' .
                    ($months == 1 ? 'month' : 'months');
            }

            $formatted = implode(' ', $parts);
        }

        return [
            'section' => $experienceText,
            'total_years' => $totalYears,
            'total_months' => $totalMonths,
            'formatted' => $formatted
        ];
    }

    /**
     * Extract education section.
     */
    public function extractEducation($text)
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        $headings = [
            "EDUCATION",
            "ACADEMIC QUALIFICATIONS",
            "EDUCATIONAL QUALIFICATION"
        ];

        $startPosition = false;
        $matchedHeading = '';

        foreach ($headings as $heading) {
            $position = stripos($text, $heading);

            if ($position !== false) {
                if ($startPosition === false || $position < $startPosition) {
                    $startPosition = $position;
                    $matchedHeading = $heading;
                }
            }
        }

        if ($startPosition === false) {
            return '';
        }

        $education = substr(
            $text,
            $startPosition + strlen($matchedHeading)
        );

        $nextHeadings = [
            "WORK EXPERIENCE",
            "PROFESSIONAL EXPERIENCE",
            "WORK HISTORY",
            "EXPERIENCE",
            "SKILLS",
            "TECHNICAL SKILLS",
            "PROJECTS",
            "CERTIFICATIONS",
            "CERTIFICATES",
            "ACHIEVEMENTS",
            "LANGUAGES",
            "REFERENCES"
        ];

        $endPosition = strlen($education);

        foreach ($nextHeadings as $heading) {
            $position = stripos($education, $heading);

            if ($position !== false && $position < $endPosition) {
                $endPosition = $position;
            }
        }

        $education = substr($education, 0, $endPosition);

        return trim(
            preg_replace('/[ \t]+/', ' ', $education)
        );
    }

    /**
     * Extract email.
     */
    public function extractEmail($text)
    {
        preg_match(
            '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i',
            $text,
            $matches
        );

        return $matches[0] ?? '';
    }

    /**
     * Extract phone number.
     */
    public function extractPhone($text)
    {
        preg_match(
            '/(?:\+977[-\s]?)?(?:9[678]\d{8}|01[-\s]?\d{7,8})/',
            $text,
            $matches
        );

        return $matches[0] ?? '';
    }

    /**
     * Main parser function.
     */
    public function parse($filePath)
    {
        $extension = strtolower(
            pathinfo($filePath, PATHINFO_EXTENSION)
        );

        if ($extension === "pdf") {

            $text = $this->extractPDF($filePath);

        } elseif ($extension === "docx") {

            $text = $this->extractDOCX($filePath);

        } elseif ($extension === "txt") {

            $text = $this->extractTXT($filePath);

        } else {

            throw new \Exception("Unsupported file format");
        }

        $skills = $this->extractSkills($text);
        $education = $this->extractEducation($text);
        $experience = $this->extractExperience($text);
        $email = $this->extractEmail($text);
        $phone = $this->extractPhone($text);

        return [
            $text,
            $skills,
            $education,
            $experience,
            $email,
            $phone
        ];
    }
}

