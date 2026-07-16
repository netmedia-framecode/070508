<?php
require_once(__DIR__ . "/../config/Midtrans.php");

function public_redirect($path = "")
{
  global $baseURL;
  header("Location: " . $baseURL . ltrim($path, "/"));
  exit();
}

function public_generate_code($prefix, $table, $column)
{
  global $conn;
  do {
    $code = $prefix . date("YmdHis") . rand(100, 999);
    $safeCode = valid($conn, $code);
    $check = mysqli_query($conn, "SELECT id FROM $table WHERE $column='$safeCode' LIMIT 1");
  } while ($check && mysqli_num_rows($check) > 0);

  return $code;
}

function public_midtrans_create_snap($payload)
{
  global $midtrans_server_key, $midtrans_snap_api_url;

  if (empty($midtrans_server_key)) {
    return [
      "success" => false,
      "message" => "Server Key Midtrans belum diatur di config/Midtrans.php atau environment MIDTRANS_SERVER_KEY."
    ];
  }

  if (!function_exists('curl_init')) {
    return [
      "success" => false,
      "message" => "Ekstensi cURL PHP belum aktif. Aktifkan cURL di XAMPP untuk membuat Snap Token Midtrans."
    ];
  }

  $curl = curl_init();
  curl_setopt_array($curl, [
    CURLOPT_URL => $midtrans_snap_api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
      "Accept: application/json",
      "Content-Type: application/json",
      "Authorization: Basic " . base64_encode($midtrans_server_key . ":")
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 12
  ]);

  $response = curl_exec($curl);
  $error = curl_error($curl);
  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  curl_close($curl);

  if ($error) {
    return ["success" => false, "message" => "Koneksi ke Midtrans gagal: " . $error];
  }

  $data = json_decode($response, true);
  if ($httpCode < 200 || $httpCode >= 300 || empty($data["token"])) {
    return [
      "success" => false,
      "message" => $data["error_messages"][0] ?? "Snap token Midtrans gagal dibuat."
    ];
  }

  return ["success" => true, "token" => $data["token"], "redirect_url" => $data["redirect_url"] ?? ""];
}

function public_midtrans_check_status($order_id)
{
  global $midtrans_server_key, $midtrans_status_api_base_url;

  if (empty($midtrans_server_key)) {
    return [
      "success" => false,
      "message" => "Server Key Midtrans belum diatur."
    ];
  }

  if (!function_exists('curl_init')) {
    return [
      "success" => false,
      "message" => "Ekstensi cURL PHP belum aktif."
    ];
  }

  $curl = curl_init();
  curl_setopt_array($curl, [
    CURLOPT_URL => rtrim($midtrans_status_api_base_url, "/") . "/" . rawurlencode($order_id) . "/status",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
      "Accept: application/json",
      "Authorization: Basic " . base64_encode($midtrans_server_key . ":")
    ],
    CURLOPT_TIMEOUT => 8
  ]);

  $response = curl_exec($curl);
  $error = curl_error($curl);
  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  curl_close($curl);

  if ($error) {
    return ["success" => false, "message" => "Gagal mengecek status Midtrans: " . $error];
  }

  $data = json_decode($response, true);
  if ($httpCode < 200 || $httpCode >= 300) {
    return [
      "success" => false,
      "message" => $data["status_message"] ?? "Status pembayaran Midtrans gagal dicek."
    ];
  }

  return ["success" => true, "data" => $data];
}

function public_map_midtrans_status($midtrans_status, $fraud_status = "")
{
  $transaction_status = strtolower(trim($midtrans_status));
  $fraud_status = strtolower(trim($fraud_status));

  if ($transaction_status == "settlement") {
    return ["status_bayar" => "Settlement", "status_pemesanan" => "Confirmed"];
  }

  if ($transaction_status == "capture" && ($fraud_status == "accept" || $fraud_status == "")) {
    return ["status_bayar" => "Paid", "status_pemesanan" => "Confirmed"];
  }

  if ($transaction_status == "pending") {
    return ["status_bayar" => "Unpaid", "status_pemesanan" => "Pending"];
  }

  if ($transaction_status == "expire") {
    return ["status_bayar" => "Expired", "status_pemesanan" => "Cancelled"];
  }

  if (in_array($transaction_status, ["deny", "cancel", "failure"])) {
    return ["status_bayar" => "Failed", "status_pemesanan" => "Cancelled"];
  }

  return ["status_bayar" => "Unpaid", "status_pemesanan" => "Pending"];
}

