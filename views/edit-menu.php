<?php require_once("../controller/menu-management.php");
if(!isset($_GET['p'])){
  header("Location: menu");
  exit();
}else{
  $id_menu = valid($conn, $_GET['p']); 
  $menu = "SELECT * FROM user_menu WHERE id_menu = '$id_menu'";
  $edit_menu = mysqli_query($conn, $menu);
  $data_menu = mysqli_fetch_assoc($edit_menu);
  $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Ubah Menu";
  require_once("../templates/views_top.php");
?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION['project_wisata_sumba_barat_daya']['name_page'] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Menu Management</li>
        <li class="breadcrumb-item"><?= $_SESSION['project_wisata_sumba_barat_daya']['name_page'].' '.$data_menu['menu'] ?></li>
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
              <input type="hidden" name="id_menu" value="<?= $data_menu['id_menu'] ?>">
              <input type="hidden" name="menuOld" value="<?= $data_menu['menu'] ?>">
              <div class="mb-3">
                <label for="menu" class="form-label">Menu</label>
                <input type="text" name="menu" value="<?= $data_menu['menu']?>" class="form-control" id="menu"
                  placeholder="Menu" required>
              </div>
              <div class="mb-3">
                <label for="icon" class="form-label">Icon</label>
                <input type="text" name="icon" value="<?= $data_menu['icon']?>" class="form-control" id="icon"
                  placeholder="bi bi-123" required>
                <small class="form-text text-muted">Lihat icon <a href="https://icons.getbootstrap.com/"
                    class="text-decoration-none" target="_blank">disini</a>. Contoh <strong>bi bi-123</strong></small>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="menu" class="btn btn-success">Kembali</a>
                <button type="submit" name="edit_menu" class="btn btn-warning">Ubah</button>
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
require_once("../templates/views_bottom.php") ?>
