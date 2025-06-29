<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

$nickname = $_SESSION['nickname'];

// Hapus artikel
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM article WHERE id = $id");
}

// Sorting
$allowed_columns = ['id', 'title', 'date'];
$sort = $_GET['sort'] ?? 'id';
$order = $_GET['order'] ?? 'desc';
if (!in_array($sort, $allowed_columns)) {
    $sort = 'id';
}
$order = ($order === 'asc') ? 'asc' : 'desc';
$toggle_order_id = ($sort === 'id' && $order === 'asc') ? 'desc' : 'asc';
$toggle_order_title = ($sort === 'title' && $order === 'asc') ? 'desc' : 'asc';
$toggle_order_date = ($sort === 'date' && $order === 'asc') ? 'desc' : 'asc';

// Pencarian
$search = $_GET['search'] ?? '';
$search_sql = $conn->real_escape_string($search);

// Query artikel
$query = "SELECT * FROM article";
if (!empty($search_sql)) {
    $query .= " WHERE title LIKE '%$search_sql%'";
}
$query .= " ORDER BY $sort $order";
$artikel = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Artikel - ArtikelKita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
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

        .excerpt {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
    <a href="penulis.php"><i class="bi bi-person-lines-fill"></i>Penulis</a>
    <a href="kategori.php"><i class="bi bi-bookmark-fill"></i>Kategori</a>
    <a href="artikel.php" class="active"><i class="bi bi-newspaper"></i>Artikel</a>
    <hr class="text-secondary" />
    <a href="../login/logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i>Logout</a>
</div>

<!-- Konten -->
<div class="content">
    <div class="navbar rounded">
        <span class="fs-5">👋 Halo, <strong><?= htmlspecialchars($nickname) ?></strong></span>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">📰 Daftar Artikel</h3>
        <a href="adda.php" class="btn btn-dark">
            <i class="bi bi-plus-circle"></i> Tambah Artikel
        </a>
    </div>

    <!-- Pencarian -->
    <form method="GET" class="input-group mb-3" style="max-width: 400px;">
        <input type="text" name="search" class="form-control" placeholder="Cari judul artikel..." value="<?= htmlspecialchars($search) ?>">
        <input type="hidden" name="sort" value="<?= $sort ?>">
        <input type="hidden" name="order" value="<?= $order ?>">
        <button class="btn btn-outline-secondary" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </form>

    <p class="text-muted">Menampilkan <strong><?= $artikel->num_rows ?></strong> artikel<?= $search ? ' untuk kata kunci: <em>' . htmlspecialchars($search) . '</em>' : '' ?>.</p>

    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th>
                    <a href="?sort=id&order=<?= $toggle_order_id ?>&search=<?= urlencode($search) ?>">
                        ID
                        <?php if ($sort === 'id'): ?>
                            <i class="bi <?= ($order === 'asc') ? 'bi-caret-up-fill' : 'bi-caret-down-fill' ?> sort-icon"></i>
                        <?php endif; ?>
                    </a>
                </th>
                <th>
                    <a href="?sort=title&order=<?= $toggle_order_title ?>&search=<?= urlencode($search) ?>">
                        Judul
                        <?php if ($sort === 'title'): ?>
                            <i class="bi <?= ($order === 'asc') ? 'bi-caret-up-fill' : 'bi-caret-down-fill' ?> sort-icon"></i>
                        <?php endif; ?>
                    </a>
                </th>
                <th>
                    <a href="?sort=date&order=<?= $toggle_order_date ?>&search=<?= urlencode($search) ?>">
                        Tanggal
                        <?php if ($sort === 'date'): ?>
                            <i class="bi <?= ($order === 'asc') ? 'bi-caret-up-fill' : 'bi-caret-down-fill' ?> sort-icon"></i>
                        <?php endif; ?>
                    </a>
                </th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $artikel->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($row['date'])) ?></td>
                    <td>
                        <a href="edita.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus artikel ini?')">
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
