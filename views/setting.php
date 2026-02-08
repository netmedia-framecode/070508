<?php require_once("../controller/account.php");
$_SESSION['project_wisata_sumba_barat_daya']['name_page'] = "Setting";
require_once("../templates/views_top.php"); ?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION['project_wisata_sumba_barat_daya']['name_page'] ?></h5>
      </div>
    </div>
  </div>
  <!-- [ page-header ] end -->

  <!-- [ Main Content ] start -->
  <?php foreach ($views_auth as $data) { ?>
  <div class="main-content">
    <div class="row">
      <div class="col-lg-12">
        <div class="card stretch stretch-full">
          <div class="card-body">
            <div class="row">
              <div class="col-lg-6 d-none d-lg-block"
                style="height: 450px; background: url('../assets/img/auth/<?= $data['image'] ?>'); background-position: center; background-size: cover;">
              </div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">UI Login!</h1>
                  </div>
                  <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="imageOld" value="<?= $data['image'] ?>">
                    <div class="mb-3">
                      <label for="image" class="form-label">Masukan foto untuk system login</label>
                      <input class="form-control" name="image" type="file" id="image" accept="image/*">
                    </div>
                    <div class="mb-3">
                      <label for="bg" class="form-label">Masukan Warna Primary</label>
                      <div class="d-flex justify-content-start align-content-center">
                        <input type="color" name="bg" class="form-control w-50" id="bg" value="<?= $data['bg']?>"
                          required>
                        <span class="my-auto ml-3" id="bgCode"><?= $data['bg']?></span>
                      </div>
                    </div>
                    <script>
                    // Tambahkan event listener untuk mendeteksi perubahan warna
                    document.getElementById('bg').addEventListener('input', function() {
                      // Perbarui nilai kode warna
                      document.getElementById('bgCode').textContent = this.value;
                    });
                    </script>
                    <div class="mb-3">
                      <label class="form-label">Pilih Model UI System Login</label>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="model" value="1" id="creative" checked>
                        <label class="form-check-label" for="creative">
                          Creative
                        </label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="model" value="2" id="minimal">
                        <label class="form-check-label" for="minimal">
                          Minimal
                        </label>
                      </div>
                    </div>
                    <button type="submit" name="setting" class="btn btn-warning btn-user btn-block">
                      <i class="bi bi-pencil-square me-2"></i> Ubah
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>
  <!-- [ Main Content ] end -->

</div>

<?php require_once("../templates/views_bottom.php") ?>
