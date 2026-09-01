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
     * Main parser function
     */
    public function parse($filePath)
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));


        if ($extension === "pdf") {

            $text = $this->extractPDF($filePath);

        } elseif ($extension === "docx") {

            $text = $this->extractDOCX($filePath);

        } elseif ($extension === "txt") {

            $text = $this->extractTXT($filePath);

        } else {

            throw new \Exception("Unsupported file format");

        }


        // Placeholder values for fields not extracted in current implementation
        $education = '';
        $experience = '';
        $email = '';
        $phone = '';
        // Return indexed array as expected by ResumeController
        return [$text, $this->extractSkills($text), $education, $experience, $email, $phone];
    }
}