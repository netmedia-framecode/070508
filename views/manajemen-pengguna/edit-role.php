<?php require_once("../../controller/user-management.php");
if(!isset($_GET['p'])){
  header("Location: role");
  exit();
}else{
  $id_role = valid($conn, $_GET['p']); 
  $user_role = "SELECT * FROM user_role WHERE id_role = '$id_role'";
  $edit_user_role = mysqli_query($conn, $user_role);
  $data_user_role = mysqli_fetch_assoc($edit_user_role);
  $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Ubah Role";
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
        <li class="breadcrumb-item"><?= $_SESSION['project_wisata_sumba_barat_daya']['name_page'].' '.$data_user_role['role'] ?>
        </li>
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
              <input type="hidden" name="id_role" value="<?= $data_user_role['id_role'] ?>">
              <input type="hidden" name="roleOld" value="<?= $data_user_role['role'] ?>">
              <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <input type="text" name="role" value="<?= $data_user_role['role'] ?>" class="form-control" id="role" minlength="3"
                  required>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="role" class="btn btn-success">Kembali</a>
                <button type="submit" name="edit_role" class="btn btn-warning">Ubah</button>
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- [ Main Content ] end -->

</div>

<?php }
require_once("../../templates/views_bottom.php") ?>
