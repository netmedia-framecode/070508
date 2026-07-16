<?php require_once("../../controller/manajemen-transaksi.php");
        $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "E-Tiket";
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
                            <th class="text-center">Kode QR</th>
                            <th class="text-center">Order ID</th>
                            <th class="text-center">Kode Booking</th>
                            <th class="text-center">Wisatawan</th>
                            <th class="text-center">Objek Wisata</th>
                            <th class="text-center">Tanggal Kunjungan</th>
                            <th class="text-center">Jumlah Tiket</th>
                            <th class="text-center">Total Tagihan</th>
                            <th class="text-center">Berlaku Sampai</th>
                            <th class="text-center">Status Tiket</th>
                            <th class="text-center">Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if ($views_e_tiket instanceof mysqli_result) {
                            foreach ($views_e_tiket as $key => $data) {
                              $tgl_kunjungan = $data['tgl_kunjungan'] ? date('d-m-Y', strtotime($data['tgl_kunjungan'])) : '-';
                              $berlaku_sampai = $data['berlaku_sampai'] ? date('d-m-Y H:i', strtotime($data['berlaku_sampai'])) : '-';
                              $status = $data['status_tiket'] ?: 'Active';
                              $badge = 'bg-soft-success text-success';
                              if (strtolower($status) == 'used' || strtolower($status) == 'expired' || strtolower($status) == 'inactive') {
                                $badge = 'bg-soft-danger text-danger';
                              } elseif (strtolower($status) == 'pending') {
                                $badge = 'bg-soft-warning text-warning';
                              }
                          ?>
                          <tr class="single-item">
                            <td class="text-center"><?= $key + 1 ?></td>
                            <td><?= htmlspecialchars($data['kode_qr']) ?></td>
                            <td><?= htmlspecialchars($data['order_id'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($data['kode_booking'] ?: '-') ?></td>
                            <td>
                              <?= htmlspecialchars($data['nama_wisatawan'] ?: '-') ?>
                              <div class="fs-11 text-muted"><?= htmlspecialchars($data['email'] ?: '-') ?></div>
                            </td>
                            <td><?= htmlspecialchars($data['nama_wisata'] ?: '-') ?></td>
                            <td class="text-center"><?= $tgl_kunjungan ?></td>
                            <td class="text-center"><?= (int) $data['jumlah_tiket'] ?></td>
                            <td class="text-end">Rp <?= number_format((int) $data['total_tagihan'], 0, ',', '.') ?></td>
                            <td class="text-center"><?= $berlaku_sampai ?></td>
                            <td class="text-center"><span class="badge <?= $badge ?>"><?= htmlspecialchars($status) ?></span></td>
                            <td>
                              <div class="hstack gap-2 justify-content-center">
                                <?php if (canAction('delete')) { ?>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteETiketModal<?= $data['id'] ?>">
                                  <i class="bi bi-trash"></i>
                                </button>

                                <div class="modal fade e-tiket-delete-modal" id="deleteETiketModal<?= $data['id'] ?>" tabindex="-1" aria-labelledby="deleteETiketModalLabel<?= $data['id'] ?>" aria-hidden="true">
                                  <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="deleteETiketModalLabel<?= $data['id'] ?>">Konfirmasi Hapus E-Tiket</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <div class="modal-body text-start">
                                        Apakah anda yakin ingin menghapus e-tiket dengan kode QR <strong><?= htmlspecialchars($data['kode_qr']) ?></strong>?
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <form action="" method="post" class="d-inline">
                                          <input type="hidden" name="id" value="<?= $data['id'] ?>">
                                          <input type="hidden" name="kode_qr" value="<?= htmlspecialchars($data['kode_qr']) ?>">
                                          <button type="submit" name="delete_e_tiket" class="btn btn-danger">Hapus</button>
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
          document.querySelectorAll('.e-tiket-delete-modal').forEach(function(modal) {
            document.body.appendChild(modal);
          });
        });
        </script>

        <?php require_once("../../templates/views_bottom.php") ?>
        
