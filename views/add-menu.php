<?php require_once("../controller/menu-management.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Tambah Menu";
require_once("../templates/views_top.php"); ?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION['project_wisata_sumba_barat_daya']['name_page'] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Menu Management</li>
        <li class="breadcrumb-item"><?= $_SESSION['project_wisata_sumba_barat_daya']['name_page'] ?></li>
      </ul>
    </div>
  </div>
  <!-- [ page-header ] end -->

  <!-- [ Main Content ] start -->
  <div class="main-content">
    <div class="row">
      <div class="col-lg-6">
        <div class="card stretch stretch-full">
          <div class="card-body">
            <form action="" method="post">
              <div class="mb-3">
                <label for="menu" class="form-label">Menu</label>
                <input type="text" name="menu" class="form-control" id="menu" placeholder="Menu" required>
              </div>
              <div class="mb-3">
                <label for="icon" class="form-label">Icon</label>
                <input type="text" name="icon" class="form-control" id="icon" placeholder="bi bi-123" required>
                <small class="form-text text-muted">Lihat icon <a href="https://icons.getbootstrap.com/" class="text-decoration-none"
                    target="_blank">disini</a>. Contoh <strong>bi bi-123</strong></small>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="menu" class="btn btn-success">Kembali</a>
                <button type="submit" name="add_menu" class="btn btn-primary">Tambah</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->

</div>

<?php require_once("../templates/views_bottom.php") ?>
