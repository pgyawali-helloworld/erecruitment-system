<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <?php 
                $activeTab = 'reports';
                require APP_ROOT . '/views/layouts/admin_sidebar.php'; 
            ?>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="fw-extrabold mb-1">System Reports & Analytics</h2>
                    <p class="text-muted small mb-0">Live statistics and activity audits</p>
                </div>
                <button onclick="window.print()" class="btn btn-outline-dark btn-sm rounded-2 px-3">
                    <i class="fa-solid fa-print me-1"></i> Print Report
                </button>
            </div>

            <!-- Summary Statistics Cards -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Key Metrics</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 bg-light text-center">
                            <span class="text-secondary small fw-bold uppercase">Total Registered Users</span>
                            <h2 class="fw-extrabold text-primary mt-2">
                                <?php echo $stats['total_employers'] + $stats['total_candidates'] + 1; // including admin ?>
                            </h2>
                            <div class="small text-muted mt-1">
                                <?php echo $stats['total_employers']; ?> Employers | <?php echo $stats['total_candidates']; ?> Candidates
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 bg-light text-center">
                            <span class="text-secondary small fw-bold uppercase">Total Job Vacancies</span>
                            <h2 class="fw-extrabold text-success mt-2">
                                <?php echo $stats['total_jobs']; ?>
                            </h2>
                            <div class="small text-muted mt-1">
                                Open and active jobs on the platform
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 bg-light text-center">
                            <span class="text-secondary small fw-bold uppercase">Total Job Applications</span>
                            <h2 class="fw-extrabold text-warning mt-2">
                                <?php echo $stats['total_applications']; ?>
                            </h2>
                            <div class="small text-muted mt-1">
                                Resumes submitted for active openings
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jobs by Category -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Jobs by Category</h5>
<?php
// Add Chart.js CDN if not already loaded
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php
// Prepare data for Jobs by Category Pie Chart
$jobCategories = $jobsByCategory;
$jobLabels = [];
$jobCounts = [];
foreach ($jobCategories as $c) {
    $jobLabels[] = $c->name;
    $jobCounts[] = $c->job_count;
}
// Prepare data for System Activity Bar Chart
$activityCounts = [
    'user_registered' => 0,
    'job_posted' => 0,
    'application_submitted' => 0,
];
foreach ($recentActivities as $act) {
    if (array_key_exists($act->event_type, $activityCounts)) {
        $activityCounts[$act->event_type]++;
    }
}
$activityLabels = ['User Registered', 'Job Posted', 'Application Submitted'];
$activityData = [
    $activityCounts['user_registered'],
    $activityCounts['job_posted'],
    $activityCounts['application_submitted'],
];
?>
<!-- Charts Section -->
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
    <h5 class="fw-bold mb-3 border-bottom pb-2">Jobs by Category (Pie Chart)</h5>
    <canvas id="jobsByCategoryChart" width="400" height="400"></canvas>
</div>
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
    <h5 class="fw-bold mb-3 border-bottom pb-2">System Activity Overview (Bar Chart)</h5>
    <canvas id="systemActivityChart" width="400" height="200"></canvas>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Jobs by Category Pie Chart
    const ctxPie = document.getElementById('jobsByCategoryChart').getContext('2d');
    const pieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($jobLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($jobCounts); ?>,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#fd7e14'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {display: true, text: 'Jobs by Category'}
            }
        }
    });

    // System Activity Bar Chart
    const ctxBar = document.getElementById('systemActivityChart').getContext('2d');
    const barChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($activityLabels); ?>,
            datasets: [{
                label: 'Count',
                data: <?php echo json_encode($activityData); ?>,
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {beginAtZero: true, precision: 0}
            },
            plugins: {
                title: {display: true, text: 'System Activity Overview'}
            }
        }
    });
});
</script>
            </div>

            <!-- Detailed Activity Log -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Detailed System Activity Audit</h5>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th>Timestamp</th>
                                <th>Event Type</th>
                                <th>Affected Asset / Description</th>
                                <th>Attribute / Status</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($recentActivities)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No activities recorded.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentActivities as $act): ?>
                                    <tr>
                                        <td><?php echo date('Y-m-d H:i:s', strtotime($act->event_time)); ?></td>
                                        <td>
                                            <?php if ($act->event_type === 'user_registered'): ?>
                                                <span class="badge bg-primary-subtle text-primary"><i class="fa-solid fa-user-plus me-1"></i> User Registered</span>
                                            <?php elseif ($act->event_type === 'job_posted'): ?>
                                                <span class="badge bg-success-subtle text-success"><i class="fa-solid fa-briefcase me-1"></i> Job Posted</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning"><i class="fa-solid fa-file-invoice me-1"></i> Application Submitted</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($act->event_type === 'user_registered'): ?>
                                                New account registered: <strong><?php echo htmlspecialchars($act->detail); ?></strong>
                                            <?php elseif ($act->event_type === 'job_posted'): ?>
                                                New job opening listed: <strong><?php echo htmlspecialchars($act->detail); ?></strong>
                                            <?php else: ?>
                                                Application sent for job ID #<?php echo htmlspecialchars($act->detail); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="text-uppercase font-monospace small px-2 py-0.5 border rounded bg-light"><?php echo htmlspecialchars($act->extra); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>
