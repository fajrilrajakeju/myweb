<?php
require_once 'koneksi.php';

// 1. Konfigurasi Pagination & Searching
$batas   = 5; // Jumlah baris data per halaman
$halaman = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$halaman = ($halaman > 0) ? $halaman : 1;
$offset  = ($halaman - 1) * $batas;

$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // 2. Hitung Total Data (termasuk pencarian nama dosen)
    $sql_count  = "SELECT COUNT(*) FROM mata_kuliah m 
                  LEFT JOIN dosen d ON m.dosen_id = d.id 
                  WHERE m.nama LIKE :kw OR m.kode LIKE :kw OR d.nama LIKE :kw";
    $stmt_count = $koneksi->prepare($sql_count);
    $stmt_count->bindValue(':kw', "%$keyword%", PDO::PARAM_STR);
    $stmt_count->execute();
    $total_data = $stmt_count->fetchColumn();

    $total_halaman = ceil($total_data / $batas);

    // 3. Query Utama dengan LEFT JOIN ke tabel dosen
    $sql  = "SELECT m.*, d.nama AS nama_dosen 
            FROM mata_kuliah m 
            LEFT JOIN dosen d ON m.dosen_id = d.id 
            WHERE m.nama LIKE :kw OR m.kode LIKE :kw OR d.nama LIKE :kw 
            ORDER BY m.kode ASC LIMIT :limit OFFSET :offset";
    $stmt = $koneksi->prepare($sql);
    $stmt->bindValue(':kw', "%$keyword%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $batas, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error mengambil data: " . $e->getMessage());
}

// 4. Panggil Header dan Sidebar AdminLTE
require_once 'layouts/header.php';
require_once 'layouts/sidebar.php';
?>

<!-- Konten Utama -->
<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-sm-6">
          <h1 class="mb-0 fs-3">Daftar Mata Kuliah</h1>
        </div>
        <div class="col-sm-6 text-end">
          <a href="tambah-mata-kuliah.php" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Mata Kuliah
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
          <form method="GET" action="mata-kuliah.php" class="row g-2 align-items-center">
            <div class="col-auto">
              <input type="text" name="search" class="form-control" placeholder="Cari Kode / Nama / Dosen..." value="<?= htmlspecialchars($keyword); ?>">
            </div>
            <div class="col-auto">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Cari
              </button>
              <?php if ($keyword !== ''): ?>
                <a href="mata-kuliah.php" class="btn btn-secondary">Reset</a>
              <?php endif; ?>
            </div>
          </form>
        </div>

        <!-- Body Card: Tabel Data -->
        <div class="card-body p-0">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th scope="col">Kode</th>
                <th scope="col">Nama Mata Kuliah</th>
                <th scope="col">Semester</th>
                <th scope="col">Bobot (SKS)</th>
                <th scope="col">Dosen</th>
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
                    <td><?= $row['sks'] !== null ? htmlspecialchars($row['sks']) . ' SKS' : '-' ?></td>
                    <td>
                      <!-- Menampilkan nama dosen atau keterangan jika belum di-set -->
                      <?= !empty($row['nama_dosen']) ? '<strong>' . htmlspecialchars($row['nama_dosen']) . '</strong>' : '<em class="text-muted">Belum diset</em>' ?>
                    </td>
                    <td>
                      <a class="btn btn-sm btn-warning" href="edit-mata-kuliah.php?kode=<?= urlencode($row['kode']) ?>">Edit</a>
                      <a class="btn btn-sm btn-danger ms-1" href="hapus-mata-kuliah.php?kode=<?= urlencode($row['kode']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?')">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center py-3">Belum ada data mata kuliah.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Footer Card: Navigasi Pagination -->
        <?php if ($total_halaman > 1): ?>
        <div class="card-footer clearfix">
          <ul class="pagination pagination-sm m-0 float-end">
            
            <!-- Tombol Previous -->
            <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
              <a class="page-link" href="?page=<?= $halaman - 1; ?>&search=<?= urlencode($keyword); ?>">« Previous</a>
            </li>

            <!-- Angka Halaman -->
            <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
              <li class="page-item <?= ($i == $halaman) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?= $i; ?>&search=<?= urlencode($keyword); ?>"><?= $i; ?></a>
              </li>
            <?php endfor; ?>

            <!-- Tombol Next -->
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