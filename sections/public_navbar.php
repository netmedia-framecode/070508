<?php
$currentUser = $_SESSION["project_wisata_sumba_barat_daya"]["users"] ?? null;
$currentRole = strtolower($currentUser["role"] ?? "");
$currentName = trim($currentUser["name"] ?? "Wisatawan");
$currentEmail = trim($currentUser["email"] ?? "");
$currentInitial = strtoupper(substr($currentName, 0, 1));
$profileImage = "";
$siteName = $data_utilities['name_web'] ?? 'Wisata Sumba Barat Daya';
$siteLogo = $data_utilities['logo'] ?? '';

$navDesaDestinasi = public_rows($conn, "SELECT DISTINCT desa.id, desa.nama, kecamatan.nama AS nama_kecamatan
  FROM objek_wisata
  JOIN desa ON objek_wisata.desa_id=desa.id
  JOIN kecamatan ON desa.kecamatan_id=kecamatan.id
  ORDER BY kecamatan.nama ASC, desa.nama ASC");
$navKecamatanDestinasi = public_rows($conn, "SELECT DISTINCT kecamatan.id, kecamatan.nama
  FROM objek_wisata
  LEFT JOIN desa ON objek_wisata.desa_id=desa.id
  LEFT JOIN kelurahan ON objek_wisata.kelurahan_id=kelurahan.id
  JOIN kecamatan ON kecamatan.id=COALESCE(desa.kecamatan_id, kelurahan.kecamatan_id)
  ORDER BY kecamatan.nama ASC");

if (!empty($currentUser["image"])) {
  $profileImage = $baseURL . "assets/img/profil/" . ltrim($currentUser["image"], "/");
}
?>
<header class="sticky top-0 z-50 border-b border-slate-200/70 bg-white/95 backdrop-blur">
  <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
    <a href="<?= $baseURL ?>" class="flex items-center gap-3">
      <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-travel-blue text-lg font-black text-white">
        <?php if (!empty($siteLogo)): ?>
          <img src="<?= $baseURL ?>assets/img/<?= htmlspecialchars($siteLogo) ?>" alt="<?= htmlspecialchars($siteName) ?>" class="h-full w-full object-cover">
        <?php else: ?>
          <?= htmlspecialchars(strtoupper(substr($siteName, 0, 1))) ?>
        <?php endif; ?>
      </span>
      <span class="leading-tight">
        <span class="block text-base font-extrabold text-slate-900"><?= htmlspecialchars($siteName) ?></span>
        <span class="block text-xs font-medium text-slate-500">Explore Sumba Barat Daya</span>
      </span>
    </a>

    <div class="hidden items-center gap-8 text-sm font-semibold text-slate-700 lg:flex">
      <div class="flex items-center gap-1">
        <a href="<?= $baseURL ?>objek-wisata" class="hover:text-travel-blue">Destinasi</a>
        <details class="destination-dropdown relative">
          <summary class="flex cursor-pointer list-none items-center rounded-lg p-1 text-slate-500 hover:bg-slate-100 hover:text-travel-blue" aria-label="Filter destinasi">
            <svg class="destination-caret h-4 w-4 transition" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
            </svg>
          </summary>
          <div class="absolute left-0 mt-3 grid max-h-[70vh] w-[34rem] grid-cols-2 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft">
            <div class="border-r border-slate-100 p-3">
              <p class="px-3 pb-2 text-xs font-extrabold uppercase tracking-wide text-travel-blue">Berdasarkan Desa</p>
              <div class="max-h-80 overflow-y-auto">
                <?php foreach ($navDesaDestinasi as $navDesa): ?>
                <a href="<?= $baseURL ?>objek-wisata?tipe=desa&amp;id=<?= (int) $navDesa['id'] ?>" class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-travel-blue">
                  <?= htmlspecialchars($navDesa['nama']) ?>
                  <span class="block text-xs font-medium text-slate-400">Kec. <?= htmlspecialchars($navDesa['nama_kecamatan']) ?></span>
                </a>
                <?php endforeach; ?>
                <?php if (count($navDesaDestinasi) == 0): ?>
                <p class="px-3 py-2 text-xs font-medium text-slate-400">Belum ada destinasi berdasarkan desa.</p>
                <?php endif; ?>
              </div>
            </div>
            <div class="p-3">
              <p class="px-3 pb-2 text-xs font-extrabold uppercase tracking-wide text-travel-blue">Berdasarkan Kecamatan</p>
              <div class="max-h-80 overflow-y-auto">
                <?php foreach ($navKecamatanDestinasi as $navKecamatan): ?>
                <a href="<?= $baseURL ?>objek-wisata?tipe=kecamatan&amp;id=<?= (int) $navKecamatan['id'] ?>" class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-travel-blue">
                  <?= htmlspecialchars($navKecamatan['nama']) ?>
                </a>
                <?php endforeach; ?>
                <?php if (count($navKecamatanDestinasi) == 0): ?>
                <p class="px-3 py-2 text-xs font-medium text-slate-400">Belum ada destinasi berdasarkan kecamatan.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </details>
      </div>
      <a href="<?= $baseURL ?>informasi-wisata" class="hover:text-travel-blue">Informasi</a>
      <a href="<?= $baseURL ?>galeri" class="hover:text-travel-blue">Galeri</a>
      <a href="<?= $baseURL ?>bantuan" class="hover:text-travel-blue">Bantuan</a>
    </div>

    <div class="flex items-center gap-3">
      <?php if ($currentUser): ?>
        <details class="profile-dropdown relative">
          <summary class="flex cursor-pointer list-none items-center gap-2 rounded-2xl border border-slate-200 bg-white py-1.5 pl-1.5 pr-3 shadow-sm outline-none transition hover:border-travel-blue hover:shadow-md">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-travel-sky text-sm font-extrabold text-travel-blue">
              <?php if ($profileImage): ?>
                <img src="<?= htmlspecialchars($profileImage) ?>" alt="<?= htmlspecialchars($currentName) ?>" class="h-full w-full object-cover">
              <?php else: ?>
                <?= htmlspecialchars($currentInitial) ?>
              <?php endif; ?>
            </span>
            <span class="hidden max-w-[130px] truncate text-sm font-bold text-slate-700 sm:block"><?= htmlspecialchars($currentName) ?></span>
            <svg class="profile-caret h-4 w-4 text-slate-500 transition" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
            </svg>
          </summary>

          <div class="absolute right-0 mt-3 w-80 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft">
            <div class="border-b border-slate-100 p-4">
              <p class="truncate text-sm font-extrabold text-slate-900"><?= htmlspecialchars($currentName) ?></p>
              <?php if ($currentEmail): ?>
                <p class="mt-1 truncate text-xs font-medium text-slate-500"><?= htmlspecialchars($currentEmail) ?></p>
              <?php endif; ?>
              <span class="mt-3 inline-flex rounded-full bg-travel-sky px-3 py-1 text-xs font-bold text-travel-blue"><?= htmlspecialchars($currentUser["role"] ?? "Wisatawan") ?></span>
            </div>

            <div class="p-2">
              <?php if ($currentRole == 'wisatawan'): ?>
                <a href="<?= $baseURL ?>profil" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-travel-blue">
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21a8 8 0 0 0-16 0" />
                    <circle cx="12" cy="7" r="4" />
                  </svg>
                  Profil Saya
                </a>
                <a href="<?= $baseURL ?>keranjang" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-travel-blue">
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="8" cy="21" r="1" />
                    <circle cx="19" cy="21" r="1" />
                    <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                  </svg>
                  Keranjang
                </a>
                <a href="<?= $baseURL ?>pesanan-saya" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-travel-blue">
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 6h13" />
                    <path d="M8 12h13" />
                    <path d="M8 18h13" />
                    <path d="M3 6h.01" />
                    <path d="M3 12h.01" />
                    <path d="M3 18h.01" />
                  </svg>
                  Pesanan Saya
                </a>
                <a href="<?= $baseURL ?>e-tiket" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-travel-blue">
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z" />
                    <path d="M13 5v2" />
                    <path d="M13 17v2" />
                    <path d="M13 11v2" />
                  </svg>
                  E-Tiket Saya
                </a>
                <a href="<?= $baseURL ?>riwayat-pembayaran" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-travel-blue">
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect width="20" height="14" x="2" y="5" rx="2" />
                    <path d="M2 10h20" />
                  </svg>
                  Riwayat Pembayaran
                </a>
                <a href="<?= $baseURL ?>riwayat-transaksi" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-travel-blue">
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12a9 9 0 1 0 3-6.7" />
                    <path d="M3 3v6h6" />
                    <path d="M12 7v5l3 2" />
                  </svg>
                  Riwayat Transaksi
                </a>
              <?php else: ?>
                <a href="<?= $baseURL ?>views/" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-travel-blue">
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect width="7" height="9" x="3" y="3" rx="1" />
                    <rect width="7" height="5" x="14" y="3" rx="1" />
                    <rect width="7" height="9" x="14" y="12" rx="1" />
                    <rect width="7" height="5" x="3" y="16" rx="1" />
                  </svg>
                  Dashboard
                </a>
                <a href="<?= $baseURL ?>views/profil" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-travel-blue">
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21a8 8 0 0 0-16 0" />
                    <circle cx="12" cy="7" r="4" />
                  </svg>
                  Detail Profil
                </a>
              <?php endif; ?>
            </div>

            <div class="border-t border-slate-100 p-2">
              <a href="<?= $baseURL ?>auth/logout" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                  <path d="M16 17l5-5-5-5" />
                  <path d="M21 12H9" />
                </svg>
                Logout
              </a>
            </div>
          </div>
        </details>
      <?php else: ?>
        <a href="<?= $baseURL ?>auth/" class="hidden rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 hover:border-travel-blue hover:text-travel-blue sm:inline-flex">Masuk</a>
        <a href="<?= $baseURL ?>auth/register" class="rounded-xl bg-travel-blue px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-blue-600">Daftar</a>
      <?php endif; ?>
    </div>
  </nav>
</header>
