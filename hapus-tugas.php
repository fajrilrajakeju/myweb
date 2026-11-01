<?php
require_once 'koneksi.php';

// Ambil ID tugas dari parameter URL
$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // Prepared Statement untuk DELETE Data
        $sql = "DELETE FROM tugas WHERE id = :id";
        $stmt = $koneksi->prepare($sql);
        $stmt->execute([':id' => $id]);

        // Redirect kembali ke halaman daftar tugas setelah berhasil
        header("Location: tugas.php");
        exit;
    } catch (PDOException $e) {
        die("Gagal menghapus data tugas: " . $e->getMessage());
    }
} else {
    // Jika tidak ada parameter ID, kembalikan ke halaman tugas
    header("Location: tugas.php");
    exit;
}
?>