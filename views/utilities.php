<?php require_once("../controller/utilities.php");
$_SESSION['project_wisata_sumba_barat_daya']['name_page'] = "Utilitas";
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
  <?php foreach ($views_utilities as $data) { ?>
  <div class="main-content">
    <div class="row">
      <div class="col-lg-12">
        <div class="card stretch stretch-full">
          <div class="card-body">
            <div class="row">
              <div class="col-lg-6 d-none d-lg-block"
                style="height: 450px; background: url('../assets/img/<?= $data['logo'] ?>'); background-position: center; background-size: cover;">
              </div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Data Meta Website</h1>
                  </div>
                  <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="logoOld" value="<?= $data['logo'] ?>">
                    <div class="mb-3">
                      <label for="logo" class="form-label">Masukan logo untuk website</label>
                      <input class="form-control" name="logo" type="file" id="logo" accept="image/*">
                    </div>
                    <div class="mb-3">
                      <label for="name_web" class="form-label">Nama Website</label>
                      <input type="text" name="name_web" value="<?= $data['name_web'] ?>" class="form-control"
                        id="name_web" placeholder="Nama Website" required>
                    </div>
                    <div class="mb-3">
                      <label for="keyword" class="form-label">Kata Kunci Website</label>
                      <input type="text" name="keyword" value="<?= $data['keyword'] ?>" class="form-control"
                        id="keyword" placeholder="Kata Kunci Website">
                    </div>
                    <div class="mb-3">
                      <label for="description" class="form-label">Deskripsi</label>
                      <textarea class="form-control" name="description" id="description"
                        rows="3"><?= $data['description'] ?></textarea>
                    </div>
                    <div class="mb-3">
                      <label for="author" class="form-label">Author</label>
                      <input type="text" name="author" value="<?= $data['author'] ?>" class="form-control" id="author"
                        placeholder="Author">
                    </div>
                    <button type="submit" name="utilities" class="btn btn-warning btn-user btn-block">
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
