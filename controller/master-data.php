<?php

require_once("../../config/Base.php");
require_once("../../config/Auth.php");
require_once("../../config/Alert.php");
require_once("../../views/master-data/redirect.php");

$select_kabupaten_kota = "SELECT * FROM kabupaten_kota ORDER BY jenis ASC, nama ASC";
$views_kabupaten_kota = mysqli_query($conn, $select_kabupaten_kota);

$select_kecamatan = "SELECT kecamatan.*, kabupaten_kota.nama AS nama_kabupaten_kota,
  kabupaten_kota.jenis AS jenis_kabupaten_kota
  FROM kecamatan
  JOIN kabupaten_kota ON kecamatan.kabupaten_kota_id=kabupaten_kota.id
  ORDER BY kabupaten_kota.nama ASC, kecamatan.nama ASC";
$views_kecamatan = mysqli_query($conn, $select_kecamatan);

$select_desa = "SELECT desa.*, kecamatan.nama AS nama_kecamatan,
  kabupaten_kota.nama AS nama_kabupaten_kota,
  kabupaten_kota.jenis AS jenis_kabupaten_kota
  FROM desa
  JOIN kecamatan ON desa.kecamatan_id=kecamatan.id
  JOIN kabupaten_kota ON kecamatan.kabupaten_kota_id=kabupaten_kota.id
  ORDER BY kabupaten_kota.nama ASC, kecamatan.nama ASC, desa.nama ASC";
$views_desa = mysqli_query($conn, $select_desa);

$select_kelurahan = "SELECT kelurahan.*, kecamatan.nama AS nama_kecamatan,
  kabupaten_kota.nama AS nama_kabupaten_kota,
  kabupaten_kota.jenis AS jenis_kabupaten_kota
  FROM kelurahan
  JOIN kecamatan ON kelurahan.kecamatan_id=kecamatan.id
  JOIN kabupaten_kota ON kecamatan.kabupaten_kota_id=kabupaten_kota.id
  ORDER BY kabupaten_kota.nama ASC, kecamatan.nama ASC, kelurahan.nama ASC";
$views_kelurahan = mysqli_query($conn, $select_kelurahan);

$select_objek_wisata = "SELECT objek_wisata.*,
  desa.nama AS nama_desa,
  kelurahan.nama AS nama_kelurahan,
  COALESCE(kecamatan_desa.nama, kecamatan_kelurahan.nama) AS nama_kecamatan,
  COALESCE(kabupaten_desa.nama, kabupaten_kelurahan.nama) AS nama_kabupaten_kota,
  COALESCE(kabupaten_desa.jenis, kabupaten_kelurahan.jenis) AS jenis_kabupaten_kota
  FROM objek_wisata
  LEFT JOIN desa ON objek_wisata.desa_id=desa.id
  LEFT JOIN kecamatan AS kecamatan_desa ON desa.kecamatan_id=kecamatan_desa.id
  LEFT JOIN kabupaten_kota AS kabupaten_desa ON kecamatan_desa.kabupaten_kota_id=kabupaten_desa.id
  LEFT JOIN kelurahan ON objek_wisata.kelurahan_id=kelurahan.id
  LEFT JOIN kecamatan AS kecamatan_kelurahan ON kelurahan.kecamatan_id=kecamatan_kelurahan.id
  LEFT JOIN kabupaten_kota AS kabupaten_kelurahan ON kecamatan_kelurahan.kabupaten_kota_id=kabupaten_kelurahan.id
  ORDER BY objek_wisata.id DESC";
$views_objek_wisata = mysqli_query($conn, $select_objek_wisata);

$select_informasi_wisata = "SELECT informasi_wisata.*, users.name AS nama_user
  FROM informasi_wisata
  JOIN users ON informasi_wisata.id_user=users.id_user
  ORDER BY informasi_wisata.tgl_posting DESC, informasi_wisata.id DESC";
$views_informasi_wisata = mysqli_query($conn, $select_informasi_wisata);

$select_galeri = "SELECT galeri.*, objek_wisata.nama_wisata
  FROM galeri
  JOIN objek_wisata ON galeri.objek_wisata_id=objek_wisata.id
  ORDER BY galeri.id DESC";
$views_galeri = mysqli_query($conn, $select_galeri);

