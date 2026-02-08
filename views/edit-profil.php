<?php require_once("../controller/account.php");
$_SESSION['project_wisata_sumba_barat_daya']['name_page'] = "Profil";
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
  <?php foreach ($view_profile as $data) { ?>
  <div class="main-content">
    <div class="row">
      <div class="col-lg-12">
        <div class="card stretch stretch-full">
          <div class="card-body">
            <div class="row no-gutters">
              <div class="col-lg-5">
                <img src="../assets/img/profil/<?= $data["image"] ?>" alt="<?= $data["image"] ?>"
                  style="width: 350px;height: 350px;object-fit: cover;">
              </div>
              <div class="col-lg-7">
                <form action="" method="post">
                  <input type="hidden" name="id_user" value="<?= $data['id_user'] ?>">
                  <input type="hidden" name="imageOld" value="<?= $data['image'] ?>">
                  <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" name="name" value="<?= $data['name'] ?>" class="form-control" id="name"
                      placeholder="Nama" minlength="3" required>
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" value="<?= $data['email'] ?>" class="form-control" id="email"
                      placeholder="Email" readonly>
                  </div>
                  <div class="mb-3">
                    <label for="password" class="form-label">Password baru <small>(Optional)</small></label>
                    <input type="text" name="password" class="form-control" id="password" minlength="8" required>
                  </div>
                  <div class="mb-3">
                    <label for="formFile" class="form-label">Foto profil <small>(Optional)</small></label>
                    <input class="form-control" name="image" type="file" id="formFile" accept="image/*">
                  </div>
                  <div class="mb-3">
                    <button type="submit" name="edit_profil" class="btn btn-warning">
                      <i class="bi bi-pencil-square me-2"></i> Ubah</button>
                  </div>
                </form>
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
