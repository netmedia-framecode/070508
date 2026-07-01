<?php require_once("../../controller/transaksi.php");
        $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Pemesanan Tiket";
        require_once("../../templates/views_top.php"); ?>

        <div class="nxl-content" style="height: 100vh;">

          <!-- [ page-header ] start -->
          <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
              <div class="page-header-title">
                <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item">Pemesanan Tiket</li>
                <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></li>
              </ul>
            </div>
            <div class="page-header-right ms-auto">
              <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                  <a href="javascript:void(0)" class="page-header-right-close-toggle">
                    <i class="feather-arrow-left me-2"></i>
                    <span>Back</span>
                  </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                  <a href="add-pemesanan-tiket" class="btn btn-primary">
                    <i class="feather-plus me-2"></i>
                    <span>Tambah</span>
                  </a>
                </div>
              </div>
              <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                  <i class="feather-align-right fs-20"></i>
                </a>
              </div>
            </div>
          </div>
          <!-- [ page-header ] end -->

          <!-- [ Main Content ] start -->
          <div class="main-content">
          </div>
          <!-- [ Main Content ] end -->

        </div>

        <?php require_once("../../templates/views_bottom.php") ?>
        