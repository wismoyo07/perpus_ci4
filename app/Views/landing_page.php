<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($setting['nama_lembaga']) ?> - Perpustakaan Digital</title>
    
    <!-- Google Fonts & Bootstrap 5 -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f6f8fb;
            color: #2b3a4a;
        }
        .navbar-custom {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(225, 230, 239, 0.8);
        }
        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #fff;
            padding: 80px 0;
            border-radius: 0 0 40px 40px;
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.15);
        }
        .search-card {
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            border: 0;
            margin-top: -50px;
        }
        .search-input {
            border: 1px solid #e1e6ef;
            padding: 15px 25px;
            border-radius: 30px 0 0 30px;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        .search-input:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }
        .search-btn {
            border-radius: 0 30px 30px 0;
            padding: 0 30px;
            font-weight: 500;
        }
        .book-card {
            border: 0;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
            transition: all 0.3s;
            overflow: hidden;
            background-color: #fff;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        }
        .badge-available {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .badge-empty {
            background-color: #f8d7da;
            color: #842029;
        }
        .school-logo-img {
            max-height: 40px;
            width: auto;
            object-fit: contain;
        }
        footer {
            background-color: #fff;
            border-top: 1px solid #e1e6ef;
            color: #6c757d;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top navbar-custom py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold text-primary" href="/">
            <?php if ($setting['logo']): ?>
                <img src="<?= base_url('uploads/settings/' . $setting['logo']) ?>" alt="Logo" class="school-logo-img me-2">
            <?php else: ?>
                <i class="bi bi-book-half me-2"></i>
            <?php endif; ?>
            <?= esc($setting['nama_lembaga']) ?>
        </a>
        <div class="ms-auto">
            <a href="/auth/login" class="btn btn-outline-primary rounded-pill px-4 fw-semibold shadow-sm">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Perpustakaan
            </a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-5 fw-bold mb-3">Pustaka Pintar Mandiri</h1>
        <p class="lead mb-0 text-white-50">Temukan ribuan referensi buku sekolah, modul sains, dan karya literatur digital secara instan.</p>
    </div>
</section>

<!-- Search Bar Area -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card search-card p-4">
                <form action="/" method="GET">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control search-input" placeholder="Cari judul buku, pengarang, atau penerbit..." value="<?= esc($search ?? '') ?>" aria-label="Search">
                        <button class="btn btn-primary search-btn" type="submit"><i class="bi bi-search me-1"></i> Cari</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Main Catalog Area -->
<section class="container my-5 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <?= $search ? 'Hasil Pencarian: "' . esc($search) . '"' : 'Koleksi Buku Terbaru' ?>
            </h4>
            <p class="text-muted mb-0 small"><?= count($books) ?> buku ditampilkan</p>
        </div>
        <?php if ($search): ?>
            <a href="/" class="btn btn-link text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali ke Katalog Utama</a>
        <?php endif; ?>
    </div>

    <!-- Book Grid -->
    <div class="row g-4">
        <?php if (empty($books)): ?>
            <div class="col-12 text-center py-5">
                <div class="fs-1 text-muted mb-3"><i class="bi bi-emoji-frown"></i></div>
                <h5 class="text-secondary fw-semibold">Buku tidak ditemukan</h5>
                <p class="text-muted">Gunakan kata kunci pencarian lain.</p>
            </div>
        <?php else: ?>
            <?php foreach ($books as $b): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card book-card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge rounded-pill <?= $b['stok_tersedia'] > 0 ? 'badge-available' : 'badge-empty' ?>">
                                    <?= $b['stok_tersedia'] > 0 ? 'Tersedia' : 'Kosong' ?>
                                </span>
                                <small class="text-muted fw-bold"><?= esc($b['kode_buku']) ?></small>
                            </div>
                            <h5 class="card-title fw-bold text-dark mb-2 line-clamp"><?= esc($b['judul']) ?></h5>
                            <p class="text-muted small mb-1">Pengarang: <strong><?= esc($b['pengarang'] ?: '-') ?></strong></p>
                            <p class="text-muted small mb-1">Penerbit: <strong><?= esc($b['penerbit'] ?: '-') ?></strong></p>
                            <?php $panggil = \App\Services\BookService::nomorPanggil($b); ?>
                            <p class="text-muted small mb-3">No. Panggil: <strong><?= $panggil ? esc($panggil) : '-' ?></strong></p>
                            
                            <div class="mt-auto border-top pt-3 text-center">
                                <div class="row text-center w-100 g-0">
                                    <div class="col-6 border-end">
                                        <small class="text-muted d-block small">Stok Total</small>
                                        <strong class="text-dark"><?= $b['stok_total'] ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block small">Tersedia</small>
                                        <strong class="text-primary"><?= $b['stok_tersedia'] ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Footer -->
<footer class="py-4 text-center mt-5">
    <div class="container">
        <p class="mb-1 fw-bold text-dark"><?= esc($setting['nama_lembaga']) ?></p>
        <p class="mb-0 small"><?= esc($setting['alamat']) ?></p>
        <p class="mb-0 mt-2 text-muted-50 small">&copy; <?= date('Y') ?> Sistem Informasi Perpustakaan. Hak Cipta Dilindungi.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
