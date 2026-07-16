<?php require_once("../../controller/manajemen-transaksi.php");
        $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Tambah Konfirmasi Pembayaran";
        require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">

          <!-- [ page-header ] start -->
          <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
              <div class="page-header-title">
                <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item">Konfirmasi Pembayaran</li>
                <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></li>
              </ul>
            </div>
          </div>
          <!-- [ page-header ] end -->

          <!-- [ Main Content ] start -->
          <div class="main-content">
            <div class="row">
              <div class="col-lg-8">
                <div class="card stretch stretch-full">
                  <div class="card-body">
                    <form action="" method="post">
                      <div class="mb-3">
                        <label for="id_pemesanan" class="form-label">Pemesanan Tiket</label>
                        <select name="id_pemesanan" class="form-select" id="id_pemesanan" data-select2-selector="default" required>
                          <option value="">Pilih pemesanan tiket</option>
                          <?php if ($views_pemesanan_tiket_belum_bayar instanceof mysqli_result) {
                            foreach ($views_pemesanan_tiket_belum_bayar as $pemesanan) { ?>
                          <option value="<?= $pemesanan['id'] ?>">
                            <?= htmlspecialchars($pemesanan['kode_booking']) ?> -
                            <?= htmlspecialchars($pemesanan['nama_wisatawan'] ?: '-') ?> -
                            <?= htmlspecialchars($pemesanan['nama_wisata'] ?: '-') ?> -
                            Rp <?= number_format((int) $pemesanan['total_tagihan'], 0, ',', '.') ?>
                          </option>
                          <?php }
                          } ?>
                        </select>
                      </div>
                      <div class="row">
                        <div class="col-lg-6">
                          <div class="mb-3">
                            <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="form-select" id="metode_pembayaran">
                              <option value="">Pilih metode</option>
                              <option value="Bank Transfer">Bank Transfer</option>
                              <option value="E-Wallet">E-Wallet</option>
                              <option value="QRIS">QRIS</option>
                              <option value="Tunai">Tunai</option>
                              <option value="Kartu Kredit">Kartu Kredit</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="mb-3">
                            <label for="waktu_bayar" class="form-label">Waktu Bayar</label>
                            <input type="datetime-local" name="waktu_bayar" class="form-control" id="waktu_bayar">
                          </div>
                        </div>
                      </div>
                      <div class="mb-3 hstack gap-2 justify-content-left">
                        <a href="konfirmasi-pembayaran" class="btn btn-success">Kembali</a>
                        <button type="submit" name="add_pembayaran" class="btn btn-primary">Tambah</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- [ Main Content ] end -->

        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
          if (window.jQuery && jQuery.fn.select2) {
            jQuery('#id_pemesanan').select2({
              theme: 'bootstrap-5',
              width: '100%',
              placeholder: 'Pilih pemesanan tiket',
              minimumResultsForSearch: 0
            });
          }
        });
        </script>

        <?php require_once("../../templates/views_bottom.php") ?>
        
