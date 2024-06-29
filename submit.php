<?php
date_default_timezone_set("Asia/Jakarta");
error_reporting(0);

// sesuaikan dengan server anda
$servername = 'localhost'; // host server
$username = 'root';  // username server
$password = ''; // password server, kalau pakai xampp kosongin saja
$dbname = 'db_toko'; // nama database anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Mengambil data dari form
$kode_barang = $_POST['kode_barang'];
$kode_kategori = $_POST['Kode_Kategori'];
$nama_barang = $_POST['nama_barang'];
$merk = $_POST['merk'];
$biaya_sewa = $_POST['biaya_sewa'];
$stok = $_POST['stok'];
$tgl_sewa = $_POST['tgl_sewa'];
$tgl_selesai = $_POST['tgl_selesai'];

// SQL untuk memasukkan data ke tabel barang
$sql = "INSERT INTO barang (kode_barang, kode_kategori, nama_barang, merk, biaya_sewa, stok, tgl_sewa, tgl_selesai) 
VALUES ('$kode_barang', '$kode_kategori', '$nama_barang', '$merk', '$biaya_sewa', '$stok', '$tgl_sewa', '$tgl_selesai')";

if ($conn->query($sql) === TRUE) {
    // Redirect ke halaman index.php setelah data berhasil disimpan
    header("Location: index.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>