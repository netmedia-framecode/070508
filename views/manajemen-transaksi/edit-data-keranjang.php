<?php require_once("../../controller/manajemen-transaksi.php");
if (!isset($_GET["p"])) {
  header("Location: data-keranjang");
  exit();
}

$id = valid($conn, $_GET["p"]);
$pull_data = "SELECT keranjang.*, users.name AS nama_wisatawan, users.email, objek_wisata.nama_wisata, objek_wisata.harga_tiket
  FROM keranjang
  LEFT JOIN users ON keranjang.id_wisatawan=users.id_user
  LEFT JOIN user_role ON users.id_role=user_role.id_role
  LEFT JOIN objek_wisata ON keranjang.id_objek_wisata=objek_wisata.id
  WHERE keranjang.id='$id'";
$store_data = mysqli_query($conn, $pull_data);
$view_data = mysqli_fetch_assoc($store_data);

if (!$view_data) {
  header("Location: data-keranjang");
  exit();
}

$_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Ubah Data Keranjang";
require_once("../../templates/views_top.php"); ?>

<div class="nxl-content">

  <!-- [ page-header ] start -->
  <div class="page-header">
    <div class="page-header-left d-flex align-items-center">
      <div class="page-header-title">
        <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
      </div>
      <ul class="breadcrumb">
        <li class="breadcrumb-item">Data Keranjang</li>
        <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] . ' ' . htmlspecialchars($view_data["nama_wisatawan"] ?: 'User #' . $view_data['id_wisatawan']) ?></li>
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
                    <label for="nama_wisatawan" class="form-label">Wisatawan</label>
                    <input type="text" class="form-control" id="nama_wisatawan" value="<?= htmlspecialchars($view_data['nama_wisatawan'] ?: 'User #' . $view_data['id_wisatawan']) ?>" readonly>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($view_data['email'] ?: '-') ?>" readonly>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label for="id_objek_wisata" class="form-label">Objek Wisata</label>
                <select name="id_objek_wisata" class="form-select" id="id_objek_wisata" required>
                  <option value="">Pilih objek wisata</option>
                  <?php if ($views_objek_wisata instanceof mysqli_result) {
                    foreach ($views_objek_wisata as $objek_wisata) { ?>
                  <option value="<?= $objek_wisata['id'] ?>" data-harga="<?= (int) $objek_wisata['harga_tiket'] ?>" <?= ($view_data['id_objek_wisata'] == $objek_wisata['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($objek_wisata['nama_wisata']) ?> - Rp <?= number_format((int) $objek_wisata['harga_tiket'], 0, ',', '.') ?>
                  </option>
                  <?php }
                  } ?>
                </select>
              </div>
              <div class="row">
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label for="jumlah_tiket" class="form-label">Jumlah Tiket</label>
                    <input type="number" name="jumlah_tiket" class="form-control" id="jumlah_tiket" min="1" value="<?= (int) $view_data['jumlah_tiket'] ?>" required>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="mb-3">
                    <label for="total_harga_sementara" class="form-label">Total Sementara</label>
                    <input type="text" class="form-control" id="total_harga_sementara" value="Rp <?= number_format((int) $view_data['total_harga_sementara'], 0, ',', '.') ?>" readonly>
                  </div>
                </div>
              </div>
              <div class="mb-3 hstack gap-2 justify-content-left">
                <a href="data-keranjang" class="btn btn-success">Kembali</a>
                <button type="submit" name="edit_keranjang" class="btn btn-warning">Ubah</button>
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
  const totalHarga = document.getElementById('total_harga_sementara');

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
    totalHarga.value = formatRupiah(harga * jumlah);
  };

  objekWisata.addEventListener('change', updateTotal);
  jumlahTiket.addEventListener('input', updateTotal);
});
</script>

<?php require_once("../../templates/views_bottom.php") ?>
