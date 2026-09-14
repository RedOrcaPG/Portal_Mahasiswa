<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';
require_admin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim      = trim($_POST['nim'] ?? '');
    $nama     = trim($_POST['nama'] ?? '');
    $prodi    = trim($_POST['program_studi'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nim === '' || $nama === '' || $username === '' || $password === '') {
        $error = 'NIM, nama, username, dan password wajib diisi.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('INSERT INTO mahasiswa (nim, nama, program_studi) VALUES (?, ?, ?)');
            $stmt->execute([$nim, $nama, $prodi ?: null]);
            $mahasiswaId = $pdo->lastInsertId();

            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password, role, mahasiswa_id) VALUES (?, ?, "mahasiswa", ?)');
            $stmt->execute([$username, $hash, $mahasiswaId]);

            $pdo->commit();
            $success = "Mahasiswa \"$nama\" berhasil ditambahkan beserta akun login.";
        } catch (PDOException $ex) {
            $pdo->rollBack();
            if ($ex->getCode() == 23000) {
                $error = 'NIM atau username sudah digunakan. Gunakan nilai lain.';
            } else {
                $error = 'Gagal menyimpan data: ' . $ex->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Mahasiswa</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <div class="brand">Student Portal</div>
    <div class="brand-sub">Admin</div>
    <nav>
      <a href="dashboard.php">Daftar Mahasiswa</a>
      <a href="add_student.php" class="active">Tambah Mahasiswa</a>
      <a href="add_course.php">Tambah Mata Kuliah</a>
    </nav>
    <div class="spacer"></div>
    <a class="logout-link" href="../logout.php">Keluar &rarr;</a>
  </aside>

  <main class="main">
    <div class="topbar">
      <div>
        <span class="eyebrow">Admin Panel</span>
        <h1>Tambah Mahasiswa Baru</h1>
      </div>
    </div>

    <?php if ($error): ?><div class="alert alert-err"><?= e($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-ok"><?= e($success) ?></div><?php endif; ?>

    <div class="card">
      <h2>Data Diri</h2>
      <form method="post" action="add_student.php">
        <div class="form-row">
          <div>
            <label for="nim">NIM</label>
            <input type="text" id="nim" name="nim" required>
          </div>
          <div>
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" required>
          </div>
        </div>
        <label for="program_studi">Program Studi</label>
        <input type="text" id="program_studi" name="program_studi" placeholder="Contoh: Teknik Informatika">

        <h2 style="margin-top:8px">Akun Login Mahasiswa</h2>
        <div class="form-row">
          <div>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
          </div>
          <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
          </div>
        </div>

        <button type="submit">Simpan Mahasiswa &amp; Buat Akun</button>
      </form>
    </div>
  </main>
</div>
</body>
</html>
