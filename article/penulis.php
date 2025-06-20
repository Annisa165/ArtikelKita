<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

$nickname = $_SESSION['nickname'];

// Tambah penulis
if (isset($_POST['tambah_penulis'])) {
    $nicknameInput = trim($_POST['nickname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($nicknameInput && $email && $password) {
        $stmt = $conn->prepare("INSERT INTO author (nickname, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nicknameInput, $email, $password);
        $stmt->execute();
    }
}

// Hapus penulis
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM author WHERE id = $id");
}

// Sorting
$allowed_columns = ['id', 'nickname', 'email'];
$sort = $_GET['sort'] ?? 'id';
$order = $_GET['order'] ?? 'desc';
if (!in_array($sort, $allowed_columns)) {
    $sort = 'id';
}
$order = ($order === 'asc') ? 'asc' : 'desc';
$toggle_order_id = ($sort === 'id' && $order === 'asc') ? 'desc' : 'asc';
$toggle_order_nickname = ($sort === 'nickname' && $order === 'asc') ? 'desc' : 'asc';
$toggle_order_email = ($sort === 'email' && $order === 'asc') ? 'desc' : 'asc';

// Ambil semua penulis
$query = "SELECT * FROM author ORDER BY $sort $order";
$penulis = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Penulis - ArtikelKita</title>
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

        table th {
            background-color: #e9ecef;
        }

        .sort-icon {
            font-size: 0.9rem;
            margin-left: 4px;
        }

        th a {
            color: #000;
            text-decoration: none;
        }

        th a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center mb-4">Admin Panel</h4>
    <a href="indexu.php"><i class="bi bi-house-door-fill"></i>Dashboard</a>
    <a href="penulis.php" class="active"><i class="bi bi-person-lines-fill"></i>Penulis</a>
    <a href="kategori.php"><i class="bi bi-bookmark-fill"></i>Kategori</a>
    <a href="artikel.php"><i class="bi bi-newspaper"></i>Artikel</a>
    <hr class="text-secondary" />
    <a href="../login/logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i>Logout</a>
</div>

<!-- Content -->
<div class="content">
    <div class="navbar rounded">
        <span class="fs-5">👋 Halo, <strong><?= htmlspecialchars($nickname) ?></strong></span>
    </div>

    <h3 class="mb-4">📋 Daftar Penulis</h3>

    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th style="width: 5%;">
                    <a href="?sort=id&order=<?= $toggle_order_id ?>">
                        ID
                        <?php if ($sort === 'id'): ?>
                            <i class="bi <?= ($order === 'asc') ? 'bi-caret-up-fill' : 'bi-caret-down-fill' ?> sort-icon"></i>
                        <?php endif; ?>
                    </a>
                </th>
                <th>
                    <a href="?sort=nickname&order=<?= $toggle_order_nickname ?>">
                        Nickname
                        <?php if ($sort === 'nickname'): ?>
                            <i class="bi <?= ($order === 'asc') ? 'bi-caret-up-fill' : 'bi-caret-down-fill' ?> sort-icon"></i>
                        <?php endif; ?>
                    </a>
                </th>
                <th>
                    <a href="?sort=email&order=<?= $toggle_order_email ?>">
                        Email
                        <?php if ($sort === 'email'): ?>
                            <i class="bi <?= ($order === 'asc') ? 'bi-caret-up-fill' : 'bi-caret-down-fill' ?> sort-icon"></i>
                        <?php endif; ?>
                    </a>
                </th>
                <th style="width: 20%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $penulis->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nickname']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td>
                        <a href="editp.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus penulis ini?')">
                            <i class="bi bi-trash"></i> Hapus
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="indexu.php" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>

</body>
</html>
