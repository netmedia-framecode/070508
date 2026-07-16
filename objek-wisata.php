<?php
require_once("config/Base.php");
require_once("sections/public_helpers.php");
require_once("controller/public-transaksi.php");

$fallbackImages = public_fallback_images();
$lokasiSelect = public_objek_location_select('objek_wisata');
$filterTipe = $_GET['tipe'] ?? '';
$filterId = (int) ($_GET['id'] ?? 0);
$kataKunci = trim($_GET['q'] ?? '');
$kataKunciSql = mysqli_real_escape_string($conn, $kataKunci);
$tanggalKunjungan = $_GET['tanggal'] ?? '';
$tanggalKunjungan = preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalKunjungan) && $tanggalKunjungan >= date('Y-m-d')
  ? $tanggalKunjungan
  : '';
$jumlahTiket = max(1, (int) ($_GET['tiket'] ?? 1));
$filterJudul = 'Semua Destinasi';
$filterConditions = [];

if ($filterTipe === 'desa' && $filterId > 0) {
  $filterData = public_rows($conn, "SELECT desa.nama, kecamatan.nama AS nama_kecamatan
    FROM desa JOIN kecamatan ON desa.kecamatan_id=kecamatan.id
    WHERE desa.id='$filterId' LIMIT 1");
  if ($filterData) {
    $filterConditions[] = "objek_wisata.desa_id='$filterId'";
    $filterJudul = 'Desa ' . $filterData[0]['nama'] . ', Kecamatan ' . $filterData[0]['nama_kecamatan'];
  }
} elseif ($filterTipe === 'kecamatan' && $filterId > 0) {
  $filterData = public_rows($conn, "SELECT nama FROM kecamatan WHERE id='$filterId' LIMIT 1");
  if ($filterData) {
    $filterConditions[] = "(desa_filter.kecamatan_id='$filterId' OR kelurahan_filter.kecamatan_id='$filterId')";
    $filterJudul = 'Kecamatan ' . $filterData[0]['nama'];
  }
}

if ($kataKunci !== '') {
  $filterConditions[] = "(
    objek_wisata.nama_wisata LIKE '%$kataKunciSql%'
    OR objek_wisata.deskripsi LIKE '%$kataKunciSql%'
    OR desa_filter.nama LIKE '%$kataKunciSql%'
    OR kelurahan_filter.nama LIKE '%$kataKunciSql%'
    OR kecamatan_desa_filter.nama LIKE '%$kataKunciSql%'
    OR kecamatan_kelurahan_filter.nama LIKE '%$kataKunciSql%'
  )";
  $filterJudul = 'Hasil pencarian “' . $kataKunci . '”';
}

$filterWhere = $filterConditions ? 'WHERE ' . implode(' AND ', $filterConditions) : '';

