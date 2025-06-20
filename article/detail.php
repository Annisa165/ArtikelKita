<?php
include '../db.php';

$id = (int) ($_GET['id'] ?? 0);

// Ambil artikel utama
$sql = "SELECT 
            a.id, a.title, a.date, a.content, a.picture,
            GROUP_CONCAT(DISTINCT au.nickname) AS authors,
            GROUP_CONCAT(DISTINCT c.name) AS categories
        FROM article a
        LEFT JOIN article_author aa ON a.id = aa.article_id
        LEFT JOIN author au ON aa.author_id = au.id
        LEFT JOIN article_category ac ON a.id = ac.article_id
        LEFT JOIN category c ON ac.category_id = c.id
        WHERE a.id = ?
        GROUP BY a.id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

// Ambil artikel terkait (1 saja dari kategori yang sama)
$related = [];
if ($article) {
    $sql_related = "
        SELECT a.id, a.title 
        FROM article a
        INNER JOIN article_category ac ON a.id = ac.article_id
        WHERE ac.category_id IN (
            SELECT category_id FROM article_category WHERE article_id = ?
        ) AND a.id != ?
        GROUP BY a.id
        ORDER BY a.date DESC
        LIMIT 3
    ";
    $stmt_related = $conn->prepare($sql_related);
    $stmt_related->bind_param("ii", $id, $id);
    $stmt_related->execute();
    $related_result = $stmt_related->get_result();
    while ($row = $related_result->fetch_assoc()) {
        $related[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $article ? htmlspecialchars($article['title']) : 'Artikel Tidak Ditemukan' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .card-body img {
            max-width: 100%;
            height: auto;
        }
        .sidebar h5 {
            margin-top: 20px;
        }
        .sidebar input[type="search"] {
            margin-bottom: 10px;
        }
        .badge-category {
            background-color: #198754;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold fs-4" href="../index.php">ArtikelKita</a>
      <div class="collapse navbar-collapse justify-content-between" id="navbarContent">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == '../index.php' ? 'active' : '' ?>" href="../index.php">Beranda</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == '../tentang.php' ? 'active' : '' ?>" href="../tentang.php">Tentang</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == '../kontak.php' ? 'active' : '' ?>" href="../kontak.php">Kontak</a>
          </li>
        </ul>
        <a class="btn btn-outline-light" href="../login/login.php">Login</a>
      </div>
    </div>
  </nav>

<div class="container mt-4">
    <div class="row">
        <!-- Konten Artikel -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <?php if ($article): ?>
                        <?php if (!empty($article['picture'])): ?>
                            <img src="../images/<?= htmlspecialchars($article['picture']) ?>" class="mb-3 rounded">
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($article['title']) ?></h3>
                        <p class="text-muted mb-1">🗓️ <?= date('Y-m-d H:i', strtotime($article['date'])) ?> | ✍️ <?= htmlspecialchars($article['authors']) ?></p>
                        <p><span class="badge bg-success"><?= htmlspecialchars($article['categories']) ?></span></p>
                        <div class="mb-3"><?= html_entity_decode($article['content']) ?></div>
                        <a href="../index.php" class="btn btn-secondary">← Kembali ke daftar</a>
                    <?php else: ?>
                        <h4>Artikel tidak ditemukan.</h4>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 sidebar">
            <div class="card p-3 mb-3">
                <h5 class="fw-bold">Cari Artikel</h5>
                <form action="../index.php" method="get">
                    <input type="search" name="search" class="form-control mb-2" placeholder="Masukkan kata kunci...">
                    <button class="btn btn-dark w-100">Cari</button>
                </form>
            </div>

            <div class="card p-3">
                <h5 class="fw-bold">Artikel Terkait</h5>
                <?php if (count($related) > 0): ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($related as $rel): ?>
                            <li><a href="detail.php?id=<?= $rel['id'] ?>"><?= htmlspecialchars($rel['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Tidak ada artikel terkait.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-light text-center py-3 mt-4">
    &copy; 2025 ArtikelKita. Dibuat oleh Siti Annisa Rahmiasari
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
