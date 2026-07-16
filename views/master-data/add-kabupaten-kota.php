<?php require_once("../../controller/master-data.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Tambah Kabupaten/Kota";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Kabupaten/Kota</li>
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
                <label for="nama" class="form-label">Nama Kabupaten/Kota</label>
                <input type="text" name="nama" class="form-control" id="nama" placeholder="Masukan nama kabupaten atau kota" required>
              </div>
              <div class="mb-3">
                <label for="jenis" class="form-label">Jenis</label>
                <select name="jenis" class="form-select" id="jenis" required>
                  <option value="">Pilih jenis wilayah</option>
                  <option value="Kabupaten">Kabupaten</option>
                  <option value="Kota">Kota</option>
                </select>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="kabupaten-kota" class="btn btn-success">Kembali</a>
                <button type="submit" name="add_kabupaten_kota" class="btn btn-primary">Tambah</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once("../../templates/views_bottom.php") ?>
