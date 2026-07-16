<?php require_once("../../controller/master-data.php");
if (!isset($_GET["p"])) {
  header("Location: galeri");
  exit();
}

$id = valid($conn, $_GET["p"]);
$pull_data = "SELECT * FROM galeri WHERE id='$id'";
$store_data = mysqli_query($conn, $pull_data);
$view_data = mysqli_fetch_assoc($store_data);

if (!$view_data) {
  header("Location: galeri");
  exit();
}

$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Ubah Galeri";
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
        <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] . ' ' . htmlspecialchars($view_data["judul"]) ?></li>
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
              <input type="hidden" name="id" value="<?= $view_data['id'] ?>">
              <input type="hidden" name="file_path_old" value="<?= htmlspecialchars($view_data['file_path']) ?>">
              <div class="mb-3">
                <label for="objek_wisata_id" class="form-label">Objek Wisata</label>
                <select name="objek_wisata_id" class="form-select" id="objek_wisata_id" required>
                  <option value="">Pilih objek wisata</option>
                  <?php if ($views_objek_wisata instanceof mysqli_result) {
                    foreach ($views_objek_wisata as $objek_wisata) { ?>
                  <option value="<?= $objek_wisata['id'] ?>" <?= ($view_data['objek_wisata_id'] == $objek_wisata['id']) ? 'selected' : '' ?>><?= htmlspecialchars($objek_wisata['nama_wisata']) ?></option>
                  <?php }
                  } ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="judul" class="form-label">Judul</label>
                <input type="text" name="judul" class="form-control" id="judul" value="<?= htmlspecialchars($view_data['judul']) ?>" placeholder="Masukan judul galeri">
              </div>
              <div class="mb-3">
                <label for="file_path" class="form-label">Gambar</label>
                <?php if (!empty($view_data['file_path'])) { ?>
                <div class="mb-2">
                  <img src="<?= $baseURL . htmlspecialchars($view_data['file_path']) ?>" alt="<?= htmlspecialchars($view_data['judul']) ?>" class="rounded" style="width: 180px; height: 110px; object-fit: cover;">
                </div>
                <?php } ?>
                <input type="file" name="file_path" class="form-control" id="file_path" accept="image/*">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti gambar. Format yang diizinkan: JPG, JPEG, PNG.</small>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="galeri" class="btn btn-success">Kembali</a>
                <button type="submit" name="edit_galeri" class="btn btn-warning">Ubah</button>
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
