<?php
  
          require_once("../../config/Base.php");
          require_once("../../config/Auth.php");
          require_once("../../config/Alert.php");
          require_once("../../views/manajemen-kunjungan/redirect.php");

          $select_data_kunjungan = "SELECT data_kunjungan.*, e_tiket.kode_qr, e_tiket.status_tiket, pemesanan_tiket.kode_booking, pemesanan_tiket.tgl_kunjungan, users.name AS nama_wisatawan, users.email, petugas.name AS nama_petugas, objek_wisata.nama_wisata
            FROM data_kunjungan
            LEFT JOIN e_tiket ON data_kunjungan.id_e_tiket=e_tiket.id
            LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
            LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
            LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
            LEFT JOIN users AS petugas ON data_kunjungan.id_petugas=petugas.id_user
            LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
            ORDER BY data_kunjungan.waktu_kunjungan DESC, data_kunjungan.id DESC";
          $views_data_kunjungan = mysqli_query($conn, $select_data_kunjungan);

          if (isset($_POST["scan_qr_wisatawan"])) {
            $validated_post = array_map(function ($value) use ($conn) {
              return valid($conn, $value);
            }, $_POST);
            if (data_kunjungan($conn, $validated_post, $action = 'insert') > 0) {
              $message = "Data kunjungan berhasil disimpan.";
              $message_type = "success";
              alert($message, $message_type);
              header("Location: data-kunjungan");
              exit();
            }
          }
          
