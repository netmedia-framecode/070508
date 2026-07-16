<?php require_once("../../controller/manajemen-transaksi.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Konfirmasi Pembayaran";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Manajemen Transaksi</li>
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
          <a href="add-konfirmasi-pembayaran" class="btn btn-primary">
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
      <div class="col-lg-12">
        <div class="card stretch stretch-full">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover" id="dataTable">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Order ID</th>
                    <th class="text-center">Kode Booking</th>
                    <th class="text-center">Wisatawan</th>
                    <th class="text-center">Objek Wisata</th>
                    <th class="text-center">Total Tagihan</th>
                    <th class="text-center">Metode</th>
                    <th class="text-center">Waktu Bayar</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($views_konfirmasi_pembayaran instanceof mysqli_result) {
                    foreach ($views_konfirmasi_pembayaran as $key => $data) {
                      $waktu_bayar = $data['waktu_bayar'] ? date('d-m-Y H:i', strtotime($data['waktu_bayar'])) : '-';
                      $status = $data['status_bayar'] ?: 'Unpaid';
                      $badge = 'bg-soft-warning text-warning';
                      if (strtolower($status) == 'paid' || strtolower($status) == 'settlement') {
                        $badge = 'bg-soft-success text-success';
                      } elseif (strtolower($status) == 'failed' || strtolower($status) == 'expired' || strtolower($status) == 'cancelled') {
                        $badge = 'bg-soft-danger text-danger';
                      }
                  ?>
                  <tr class="single-item">
                    <td class="text-center"><?= $key + 1 ?></td>
                    <td><?= htmlspecialchars($data['order_id']) ?></td>
                    <td><?= htmlspecialchars($data['kode_booking'] ?: '-') ?></td>
                    <td>
                      <?= htmlspecialchars($data['nama_wisatawan'] ?: '-') ?>
                      <div class="fs-11 text-muted"><?= htmlspecialchars($data['email'] ?: '-') ?></div>
                    </td>
                    <td><?= htmlspecialchars($data['nama_wisata'] ?: '-') ?></td>
                    <td class="text-end">Rp <?= number_format((int) $data['total_tagihan'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($data['metode_pembayaran'] ?: '-') ?></td>
                    <td class="text-center"><?= $waktu_bayar ?></td>
                    <td class="text-center"><span class="badge <?= $badge ?>"><?= htmlspecialchars($status) ?></span></td>
                    <td>
                      <div class="hstack gap-2 justify-content-center">
                        <?php if (canAction('edit')) { ?>
                        <a href="edit-konfirmasi-pembayaran?p=<?= $data['id'] ?>" class="btn btn-warning btn-sm">
                          <i class="bi bi-pencil-square"></i>
                        </a>
                        <?php } ?>
                        <?php if (canAction('delete')) { ?>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deletePembayaranModal<?= $data['id'] ?>">
                          <i class="bi bi-trash"></i>
                        </button>

                        <div class="modal fade pembayaran-delete-modal" id="deletePembayaranModal<?= $data['id'] ?>" tabindex="-1" aria-labelledby="deletePembayaranModalLabel<?= $data['id'] ?>" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="deletePembayaranModalLabel<?= $data['id'] ?>">Konfirmasi Hapus Pembayaran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body text-start">
                                Apakah anda yakin ingin menghapus konfirmasi pembayaran <strong><?= htmlspecialchars($data['order_id']) ?></strong>?
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <form action="" method="post" class="d-inline">
                                  <input type="hidden" name="id" value="<?= $data['id'] ?>">
                                  <input type="hidden" name="order_id" value="<?= htmlspecialchars($data['order_id']) ?>">
                                  <button type="submit" name="delete_pembayaran" class="btn btn-danger">Hapus</button>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php } ?>
                      </div>
                    </td>
                  </tr>
                  <?php }
                  } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.pembayaran-delete-modal').forEach(function(modal) {
    document.body.appendChild(modal);
  });
});
</script>

<?php require_once("../../templates/views_bottom.php") ?>
