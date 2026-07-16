<?php require_once("../../controller/manajemen-transaksi.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Data Keranjang";
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
                    <th class="text-center">Wisatawan</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Objek Wisata</th>
                    <th class="text-center">Jumlah Tiket</th>
                    <th class="text-center">Total Sementara</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($views_data_keranjang instanceof mysqli_result) {
                    foreach ($views_data_keranjang as $key => $data) { ?>
                  <tr class="single-item">
                    <td class="text-center"><?= $key + 1 ?></td>
                    <td><?= htmlspecialchars($data['nama_wisatawan'] ?: 'User #' . $data['id_wisatawan']) ?></td>
                    <td><?= htmlspecialchars($data['email'] ?: '-') ?></td>
                    <td><?= htmlspecialchars($data['nama_wisata'] ?: 'Objek Wisata #' . $data['id_objek_wisata']) ?></td>
                    <td class="text-center"><?= (int) $data['jumlah_tiket'] ?></td>
                    <td class="text-end">Rp <?= number_format((int) $data['total_harga_sementara'], 0, ',', '.') ?></td>
                    <td class="text-center"><span class="badge bg-soft-warning text-warning">Tertunda</span></td>
                    <td>
                      <div class="hstack gap-2 justify-content-center">
                        <?php if (canAction('edit')) { ?>
                        <a href="edit-data-keranjang?p=<?= $data['id'] ?>" class="btn btn-warning btn-sm">
                          <i class="bi bi-pencil-square"></i>
                        </a>
                        <?php } ?>
                        <?php if (canAction('delete')) { ?>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteKeranjangModal<?= $data['id'] ?>">
                          <i class="bi bi-trash"></i>
                        </button>

                        <div class="modal fade keranjang-delete-modal" id="deleteKeranjangModal<?= $data['id'] ?>" tabindex="-1" aria-labelledby="deleteKeranjangModalLabel<?= $data['id'] ?>" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="deleteKeranjangModalLabel<?= $data['id'] ?>">Konfirmasi Hapus Keranjang</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body text-start">
                                Apakah anda yakin ingin menghapus keranjang <strong><?= htmlspecialchars($data['nama_wisatawan'] ?: 'User #' . $data['id_wisatawan']) ?></strong> untuk objek wisata <strong><?= htmlspecialchars($data['nama_wisata'] ?: 'Objek Wisata #' . $data['id_objek_wisata']) ?></strong>?
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <form action="" method="post" class="d-inline">
                                  <input type="hidden" name="id" value="<?= $data['id'] ?>">
                                  <input type="hidden" name="nama_wisatawan" value="<?= htmlspecialchars($data['nama_wisatawan'] ?: 'User #' . $data['id_wisatawan']) ?>">
                                  <button type="submit" name="delete_keranjang" class="btn btn-danger">Hapus</button>
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
  document.querySelectorAll('.keranjang-delete-modal').forEach(function(modal) {
    document.body.appendChild(modal);
  });
});
</script>

<?php require_once("../../templates/views_bottom.php") ?>
