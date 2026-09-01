<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

/**
 * AuthController Class
 * Manages user login, registration, and logout workflows.
 */
class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        // Load User model
        $this->userModel = $this->model('User');
    }

    /**
     * Show Login Form
     */
    public function showLogin() {
        // Redirect to dashboard if already logged in
        if (Session::isLoggedIn()) {
            $this->redirectBasedOnRole(Session::get('user_role'));
        }

        $data = [
            'title' => 'Login to E-Recruit',
            'email' => '',
            'email_err' => '',
            'password_err' => ''
        ];

        $this->view('auth/login', $data);
    }

    /**
     * Handle Login Submission
     */
    public function login() {
        // Process form if POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data = [
                'title' => 'Login to E-Recruit',
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => ''
            ];

            // 1. Server-side Validation
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter your email.';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Please enter a valid email format.';
            }

            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter your password.';
            }

            // 2. Authenticate
            if (empty($data['email_err']) && empty($data['password_err'])) {
                // Check user
                $user = $this->userModel->findUserByEmail($data['email']);

                if ($user) {
                    // Verify hashed password
                    if (password_verify($data['password'], $user->password)) {
                        // User verified, check if active
                        if ($user->status !== 'active') {
                            Session::setFlash('login_error', 'Your account has been deactivated. Please contact support.', 'alert-danger');
                            $this->view('auth/login', $data);
                            return;
                        }

                        // Create session variables
                        Session::set('user_id', $user->id);
                        Session::set('user_name', $user->name);
                        Session::set('user_email', $user->email);
                        Session::set('user_role', $user->role);

                        // Redirect based on user role
                        $this->redirectBasedOnRole($user->role);
                    } else {
                        // Invalid Password
                        $data['password_err'] = 'Incorrect password.';
                        Session::setFlash('login_error', 'Invalid login credentials. Please try again.', 'alert-danger');
                        $this->view('auth/login', $data);
                    }
                } else {
                    // User Not Found
                    $data['email_err'] = 'No account found with this email.';
                    Session::setFlash('login_error', 'Invalid login credentials. Please try again.', 'alert-danger');
                    $this->view('auth/login', $data);
                }
            } else {
                // Load view with errors
                $this->view('auth/login', $data);
            }
        } else {
            // If request is not POST, redirect to show login
            $this->redirect('login');
        }
    }

    /**
     * Show Registration Form
     */
    public function showRegister() {
        if (Session::isLoggedIn()) {
            $this->redirectBasedOnRole(Session::get('user_role'));
        }

        $data = [
            'title' => 'Register with E-Recruit',
            'role' => 'candidate', // Default active tab/role
            'name' => '',
            'email' => '',
            'phone' => '',
            'company_name' => '',
            'password' => '',
            'confirm_password' => '',
            'name_err' => '',
            'email_err' => '',
            'phone_err' => '',
            'company_name_err' => '',
            'password_err' => '',
            'confirm_password_err' => ''
        ];

        $this->view('auth/register', $data);
    }

    /**
     * Handle Registration Submission
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $role = isset($_POST['role']) ? trim($_POST['role']) : 'candidate';

            $data = [
                'title' => 'Register with E-Recruit',
                'role' => $role,
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
                'company_name' => isset($_POST['company_name']) ? trim($_POST['company_name']) : '',
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'name_err' => '',
                'email_err' => '',
                'phone_err' => '',
                'company_name_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // 1. Common Server-Side Validations
            if (empty($data['name'])) {
                $data['name_err'] = 'Full Name is required.';
            }

            if (empty($data['email'])) {
                $data['email_err'] = 'Email is required.';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Please enter a valid email format.';
            } else {
                // Check if email already exists
                if ($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_err'] = 'This email is already registered.';
                }
            }

            // Role-specific Validations
            if ($role === 'candidate') {
                if (empty($data['phone'])) {
                    $data['phone_err'] = 'Phone number is required.';
                } elseif (!preg_match('/^[0-9+]{10,15}$/', $data['phone'])) {
                    $data['phone_err'] = 'Please enter a valid phone number (10-15 digits).';
                }
            } elseif ($role === 'employer') {
                if (empty($data['company_name'])) {
                    $data['company_name_err'] = 'Company name is required.';
                }
            } else {
                $data['role'] = 'candidate'; // Force fallback
            }

            // Password Validations
            if (empty($data['password'])) {
                $data['password_err'] = 'Password is required.';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters.';
            }

            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm password.';
            } else {
                if ($data['password'] !== $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match.';
                }
            }

            // 2. Register User if no errors
            if (
                empty($data['name_err']) && 
                empty($data['email_err']) && 
                empty($data['phone_err']) && 
                empty($data['company_name_err']) && 
                empty($data['password_err']) && 
                empty($data['confirm_password_err'])
            ) {
                // Hash Password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Register
                if ($this->userModel->register($data)) {
                    Session::setFlash('register_success', 'Registration successful! You can now log in.', 'alert-success');
                    $this->redirect('login');
                } else {
                    Session::setFlash('register_error', 'Something went wrong during registration. Please try again.', 'alert-danger');
                    $this->view('auth/register', $data);
                }
            } else {
                // View registration with errors
                $this->view('auth/register', $data);
            }
        } else {
            $this->redirect('register');
        }
    }

    /**
     * Handle Logout
     */
    public function logout() {
        Session::destroy();
        Session::init();
        Session::setFlash('logout_success', 'You have been successfully logged out.', 'alert-success');
        $this->redirect('login');
    }

    /**
     * Redirect utility helper based on user role
     */
    private function redirectBasedOnRole($role) {
        switch ($role) {
            case 'admin':
                $this->redirect('admin/dashboard');
                break;
            case 'employer':
                $this->redirect('employer/dashboard');
                break;
            case 'candidate':
                $this->redirect('candidate/dashboard');
                break;
            default:
                $this->redirect('');
        }
    }
}
