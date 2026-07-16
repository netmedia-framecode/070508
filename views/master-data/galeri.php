<?php require_once("../../controller/master-data.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Galeri";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Master Data</li>
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
          <?php if (canAction('create')) { ?>
          <a href="add-galeri" class="btn btn-primary">
            <i class="feather-plus me-2"></i>
            <span>Tambah</span>
          </a>
          <?php } ?>
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
    <div class="row">
      <?php if ($views_galeri instanceof mysqli_result && mysqli_num_rows($views_galeri) > 0) {
        foreach ($views_galeri as $data) {
          $file_path = trim($data['file_path'] ?? '');
          $file_url = preg_match('/^https?:\/\//', $file_path) ? $file_path : $baseURL . ltrim($file_path, '/');
          $judul = $data['judul'] ?: 'Tanpa Judul';
      ?>
      <div class="col-sm-6 col-xl-4">
        <div class="card stretch stretch-full">
          <?php if ($file_path) { ?>
          <img src="<?= htmlspecialchars($file_url) ?>" class="card-img-top" alt="<?= htmlspecialchars($judul) ?>" style="height: 220px; object-fit: cover;">
          <?php } else { ?>
          <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="height: 220px;">
            Tidak ada gambar
          </div>
          <?php } ?>
          <div class="card-body">
            <h6 class="card-title mb-2"><?= htmlspecialchars($judul) ?></h6>
            <p class="card-text text-muted mb-3">
              <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($data['nama_wisata']) ?>
            </p>
            <div class="hstack gap-2">
              <?php if (canAction('edit')) { ?>
              <a href="edit-galeri?p=<?= $data['id'] ?>" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil-square"></i>
              </a>
              <?php } ?>
              <?php if (canAction('delete')) { ?>
              <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteGaleriModal<?= $data['id'] ?>">
                <i class="bi bi-trash"></i>
              </button>

              <div class="modal fade galeri-delete-modal" id="deleteGaleriModal<?= $data['id'] ?>" tabindex="-1" aria-labelledby="deleteGaleriModalLabel<?= $data['id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="deleteGaleriModalLabel<?= $data['id'] ?>">Konfirmasi Hapus Galeri</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                      Apakah anda yakin ingin menghapus gambar galeri <strong><?= htmlspecialchars($judul) ?></strong> dari objek wisata <strong><?= htmlspecialchars($data['nama_wisata']) ?></strong>?
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      <form action="" method="post" class="d-inline">
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">
                        <input type="hidden" name="judul" value="<?= htmlspecialchars($judul) ?>">
                        <input type="hidden" name="file_path" value="<?= htmlspecialchars($data['file_path']) ?>">
                        <button type="submit" name="delete_galeri" class="btn btn-danger">Hapus</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <?php }
      } else { ?>
      <div class="col-12">
        <div class="card stretch stretch-full">
          <div class="card-body text-center text-muted">
            Data galeri belum tersedia.
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
  <!-- [ Main Content ] end -->

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.galeri-delete-modal').forEach(function(modal) {
    document.body.appendChild(modal);
  });
});
</script>

<?php require_once("../../templates/views_bottom.php") ?>
