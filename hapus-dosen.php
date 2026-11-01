<?php
require_once 'koneksi.php';

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $sql = "DELETE FROM dosen WHERE id = :id";
        $stmt = $koneksi->prepare($sql);
        $stmt->execute([':id' => $id]);

        header("Location: dosen.php");
        exit;
    } catch (PDOException $e) {
        die("Gagal menghapus data dosen: " . $e->getMessage());
    }
} else {
    header("Location: dosen.php");
    exit;
}
?>