<?php
$activeTab = isset($activeTab) ? $activeTab : 'dashboard';
?>
<div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
    <div class="text-center py-3 border-bottom mb-3">
        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px;">
            <i class="fa-solid fa-user-shield fs-2"></i>
        </div>
        <h5 class="fw-bold mb-0"><?php echo htmlspecialchars(\App\Core\Session::get('user_name')); ?></h5>
        <span class="badge bg-danger mt-2 px-3 py-2 rounded-pill small">Administrator</span>
    </div>
    <div class="nav flex-column nav-pills gap-1">
        <a class="nav-link <?php echo $activeTab === 'dashboard' ? 'active text-white' : 'text-secondary'; ?> py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/admin/dashboard">
            <i class="fa-solid fa-gauge me-2"></i>Overview
        </a>
        <a class="nav-link <?php echo $activeTab === 'categories' ? 'active text-white' : 'text-secondary'; ?> py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/admin/categories">
            <i class="fa-solid fa-tags me-2"></i>Job Categories
        </a>
        <a class="nav-link <?php echo $activeTab === 'employers' ? 'active text-white' : 'text-secondary'; ?> py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/admin/employers">
            <i class="fa-solid fa-building me-2"></i>Manage Employers
        </a>
        <a class="nav-link <?php echo $activeTab === 'candidates' ? 'active text-white' : 'text-secondary'; ?> py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/admin/candidates">
            <i class="fa-solid fa-users me-2"></i>Manage Job Seekers
        </a>
        <a class="nav-link <?php echo $activeTab === 'reports' ? 'active text-white' : 'text-secondary'; ?> py-2.5 rounded-3 fw-semibold" href="<?php echo URL_ROOT; ?>/admin/reports">
            <i class="fa-solid fa-chart-line me-2"></i>Reports & Logs
        </a>
    </div>
</div>
