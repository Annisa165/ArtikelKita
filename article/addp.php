<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = trim($_POST['nickname']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // Sebaiknya hash di versi produksi

    if ($nickname && $email && $password) {
        $stmt = $conn->prepare("INSERT INTO author (nickname, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nickname, $email, $password);
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
    <title>Tambah Penulis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h3>Tambah Penulis</h3>
    <form method="POST">
        <div class="mb-3">
            <label>Nickname</label>
            <input type="text" name="nickname" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="penulis.php" class="btn btn-secondary">Kembali</a>
    </form>
</body>
</html>
