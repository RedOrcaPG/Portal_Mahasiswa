<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/db.php';

if (is_logged_in()) {
    header('Location: ' . (current_role() === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['username']     = $user['username'];
            $_SESSION['role']         = $user['role'];
            $_SESSION['mahasiswa_id'] = $user['mahasiswa_id'];

            header('Location: ' . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php'));
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — Student Portal</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-shell">
  <div class="login-side">
    <div class="mark">Student Portal</div>
    <p>Satu tempat untuk memantau progres akademik: mata kuliah yang telah diselesaikan, nilai, dan riwayat studi.</p>
  </div>
  <div class="login-form-wrap">
    <div class="login-card">
      <span class="eyebrow">Masuk</span>
      <h1>Selamat datang kembali</h1>
      <div class="sub">Masukkan kredensial Anda untuk melanjutkan.</div>

      <?php if ($error): ?>
        <div class="alert alert-err"><?= e($error) ?></div>
      <?php endif; ?>

      <form method="post" action="login.php">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= e($_POST['username'] ?? '') ?>" autofocus required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Masuk</button>
      </form>

      <div class="hint">
        Akun admin bawaan: <strong class="mono">admin</strong> / <strong class="mono">admin123</strong> — segera ganti setelah login pertama.
      </div>
    </div>
  </div>
</div>
</body>
</html>
