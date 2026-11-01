<?php
require_once 'koneksi.php';

$pesan = '';
$tipe_pesan = '';

// 1. Ambil kode mata kuliah dari parameter URL
$kode = $_GET['kode'] ?? $_POST['kode_lama'] ?? null;

if (!$kode) {
    header("Location: mata-kuliah.php");
    exit;
}

// 2. Ambil daftar dosen dari database untuk mengisi opsi Dropdown
try {
    $stmt_dosen = $koneksi->query("SELECT id, nama FROM dosen ORDER BY nama ASC");
    $list_dosen = $stmt_dosen->fetchAll();
} catch (PDOException $e) {
    die("Error mengambil data dosen: " . $e->getMessage());
}

// 3. Proses saat form di-submit (Method POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_lama   = $_POST['kode_lama'];
    $kode_matkul = trim($_POST['kode_matkul']);
    $nama_matkul = trim($_POST['nama_matkul']);
    $semester    = !empty($_POST['semester']) ? (int)$_POST['semester'] : null;
    $sks         = !empty($_POST['sks']) ? (int)$_POST['sks'] : null;
    $dosen_id    = !empty($_POST['dosen_id']) ? (int)$_POST['dosen_id'] : null;

    if (empty($kode_matkul) || empty($nama_matkul)) {
        $pesan = "Kode dan Nama Mata Kuliah wajib diisi!";
        $tipe_pesan = "danger";
    } else {
        try {
            // Prepared Statement untuk UPDATE Data
            $sql = "UPDATE mata_kuliah 
                    SET kode = :kode_baru, 
                        nama = :nama, 
                        semester = :semester, 
                        sks = :sks, 
                        dosen_id = :dosen_id 
                    WHERE kode = :kode_lama";

            $stmt = $koneksi->prepare($sql);
            $update = $stmt->execute([
                ':kode_baru' => $kode_matkul,
                ':nama'      => $nama_matkul,
                ':semester'  => $semester,
                ':sks'       => $sks,
                ':dosen_id'  => $dosen_id,
                ':kode_lama' => $kode_lama
            ]);

            if ($update) {
                header("Location: mata-kuliah.php");
                exit;
            }
        } catch (PDOException $e) {
            $pesan = "Gagal memperbarui data: " . $e->getMessage();
            $tipe_pesan = "danger";
        }
    }
}

// 4. Ambil data mata kuliah dari database untuk ditampilkan di form
try {
    $stmt = $koneksi->prepare("SELECT * FROM mata_kuliah WHERE kode = :kode");
    $stmt->execute([':kode' => $kode]);
    $matkul = $stmt->fetch();

    if (!$matkul) {
        die("Data mata kuliah tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Error mengambil data: " . $e->getMessage());
}

// 5. Panggil Header dan Sidebar AdminLTE
require_once 'layouts/header.php';
require_once 'layouts/sidebar.php';
?>

<!-- Konten Utama -->
<main class="app-main">
    <div class="container mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0 fs-5">Edit Mata Kuliah</h4>
                    </div>
                    <div class="card-body">
                        
                        <?php if (!empty($pesan)): ?>
                            <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($pesan) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <!-- Input Hidden untuk menyimpan kode lama sebagai acuan WHERE -->
                            <input type="hidden" name="kode_lama" value="<?= htmlspecialchars($matkul['kode']) ?>">

                            <!-- Field 1: Kode Mata Kuliah -->
                            <div class="mb-3">
                                <label for="kode_matkul" class="form-label">Kode Mata Kuliah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kode_matkul" name="kode_matkul" value="<?= htmlspecialchars($matkul['kode']) ?>" required>
                            </div>

                            <!-- Field 2: Nama Mata Kuliah -->
                            <div class="mb-3">
                                <label for="nama_matkul" class="form-label">Nama Mata Kuliah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_matkul" name="nama_matkul" value="<?= htmlspecialchars($matkul['nama']) ?>" required>
                            </div>

                            <!-- Field 3: Semester -->
                            <div class="mb-3">
                                <label for="semester" class="form-label">Semester</label>
                                <input type="number" class="form-control" id="semester" name="semester" min="1" max="8" value="<?= htmlspecialchars($matkul['semester'] ?? '') ?>">
                            </div>

                            <!-- Field 4: Bobot (SKS) -->
                            <div class="mb-3">
                                <label for="sks" class="form-label">Bobot (SKS)</label>
                                <input type="number" class="form-control" id="sks" name="sks" min="1" max="6" value="<?= htmlspecialchars($matkul['sks'] ?? '') ?>">
                            </div>

                            <!-- Field 5: Dropdown Dosen Pengampu (Dinamis dari Tabel Dosen) -->
                            <div class="mb-3">
                                <label for="dosen_id" class="form-label">Dosen Pengampu</label>
                                <select name="dosen_id" id="dosen_id" class="form-select">
                                    <option value="">-- Pilih Dosen Pengampu --</option>
                                    <?php foreach ($list_dosen as $dosen): ?>
                                        <option value="<?= $dosen['id'] ?>" <?= ($matkul['dosen_id'] == $dosen['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($dosen['nama']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="mata-kuliah.php" class="btn btn-secondary">Batal</a>
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