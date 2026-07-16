<?php require_once("../../controller/master-data.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Tambah Kecamatan";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Kecamatan</li>
        <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></li>
      </ul>
    </div>
  </div>

  <div class="main-content">
    <div class="row">
      <div class="col-lg-8">
        <div class="card stretch stretch-full">
          <div class="card-body">
            <form action="" method="post">
              <div class="mb-3">
                <label for="kabupaten_kota_id" class="form-label">Kabupaten/Kota</label>
                <select name="kabupaten_kota_id" class="form-select" id="kabupaten_kota_id" required>
                  <option value="">Pilih kabupaten/kota</option>
                  <?php if ($views_kabupaten_kota instanceof mysqli_result) {
                    foreach ($views_kabupaten_kota as $kabupaten_kota) { ?>
                  <option value="<?= $kabupaten_kota['id'] ?>"><?= htmlspecialchars($kabupaten_kota['jenis'] . ' ' . $kabupaten_kota['nama']) ?></option>
                  <?php }
                  } ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="nama" class="form-label">Nama Kecamatan</label>
                <input type="text" name="nama" class="form-control" id="nama" placeholder="Masukan nama kecamatan" required>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="kecamatan" class="btn btn-success">Kembali</a>
                <button type="submit" name="add_kecamatan" class="btn btn-primary">Tambah</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once("../../templates/views_bottom.php") ?>
