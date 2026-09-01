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
                    <h2 class="fw-extrabold mb-1">Edit Category</h2>
                    <p class="text-muted small mb-0">Modify the category name and icon representation</p>
                </div>
                <a href="<?php echo URL_ROOT; ?>/admin/categories" class="btn btn-outline-secondary btn-sm rounded-2 px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Categories
                </a>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Category Details</h5>
                        
                        <form action="<?php echo URL_ROOT; ?>/admin/categories/edit/<?php echo $category->id; ?>" method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label small fw-bold">Category Name</label>
                                <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" required minlength="3" placeholder="Category Name" value="<?php echo htmlspecialchars($category->name); ?>">
                                <?php if (isset($errors['name'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
                                <?php else: ?>
                                    <div class="invalid-feedback">Please enter a category name (min 3 characters).</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="icon" class="form-label small fw-bold">FontAwesome Icon Class</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid <?php echo htmlspecialchars($category->icon ?: 'fa-briefcase'); ?>"></i></span>
                                    <input type="text" class="form-control" id="icon" name="icon" placeholder="e.g. fa-code" value="<?php echo htmlspecialchars($category->icon ?: 'fa-briefcase'); ?>">
                                </div>
                                <div class="form-text xsmall">Enter a FontAwesome class (e.g. <code>fa-code</code>, <code>fa-palette</code>). Default is <code>fa-briefcase</code>.</div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1 rounded-3 py-2">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                                </button>
                                <a href="<?php echo URL_ROOT; ?>/admin/categories" class="btn btn-outline-secondary rounded-3 py-2 px-4">
                                    Cancel
                                </a>
                            </div>
                        </form>
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
