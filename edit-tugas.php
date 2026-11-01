<?php
require_once 'koneksi.php';

$pesan = '';
$tipe_pesan = '';

$id = $_GET['id'] ?? $_POST['id_tugas'] ?? null;

if (!$id) {
    header("Location: tugas.php");
    exit;
}

// 1. Ambil list mata kuliah untuk opsi Dropdown
try {
    $stmt_matkul = $koneksi->query("SELECT kode, nama FROM mata_kuliah ORDER BY nama ASC");
    $list_matkul = $stmt_matkul->fetchAll();
} catch (PDOException $e) {
    die("Error mengambil data mata kuliah: " . $e->getMessage());
}

// 2. Proses saat form di-submit (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tugas    = (int)$_POST['id_tugas'];
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
            $sql = "UPDATE tugas 
                    SET matkul_kode = :matkul_kode, 
                        judul = :judul, 
                        deskripsi = :deskripsi, 
                        deadline = :deadline, 
                        status = :status 
                    WHERE id = :id";

            $stmt = $koneksi->prepare($sql);
            $update = $stmt->execute([
                ':matkul_kode' => $matkul_kode,
                ':judul'       => $judul,
                ':deskripsi'   => $deskripsi,
                ':deadline'    => $deadline,
                ':status'      => $status,
                ':id'          => $id_tugas
            ]);

            if ($update) {
                header("Location: tugas.php");
                exit;
            }
        } catch (PDOException $e) {
            $pesan = "Gagal memperbarui data: " . $e->getMessage();
            $tipe_pesan = "danger";
        }
    }
}

// 3. Ambil data tugas berdasarkan ID untuk diisi ke form
try {
    $stmt = $koneksi->prepare("SELECT * FROM tugas WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $tugas = $stmt->fetch();

    if (!$tugas) {
        die("Data tugas tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Error mengambil data tugas: " . $e->getMessage());
}

require_once 'layouts/header.php';
require_once 'layouts/sidebar.php';
?>

<main class="app-main">
    <div class="container mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0 fs-5">Edit Data Tugas</h4>
                    </div>
                    <div class="card-body">
                        
                        <?php if (!empty($pesan)): ?>
                            <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($pesan) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <input type="hidden" name="id_tugas" value="<?= htmlspecialchars($tugas['id']) ?>">

                            <!-- Field 1: Mata Kuliah -->
                            <div class="mb-3">
                                <label for="matkul_kode" class="form-label">Pilih Mata Kuliah <span class="text-danger">*</span></label>
                                <select name="matkul_kode" id="matkul_kode" class="form-select" required>
                                    <option value="">-- Pilih Mata Kuliah --</option>
                                    <?php foreach ($list_matkul as $mk): ?>
                                        <option value="<?= htmlspecialchars($mk['kode']) ?>" <?= ($tugas['matkul_kode'] === $mk['kode']) ? 'selected' : '' ?>>
                                            [<?= htmlspecialchars($mk['kode']) ?>] <?= htmlspecialchars($mk['nama']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Field 2: Judul Tugas -->
                            <div class="mb-3">
                                <label for="judul" class="form-label">Judul Tugas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="judul" name="judul" value="<?= htmlspecialchars($tugas['judul']) ?>" required>
                            </div>

                            <!-- Field 3: Deskripsi -->
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi / Petunjuk</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= htmlspecialchars($tugas['deskripsi'] ?? '') ?></textarea>
                            </div>

                            <!-- Field 4: Deadline -->
                            <div class="mb-3">
                                <label for="deadline" class="form-label">Tenggat Waktu (Deadline)</label>
                                <input type="date" class="form-control" id="deadline" name="deadline" value="<?= htmlspecialchars($tugas['deadline'] ?? '') ?>">
                            </div>

                            <!-- Field 5: Status -->
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="Belum Selesai" <?= ($tugas['status'] === 'Belum Selesai') ? 'selected' : '' ?>>Belum Selesai</option>
                                    <option value="Selesai" <?= ($tugas['status'] === 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="tugas.php" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-warning">Perbarui Data</button>
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