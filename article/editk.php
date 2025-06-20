<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$result = $conn->query("SELECT * FROM category WHERE id = $id");
if ($result->num_rows === 0) {
    echo "Kategori tidak ditemukan.";
    exit;
}
$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    if ($name) {
        $stmt = $conn->prepare("UPDATE category SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $id);
        $stmt->execute();
        header("Location: kategori.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar-custom {
            background-color: #1c1c1c;
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: #fff;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .btn-dark {
            background-color: #1c1c1c;
            border: none;
        }

        .btn-dark:hover {
            background-color: #343a40;
        }

        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="#">ArtikelKita</a>
        <div class="ms-auto">
            <a href="kategori.php" class="btn btn-light btn-sm">Kembali</a>
        </div>
    </div>
</nav>

<!-- Form Edit -->
<div class="container my-5">
    <div class="card p-4 mx-auto" style="max-width: 600px;">
        <h4 class="mb-4 text-center">Edit Kategori</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($data['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi Kategori</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Tulis deskripsi kategori"><?= htmlspecialchars($data['description']) ?></textarea>
            </div>
            <div class="d-flex justify-content-between">
                <a href="kategori.php" class="btn btn-secondary">🔙 Kembali</a>
                <button type="submit" class="btn btn-dark">💾 Update</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
