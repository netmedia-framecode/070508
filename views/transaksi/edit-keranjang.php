<?php require_once("../../controller/transaksi.php");
        if(!isset($_GET["p"])){
          header("Location: menu");
          exit();
        }else{
          $id = valid($conn, $_GET["p"]); 
          $pull_data = "SELECT * FROM  WHERE  = '$id'";
          $store_data = mysqli_query($conn, $pull_data);
          $view_data = mysqli_fetch_assoc($store_data);
        $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Ubah Keranjang";
        require_once("../../templates/views_top.php"); ?>

        <div class="nxl-content" style="height: 100vh;">

          <!-- [ page-header ] start -->
          <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
              <div class="page-header-title">
                <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item">Keranjang</li>
                <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"].' '.$view_data[""]  ?></li>
              </ul>
            </div>
          </div>
          <!-- [ page-header ] end -->

          <!-- [ Main Content ] start -->
          <div class="main-content">
          </div>
          <!-- [ Main Content ] end -->

        </div>

        <?php }
        require_once("../../templates/views_bottom.php") ?>
        