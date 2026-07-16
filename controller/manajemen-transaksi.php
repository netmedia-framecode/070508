<?php

require_once("../../config/Base.php");
require_once("../../config/Auth.php");
require_once("../../config/Alert.php");
require_once("../../views/manajemen-transaksi/redirect.php");

$select_data_keranjang = "SELECT keranjang.*, users.name AS nama_wisatawan, users.email, user_role.role, objek_wisata.nama_wisata
  FROM keranjang
  LEFT JOIN users ON keranjang.id_wisatawan=users.id_user
  LEFT JOIN user_role ON users.id_role=user_role.id_role
  LEFT JOIN objek_wisata ON keranjang.id_objek_wisata=objek_wisata.id
  ORDER BY keranjang.id DESC";
$views_data_keranjang = mysqli_query($conn, $select_data_keranjang);

$select_pemesanan_tiket = "SELECT pemesanan_tiket.*, users.name AS nama_wisatawan, users.email, objek_wisata.nama_wisata
  FROM pemesanan_tiket
  LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
  LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
  ORDER BY pemesanan_tiket.waktu_pesan DESC, pemesanan_tiket.id DESC";
$views_pemesanan_tiket = mysqli_query($conn, $select_pemesanan_tiket);

$select_pemesanan_tiket_belum_bayar = "SELECT pemesanan_tiket.*, users.name AS nama_wisatawan, users.email, objek_wisata.nama_wisata
  FROM pemesanan_tiket
  LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
  LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
  LEFT JOIN pembayaran ON pembayaran.id_pemesanan=pemesanan_tiket.id
  WHERE pembayaran.id IS NULL
  ORDER BY pemesanan_tiket.waktu_pesan DESC, pemesanan_tiket.id DESC";
$views_pemesanan_tiket_belum_bayar = mysqli_query($conn, $select_pemesanan_tiket_belum_bayar);

$select_konfirmasi_pembayaran = "SELECT pembayaran.*, pemesanan_tiket.kode_booking, pemesanan_tiket.total_tagihan, users.name AS nama_wisatawan, users.email, objek_wisata.nama_wisata
  FROM pembayaran
  LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
  LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
  LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
  ORDER BY pembayaran.waktu_bayar DESC, pembayaran.id DESC";
$views_konfirmasi_pembayaran = mysqli_query($conn, $select_konfirmasi_pembayaran);

$select_e_tiket = "SELECT e_tiket.*, pembayaran.order_id, pembayaran.status_bayar, pemesanan_tiket.kode_booking, pemesanan_tiket.tgl_kunjungan, pemesanan_tiket.jumlah_tiket, pemesanan_tiket.total_tagihan, users.name AS nama_wisatawan, users.email, objek_wisata.nama_wisata
  FROM e_tiket
  LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
  LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
  LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
  LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
  ORDER BY e_tiket.id DESC";
$views_e_tiket = mysqli_query($conn, $select_e_tiket);

$select_riwayat_transaksi = "SELECT riwayat_transaksi.*, users.name AS nama_wisatawan, users.email, pemesanan_tiket.kode_booking, pemesanan_tiket.tgl_kunjungan, pemesanan_tiket.jumlah_tiket, pemesanan_tiket.total_tagihan, objek_wisata.nama_wisata
  FROM riwayat_transaksi
  LEFT JOIN users ON riwayat_transaksi.id_wisatawan=users.id_user
  LEFT JOIN pemesanan_tiket ON riwayat_transaksi.id_pemesanan=pemesanan_tiket.id
  LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
  ORDER BY riwayat_transaksi.tanggal_selesai DESC, riwayat_transaksi.id DESC";
$views_riwayat_transaksi = mysqli_query($conn, $select_riwayat_transaksi);

$select_wisatawan = "SELECT users.id_user, users.name, users.email
  FROM users
  LEFT JOIN user_role ON users.id_role=user_role.id_role
  WHERE LOWER(TRIM(user_role.role))='wisatawan'
  ORDER BY users.name ASC";
$views_wisatawan = mysqli_query($conn, $select_wisatawan);

if (isset($_POST["edit_keranjang"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (keranjang($conn, $validated_post, $action = 'update') > 0) {
    $message = "Data keranjang berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: data-keranjang");
    exit();
  }
}

if (isset($_POST["delete_keranjang"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (keranjang($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Data keranjang " . $_POST['nama_wisatawan'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: data-keranjang");
    exit();
  }
}

if (isset($_POST["add_pemesanan_tiket"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (pemesanan_tiket($conn, $validated_post, $action = 'insert') > 0) {
    $message = "Pemesanan tiket baru berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: pemesanan-tiket");
    exit();
  }
}

if (isset($_POST["edit_pemesanan_tiket"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (pemesanan_tiket($conn, $validated_post, $action = 'update') > 0) {
    $message = "Pemesanan tiket berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: pemesanan-tiket");
    exit();
  }
}

if (isset($_POST["delete_pemesanan_tiket"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (pemesanan_tiket($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Pemesanan tiket " . $_POST['kode_booking'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: pemesanan-tiket");
    exit();
  }
}

if (isset($_POST["add_pembayaran"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (pembayaran($conn, $validated_post, $action = 'insert') > 0) {
    $message = "Konfirmasi pembayaran baru berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: konfirmasi-pembayaran");
    exit();
  }
}

if (isset($_POST["edit_pembayaran"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (pembayaran($conn, $validated_post, $action = 'update') > 0) {
    $message = "Konfirmasi pembayaran " . $_POST['order_id'] . " berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: konfirmasi-pembayaran");
    exit();
  }
}

if (isset($_POST["delete_pembayaran"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (pembayaran($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Konfirmasi pembayaran " . $_POST['order_id'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: konfirmasi-pembayaran");
    exit();
  }
}

if (isset($_POST["delete_e_tiket"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (e_tiket($conn, $validated_post, $action = 'delete') > 0) {
    $message = "E-Tiket " . $_POST['kode_qr'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: e-tiket");
    exit();
  }
}

$select_objek_wisata = "SELECT id, nama_wisata, harga_tiket FROM objek_wisata ORDER BY nama_wisata ASC";
$views_objek_wisata = mysqli_query($conn, $select_objek_wisata);
