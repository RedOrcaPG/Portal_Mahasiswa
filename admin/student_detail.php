<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM mahasiswa WHERE id = ?');
$stmt->execute([$id]);
$mhs = $stmt->fetch();

if (!$mhs) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

// Tambah entri nilai
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_nilai') {
    $mataKuliahId = (int)($_POST['mata_kuliah_id'] ?? 0);
    $nilaiHuruf   = $_POST['nilai_huruf'] ?? '';
    $semester     = trim($_POST['semester'] ?? '');
    $validGrades  = ['A','A-','B+','B','B-','C+','C','D','E'];

    if (!$mataKuliahId || !in_array($nilaiHuruf, $validGrades, true)) {
        $error = 'Mata kuliah dan nilai wajib dipilih dengan benar.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO nilai (mahasiswa_id, mata_kuliah_id, nilai_huruf, semester) VALUES (?, ?, ?, ?)');
            $stmt->execute([$id, $mataKuliahId, $nilaiHuruf, $semester ?: null]);
            $success = 'Nilai berhasil dicatat.';
        } catch (PDOException $ex) {
            $error = $ex->getCode() == 23000
                ? 'Mata kuliah ini untuk semester tersebut sudah tercatat bagi mahasiswa ini.'
                : 'Gagal menyimpan: ' . $ex->getMessage();
        }
    }
}

// Hapus entri nilai
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_nilai') {
    $nilaiId = (int)($_POST['nilai_id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM nilai WHERE id = ? AND mahasiswa_id = ?');
    $stmt->execute([$nilaiId, $id]);
    $success = 'Entri nilai dihapus.';
}

$courses = $pdo->query('SELECT * FROM mata_kuliah ORDER BY nama_mk')->fetchAll();

$stmt = $pdo->prepare('
    SELECT n.id, mk.kode_mk, mk.nama_mk, mk.sks, n.nilai_huruf, n.semester
    FROM nilai n JOIN mata_kuliah mk ON mk.id = n.mata_kuliah_id
    WHERE n.mahasiswa_id = ?
    ORDER BY n.created_at DESC
');
$stmt->execute([$id]);
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Nilai — <?= e($mhs['nama']) ?></title>
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
        <span class="eyebrow"><a href="dashboard.php" style="color:var(--brass)">&larr; Daftar Mahasiswa</a></span>
        <h1><?= e($mhs['nama']) ?></h1>
        <div style="color:var(--ink-soft)" class="mono">NIM <?= e($mhs['nim']) ?></div>
      </div>
    </div>

    <?php if ($error): ?><div class="alert alert-err"><?= e($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-ok"><?= e($success) ?></div><?php endif; ?>

    <div class="card">
      <h2>Catat Mata Kuliah Selesai</h2>
      <?php if (empty($courses)): ?>
        <p style="color:var(--ink-soft)">Belum ada mata kuliah master. <a href="add_course.php">Tambahkan dulu di sini.</a></p>
      <?php else: ?>
        <form method="post" action="student_detail.php?id=<?= $id ?>">
          <input type="hidden" name="action" value="add_nilai">
          <div class="form-row">
            <div>
              <label for="mata_kuliah_id">Mata Kuliah</label>
              <select id="mata_kuliah_id" name="mata_kuliah_id" required>
                <option value="">— Pilih —</option>
                <?php foreach ($courses as $c): ?>
                  <option value="<?= $c['id'] ?>"><?= e($c['kode_mk']) ?> — <?= e($c['nama_mk']) ?> (<?= $c['sks'] ?> SKS)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label for="nilai_huruf">Nilai</label>
              <select id="nilai_huruf" name="nilai_huruf" required>
                <option value="">— Pilih —</option>
                <?php foreach (['A','A-','B+','B','B-','C+','C','D','E'] as $g): ?>
                  <option value="<?= $g ?>"><?= $g ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label for="semester">Semester</label>
              <input type="text" id="semester" name="semester" placeholder="Ganjil 2024/2025">
            </div>
          </div>
          <button type="submit">Simpan Nilai</button>
        </form>
      <?php endif; ?>
    </div>

    <div class="card">
      <h2>Riwayat Nilai</h2>
      <?php if (empty($rows)): ?>
        <p style="color:var(--ink-soft)">Belum ada mata kuliah tercatat untuk mahasiswa ini.</p>
      <?php else: ?>
        <table>
          <thead><tr><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Semester</th><th>Nilai</th><th></th></tr></thead>
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
                <td>
                  <form method="post" action="student_detail.php?id=<?= $id ?>" onsubmit="return confirm('Hapus entri nilai ini?');">
                    <input type="hidden" name="action" value="delete_nilai">
                    <input type="hidden" name="nilai_id" value="<?= $r['id'] ?>">
                    <button type="submit" class="btn-clay" style="padding:5px 12px;font-size:12px;">Hapus</button>
                  </form>
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
