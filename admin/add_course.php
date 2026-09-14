<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';
require_admin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode_mk'] ?? '');
    $nama = trim($_POST['nama_mk'] ?? '');
    $sks  = (int)($_POST['sks'] ?? 0);

    if ($kode === '' || $nama === '' || $sks <= 0) {
        $error = 'Kode, nama mata kuliah, dan SKS (lebih dari 0) wajib diisi.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO mata_kuliah (kode_mk, nama_mk, sks) VALUES (?, ?, ?)');
            $stmt->execute([$kode, $nama, $sks]);
            $success = "Mata kuliah \"$nama\" berhasil ditambahkan.";
        } catch (PDOException $ex) {
            $error = $ex->getCode() == 23000 ? 'Kode mata kuliah sudah ada.' : 'Gagal menyimpan: ' . $ex->getMessage();
        }
    }
}

$courses = $pdo->query('SELECT * FROM mata_kuliah ORDER BY kode_mk')->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Mata Kuliah</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <div class="brand">Student Portal</div>
    <div class="brand-sub">Admin</div>
    <nav>
      <a href="dashboard.php">Daftar Mahasiswa</a>
      <a href="add_student.php">Tambah Mahasiswa</a>
      <a href="add_course.php" class="active">Tambah Mata Kuliah</a>
    </nav>
    <div class="spacer"></div>
    <a class="logout-link" href="../logout.php">Keluar &rarr;</a>
  </aside>

  <main class="main">
    <div class="topbar">
      <div>
        <span class="eyebrow">Admin Panel</span>
        <h1>Mata Kuliah</h1>
      </div>
    </div>

    <?php if ($error): ?><div class="alert alert-err"><?= e($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-ok"><?= e($success) ?></div><?php endif; ?>

    <div class="card">
      <h2>Tambah Mata Kuliah Baru</h2>
      <form method="post" action="add_course.php">
        <div class="form-row">
          <div>
            <label for="kode_mk">Kode Mata Kuliah</label>
            <input type="text" id="kode_mk" name="kode_mk" placeholder="Contoh: IF301" required>
          </div>
          <div>
            <label for="nama_mk">Nama Mata Kuliah</label>
            <input type="text" id="nama_mk" name="nama_mk" required>
          </div>
          <div>
            <label for="sks">SKS</label>
            <input type="number" id="sks" name="sks" min="1" max="6" value="3" required>
          </div>
        </div>
        <button type="submit">Simpan Mata Kuliah</button>
      </form>
    </div>

    <div class="card">
      <h2>Daftar Mata Kuliah Terdaftar</h2>
      <?php if (empty($courses)): ?>
        <p style="color:var(--ink-soft)">Belum ada mata kuliah.</p>
      <?php else: ?>
        <table>
          <thead><tr><th>Kode</th><th>Nama</th><th>SKS</th></tr></thead>
          <tbody>
            <?php foreach ($courses as $c): ?>
              <tr>
                <td class="mono"><?= e($c['kode_mk']) ?></td>
                <td><?= e($c['nama_mk']) ?></td>
                <td><?= (int)$c['sks'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </main>
</div>
</body>
</html>
