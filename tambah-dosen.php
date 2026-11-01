<?php
require_once 'koneksi.php';

$pesan = '';
$tipe_pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama']);
    $nidn  = !empty($_POST['nidn']) ? trim($_POST['nidn']) : null;
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;

    if (empty($nama)) {
        $pesan = "Nama Dosen wajib diisi!";
        $tipe_pesan = "danger";
    } else {
        try {
            $sql = "INSERT INTO dosen (nama, nidn, email) VALUES (:nama, :nidn, :email)";
            $stmt = $koneksi->prepare($sql);
            $simpan = $stmt->execute([
                ':nama'  => $nama,
                ':nidn'  => $nidn,
                ':email' => $email
            ]);

            if ($simpan) {
                header("Location: dosen.php");
                exit;
            }
        } catch (PDOException $e) {
            $pesan = "Gagal menyimpan data dosen: " . $e->getMessage();
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
                        <h4 class="mb-0 fs-5">Tambah Dosen Baru</h4>
                    </div>
                    <div class="card-body">
                        
                        <?php if (!empty($pesan)): ?>
                            <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($pesan) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <!-- Field 1: Nama Dosen -->
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Dosen <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Contoh: Pak Anwar, M.Kom." required>
                            </div>

                            <!-- Field 2: NIDN -->
                            <div class="mb-3">
                                <label for="nidn" class="form-label">NIDN</label>
                                <input type="text" class="form-control" id="nidn" name="nidn" placeholder="Contoh: 041505001">
                            </div>

                            <!-- Field 3: Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Contoh: anwar@horizon.ac.id">
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="dosen.php" class="btn btn-secondary">Kembali</a>
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