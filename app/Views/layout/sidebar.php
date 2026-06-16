<?php 
$role = session()->get('role');
$uri  = service('uri');
$currentSegment = $uri->getSegment(1);
?>

<nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">

        <ul class="nav flex-column">

            <!-- ================= DASHBOARD ================= -->
            <li class="nav-item">
                <a class="nav-link <?= $currentSegment === 'admin' || $currentSegment === 'staff' || $currentSegment === 'member' ? 'active fw-bold' : '' ?>"
                   href="/<?= $role ?>/dashboard">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>

            <!-- ================= STAFF ================= -->
            <?php if(in_array($role, ['staff'])): ?>

                <!-- Manajemen Buku -->
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center <?= $currentSegment === 'books' ? 'active fw-bold' : '' ?>"
                       data-bs-toggle="collapse"
                       href="#menuBooks"
                       role="button"
                       aria-expanded="<?= $currentSegment === 'books' ? 'true' : 'false' ?>"
                       aria-controls="menuBooks">

                        <span>
                            <i class="bi bi-book"></i>
                            Manajemen Buku
                        </span>

                        <i class="bi bi-chevron-down small"></i>
                    </a>

                    <div class="collapse <?= $currentSegment === 'books' ? 'show' : '' ?>"
                         id="menuBooks">

                        <ul class="nav flex-column ms-3">

                            <li class="nav-item">
                                <a class="nav-link <?= $uri->getSegment(2) === null ? 'active' : '' ?>"
                                   href="/books">
                                    📋 Daftar Buku
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link <?= $uri->getSegment(2) === 'create' ? 'active' : '' ?>"
                                   href="/books/create">
                                    ➕ Tambah Buku
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link"
                                   href="/books?status=archived">
                                    🗄 Buku Archive
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <!-- Transaksi -->
                <li class="nav-item">
                    <a class="nav-link <?= $currentSegment === 'transactions' ? 'active fw-bold' : '' ?>"
                       href="/transactions">
                        <i class="bi bi-arrow-left-right"></i>
                        Transaksi
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= $currentSegment === 'lost-books' ? 'active fw-bold' : '' ?>"
                    href="/lost-books">
                        <i class="bi bi-exclamation-triangle"></i>
                        Buku Hilang
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= $currentSegment === 'users' ? 'active fw-bold' : '' ?>"
                    href="/users">
                        <i class="bi bi-people"></i>
                        Manajemen User
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= $currentSegment === 'reports' ? 'active fw-bold' : '' ?>"
                    href="/reports">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        Laporan Perpustakaan
                    </a>
                </li>

            <?php endif; ?>


            <!-- ================= ADMIN ONLY ================= -->
            
            
            <?php if($role === 'admin'): ?>

                <!-- Manajemen Buku -->
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center <?= $currentSegment === 'books' ? 'active fw-bold' : '' ?>"
                       data-bs-toggle="collapse"
                       href="#menuBooks"
                       role="button"
                       aria-expanded="<?= $currentSegment === 'books' ? 'true' : 'false' ?>"
                       aria-controls="menuBooks">

                        <span>
                            <i class="bi bi-book"></i>
                            Manajemen Buku
                        </span>

                        <i class="bi bi-chevron-down small"></i>
                    </a>

                    <div class="collapse <?= $currentSegment === 'books' ? 'show' : '' ?>"
                         id="menuBooks">

                        <ul class="nav flex-column ms-3">

                            <li class="nav-item">
                                <a class="nav-link <?= $uri->getSegment(2) === null ? 'active' : '' ?>"
                                   href="/books">
                                    📋 Daftar Buku
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link <?= $uri->getSegment(2) === 'create' ? 'active' : '' ?>"
                                   href="/books/create">
                                    ➕ Tambah Buku
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link"
                                   href="/books?status=archived">
                                    🗄 Buku Archive
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= $currentSegment === 'transactions' ? 'active fw-bold' : '' ?>"
                       href="/admin/transactions">
                        <i class="bi bi-arrow-left-right"></i>
                        Transaksi
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= $currentSegment === 'lost-books' ? 'active fw-bold' : '' ?>"
                    href="/lost-books">
                        <i class="bi bi-exclamation-triangle"></i>
                        Buku Hilang
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= $currentSegment === 'users' ? 'active fw-bold' : '' ?>"
                       href="/users">
                        <i class="bi bi-people"></i>
                        Manajemen User
                    </a>
                </li>

                <hr>
                <li class="nav-item">
                    <small class="text-muted px-3">Monitoring</small>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= $currentSegment === 'activity-logs' ? 'active fw-bold' : '' ?>"
                    href="/activity-logs">
                        <i class="bi bi-clipboard-data"></i>
                        Activity Log
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= $currentSegment === 'reports' ? 'active fw-bold' : '' ?>"
                    href="/reports">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        Laporan Perpustakaan
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= $currentSegment === 'settings' ? 'active fw-bold' : '' ?>"
                    href="/admin/settings">
                        <i class="bi bi-gear"></i>
                        Pengaturan Sekolah
                    </a>
                </li>

            <?php endif; ?>


            <!-- ================= MEMBER ================= -->
            <?php if($role === 'member'): ?>

                <li class="nav-item">
                    <a class="nav-link <?= $currentSegment === 'books' ? 'active fw-bold' : '' ?>"
                       href="/member/books">
                        <i class="bi bi-book"></i>
                        Daftar Buku
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= $currentSegment === 'my-transactions' ? 'active fw-bold' : '' ?>"
                       href="/member/transactions">
                        <i class="bi bi-clock-history"></i>
                        Buku Saya
                    </a>
                </li>

            <?php endif; ?>

        </ul>

    </div>
</nav>