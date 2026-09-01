<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <!-- Flash Messages -->
    <?php echo \App\Core\Session::flash('success'); ?>
    <?php echo \App\Core\Session::flash('error'); ?>

    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <?php 
                $activeTab = 'employers';
                require APP_ROOT . '/views/layouts/admin_sidebar.php'; 
            ?>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="fw-extrabold mb-1">Manage Employers</h2>
                    <p class="text-muted small mb-0">Monitor and manage registered company profiles</p>
                </div>
            </div>

            <!-- Employers List Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <?php if (empty($employers)): ?>
                    <p class="text-muted text-center py-5">No registered employers found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th>Company Name</th>
                                    <th>Industry</th>
                                    <th>Owner Email</th>
                                    <th>Status</th>
                                    <th>Joined At</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php foreach ($employers as $emp): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($emp->company_name ?: $emp->name); ?></div>
                                            <span class="text-muted small"><i class="fa-solid fa-location-dot me-1"></i><?php echo htmlspecialchars($emp->address ?: 'N/A'); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary"><?php echo htmlspecialchars($emp->industry ?: 'Unspecified'); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($emp->email); ?></td>
                                        <td>
                                            <span class="badge <?php echo $emp->status === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> px-2.5 py-1.5 rounded-pill">
                                                <i class="fa-solid <?php echo $emp->status === 'active' ? 'fa-circle-check' : 'fa-circle-xmark'; ?> me-1"></i>
                                                <?php echo ucfirst(htmlspecialchars($emp->status)); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('Y-m-d', strtotime($emp->created_at)); ?></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="<?php echo URL_ROOT; ?>/admin/employers/view/<?php echo $emp->id; ?>" class="btn btn-outline-primary btn-sm rounded-2 px-2.5" title="View Details">
                                                    <i class="fa-solid fa-eye"></i> View
                                                </a>
                                                
                                                <a href="<?php echo URL_ROOT; ?>/admin/employers/toggle/<?php echo $emp->id; ?>" class="btn <?php echo $emp->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success'; ?> btn-sm rounded-2 px-2.5" title="<?php echo $emp->status === 'active' ? 'Deactivate' : 'Activate'; ?>">
                                                    <i class="fa-solid <?php echo $emp->status === 'active' ? 'fa-user-slash' : 'fa-user-check'; ?>"></i> 
                                                    <?php echo $emp->status === 'active' ? 'Deactivate' : 'Activate'; ?>
                                                </a>

                                                <form action="<?php echo URL_ROOT; ?>/admin/employers/delete/<?php echo $emp->id; ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employer? Deleting will CASCADE and remove all job postings and candidate applications related to this employer.');">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-2 px-2.5" title="Delete Employer">
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
