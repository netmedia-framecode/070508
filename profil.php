<?php
require_once("config/Base.php");
require_once("sections/public_helpers.php");
require_once("controller/public-transaksi.php");
public_require_wisatawan($baseURL);

$id_user = public_user_id();
$queryProfile = mysqli_query($conn, "SELECT users.*, user_role.role, user_status.status
  FROM users
  LEFT JOIN user_role ON users.id_role=user_role.id_role
  LEFT JOIN user_status ON users.id_active=user_status.id_status
  WHERE users.id_user='$id_user'
  LIMIT 1");
$profile = mysqli_fetch_assoc($queryProfile);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <?php require_once("sections/public_head.php"); ?>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
  <?php require_once("sections/public_navbar.php"); ?>

  <main class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
    <?php public_alert(); ?>
    <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
      <div class="flex flex-col gap-5 border-b border-slate-100 pb-6 sm:flex-row sm:items-center">
        <div class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-travel-sky text-2xl font-extrabold text-travel-blue">
          <?php if (!empty($profile['image'])): ?>
            <img src="<?= $baseURL ?>assets/img/profil/<?= htmlspecialchars($profile['image']) ?>" alt="<?= htmlspecialchars($profile['name']) ?>" class="h-full w-full object-cover">
          <?php else: ?>
            <?= htmlspecialchars(strtoupper(substr($profile['name'] ?? 'W', 0, 1))) ?>
          <?php endif; ?>
        </div>
        <div>
          <h1 class="text-3xl font-extrabold text-slate-950"><?= htmlspecialchars($profile['name'] ?: 'Wisatawan') ?></h1>
          <p class="mt-1 text-sm font-semibold text-slate-500"><?= htmlspecialchars($profile['email'] ?: '-') ?></p>
          <span class="mt-3 inline-flex rounded-full bg-travel-sky px-3 py-1 text-xs font-extrabold text-travel-blue"><?= htmlspecialchars($profile['role'] ?: 'Wisatawan') ?></span>
        </div>
      </div>

      <form action="" method="post" class="mt-6 grid gap-5 sm:grid-cols-2">
        <label>
          <span class="text-xs font-bold uppercase text-slate-500">Nama Lengkap</span>
          <input type="text" name="name" value="<?= htmlspecialchars($profile['name'] ?? '') ?>" required class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold outline-none focus:border-travel-blue">
        </label>
        <label>
          <span class="text-xs font-bold uppercase text-slate-500">Email</span>
          <input type="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" readonly class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-500 outline-none">
        </label>
        <label>
          <span class="text-xs font-bold uppercase text-slate-500">No HP</span>
          <input type="text" name="no_hp" value="<?= htmlspecialchars($profile['no_hp'] ?? '') ?>" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold outline-none focus:border-travel-blue">
        </label>
        <label>
          <span class="text-xs font-bold uppercase text-slate-500">Asal Daerah</span>
          <input type="text" name="asal_daerah" value="<?= htmlspecialchars($profile['asal_daerah'] ?? '') ?>" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold outline-none focus:border-travel-blue">
        </label>
        <button type="submit" name="public_update_profile" class="sm:col-span-2 rounded-2xl bg-travel-blue px-5 py-3 text-sm font-extrabold text-white hover:bg-blue-600">Simpan Profil</button>
      </form>
    </section>
  </main>

  <?php require_once("sections/public_footer.php"); ?>
