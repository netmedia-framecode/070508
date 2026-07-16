<?php require_once("../../controller/manajemen-transaksi.php");
if (!isset($_GET["p"])) {
  header("Location: konfirmasi-pembayaran");
  exit();
}

$id = valid($conn, $_GET["p"]);
$pull_data = "SELECT pembayaran.*, pemesanan_tiket.kode_booking, pemesanan_tiket.total_tagihan, pemesanan_tiket.tgl_kunjungan, users.name AS nama_wisatawan, users.email, objek_wisata.nama_wisata
  FROM pembayaran
  LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
  LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
  LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
  WHERE pembayaran.id='$id'";
$store_data = mysqli_query($conn, $pull_data);
$view_data = mysqli_fetch_assoc($store_data);

if (!$view_data) {
  header("Location: konfirmasi-pembayaran");
  exit();
}

$waktu_bayar_value = !empty($view_data['waktu_bayar']) ? date('Y-m-d\TH:i', strtotime($view_data['waktu_bayar'])) : '';
$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Ubah Konfirmasi Pembayaran";
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
        <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] . ' ' . htmlspecialchars($view_data["order_id"]) ?></li>
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
              <input type="hidden" name="id" value="<?= $view_data['id'] ?>">
              <input type="hidden" name="order_id" value="<?= htmlspecialchars($view_data['order_id']) ?>">

              <h6 class="mb-3">Data Pembayaran</h6>
              <div class="row">
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label for="order_id" class="form-label">Order ID</label>
                    <input type="text" class="form-control" id="order_id" value="<?= htmlspecialchars($view_data['order_id']) ?>" readonly>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label for="snap_token" class="form-label">Snap Token</label>
                    <input type="text" class="form-control" id="snap_token" value="<?= htmlspecialchars($view_data['snap_token'] ?: '-') ?>" readonly>
                  </div>
                </div>
              </div>

              <h6 class="mb-3 mt-2">Data Pemesanan Tiket</h6>
              <div class="row">
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label for="kode_booking" class="form-label">Kode Booking</label>
                    <input type="text" class="form-control" id="kode_booking" value="<?= htmlspecialchars($view_data['kode_booking'] ?: '-') ?>" readonly>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label for="total_tagihan" class="form-label">Total Tagihan</label>
                    <input type="text" class="form-control" id="total_tagihan" value="Rp <?= number_format((int) $view_data['total_tagihan'], 0, ',', '.') ?>" readonly>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label for="wisatawan" class="form-label">Wisatawan</label>
                    <input type="text" class="form-control" id="wisatawan" value="<?= htmlspecialchars($view_data['nama_wisatawan'] ?: '-') ?> - <?= htmlspecialchars($view_data['email'] ?: '-') ?>" readonly>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label for="objek_wisata" class="form-label">Objek Wisata</label>
                    <input type="text" class="form-control" id="objek_wisata" value="<?= htmlspecialchars($view_data['nama_wisata'] ?: '-') ?>" readonly>
                  </div>
                </div>
              </div>

              <h6 class="mb-3 mt-2">Ubah Data Pembayaran</h6>
              <div class="row">
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                    <select name="metode_pembayaran" class="form-select" id="metode_pembayaran">
                      <?php foreach (['' => 'Pilih metode', 'Bank Transfer' => 'Bank Transfer', 'E-Wallet' => 'E-Wallet', 'QRIS' => 'QRIS', 'Tunai' => 'Tunai', 'Kartu Kredit' => 'Kartu Kredit'] as $value => $label) { ?>
                      <option value="<?= $value ?>" <?= ($view_data['metode_pembayaran'] == $value) ? 'selected' : '' ?>><?= $label ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label for="waktu_bayar" class="form-label">Waktu Bayar</label>
                    <input type="datetime-local" name="waktu_bayar" class="form-control" id="waktu_bayar" value="<?= $waktu_bayar_value ?>">
                  </div>
                </div>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="konfirmasi-pembayaran" class="btn btn-success">Kembali</a>
                <button type="submit" name="edit_pembayaran" class="btn btn-warning">Ubah</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->

</div>

<?php require_once("../../templates/views_bottom.php") ?>
