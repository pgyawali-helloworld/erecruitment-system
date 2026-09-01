<?php
namespace App\Controllers;

use App\Core\Controller;

/**
 * HomeController
 * Manages public landing page and home operations.
 */
class HomeController extends Controller {
    
    /**
     * Display the landing/home page
     */
    public function index() {
        $data = [
            'title' => 'Welcome to E-Recruitment Portal',
            'description' => 'Your gateway to a dream career. Search jobs, apply, and get hired!'
        ];
        
        $this->view('home/index', $data);
    }
}
