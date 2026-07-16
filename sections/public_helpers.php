<?php
if (!function_exists('public_rows')) {
  function public_rows($conn, $query)
  {
    $rows = [];
    $result = mysqli_query($conn, $query);
    if ($result instanceof mysqli_result) {
      foreach ($result as $row) {
        $rows[] = $row;
      }
    }
    return $rows;
  }
}

if (!function_exists('public_count')) {
  function public_count($conn, $table)
  {
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM $table");
    if (!$result) {
      return 0;
    }
    $row = mysqli_fetch_assoc($result);
    return (int) ($row['total'] ?? 0);
  }
}

if (!function_exists('public_objek_location_select')) {
  function public_objek_location_select($alias = 'objek_wisata')
  {
    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $alias)) {
      $alias = 'objek_wisata';
    }

    return "COALESCE(
      (SELECT CONCAT('Desa ', d.nama, ', Kecamatan ', k.nama, ', ', kk.jenis, ' ', kk.nama)
        FROM desa d
        JOIN kecamatan k ON d.kecamatan_id=k.id
        JOIN kabupaten_kota kk ON k.kabupaten_kota_id=kk.id
        WHERE d.id=$alias.desa_id),
      (SELECT CONCAT('Kelurahan ', kl.nama, ', Kecamatan ', k.nama, ', ', kk.jenis, ' ', kk.nama)
        FROM kelurahan kl
        JOIN kecamatan k ON kl.kecamatan_id=k.id
        JOIN kabupaten_kota kk ON k.kabupaten_kota_id=kk.id
        WHERE kl.id=$alias.kelurahan_id)
    ) AS lokasi";
  }
}

if (!function_exists('public_asset')) {
  function public_asset($baseURL, $path, $fallback)
  {
    if (!empty($path)) {
      return $baseURL . ltrim($path, '/');
    }
    return $fallback;
  }
}

if (!function_exists('public_fallback_images')) {
  function public_fallback_images()
  {
    return [
      'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80',
      'https://images.unsplash.com/photo-1512100356356-de1b84283e18?auto=format&fit=crop&w=1200&q=80',
      'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80',
      'https://images.unsplash.com/photo-1526772662000-3f88f10405ff?auto=format&fit=crop&w=1200&q=80',
      'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=1200&q=80',
      'https://images.unsplash.com/photo-1533105079780-92b9be482077?auto=format&fit=crop&w=1200&q=80'
    ];
  }
}

if (!function_exists('public_user')) {
  function public_user()
  {
    return $_SESSION["project_wisata_sumba_barat_daya"]["users"] ?? null;
  }
}

if (!function_exists('public_user_id')) {
  function public_user_id()
  {
    $user = public_user();
    return (int) ($user['id'] ?? $user['id_user'] ?? 0);
  }
}

if (!function_exists('public_is_wisatawan')) {
  function public_is_wisatawan()
  {
    $user = public_user();
    return strtolower($user['role'] ?? '') == 'wisatawan';
  }
}

if (!function_exists('public_require_wisatawan')) {
  function public_require_wisatawan($baseURL)
  {
    if (!public_user()) {
      header("Location: " . $baseURL . "auth/");
      exit();
    }

    if (!public_is_wisatawan()) {
      header("Location: " . $baseURL . "views/");
      exit();
    }
  }
}

if (!function_exists('public_flash_message')) {
  function public_flash_message()
  {
    $messageTypes = ["success", "info", "warning", "danger", "dark"];
    $session = $_SESSION["project_wisata_sumba_barat_daya"] ?? [];
    $userSession = $session["users"] ?? [];

    foreach ($messageTypes as $type) {
      if (isset($userSession["message_$type"])) {
        return ["type" => $type, "message" => $userSession["message_$type"]];
      }

      if (isset($session["message_$type"])) {
        return ["type" => $type, "message" => $session["message_$type"]];
      }
    }

    return null;
  }
}

if (!function_exists('public_alert')) {
  function public_alert()
  {
    $flash = public_flash_message();
    if (!$flash) {
      return;
    }

    $classes = [
      "success" => "border-emerald-200 bg-emerald-50 text-emerald-800",
      "info" => "border-blue-200 bg-blue-50 text-blue-800",
      "warning" => "border-amber-200 bg-amber-50 text-amber-800",
      "danger" => "border-red-200 bg-red-50 text-red-800",
      "dark" => "border-slate-200 bg-slate-100 text-slate-800"
    ];
    $class = $classes[$flash["type"]] ?? $classes["info"];
    echo '<div class="mb-6 rounded-2xl border px-4 py-3 text-sm font-semibold ' . $class . '">' . htmlspecialchars($flash["message"]) . '</div>';
  }
}
