<?php
$siteName = $data_utilities['name_web'] ?? 'Wisata Sumba Barat Daya';
$siteLogo = $data_utilities['logo'] ?? '';
?>
<footer id="bantuan" class="mt-20 border-t border-slate-200 bg-white">
  <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-4 lg:px-8">
    <div class="md:col-span-2">
      <div class="flex items-center gap-3">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-travel-blue text-lg font-black text-white">
          <?php if (!empty($siteLogo)): ?>
            <img src="<?= $baseURL ?>assets/img/<?= htmlspecialchars($siteLogo) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-full w-full object-cover">
          <?php else: ?>
            <?= htmlspecialchars(strtoupper(substr($siteName, 0, 1))) ?>
          <?php endif; ?>
        </span>
        <div>
          <h2 class="font-extrabold text-slate-900"><?= htmlspecialchars($siteName) ?></h2>
          <p class="text-sm text-slate-500">Platform informasi dan pemesanan tiket wisata.</p>
        </div>
      </div>
      <p class="mt-5 max-w-xl text-sm leading-6 text-slate-600">
        Jelajahi destinasi Sumba Barat Daya, simpan rencana kunjungan, lakukan pemesanan tiket, dan gunakan e-tiket saat tiba di lokasi wisata.
      </p>
    </div>
    <div>
      <h3 class="text-sm font-bold uppercase tracking-wide text-slate-900">Menu</h3>
      <div class="mt-4 space-y-3 text-sm text-slate-600">
        <a href="<?= $baseURL ?>objek-wisata" class="block hover:text-travel-blue">Destinasi</a>
        <a href="<?= $baseURL ?>informasi-wisata" class="block hover:text-travel-blue">Informasi Wisata</a>
        <a href="<?= $baseURL ?>galeri" class="block hover:text-travel-blue">Galeri</a>
        <a href="<?= $baseURL ?>bantuan" class="block hover:text-travel-blue">Bantuan</a>
      </div>
    </div>
    <div>
      <h3 class="text-sm font-bold uppercase tracking-wide text-slate-900">Akun</h3>
      <div class="mt-4 space-y-3 text-sm text-slate-600">
        <a href="<?= $baseURL ?>auth/" class="block hover:text-travel-blue">Masuk</a>
        <a href="<?= $baseURL ?>auth/register" class="block hover:text-travel-blue">Daftar Wisatawan</a>
        <a href="<?= $baseURL ?>auth/forgot-password" class="block hover:text-travel-blue">Lupa Password</a>
      </div>
    </div>
  </div>
  <div class="border-t border-slate-200 py-5">
    <p class="text-center text-xs font-medium text-slate-500">
      Copyright &copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>.
    </p>
  </div>
</footer>
</body>
</html>
