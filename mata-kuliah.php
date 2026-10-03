<?php
require_once 'koneksi.php';

try {
    $stmt = $koneksi->prepare("SELECT * FROM mata_kuliah");
    $matkul = $stmt->execute();
    $data = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error mengambil data: " . $e->getMessage());
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MyWeb - Fajril Yusin Supriadi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<style>
    body {
        font-family: 'Inter', sans-serif !important;
        letter-spacing: -0.5px !important;   
        background-color: #f4f6f9 !important;
        color: #1e293b !important;
    }
</style>
</head>
<body>
    <main>
        <nav class="navbar navbar-expand-lg bg-primary navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">MyWeb</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                <li class="nav-item">
                <a class="nav-link" aria-current="page" href="index.php">Beranda</a>
                </li>
                <li class="nav-item">
                <a class="nav-link active" href="mata-kuliah.php">Mata Kuliah</a>
                </li>
                </ul>
                </div>
            </div>
        </nav>
    </main>
    <div class="container mt-4">
        <div class="row">
            <div class="col-6">
                <h1>Daftar Mata Kuliah</h1>
            </div>

            <div class="col-6 text-end">
                <a href="tambah-mata-kuliah.php" class="btn btn-primary">Tambah Mata Kuliah</a>
            </div>
        </div>
        <table class="table table-striped mt-3">
          <thead>
            <tr>
              <th scope="col">Kode Mata Kuliah</th>
              <th scope="col">Nama Mata Kuliah</th>
              <th scope="col">Semester</th>
              <th scope="col">Bobot (SKS)</th>
              <th scope="col">Dosen ID</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($data)): ?>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <th scope="row"><?= htmlspecialchars($row['kode']) ?></th>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['semester'] ?? '-') ?></td>
                        <td><?= $row['sks'] !== null ? htmlspecialchars($row['sks']) : '<em class="text-muted">NULL</em>' ?></td>
                        <td><?= $row['dosen_id'] !== null ? htmlspecialchars($row['dosen_id']) : '<em class="text-muted">NULL</em>' ?></td>
                        <td>
                            <a class="btn btn-warning" href="edit-mata-kuliah.php?kode=<?= urlencode($row['kode']) ?>">Edit</a>
                            <a style="margin-left: 4px;" class="btn btn-danger" href="hapus-mata-kuliah.php?kode=<?= urlencode($row['kode']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Belum ada data mata kuliah.</td>
                </tr>
            <?php endif; ?>
          </tbody>
        </table>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>