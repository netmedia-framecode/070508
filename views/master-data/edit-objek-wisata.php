<?php require_once("../../controller/master-data.php");
if (!isset($_GET["p"])) {
  header("Location: objek-wisata");
  exit();
}

$id = valid($conn, $_GET["p"]);
$pull_data = "SELECT * FROM objek_wisata WHERE id='$id'";
$store_data = mysqli_query($conn, $pull_data);
$view_data = mysqli_fetch_assoc($store_data);

if (!$view_data) {
  header("Location: objek-wisata");
  exit();
}

$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Ubah Objek Wisata";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Objek Wisata</li>
        <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] . ' ' . htmlspecialchars($view_data["nama_wisata"]) ?></li>
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
              <input type="hidden" name="gambar_old" value="<?= htmlspecialchars($view_data['gambar']) ?>">
              <div class="mb-3">
                <label for="nama_wisata" class="form-label">Nama Wisata</label>
                <input type="text" name="nama_wisata" class="form-control" id="nama_wisata" value="<?= htmlspecialchars($view_data['nama_wisata']) ?>" placeholder="Masukan nama objek wisata" required>
              </div>
              <div class="row">
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label for="desa_id" class="form-label">Desa</label>
                    <select name="desa_id" class="form-select lokasi-wisata" id="desa_id">
                      <option value="">Pilih desa</option>
                      <?php if ($views_desa instanceof mysqli_result) {
                        foreach ($views_desa as $desa) { ?>
                      <option value="<?= $desa['id'] ?>" <?= $view_data['desa_id'] == $desa['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($desa['nama'] . ' — Kec. ' . $desa['nama_kecamatan']) ?>
                      </option>
                      <?php }
                      } ?>
                    </select>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label for="kelurahan_id" class="form-label">Kelurahan</label>
                    <select name="kelurahan_id" class="form-select lokasi-wisata" id="kelurahan_id">
                      <option value="">Pilih kelurahan</option>
                      <?php if ($views_kelurahan instanceof mysqli_result) {
                        foreach ($views_kelurahan as $kelurahan) { ?>
                      <option value="<?= $kelurahan['id'] ?>" <?= $view_data['kelurahan_id'] == $kelurahan['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kelurahan['nama'] . ' — Kec. ' . $kelurahan['nama_kecamatan']) ?>
                      </option>
                      <?php }
                      } ?>
                    </select>
                  </div>
                </div>
              </div>
              <small class="form-text text-muted d-block mb-3">Pilih salah satu lokasi: desa atau kelurahan.</small>
              <div class="row">
                <div class="col-lg-4">
                  <div class="mb-3">
                    <label for="harga_tiket" class="form-label">Harga Tiket</label>
                    <input type="number" name="harga_tiket" class="form-control" id="harga_tiket" min="0" value="<?= (int) $view_data['harga_tiket'] ?>" required>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="mb-3">
                    <label for="jam_buka" class="form-label">Jam Buka</label>
                    <input type="time" name="jam_buka" class="form-control" id="jam_buka" value="<?= $view_data['jam_buka'] ? date('H:i', strtotime($view_data['jam_buka'])) : '' ?>">
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="mb-3">
                    <label for="jam_tutup" class="form-label">Jam Tutup</label>
                    <input type="time" name="jam_tutup" class="form-control" id="jam_tutup" value="<?= $view_data['jam_tutup'] ? date('H:i', strtotime($view_data['jam_tutup'])) : '' ?>">
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label for="gambar" class="form-label">Gambar</label>
                <?php if (!empty($view_data['gambar'])) { ?>
                <div class="mb-2">
                  <img src="<?= $baseURL . htmlspecialchars($view_data['gambar']) ?>" alt="<?= htmlspecialchars($view_data['nama_wisata']) ?>" class="rounded" style="width: 160px; height: 100px; object-fit: cover;">
                </div>
                <?php } ?>
                <input type="file" name="gambar" class="form-control" id="gambar" accept="image/*">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti gambar. Format yang diizinkan: JPG, JPEG, PNG.</small>
              </div>
              <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" id="deskripsi" rows="5" placeholder="Masukan deskripsi objek wisata"><?= htmlspecialchars($view_data['deskripsi']) ?></textarea>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="objek-wisata" class="btn btn-success">Kembali</a>
                <button type="submit" name="edit_objek_wisata" class="btn btn-warning">Ubah</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const desa = document.getElementById('desa_id');
  const kelurahan = document.getElementById('kelurahan_id');
  function syncLokasi() {
    kelurahan.disabled = desa.value !== '';
    desa.disabled = kelurahan.value !== '';
  }
  desa.addEventListener('change', syncLokasi);
  kelurahan.addEventListener('change', syncLokasi);
  syncLokasi();
});
</script>

<?php require_once("../../templates/views_bottom.php") ?>
