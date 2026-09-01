<?php require APP_ROOT . '/views/layouts/header.php'; ?>

<div class="container text-center py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="mb-4 text-primary opacity-25">
                <i class="fa-solid fa-circle-question" style="font-size: 8rem;"></i>
            </div>
            <h1 class="display-1 fw-extrabold text-gradient mb-3">404</h1>
            <h3 class="mb-3">Oops! Page Not Found</h3>
            <p class="text-muted mb-5">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
            <a href="<?php echo URL_ROOT; ?>" class="btn btn-primary btn-lg px-4 py-3">
                <i class="fa-solid fa-house me-2"></i>Back to Home
            </a>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/views/layouts/footer.php'; ?>
