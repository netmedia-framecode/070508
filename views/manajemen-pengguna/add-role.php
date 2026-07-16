<?php require_once("../../controller/user-management.php");
  $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Tambah Role";
  require_once("../../templates/views_top.php");
?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION['project_wisata_sumba_barat_daya']['name_page'] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">User Management</li>
        <li class="breadcrumb-item"><?= $_SESSION['project_wisata_sumba_barat_daya']['name_page']?></li>
      </ul>
    </div>
  </div>
  <!-- [ page-header ] end -->

  <!-- [ Main Content ] start -->
  <div class="main-content">
    <div class="row">
      <div class="col-lg-6">
        <div class="card stretch stretch-full">
          <fo class="card-body">
            <form action="" method="post">
              <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <input type="text" name="role" class="form-control" id="role" minlength="3" required>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="role" class="btn btn-success">Kembali</a>
                <button type="submit" name="add_role" class="btn btn-primary">Tambah</button>
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