function public_apply_midtrans_payment_status($order_id, $status_data)
{
  global $conn;

  $order_id = valid($conn, $order_id);
  $payment_type = valid($conn, $status_data["payment_type"] ?? "Midtrans");
  $mapped = public_map_midtrans_status($status_data["transaction_status"] ?? "", $status_data["fraud_status"] ?? "");
  $status_bayar = $mapped["status_bayar"];
  $status_pemesanan = $mapped["status_pemesanan"];
  $paidStatuses = ["paid", "settlement"];
  $waktu_bayar = date("Y-m-d H:i:s");
  $waktu_bayar_sql = in_array(strtolower($status_bayar), $paidStatuses) ? "'$waktu_bayar'" : "NULL";

  $queryPayment = mysqli_query($conn, "SELECT * FROM pembayaran WHERE order_id='$order_id' LIMIT 1");
  $payment = mysqli_fetch_assoc($queryPayment);
  if (!$payment) {
    return ["success" => false, "message" => "Order ID tidak ditemukan di database lokal."];
  }

  mysqli_query($conn, "UPDATE pembayaran SET
    metode_pembayaran='$payment_type',
    waktu_bayar=$waktu_bayar_sql,
    status_bayar='$status_bayar'
    WHERE id='$payment[id]'");

  mysqli_query($conn, "UPDATE pemesanan_tiket SET status_pemesanan='$status_pemesanan' WHERE id='$payment[id_pemesanan]'");

  if (function_exists('sinkron_e_tiket_pembayaran')) {
    sinkron_e_tiket_pembayaran($conn, $payment["id"], $status_bayar, $waktu_bayar);
  }

  if (function_exists('sinkron_riwayat_transaksi')) {
    sinkron_riwayat_transaksi($conn, $payment["id_pemesanan"]);
  }

  return ["success" => true, "status_bayar" => $status_bayar, "status_pemesanan" => $status_pemesanan];
}

function public_sync_midtrans_order($order_id, $id_user)
{
  global $conn;

  $order_id = valid($conn, $order_id);
  $id_user = (int) $id_user;
  $checkPayment = mysqli_query($conn, "SELECT pembayaran.id
    FROM pembayaran
    LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
    WHERE pembayaran.order_id='$order_id' AND pemesanan_tiket.id_wisatawan='$id_user'
    LIMIT 1");

  if (!$checkPayment || mysqli_num_rows($checkPayment) == 0) {
    return ["success" => false, "message" => "Pembayaran tidak ditemukan untuk akun ini."];
  }

  $status = public_midtrans_check_status($order_id);
  if (!$status["success"]) {
    return $status;
  }

  return public_apply_midtrans_payment_status($order_id, $status["data"]);
}

function public_sync_user_pending_payments($id_user)
{
  global $conn;

  $id_user = (int) $id_user;
  $payments = public_rows($conn, "SELECT pembayaran.order_id
    FROM pembayaran
    LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
    WHERE pemesanan_tiket.id_wisatawan='$id_user'
      AND pembayaran.order_id IS NOT NULL
      AND pembayaran.order_id!=''
      AND LOWER(COALESCE(pembayaran.status_bayar, 'unpaid')) IN ('unpaid', 'pending')
    ORDER BY pembayaran.id DESC
    LIMIT 3");

  foreach ($payments as $payment) {
    public_sync_midtrans_order($payment["order_id"], $id_user);
  }
}

function public_get_or_create_payment($id_pemesanan)
{
  global $conn;

  $id_pemesanan = (int) $id_pemesanan;
  $queryPayment = mysqli_query($conn, "SELECT * FROM pembayaran WHERE id_pemesanan='$id_pemesanan' ORDER BY id DESC LIMIT 1");
  $payment = mysqli_fetch_assoc($queryPayment);
  if ($payment && !empty($payment["snap_token"])) {
    return ["success" => true, "payment" => $payment];
  }

  $queryOrder = mysqli_query($conn, "SELECT pemesanan_tiket.*, users.name, users.email, users.no_hp, objek_wisata.nama_wisata, objek_wisata.harga_tiket
    FROM pemesanan_tiket
    LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
    LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
    WHERE pemesanan_tiket.id='$id_pemesanan'
    LIMIT 1");
  $order = mysqli_fetch_assoc($queryOrder);

  if (!$order) {
    return ["success" => false, "message" => "Data pemesanan tidak ditemukan."];
  }

  $orderId = $payment["order_id"] ?? public_generate_code("ORDER-", "pembayaran", "order_id");
  $payload = [
    "transaction_details" => [
      "order_id" => $orderId,
      "gross_amount" => (int) $order["total_tagihan"]
    ],
    "customer_details" => [
      "first_name" => $order["name"] ?: "Wisatawan",
      "email" => $order["email"] ?: "wisatawan@example.com",
      "phone" => $order["no_hp"] ?: ""
    ],
    "item_details" => [[
      "id" => "OW-" . $order["id_objek_wisata"],
      "price" => (int) $order["harga_tiket"],
      "quantity" => (int) $order["jumlah_tiket"],
      "name" => substr($order["nama_wisata"] ?: "Tiket Wisata", 0, 50)
    ]]
  ];

  $snap = public_midtrans_create_snap($payload);
  if (!$snap["success"]) {
    return $snap;
  }

  $snapToken = valid($conn, $snap["token"]);
  if ($payment) {
    mysqli_query($conn, "UPDATE pembayaran SET order_id='$orderId', snap_token='$snapToken', metode_pembayaran='Midtrans Snap', status_bayar='Unpaid' WHERE id='$payment[id]'");
    $idPayment = $payment["id"];
  } else {
    mysqli_query($conn, "INSERT INTO pembayaran (id_pemesanan, order_id, snap_token, metode_pembayaran, status_bayar)
      VALUES ('$id_pemesanan', '$orderId', '$snapToken', 'Midtrans Snap', 'Unpaid')");
    $idPayment = mysqli_insert_id($conn);
  }

  if (mysqli_errno($conn) != 0) {
    return ["success" => false, "message" => "Data pembayaran gagal disimpan: " . mysqli_error($conn)];
  }

  $queryNewPayment = mysqli_query($conn, "SELECT * FROM pembayaran WHERE id='$idPayment'");
  return ["success" => true, "payment" => mysqli_fetch_assoc($queryNewPayment)];
}

function public_user_orders($id_user)
{
  global $conn;
  $id_user = (int) $id_user;
  $lokasiSelect = public_objek_location_select('objek_wisata');
  return public_rows($conn, "SELECT pemesanan_tiket.*, objek_wisata.nama_wisata, $lokasiSelect, objek_wisata.gambar, pembayaran.order_id, pembayaran.snap_token, pembayaran.status_bayar, e_tiket.kode_qr, e_tiket.status_tiket, e_tiket.berlaku_sampai
    FROM pemesanan_tiket
    LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
    LEFT JOIN pembayaran ON pembayaran.id_pemesanan=pemesanan_tiket.id
    LEFT JOIN e_tiket ON e_tiket.id_pembayaran=pembayaran.id
    WHERE pemesanan_tiket.id_wisatawan='$id_user'
    ORDER BY pemesanan_tiket.waktu_pesan DESC, pemesanan_tiket.id DESC");
}

function public_sync_expired_tickets($id_user)
{
  global $conn;
  $id_user = (int) $id_user;
  $expiredTickets = public_rows($conn, "SELECT e_tiket.id, pembayaran.id_pemesanan
    FROM e_tiket
    LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
    LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
    WHERE pemesanan_tiket.id_wisatawan='$id_user'
      AND LOWER(e_tiket.status_tiket)='active'
      AND e_tiket.berlaku_sampai IS NOT NULL
      AND e_tiket.berlaku_sampai < NOW()");

  foreach ($expiredTickets as $ticket) {
    mysqli_query($conn, "UPDATE e_tiket SET status_tiket='Expired' WHERE id='$ticket[id]'");
    if (!empty($ticket['id_pemesanan']) && function_exists('sinkron_riwayat_transaksi')) {
      sinkron_riwayat_transaksi($conn, $ticket['id_pemesanan']);
    }
  }

  return count($expiredTickets);
}

function public_user_payments($id_user)
{
  global $conn;
  $id_user = (int) $id_user;
  return public_rows($conn, "SELECT pembayaran.*, pemesanan_tiket.kode_booking, pemesanan_tiket.total_tagihan, objek_wisata.nama_wisata
    FROM pembayaran
    LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
    LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
    WHERE pemesanan_tiket.id_wisatawan='$id_user'
    ORDER BY pembayaran.id DESC");
}

function public_user_tickets($id_user)
{
  global $conn;
  $id_user = (int) $id_user;
  return public_rows($conn, "SELECT e_tiket.*, pembayaran.order_id, pembayaran.status_bayar, pemesanan_tiket.kode_booking, pemesanan_tiket.tgl_kunjungan, pemesanan_tiket.jumlah_tiket, pemesanan_tiket.total_tagihan, objek_wisata.nama_wisata
    FROM e_tiket
    LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
    LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
    LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
    WHERE pemesanan_tiket.id_wisatawan='$id_user'
    ORDER BY e_tiket.id DESC");
}

function public_user_history($id_user)
{
  global $conn;
  $id_user = (int) $id_user;
  return public_rows($conn, "SELECT riwayat_transaksi.*, pemesanan_tiket.kode_booking, pemesanan_tiket.tgl_kunjungan, pemesanan_tiket.jumlah_tiket, pemesanan_tiket.total_tagihan, objek_wisata.nama_wisata
    FROM riwayat_transaksi
    LEFT JOIN pemesanan_tiket ON riwayat_transaksi.id_pemesanan=pemesanan_tiket.id
    LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
    WHERE riwayat_transaksi.id_wisatawan='$id_user'
    ORDER BY riwayat_transaksi.tanggal_selesai DESC, riwayat_transaksi.id DESC");
}

function public_user_cart($id_user)
{
  global $conn;
  $id_user = (int) $id_user;
  $lokasiSelect = public_objek_location_select('objek_wisata');
  return public_rows($conn, "SELECT keranjang.*, objek_wisata.nama_wisata, $lokasiSelect, objek_wisata.harga_tiket, objek_wisata.gambar
    FROM keranjang
    LEFT JOIN objek_wisata ON keranjang.id_objek_wisata=objek_wisata.id
    WHERE keranjang.id_wisatawan='$id_user'
    ORDER BY keranjang.id DESC");
}

if (isset($_POST["public_add_cart"])) {
  public_require_wisatawan($baseURL);
  $id_user = public_user_id();
  $id_objek_wisata = (int) ($_POST["id_objek_wisata"] ?? 0);
  $jumlah_tiket = max(1, (int) ($_POST["jumlah_tiket"] ?? 1));
  $queryObjek = mysqli_query($conn, "SELECT harga_tiket FROM objek_wisata WHERE id='$id_objek_wisata'");
  $objek = mysqli_fetch_assoc($queryObjek);

  if (!$objek) {
    alert("Objek wisata tidak ditemukan.", "danger");
    public_redirect("objek-wisata");
  }

  $total = (int) $objek["harga_tiket"] * $jumlah_tiket;
  $checkCart = mysqli_query($conn, "SELECT id, jumlah_tiket FROM keranjang WHERE id_wisatawan='$id_user' AND id_objek_wisata='$id_objek_wisata'");
  $cart = mysqli_fetch_assoc($checkCart);
  if ($cart) {
    $newQty = (int) $cart["jumlah_tiket"] + $jumlah_tiket;
    $newTotal = (int) $objek["harga_tiket"] * $newQty;
    mysqli_query($conn, "UPDATE keranjang SET jumlah_tiket='$newQty', total_harga_sementara='$newTotal' WHERE id='$cart[id]'");
  } else {
    mysqli_query($conn, "INSERT INTO keranjang (id_wisatawan, id_objek_wisata, jumlah_tiket, total_harga_sementara)
      VALUES ('$id_user', '$id_objek_wisata', '$jumlah_tiket', '$total')");
  }

  alert("Tiket berhasil ditambahkan ke keranjang.", "success");
  public_redirect("keranjang");
}

if (isset($_POST["public_delete_cart"])) {
  public_require_wisatawan($baseURL);
  $id = (int) ($_POST["id"] ?? 0);
  $id_user = public_user_id();
  mysqli_query($conn, "DELETE FROM keranjang WHERE id='$id' AND id_wisatawan='$id_user'");
  alert("Item keranjang berhasil dihapus.", "success");
  public_redirect("keranjang");
}

if (isset($_POST["public_checkout_order"])) {
  public_require_wisatawan($baseURL);
  $id_user = public_user_id();
  $id_pemesanan = (int) ($_POST["id_pemesanan"] ?? 0);
  $check_order = mysqli_query($conn, "SELECT id FROM pemesanan_tiket WHERE id='$id_pemesanan' AND id_wisatawan='$id_user' LIMIT 1");

  if (!$check_order || mysqli_num_rows($check_order) == 0) {
    alert("Data pesanan tidak ditemukan.", "danger");
    public_redirect("pesanan-saya");
  }

  $_SESSION["project_wisata_sumba_barat_daya"]["auto_checkout_order"] = $id_pemesanan;
  public_redirect("pesanan-saya?checkout=" . $id_pemesanan . "#checkout");
}

if (isset($_POST["public_create_order"])) {
  public_require_wisatawan($baseURL);
  $id_user = public_user_id();
  $id_objek_wisata = (int) ($_POST["id_objek_wisata"] ?? 0);
  $jumlah_tiket = max(1, (int) ($_POST["jumlah_tiket"] ?? 1));
  $tgl_kunjungan = valid($conn, $_POST["tgl_kunjungan"] ?? "");
  $id_cart = (int) ($_POST["id_cart"] ?? 0);

  if (empty($tgl_kunjungan)) {
    alert("Tanggal kunjungan wajib diisi.", "danger");
    public_redirect("pesanan-saya?objek=" . $id_objek_wisata);
  }

  $queryObjek = mysqli_query($conn, "SELECT harga_tiket FROM objek_wisata WHERE id='$id_objek_wisata'");
  $objek = mysqli_fetch_assoc($queryObjek);
  if (!$objek) {
    alert("Objek wisata tidak ditemukan.", "danger");
    public_redirect("objek-wisata");
  }

  $kode_booking = public_generate_code("BK", "pemesanan_tiket", "kode_booking");
  $total = (int) $objek["harga_tiket"] * $jumlah_tiket;
  mysqli_query($conn, "INSERT INTO pemesanan_tiket
    (kode_booking, id_wisatawan, id_objek_wisata, tgl_kunjungan, jumlah_tiket, total_tagihan, status_pemesanan)
    VALUES
    ('$kode_booking', '$id_user', '$id_objek_wisata', '$tgl_kunjungan', '$jumlah_tiket', '$total', 'Pending')");
  $id_pemesanan = mysqli_insert_id($conn);

  if (mysqli_affected_rows($conn) > 0) {
    sinkron_riwayat_transaksi($conn, $id_pemesanan);
    if ($id_cart > 0) {
      mysqli_query($conn, "DELETE FROM keranjang WHERE id='$id_cart' AND id_wisatawan='$id_user'");
    }
    alert("Pemesanan berhasil dibuat. Silakan lanjutkan pembayaran.", "success");
    public_redirect("pesanan-saya?checkout=" . $id_pemesanan);
  }

  alert("Pemesanan gagal dibuat.", "danger");
  public_redirect("pesanan-saya");
}

if (isset($_POST["public_update_profile"])) {
  public_require_wisatawan($baseURL);
  $id_user = public_user_id();
  $name = valid($conn, $_POST["name"] ?? "");
  $no_hp = valid($conn, $_POST["no_hp"] ?? "");
  $asal_daerah = valid($conn, $_POST["asal_daerah"] ?? "");
  mysqli_query($conn, "UPDATE users SET name='$name', no_hp='$no_hp', asal_daerah='$asal_daerah' WHERE id_user='$id_user'");

  if (mysqli_errno($conn) == 0) {
    $_SESSION["project_wisata_sumba_barat_daya"]["users"]["name"] = $name;
    alert("Profil berhasil diperbarui.", "success");
  } else {
    alert("Profil gagal diperbarui.", "danger");
  }
  public_redirect("profil");
}
