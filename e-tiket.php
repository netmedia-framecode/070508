<?php
require_once("config/Base.php");
require_once("sections/public_helpers.php");
require_once("controller/public-transaksi.php");
public_require_wisatawan($baseURL);

$id_user = public_user_id();
public_sync_expired_tickets($id_user);
$tickets = public_user_tickets($id_user);
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
      <span class="inline-flex rounded-full bg-travel-sky px-4 py-2 text-sm font-extrabold text-travel-blue">E-Tiket</span>
      <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-slate-950">E-Tiket Saya</h1>
      <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Gunakan kode QR berikut saat berkunjung ke destinasi wisata.</p>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
      <?php if (count($tickets) > 0): ?>
        <?php foreach ($tickets as $ticket): ?>
          <article class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col justify-between gap-4 sm:flex-row">
              <div>
                <p class="text-sm font-bold text-travel-blue"><?= htmlspecialchars($ticket['kode_booking'] ?: '-') ?></p>
                <h2 class="mt-2 text-xl font-extrabold text-slate-950"><?= htmlspecialchars($ticket['nama_wisata'] ?: '-') ?></h2>
                <p class="mt-2 text-sm text-slate-500">Kunjungan: <?= $ticket['tgl_kunjungan'] ? date('d M Y', strtotime($ticket['tgl_kunjungan'])) : '-' ?></p>
              </div>
              <?php
                $ticketStatus = strtolower($ticket['status_tiket'] ?: 'active');
                $ticketBadge = $ticketStatus == 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700';
              ?>
              <span class="h-fit rounded-full px-3 py-1 text-xs font-extrabold <?= $ticketBadge ?>"><?= htmlspecialchars($ticket['status_tiket'] ?: 'Active') ?></span>
            </div>
            <div class="mt-6 rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-5 text-center">
              <div class="break-all text-2xl font-extrabold tracking-wide text-slate-950"><?= htmlspecialchars($ticket['kode_qr']) ?></div>
              <p class="mt-2 text-xs font-semibold text-slate-500">Kode ini dipindai oleh petugas di lokasi.</p>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-3">
              <div class="rounded-2xl bg-slate-50 p-4">
                <span class="text-xs font-bold uppercase text-slate-400">Tiket</span>
                <p class="mt-1 font-extrabold"><?= (int) $ticket['jumlah_tiket'] ?></p>
              </div>
              <div class="rounded-2xl bg-slate-50 p-4">
                <span class="text-xs font-bold uppercase text-slate-400">Berlaku</span>
                <p class="mt-1 font-extrabold"><?= $ticket['berlaku_sampai'] ? date('d M H:i', strtotime($ticket['berlaku_sampai'])) : '-' ?></p>
              </div>
              <div class="rounded-2xl bg-orange-50 p-4">
                <span class="text-xs font-bold uppercase text-orange-400">Total</span>
                <p class="mt-1 font-extrabold text-travel-orange">Rp <?= number_format((int) $ticket['total_tagihan'], 0, ',', '.') ?></p>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="rounded-3xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200 md:col-span-2">
          <h2 class="text-xl font-extrabold text-slate-950">Belum ada e-tiket</h2>
          <p class="mt-2 text-sm leading-6 text-slate-600">E-tiket akan muncul setelah pembayaran berhasil dikonfirmasi Midtrans.</p>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <?php require_once("sections/public_footer.php"); ?>
