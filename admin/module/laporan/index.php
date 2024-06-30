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
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $config->rollBack();
        echo "<script>alert('Transaksi gagal: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Penyewaan Barang</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h4>Formulir Penyewaan Barang</h4>
            <br />
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mt-2">Isi Informasi Penyewa dan Barang yang Disewa</h5>
                </div>
                <div class="card-body p-0">
                    <form id="form-sewa" method="post" action="submit.php">
                        <table class="table table-striped">
                            <tr>
                                <th>Nama Penyewa</th>
                                <td><input type="text" name="nama" class="form-control" required></td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td><textarea name="alamat" class="form-control" rows="3" required></textarea></td>
                            </tr>
                            <tr>
                                <th>Barang yang Disewa</th>
                                <td>
                                    <div id="list-barang">
                                        <div class="input-group mb-3">
                                            <select name="barang[]" class="form-control">
                                                <?php
                                                // Ambil data barang dari database
                                                $sql_barang = "SELECT * FROM barang";
                                                $stmt_barang = $config->prepare($sql_barang);
                                                $stmt_barang->execute();
                                                $data_barang = $stmt_barang->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($data_barang as $row) {
                                                    echo "<option value='{$row['kode_barang']}' data-harga='{$row['biaya_sewa']}'>{$row['nama_barang']} (Stok: {$row['stok']})</option>";
                                                }
                                                ?>
                                            </select>
                                            <input type="number" name="jumlah_barang[]" class="form-control" placeholder="Jumlah barang" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-success" type="button" onclick="addItem()">Tambah</button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Lama Sewa (Hari)</th>
                                <td><input type="number" name="lama_sewa" class="form-control" required></td>
                            </tr>
                            <tr>
                                <th>Harga Sewa per Hari</th>
                                <td><input type="number" name="harga_sewa_per_hari" class="form-control" required></td>
                            </tr>
                            <tr>
                                <th>Total Harga Sewa</th>
                                <td><span id="total_harga">0</span></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                    <button class="btn btn-success" type="reset">Reset</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-sewa');
    const hargaSewaPerHariInputs = document.querySelectorAll('select[name="barang[]"]');
    const jumlahBarangInputs = document.querySelectorAll('input[name="jumlah_barang[]"]');
    const lamaSewaInput = document.querySelector('input[name="lama_sewa"]');
    const inputHargaSewa = document.querySelector('input[name="harga_sewa_per_hari"]');
    const totalHargaSpan = document.getElementById('total_harga');

    // Event listener untuk setiap input barang
    hargaSewaPerHariInputs.forEach(select => {
        select.addEventListener('change', function() {
            updateHarga(this);
            hitungTotalHarga();
        });
    });

    // Event listener untuk setiap input jumlah barang
    jumlahBarangInputs.forEach(input => {
        input.addEventListener('input', function() {
            hitungTotalHarga();
        });
    });

    // Event listener untuk input lama sewa
    lamaSewaInput.addEventListener('input', function() {
        hitungTotalHarga();
    });

    // Fungsi untuk mengupdate harga sewa per hari berdasarkan pilihan barang
    function updateHarga(select) {
        const hargaSewaPerHari = select.options[select.selectedIndex].getAttribute('data-harga');
        inputHargaSewa.value = hargaSewaPerHari;
    }

    // Fungsi untuk menghitung total harga sewa dan memformat ke Rupiah
    function hitungTotalHarga() {
        let totalHarga = 0;

        // Loop untuk setiap item barang yang disewa
        hargaSewaPerHariInputs.forEach((select, index) => {
            const jumlahBarang = jumlahBarangInputs[index].value;
            const hargaSewaPerHari = select.options[select.selectedIndex].getAttribute('data-harga');
            const lamaSewa = lamaSewaInput.value;

            // Hitung harga sewa barang ini
            const hargaSewaBarang = hargaSewaPerHari * jumlahBarang * lamaSewa;

            // Tambahkan harga sewa barang ke total harga
            totalHarga += hargaSewaBarang;
        });

        // Format total harga ke dalam Rupiah dengan menggunakan fungsi formatRupiah
        totalHargaSpan.textContent = formatRupiah(totalHarga);
    }

    // Fungsi untuk format angka ke dalam format Rupiah
    function formatRupiah(angka) {
        var number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return 'Rp ' + rupiah;
    }

    // Panggil fungsi update harga awal saat halaman dimuat
    updateHarga(hargaSewaPerHariInputs[0]); // Panggil untuk elemen pertama

    // Submit form secara asynchronous
    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(form);
        fetch(form.action, {
            method: form.method,
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.includes('Transaksi gagal')) {
                alert(data);
            } else {
                // Jika berhasil, tampilkan total harga dari response
                totalHargaSpan.textContent = formatRupiah(data.match(/\d+/)[0]); // Ambil angka dari pesan berhasil dan format ke Rupiah
                alert('Transaksi berhasil! Total harga sewa: ' + formatRupiah(data.match(/\d+/)[0]));
            }
        })
        .catch(error => console.error('Error:', error));
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-sewa');

    // Submit form secara asynchronous
    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(form);
        fetch(form.action, {
            method: form.method,
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (!data.includes('Transaksi gagal')) {
                // Jika berhasil, arahkan kembali ke index.php
                window.location.href = 'index.php';
            } else {
                // Jika gagal, tampilkan pesan kesalahan
                alert(data);
            }
        })
        .catch(error => console.error('Error:', error));
    });
});


</script>



</body>
</html>
