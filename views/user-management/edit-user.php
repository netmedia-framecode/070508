<?php require_once("../../controller/user-management.php");
if(!isset($_GET['p'])){
  header("Location: users");
  exit();
}else{
  $id_user = valid($conn, $_GET['p']); 
  $users = "SELECT * FROM users WHERE id_user = '$id_user'";
  $edit_users = mysqli_query($conn, $users);
  $data_users = mysqli_fetch_assoc($edit_users);
  $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Ubah User";
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
        <li class="breadcrumb-item"><?= $_SESSION['project_wisata_sumba_barat_daya']['name_page'].' '.$data_users['name'] ?></li>
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
              <input type="hidden" name="id_user" value="<?= $data_users['id_user'] ?>">
              <div class="mb-3">
                <label for="id_active" class="form-label">Status</label>
                <select name="id_active" class="form-control" id="id_active" required>
                  <option value="" selected>Pilih Status</option>
                  <?php $id_status = $data_users['id_active'];
                  foreach ($views_user_status as $data_select_status) {
                    $selected = ($data_select_status['id_status'] == $id_status) ? 'selected' : ''; ?>
                  <option value="<?= $data_select_status['id_status'] ?>" <?= $selected ?>>
                    <?= $data_select_status['status'] ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="id_role" class="form-label">Role</label>
                <select name="id_role" class="form-control" id="id_role" required>
                  <option value="" selected>Pilih Role</option>
                  <?php $id_role = $data_users['id_role'];
                  foreach ($views_user_role as $data_select_role) {
                    $selected = ($data_select_role['id_role'] == $id_role) ? 'selected' : ''; ?>
                  <option value="<?= $data_select_role['id_role'] ?>" <?= $selected ?>>
                    <?= $data_select_role['role'] ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="users" class="btn btn-success">Kembali</a>
                <button type="submit" name="edit_users" class="btn btn-warning">Ubah</button>
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
