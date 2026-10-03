<?php
require_once 'koneksi.php';

$pesan = '';
$tipe_pesan = '';

// Memproses data saat form dikirimkan (Method POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_matkul = trim($_POST['kode_matkul']);
    $nama_matkul = trim($_POST['nama_matkul']);
    $semester    = !empty($_POST['semester']) ? (int)$_POST['semester'] : null;
    $sks         = !empty($_POST['sks']) ? (int)$_POST['sks'] : null;

    // Validasi input sederhana
    if (empty($kode_matkul) || empty($nama_matkul)) {
        $pesan = "Kode dan Nama Mata Kuliah wajib diisi!";
        $tipe_pesan = "danger";
    } else {
        try {
            // Prepared Statement untuk Insert Data
            $sql = "INSERT INTO mata_kuliah (kode, nama, semester, sks) 
                    VALUES (:kode, :nama, :semester, :sks)";
            
            $stmt = $koneksi->prepare($sql);
            
            // Eksekusi query dengan binding parameter
            $simpan = $stmt->execute([
                ':kode'     => $kode_matkul,
                ':nama'     => $nama_matkul,
                ':semester' => $semester,
                ':sks'      => $sks
            ]);

            if ($simpan) {
                // Redirect kembali ke halaman daftar mata kuliah setelah berhasil
                header("Location: mata-kuliah.php");
                exit;
            }
        } catch (PDOException $e) {
            // Menangkap error jika kode mata kuliah duplikat atau ada masalah database
            $pesan = "Gagal menyimpan data: " . $e->getMessage();
            $tipe_pesan = "danger";
        }
    }
}
?>

<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Tambah Mata Kuliah - MyWeb</title>
<style>
    body {
        font-family: 'Inter', sans-serif !important;
        letter-spacing: -0.5px !important;   
        background-color: #f4f6f9 !important;
        color: #1e293b !important;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    <main>
    </main>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Tambah Mata Kuliah</h4>
                    </div>
                    <div class="card-body">
                        
                        <!-- Notifikasi Error/Pesan -->
                        <?php if (!empty($pesan)): ?>
                            <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($pesan) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <!-- Field 1: Kode Mata Kuliah -->
                            <div class="mb-3">
                                <label for="kode_matkul" class="form-label">Kode Mata Kuliah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kode_matkul" name="kode_matkul" placeholder="Contoh: ICT015" required>
                            </div>

                            <!-- Field 2: Nama Mata Kuliah -->
                            <div class="mb-3">
                                <label for="nama_matkul" class="form-label">Nama Mata Kuliah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_matkul" name="nama_matkul" placeholder="Contoh: Pemrograman Web Lanjut" required>
                            </div>

                            <!-- Field 3: Semester -->
                            <div class="mb-3">
                                <label for="semester" class="form-label">Semester</label>
                                <input type="number" class="form-control" id="semester" name="semester" min="1" max="8" placeholder="Contoh: 3">
                            </div>

                            <!-- Field 4: Bobot (SKS) -->
                            <div class="mb-3">
                                <label for="sks" class="form-label">Bobot (SKS)</label>
                                <input type="number" class="form-control" id="sks" name="sks" min="1" max="6" placeholder="Contoh: 3">
                            </div>

                            <div class="mb-3">
                                <label for="dosen" class="form-label">Dosen</label>
                                <input type="text" class="form-control" id="dosen" name="dosen" placeholder="Contoh: Dr. John Doe">
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="mata-kuliah.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Simpan Data</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>