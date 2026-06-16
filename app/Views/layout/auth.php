<!doctype html>
<html lang="en" data-page-module="login-register">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Register | Notika - Modern Vite Template</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= base_url('assets/style.css'); ?>">
    

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="<?=  base_url('assets/src/css/widgets-consistent.css'); ?>">

    <meta name="description" content="Login and registration - Notika Admin Template">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome and all other CSS imported via Vite -->
</head>

<body>
    <div class="nk-auth-wrapper">
        <!-- Left branding panel -->
        <div class="nk-auth-brand">
            <div class="nk-auth-brand-inner">
                <div class="nk-auth-logo">
                    <img src="<?=  base_url('assets/img/logo/notika-logo-horizontal-white.svg'); ?>" alt="Notika" height="40">
                </div>
                <h1>Welcome to Notika</h1>
                <p>Modern admin dashboard template built with Bootstrap 5 and Vite.</p>
                <div class="nk-auth-features">
                    <div class="nk-auth-feature">
                        <i class="fa-solid fa-shield"></i>
                        <span>Secure Authentication</span>
                    </div>
                    <div class="nk-auth-feature">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Real-time Analytics</span>
                    </div>
                    <div class="nk-auth-feature">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Lightning Fast</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right form panel -->
        <div class="nk-auth-form-panel">
            <?=  $this->renderSection('content') ?>
        </div>
    </div>

    <!-- ONLY VITE ENTRY POINT - Everything else bundled by Vite -->
    <script type="module" src="<?=  base_url('assets/src/js/pages/login-register.js'); ?>"></script>
</body>

</html>
