<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

$nickname = $_SESSION['nickname'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - ArtikelKita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            background-color: #212529;
            color: #fff;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .sidebar a {
            color: #fff;
            padding: 12px 15px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            border-radius: 5px;
            text-decoration: none;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #343a40;
        }

        .sidebar i {
            margin-right: 10px;
        }

        .content {
            flex-grow: 1;
            padding: 30px;
        }

        .navbar {
            background-color: #fff;
            padding: 15px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .card {
            border: none;
            border-left: 5px solid #343a40;
            background-color: #212529;
            color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-3px);
            background-color: #2a2f35;
        }

        .card-icon {
            font-size: 2rem;
            color: #f8f9fa;
        }

        .card-title, .text-light {
            color: #ffffff !important;
        }

        small.text-muted {
            color: #d3d3d3 !important;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center mb-4">Admin Panel</h4>
    <a href="indexu.php" class="active"><i class="bi bi-house-door-fill"></i>Dashboard</a>
    <a href="penulis.php"><i class="bi bi-person-lines-fill"></i>Penulis</a>
    <a href="kategori.php"><i class="bi bi-bookmark-fill"></i>Kategori</a>
    <a href="artikel.php"><i class="bi bi-newspaper"></i>Artikel</a>
    <hr class="text-secondary" />
    <a href="../login/logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <div class="navbar rounded">
        <span class="fs-5">👋 Halo, <strong><?= htmlspecialchars($nickname) ?></strong></span>
    </div>

    <h3 class="mb-4">Selamat Datang di Admin Dashboard</h3>
    <p class="text-muted mb-4">Kelola konten website Anda melalui panel ini.</p>

    <div class="row g-4">
    <!-- Kartu Penulis -->
    <div class="col-md-4">
        <a href="penulis.php" class="text-decoration-none">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="me-3 card-icon"><i class="bi bi-person-lines-fill"></i></div>
                    <div>
                        <h5 class="card-title mb-0 text-light">Penulis</h5>
                        <small class="text-muted">Kelola data penulis</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Kartu Kategori -->
    <div class="col-md-4">
        <a href="kategori.php" class="text-decoration-none">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="me-3 card-icon"><i class="bi bi-bookmark-fill"></i></div>
                    <div>
                        <h5 class="card-title mb-0 text-light">Kategori</h5>
                        <small class="text-muted">Kelola kategori artikel</small>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Kartu Artikel -->
    <div class="col-md-4">
        <a href="artikel.php" class="text-decoration-none">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="me-3 card-icon"><i class="bi bi-newspaper"></i></div>
                    <div>
                        <h5 class="card-title mb-0 text-light">Artikel</h5>
                        <small class="text-muted">Kelola semua artikel</small>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

</body>
</html>
