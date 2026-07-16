<?php
require_once("config/Base.php");
require_once("sections/public_helpers.php");

$lokasiSelect = public_objek_location_select('objek_wisata');
$destinasi = public_rows($conn, "SELECT objek_wisata.*, $lokasiSelect FROM objek_wisata ORDER BY id DESC LIMIT 6");
$informasi = public_rows($conn, "SELECT informasi_wisata.*, users.name AS nama_user
  FROM informasi_wisata
  LEFT JOIN users ON informasi_wisata.id_user=users.id_user
  ORDER BY informasi_wisata.tgl_posting DESC, informasi_wisata.id DESC
  LIMIT 3");
$galeri = public_rows($conn, "SELECT galeri.*, objek_wisata.nama_wisata
  FROM galeri
  LEFT JOIN objek_wisata ON galeri.objek_wisata_id=objek_wisata.id
  ORDER BY galeri.id DESC
  LIMIT 8");

$stats = [
  ['label' => 'Destinasi', 'value' => public_count($conn, 'objek_wisata')],
  ['label' => 'Galeri', 'value' => public_count($conn, 'galeri')],
  ['label' => 'Informasi', 'value' => public_count($conn, 'informasi_wisata')],
  ['label' => 'Kunjungan', 'value' => public_count($conn, 'data_kunjungan')]
];

$fallbackImages = public_fallback_images();
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <?php require_once("sections/public_head.php"); ?>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
  <?php require_once("sections/public_navbar.php"); ?>

  <main>
    <section class="relative overflow-hidden bg-travel-sky">
      <div class="absolute inset-0">
        <img src="<?= $fallbackImages[0] ?>" alt="Pemandangan wisata Sumba Barat Daya" class="h-full w-full object-cover opacity-25">
      </div>
      <div class="relative mx-auto max-w-7xl px-4 pb-24 pt-14 sm:px-6 lg:px-8 lg:pb-28 lg:pt-20">
        <div class="max-w-3xl">
          <span class="inline-flex rounded-full bg-white/90 px-4 py-2 text-sm font-bold text-travel-blue shadow-sm">Wisata Sumba Barat Daya</span>
          <h1 class="mt-6 max-w-4xl text-4xl font-extrabold tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
            Temukan tiket dan destinasi terbaik untuk liburanmu.
          </h1>
          <p class="mt-5 max-w-2xl text-base leading-8 text-slate-700 sm:text-lg">
            Cari objek wisata, lihat informasi terbaru, pesan tiket, dan gunakan e-tiket saat berkunjung ke destinasi pilihan di Sumba Barat Daya.
          </p>
        </div>

        <form action="<?= $baseURL ?>objek-wisata" method="get" class="mt-10 max-w-5xl rounded-3xl bg-white p-4 shadow-soft ring-1 ring-slate-200">
          <div class="grid gap-3 md:grid-cols-[1.4fr_1fr_1fr_auto]">
            <label class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
              <span class="block text-xs font-bold uppercase tracking-wide text-slate-500">Mau kemana?</span>
              <input type="search" name="q" placeholder="Cari wisata, desa, kecamatan..." class="mt-1 w-full bg-transparent text-sm font-semibold text-slate-900 outline-none placeholder:text-slate-400">
            </label>
            <label class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
              <span class="block text-xs font-bold uppercase tracking-wide text-slate-500">Tanggal</span>
              <input type="date" name="tanggal" min="<?= date('Y-m-d') ?>" class="mt-1 w-full bg-transparent text-sm font-semibold text-slate-900 outline-none">
            </label>
            <label class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
              <span class="block text-xs font-bold uppercase tracking-wide text-slate-500">Tiket</span>
              <input type="number" name="tiket" min="1" value="1" class="mt-1 w-full bg-transparent text-sm font-semibold text-slate-900 outline-none">
            </label>
            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-travel-orange px-7 py-4 text-sm font-extrabold text-white shadow-lg shadow-orange-500/20 hover:bg-orange-600">
              Cari Tiket
            </button>
          </div>
        </form>

        <div class="mt-8 grid max-w-4xl gap-3 sm:grid-cols-4">
          <?php foreach ($stats as $stat): ?>
            <div class="rounded-2xl bg-white/90 p-4 shadow-sm ring-1 ring-slate-200">
              <div class="text-2xl font-extrabold text-slate-950"><?= number_format($stat['value'], 0, ',', '.') ?></div>
              <div class="mt-1 text-sm font-semibold text-slate-500"><?= htmlspecialchars($stat['label']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="relative z-10 mx-auto mt-8 max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="grid gap-4 rounded-3xl bg-white p-5 shadow-soft ring-1 ring-slate-200 md:grid-cols-4">
        <a href="<?= $baseURL ?>objek-wisata" class="rounded-2xl border border-slate-200 p-5 hover:border-travel-blue hover:bg-blue-50">
          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-travel-blue">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 21s7-4.5 7-11a7 7 0 1 0-14 0c0 6.5 7 11 7 11Z"></path>
              <path d="M12 10.5h.01"></path>
            </svg>
          </div>
          <h3 class="mt-3 font-extrabold text-slate-900">Destinasi</h3>
          <p class="mt-1 text-sm text-slate-500">Pilih objek wisata favorit.</p>
        </a>
        <a href="<?= public_user() ? $baseURL . 'pesanan-saya' : $baseURL . 'auth/' ?>" class="rounded-2xl border border-slate-200 p-5 hover:border-travel-blue hover:bg-blue-50">
          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-orange-50 text-travel-orange">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 9a3 3 0 0 0 0 6v3a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3a3 3 0 0 0 0-6V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v3Z"></path>
              <path d="M13 5v14"></path>
            </svg>
          </div>
          <h3 class="mt-3 font-extrabold text-slate-900">Pesan Tiket</h3>
          <p class="mt-1 text-sm text-slate-500"><?= public_user() ? 'Kelola dan bayar pesanan.' : 'Masuk untuk mulai memesan.' ?></p>
        </a>
        <a href="<?= $baseURL ?>informasi-wisata" class="rounded-2xl border border-slate-200 p-5 hover:border-travel-blue hover:bg-blue-50">
          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="9"></circle>
              <path d="m15.5 8.5-2 5-5 2 2-5 5-2Z"></path>
            </svg>
          </div>
          <h3 class="mt-3 font-extrabold text-slate-900">Info Wisata</h3>
          <p class="mt-1 text-sm text-slate-500">Baca kabar dan panduan.</p>
        </a>
        <a href="<?= $baseURL ?>galeri" class="rounded-2xl border border-slate-200 p-5 hover:border-travel-blue hover:bg-blue-50">
          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="5" width="18" height="14" rx="2"></rect>
              <circle cx="12" cy="12" r="3"></circle>
              <path d="M8 5l1.5-2h5L16 5"></path>
            </svg>
          </div>
          <h3 class="mt-3 font-extrabold text-slate-900">Galeri</h3>
          <p class="mt-1 text-sm text-slate-500">Lihat suasana destinasi.</p>
        </a>
      </div>
    </section>

    <section id="destinasi" class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
      <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
          <p class="text-sm font-extrabold uppercase tracking-wide text-travel-blue">Destinasi Populer</p>
          <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">Objek wisata pilihan</h2>
        </div>
        <a href="<?= public_user() ? $baseURL . 'pesanan-saya' : $baseURL . 'auth/' ?>" class="text-sm font-bold text-travel-blue hover:text-blue-700"><?= public_user() ? 'Lihat pesanan saya' : 'Masuk untuk pesan tiket' ?></a>
      </div>

      <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php if (count($destinasi) > 0): ?>
          <?php foreach ($destinasi as $index => $item):
            $image = public_asset($baseURL, $item['gambar'] ?? '', $fallbackImages[$index % count($fallbackImages)]);
          ?>
            <article class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-soft">
              <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($item['nama_wisata']) ?>" class="h-56 w-full object-cover">
              <div class="p-5">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <h3 class="text-lg font-extrabold text-slate-950"><?= htmlspecialchars($item['nama_wisata']) ?></h3>
                    <p class="mt-1 text-sm text-slate-500"><?= htmlspecialchars($item['lokasi'] ?: 'Sumba Barat Daya') ?></p>
                  </div>
                  <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-extrabold text-travel-blue">Tiket</span>
                </div>
                <p class="mt-4 line-clamp-2 text-sm leading-6 text-slate-600"><?= htmlspecialchars($item['deskripsi'] ?: 'Destinasi wisata menarik untuk rencana perjalanan Anda.') ?></p>
                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                  <div>
                    <span class="block text-xs font-semibold text-slate-500">Mulai dari</span>
                    <span class="text-lg font-extrabold text-travel-orange">Rp <?= number_format((int) $item['harga_tiket'], 0, ',', '.') ?></span>
                  </div>
                  <a href="<?= public_user() ? $baseURL . 'pesanan-saya?objek=' . $item['id'] : $baseURL . 'auth/' ?>" class="rounded-xl bg-travel-blue px-4 py-2 text-sm font-bold text-white hover:bg-blue-600">Pesan</a>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <?php foreach ($fallbackImages as $index => $image): ?>
            <article class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
              <img src="<?= $image ?>" alt="Destinasi wisata" class="h-56 w-full object-cover">
              <div class="p-5">
                <h3 class="text-lg font-extrabold text-slate-950">Destinasi Sumba <?= $index + 1 ?></h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Tambahkan data objek wisata dari dashboard untuk menampilkan destinasi asli di halaman ini.</p>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
      <div class="overflow-hidden rounded-[2rem] bg-gradient-to-r from-travel-blue to-blue-700 p-8 text-white shadow-soft md:p-10">
        <div class="grid items-center gap-8 md:grid-cols-[1.2fr_.8fr]">
          <div>
            <p class="text-sm font-bold uppercase tracking-wide text-blue-100">Mudah untuk wisatawan</p>
            <h2 class="mt-2 text-3xl font-extrabold">Satu akun untuk pesan tiket dan akses e-tiket.</h2>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-blue-50">Daftar sebagai wisatawan, pilih destinasi, lakukan pemesanan, dan gunakan kode QR e-tiket saat tiba di lokasi wisata.</p>
          </div>
          <div class="rounded-3xl bg-white p-5 text-slate-900">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
              <span class="font-extrabold">Alur Cepat</span>
              <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-extrabold text-travel-orange">Online</span>
            </div>
            <div class="mt-4 space-y-3 text-sm font-semibold text-slate-700">
              <div class="flex items-center gap-3"><span class="h-2 w-2 rounded-full bg-travel-blue"></span> Pilih destinasi wisata</div>
              <div class="flex items-center gap-3"><span class="h-2 w-2 rounded-full bg-travel-blue"></span> Pesan tiket kunjungan</div>
              <div class="flex items-center gap-3"><span class="h-2 w-2 rounded-full bg-travel-blue"></span> Tunjukkan QR saat berkunjung</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="informasi" class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
      <div>
        <p class="text-sm font-extrabold uppercase tracking-wide text-travel-blue">Informasi Wisata</p>
        <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">Panduan dan kabar terbaru</h2>
      </div>
      <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <?php if (count($informasi) > 0): ?>
          <?php foreach ($informasi as $info): ?>
            <article class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
              <div class="text-sm font-bold text-travel-blue"><?= $info['tgl_posting'] ? date('d M Y', strtotime($info['tgl_posting'])) : date('d M Y') ?></div>
              <h3 class="mt-3 text-lg font-extrabold text-slate-950"><?= htmlspecialchars($info['judul']) ?></h3>
              <p class="mt-3 line-clamp-4 text-sm leading-7 text-slate-600"><?= htmlspecialchars(strip_tags($info['konten'] ?: '-')) ?></p>
              <div class="mt-5 text-xs font-semibold text-slate-500">Oleh <?= htmlspecialchars($info['nama_user'] ?: 'Admin') ?></div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="rounded-3xl bg-white p-6 text-sm text-slate-600 shadow-sm ring-1 ring-slate-200 lg:col-span-3">Belum ada informasi wisata yang dipublikasikan.</div>
        <?php endif; ?>
      </div>
    </section>

    <section id="galeri" class="mx-auto max-w-7xl px-4 pt-20 sm:px-6 lg:px-8">
      <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
          <p class="text-sm font-extrabold uppercase tracking-wide text-travel-blue">Galeri</p>
          <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">Cerita visual perjalanan</h2>
        </div>
      </div>
      <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-4">
        <?php if (count($galeri) > 0): ?>
          <?php foreach ($galeri as $index => $foto):
            $image = public_asset($baseURL, $foto['file_path'] ?? '', $fallbackImages[$index % count($fallbackImages)]);
          ?>
            <figure class="group relative overflow-hidden rounded-3xl bg-slate-200">
              <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($foto['judul'] ?: $foto['nama_wisata']) ?>" class="h-48 w-full object-cover transition duration-300 group-hover:scale-105 md:h-56">
              <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/80 to-transparent p-4 text-sm font-bold text-white">
                <?= htmlspecialchars($foto['judul'] ?: $foto['nama_wisata'] ?: 'Galeri Wisata') ?>
              </figcaption>
            </figure>
          <?php endforeach; ?>
        <?php else: ?>
          <?php foreach (array_slice($fallbackImages, 0, 4) as $index => $image): ?>
            <figure class="group relative overflow-hidden rounded-3xl bg-slate-200">
              <img src="<?= $image ?>" alt="Galeri wisata" class="h-48 w-full object-cover md:h-56">
              <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/80 to-transparent p-4 text-sm font-bold text-white">Galeri Wisata <?= $index + 1 ?></figcaption>
            </figure>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <?php require_once("sections/public_footer.php"); ?>
