<?php require_once("../../controller/manajemen-transaksi.php");
if (!isset($_GET["p"])) {
  header("Location: pemesanan-tiket");
  exit();
}

$id = valid($conn, $_GET["p"]);
$pull_data = "SELECT pemesanan_tiket.*, users.name AS nama_wisatawan, users.email, objek_wisata.nama_wisata, objek_wisata.harga_tiket
  FROM pemesanan_tiket
  LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
  LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
  WHERE pemesanan_tiket.id='$id'";
$store_data = mysqli_query($conn, $pull_data);
$view_data = mysqli_fetch_assoc($store_data);

if (!$view_data) {
  header("Location: pemesanan-tiket");
  exit();
}

$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Ubah Pemesanan Tiket";
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
        <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] . ' ' . htmlspecialchars($view_data["kode_booking"]) ?></li>
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
              <div class="row">
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label for="kode_booking" class="form-label">Kode Booking</label>
                    <input type="text" class="form-control" id="kode_booking" value="<?= htmlspecialchars($view_data['kode_booking']) ?>" readonly>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label for="waktu_pesan" class="form-label">Waktu Pesan</label>
                    <input type="text" class="form-control" id="waktu_pesan" value="<?= $view_data['waktu_pesan'] ? date('d-m-Y H:i', strtotime($view_data['waktu_pesan'])) : '-' ?>" readonly>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label for="wisatawan" class="form-label">Wisatawan</label>
                <input type="hidden" name="id_wisatawan" value="<?= $view_data['id_wisatawan'] ?>">
                <input type="text" class="form-control" id="wisatawan" value="<?= htmlspecialchars($view_data['nama_wisatawan'] ?: '-') ?> - <?= htmlspecialchars($view_data['email'] ?: '-') ?>" readonly>
              </div>
              <div class="mb-3">
                <label for="objek_wisata" class="form-label">Objek Wisata</label>
                <input type="hidden" name="id_objek_wisata" value="<?= $view_data['id_objek_wisata'] ?>">
                <input type="text" class="form-control" id="objek_wisata" value="<?= htmlspecialchars($view_data['nama_wisata'] ?: '-') ?> - Rp <?= number_format((int) $view_data['harga_tiket'], 0, ',', '.') ?>" readonly>
              </div>
              <div class="row">
                <div class="col-lg-4">
                  <div class="mb-3">
                    <label for="tgl_kunjungan" class="form-label">Tanggal Kunjungan</label>
                    <input type="date" name="tgl_kunjungan" class="form-control" id="tgl_kunjungan" value="<?= htmlspecialchars($view_data['tgl_kunjungan']) ?>" required>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="mb-3">
                    <label for="jumlah_tiket" class="form-label">Jumlah Tiket</label>
                    <input type="number" name="jumlah_tiket" class="form-control" id="jumlah_tiket" min="1" value="<?= (int) $view_data['jumlah_tiket'] ?>" required>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="mb-3">
                    <label for="total_tagihan_preview" class="form-label">Total Tagihan</label>
                    <input type="text" class="form-control" id="total_tagihan_preview" value="Rp <?= number_format((int) $view_data['total_tagihan'], 0, ',', '.') ?>" readonly>
                  </div>
                </div>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="pemesanan-tiket" class="btn btn-success">Kembali</a>
                <button type="submit" name="edit_pemesanan_tiket" class="btn btn-warning">Ubah</button>
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
  const jumlahTiket = document.getElementById('jumlah_tiket');
  const totalTagihan = document.getElementById('total_tagihan_preview');
  const hargaTiket = <?= (int) $view_data['harga_tiket'] ?>;

  const formatRupiah = function(value) {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      maximumFractionDigits: 0
    }).format(value);
  };

  const updateTotal = function() {
    const jumlah = parseInt(jumlahTiket.value || 0, 10);
    totalTagihan.value = formatRupiah(hargaTiket * jumlah);
  };

  jumlahTiket.addEventListener('input', updateTotal);
});
</script>

<?php require_once("../../templates/views_bottom.php") ?>
