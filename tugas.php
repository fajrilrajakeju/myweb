<?php
require_once 'koneksi.php';

// 1. Konfigurasi Pagination & Searching
$batas   = 5;
$halaman = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$halaman = ($halaman > 0) ? $halaman : 1;
$offset  = ($halaman - 1) * $batas;

$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // 2. Hitung Total Data Tugas (dengan Multi-JOIN ke mata_kuliah & dosen)
    $sql_count  = "SELECT COUNT(*) FROM tugas t 
                  JOIN mata_kuliah m ON t.matkul_kode = m.kode 
                  LEFT JOIN dosen d ON m.dosen_id = d.id 
                  WHERE t.judul LIKE :kw OR m.nama LIKE :kw OR d.nama LIKE :kw";
    $stmt_count = $koneksi->prepare($sql_count);
    $stmt_count->bindValue(':kw', "%$keyword%", PDO::PARAM_STR);
    $stmt_count->execute();
    $total_data = $stmt_count->fetchColumn();

    $total_halaman = ceil($total_data / $batas);

    // 3. Query Data Utama dengan Multi-JOIN, LIMIT, dan OFFSET
    $sql  = "SELECT t.*, m.nama AS nama_matkul, d.nama AS nama_dosen 
            FROM tugas t 
            JOIN mata_kuliah m ON t.matkul_kode = m.kode 
            LEFT JOIN dosen d ON m.dosen_id = d.id 
            WHERE t.judul LIKE :kw OR m.nama LIKE :kw OR d.nama LIKE :kw 
            ORDER BY t.id DESC LIMIT :limit OFFSET :offset";
    $stmt = $koneksi->prepare($sql);
    $stmt->bindValue(':kw', "%$keyword%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $batas, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error mengambil data tugas: " . $e->getMessage());
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
          <h1 class="mb-0 fs-3">Daftar Tugas</h1>
        </div>
        <div class="col-sm-6 text-end">
          <a href="tambah-tugas.php" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Tugas
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
          <form method="GET" action="tugas.php" class="row g-2 align-items-center">
            <div class="col-auto">
              <input type="text" name="search" class="form-control" placeholder="Cari Judul / Matkul / Dosen..." value="<?= htmlspecialchars($keyword); ?>">
            </div>
            <div class="col-auto">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Cari
              </button>
              <?php if ($keyword !== ''): ?>
                <a href="tugas.php" class="btn btn-secondary">Reset</a>
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
                <th scope="col">Mata Kuliah</th>
                <th scope="col">Dosen</th>
                <th scope="col">Judul Tugas</th>
                <th scope="col">Deadline</th>
                <th scope="col">Status</th>
                <th scope="col">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($data)): ?>
                <?php foreach ($data as $row): ?>
                  <tr>
                    <th scope="row"><?= htmlspecialchars($row['id']) ?></th>
                    <td>
                      <strong>[<?= htmlspecialchars($row['matkul_kode']) ?>]</strong> 
                      <?= htmlspecialchars($row['nama_matkul']) ?>
                    </td>
                    <td><?= $row['nama_dosen'] ? htmlspecialchars($row['nama_dosen']) : '<em class="text-muted">-</em>' ?></td>
                    <td><?= htmlspecialchars($row['judul']) ?></td>
                    <td><?= $row['deadline'] ? date('d M Y', strtotime($row['deadline'])) : '-' ?></td>
                    <td>
                      <?php if ($row['status'] === 'Selesai'): ?>
                        <span class="badge bg-success">Selesai</span>
                      <?php else: ?>
                        <span class="badge bg-warning text-dark">Ditugaskan</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a class="btn btn-sm btn-warning" href="edit-tugas.php?id=<?= urlencode($row['id']) ?>">Edit</a>
                      <a class="btn btn-sm btn-danger ms-1" href="hapus-tugas.php?id=<?= urlencode($row['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center py-3">Belum ada data tugas.</td>
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