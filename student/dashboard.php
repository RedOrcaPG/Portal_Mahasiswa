<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';
require_mahasiswa();

$mahasiswa_id = $_SESSION['mahasiswa_id'];

$stmt = $pdo->prepare('SELECT * FROM mahasiswa WHERE id = ?');
$stmt->execute([$mahasiswa_id]);
$mhs = $stmt->fetch();

$stmt = $pdo->prepare('
    SELECT mk.kode_mk, mk.nama_mk, mk.sks, n.nilai_huruf, n.semester
    FROM nilai n
    JOIN mata_kuliah mk ON mk.id = n.mata_kuliah_id
    WHERE n.mahasiswa_id = ?
    ORDER BY n.created_at DESC
');
$stmt->execute([$mahasiswa_id]);
$rows = $stmt->fetchAll();

// Hitung IPK sederhana
$bobot = ['A'=>4,'A-'=>3.7,'B+'=>3.3,'B'=>3,'B-'=>2.7,'C+'=>2.3,'C'=>2,'D'=>1,'E'=>0];
$totalSks = 0; $totalBobot = 0;
foreach ($rows as $r) {
    $totalSks += $r['sks'];
    $totalBobot += $r['sks'] * ($bobot[$r['nilai_huruf']] ?? 0);
}
$ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Mahasiswa</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <div class="brand">Student Portal</div>
    <div class="brand-sub">Mahasiswa</div>
    <nav>
      <a href="dashboard.php" class="active">Dashboard Saya</a>
    </nav>
    <div class="spacer"></div>
    <a class="logout-link" href="../logout.php">Keluar &rarr;</a>
  </aside>

  <main class="main">
    <div class="topbar">
      <div>
        <span class="eyebrow">Dashboard</span>
        <h1>Halo, <?= e($mhs['nama']) ?></h1>
      </div>
    </div>

    <div class="stat-grid">
      <div class="stat">
        <div class="label">NIM</div>
        <div class="value mono" style="font-size:22px"><?= e($mhs['nim']) ?></div>
      </div>
      <div class="stat">
        <div class="label">Mata Kuliah Selesai</div>
        <div class="value"><?= count($rows) ?></div>
      </div>
      <div class="stat">
        <div class="label">Total SKS</div>
        <div class="value"><?= $totalSks ?></div>
      </div>
      <div class="stat">
        <div class="label">IPK</div>
        <div class="value"><?= number_format($ipk, 2) ?></div>
      </div>
    </div>

    <div class="card">
      <h2>Riwayat Mata Kuliah &amp; Nilai</h2>
      <?php if (empty($rows)): ?>
        <p style="color:var(--ink-soft)">Belum ada mata kuliah yang tercatat selesai.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Semester</th><th>Nilai</th></tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td class="mono"><?= e($r['kode_mk']) ?></td>
                <td><?= e($r['nama_mk']) ?></td>
                <td><?= (int)$r['sks'] ?></td>
                <td><?= e($r['semester'] ?? '-') ?></td>
                <td>
                  <?php $low = in_array($r['nilai_huruf'], ['D','E']); ?>
                  <span class="grade-pill <?= $low ? 'low' : '' ?>"><?= e($r['nilai_huruf']) ?></span>
                </td>
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
