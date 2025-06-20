<?php
session_start();
include '../db.php';

$error = "";
$success = "";

// LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, nickname, password FROM author WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();

            // Tidak pakai hash, langsung bandingkan
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nickname'] = $user['nickname'];
                header("Location: ../article/indexu.php");
                exit;
            } else {
                $error = "Email atau password salah.";
            }
        } else {
            $error = "Email tidak ditemukan.";
        }
    } else {
        $error = "Email dan password wajib diisi.";
    }
}

// SIGNUP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $email = trim($_POST['new_email'] ?? "");
    $password = $_POST['new_password'] ?? "";
    $nickname = trim($_POST['nickname'] ?? "");

    if (!empty($email) && !empty($password) && !empty($nickname)) {
        if (strlen($password) < 6) {
            $error = "Password minimal 6 karakter.";
        } else {
            $check = $conn->prepare("SELECT id FROM author WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows > 0) {
                $error = "Email sudah digunakan.";
            } else {
                $insert = $conn->prepare("INSERT INTO author (email, password, nickname) VALUES (?, ?, ?)");
                $insert->bind_param("sss", $email, $password, $nickname);

                if ($insert->execute()) {
                    $success = "Pendaftaran berhasil. Silakan login.";
                } else {
                    $error = "Pendaftaran gagal. Silakan coba lagi.";
                }
            }
        }
    } else {
        $error = "Semua kolom harus diisi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>Login & Signup - Blog Dinamis</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<style>
/* Tombol hitam */
.btn-primary, .btn-success {
    background-color: #212529;
    border-color: #212529;
}

.btn-primary:hover, .btn-success:hover {
    background-color: #000;
    border-color: #000;
}

/* Tambahan: form dalam card style */
.auth-card {
    background-color: #fff;
    padding: 20px 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Nav tab aktif: biar tegas */
.nav-tabs .nav-link.active {
    background-color: #212529;
    color: #fff;
    border-color: #212529 #212529 #fff;
}

.nav-tabs .nav-link {
    color: #212529;
}
</style>

</head>
<body>
<div class="container mt-5" style="max-width: 500px;">
  <ul class="nav nav-tabs mb-3" id="authTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Login</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="signup-tab" data-bs-toggle="tab" data-bs-target="#signup" type="button" role="tab">Sign Up</button>
    </li>
  </ul>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php elseif ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <div class="tab-content">
    <!-- Login Form -->
    <div class="tab-pane fade show active" id="login" role="tabpanel">
      <div class="auth-card">
        <form method="POST" action="">
          <input type="hidden" name="login" value="1">
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" required class="form-control" />
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" required class="form-control" />
          </div>
          <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
      </div>
    </div>

    <!-- Signup Form -->
    <div class="tab-pane fade" id="signup" role="tabpanel">
      <div class="auth-card">
        <form method="POST" action="">
          <input type="hidden" name="signup" value="1">
          <div class="mb-3">
            <label for="nickname" class="form-label">Nickname</label>
            <input type="text" name="nickname" id="nickname" required class="form-control" />
          </div>
          <div class="mb-3">
            <label for="new_email" class="form-label">Email</label>
            <input type="email" name="new_email" id="new_email" required class="form-control" />
          </div>
          <div class="mb-3">
            <label for="new_password" class="form-label">Password</label>
            <input type="password" name="new_password" id="new_password" required class="form-control" />
          </div>
          <button type="submit" class="btn btn-success w-100">Daftar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include '../footer.php'; ?>
</body>
</html>
