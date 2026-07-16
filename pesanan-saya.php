<?php
require_once("config/Base.php");
require_once("sections/public_helpers.php");
require_once("controller/public-transaksi.php");
public_require_wisatawan($baseURL);

$id_user = public_user_id();
$fallbackImages = public_fallback_images();
$selectedObjekId = (int) ($_GET["objek"] ?? 0);
$checkoutId = (int) ($_GET["checkout"] ?? 0);
$selectedObjek = null;
$selectedTanggal = $_GET['tgl_kunjungan'] ?? '';
$selectedTanggal = preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedTanggal) && $selectedTanggal >= date('Y-m-d') ? $selectedTanggal : '';
$selectedJumlahTiket = max(1, (int) ($_GET['jumlah_tiket'] ?? 1));
$checkoutOrder = null;
$checkoutPayment = null;
$checkoutError = "";
$autoOpenSnap = false;

if (!empty($_GET["sync_order"])) {
  $syncResult = public_sync_midtrans_order($_GET["sync_order"], $id_user);
  if ($syncResult["success"]) {
    alert("Status pembayaran berhasil disinkronkan.", "success");
    public_redirect("pesanan-saya");
  } else {
    alert($syncResult["message"] ?? "Status pembayaran belum bisa disinkronkan.", "warning");
    public_redirect("pesanan-saya");
  }
}

public_sync_user_pending_payments($id_user);

if ($selectedObjekId > 0) {
  $lokasiSelect = public_objek_location_select('objek_wisata');
  $querySelected = mysqli_query($conn, "SELECT objek_wisata.*, $lokasiSelect FROM objek_wisata WHERE id='$selectedObjekId' LIMIT 1");
  $selectedObjek = mysqli_fetch_assoc($querySelected);
}

