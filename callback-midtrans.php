<?php
require_once("config/Base.php");
require_once("config/Midtrans.php");

header("Content-Type: application/json");

if (!function_exists('callback_sinkron_riwayat_transaksi')) {
  function callback_sinkron_riwayat_transaksi($conn, $id_pemesanan)
  {
    $query_pemesanan = mysqli_query($conn, "SELECT id, id_wisatawan, status_pemesanan FROM pemesanan_tiket WHERE id='$id_pemesanan'");
    $data_pemesanan = mysqli_fetch_assoc($query_pemesanan);
    if (!$data_pemesanan) {
      return false;
    }

    $status_akhir = $data_pemesanan['status_pemesanan'] ?: "Pending";
    $query_pembayaran = mysqli_query($conn, "SELECT pembayaran.status_bayar, e_tiket.status_tiket
      FROM pembayaran
      LEFT JOIN e_tiket ON e_tiket.id_pembayaran=pembayaran.id
      WHERE pembayaran.id_pemesanan='$id_pemesanan'
      ORDER BY pembayaran.id DESC
      LIMIT 1");
    $data_pembayaran = mysqli_fetch_assoc($query_pembayaran);

    if ($data_pembayaran) {
      if (!empty($data_pembayaran['status_tiket'])) {
        $status_akhir = $data_pembayaran['status_tiket'];
      } elseif (!empty($data_pembayaran['status_bayar'])) {
        $status_akhir = $data_pembayaran['status_bayar'];
      }
    }

    $check_riwayat = mysqli_query($conn, "SELECT id FROM riwayat_transaksi WHERE id_pemesanan='$id_pemesanan'");
    $data_riwayat = mysqli_fetch_assoc($check_riwayat);
    if ($data_riwayat) {
      mysqli_query($conn, "UPDATE riwayat_transaksi SET id_wisatawan='$data_pemesanan[id_wisatawan]', status_akhir='$status_akhir', tanggal_selesai=current_timestamp WHERE id='$data_riwayat[id]'");
    } else {
      mysqli_query($conn, "INSERT INTO riwayat_transaksi (id_wisatawan, id_pemesanan, status_akhir) VALUES ('$data_pemesanan[id_wisatawan]', '$id_pemesanan', '$status_akhir')");
    }

    return true;
  }
}

if (!function_exists('callback_sinkron_e_tiket_pembayaran')) {
  function callback_sinkron_e_tiket_pembayaran($conn, $id_pembayaran, $status_bayar, $waktu_bayar = "")
  {
    $status_bayar_lower = strtolower(trim($status_bayar));
    $check_e_tiket = mysqli_query($conn, "SELECT id FROM e_tiket WHERE id_pembayaran='$id_pembayaran'");
    $data_e_tiket = mysqli_fetch_assoc($check_e_tiket);

    if ($status_bayar_lower == "paid" || $status_bayar_lower == "settlement") {
      $mulai_berlaku = !empty($waktu_bayar) ? $waktu_bayar : date("Y-m-d H:i:s");
      $berlaku_sampai = date("Y-m-d H:i:s", strtotime($mulai_berlaku . " +1 day"));
      if ($data_e_tiket) {
        mysqli_query($conn, "UPDATE e_tiket SET status_tiket='Active', berlaku_sampai='$berlaku_sampai' WHERE id='$data_e_tiket[id]'");
      } else {
        do {
          $kode_qr = "ETK-" . date("YmdHis") . rand(100, 999);
          $check_qr = mysqli_query($conn, "SELECT id FROM e_tiket WHERE kode_qr='$kode_qr'");
        } while ($check_qr && mysqli_num_rows($check_qr) > 0);
        mysqli_query($conn, "INSERT INTO e_tiket (id_pembayaran, kode_qr, status_tiket, berlaku_sampai) VALUES ('$id_pembayaran', '$kode_qr', 'Active', '$berlaku_sampai')");
      }
    } elseif ($data_e_tiket) {
      mysqli_query($conn, "UPDATE e_tiket SET status_tiket='Inactive', berlaku_sampai=NULL WHERE id='$data_e_tiket[id]'");
    }
  }
}

$rawBody = file_get_contents("php://input");
$notification = json_decode($rawBody, true);

if (!$notification || empty($notification["order_id"])) {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Payload tidak valid."]);
  exit();
}

$order_id = valid($conn, $notification["order_id"]);
$status_code = $notification["status_code"] ?? "";
$gross_amount = $notification["gross_amount"] ?? "";
$signature_key = $notification["signature_key"] ?? "";

if (!empty($midtrans_server_key) && !empty($signature_key)) {
  $expected_signature = hash("sha512", $order_id . $status_code . $gross_amount . $midtrans_server_key);
  if (!hash_equals($expected_signature, $signature_key)) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Signature Midtrans tidak valid."]);
    exit();
  }
}

$transaction_status = strtolower($notification["transaction_status"] ?? "");
$fraud_status = strtolower($notification["fraud_status"] ?? "");
$payment_type = valid($conn, $notification["payment_type"] ?? "Midtrans");
$waktu_bayar = date("Y-m-d H:i:s");
$status_bayar = "Unpaid";
$status_pemesanan = "Pending";

if ($transaction_status == "settlement") {
  $status_bayar = "Settlement";
  $status_pemesanan = "Confirmed";
} elseif ($transaction_status == "capture" && ($fraud_status == "accept" || $fraud_status == "")) {
  $status_bayar = "Paid";
  $status_pemesanan = "Confirmed";
} elseif ($transaction_status == "pending") {
  $status_bayar = "Unpaid";
} elseif ($transaction_status == "expire") {
  $status_bayar = "Expired";
  $status_pemesanan = "Cancelled";
} elseif (in_array($transaction_status, ["deny", "cancel", "failure"])) {
  $status_bayar = "Failed";
  $status_pemesanan = "Cancelled";
}

$query_payment = mysqli_query($conn, "SELECT * FROM pembayaran WHERE order_id='$order_id' LIMIT 1");
$payment = mysqli_fetch_assoc($query_payment);

if (!$payment) {
  http_response_code(404);
  echo json_encode(["success" => false, "message" => "Order ID tidak ditemukan."]);
  exit();
}

$waktu_bayar_sql = in_array(strtolower($status_bayar), ["paid", "settlement"]) ? "'$waktu_bayar'" : "NULL";
mysqli_query($conn, "UPDATE pembayaran SET
  metode_pembayaran='$payment_type',
  waktu_bayar=$waktu_bayar_sql,
  status_bayar='$status_bayar'
  WHERE id='$payment[id]'");

mysqli_query($conn, "UPDATE pemesanan_tiket SET status_pemesanan='$status_pemesanan' WHERE id='$payment[id_pemesanan]'");
if (function_exists('sinkron_e_tiket_pembayaran')) {
  sinkron_e_tiket_pembayaran($conn, $payment["id"], $status_bayar, $waktu_bayar);
} else {
  callback_sinkron_e_tiket_pembayaran($conn, $payment["id"], $status_bayar, $waktu_bayar);
}

if (function_exists('sinkron_riwayat_transaksi')) {
  sinkron_riwayat_transaksi($conn, $payment["id_pemesanan"]);
} else {
  callback_sinkron_riwayat_transaksi($conn, $payment["id_pemesanan"]);
}

echo json_encode(["success" => true, "message" => "Callback diproses."]);
