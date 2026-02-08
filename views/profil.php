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
                <table class="table table-sm table-borderless text-dark">
                  <thead>
                    <tr>
                      <th scope="col" style="width: 50px;">Nama</th>
                      <th scope="col" class="text-right" style="width: 20px;">:</th>
                      <th scope="col" style="width: 200px;"><?= $data['name'] ?></th>
                    </tr>
                    <tr>
                      <th scope="col">Email</th>
                      <th scope="col" class="text-right">:</th>
                      <th scope="col"><?= $data['email'] ?></th>
                    </tr>
                    <tr>
                      <th scope="col">Status</th>
                      <th scope="col" class="text-right">:</th>
                      <th scope="col"><?= $data['status'] ?></th>
                    </tr>
                    <tr>
                      <th scope="col">Role</th>
                      <th scope="col" class="text-right">:</th>
                      <th scope="col"><?= $data['role'] ?></th>
                    </tr>
                  </thead>
                </table>
                <p class="card-text"><small class="text-muted">Terakhir diperbarui
                    <?php $date = date_create($data["updated_at"]);
                    echo date_format($date, "l, d M Y"); ?></small>
                </p>
                <a href="edit-profil" class="btn btn-warning">
                  <i class="bi bi-pencil-square me-2"></i> Ubah
                </a>
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