if ($checkoutId > 0) {
  $lokasiSelect = public_objek_location_select('objek_wisata');
  $queryCheckout = mysqli_query($conn, "SELECT pemesanan_tiket.*, objek_wisata.nama_wisata, $lokasiSelect, users.name, users.email
    FROM pemesanan_tiket
    LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
    LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
    WHERE pemesanan_tiket.id='$checkoutId' AND pemesanan_tiket.id_wisatawan='$id_user'
    LIMIT 1");
  $checkoutOrder = mysqli_fetch_assoc($queryCheckout);
  if ($checkoutOrder) {
    $paymentResult = public_get_or_create_payment($checkoutId);
    if ($paymentResult["success"]) {
      $checkoutPayment = $paymentResult["payment"];
      $autoOpenSnap = (int) ($_SESSION["project_wisata_sumba_barat_daya"]["auto_checkout_order"] ?? 0) == $checkoutId;
      unset($_SESSION["project_wisata_sumba_barat_daya"]["auto_checkout_order"]);
    } else {
      $checkoutError = $paymentResult["message"];
    }
  }
}

public_sync_expired_tickets($id_user);
$orders = public_user_orders($id_user);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <?php require_once("sections/public_head.php"); ?>
  <?php if ($checkoutPayment && !empty($midtrans_client_key)): ?>
    <script src="<?= htmlspecialchars($midtrans_snap_js_url) ?>" data-client-key="<?= htmlspecialchars($midtrans_client_key) ?>"></script>
  <?php endif; ?>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
  <?php require_once("sections/public_navbar.php"); ?>

  <main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <?php public_alert(); ?>

    <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
      <div>
        <span class="inline-flex rounded-full bg-travel-sky px-4 py-2 text-sm font-extrabold text-travel-blue">Transaksi Wisatawan</span>
        <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-slate-950">Pesanan Saya</h1>
        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Buat pemesanan tiket, lanjutkan checkout, dan pantau status pesanan wisata Anda.</p>
      </div>
      <a href="<?= $baseURL ?>objek-wisata" class="inline-flex items-center justify-center rounded-2xl bg-travel-blue px-5 py-3 text-sm font-extrabold text-white hover:bg-blue-600">Pilih Destinasi</a>
    </div>

    <?php if ($selectedObjek): ?>
      <section class="mb-8 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="grid gap-6 lg:grid-cols-[.8fr_1fr]">
          <img src="<?= htmlspecialchars(public_asset($baseURL, $selectedObjek['gambar'] ?? '', $fallbackImages[0])) ?>" alt="<?= htmlspecialchars($selectedObjek['nama_wisata']) ?>" class="h-72 w-full rounded-3xl object-cover">
          <div>
            <h2 class="text-2xl font-extrabold text-slate-950"><?= htmlspecialchars($selectedObjek['nama_wisata']) ?></h2>
            <p class="mt-2 text-sm font-semibold text-slate-500"><?= htmlspecialchars($selectedObjek['lokasi'] ?: 'Sumba Barat Daya') ?></p>
            <p class="mt-4 line-clamp-3 text-sm leading-7 text-slate-600"><?= htmlspecialchars($selectedObjek['deskripsi'] ?: 'Lengkapi detail kunjungan untuk membuat pesanan tiket.') ?></p>
            <form action="" method="post" class="mt-6 grid gap-4 sm:grid-cols-3">
              <input type="hidden" name="id_objek_wisata" value="<?= (int) $selectedObjek['id'] ?>">
              <label class="sm:col-span-1">
                <span class="text-xs font-bold uppercase text-slate-500">Tanggal</span>
                <input type="date" name="tgl_kunjungan" required min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($selectedTanggal) ?>" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold outline-none focus:border-travel-blue">
              </label>
              <label>
                <span class="text-xs font-bold uppercase text-slate-500">Jumlah Tiket</span>
                <input type="number" name="jumlah_tiket" min="1" value="<?= $selectedJumlahTiket ?>" required class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold outline-none focus:border-travel-blue">
              </label>
              <div>
                <span class="text-xs font-bold uppercase text-slate-500">Harga</span>
                <div class="mt-2 rounded-2xl bg-orange-50 px-4 py-3 text-sm font-extrabold text-travel-orange">Rp <?= number_format((int) $selectedObjek['harga_tiket'], 0, ',', '.') ?></div>
              </div>
              <button type="submit" name="public_create_order" class="sm:col-span-3 rounded-2xl bg-travel-orange px-5 py-3 text-sm font-extrabold text-white hover:bg-orange-600">Buat Pesanan dan Checkout</button>
            </form>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <?php if ($checkoutOrder): ?>
      <section id="checkout" class="mb-8 scroll-mt-24 rounded-3xl bg-white p-6 shadow-soft ring-1 ring-slate-200">
        <div class="grid gap-6 lg:grid-cols-[1fr_.75fr] lg:items-center">
          <div>
            <p class="text-sm font-extrabold uppercase tracking-wide text-travel-blue">Checkout Midtrans</p>
            <h2 class="mt-2 text-2xl font-extrabold text-slate-950"><?= htmlspecialchars($checkoutOrder['kode_booking']) ?> - <?= htmlspecialchars($checkoutOrder['nama_wisata']) ?></h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-3">
              <div class="rounded-2xl bg-slate-50 p-4">
                <span class="text-xs font-bold uppercase text-slate-400">Tanggal</span>
                <p class="mt-1 font-extrabold"><?= date('d M Y', strtotime($checkoutOrder['tgl_kunjungan'])) ?></p>
              </div>
              <div class="rounded-2xl bg-slate-50 p-4">
                <span class="text-xs font-bold uppercase text-slate-400">Tiket</span>
                <p class="mt-1 font-extrabold"><?= (int) $checkoutOrder['jumlah_tiket'] ?></p>
              </div>
              <div class="rounded-2xl bg-orange-50 p-4">
                <span class="text-xs font-bold uppercase text-orange-400">Tagihan</span>
                <p class="mt-1 font-extrabold text-travel-orange">Rp <?= number_format((int) $checkoutOrder['total_tagihan'], 0, ',', '.') ?></p>
              </div>
            </div>
          </div>
          <div class="rounded-3xl border border-slate-200 p-5">
            <?php if ($checkoutError): ?>
              <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold leading-7 text-red-800"><?= htmlspecialchars($checkoutError) ?></div>
            <?php elseif (empty($midtrans_client_key)): ?>
              <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold leading-7 text-amber-800">Client Key Midtrans belum diatur di config/Midtrans.php atau environment MIDTRANS_CLIENT_KEY.</div>
            <?php else: ?>
              <p class="text-sm leading-7 text-slate-600">Klik tombol di bawah untuk membuka popup pembayaran Midtrans Snap.</p>
              <button id="pay-button" type="button" class="mt-5 w-full rounded-2xl bg-travel-blue px-5 py-3 text-sm font-extrabold text-white hover:bg-blue-600">Bayar Sekarang</button>
            <?php endif; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <h2 class="text-xl font-extrabold text-slate-950">Daftar Pesanan</h2>
      <div class="mt-5 overflow-x-auto">
        <table class="w-full min-w-[820px] text-left text-sm">
          <thead class="bg-slate-50 text-xs uppercase text-slate-500">
            <tr>
              <th class="px-4 py-3">Kode</th>
              <th class="px-4 py-3">Destinasi</th>
              <th class="px-4 py-3">Tanggal</th>
              <th class="px-4 py-3">Tiket</th>
              <th class="px-4 py-3">Total</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if (count($orders) > 0): ?>
              <?php foreach ($orders as $order):
                $paymentStatus = strtolower($order['status_bayar'] ?? '');
                $ticketStatus = strtolower($order['status_tiket'] ?? '');
                $isPaid = in_array($paymentStatus, ['paid', 'settlement']);
                $isTicketActive = $ticketStatus == 'active';
                $isExpired = !empty($order['berlaku_sampai']) && strtotime($order['berlaku_sampai']) < time();
                $canShowQr = $isPaid && $isTicketActive && !$isExpired && !empty($order['kode_qr']);
              ?>
                <tr>
                  <td class="px-4 py-4 font-bold text-slate-900"><?= htmlspecialchars($order['kode_booking']) ?></td>
                  <td class="px-4 py-4"><?= htmlspecialchars($order['nama_wisata'] ?: '-') ?></td>
                  <td class="px-4 py-4"><?= $order['tgl_kunjungan'] ? date('d M Y', strtotime($order['tgl_kunjungan'])) : '-' ?></td>
                  <td class="px-4 py-4"><?= (int) $order['jumlah_tiket'] ?></td>
                  <td class="px-4 py-4 font-extrabold text-travel-orange">Rp <?= number_format((int) $order['total_tagihan'], 0, ',', '.') ?></td>
                  <td class="px-4 py-4"><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-extrabold text-slate-700"><?= htmlspecialchars($order['status_pemesanan']) ?></span></td>
                  <td class="px-4 py-4">
                    <?php if (!$isPaid): ?>
                      <form action="" method="post" class="inline">
                        <input type="hidden" name="id_pemesanan" value="<?= (int) $order['id'] ?>">
                        <button type="submit" name="public_checkout_order" class="rounded-xl bg-travel-blue px-3 py-2 text-xs font-extrabold text-white hover:bg-blue-600">Checkout</button>
                      </form>
                    <?php elseif ($canShowQr): ?>
                      <button type="button" onclick="document.getElementById('qr-dialog-<?= (int) $order['id'] ?>').showModal()" class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-extrabold text-white hover:bg-emerald-700">QR Code</button>
                      <dialog id="qr-dialog-<?= (int) $order['id'] ?>" class="w-[min(92vw,420px)] rounded-3xl p-0 shadow-soft backdrop:bg-slate-950/50">
                        <div class="bg-white p-6">
                          <div class="flex items-start justify-between gap-4">
                            <div>
                              <p class="text-sm font-bold text-travel-blue"><?= htmlspecialchars($order['kode_booking']) ?></p>
                              <h3 class="mt-1 text-xl font-extrabold text-slate-950"><?= htmlspecialchars($order['nama_wisata'] ?: '-') ?></h3>
                            </div>
                            <form method="dialog">
                              <button class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200" aria-label="Tutup">x</button>
                            </form>
                          </div>
                          <div class="mt-6 rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-5 text-center">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=<?= urlencode($order['kode_qr']) ?>" alt="QR Code <?= htmlspecialchars($order['kode_booking']) ?>" class="mx-auto h-64 w-64 rounded-2xl bg-white p-3">
                            <div class="mt-4 break-all rounded-2xl bg-white px-4 py-3 text-lg font-extrabold tracking-wide text-slate-950"><?= htmlspecialchars($order['kode_qr']) ?></div>
                          </div>
                          <div class="mt-5 grid gap-3 text-sm sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 p-4">
                              <span class="text-xs font-bold uppercase text-slate-400">Status Tiket</span>
                              <p class="mt-1 font-extrabold text-emerald-700"><?= htmlspecialchars($order['status_tiket']) ?></p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                              <span class="text-xs font-bold uppercase text-slate-400">Berlaku Sampai</span>
                              <p class="mt-1 font-extrabold"><?= $order['berlaku_sampai'] ? date('d M Y H:i', strtotime($order['berlaku_sampai'])) : '-' ?></p>
                            </div>
                          </div>
                          <p class="mt-5 text-center text-xs font-semibold leading-6 text-slate-500">Tunjukkan QR ini kepada petugas saat masuk objek wisata.</p>
                        </div>
                      </dialog>
                    <?php elseif ($isPaid && $isExpired): ?>
                      <span class="rounded-xl bg-red-50 px-3 py-2 text-xs font-extrabold text-red-700">Expired</span>
                    <?php elseif ($isPaid): ?>
                      <span class="text-xs font-bold text-emerald-600">Lunas</span>
                    <?php else: ?>
                      <span class="text-xs font-bold text-slate-500">-</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada pesanan tiket.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <?php if ($checkoutPayment && !empty($midtrans_client_key)): ?>
    <script>
      function openMidtransSnap() {
        if (!window.snap) {
          alert('Script Midtrans Snap belum berhasil dimuat. Periksa koneksi internet atau Client Key Midtrans.');
          return;
        }

        window.snap.pay('<?= htmlspecialchars($checkoutPayment['snap_token']) ?>', {
          onSuccess: function() { window.location.href = '<?= $baseURL ?>riwayat-pembayaran?sync_order=<?= urlencode($checkoutPayment['order_id']) ?>'; },
          onPending: function() { window.location.href = '<?= $baseURL ?>riwayat-pembayaran?sync_order=<?= urlencode($checkoutPayment['order_id']) ?>'; },
          onError: function() { window.location.href = '<?= $baseURL ?>riwayat-pembayaran?sync_order=<?= urlencode($checkoutPayment['order_id']) ?>'; },
          onClose: function() {}
        });
      }

      document.getElementById('pay-button')?.addEventListener('click', function() {
        openMidtransSnap();
      });

      <?php if ($autoOpenSnap): ?>
        window.addEventListener('load', function() {
          setTimeout(openMidtransSnap, 350);
        });
      <?php endif; ?>
    </script>
  <?php endif; ?>

  <?php require_once("sections/public_footer.php"); ?>
