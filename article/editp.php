<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$result = $conn->query("SELECT * FROM author WHERE id = $id");
if ($result->num_rows === 0) {
    echo "Data penulis tidak ditemukan.";
    exit;
}
$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = trim($_POST['nickname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($nickname && $email) {
        if (!empty($password)) {
            $stmt = $conn->prepare("UPDATE author SET nickname = ?, email = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nickname, $email, $password, $id);
        } else {
            $stmt = $conn->prepare("UPDATE author SET nickname = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nickname, $email, $id);
        }
        $stmt->execute();
        header("Location: penulis.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Penulis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-custom {
            background-color: #1a1a1a;
        }
        .footer-custom {
            background-color: #1a1a1a;
            color: white;
        }
        .btn-dark-custom {
            background-color: #1a1a1a;
            color: white;
        }
        .btn-dark-custom:hover {
            background-color: #333333;
            color: white;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom px-4">
    <a class="navbar-brand text-white fw-bold" href="../index.php">ArtikelKita</a>
    <div class="ms-auto">
        <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>

<!-- Form Edit Penulis -->
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4 fw-semibold">Edit Data Penulis</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nickname</label>
                    <input type="text" name="nickname" class="form-control" value="<?= htmlspecialchars($data['nickname']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                    <input type="password" name="password" class="form-control">
                </div>
                <button type="submit" class="btn btn-dark-custom">💾 Update</button>
                <a href="penulis.php" class="btn btn-secondary">🔙 Kembali</a>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
