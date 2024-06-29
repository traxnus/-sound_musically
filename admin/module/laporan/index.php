<?php
include 'config.php'; // Sertakan file koneksi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $barang = $_POST['barang'];
    $jumlah_barang = $_POST['jumlah_barang'];
    $lama_sewa = $_POST['lama_sewa'];

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
            $stmt_update_stok->execute([':kode_barang' => $kode_barang, ':jumlah' => $jumlah]);

            // Masukkan data penyewaan ke dalam tabel sewa
            $sql_sewa = "INSERT INTO sewa (id_penyewa, kode_barang, jumlah, lama_sewa) VALUES (:id_penyewa, :kode_barang, :jumlah, :lama_sewa)";
            $stmt_sewa = $config->prepare($sql_sewa);
            $stmt_sewa->execute([':id_penyewa' => $id_penyewa, ':kode_barang' => $kode_barang, ':jumlah' => $jumlah, ':lama_sewa' => $lama_sewa]);
        }

        // Commit transaksi
        $config->commit();

        echo "Transaksi berhasil!";
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $config->rollBack();
        echo "Transaksi gagal: " . $e->getMessage();
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
                    <form method="post" action="">
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
                                                    echo "<option value='{$row['kode_barang']}'>{$row['nama_barang']} (Stok: {$row['stok']})</option>";
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
function addItem() {
    const listBarang = document.getElementById('list-barang');
    const newItem = document.createElement('div');
    newItem.className = 'input-group mb-3';
    newItem.innerHTML = `
        <select name="barang[]" class="form-control">
            <?php
            foreach ($data_barang as $row) {
                echo "<option value='{$row['kode_barang']}'>{$row['nama_barang']} (Stok: {$row['stok']})</option>";
            }
            ?>
        </select>
        <input type="number" name="jumlah_barang[]" class="form-control" placeholder="Jumlah barang" required>
        <div class="input-group-append">
            <button class="btn btn-danger" type="button" onclick="removeItem(this)">Hapus</button>
        </div>
    `;
    listBarang.appendChild(newItem);
}

function removeItem(button) {
    button.closest('.input-group').remove();
}
</script>

</body>
</html>
