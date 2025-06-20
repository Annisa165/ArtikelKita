<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data artikel
$artikel = $conn->query("SELECT * FROM article WHERE id = $id");
if ($artikel->num_rows == 0) {
    echo "Artikel tidak ditemukan.";
    exit;
}
$data = $artikel->fetch_assoc();

// Ambil semua kategori
$kategori_all = $conn->query("SELECT * FROM category ORDER BY name ASC");

// Ambil kategori artikel saat ini
$kategori_terpilih = 0;
$kategori_result = $conn->query("SELECT category_id FROM article_category WHERE article_id = $id LIMIT 1");
if ($kategori_result->num_rows > 0) {
    $kategori_terpilih = $kategori_result->fetch_assoc()['category_id'];
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $conn->real_escape_string($_POST['title']);
    $isi = $conn->real_escape_string($_POST['content']);
    $kategori_baru = (int)$_POST['category'];
    $tanggal = $_POST['tanggal'];
    $gambarBaru = $data['picture'];

    if (!empty($_FILES['picture']['name'])) {
        $gambarBaru = 'gambar_' . time() . '_' . basename($_FILES['picture']['name']);
        $targetPath = '../images/' . $gambarBaru;

        if (move_uploaded_file($_FILES['picture']['tmp_name'], $targetPath)) {
            if (!empty($data['picture']) && file_exists('../images/' . $data['picture'])) {
                unlink('../images/' . $data['picture']);
            }
        } else {
            echo "Upload gambar gagal.";
            exit;
        }
    }

    $conn->query("UPDATE article SET title = '$judul', content = '$isi', picture = '$gambarBaru', date = '$tanggal' WHERE id = $id");

    $conn->query("DELETE FROM article_category WHERE article_id = $id");
    $conn->query("INSERT INTO article_category (article_id, category_id) VALUES ($id, $kategori_baru)");

    header("Location: artikel.php");
    exit;
}

// Menyiapkan nilai tanggal
$tanggal_value = !empty($data['date']) && strtotime($data['date']) !== false
    ? date('Y-m-d', strtotime($data['date']))
    : date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Edit Artikel - ArtikelKita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        header { background-color: #212529; color: white; padding: 1rem; }
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }
        .btn-dark { background-color: #212529; border: none; }
        .btn-dark:hover { background-color: #343a40; }
        .btn-secondary { background-color: #6c757d; border: none; }
        .btn-secondary:hover { background-color: #5a6268; }
        img.preview {
            max-width: 200px;
            margin-bottom: 1rem;
            display: block;
        }
        footer {
            background-color: #212529;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: 40px;
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h2 class="text-center">✏️ Edit Artikel</h2>
    </div>
</header>

<div class="container">
    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">🖊️ Judul Artikel</label>
                <input type="text" name="title" id="title" class="form-control" required value="<?= htmlspecialchars($data['title']) ?>" />
            </div>

            <div class="mb-3">
                <label for="tanggal" class="form-label">📅 Tanggal Artikel</label>
                <input type="datetime-local" name="tanggal" id="tanggal" class="form-control" required value="<?= date('Y-m-d\TH:i', strtotime($data['date'])) ?>" />
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">📄 Isi Artikel</label>
                <textarea name="content" id="content" rows="10" class="form-control" required><?= htmlspecialchars($data['content']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">📚 Kategori</label>
                <select name="category" id="category" class="form-select" required>
                    <option value="" disabled <?= $kategori_terpilih == 0 ? 'selected' : '' ?>>-- Pilih Kategori --</option>
                    <?php while($row = $kategori_all->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>" <?= ($row['id'] == $kategori_terpilih) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="picture" class="form-label">🖼️ Gambar Saat Ini</label><br>
                <?php if (!empty($data['picture']) && file_exists('../images/' . $data['picture'])): ?>
                    <img src="../images/<?= $data['picture'] ?>" alt="Gambar Artikel" class="preview">
                <?php else: ?>
                    <p><em>Tidak ada gambar.</em></p>
                <?php endif; ?>
                <input type="file" name="picture" id="picture" accept="image/*" class="form-control mt-2" />
                <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
            </div>

            <div class="d-flex justify-content-between">
                <a href="artikel.php" class="btn btn-secondary">🔙 Batal</a>
                <button type="submit" class="btn btn-dark">💾 Update</button>
            </div>
        </form>
    </div>
</div>

<footer>
    <div class="container">
        <small>&copy; <?= date('Y') ?> ArtikelKita | Admin Panel</small>
    </div>
</footer>

<script>
ClassicEditor
    .create(document.querySelector('#content'))
    .catch(error => {
        console.error(error);
    });
</script>

</body>
</html>
