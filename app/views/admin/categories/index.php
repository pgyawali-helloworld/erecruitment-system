<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <!-- Flash Messages -->
    <?php echo \App\Core\Session::flash('success'); ?>
    <?php echo \App\Core\Session::flash('error'); ?>

    <div class="row g-4">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3">
            <?php 
                $activeTab = 'categories';
                require APP_ROOT . '/views/layouts/admin_sidebar.php'; 
            ?>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="fw-extrabold mb-1">Job Categories</h2>
                    <p class="text-muted small mb-0">Add, edit, or delete categories for job postings</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Add Category Form Card -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h5 class="fw-bold mb-3">Add New Category</h5>
                        
                        <form action="<?php echo URL_ROOT; ?>/admin/categories/add" method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label small fw-bold">Category Name</label>
                                <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" required minlength="3" placeholder="e.g. Data Science" value="<?php echo isset($old['name']) ? htmlspecialchars($old['name']) : ''; ?>">
                                <?php if (isset($errors['name'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                <?php else: ?>
                                    <div class="invalid-feedback">Please enter a category name (min 3 characters).</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="icon" class="form-label small fw-bold">FontAwesome Icon Class</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-icons"></i></span>
                                    <input type="text" class="form-control" id="icon" name="icon" placeholder="e.g. fa-code" value="<?php echo isset($old['icon']) ? htmlspecialchars($old['icon']) : 'fa-briefcase'; ?>">
                                </div>
                                <div class="form-text xsmall">Enter a FontAwesome class (e.g. <code>fa-code</code>, <code>fa-palette</code>). Default is <code>fa-briefcase</code>.</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 rounded-3 py-2">
                                <i class="fa-solid fa-plus me-1"></i> Add Category
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Categories List -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Active Categories</h5>
                        
                        <?php if (empty($categories)): ?>
                            <p class="text-muted text-center py-5">No categories found. Add one to get started.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle table-hover mb-0">
                                    <thead class="table-light text-muted small">
                                        <tr>
                                            <th style="width: 80px;">Icon</th>
                                            <th>Category Name</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        <?php foreach ($categories as $cat): ?>
                                            <tr>
                                                <td>
                                                    <div class="bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fa-solid <?php echo htmlspecialchars($cat->icon ?: 'fa-briefcase'); ?> fs-5"></i>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($cat->name); ?></span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <a href="<?php echo URL_ROOT; ?>/admin/categories/edit/<?php echo $cat->id; ?>" class="btn btn-outline-primary btn-sm rounded-2 px-2.5" title="Edit">
                                                            <i class="fa-solid fa-pen"></i> Edit
                                                        </a>
                                                        <!-- Delete Button with form -->
                                                        <form action="<?php echo URL_ROOT; ?>/admin/categories/delete/<?php echo $cat->id; ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category? This cannot be undone.');">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-2 px-2.5" title="Delete">
                                                                <i class="fa-solid fa-trash"></i> Delete
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
    </div>
</div>

<script>
// Bootstrap 5 client-side validation
(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
})()
</script>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>
