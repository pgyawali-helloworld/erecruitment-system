<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <!-- Flash Messages -->
    <?php echo \App\Core\Session::flash('success'); ?>
    <?php echo \App\Core\Session::flash('error'); ?>

    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <?php 
                $activeTab = 'candidates';
                require APP_ROOT . '/views/layouts/admin_sidebar.php'; 
            ?>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="fw-extrabold mb-1">Manage Job Seekers</h2>
                    <p class="text-muted small mb-0">Monitor and manage registered candidate profiles</p>
                </div>
            </div>

            <!-- Candidates List Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <?php if (empty($candidates)): ?>
                    <p class="text-muted text-center py-5">No registered job seekers found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Key Skills</th>
                                    <th>Status</th>
                                    <th>Joined At</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php foreach ($candidates as $cand): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($cand->name); ?></div>
                                            <span class="text-muted small"><?php echo htmlspecialchars($cand->email); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($cand->phone ?: 'N/A'); ?></td>
                                        <td>
                                            <?php 
                                            $skills = explode(',', $cand->skills);
                                            $shownSkills = array_slice($skills, 0, 3);
                                            foreach ($shownSkills as $skill) {
                                                if (trim($skill)) {
                                                    echo '<span class="badge bg-primary-subtle text-primary me-1">' . htmlspecialchars(trim($skill)) . '</span>';
                                                }
                                            }
                                            if (count($skills) > 3) {
                                                echo '<span class="badge bg-secondary-subtle text-secondary">+ ' . (count($skills) - 3) . ' more</span>';
                                            }
                                            if (empty(trim($cand->skills))) {
                                                echo '<span class="text-muted small">None listed</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $cand->status === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> px-2.5 py-1.5 rounded-pill">
                                                <i class="fa-solid <?php echo $cand->status === 'active' ? 'fa-circle-check' : 'fa-circle-xmark'; ?> me-1"></i>
                                                <?php echo ucfirst(htmlspecialchars($cand->status)); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('Y-m-d', strtotime($cand->created_at)); ?></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="<?php echo URL_ROOT; ?>/admin/candidates/view/<?php echo $cand->id; ?>" class="btn btn-outline-primary btn-sm rounded-2 px-2.5" title="View Details">
                                                    <i class="fa-solid fa-eye"></i> View
                                                </a>
                                                
                                                <a href="<?php echo URL_ROOT; ?>/admin/candidates/toggle/<?php echo $cand->id; ?>" class="btn <?php echo $cand->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success'; ?> btn-sm rounded-2 px-2.5" title="<?php echo $cand->status === 'active' ? 'Deactivate' : 'Activate'; ?>">
                                                    <i class="fa-solid <?php echo $cand->status === 'active' ? 'fa-user-slash' : 'fa-user-check'; ?>"></i>
                                                    <?php echo $cand->status === 'active' ? 'Deactivate' : 'Activate'; ?>
                                                </a>

                                                <form action="<?php echo URL_ROOT; ?>/admin/candidates/delete/<?php echo $cand->id; ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this job seeker? This will permanently delete their profile and all submitted job applications.');">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-2 px-2.5" title="Delete Candidate">
                                                        <i class="fa-solid fa-trash-can"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>
