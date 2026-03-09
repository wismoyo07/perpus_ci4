<nav class="navbar navbar-dark bg-dark sticky-top shadow">
    <div class="container-fluid">
        <span class="navbar-brand">Perpustakaan System</span>

        
        <div class="text-white">
            <?= session()->get('name') ?> |
            <a href="/logout" class="text-white text-decoration-none">Logout</a>
        </div>
    </div>
</nav>