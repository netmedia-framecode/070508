<?php require_once("../../controller/master-data.php");
if (!isset($_GET["p"])) {
  header("Location: desa");
  exit();
}

$id = valid($conn, $_GET["p"]);
$pull_data = "SELECT * FROM desa WHERE id='$id'";
$store_data = mysqli_query($conn, $pull_data);
$view_data = $store_data ? mysqli_fetch_assoc($store_data) : null;

if (!$view_data) {
  header("Location: desa");
  exit();
}

$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Ubah Desa";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title"><h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5></div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Desa</li>
        <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] . ' ' . htmlspecialchars($view_data['nama']) ?></li>
      </ul>
    </div>
  </div>

  <div class="main-content">
    <div class="row">
      <div class="col-lg-8">
        <div class="card stretch stretch-full">
          <div class="card-body">
            <form action="" method="post">
              <input type="hidden" name="id" value="<?= $view_data['id'] ?>">
              <div class="mb-3">
                <label for="kecamatan_id" class="form-label">Kecamatan</label>
                <select name="kecamatan_id" class="form-select" id="kecamatan_id" required>
                  <option value="">Pilih kecamatan</option>
                  <?php if ($views_kecamatan instanceof mysqli_result) {
                    foreach ($views_kecamatan as $kecamatan) { ?>
                  <option value="<?= $kecamatan['id'] ?>" <?= $view_data['kecamatan_id'] == $kecamatan['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($kecamatan['nama'] . ' — ' . $kecamatan['jenis_kabupaten_kota'] . ' ' . $kecamatan['nama_kabupaten_kota']) ?>
                  </option>
                  <?php }
                  } ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="nama" class="form-label">Nama Desa</label>
                <input type="text" name="nama" class="form-control" id="nama" value="<?= htmlspecialchars($view_data['nama']) ?>" placeholder="Masukan nama desa" required>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="desa" class="btn btn-success">Kembali</a>
                <button type="submit" name="edit_desa" class="btn btn-warning">Ubah</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once("../../templates/views_bottom.php") ?>
