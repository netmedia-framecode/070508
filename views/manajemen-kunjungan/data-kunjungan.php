<?php require_once("../../controller/manajemen-kunjungan.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Data Kunjungan";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Manajemen Kunjungan</li>
        <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></li>
      </ul>
    </div>
  </div>
  <!-- [ page-header ] end -->

  <!-- [ Main Content ] start -->
  <div class="main-content">
    <div class="row">
      <div class="col-lg-12">
        <div class="card stretch stretch-full">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover" id="dataTable">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Kode QR</th>
                    <th class="text-center">Kode Booking</th>
                    <th class="text-center">Wisatawan</th>
                    <th class="text-center">Objek Wisata</th>
                    <th class="text-center">Tanggal Kunjungan</th>
                    <th class="text-center">Petugas</th>
                    <th class="text-center">Waktu Scan</th>
                    <th class="text-center">Keterangan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($views_data_kunjungan instanceof mysqli_result) {
                    foreach ($views_data_kunjungan as $key => $data) {
                      $tgl_kunjungan = $data['tgl_kunjungan'] ? date('d-m-Y', strtotime($data['tgl_kunjungan'])) : '-';
                      $waktu_kunjungan = $data['waktu_kunjungan'] ? date('d-m-Y H:i', strtotime($data['waktu_kunjungan'])) : '-';
                  ?>
                  <tr class="single-item">
                    <td class="text-center"><?= $key + 1 ?></td>
                    <td><?= htmlspecialchars($data['kode_qr'] ?: '-') ?></td>
                    <td><?= htmlspecialchars($data['kode_booking'] ?: '-') ?></td>
                    <td>
                      <?= htmlspecialchars($data['nama_wisatawan'] ?: '-') ?>
                      <div class="fs-11 text-muted"><?= htmlspecialchars($data['email'] ?: '-') ?></div>
                    </td>
                    <td><?= htmlspecialchars($data['nama_wisata'] ?: '-') ?></td>
                    <td class="text-center"><?= $tgl_kunjungan ?></td>
                    <td><?= htmlspecialchars($data['nama_petugas'] ?: '-') ?></td>
                    <td class="text-center"><?= $waktu_kunjungan ?></td>
                    <td><?= htmlspecialchars($data['keterangan'] ?: '-') ?></td>
                  </tr>
                  <?php }
                  } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->

</div>

<?php require_once("../../templates/views_bottom.php") ?>
