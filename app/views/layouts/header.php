<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' . APP_NAME : APP_NAME; ?></title>
    <meta name="description" content="<?php echo isset($description) ? $description : 'A modern online recruitment portal connecting job seekers and employers.'; ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
<link rel="stylesheet" href="<?php echo URL_ROOT; ?>/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- Navigation Bar -->
<!-- <nav class="navbar navbar-expand-lg sticky-top"> -->
    <nav class="navbar navbar-expand-lg bg-white">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?php echo URL_ROOT; ?>">
            <span class="brand-logo-icon me-2">
                <i class="fa-solid fa-briefcase"></i>
            </span>
            <span class="brand-text">E-Recruit</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?php echo URL_ROOT; ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="<?php echo URL_ROOT; ?>/jobs">Browse Jobs</a>
                </li>
                
                <!-- Authentication Links -->
                <?php if (\App\Core\Session::isLoggedIn()) : ?>
                    <li class="nav-item dropdown ms-lg-3">
                        <a class="nav-link dropdown-toggle fw-bold text-dark px-3 py-2 border rounded-3 bg-light" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-circle-user text-primary me-1"></i> 
                            <?php echo htmlspecialchars(\App\Core\Session::get('user_name')); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 mt-2" aria-labelledby="navbarDropdown">
                            <?php 
                                $dashboardUrl = '';
                                $role = \App\Core\Session::get('user_role');
                                if ($role === 'admin') $dashboardUrl = '/admin/dashboard';
                                elseif ($role === 'employer') $dashboardUrl = '/employer/dashboard';
                                else $dashboardUrl = '/candidate/dashboard';
                            ?>
                            <li>
                                <a class="dropdown-item py-2 rounded-2" href="<?php echo URL_ROOT . $dashboardUrl; ?>">
                                    <i class="fa-solid fa-gauge text-primary me-2"></i>Dashboard
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2 rounded-2 text-danger" href="<?php echo URL_ROOT; ?>/logout">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else : ?>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-primary btn-nav px-4 me-2" href="<?php echo URL_ROOT; ?>/login">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-nav px-4" href="<?php echo URL_ROOT; ?>/register">
                            <i class="fa-solid fa-user-plus me-1"></i> Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="main-wrapper">
