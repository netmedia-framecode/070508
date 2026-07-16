<?php
require_once("config/Base.php");
require_once("sections/public_helpers.php");
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
      <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
          <span class="inline-flex rounded-full bg-travel-sky px-4 py-2 text-sm font-extrabold text-travel-blue">Bantuan</span>
          <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-slate-950 sm:text-5xl">Butuh bantuan saat memesan tiket?</h1>
          <p class="mt-5 text-base leading-8 text-slate-600">Ikuti panduan singkat ini untuk membuat akun, memesan tiket, melakukan pembayaran, dan menggunakan e-tiket saat berkunjung.</p>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
      <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-travel-sky text-lg font-extrabold text-travel-blue">1</div>
          <h2 class="mt-5 text-lg font-extrabold text-slate-950">Buat Akun</h2>
          <p class="mt-3 text-sm leading-7 text-slate-600">Daftar sebagai wisatawan agar bisa menyimpan data pesanan dan mengakses e-tiket.</p>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-lg font-extrabold text-travel-orange">2</div>
          <h2 class="mt-5 text-lg font-extrabold text-slate-950">Pilih Destinasi</h2>
          <p class="mt-3 text-sm leading-7 text-slate-600">Cari objek wisata, tentukan tanggal kunjungan, dan jumlah tiket yang diperlukan.</p>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-lg font-extrabold text-emerald-600">3</div>
          <h2 class="mt-5 text-lg font-extrabold text-slate-950">Bayar Pesanan</h2>
          <p class="mt-3 text-sm leading-7 text-slate-600">Selesaikan pembayaran agar pesanan dikonfirmasi dan e-tiket aktif.</p>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 text-lg font-extrabold text-violet-600">4</div>
          <h2 class="mt-5 text-lg font-extrabold text-slate-950">Scan QR</h2>
          <p class="mt-3 text-sm leading-7 text-slate-600">Tunjukkan kode QR e-tiket kepada petugas saat tiba di lokasi wisata.</p>
        </div>
      </div>

      <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_.8fr]">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
          <h2 class="text-xl font-extrabold text-slate-950">Pertanyaan Umum</h2>
          <div class="mt-5 space-y-3">
            <details class="rounded-2xl border border-slate-200 p-4">
              <summary class="cursor-pointer list-none font-bold text-slate-800">Apakah saya harus login untuk pesan tiket?</summary>
              <p class="mt-3 text-sm leading-7 text-slate-600">Ya. Login diperlukan agar sistem bisa menyimpan pesanan, pembayaran, dan e-tiket ke akun wisatawan.</p>
            </details>
            <details class="rounded-2xl border border-slate-200 p-4">
              <summary class="cursor-pointer list-none font-bold text-slate-800">Kapan e-tiket aktif?</summary>
              <p class="mt-3 text-sm leading-7 text-slate-600">E-tiket aktif setelah pembayaran berstatus Paid atau Settlement dan pesanan sudah dikonfirmasi.</p>
            </details>
            <details class="rounded-2xl border border-slate-200 p-4">
              <summary class="cursor-pointer list-none font-bold text-slate-800">Apa yang perlu ditunjukkan di lokasi?</summary>
              <p class="mt-3 text-sm leading-7 text-slate-600">Tunjukkan e-tiket yang berisi kode QR agar petugas dapat melakukan scan kunjungan.</p>
            </details>
          </div>
        </div>

        <div class="rounded-3xl bg-travel-blue p-6 text-white shadow-soft">
          <h2 class="text-xl font-extrabold">Akses Cepat</h2>
          <p class="mt-3 text-sm leading-7 text-blue-50">Masuk ke akun wisatawan untuk melihat transaksi, pembayaran, dan e-tiket yang sudah aktif.</p>
          <div class="mt-6 grid gap-3">
            <a href="<?= $baseURL ?>auth/" class="rounded-2xl bg-white px-4 py-3 text-center text-sm font-extrabold text-travel-blue hover:bg-blue-50">Masuk Akun</a>
            <a href="<?= $baseURL ?>auth/register" class="rounded-2xl border border-white/30 px-4 py-3 text-center text-sm font-extrabold text-white hover:bg-white/10">Daftar Wisatawan</a>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php require_once("sections/public_footer.php"); ?>
