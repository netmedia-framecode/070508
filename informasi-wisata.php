<?php
require_once("config/Base.php");
require_once("sections/public_helpers.php");

$informasi = public_rows($conn, "SELECT informasi_wisata.*, users.name AS nama_user
  FROM informasi_wisata
  LEFT JOIN users ON informasi_wisata.id_user=users.id_user
  ORDER BY informasi_wisata.tgl_posting DESC, informasi_wisata.id DESC");
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <?php require_once("sections/public_head.php"); ?>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
  <?php require_once("sections/public_navbar.php"); ?>

  <main>
    <section class="border-b border-slate-200 bg-white">
      <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <span class="inline-flex rounded-full bg-travel-sky px-4 py-2 text-sm font-extrabold text-travel-blue">Informasi Wisata</span>
        <div class="mt-6 grid gap-8 lg:grid-cols-[.9fr_1.1fr] lg:items-end">
          <div>
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-950 sm:text-5xl">Panduan dan kabar terbaru untuk perjalananmu.</h1>
          </div>
          <p class="text-base leading-8 text-slate-600">Baca informasi seputar destinasi, panduan kunjungan, dan pengumuman terbaru dari pengelola wisata Sumba Barat Daya.</p>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
      <div class="grid gap-6 lg:grid-cols-3">
        <?php if (count($informasi) > 0): ?>
          <?php foreach ($informasi as $index => $info): ?>
            <article class="<?= $index == 0 ? 'lg:col-span-2 lg:row-span-2 ' : '' ?>rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-1 hover:shadow-soft">
              <div class="flex flex-wrap items-center gap-2 text-xs font-bold uppercase tracking-wide text-slate-500">
                <span class="rounded-full bg-travel-sky px-3 py-1 text-travel-blue"><?= $info['tgl_posting'] ? date('d M Y', strtotime($info['tgl_posting'])) : date('d M Y') ?></span>
                <span>Oleh <?= htmlspecialchars($info['nama_user'] ?: 'Admin') ?></span>
              </div>
              <h2 class="mt-5 <?= $index == 0 ? 'text-3xl' : 'text-xl' ?> font-extrabold tracking-tight text-slate-950"><?= htmlspecialchars($info['judul']) ?></h2>
              <p class="mt-4 <?= $index == 0 ? 'line-clamp-4' : 'line-clamp-3' ?> text-sm leading-7 text-slate-600"><?= htmlspecialchars(strip_tags($info['konten'] ?: '-')) ?></p>
              <div class="mt-6 inline-flex rounded-2xl bg-slate-50 px-4 py-2 text-sm font-bold text-slate-700">Informasi Publik</div>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="rounded-3xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200 lg:col-span-3">
            <h2 class="text-xl font-extrabold text-slate-950">Belum ada informasi wisata</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Artikel dan pengumuman akan muncul setelah ditambahkan dari dashboard.</p>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <?php require_once("sections/public_footer.php"); ?>
