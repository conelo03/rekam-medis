<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $page = "Riwayat Rawat Jalan";
  session_start();
  include 'auth/connect.php';
  include "part/head.php";
  date_default_timezone_set("Asia/Jakarta");
  $date = date('Y-m-d');
  if (isset($_POST['set_tanggal'])) {
    $date = $_POST['tgl'];
  }

  $cek = mysqli_query($conn, "SELECT SUM(biaya_pengobatan) as biaya FROM riwayat_penyakit WHERE tgl='$date'");
  $rp = mysqli_fetch_array($cek);

  ?>
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>

      <?php
      include 'part/navbar.php';
      include 'part/sidebar.php';
      include 'part_func/umur.php';
      include 'part_func/tgl_ind.php';
      ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Riwayat Rawat Jalan</h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="#">Data Riwayat Rawat Jalan</a></div>
              <div class="breadcrumb-item">Detail Pasien</div>
            </div>
          </div>

          <div class="section-body">

            <div class="section-body">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header">
                      <h4>Riwayat Rawat Jalan per Hari</h4>
                    </div>
                    <div class="card-body">
                      <form action="#" method="post">
                        <div class="row">
                          <div class="col-md-6 form-group">
                            <label>Pilih Tanggal</label>
                            <input type="text" class="form-control datepicker" name="tgl" required="" value="<?php echo $date; ?>">
                          </div>
                          <div class="col-md-6 form-group">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-primary" name="set_tanggal"><i class="fa fa-filter"></i> Filter</button>
                          </div>
                        </div>
                      </form>
                      <div class="row mb-4">
                        <div class="col-4"><h6>Total Biaya</h6></div>
                        <div class="col-8">: <?= 'Rp '.number_format($rp['biaya'], 2, ',', '.'); ?></div>
                      </div>
                      <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="table-1">
                          <thead>
                            <tr>
                              <th>Tanggal Berobat</th>
                              <th>Penyakit</th>
                              <th>Diagnosa</th>
                              <th>Obat</th>
                              <th>Biaya</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $sql = mysqli_query($conn, "SELECT * FROM riwayat_penyakit WHERE tgl = '$date'");
                            $i = 0;
                            while ($row = mysqli_fetch_array($sql)) {
                              $idpenyakit = $row['id'];
                              $idpasien = $row['id_pasien'];
                            ?>
                              <tr>
                                <td><?php echo ucwords(tgl_indo($row['tgl'])); ?></td>
                                <td><?php echo ucwords($row['penyakit']); ?></td>
                                <td><?php
                                    echo $row['diagnosa']." - ";
                                    $status = substr($row['id_rawatinap'], 0, 3);
                                    $idrawatinap = substr($row['id_rawatinap'], 3);
                                    if ($row['id_rawatinap'] == '0') {
                                      echo 'Pasien tidak membutuhkan Rawat Inap';
                                    } else {
                                      if ($status == "tmp") {
                                        $ruang = mysqli_query($conn, "SELECT * FROM ruang_inap WHERE id='$idrawatinap'");
                                        $showruang = mysqli_fetch_array($ruang);
                                        echo "<a href='ruangan.php' title='Detail Ruang Rawat Inap Pasien' data-toggle='tooltip'><i class='fas fa-info-circle text-info'></i> Pasien masih dirawat di ruang " . $showruang['nama_ruang'] . " sejak tgl " . tgl_indo($showruang['tgl_masuk']) . "</a>";
                                      } else {
                                        $riw1 = mysqli_query($conn, "SELECT * FROM riwayat_rawatinap WHERE id='$idrawatinap'");
                                        $riwayatinap = mysqli_fetch_array($riw1);
                                        echo "<a href='riwayat_inap.php' title='Riwayat Rawat Inap Pasien' data-toggle='tooltip'><i class='fas fa-info-circle text-info'></i> Pasien pernah dirawat pada tgl " . tgl_indo($riwayatinap['2']) . ' - ' . tgl_indo($riwayatinap['3']) . "</a>";
                                      }
                                    } ?>
                                </td>
                                <td>
                                  <?php
                                  $obat2an = mysqli_query($conn, "SELECT * FROM riwayat_obat WHERE id_penyakit='$idpenyakit' AND id_pasien='$idpasien'");
                                  $jumobat = mysqli_num_rows($obat2an);
                                  if ($jumobat == 0) {
                                    echo "Tidak ada obat yang diberikan";
                                  } else {
                                    $count = 0;
                                    while ($showobat = mysqli_fetch_array($obat2an)) {
                                      $idobat = $showobat['id_obat'];
                                      $obatlagi = mysqli_query($conn, "SELECT * FROM obat WHERE id='$idobat'");
                                      $namaobat = mysqli_fetch_array($obatlagi);
                                      echo $str = ucwords($namaobat['nama_obat']);
                                      $count = $count + 1;

                                      if ($count < $jumobat) {
                                        echo ", ";
                                      }
                                    }
                                  }
                                  ?>
                                </td>
                                <td>Rp. <?php echo number_format($row['biaya_pengobatan'], 0, ".", "."); ?></td>
                              </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

        </section>
      </div>

      <?php include 'part/footer.php'; ?>
    </div>
  </div>
  <?php include "part/all-js.php"; ?>
</body>

</html>