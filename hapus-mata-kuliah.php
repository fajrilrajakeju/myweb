<?php
require_once 'koneksi.php';

// Ambil kode mata kuliah dari parameter URL
$kode = $_GET['kode'] ?? null;

if ($kode) {
    try {
        // Prepared Statement untuk DELETE Data
        $sql = "DELETE FROM mata_kuliah WHERE kode = :kode";
        $stmt = $koneksi->prepare($sql);
        $stmt->execute([':kode' => $kode]);

        // Redirect kembali ke halaman daftar mata kuliah setelah berhasil
        header("Location: mata-kuliah.php");
        exit;
    } catch (PDOException $e) {
        die("Gagal menghapus data: " . $e->getMessage());
    }
} else {
    // Jika tidak ada parameter kode, kembalikan ke halaman mata kuliah
    header("Location: mata-kuliah.php");
    exit;
}
?>