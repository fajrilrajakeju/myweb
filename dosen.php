<?php
require_once 'koneksi.php';

// 1. Konfigurasi Pagination & Searching
$batas   = 5;
$halaman = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$halaman = ($halaman > 0) ? $halaman : 1;
$offset  = ($halaman - 1) * $batas;

$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // 2. Hitung Total Data Dosen
    $sql_count  = "SELECT COUNT(*) FROM dosen WHERE nama LIKE :kw OR nidn LIKE :kw OR email LIKE :kw";
    $stmt_count = $koneksi->prepare($sql_count);
    $stmt_count->bindValue(':kw', "%$keyword%", PDO::PARAM_STR);
    $stmt_count->execute();
    $total_data = $stmt_count->fetchColumn();

    $total_halaman = ceil($total_data / $batas);

    // 3. Query Data Utama Dosen
    $sql  = "SELECT * FROM dosen 
            WHERE nama LIKE :kw OR nidn LIKE :kw OR email LIKE :kw 
            ORDER BY id ASC LIMIT :limit OFFSET :offset";
    $stmt = $koneksi->prepare($sql);
    $stmt->bindValue(':kw', "%$keyword%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $batas, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error mengambil data dosen: " . $e->getMessage());
}

require_once 'layouts/header.php';
require_once 'layouts/sidebar.php';
?>

<!-- Konten Utama -->
<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-sm-6">
          <h1 class="mb-0 fs-3">Daftar Dosen</h1>
        </div>
        <div class="col-sm-6 text-end">
          <a href="tambah-dosen.php" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Dosen
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="card mb-4">
        
        <!-- Header Card: Form Searching -->
        <div class="card-header">
          <form method="GET" action="dosen.php" class="row g-2 align-items-center">
            <div class="col-auto">
              <input type="text" name="search" class="form-control" placeholder="Cari Nama / NIDN / Email..." value="<?= htmlspecialchars($keyword); ?>">
            </div>
            <div class="col-auto">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Cari
              </button>
              <?php if ($keyword !== ''): ?>
                <a href="dosen.php" class="btn btn-secondary">Reset</a>
              <?php endif; ?>
            </div>
          </form>
        </div>

        <!-- Body Card: Tabel Data -->
        <div class="card-body p-0">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Nama Dosen</th>
                <th scope="col">NIDN</th>
                <th scope="col">Email</th>
                <th scope="col">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($data)): ?>
                <?php foreach ($data as $row): ?>
                  <tr>
                    <th scope="row"><?= htmlspecialchars($row['id']) ?></th>
                    <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                    <td><?= $row['nidn'] ? htmlspecialchars($row['nidn']) : '<em class="text-muted">-</em>' ?></td>
                    <td><?= $row['email'] ? htmlspecialchars($row['email']) : '<em class="text-muted">-</em>' ?></td>
                    <td>
                      <a class="btn btn-sm btn-warning" href="edit-dosen.php?id=<?= urlencode($row['id']) ?>">Edit</a>
                      <a class="btn btn-sm btn-danger ms-1" href="hapus-dosen.php?id=<?= urlencode($row['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data dosen ini?')">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="text-center py-3">Belum ada data dosen.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Footer Card: Navigasi Pagination -->
        <?php if ($total_halaman > 1): ?>
        <div class="card-footer clearfix">
          <ul class="pagination pagination-sm m-0 float-end">
            
            <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
              <a class="page-link" href="?page=<?= $halaman - 1; ?>&search=<?= urlencode($keyword); ?>">« Previous</a>
            </li>

            <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
              <li class="page-item <?= ($i == $halaman) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?= $i; ?>&search=<?= urlencode($keyword); ?>"><?= $i; ?></a>
              </li>
            <?php endfor; ?>

            <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
              <a class="page-link" href="?page=<?= $halaman + 1; ?>&search=<?= urlencode($keyword); ?>">Next »</a>
            </li>

          </ul>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</main>

<?php
require_once 'layouts/footer.php';
?>