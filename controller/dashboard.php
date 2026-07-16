<?php

require_once("../config/Base.php");
require_once("../config/Auth.php");
require_once("../config/Alert.php");

function dashboard_value($conn, $query, $field = 'total')
{
  $result = mysqli_query($conn, $query);
  if (!$result) {
    return 0;
  }

  $row = mysqli_fetch_assoc($result);
  return isset($row[$field]) ? $row[$field] : 0;
}

function dashboard_rows($conn, $query)
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

$dashboard_total_objek_wisata = (int) dashboard_value($conn, "SELECT COUNT(*) AS total FROM objek_wisata");
$dashboard_total_informasi = (int) dashboard_value($conn, "SELECT COUNT(*) AS total FROM informasi_wisata");
$dashboard_total_galeri = (int) dashboard_value($conn, "SELECT COUNT(*) AS total FROM galeri");
$dashboard_total_keranjang = (int) dashboard_value($conn, "SELECT COUNT(*) AS total FROM keranjang");
$dashboard_total_pemesanan = (int) dashboard_value($conn, "SELECT COUNT(*) AS total FROM pemesanan_tiket");
$dashboard_total_pembayaran = (int) dashboard_value($conn, "SELECT COUNT(*) AS total FROM pembayaran");
$dashboard_total_e_tiket = (int) dashboard_value($conn, "SELECT COUNT(*) AS total FROM e_tiket");
$dashboard_total_kunjungan = (int) dashboard_value($conn, "SELECT COUNT(*) AS total FROM data_kunjungan");
$dashboard_total_riwayat = (int) dashboard_value($conn, "SELECT COUNT(*) AS total FROM riwayat_transaksi");
$dashboard_total_wisatawan = (int) dashboard_value($conn, "SELECT COUNT(*) AS total FROM users LEFT JOIN user_role ON users.id_role=user_role.id_role WHERE LOWER(TRIM(user_role.role))='wisatawan'");
$dashboard_total_users = (int) dashboard_value($conn, "SELECT COUNT(*) AS total FROM users");
$dashboard_pendapatan = (float) dashboard_value($conn, "SELECT COALESCE(SUM(pemesanan_tiket.total_tagihan), 0) AS total
  FROM pembayaran
  LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
  WHERE LOWER(pembayaran.status_bayar) IN ('paid', 'settlement')");
$dashboard_pending_pemesanan = (int) dashboard_value($conn, "SELECT COUNT(*) AS total FROM pemesanan_tiket WHERE LOWER(status_pemesanan)='pending'");
$dashboard_active_tiket = (int) dashboard_value($conn, "SELECT COUNT(*) AS total FROM e_tiket WHERE LOWER(status_tiket)='active'");
$dashboard_used_tiket = (int) dashboard_value($conn, "SELECT COUNT(*) AS total FROM e_tiket WHERE LOWER(status_tiket)='used'");

$dashboard_chart_months = [];
$dashboard_chart_pemesanan = [];
$dashboard_chart_pembayaran = [];
$dashboard_chart_kunjungan = [];
$dashboard_chart_pendapatan = [];

for ($month = 1; $month <= 12; $month++) {
  $key = date('Y') . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
  $dashboard_chart_months[$key] = date('M', mktime(0, 0, 0, $month, 1));
  $dashboard_chart_pemesanan[$key] = 0;
  $dashboard_chart_pembayaran[$key] = 0;
  $dashboard_chart_kunjungan[$key] = 0;
  $dashboard_chart_pendapatan[$key] = 0;
}

$monthly_pemesanan = dashboard_rows($conn, "SELECT DATE_FORMAT(waktu_pesan, '%Y-%m') AS bulan, COUNT(*) AS jumlah
  FROM pemesanan_tiket
  WHERE YEAR(waktu_pesan)=YEAR(CURDATE())
  GROUP BY DATE_FORMAT(waktu_pesan, '%Y-%m')");
foreach ($monthly_pemesanan as $row) {
  if (isset($dashboard_chart_pemesanan[$row['bulan']])) {
    $dashboard_chart_pemesanan[$row['bulan']] = (int) $row['jumlah'];
  }
}

$monthly_pembayaran = dashboard_rows($conn, "SELECT DATE_FORMAT(waktu_bayar, '%Y-%m') AS bulan, COUNT(*) AS jumlah, COALESCE(SUM(pemesanan_tiket.total_tagihan), 0) AS total
  FROM pembayaran
  LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
  WHERE waktu_bayar IS NOT NULL AND YEAR(waktu_bayar)=YEAR(CURDATE()) AND LOWER(pembayaran.status_bayar) IN ('paid', 'settlement')
  GROUP BY DATE_FORMAT(waktu_bayar, '%Y-%m')");
foreach ($monthly_pembayaran as $row) {
  if (isset($dashboard_chart_pembayaran[$row['bulan']])) {
    $dashboard_chart_pembayaran[$row['bulan']] = (int) $row['jumlah'];
    $dashboard_chart_pendapatan[$row['bulan']] = (float) $row['total'];
  }
}

$monthly_kunjungan = dashboard_rows($conn, "SELECT DATE_FORMAT(waktu_kunjungan, '%Y-%m') AS bulan, COUNT(*) AS jumlah
  FROM data_kunjungan
  WHERE YEAR(waktu_kunjungan)=YEAR(CURDATE())
  GROUP BY DATE_FORMAT(waktu_kunjungan, '%Y-%m')");
foreach ($monthly_kunjungan as $row) {
  if (isset($dashboard_chart_kunjungan[$row['bulan']])) {
    $dashboard_chart_kunjungan[$row['bulan']] = (int) $row['jumlah'];
  }
}

$dashboard_feature_summary = [
  ['fitur' => 'Objek Wisata', 'kategori' => 'Master Data', 'jumlah' => $dashboard_total_objek_wisata, 'status' => 'Aktif', 'link' => 'master-data/objek-wisata'],
  ['fitur' => 'Informasi Wisata', 'kategori' => 'Master Data', 'jumlah' => $dashboard_total_informasi, 'status' => 'Publikasi', 'link' => 'master-data/informasi-wisata'],
  ['fitur' => 'Galeri', 'kategori' => 'Master Data', 'jumlah' => $dashboard_total_galeri, 'status' => 'Media', 'link' => 'master-data/galeri'],
  ['fitur' => 'Data Keranjang', 'kategori' => 'Transaksi', 'jumlah' => $dashboard_total_keranjang, 'status' => 'Tertunda', 'link' => 'manajemen-transaksi/data-keranjang'],
  ['fitur' => 'Pemesanan Tiket', 'kategori' => 'Transaksi', 'jumlah' => $dashboard_total_pemesanan, 'status' => 'Diproses', 'link' => 'manajemen-transaksi/pemesanan-tiket'],
  ['fitur' => 'Konfirmasi Pembayaran', 'kategori' => 'Transaksi', 'jumlah' => $dashboard_total_pembayaran, 'status' => 'Terkonfirmasi', 'link' => 'manajemen-transaksi/konfirmasi-pembayaran'],
  ['fitur' => 'E-Tiket', 'kategori' => 'Transaksi', 'jumlah' => $dashboard_total_e_tiket, 'status' => 'Digital', 'link' => 'manajemen-transaksi/e-tiket'],
  ['fitur' => 'Data Kunjungan', 'kategori' => 'Kunjungan', 'jumlah' => $dashboard_total_kunjungan, 'status' => 'Scan QR', 'link' => 'manajemen-kunjungan/data-kunjungan'],
  ['fitur' => 'Riwayat Transaksi', 'kategori' => 'Transaksi', 'jumlah' => $dashboard_total_riwayat, 'status' => 'Arsip', 'link' => 'manajemen-transaksi/riwayat-transaksi'],
  ['fitur' => 'Pengguna', 'kategori' => 'Manajemen Pengguna', 'jumlah' => $dashboard_total_users, 'status' => 'Akun', 'link' => 'manajemen-pengguna/users']
];

$dashboard_top_objek = dashboard_rows($conn, "SELECT objek_wisata.nama_wisata, objek_wisata.harga_tiket, COUNT(pemesanan_tiket.id) AS jumlah_pemesanan, COALESCE(SUM(pemesanan_tiket.total_tagihan), 0) AS total_tagihan
  FROM objek_wisata
  LEFT JOIN pemesanan_tiket ON objek_wisata.id=pemesanan_tiket.id_objek_wisata
  GROUP BY objek_wisata.id, objek_wisata.nama_wisata, objek_wisata.harga_tiket
  ORDER BY jumlah_pemesanan DESC, total_tagihan DESC
  LIMIT 5");

$dashboard_recent_activity = dashboard_rows($conn, "
  SELECT 'Pemesanan' AS jenis, pemesanan_tiket.kode_booking AS kode, users.name AS nama, objek_wisata.nama_wisata, pemesanan_tiket.total_tagihan AS nominal, pemesanan_tiket.waktu_pesan AS tanggal, pemesanan_tiket.status_pemesanan AS status
  FROM pemesanan_tiket
  LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
  LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id

  UNION ALL

  SELECT 'Pembayaran' AS jenis, pembayaran.order_id AS kode, users.name AS nama, objek_wisata.nama_wisata, pemesanan_tiket.total_tagihan AS nominal, pembayaran.waktu_bayar AS tanggal, pembayaran.status_bayar AS status
  FROM pembayaran
  LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
  LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
  LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id

  UNION ALL

  SELECT 'Kunjungan' AS jenis, e_tiket.kode_qr AS kode, users.name AS nama, objek_wisata.nama_wisata, 0 AS nominal, data_kunjungan.waktu_kunjungan AS tanggal, e_tiket.status_tiket AS status
  FROM data_kunjungan
  LEFT JOIN e_tiket ON data_kunjungan.id_e_tiket=e_tiket.id
  LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
  LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
  LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
  LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id

  ORDER BY tanggal DESC
  LIMIT 12");

$dashboard_feature_chart_labels = array_column($dashboard_feature_summary, 'fitur');
$dashboard_feature_chart_data = array_map(function ($feature) {
  return (int) $feature['jumlah'];
}, $dashboard_feature_summary);