if (isset($_POST["add_kabupaten_kota"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (kabupaten_kota($conn, $validated_post, 'insert') > 0) {
    alert("Kabupaten/kota baru berhasil ditambahkan.", "success");
    header("Location: kabupaten-kota");
    exit();
  }
}

if (isset($_POST["edit_kabupaten_kota"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (kabupaten_kota($conn, $validated_post, 'update') > 0) {
    alert("Kabupaten/kota berhasil diubah.", "success");
    header("Location: kabupaten-kota");
    exit();
  }
}

if (isset($_POST["delete_kabupaten_kota"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (kabupaten_kota($conn, $validated_post, 'delete') > 0) {
    alert("Kabupaten/kota " . $_POST['nama'] . " berhasil dihapus.", "success");
    header("Location: kabupaten-kota");
    exit();
  }
}

if (isset($_POST["add_kecamatan"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (kecamatan($conn, $validated_post, 'insert') > 0) {
    alert("Kecamatan baru berhasil ditambahkan.", "success");
    header("Location: kecamatan");
    exit();
  }
}

if (isset($_POST["edit_kecamatan"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (kecamatan($conn, $validated_post, 'update') > 0) {
    alert("Kecamatan berhasil diubah.", "success");
    header("Location: kecamatan");
    exit();
  }
}

if (isset($_POST["delete_kecamatan"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (kecamatan($conn, $validated_post, 'delete') > 0) {
    alert("Kecamatan " . $_POST['nama'] . " berhasil dihapus.", "success");
    header("Location: kecamatan");
    exit();
  }
}

if (isset($_POST["add_desa"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (desa($conn, $validated_post, 'insert') > 0) {
    alert("Desa baru berhasil ditambahkan.", "success");
    header("Location: desa");
    exit();
  }
}

if (isset($_POST["edit_desa"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (desa($conn, $validated_post, 'update') > 0) {
    alert("Desa berhasil diubah.", "success");
    header("Location: desa");
    exit();
  }
}

if (isset($_POST["delete_desa"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (desa($conn, $validated_post, 'delete') > 0) {
    alert("Desa " . $_POST['nama'] . " berhasil dihapus.", "success");
    header("Location: desa");
    exit();
  }
}

if (isset($_POST["add_kelurahan"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (kelurahan($conn, $validated_post, 'insert') > 0) {
    alert("Kelurahan baru berhasil ditambahkan.", "success");
    header("Location: kelurahan");
    exit();
  }
}

if (isset($_POST["edit_kelurahan"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (kelurahan($conn, $validated_post, 'update') > 0) {
    alert("Kelurahan berhasil diubah.", "success");
    header("Location: kelurahan");
    exit();
  }
}

if (isset($_POST["delete_kelurahan"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (kelurahan($conn, $validated_post, 'delete') > 0) {
    alert("Kelurahan " . $_POST['nama'] . " berhasil dihapus.", "success");
    header("Location: kelurahan");
    exit();
  }
}

if (isset($_POST["add_objek_wisata"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (objek_wisata($conn, $validated_post, $action = 'insert') > 0) {
    $message = "Objek wisata baru berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: objek-wisata");
    exit();
  }
}

if (isset($_POST["edit_objek_wisata"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (objek_wisata($conn, $validated_post, $action = 'update') > 0) {
    $message = "Objek wisata berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: objek-wisata");
    exit();
  }
}

if (isset($_POST["delete_objek_wisata"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (objek_wisata($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Objek wisata " . $_POST['nama_wisata'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: objek-wisata");
    exit();
  }
}

if (isset($_POST["add_informasi_wisata"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (informasi_wisata($conn, $validated_post, $action = 'insert') > 0) {
    $message = "Informasi wisata baru berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: informasi-wisata");
    exit();
  }
}

if (isset($_POST["edit_informasi_wisata"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (informasi_wisata($conn, $validated_post, $action = 'update') > 0) {
    $message = "Informasi wisata berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: informasi-wisata");
    exit();
  }
}

if (isset($_POST["delete_informasi_wisata"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (informasi_wisata($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Informasi wisata " . $_POST['judul'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: informasi-wisata");
    exit();
  }
}

if (isset($_POST["add_galeri"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (galeri($conn, $validated_post, $action = 'insert') > 0) {
    $message = "Galeri baru berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: galeri");
    exit();
  }
}

if (isset($_POST["edit_galeri"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (galeri($conn, $validated_post, $action = 'update') > 0) {
    $message = "Galeri berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: galeri");
    exit();
  }
}

if (isset($_POST["delete_galeri"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (galeri($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Galeri " . $_POST['judul'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: galeri");
    exit();
  }
}
