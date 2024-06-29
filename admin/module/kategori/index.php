<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Orang yang Sewa</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h4>Daftar Customer Sewa</h4>
            <br />
            <?php 
                if(isset($_GET['success'])){
            ?>
            <div class="alert alert-success">
                <p>Tambah Data Berhasil!</p>
            </div>
            <?php 
                }
            ?>
            <?php 
                if(isset($_GET['success-edit'])){
            ?>
            <div class="alert alert-success">
                <p>Update Data Berhasil!</p>
            </div>
            <?php 
                }
            ?>
            <?php 
                if(isset($_GET['remove'])){
            ?>
            <div class="alert alert-danger">
                <p>Hapus Data Berhasil!</p>
            </div>
            <?php 
                }
            ?>

            <div class="card card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr style="background:#DFF0D8;color:#333;">
                                <th>No.</th>
                                <th>Nama Penyewa</th>
                                <th>Alamat</th>
                                <th>Barang yang Disewa</th>
                                <th>Jumlah</th>
                                <th>Lama Sewa (Hari)</th>
                                <th>Tanggal Input</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Ambil data penyewaan dari database
                            $sql_sewa = "SELECT penyewa.nama AS nama_penyewa, penyewa.alamat, barang.nama_barang, sewa.jumlah, sewa.lama_sewa, sewa.tgl_input
                                         FROM penyewa
                                         JOIN sewa ON penyewa.id_penyewa = sewa.id_penyewa
                                         JOIN barang ON sewa.kode_barang = barang.kode_barang
                                         ORDER BY sewa.tgl_input DESC";
                            $stmt_sewa = $config->prepare($sql_sewa);
                            $stmt_sewa->execute();
                            $hasil_sewa = $stmt_sewa->fetchAll(PDO::FETCH_ASSOC);
                            
                            $no = 1;
                            foreach($hasil_sewa as $data_sewa){
                            ?>
                            <tr>
                                <td><?php echo $no;?></td>
                                <td><?php echo $data_sewa['nama_penyewa'];?></td>
                                <td><?php echo $data_sewa['alamat'];?></td>
                                <td><?php echo $data_sewa['nama_barang'];?></td>
                                <td><?php echo $data_sewa['jumlah'];?></td>
                                <td><?php echo $data_sewa['lama_sewa'];?></td>
                                <td><?php echo $data_sewa['tgl_input'];?></td>
                                <td>
                                    <!-- Tambahkan tombol edit dan hapus sesuai kebutuhan -->
                                    <!-- Contoh tombol edit -->
                                    <!-- <a href="edit.php?id=<?php echo $data_sewa['id'];?>"><button class="btn btn-warning">Edit</button></a> -->
                                    
                                    <!-- Contoh tombol hapus dengan konfirmasi menggunakan JavaScript -->
                                    <!-- <a href="hapus.php?id=<?php echo $data_sewa['id'];?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');"><button class="btn btn-danger">Hapus</button></a> -->
                                </td>
                            </tr>
                            <?php 
                                $no++;
                            } 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
