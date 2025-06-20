<?php
include 'db.php';

// Tangkap input pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category_id = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;

// Ambil semua kategori (untuk sidebar)
$categoryQuery = $conn->query("SELECT id, name FROM category ORDER BY name ASC");

// Query artikel
$sql = "SELECT 
            article.id, 
            article.title, 
            article.date, 
            article.picture, 
            article.content,
            GROUP_CONCAT(DISTINCT author.nickname SEPARATOR ', ') AS authors,
            GROUP_CONCAT(DISTINCT category.name SEPARATOR ', ') AS categories
        FROM article
        LEFT JOIN article_author ON article.id = article_author.article_id
        LEFT JOIN author ON article_author.author_id = author.id
        LEFT JOIN article_category ON article.id = article_category.article_id
        LEFT JOIN category ON article_category.category_id = category.id
        WHERE 1 ";

$params = [];
$types = "";

// Tambahkan kondisi pencarian jika ada
if (!empty($search)) {
    $sql .= " AND (article.title LIKE ? OR article.content LIKE ?) ";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

// Tambahkan filter kategori jika ada
if ($category_id > 0) {
    $sql .= " AND category.id = ? ";
    $params[] = $category_id;
    $types .= "i";
}

$sql .= " GROUP BY article.id ORDER BY article.date DESC";

$stmt = $conn->prepare($sql);

// Binding parameter dinamis
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>ArtikelKita</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
  .sidebar .list-group-item a {
    text-decoration: none;
    color: #212529; /* hitam elegan */
    display: block;
    padding: 5px;
    font-weight: normal;
  }

  .sidebar .list-group-item a:hover {
    background-color: #f8f9fa; /* abu muda */
    color: #000;
  }

  .sidebar .list-group-item.active a {
    background-color: #212529; /* hitam */
    color: #fff;
    font-weight: bold;
  }

  .sidebar .list-group-item.active {
    background-color: #212529; /* supaya border dan background sama */
    border-color: #212529;
  }

  /* Tombol Cari jadi hitam */
  .sidebar button.btn-primary {
    background-color: #212529;
    border-color: #212529;
  }

  .sidebar button.btn-primary:hover {
    background-color: #000;
    border-color: #000;
  }

  .card .btn-primary {
    background-color: #212529;
    border-color: #212529;
}

.card .btn-primary:hover {
    background-color: #000;
    border-color: #000;
}

</style>

</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold fs-4" href="index.php">ArtikelKita</a>
      <div class="collapse navbar-collapse justify-content-between" id="navbarContent">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">Beranda</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'tentang.php' ? 'active' : '' ?>" href="tentang.php">Tentang</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'kontak.php' ? 'active' : '' ?>" href="kontak.php">Kontak</a>
          </li>
        </ul>
        <a class="btn btn-outline-light" href="login/login.php">Login</a>
      </div>
    </div>
  </nav>

  <div class="container my-4">
    <div class="row">
      <!-- Artikel -->
      <div class="col-md-8">
        <h3 class="mb-4">
          <?php 
          if ($search) {
              echo "Hasil pencarian untuk: <em>" . htmlspecialchars($search) . "</em>";
          } elseif ($category_id > 0) {
              // Ambil nama kategori
              $catRes = $conn->query("SELECT name FROM category WHERE id = $category_id");
              $catRow = $catRes->fetch_assoc();
              echo "Artikel dalam kategori: <em>" . htmlspecialchars($catRow['name']) . "</em>";
          } else {
              echo "Daftar Artikel Terbaru";
          }
          ?>
        </h3>
        <div class="row">
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                  <a href="article/detail.php?id=<?= $row['id'] ?>">
                    <?php if (!empty($row['picture'])): ?>
                      <img src="images/<?= htmlspecialchars($row['picture']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['title']) ?>" />
                    <?php else: ?>
                      <img src="https://via.placeholder.com/400x200?text=No+Image" class="card-img-top" alt="No Image" />
                    <?php endif; ?>
                  </a>
                  <div class="card-body d-flex flex-column">
                    <h5 class="card-title">
                      <a href="article/detail.php?id=<?= $row['id'] ?>" class="text-dark text-decoration-none">
                        <?= htmlspecialchars($row['title']) ?>
                      </a>
                    </h5>
                    <small class="text-muted mb-1">
                      📅 <?= date('d M Y', strtotime($row['date'])) ?> | ✍️ <?= htmlspecialchars($row['authors']) ?>
                    </small><br />
                    <small class="text-muted mb-2">📚 Kategori: <?= htmlspecialchars($row['categories']) ?></small>
                    <p class="card-text flex-grow-1"><?= htmlspecialchars(substr(strip_tags($row['content']), 0, 100)) ?>...</p>
                    <a href="article/detail.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm mt-auto align-self-start">Baca Selengkapnya</a>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p class="text-center">Artikel tidak ditemukan.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-md-4">
        <div class="sidebar mb-4">
          <h5>Cari Artikel</h5>
          <form method="GET" action="index.php" class="mb-3">
            <div class="input-group">
              <input type="search" class="form-control" name="search" placeholder="Cari artikel..." value="<?= htmlspecialchars($search) ?>" />
              <button class="btn btn-primary" type="submit">Cari</button>
            </div>
          </form>

          <h5>Kategori</h5>
          <ul class="list-group">
            <li class="list-group-item <?= ($category_id == 0 ? 'active' : '') ?>">
              <a href="index.php" class="<?= ($category_id == 0 ? 'text-white' : '') ?>">Semua Kategori</a>
            </li>
            <?php while ($cat = $categoryQuery->fetch_assoc()): ?>
              <li class="list-group-item <?= ($category_id == $cat['id'] ? 'active' : '') ?>">
                <a href="index.php?category_id=<?= $cat['id'] ?>" class="<?= ($category_id == $cat['id'] ? 'text-white' : '') ?>">
                  <?= htmlspecialchars($cat['name']) ?>
                </a>
              </li>
            <?php endwhile; ?>
          </ul>
        </div>
       <div class="card">
        <div class="card-body">
          <h5 class="card-title">Tentang</h5>
          <p>
            <strong>ArtikelKita</strong> adalah blog sederhana yang kami buat untuk membagikan cerita, informasi, dan pengalaman seputar tempat-tempat menarik, edukasi, budaya, serta inspirasi lainnya.
          </p>
          <p>
            Kami percaya bahwa menulis adalah cara terbaik untuk berbagi pengetahuan dan memperluas wawasan. Blog ini dikelola oleh beberapa penulis yang memiliki minat dalam dunia wisata, edukasi, dan gaya hidup.
          </p>
          <p>
            Semoga artikel yang kami sajikan dapat bermanfaat dan menginspirasi pembaca. Terima kasih telah berkunjung! 😊
          </p>
        </div>
      </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>
