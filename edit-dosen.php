<?php
require_once 'koneksi.php';

$pesan = '';
$tipe_pesan = '';

$id = $_GET['id'] ?? $_POST['id_dosen'] ?? null;

if (!$id) {
    header("Location: dosen.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_dosen = (int)$_POST['id_dosen'];
    $nama     = trim($_POST['nama']);
    $nidn     = !empty($_POST['nidn']) ? trim($_POST['nidn']) : null;
    $email    = !empty($_POST['email']) ? trim($_POST['email']) : null;

    if (empty($nama)) {
        $pesan = "Nama Dosen wajib diisi!";
        $tipe_pesan = "danger";
    } else {
        try {
            $sql = "UPDATE dosen SET nama = :nama, nidn = :nidn, email = :email WHERE id = :id";
            $stmt = $koneksi->prepare($sql);
            $update = $stmt->execute([
                ':nama'  => $nama,
                ':nidn'  => $nidn,
                ':email' => $email,
                ':id'    => $id_dosen
            ]);

            if ($update) {
                header("Location: dosen.php");
                exit;
            }
        } catch (PDOException $e) {
            $pesan = "Gagal memperbarui data dosen: " . $e->getMessage();
            $tipe_pesan = "danger";
        }
    }
}

try {
    $stmt = $koneksi->prepare("SELECT * FROM dosen WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $dosen = $stmt->fetch();

    if (!$dosen) {
        die("Data dosen tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Error mengambil data dosen: " . $e->getMessage());
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
                        <h4 class="mb-0 fs-5">Edit Data Dosen</h4>
                    </div>
                    <div class="card-body">
                        
                        <?php if (!empty($pesan)): ?>
                            <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($pesan) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <input type="hidden" name="id_dosen" value="<?= htmlspecialchars($dosen['id']) ?>">

                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Dosen <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($dosen['nama']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="nidn" class="form-label">NIDN</label>
                                <input type="text" class="form-control" id="nidn" name="nidn" value="<?= htmlspecialchars($dosen['nidn'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($dosen['email'] ?? '') ?>">
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="dosen.php" class="btn btn-secondary">Batal</a>
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