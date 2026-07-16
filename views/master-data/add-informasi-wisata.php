<?php require_once("../../controller/master-data.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Tambah Informasi Wisata";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Informasi Wisata</li>
        <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></li>
      </ul>
    </div>
  </div>
  <!-- [ page-header ] end -->

  <!-- [ Main Content ] start -->
  <div class="main-content">
    <div class="row">
      <div class="col-lg-8">
        <div class="card stretch stretch-full">
          <div class="card-body">
            <form action="" method="post">
              <div class="mb-3">
                <label for="judul" class="form-label">Judul</label>
                <input type="text" name="judul" class="form-control" id="judul" placeholder="Masukan judul informasi wisata" required>
              </div>
              <div class="mb-3">
                <label for="konten" class="form-label">Konten</label>
                <textarea name="konten" class="form-control" id="konten" rows="7" placeholder="Masukan konten informasi wisata"></textarea>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="informasi-wisata" class="btn btn-success">Kembali</a>
                <button type="submit" name="add_informasi_wisata" class="btn btn-primary">Tambah</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->

</div>

<?php require_once("../../templates/views_bottom.php") ?>