$destinasi = public_rows($conn, "SELECT objek_wisata.*, $lokasiSelect
  FROM objek_wisata
  LEFT JOIN desa AS desa_filter ON objek_wisata.desa_id=desa_filter.id
  LEFT JOIN kelurahan AS kelurahan_filter ON objek_wisata.kelurahan_id=kelurahan_filter.id
  LEFT JOIN kecamatan AS kecamatan_desa_filter ON desa_filter.kecamatan_id=kecamatan_desa_filter.id
  LEFT JOIN kecamatan AS kecamatan_kelurahan_filter ON kelurahan_filter.kecamatan_id=kecamatan_kelurahan_filter.id
  $filterWhere
  ORDER BY objek_wisata.nama_wisata ASC");
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <?php require_once("sections/public_head.php"); ?>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
  <?php require_once("sections/public_navbar.php"); ?>

  <main>
    <section class="bg-white">
      <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[.95fr_1.05fr] lg:px-8 lg:py-20">
        <div>
          <span class="inline-flex rounded-full bg-travel-sky px-4 py-2 text-sm font-extrabold text-travel-blue">Destinasi Wisata</span>
          <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-slate-950 sm:text-5xl">Pilih objek wisata terbaik di Sumba Barat Daya.</h1>
          <p class="mt-5 max-w-2xl text-base leading-8 text-slate-600">Lihat lokasi, jam operasional, harga tiket, dan detail destinasi sebelum membuat rencana kunjungan.</p>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <?php foreach (array_slice($fallbackImages, 0, 4) as $index => $image): ?>
            <img src="<?= htmlspecialchars($image) ?>" alt="Destinasi wisata" class="<?= $index % 2 == 1 ? 'mt-8 ' : '' ?>h-44 rounded-3xl object-cover shadow-sm ring-1 ring-slate-200 sm:h-56">
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
      <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
          <p class="text-sm font-extrabold uppercase tracking-wide text-travel-blue">Daftar Destinasi</p>
          <p class="mt-2 text-sm font-semibold text-slate-500"><?= htmlspecialchars($filterJudul) ?></p>
          <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950"><?= number_format(count($destinasi), 0, ',', '.') ?> objek wisata tersedia</h2>
        </div>
        <a href="<?= public_user() ? $baseURL . 'pesanan-saya' : $baseURL . 'auth/' ?>" class="inline-flex items-center justify-center rounded-2xl bg-travel-blue px-5 py-3 text-sm font-extrabold text-white shadow-sm hover:bg-blue-600">Pesan tiket</a>
      </div>

      <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php if (count($destinasi) > 0): ?>
          <?php foreach ($destinasi as $index => $item):
            $image = public_asset($baseURL, $item['gambar'] ?? '', $fallbackImages[$index % count($fallbackImages)]);
          ?>
            <article class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-soft">
              <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($item['nama_wisata']) ?>" class="h-60 w-full object-cover">
              <div class="p-5">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <h3 class="text-lg font-extrabold text-slate-950"><?= htmlspecialchars($item['nama_wisata']) ?></h3>
                    <p class="mt-1 text-sm font-medium text-slate-500"><?= htmlspecialchars($item['lokasi'] ?: 'Sumba Barat Daya') ?></p>
                  </div>
                  <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-extrabold text-travel-orange">Rp <?= number_format((int) $item['harga_tiket'], 0, ',', '.') ?></span>
                </div>
                <p class="mt-4 line-clamp-4 text-sm leading-7 text-slate-600"><?= htmlspecialchars($item['deskripsi'] ?: 'Informasi detail destinasi belum tersedia.') ?></p>
                <div class="mt-5 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4 text-sm">
                  <div class="rounded-2xl bg-slate-50 p-3">
                    <span class="block text-xs font-bold uppercase text-slate-400">Buka</span>
                    <span class="mt-1 block font-extrabold text-slate-800"><?= $item['jam_buka'] ? date('H:i', strtotime($item['jam_buka'])) : '-' ?></span>
                  </div>
                  <div class="rounded-2xl bg-slate-50 p-3">
                    <span class="block text-xs font-bold uppercase text-slate-400">Tutup</span>
                    <span class="mt-1 block font-extrabold text-slate-800"><?= $item['jam_tutup'] ? date('H:i', strtotime($item['jam_tutup'])) : '-' ?></span>
                  </div>
                </div>
                <div class="mt-5 grid gap-2 sm:grid-cols-2">
                  <?php
                    $bookingParams = ['objek' => (int) $item['id'], 'jumlah_tiket' => $jumlahTiket];
                    if ($tanggalKunjungan) {
                      $bookingParams['tgl_kunjungan'] = $tanggalKunjungan;
                    }
                    $bookingUrl = $baseURL . 'pesanan-saya?' . http_build_query($bookingParams);
                  ?>
                  <a href="<?= public_user() ? htmlspecialchars($bookingUrl) : $baseURL . 'auth/' ?>" class="inline-flex items-center justify-center rounded-2xl bg-travel-blue px-4 py-3 text-sm font-extrabold text-white hover:bg-blue-600">Pesan</a>
                  <form action="" method="post">
                    <input type="hidden" name="id_objek_wisata" value="<?= (int) $item['id'] ?>">
                    <input type="hidden" name="jumlah_tiket" value="<?= $jumlahTiket ?>">
                    <button type="submit" name="public_add_cart" class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-200 px-4 py-3 text-sm font-extrabold text-slate-700 hover:border-travel-blue hover:text-travel-blue">Keranjang</button>
                  </form>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="rounded-3xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200 md:col-span-2 lg:col-span-3">
            <h3 class="text-xl font-extrabold text-slate-950">Belum ada destinasi</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Data objek wisata akan muncul di halaman ini setelah ditambahkan dari dashboard.</p>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <?php require_once("sections/public_footer.php"); ?>
