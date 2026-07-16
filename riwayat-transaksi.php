<?php
require_once("config/Base.php");
require_once("sections/public_helpers.php");
require_once("controller/public-transaksi.php");
public_require_wisatawan($baseURL);

$histories = public_user_history(public_user_id());
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <?php require_once("sections/public_head.php"); ?>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
  <?php require_once("sections/public_navbar.php"); ?>

  <main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="mb-8">
      <span class="inline-flex rounded-full bg-travel-sky px-4 py-2 text-sm font-extrabold text-travel-blue">Riwayat</span>
      <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-slate-950">Riwayat Transaksi</h1>
      <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Pantau perubahan akhir dari setiap pemesanan tiket wisata.</p>
    </div>

    <section class="grid gap-4">
      <?php if (count($histories) > 0): ?>
        <?php foreach ($histories as $history): ?>
          <article class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
              <div>
                <p class="text-sm font-bold text-travel-blue"><?= htmlspecialchars($history['kode_booking'] ?: '-') ?></p>
                <h2 class="mt-1 text-xl font-extrabold text-slate-950"><?= htmlspecialchars($history['nama_wisata'] ?: '-') ?></h2>
                <p class="mt-2 text-sm text-slate-500">Tanggal kunjungan: <?= $history['tgl_kunjungan'] ? date('d M Y', strtotime($history['tgl_kunjungan'])) : '-' ?></p>
              </div>
              <span class="h-fit rounded-full bg-slate-100 px-4 py-2 text-sm font-extrabold text-slate-700"><?= htmlspecialchars($history['status_akhir']) ?></span>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-3">
              <div class="rounded-2xl bg-slate-50 p-4">
                <span class="text-xs font-bold uppercase text-slate-400">Tiket</span>
                <p class="mt-1 font-extrabold"><?= (int) $history['jumlah_tiket'] ?></p>
              </div>
              <div class="rounded-2xl bg-orange-50 p-4">
                <span class="text-xs font-bold uppercase text-orange-400">Total</span>
                <p class="mt-1 font-extrabold text-travel-orange">Rp <?= number_format((int) $history['total_tagihan'], 0, ',', '.') ?></p>
              </div>
              <div class="rounded-2xl bg-slate-50 p-4">
                <span class="text-xs font-bold uppercase text-slate-400">Update</span>
                <p class="mt-1 font-extrabold"><?= $history['tanggal_selesai'] ? date('d M Y H:i', strtotime($history['tanggal_selesai'])) : '-' ?></p>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="rounded-3xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200">
          <h2 class="text-xl font-extrabold text-slate-950">Belum ada riwayat transaksi</h2>
          <p class="mt-2 text-sm leading-6 text-slate-600">Riwayat akan muncul setelah Anda membuat pemesanan.</p>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <?php require_once("sections/public_footer.php"); ?>
