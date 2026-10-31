<?php 
$role = session()->get('role');
$uri  = service('uri');
$currentSegment = $uri->getSegment(1);
$secondSegment = $uri->getTotalSegments() >= 2 ? $uri->getSegment(2) : null;
?>
<nav class="navbar navbar-expand-lg notika-navbar">
    <div class="container">
        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#notikaNavbarCollapse" aria-controls="notikaNavbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa-solid fa-bars text-success"></i>
        </button>
        <div class="collapse navbar-collapse" id="notikaNavbarCollapse">
            <ul class="navbar-nav w-100 justify-content-start">
                
                <!-- ================= DASHBOARD ================= -->
                <li class="nav-item">
                    <a class="nav-link <?= ($currentSegment === 'admin' || $currentSegment === 'staff' || $currentSegment === 'member') && $secondSegment === 'dashboard' ? 'active' : '' ?>" 
                       href="/<?= $role ?>/dashboard">
                        <i class="fa-solid fa-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- ================= ADMIN / STAFF MENU ================= -->
                <?php if (in_array($role, ['admin', 'staff'])): ?>
                    
                    <!-- Manajemen Buku (Dropdown) -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= in_array($currentSegment, ['books', 'transactions', 'lost-books']) || ($currentSegment === 'admin' && $secondSegment === 'transactions') ? 'active' : '' ?>" 
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-book"></i>
                            <span>Manajemen Buku</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/books">Daftar Buku</a></li>
                            <li>
                                <?php 
                                $transUrl = ($role === 'admin') ? '/admin/transactions' : '/transactions';
                                ?>
                                <a class="dropdown-item" href="<?= $transUrl ?>">Pinjam Kembali</a>
                            </li>
                            <li><a class="dropdown-item" href="/lost-books">Buku Hilang</a></li>
                        </ul>
                    </li>

                    <!-- Manajemen User -->
                    <li class="nav-item">
                        <a class="nav-link <?= $currentSegment === 'users' ? 'active' : '' ?>" 
                           href="/users">
                            <i class="fa-solid fa-users"></i>
                            <span>Manajemen User</span>
                        </a>
                    </li>

                    <!-- Laporan Perpustakaan -->
                    <li class="nav-item">
                        <a class="nav-link <?= $currentSegment === 'reports' ? 'active' : '' ?>" 
                           href="/reports">
                            <i class="fa-solid fa-chart-column"></i>
                            <span>Laporan</span>
                        </a>
                    </li>

                    <!-- Monitoring (Admin Only) -->
                    <?php if ($role === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentSegment === 'activity-logs' ? 'active' : '' ?>" 
                               href="/activity-logs">
                                <i class="fa-solid fa-list-check"></i>
                                <span>Activity Log</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentSegment === 'admin' && $secondSegment === 'master' ? 'active' : '' ?>" 
                               href="/admin/master/ddc">
                                <i class="fa-solid fa-database"></i>
                                <span>Data Master</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $currentSegment === 'admin' && $secondSegment === 'settings' ? 'active' : '' ?>" 
                               href="/admin/settings">
                                <i class="fa-solid fa-gear"></i>
                                <span>Pengaturan</span>
                            </a>
                        </li>
                    <?php endif; ?>

                <!-- ================= MEMBER MENU ================= -->
                <?php elseif ($role === 'member'): ?>
                    
                    <!-- Katalog Buku -->
                    <li class="nav-item">
                        <a class="nav-link <?= $currentSegment === 'member' && $secondSegment === 'books' ? 'active' : '' ?>" 
                           href="/member/books">
                            <i class="fa-solid fa-book-open"></i>
                            <span>Katalog Buku</span>
                        </a>
                    </li>

                    <!-- Buku Saya -->
                    <li class="nav-item">
                        <a class="nav-link <?= $currentSegment === 'member' && $secondSegment === 'transactions' ? 'active' : '' ?>" 
                           href="/member/transactions">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span>Buku Saya</span>
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>