<?php
require_once 'koneksi.php';

$pesan = '';
$tipe_pesan = '';

// 1. Ambil daftar mata kuliah untuk opsi Dropdown
try {
    $stmt_matkul = $koneksi->query("SELECT kode, nama FROM mata_kuliah ORDER BY nama ASC");
    $list_matkul = $stmt_matkul->fetchAll();
} catch (PDOException $e) {
    die("Error mengambil data mata kuliah: " . $e->getMessage());
}

// 2. Memproses data saat form dikirimkan (Method POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matkul_kode = trim($_POST['matkul_kode']);
    $judul       = trim($_POST['judul']);
    $deskripsi   = trim($_POST['deskripsi']);
    $deadline    = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
    $status      = $_POST['status'] ?? 'Belum Selesai';

    if (empty($matkul_kode) || empty($judul)) {
        $pesan = "Mata Kuliah dan Judul Tugas wajib diisi!";
        $tipe_pesan = "danger";
    } else {
        try {
            $sql = "INSERT INTO tugas (matkul_kode, judul, deskripsi, deadline, status) 
                    VALUES (:matkul_kode, :judul, :deskripsi, :deadline, :status)";
            
            $stmt = $koneksi->prepare($sql);
            $simpan = $stmt->execute([
                ':matkul_kode' => $matkul_kode,
                ':judul'       => $judul,
                ':deskripsi'   => $deskripsi,
                ':deadline'    => $deadline,
                ':status'      => $status
            ]);

            if ($simpan) {
                header("Location: tugas.php");
                exit;
            }
        } catch (PDOException $e) {
            $pesan = "Gagal menyimpan data tugas: " . $e->getMessage();
            $tipe_pesan = "danger";
        }
    }
}

require_once 'layouts/header.php';
require_once 'layouts/sidebar.php';
?>

<main class="app-main">
    <div class="container mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0 fs-5">Tambah Tugas Baru</h4>
                    </div>
                    <div class="card-body">
                        
                        <?php if (!empty($pesan)): ?>
                            <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($pesan) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <!-- Field 1: Opsi Mata Kuliah -->
                            <div class="mb-3">
                                <label for="matkul_kode" class="form-label">Pilih Mata Kuliah <span class="text-danger">*</span></label>
                                <select name="matkul_kode" id="matkul_kode" class="form-select" required>
                                    <option value="">-- Pilih Mata Kuliah --</option>
                                    <?php foreach ($list_matkul as $mk): ?>
                                        <option value="<?= htmlspecialchars($mk['kode']) ?>">
                                            [<?= htmlspecialchars($mk['kode']) ?>] <?= htmlspecialchars($mk['nama']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Field 2: Judul Tugas -->
                            <div class="mb-3">
                                <label for="judul" class="form-label">Judul Tugas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="judul" name="judul" placeholder="Contoh: Tugas 1 - Auth PHP Native" required>
                            </div>

                            <!-- Field 3: Deskripsi -->
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi / Petunjuk</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Detail instruksi tugas..."></textarea>
                            </div>

                            <!-- Field 4: Deadline -->
                            <div class="mb-3">
                                <label for="deadline" class="form-label">Tenggat Waktu (Deadline)</label>
                                <input type="date" class="form-control" id="deadline" name="deadline">
                            </div>

                            <!-- Field 5: Status -->
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="Belum Selesai">Ditugaskan</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="tugas.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Simpan Data</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once 'layouts/footer.php';
?>