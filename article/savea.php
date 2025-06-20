<?php
session_start();
file_put_contents("debug.log", "FORM MASUK\n", FILE_APPEND);
file_put_contents("debug.log", print_r($_POST, true), FILE_APPEND);
file_put_contents("debug.log", print_r($_FILES, true), FILE_APPEND);
include '../db.php';

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = (int) $_POST['category'];
    $author_id = $_SESSION['user_id'];

    // Validasi input dasar
    if (empty($title) || empty($content) || $category_id === 0) {
        die("Judul, isi artikel, dan kategori wajib diisi.");
    }

    // Handle upload gambar jika ada
    $filename = '';
    if (!empty($_FILES['picture']['name'])) {
        if ($_FILES['picture']['error'] !== UPLOAD_ERR_OK) {
            die("Terjadi kesalahan saat upload gambar. Kode error: " . $_FILES['picture']['error']);
        }

        // Pastikan folder ../images/ tersedia
        $uploadDir = '../images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Buat folder jika belum ada
        }

        $filename = uniqid() . '_' . basename($_FILES['picture']['name']);
        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($_FILES['picture']['tmp_name'], $targetPath)) {
            die("Gagal menyimpan gambar ke folder.");
        }
    }

    // Simpan ke tabel article
    $stmt = $conn->prepare("INSERT INTO article (title, content, date, picture) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param("sss", $title, $content, $filename);

    if ($stmt->execute()) {
        $article_id = $stmt->insert_id;

        // Relasi kategori
        $stmt2 = $conn->prepare("INSERT INTO article_category (article_id, category_id) VALUES (?, ?)");
        $stmt2->bind_param("ii", $article_id, $category_id);
        $stmt2->execute();

        // Relasi author
        $stmt3 = $conn->prepare("INSERT INTO article_author (article_id, author_id) VALUES (?, ?)");
        $stmt3->bind_param("ii", $article_id, $author_id);
        $stmt3->execute();

        // Redirect dengan sukses
        header("Location: artikel.php?success=1");
        exit;
    } else {
        die("Gagal menyimpan artikel: " . $stmt->error);
    }
}
?>
