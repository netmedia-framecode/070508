<?php
require_once("config/Base.php");
require_once("sections/public_helpers.php");

$fallbackImages = public_fallback_images();
$galeri = public_rows($conn, "SELECT galeri.*, objek_wisata.nama_wisata
  FROM galeri
  LEFT JOIN objek_wisata ON galeri.objek_wisata_id=objek_wisata.id
  ORDER BY galeri.id DESC");
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <?php require_once("sections/public_head.php"); ?>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
  <?php require_once("sections/public_navbar.php"); ?>

  <main>
    <section class="bg-slate-950 text-white">
      <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
          <span class="inline-flex rounded-full bg-white/10 px-4 py-2 text-sm font-extrabold text-blue-100 ring-1 ring-white/15">Galeri Wisata</span>
          <h1 class="mt-6 text-4xl font-extrabold tracking-tight sm:text-5xl">Lihat suasana destinasi sebelum berkunjung.</h1>
          <p class="mt-5 text-base leading-8 text-slate-300">Kumpulan foto destinasi wisata Sumba Barat Daya yang ditambahkan oleh pengelola.</p>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
      <?php if (count($galeri) > 0): ?>
        <div class="grid auto-rows-[220px] gap-4 md:grid-cols-3 lg:grid-cols-4">
          <?php foreach ($galeri as $index => $foto):
            $image = public_asset($baseURL, $foto['file_path'] ?? '', $fallbackImages[$index % count($fallbackImages)]);
            $spanClass = $index % 7 == 0 ? 'md:col-span-2 md:row-span-2' : '';
          ?>
            <figure class="group relative overflow-hidden rounded-3xl bg-slate-200 shadow-sm ring-1 ring-slate-200 <?= $spanClass ?>">
              <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($foto['judul'] ?: $foto['nama_wisata'] ?: 'Galeri Wisata') ?>" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
              <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/85 via-slate-950/35 to-transparent p-5 text-white">
                <h2 class="text-base font-extrabold"><?= htmlspecialchars($foto['judul'] ?: 'Galeri Wisata') ?></h2>
                <p class="mt-1 text-sm font-medium text-slate-200"><?= htmlspecialchars($foto['nama_wisata'] ?: 'Sumba Barat Daya') ?></p>
              </figcaption>
            </figure>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="grid auto-rows-[220px] gap-4 md:grid-cols-3 lg:grid-cols-4">
          <?php foreach ($fallbackImages as $index => $image): ?>
            <figure class="relative overflow-hidden rounded-3xl bg-slate-200 shadow-sm ring-1 ring-slate-200 <?= $index == 0 ? 'md:col-span-2 md:row-span-2' : '' ?>">
              <img src="<?= htmlspecialchars($image) ?>" alt="Galeri wisata" class="h-full w-full object-cover">
              <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/85 to-transparent p-5 text-white">
                <h2 class="text-base font-extrabold">Galeri Wisata <?= $index + 1 ?></h2>
                <p class="mt-1 text-sm font-medium text-slate-200">Tambahkan foto dari dashboard</p>
              </figcaption>
            </figure>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <?php require_once("sections/public_footer.php"); ?>
