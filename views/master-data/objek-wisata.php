<?php require_once("../../controller/master-data.php");
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Objek Wisata";
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
          <a href="add-objek-wisata" class="btn btn-primary">
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
                    <th class="text-center">Gambar</th>
                    <th class="text-center">Nama Wisata</th>
                    <th class="text-center">Lokasi</th>
                    <th class="text-center">Harga Tiket</th>
                    <th class="text-center">Jam Operasional</th>
                    <th class="text-center">Deskripsi</th>
                    <th class="text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($views_objek_wisata instanceof mysqli_result) {
                    foreach ($views_objek_wisata as $key => $data) {
                      $gambar = trim($data['gambar'] ?? '');
                      $gambar_url = preg_match('/^https?:\/\//', $gambar) ? $gambar : $baseURL . ltrim($gambar, '/');
                      $jam_buka = $data['jam_buka'] ? date('H:i', strtotime($data['jam_buka'])) : '-';
                      $jam_tutup = $data['jam_tutup'] ? date('H:i', strtotime($data['jam_tutup'])) : '-';
                      $deskripsi = trim(strip_tags($data['deskripsi'] ?? ''));
                      $deskripsi = strlen($deskripsi) > 80 ? substr($deskripsi, 0, 80) . '...' : $deskripsi;
                      $jenis_lokasi = $data['desa_id'] ? 'Desa' : 'Kelurahan';
                      $nama_lokasi = $data['nama_desa'] ?: $data['nama_kelurahan'];
                      $lokasi = $nama_lokasi
                        ? $jenis_lokasi . ' ' . $nama_lokasi . ', Kecamatan ' . $data['nama_kecamatan'] . ', ' . $data['jenis_kabupaten_kota'] . ' ' . $data['nama_kabupaten_kota']
                        : '-';
                  ?>
                  <tr class="single-item">
                    <td class="text-center"><?= $key + 1 ?></td>
                    <td class="text-center">
                      <?php if ($gambar) { ?>
                      <img src="<?= htmlspecialchars($gambar_url) ?>" alt="<?= htmlspecialchars($data['nama_wisata']) ?>" class="rounded" style="width: 72px; height: 48px; object-fit: cover;">
                      <?php } else { ?>
                      <span class="badge bg-soft-secondary text-secondary">Tidak ada</span>
                      <?php } ?>
                    </td>
                    <td><?= htmlspecialchars($data['nama_wisata']) ?></td>
                    <td><?= htmlspecialchars($lokasi) ?></td>
                    <td class="text-end">Rp <?= number_format((int) $data['harga_tiket'], 0, ',', '.') ?></td>
                    <td class="text-center"><?= $jam_buka ?> - <?= $jam_tutup ?></td>
                    <td><?= htmlspecialchars($deskripsi ?: '-') ?></td>
                    <td>
                      <div class="hstack gap-2 justify-content-center">
                        <?php if (canAction('edit')) { ?>
                        <a href="edit-objek-wisata?p=<?= $data['id'] ?>" class="btn btn-warning btn-sm">
                          <i class="bi bi-pencil-square"></i>
                        </a>
                        <?php } ?>
                        <?php if (canAction('delete')) { ?>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteObjekWisataModal<?= $data['id'] ?>">
                          <i class="bi bi-trash"></i>
                        </button>

                        <div class="modal fade objek-wisata-delete-modal" id="deleteObjekWisataModal<?= $data['id'] ?>" tabindex="-1" aria-labelledby="deleteObjekWisataModalLabel<?= $data['id'] ?>" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="deleteObjekWisataModalLabel<?= $data['id'] ?>">Konfirmasi Hapus Objek Wisata</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body text-start">
                                Apakah anda yakin ingin menghapus objek wisata <strong><?= htmlspecialchars($data['nama_wisata']) ?></strong>?
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <form action="" method="post" class="d-inline">
                                  <input type="hidden" name="id" value="<?= $data['id'] ?>">
                                  <input type="hidden" name="nama_wisata" value="<?= htmlspecialchars($data['nama_wisata']) ?>">
                                  <input type="hidden" name="gambar" value="<?= htmlspecialchars($data['gambar']) ?>">
                                  <button type="submit" name="delete_objek_wisata" class="btn btn-danger">Hapus</button>
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
  document.querySelectorAll('.objek-wisata-delete-modal').forEach(function(modal) {
    document.body.appendChild(modal);
  });
});
</script>

<?php require_once("../../templates/views_bottom.php") ?>
