<?php require_once("../../controller/master-data.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Tambah Galeri";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Galeri</li>
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
            <form action="" method="post" enctype="multipart/form-data">
              <div class="mb-3">
                <label for="objek_wisata_id" class="form-label">Objek Wisata</label>
                <select name="objek_wisata_id" class="form-select" id="objek_wisata_id" required>
                  <option value="">Pilih objek wisata</option>
                  <?php if ($views_objek_wisata instanceof mysqli_result) {
                    foreach ($views_objek_wisata as $objek_wisata) { ?>
                      <option value="<?= $objek_wisata['id'] ?>"><?= htmlspecialchars($objek_wisata['nama_wisata']) ?></option>
                  <?php }
                  } ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="judul" class="form-label">Judul</label>
                <input type="text" name="judul" class="form-control" id="judul" placeholder="Masukan judul galeri">
              </div>
              <div class="mb-3">
                <label for="file_path" class="form-label">Gambar</label>
                <input type="file" name="file_path" class="form-control" id="file_path" accept="image/*" required>
                <small class="form-text text-muted">Format yang diizinkan: JPG, JPEG, PNG.</small>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="galeri" class="btn btn-success">Kembali</a>
                <button type="submit" name="add_galeri" class="btn btn-primary">Tambah</button>
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
