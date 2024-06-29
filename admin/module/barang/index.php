<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Barang</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Data Barang</h2>
    <a href="insert_barang.php" class="btn btn-primary btn-md mr-2">
        <i class="fa fa-plus"></i> Insert Data
    </a>
    <a href="index.php?page=barang&stok=yes" class="btn btn-warning btn-md mr-2">
        <i class="fa fa-list"></i> Sortir Stok Kurang
    </a>
    <a href="index.php?page=barang" class="btn btn-success btn-md">
        <i class="fa fa-refresh"></i> Refresh Data
    </a>
    <br /><br />

    <!-- View Barang -->
    <div class="card card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" id="example1">
                <thead>
                    <tr style="background:#DFF0D8;color:#333;">
                        <th>No.</th>
                        <th>ID Barang</th>
                        <th>Kategori</th>
                        <th>Nama Barang</th>
                        <th>Merk</th>
                        <th>Stok</th>
                        <th>Biaya Sewa</th>
                    </tr>
                </thead>
                <tbody>
                <?php
include 'config.php'; // Sertakan file koneksi

// Query SQL untuk mengambil data barang dari tabel
$sql = "SELECT * FROM barang";
$row = $config->prepare($sql);
$row->execute();
$barang = $row->fetchAll(PDO::FETCH_ASSOC);
$no = 1; // Inisialisasi nomor urutan

if ($barang) {
    // Jika ada data, tampilkan dalam tabel
    foreach ($barang as $row) { // Perulangan foreach untuk setiap baris barang
?>
        <tr>
            <td><?php echo $no;?></td>
            <td><?php echo $row['kode_barang'];?></td>
            <td><?php echo $row['kode_kategori'];?></td>
            <td><?php echo $row['nama_barang'];?></td>
            <td><?php echo $row['merk'];?></td>
            <td><?php echo $row['stok'];?></td>
            <td><?php echo $row['biaya_sewa'];?></td>
        </tr>
<?php 
        $no++; // Increment nomor urutan
    }
} else {
    // Jika tidak ada data barang yang ditemukan, tampilkan pesan kosong
    echo "<tr><td colspan='7'>Tidak ada data barang.</td></tr>";
}

// Tutup koneksi database
$conn->close();
?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5">Total</td>
                        <th colspan="2" style="background:#ddd"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal untuk tambah data barang -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" style="border-radius:0px;">
            <div class="modal-header" style="background:#285c64;color:#fff;">
                <h5 class="modal-title"><i class="fa fa-plus"></i> Tambah Barang</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="submit.php" method="POST">
                <div class="modal-body">
                    <table class="table table-striped bordered">
                        <tr>
                            <td>ID Barang</td>
                            <td><input type="text" required class="form-control" name="kode_barang"></td>
                        </tr>
                        <tr>
                            <td>Kategori</td>
                            <td><input type="text" required class="form-control" name="kode_kategori"></td>
                        </tr>
                        <tr>
                            <td>Nama Barang</td>
                            <td><input type="text" required class="form-control" name="nama_barang"></td>
                        </tr>
                        <tr>
                            <td>Merk Barang</td>
                            <td><input type="text" required class="form-control" name="merk"></td>
                        </tr>
                        <tr>
                            <td>Biaya Sewa</td>
                            <td><input type="number" required class="form-control" name="biaya_sewa"></td>
                        </tr>
                        <tr>
                            <td>Stok</td>
                            <td><input type="number" required class="form-control" name="stok"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Insert Data</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // jQuery
    $(document).ready(function(){
        $("#myModal").modal('show');
    });

    // JavaScript murni
    document.addEventListener("DOMContentLoaded", function(){
        var myModal = new bootstrap.Modal(document.getElementById('myModal'), {});
        myModal.show();
    });
</script>

</body>
</html>
