<h3>Dashboard</h3>
<br/>
<?php 
    $sql = "SELECT * FROM barang WHERE stok <= 3";
    $row = $config->prepare($sql);
    $row->execute();
    $r = $row->rowCount();
    if ($r > 0) {
        echo "
        <div class='alert alert-warning'>
            <span class='glyphicon glyphicon-info-sign'></span> Ada <span style='color:red'>$r</span> barang yang Stok tersisa sudah kurang dari 3 items. Silahkan pesan lagi !!
            <span class='pull-right'><a href='index.php?page=barang&stok=yes'>Tabel Barang <i class='fa fa-angle-double-right'></i></a></span>
        </div>
        ";  
    }
?>

<?php
    // Ambil jumlah data dari tabel sewa
    $sql_jumlah_sewa = "SELECT COUNT(*) AS total_sewa FROM sewa";
    $stmt_jumlah_sewa = $config->prepare($sql_jumlah_sewa);
    $stmt_jumlah_sewa->execute();
    $jumlah_sewa = $stmt_jumlah_sewa->fetch(PDO::FETCH_ASSOC);
    $jumlah_sewa_value = $jumlah_sewa['total_sewa'];
?>
<?php
    // Query untuk mengambil jumlah data dari tabel penyewa
    $sql_jumlah_penyewa = "SELECT COUNT(*) AS jumlah_penyewa FROM penyewa";
    $stmt_jumlah_penyewa = $config->prepare($sql_jumlah_penyewa);
    $stmt_jumlah_penyewa->execute();
    $jumlah_penyewa = $stmt_jumlah_penyewa->fetch(PDO::FETCH_ASSOC);
    $jumlah_penyewa_value = $jumlah_penyewa['jumlah_penyewa'];
?>


<?php $hasil_barang = $lihat->barang_row();?>
<?php $hasil_kategori = $lihat->kategori_row();?>
<?php $stok = $lihat->barang_stok_row();?>
<?php $jual = $lihat->jual_row();?>
<div class="row">
    <!-- STATUS cards -->
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="pt-2"><i class="fas fa-cubes"></i> Nama Barang</h6>
            </div>
            <div class="card-body">
                <center>
                    <h1><?php echo number_format($hasil_barang);?></h1>
                </center>
            </div>
            <div class="card-footer">
                <a href='index.php?page=barang'>Tabel Barang <i class='fa fa-angle-double-right'></i></a>
            </div>
        </div>
        <!--/card -->
    </div><!-- /col-md-3-->

    <!-- STATUS cards -->
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="pt-2"><i class="fas fa-chart-bar"></i> Stok Barang</h6>
            </div>
            <div class="card-body">
                <center>
                    <h1><?php echo number_format($stok['jml']);?></h1>
                </center>
            </div>
            <div class="card-footer">
                <a href='index.php?page=barang'>Tabel Barang <i class='fa fa-angle-double-right'></i></a>
            </div>
        </div>
        <!--/card -->
    </div><!-- /col-md-3-->

    <!-- STATUS cards -->
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="pt-2"><i class="fas fa-upload"></i>Total Sewa </h6>
            </div>
            <div class="card-body">
                <center>
                    <h1><?php echo number_format($jumlah_sewa_value);?></h1>
                </center>
            </div>
            <div class="card-footer">
                <a href='index.php?page=laporan'>Form Penyewa <i class='fa fa-angle-double-right'></i></a>
            </div>
        </div>
        <!--/card -->
    </div><!-- /col-md-3-->

    <!-- STATUS cards -->
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="pt-2"><i class="fa fa-bookmark"></i> Transaksi Sewa</h6>
            </div>
            <div class="card-body">
                <center>
                    <h1><?php echo number_format($jumlah_penyewa_value);?></h1>
                </center>
            </div>
            <div class="card-footer">
                <a href='index.php?page=kategori'>Tabel Penyewa <i class='fa fa-angle-double-right'></i></a>
            </div>
        </div>
        <!--/card -->
    </div><!-- /col-md-3-->
</div><!-- /row -->
