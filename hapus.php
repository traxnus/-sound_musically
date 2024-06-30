<?php
// Sesuaikan dengan konfigurasi koneksi database Anda
include 'config.php';

// Pastikan parameter id_penyewa ada dan merupakan bilangan bulat positif
if (isset($_GET['id_penyewa']) && is_numeric($_GET['id_penyewa']) && $_GET['id_penyewa'] > 0) {
    $id_penyewa = $_GET['id_penyewa'];

    try {
        // Mulai transaksi untuk keamanan
        $config->beginTransaction();

        // Hapus data dari tabel sewa berdasarkan id_penyewa
        $sql_delete_sewa = "DELETE FROM sewa WHERE id_penyewa = :id_penyewa";
        $stmt_delete_sewa = $config->prepare($sql_delete_sewa);
        $stmt_delete_sewa->execute([':id_penyewa' => $id_penyewa]);

        // Hapus data dari tabel penyewa berdasarkan id_penyewa
        $sql_delete_penyewa = "DELETE FROM penyewa WHERE id_penyewa = :id_penyewa";
        $stmt_delete_penyewa = $config->prepare($sql_delete_penyewa);
        $stmt_delete_penyewa->execute([':id_penyewa' => $id_penyewa]);

        // Commit transaksi jika berhasil
        $config->commit();

        // Redirect kembali ke halaman utama dengan status remove=success
        header("Location: index.php?remove=success");
        exit();
    } catch (PDOException $e) {
        // Rollback transaksi jika terjadi kesalahan
        $config->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    // Redirect kembali ke halaman utama jika parameter id_penyewa tidak sesuai
    header("Location: index.php");
    exit();
}
?>
