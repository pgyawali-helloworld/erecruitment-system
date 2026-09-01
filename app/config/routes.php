<?php
/**
 * Routes Configuration
 * Registers application routes with HTTP methods and mapped controller actions.
 */

use App\Core\Router;

$router = new Router();

// Public / Home routes
$router->get('/', 'HomeController@index');

// Auth routes
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// Job vacancy routes (Public & Candidate)
$router->get('/jobs', 'JobController@index');
$router->get('/jobs/view/{id}', 'JobController@show');
$router->post('/jobs/apply/{id}', 'JobController@apply');


// Candidate routes
$router->get('/candidate/dashboard', 'CandidateController@dashboard');
$router->get('/candidate/applications', 'CandidateController@applications');
$router->get('/candidate/profile', 'CandidateController@profile');
$router->post('/candidate/profile', 'CandidateController@profile');
// Resume routes
$router->get('/candidate/resume', 'CandidateController@resume');
$router->post('/resume/upload', 'ResumeController@upload');

// Employer routes
$router->get('/employer/dashboard', 'EmployerController@dashboard');
$router->get('/employer/jobs', 'EmployerController@jobs');
$router->get('/employer/jobs/create', 'EmployerController@showCreateJob');
$router->post('/employer/jobs/create', 'EmployerController@createJob');
$router->get('/employer/jobs/edit/{id}', 'EmployerController@showEditJob');
$router->post('/employer/jobs/edit/{id}', 'EmployerController@editJob');
$router->post('/employer/jobs/delete/{id}', 'EmployerController@deleteJob');
$router->get('/employer/applications', 'EmployerController@applications');
$router->post('/employer/applications/status/{id}', 'EmployerController@updateApplicationStatus');

// Admin routes
$router->get('/admin/dashboard', 'AdminController@dashboard');
$router->get('/admin/reports', 'AdminController@reports');

// Category routes (Admin)
$router->get('/admin/categories', 'AdminController@categories');
$router->post('/admin/categories/add', 'AdminController@addCategory');
$router->get('/admin/categories/edit/{id}', 'AdminController@showEditCategory');
$router->post('/admin/categories/edit/{id}', 'AdminController@editCategory');
$router->post('/admin/categories/delete/{id}', 'AdminController@deleteCategory');

// Employer management routes (Admin)
$router->get('/admin/employers', 'AdminController@employers');
$router->get('/admin/employers/view/{id}', 'AdminController@employerDetails');
$router->get('/admin/employers/status/{id}', 'AdminController@toggleEmployerStatus');
$router->post('/admin/employers/delete/{id}', 'AdminController@deleteEmployer');

// Candidate management routes (Admin)
$router->get('/admin/candidates', 'AdminController@candidates');
$router->get('/admin/candidates/view/{id}', 'AdminController@candidateDetails');
$router->get('/admin/candidates/status/{id}', 'AdminController@toggleCandidateStatus');
$router->post('/admin/candidates/delete/{id}', 'AdminController@deleteCandidate');

// Job & Application management routes (Admin)
$router->get('/admin/jobs', 'AdminController@jobs');
$router->get('/admin/jobs/status/{id}', 'AdminController@toggleJobStatus');
$router->post('/admin/jobs/delete/{id}', 'AdminController@deleteJob');
$router->get('/admin/applications', 'AdminController@applications');

// Return the router instance
return $router;
