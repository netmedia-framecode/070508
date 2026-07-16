<?php require_once("../../controller/manajemen-transaksi.php");
        $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Tambah Pemesanan Tiket";
        require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">

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
          </div>
          <!-- [ page-header ] end -->

          <!-- [ Main Content ] start -->
          <div class="main-content">
            <div class="row">
              <div class="col-lg-8">
                <div class="card stretch stretch-full">
                  <div class="card-body">
                    <form action="" method="post">
                      <h6 class="mb-3">Data Profil Wisatawan</h6>
                      <div class="mb-3">
                        <label class="form-label">Sumber Data Wisatawan</label>
                        <div class="hstack gap-3">
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode_wisatawan" id="mode_wisatawan_existing" value="existing" checked>
                            <label class="form-check-label" for="mode_wisatawan_existing">Pilih yang sudah ada</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode_wisatawan" id="mode_wisatawan_baru" value="baru">
                            <label class="form-check-label" for="mode_wisatawan_baru">Input baru</label>
                          </div>
                        </div>
                      </div>
                      <div class="mb-3" id="wisatawan-existing-wrapper">
                        <label for="id_wisatawan" class="form-label">Wisatawan Terdaftar</label>
                        <select name="id_wisatawan" class="form-select" id="id_wisatawan" data-select2-selector="default" required>
                          <option value="">Pilih wisatawan</option>
                          <?php if ($views_wisatawan instanceof mysqli_result) {
                            foreach ($views_wisatawan as $wisatawan) { ?>
                          <option value="<?= $wisatawan['id_user'] ?>"><?= htmlspecialchars($wisatawan['name']) ?> - <?= htmlspecialchars($wisatawan['email']) ?></option>
                          <?php }
                          } ?>
                        </select>
                      </div>
                      <div id="wisatawan-baru-wrapper" class="d-none">
                      <div class="row">
                        <div class="col-lg-6">
                          <div class="mb-3">
                            <label for="nama_wisatawan" class="form-label">Nama Wisatawan</label>
                            <input type="text" name="nama_wisatawan" class="form-control wisatawan-baru-field" id="nama_wisatawan">
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control wisatawan-baru-field" id="email">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-lg-6">
                          <div class="mb-3">
                            <label for="no_hp" class="form-label">No. HP</label>
                            <input type="text" name="no_hp" class="form-control wisatawan-baru-field" id="no_hp">
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="mb-3">
                            <label for="asal_daerah" class="form-label">Asal Daerah</label>
                            <input type="text" name="asal_daerah" class="form-control wisatawan-baru-field" id="asal_daerah">
                          </div>
                        </div>
                      </div>
                      </div>
                      <hr class="my-4">
                      <h6 class="mb-3">Data Pemesanan Tiket</h6>
                      <div class="mb-3">
                        <label for="id_objek_wisata" class="form-label">Objek Wisata</label>
                        <select name="id_objek_wisata" class="form-select" id="id_objek_wisata" required>
                          <option value="">Pilih objek wisata</option>
                          <?php if ($views_objek_wisata instanceof mysqli_result) {
                            foreach ($views_objek_wisata as $objek_wisata) { ?>
                          <option value="<?= $objek_wisata['id'] ?>" data-harga="<?= (int) $objek_wisata['harga_tiket'] ?>">
                            <?= htmlspecialchars($objek_wisata['nama_wisata']) ?> - Rp <?= number_format((int) $objek_wisata['harga_tiket'], 0, ',', '.') ?>
                          </option>
                          <?php }
                          } ?>
                        </select>
                      </div>
                      <div class="row">
                        <div class="col-lg-4">
                          <div class="mb-3">
                            <label for="tgl_kunjungan" class="form-label">Tanggal Kunjungan</label>
                            <input type="date" name="tgl_kunjungan" class="form-control" id="tgl_kunjungan" required>
                          </div>
                        </div>
                        <div class="col-lg-4">
                          <div class="mb-3">
                            <label for="jumlah_tiket" class="form-label">Jumlah Tiket</label>
                            <input type="number" name="jumlah_tiket" class="form-control" id="jumlah_tiket" min="1" value="1" required>
                          </div>
                        </div>
                        <div class="col-lg-4">
                          <div class="mb-3">
                            <label for="total_tagihan_preview" class="form-label">Total Tagihan</label>
                            <input type="text" class="form-control" id="total_tagihan_preview" value="Rp 0" readonly>
                          </div>
                        </div>
                      </div>
                      <div class="mb-3 hstack gap-2 justify-content-left">
                        <a href="pemesanan-tiket" class="btn btn-success">Kembali</a>
                        <button type="submit" name="add_pemesanan_tiket" class="btn btn-primary">Tambah</button>
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
          const objekWisata = document.getElementById('id_objek_wisata');
          const jumlahTiket = document.getElementById('jumlah_tiket');
          const totalTagihan = document.getElementById('total_tagihan_preview');
          const modeWisatawan = document.querySelectorAll('input[name="mode_wisatawan"]');
          const wisatawanExistingWrapper = document.getElementById('wisatawan-existing-wrapper');
          const wisatawanBaruWrapper = document.getElementById('wisatawan-baru-wrapper');
          const idWisatawan = document.getElementById('id_wisatawan');
          const wisatawanBaruFields = document.querySelectorAll('.wisatawan-baru-field');

          const formatRupiah = function(value) {
            return new Intl.NumberFormat('id-ID', {
              style: 'currency',
              currency: 'IDR',
              maximumFractionDigits: 0
            }).format(value);
          };

          const updateTotal = function() {
            const selectedOption = objekWisata.options[objekWisata.selectedIndex];
            const harga = selectedOption ? parseInt(selectedOption.dataset.harga || 0, 10) : 0;
            const jumlah = parseInt(jumlahTiket.value || 0, 10);
            totalTagihan.value = formatRupiah(harga * jumlah);
          };

          objekWisata.addEventListener('change', updateTotal);
          jumlahTiket.addEventListener('input', updateTotal);

          const updateModeWisatawan = function() {
            const selectedMode = document.querySelector('input[name="mode_wisatawan"]:checked').value;
            const isBaru = selectedMode === 'baru';
            wisatawanExistingWrapper.classList.toggle('d-none', isBaru);
            wisatawanBaruWrapper.classList.toggle('d-none', !isBaru);
            idWisatawan.required = !isBaru;
            wisatawanBaruFields.forEach(function(field) {
              field.required = isBaru;
            });
          };

          modeWisatawan.forEach(function(input) {
            input.addEventListener('change', updateModeWisatawan);
          });
          updateModeWisatawan();

          if (window.jQuery && jQuery.fn.select2) {
            jQuery('#id_wisatawan').select2({
              theme: 'bootstrap-5',
              width: '100%',
              placeholder: 'Pilih wisatawan',
              minimumResultsForSearch: 0
            });
          }
        });
        </script>

        <?php require_once("../../templates/views_bottom.php") ?>
        
