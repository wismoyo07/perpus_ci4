<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'Zona Perpustakaan' ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            min-height: 100vh;
        }
        .auth-card {
            border-radius: 15px;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="col-md-5">
        <?= $this->renderSection('content') ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === "password" ? "text" : "password";
    }

    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')

        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()

    function checkStrength(password) {
        let strength = 0
        if (password.length >= 6) strength++
        if (/[A-Z]/.test(password)) strength++
        if (/[0-9]/.test(password)) strength++
        if (/[^A-Za-z0-9]/.test(password)) strength++

        const bar = document.getElementById('strengthBar')
        const text = document.getElementById('strengthText')

        if (!bar) return

        switch (strength) {
            case 0:
            case 1:
                bar.style.width = '25%'
                bar.className = 'progress-bar bg-danger'
                text.innerText = 'Weak'
                break
            case 2:
                bar.style.width = '50%'
                bar.className = 'progress-bar bg-warning'
                text.innerText = 'Medium'
                break
            case 3:
                bar.style.width = '75%'
                bar.className = 'progress-bar bg-info'
                text.innerText = 'Good'
                break
            case 4:
                bar.style.width = '100%'
                bar.className = 'progress-bar bg-success'
                text.innerText = 'Strong'
                break
        }
    }

    function checkMatch() {
        const password = document.getElementById('registerPassword').value;
        const confirm  = document.getElementById('confirmPassword').value;
        const matchText = document.getElementById('matchText');

        if (!confirm) {
            matchText.innerText = '';
            return;
        }

        if (password === confirm) {
            matchText.innerText = 'Password cocok';
            matchText.className = 'text-success fw-semibold';
        } else {
            matchText.innerText = 'Password tidak cocok';
            matchText.className = 'text-danger fw-semibold';
        }
    }
    
</script>

</body>
</html>