<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

// Ambil kategori
$kategori = $conn->query("SELECT * FROM category ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Artikel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    header {
      background-color: #212529;
      color: white;
      padding: 1rem 0;
    }
    .form-container {
      background: white;
      padding: 2rem;
      border-radius: 0.75rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      margin-top: 30px;
    }
    .form-label {
      font-weight: 500;
    }
    .btn-dark {
      background-color: #212529;
      border: none;
    }
    .btn-dark:hover {
      background-color: #343a40;
    }
    .btn-secondary {
      background-color: #6c757d;
      border: none;
    }
    .btn-secondary:hover {
      background-color: #5a6268;
    }
    footer {
      background-color: #212529;
      color: white;
      text-align: center;
      padding: 1rem 0;
      margin-top: 40px;
    }
  </style>
</head>
<body>

<header>
  <div class="container">
    <h2 class="text-center">📝 Tambah Artikel</h2>
  </div>
</header>

<div class="container">
  <div class="form-container">
    <form action="savea.php" method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="title" class="form-label">🖊️ Judul Artikel</label>
        <input type="text" name="title" id="title" class="form-control" placeholder="Masukkan judul artikel" required>
      </div>

      <div class="mb-3">
        <label for="content" class="form-label">📄 Isi Artikel</label>
        <textarea name="content" id="editor" rows="10" class="form-control" required></textarea>
      </div>

      <div class="mb-3">
        <label for="category" class="form-label">📚 Kategori</label>
        <select name="category" id="category" class="form-select" required>
          <option value="">-- Pilih Kategori --</option>
          <?php while($row = $kategori->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="picture" class="form-label">🖼️ Gambar (opsional)</label>
        <input type="file" name="picture" id="picture" accept="image/*" class="form-control">
      </div>

      <div class="d-flex justify-content-between">
        <a href="artikel.php" class="btn btn-secondary">🔙 Kembali</a>
        <button type="submit" class="btn btn-dark">💾 Simpan Artikel</button>
      </div>
    </form>
  </div>
</div>

<footer>
  &copy; <?= date('Y') ?> Sistem Artikel. Semua Hak Dilindungi.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.ckeditor.com/4.22.1/full-all/ckeditor.js"></script>
<script>
  CKEDITOR.replace('editor', {
    height: 300,
    extraPlugins: 'justify,colorbutton,sourcearea'
  });
</script>

</body>
</html>
