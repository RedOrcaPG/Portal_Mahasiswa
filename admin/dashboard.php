<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';
require_admin();

$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE nama LIKE ? OR nim LIKE ? ORDER BY nama");
    $like = "%$q%";
    $stmt->execute([$like, $like]);
} else {
    $stmt = $pdo->query("SELECT * FROM mahasiswa ORDER BY nama");
}
$mahasiswa = $stmt->fetchAll();

$totalMahasiswa = $pdo->query("SELECT COUNT(*) FROM mahasiswa")->fetchColumn();
$totalMK        = $pdo->query("SELECT COUNT(*) FROM mata_kuliah")->fetchColumn();
$totalNilai     = $pdo->query("SELECT COUNT(*) FROM nilai")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <div class="brand">Student Portal</div>
    <div class="brand-sub">Admin</div>
    <nav>
      <a href="dashboard.php" class="active">Daftar Mahasiswa</a>
      <a href="add_student.php">Tambah Mahasiswa</a>
      <a href="add_course.php">Tambah Mata Kuliah</a>
    </nav>
    <div class="spacer"></div>
    <a class="logout-link" href="../logout.php">Keluar &rarr;</a>
  </aside>

  <main class="main">
    <div class="topbar">
      <div>
        <span class="eyebrow">Admin Panel</span>
        <h1>Daftar Mahasiswa</h1>
      </div>
      <a href="add_student.php" class="btn">+ Tambah Mahasiswa</a>
    </div>

    <div class="stat-grid">
      <div class="stat"><div class="label">Total Mahasiswa</div><div class="value"><?= $totalMahasiswa ?></div></div>
      <div class="stat"><div class="label">Mata Kuliah Terdaftar</div><div class="value"><?= $totalMK ?></div></div>
      <div class="stat"><div class="label">Total Entri Nilai</div><div class="value"><?= $totalNilai ?></div></div>
    </div>

    <div class="card">
      <form method="get" style="margin-bottom:16px;display:flex;gap:10px;align-items:flex-end;">
        <div style="flex:1">
          <label for="q">Cari nama / NIM</label>
          <input type="text" id="q" name="q" value="<?= e($q) ?>" placeholder="Contoh: Siti / 2210101001" style="margin-bottom:0">
        </div>
        <button type="submit" class="btn-outline" style="background:transparent;border:1px solid var(--pine);color:var(--pine);height:42px;">Cari</button>
      </form>

      <?php if (empty($mahasiswa)): ?>
        <p style="color:var(--ink-soft)">Belum ada data mahasiswa.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr><th>NIM</th><th>Nama</th><th>Program Studi</th><th></th></tr>
          </thead>
          <tbody>
            <?php foreach ($mahasiswa as $m): ?>
              <tr>
                <td class="mono"><?= e($m['nim']) ?></td>
                <td><?= e($m['nama']) ?></td>
                <td><?= e($m['program_studi'] ?? '-') ?></td>
                <td><a href="student_detail.php?id=<?= (int)$m['id'] ?>">Kelola nilai &rarr;</a></td>
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
