<?php
require_once("config/Base.php");
require_once("sections/public_helpers.php");
require_once("controller/public-transaksi.php");
public_require_wisatawan($baseURL);

$id_user = public_user_id();

if (!empty($_GET["sync_order"])) {
  $syncResult = public_sync_midtrans_order($_GET["sync_order"], $id_user);
  if ($syncResult["success"]) {
    alert("Status pembayaran berhasil diperbarui.", "success");
  } else {
    alert($syncResult["message"] ?? "Status pembayaran belum bisa diperbarui.", "warning");
  }
  public_redirect("riwayat-pembayaran");
}

public_sync_user_pending_payments($id_user);
public_sync_expired_tickets($id_user);
$payments = public_user_payments($id_user);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <?php require_once("sections/public_head.php"); ?>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
  <?php require_once("sections/public_navbar.php"); ?>

  <main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <?php public_alert(); ?>

    <div class="mb-8">
      <span class="inline-flex rounded-full bg-travel-sky px-4 py-2 text-sm font-extrabold text-travel-blue">Pembayaran</span>
      <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-slate-950">Riwayat Pembayaran</h1>
    </div>

    <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-left text-sm">
          <thead class="bg-slate-50 text-xs uppercase text-slate-500">
            <tr>
              <th class="px-4 py-3">Order ID</th>
              <th class="px-4 py-3">Booking</th>
              <th class="px-4 py-3">Destinasi</th>
              <th class="px-4 py-3">Metode</th>
              <th class="px-4 py-3">Waktu Bayar</th>
              <th class="px-4 py-3">Total</th>
              <th class="px-4 py-3">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if (count($payments) > 0): ?>
              <?php foreach ($payments as $payment): ?>
                <tr>
                  <td class="px-4 py-4 font-bold text-slate-900"><?= htmlspecialchars($payment['order_id']) ?></td>
                  <td class="px-4 py-4"><?= htmlspecialchars($payment['kode_booking'] ?: '-') ?></td>
                  <td class="px-4 py-4"><?= htmlspecialchars($payment['nama_wisata'] ?: '-') ?></td>
                  <td class="px-4 py-4"><?= htmlspecialchars($payment['metode_pembayaran'] ?: '-') ?></td>
                  <td class="px-4 py-4"><?= $payment['waktu_bayar'] ? date('d M Y H:i', strtotime($payment['waktu_bayar'])) : '-' ?></td>
                  <td class="px-4 py-4 font-extrabold text-travel-orange">Rp <?= number_format((int) $payment['total_tagihan'], 0, ',', '.') ?></td>
                  <td class="px-4 py-4"><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-extrabold text-slate-700"><?= htmlspecialchars($payment['status_bayar'] ?: 'Unpaid') ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada riwayat pembayaran.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <?php require_once("sections/public_footer.php"); ?>
