<?php
include 'config.php'; // Sertakan file koneksi database

$total_harga = 0; // Variabel untuk menyimpan total harga, diinisialisasi dengan 0

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $barang = $_POST['barang'];
    $jumlah_barang = $_POST['jumlah_barang'];
    $lama_sewa = $_POST['lama_sewa'];
    $harga_sewa_per_hari = $_POST['harga_sewa_per_hari']; // Tambahkan input untuk harga sewa per hari

    // Mulai transaksi
    $config->beginTransaction();

    try {
        // Masukkan data penyewa ke dalam tabel penyewa
        $sql_penyewa = "INSERT INTO penyewa (nama, alamat) VALUES (:nama, :alamat)";
        $stmt_penyewa = $config->prepare($sql_penyewa);
        $stmt_penyewa->execute([':nama' => $nama, ':alamat' => $alamat]);

        // Dapatkan ID penyewa terakhir
        $id_penyewa = $config->lastInsertId();

        // Masukkan data penyewaan barang
        foreach ($barang as $index => $kode_barang) {
            $jumlah = $jumlah_barang[$index];

            // Kurangi stok barang
            $sql_update_stok = "UPDATE barang SET stok = stok - :jumlah WHERE kode_barang = :kode_barang";
            $stmt_update_stok = $config->prepare($sql_update_stok);
            $stmt_update_stok->execute([':jumlah' => $jumlah, ':kode_barang' => $kode_barang]);

            // Hitung harga sewa untuk barang ini
            $harga_sewa_barang = $harga_sewa_per_hari * $jumlah * $lama_sewa;

            // Tambahkan harga sewa barang ini ke total harga
            $total_harga += $harga_sewa_barang;

            // Masukkan data penyewaan ke dalam tabel sewa
            $sql_sewa = "INSERT INTO sewa (id_penyewa, kode_barang, jumlah, lama_sewa, harga_sewa) VALUES (:id_penyewa, :kode_barang, :jumlah, :lama_sewa, :harga_sewa)";
            $stmt_sewa = $config->prepare($sql_sewa);
            $stmt_sewa->execute([':id_penyewa' => $id_penyewa, ':kode_barang' => $kode_barang, ':jumlah' => $jumlah, ':lama_sewa' => $lama_sewa, ':harga_sewa' => $harga_sewa_barang]);
        }

        // Commit transaksi
        $config->commit();

        // Kirim total harga sebagai respons
        echo $total_harga;
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $config->rollBack();
        echo "Transaksi gagal: " . $e->getMessage();
    }
}

?>
