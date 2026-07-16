<?php
require_once("config/Base.php");
require_once("sections/public_helpers.php");
require_once("controller/public-transaksi.php");
public_require_wisatawan($baseURL);

$id_user = public_user_id();
$fallbackImages = public_fallback_images();
$cartItems = public_user_cart($id_user);
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
    <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
      <div>
        <span class="inline-flex rounded-full bg-travel-sky px-4 py-2 text-sm font-extrabold text-travel-blue">Keranjang</span>
        <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-slate-950">Keranjang Tiket</h1>
        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">Simpan destinasi yang ingin dipesan, lalu lanjutkan menjadi pemesanan tiket.</p>
      </div>
      <a href="<?= $baseURL ?>objek-wisata" class="rounded-2xl bg-travel-blue px-5 py-3 text-sm font-extrabold text-white hover:bg-blue-600">Tambah Destinasi</a>
    </div>

    <div class="grid gap-5">
      <?php if (count($cartItems) > 0): ?>
        <?php foreach ($cartItems as $index => $item):
          $image = public_asset($baseURL, $item['gambar'] ?? '', $fallbackImages[$index % count($fallbackImages)]);
        ?>
          <article class="grid gap-5 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 md:grid-cols-[220px_1fr]">
            <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($item['nama_wisata']) ?>" class="h-48 w-full rounded-3xl object-cover md:h-full">
            <div class="grid gap-5 lg:grid-cols-[1fr_320px]">
              <div>
                <h2 class="text-xl font-extrabold text-slate-950"><?= htmlspecialchars($item['nama_wisata'] ?: '-') ?></h2>
                <p class="mt-1 text-sm font-semibold text-slate-500"><?= htmlspecialchars($item['lokasi'] ?: 'Sumba Barat Daya') ?></p>
                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                  <div class="rounded-2xl bg-slate-50 p-4">
                    <span class="text-xs font-bold uppercase text-slate-400">Harga</span>
                    <p class="mt-1 font-extrabold">Rp <?= number_format((int) $item['harga_tiket'], 0, ',', '.') ?></p>
                  </div>
                  <div class="rounded-2xl bg-slate-50 p-4">
                    <span class="text-xs font-bold uppercase text-slate-400">Tiket</span>
                    <p class="mt-1 font-extrabold"><?= (int) $item['jumlah_tiket'] ?></p>
                  </div>
                  <div class="rounded-2xl bg-orange-50 p-4">
                    <span class="text-xs font-bold uppercase text-orange-400">Total</span>
                    <p class="mt-1 font-extrabold text-travel-orange">Rp <?= number_format((int) $item['total_harga_sementara'], 0, ',', '.') ?></p>
                  </div>
                </div>
              </div>
              <div>
                <form action="" method="post" class="rounded-3xl border border-slate-200 p-4">
                  <input type="hidden" name="id_cart" value="<?= (int) $item['id'] ?>">
                  <input type="hidden" name="id_objek_wisata" value="<?= (int) $item['id_objek_wisata'] ?>">
                  <input type="hidden" name="jumlah_tiket" value="<?= (int) $item['jumlah_tiket'] ?>">
                  <label>
                    <span class="text-xs font-bold uppercase text-slate-500">Tanggal Kunjungan</span>
                    <input type="date" name="tgl_kunjungan" min="<?= date('Y-m-d') ?>" required class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold outline-none focus:border-travel-blue">
                  </label>
                  <button type="submit" name="public_create_order" class="mt-4 w-full rounded-2xl bg-travel-orange px-5 py-3 text-sm font-extrabold text-white hover:bg-orange-600">Checkout Item</button>
                </form>
                <form action="" method="post" class="mt-3">
                  <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                  <button type="submit" name="public_delete_cart" class="w-full rounded-2xl border border-red-200 px-5 py-3 text-sm font-extrabold text-red-600 hover:bg-red-50">Hapus</button>
                </form>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="rounded-3xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200">
          <h2 class="text-xl font-extrabold text-slate-950">Keranjang masih kosong</h2>
          <p class="mt-2 text-sm leading-6 text-slate-600">Pilih destinasi terlebih dahulu untuk menambahkan tiket ke keranjang.</p>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <?php require_once("sections/public_footer.php"); ?>
