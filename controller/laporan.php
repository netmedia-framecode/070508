<?php
  
          require_once("../../config/Base.php");
          require_once("../../config/Auth.php");
          require_once("../../config/Alert.php");
          require_once("../../views/laporan/redirect.php");

          if (isset($_GET['export_transaksi_excel'])) {
            require_once(__DIR__ . "/../assets/vendor/autoload.php");

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet_configs = [
              [
                'title' => 'Pemesanan',
                'headers' => ['Kode Booking', 'Wisatawan', 'Email', 'Objek Wisata', 'Tanggal Kunjungan', 'Jumlah Tiket', 'Total Tagihan', 'Waktu Pesan', 'Status'],
                'query' => "SELECT pemesanan_tiket.kode_booking, users.name AS nama_wisatawan, users.email, objek_wisata.nama_wisata, pemesanan_tiket.tgl_kunjungan, pemesanan_tiket.jumlah_tiket, pemesanan_tiket.total_tagihan, pemesanan_tiket.waktu_pesan, pemesanan_tiket.status_pemesanan
                  FROM pemesanan_tiket
                  LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
                  LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
                  ORDER BY pemesanan_tiket.waktu_pesan DESC",
                'fields' => ['kode_booking', 'nama_wisatawan', 'email', 'nama_wisata', 'tgl_kunjungan', 'jumlah_tiket', 'total_tagihan', 'waktu_pesan', 'status_pemesanan']
              ],
              [
                'title' => 'Pembayaran',
                'headers' => ['Order ID', 'Kode Booking', 'Wisatawan', 'Email', 'Objek Wisata', 'Total Tagihan', 'Metode', 'Waktu Bayar', 'Status Bayar'],
                'query' => "SELECT pembayaran.order_id, pemesanan_tiket.kode_booking, users.name AS nama_wisatawan, users.email, objek_wisata.nama_wisata, pemesanan_tiket.total_tagihan, pembayaran.metode_pembayaran, pembayaran.waktu_bayar, pembayaran.status_bayar
                  FROM pembayaran
                  LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
                  LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
                  LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
                  ORDER BY pembayaran.waktu_bayar DESC, pembayaran.id DESC",
                'fields' => ['order_id', 'kode_booking', 'nama_wisatawan', 'email', 'nama_wisata', 'total_tagihan', 'metode_pembayaran', 'waktu_bayar', 'status_bayar']
              ],
              [
                'title' => 'E-Tiket',
                'headers' => ['Kode QR', 'Order ID', 'Kode Booking', 'Wisatawan', 'Objek Wisata', 'Berlaku Sampai', 'Status Tiket'],
                'query' => "SELECT e_tiket.kode_qr, pembayaran.order_id, pemesanan_tiket.kode_booking, users.name AS nama_wisatawan, objek_wisata.nama_wisata, e_tiket.berlaku_sampai, e_tiket.status_tiket
                  FROM e_tiket
                  LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
                  LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
                  LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
                  LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
                  ORDER BY e_tiket.id DESC",
                'fields' => ['kode_qr', 'order_id', 'kode_booking', 'nama_wisatawan', 'nama_wisata', 'berlaku_sampai', 'status_tiket']
              ],
              [
                'title' => 'Kunjungan',
                'headers' => ['Kode QR', 'Kode Booking', 'Wisatawan', 'Objek Wisata', 'Petugas', 'Waktu Kunjungan', 'Keterangan'],
                'query' => "SELECT e_tiket.kode_qr, pemesanan_tiket.kode_booking, users.name AS nama_wisatawan, objek_wisata.nama_wisata, petugas.name AS nama_petugas, data_kunjungan.waktu_kunjungan, data_kunjungan.keterangan
                  FROM data_kunjungan
                  LEFT JOIN e_tiket ON data_kunjungan.id_e_tiket=e_tiket.id
                  LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
                  LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
                  LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
                  LEFT JOIN users AS petugas ON data_kunjungan.id_petugas=petugas.id_user
                  LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
                  ORDER BY data_kunjungan.waktu_kunjungan DESC",
                'fields' => ['kode_qr', 'kode_booking', 'nama_wisatawan', 'nama_wisata', 'nama_petugas', 'waktu_kunjungan', 'keterangan']
              ]
            ];

            foreach ($sheet_configs as $sheet_index => $config) {
              $sheet = $sheet_index === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
              $sheet->setTitle($config['title']);

              foreach ($config['headers'] as $column_index => $header) {
                $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column_index + 1);
                $sheet->setCellValue($column . '1', $header);
              }

              $result = mysqli_query($conn, $config['query']);
              $row_number = 2;
              if ($result instanceof mysqli_result) {
                foreach ($result as $row) {
                  foreach ($config['fields'] as $column_index => $field) {
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column_index + 1);
                    $sheet->setCellValue($column . $row_number, $row[$field] ?: '-');
                  }
                  $row_number++;
                }
              }

              $highest_column = $sheet->getHighestColumn();
              $sheet->getStyle("A1:{$highest_column}1")->getFont()->setBold(true);
              $sheet->getStyle("A1:{$highest_column}1")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFEFF3FF');
              foreach (range('A', $highest_column) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
              }
            }

            $spreadsheet->setActiveSheetIndex(0);
            $filename = "laporan-transaksi-" . date('Ymd-His') . ".xlsx";

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
          }

          if (isset($_GET['export_kunjungan_excel'])) {
            require_once(__DIR__ . "/../assets/vendor/autoload.php");

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Laporan Kunjungan');
            $headers = ['Kode QR', 'Kode Booking', 'Wisatawan', 'Email', 'Objek Wisata', 'Tanggal Kunjungan', 'Petugas', 'Waktu Scan', 'Status Tiket', 'Keterangan'];
            $fields = ['kode_qr', 'kode_booking', 'nama_wisatawan', 'email', 'nama_wisata', 'tgl_kunjungan', 'nama_petugas', 'waktu_kunjungan', 'status_tiket', 'keterangan'];

            foreach ($headers as $column_index => $header) {
              $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column_index + 1);
              $sheet->setCellValue($column . '1', $header);
            }

            $result = mysqli_query($conn, "SELECT e_tiket.kode_qr, pemesanan_tiket.kode_booking, users.name AS nama_wisatawan, users.email, objek_wisata.nama_wisata, pemesanan_tiket.tgl_kunjungan, petugas.name AS nama_petugas, data_kunjungan.waktu_kunjungan, e_tiket.status_tiket, data_kunjungan.keterangan
              FROM data_kunjungan
              LEFT JOIN e_tiket ON data_kunjungan.id_e_tiket=e_tiket.id
              LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
              LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
              LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
              LEFT JOIN users AS petugas ON data_kunjungan.id_petugas=petugas.id_user
              LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
              ORDER BY data_kunjungan.waktu_kunjungan DESC");

            $row_number = 2;
            if ($result instanceof mysqli_result) {
              foreach ($result as $row) {
                foreach ($fields as $column_index => $field) {
                  $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column_index + 1);
                  $sheet->setCellValue($column . $row_number, $row[$field] ?: '-');
                }
                $row_number++;
              }
            }

            $highest_column = $sheet->getHighestColumn();
            $sheet->getStyle("A1:{$highest_column}1")->getFont()->setBold(true);
            $sheet->getStyle("A1:{$highest_column}1")->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFEAF7F1');
            foreach (range('A', $highest_column) as $column) {
              $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $filename = "laporan-kunjungan-" . date('Ymd-His') . ".xlsx";

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
          }

          $total_pemesanan_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_tagihan), 0) AS total, COUNT(*) AS jumlah_data FROM pemesanan_tiket"));
          $total_pembayaran_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(pemesanan_tiket.total_tagihan), 0) AS total, COUNT(*) AS jumlah_data
            FROM pembayaran
            LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
            WHERE LOWER(pembayaran.status_bayar) IN ('paid', 'settlement')"));
          $total_e_tiket_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jumlah_data FROM e_tiket"));
          $total_kunjungan_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jumlah_data FROM data_kunjungan"));

          $total_pemesanan = (float) $total_pemesanan_data['total'];
          $jumlah_pemesanan = (int) $total_pemesanan_data['jumlah_data'];
          $total_pembayaran = (float) $total_pembayaran_data['total'];
          $jumlah_pembayaran = (int) $total_pembayaran_data['jumlah_data'];
          $jumlah_e_tiket = (int) $total_e_tiket_data['jumlah_data'];
          $jumlah_kunjungan = (int) $total_kunjungan_data['jumlah_data'];
          $jumlah_transaksi_wisata = $jumlah_pemesanan + $jumlah_pembayaran + $jumlah_e_tiket + $jumlah_kunjungan;

          $select_laporan_transaksi = "
            SELECT
              pemesanan_tiket.id AS id_transaksi,
              'Pemesanan' AS sumber,
              'Pemesanan Tiket' AS jenis_transaksi,
              pemesanan_tiket.kode_booking,
              '-' AS order_id,
              '-' AS kode_qr,
              users.name AS nama_wisatawan,
              users.email,
              objek_wisata.nama_wisata,
              pemesanan_tiket.total_tagihan AS nominal,
              pemesanan_tiket.jumlah_tiket,
              pemesanan_tiket.waktu_pesan AS tanggal,
              pemesanan_tiket.status_pemesanan AS status,
              CONCAT('Tanggal kunjungan ', DATE_FORMAT(pemesanan_tiket.tgl_kunjungan, '%d-%m-%Y')) AS keterangan
            FROM pemesanan_tiket
            LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
            LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id

            UNION ALL

            SELECT
              pembayaran.id AS id_transaksi,
              'Pembayaran' AS sumber,
              COALESCE(pembayaran.metode_pembayaran, 'Pembayaran') AS jenis_transaksi,
              pemesanan_tiket.kode_booking,
              pembayaran.order_id,
              '-' AS kode_qr,
              users.name AS nama_wisatawan,
              users.email,
              objek_wisata.nama_wisata,
              pemesanan_tiket.total_tagihan AS nominal,
              pemesanan_tiket.jumlah_tiket,
              pembayaran.waktu_bayar AS tanggal,
              pembayaran.status_bayar AS status,
              'Konfirmasi pembayaran tiket wisata' AS keterangan
            FROM pembayaran
            LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
            LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
            LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id

            UNION ALL

            SELECT
              e_tiket.id AS id_transaksi,
              'E-Tiket' AS sumber,
              'E-Tiket' AS jenis_transaksi,
              pemesanan_tiket.kode_booking,
              pembayaran.order_id,
              e_tiket.kode_qr,
              users.name AS nama_wisatawan,
              users.email,
              objek_wisata.nama_wisata,
              0 AS nominal,
              pemesanan_tiket.jumlah_tiket,
              e_tiket.berlaku_sampai AS tanggal,
              e_tiket.status_tiket AS status,
              'Penerbitan tiket elektronik' AS keterangan
            FROM e_tiket
            LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
            LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
            LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
            LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id

            UNION ALL

            SELECT
              data_kunjungan.id AS id_transaksi,
              'Kunjungan' AS sumber,
              'Scan Kunjungan' AS jenis_transaksi,
              pemesanan_tiket.kode_booking,
              pembayaran.order_id,
              e_tiket.kode_qr,
              users.name AS nama_wisatawan,
              users.email,
              objek_wisata.nama_wisata,
              0 AS nominal,
              pemesanan_tiket.jumlah_tiket,
              data_kunjungan.waktu_kunjungan AS tanggal,
              'Visited' AS status,
              data_kunjungan.keterangan
            FROM data_kunjungan
            LEFT JOIN e_tiket ON data_kunjungan.id_e_tiket=e_tiket.id
            LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
            LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
            LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
            LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
            ORDER BY tanggal DESC";
          $views_laporan_transaksi = mysqli_query($conn, $select_laporan_transaksi);

          $laporan_transaksi = [];
          $chart_bulan = [];
          $chart_pemesanan = [];
          $chart_pembayaran = [];
          $chart_kunjungan = [];

          for ($month = 1; $month <= 12; $month++) {
            $key = date('Y') . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $chart_bulan[$key] = date('M', mktime(0, 0, 0, $month, 1));
            $chart_pemesanan[$key] = 0;
            $chart_pembayaran[$key] = 0;
            $chart_kunjungan[$key] = 0;
          }

          if ($views_laporan_transaksi instanceof mysqli_result) {
            foreach ($views_laporan_transaksi as $transaksi) {
              $tanggal = $transaksi['tanggal'] ? date('Y-m-d H:i', strtotime($transaksi['tanggal'])) : '-';
              $laporan_transaksi[] = [
                'id_transaksi' => $transaksi['id_transaksi'],
                'sumber' => $transaksi['sumber'],
                'jenis_transaksi' => $transaksi['jenis_transaksi'],
                'kode_booking' => $transaksi['kode_booking'] ?: '-',
                'order_id' => $transaksi['order_id'] ?: '-',
                'kode_qr' => $transaksi['kode_qr'] ?: '-',
                'nama_wisatawan' => $transaksi['nama_wisatawan'] ?: '-',
                'email' => $transaksi['email'] ?: '-',
                'nama_wisata' => $transaksi['nama_wisata'] ?: '-',
                'nominal' => (float) $transaksi['nominal'],
                'jumlah_tiket' => (int) $transaksi['jumlah_tiket'],
                'tanggal' => $tanggal,
                'status' => $transaksi['status'] ?: '-',
                'keterangan' => $transaksi['keterangan'] ?: '-'
              ];

              if ($transaksi['tanggal']) {
                $month_key = date('Y-m', strtotime($transaksi['tanggal']));
                if (isset($chart_bulan[$month_key])) {
                  if ($transaksi['sumber'] == 'Pemesanan') {
                    $chart_pemesanan[$month_key]++;
                  } else if ($transaksi['sumber'] == 'Pembayaran') {
                    $chart_pembayaran[$month_key]++;
                  } else if ($transaksi['sumber'] == 'Kunjungan') {
                    $chart_kunjungan[$month_key]++;
                  }
                }
              }
            }
          }

          $kunjungan_total_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jumlah_data FROM data_kunjungan"));
          $kunjungan_hari_ini_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jumlah_data FROM data_kunjungan WHERE DATE(waktu_kunjungan)=CURDATE()"));
          $kunjungan_wisatawan_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT pemesanan_tiket.id_wisatawan) AS jumlah_data
            FROM data_kunjungan
            LEFT JOIN e_tiket ON data_kunjungan.id_e_tiket=e_tiket.id
            LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
            LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id"));
          $kunjungan_objek_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT pemesanan_tiket.id_objek_wisata) AS jumlah_data
            FROM data_kunjungan
            LEFT JOIN e_tiket ON data_kunjungan.id_e_tiket=e_tiket.id
            LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
            LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id"));

          $jumlah_laporan_kunjungan = (int) $kunjungan_total_data['jumlah_data'];
          $jumlah_kunjungan_hari_ini = (int) $kunjungan_hari_ini_data['jumlah_data'];
          $jumlah_wisatawan_kunjungan = (int) $kunjungan_wisatawan_data['jumlah_data'];
          $jumlah_objek_kunjungan = (int) $kunjungan_objek_data['jumlah_data'];

          $select_laporan_kunjungan = "SELECT data_kunjungan.id AS id_kunjungan, e_tiket.kode_qr, pemesanan_tiket.kode_booking, users.name AS nama_wisatawan, users.email, objek_wisata.nama_wisata, pemesanan_tiket.tgl_kunjungan, petugas.name AS nama_petugas, data_kunjungan.waktu_kunjungan, e_tiket.status_tiket, data_kunjungan.keterangan
            FROM data_kunjungan
            LEFT JOIN e_tiket ON data_kunjungan.id_e_tiket=e_tiket.id
            LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
            LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
            LEFT JOIN users ON pemesanan_tiket.id_wisatawan=users.id_user
            LEFT JOIN users AS petugas ON data_kunjungan.id_petugas=petugas.id_user
            LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
            ORDER BY data_kunjungan.waktu_kunjungan DESC";
          $views_laporan_kunjungan = mysqli_query($conn, $select_laporan_kunjungan);

          $laporan_kunjungan = [];
          $chart_bulan_kunjungan = [];
          $chart_total_kunjungan = [];

          for ($month = 1; $month <= 12; $month++) {
            $key = date('Y') . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $chart_bulan_kunjungan[$key] = date('M', mktime(0, 0, 0, $month, 1));
            $chart_total_kunjungan[$key] = 0;
          }

          if ($views_laporan_kunjungan instanceof mysqli_result) {
            foreach ($views_laporan_kunjungan as $kunjungan) {
              $waktu_kunjungan = $kunjungan['waktu_kunjungan'] ? date('Y-m-d H:i', strtotime($kunjungan['waktu_kunjungan'])) : '-';
              $laporan_kunjungan[] = [
                'id_kunjungan' => $kunjungan['id_kunjungan'],
                'kode_qr' => $kunjungan['kode_qr'] ?: '-',
                'kode_booking' => $kunjungan['kode_booking'] ?: '-',
                'nama_wisatawan' => $kunjungan['nama_wisatawan'] ?: '-',
                'email' => $kunjungan['email'] ?: '-',
                'nama_wisata' => $kunjungan['nama_wisata'] ?: '-',
                'tgl_kunjungan' => $kunjungan['tgl_kunjungan'] ? date('Y-m-d', strtotime($kunjungan['tgl_kunjungan'])) : '-',
                'nama_petugas' => $kunjungan['nama_petugas'] ?: '-',
                'waktu_kunjungan' => $waktu_kunjungan,
                'status_tiket' => $kunjungan['status_tiket'] ?: '-',
                'keterangan' => $kunjungan['keterangan'] ?: '-'
              ];

              if ($kunjungan['waktu_kunjungan']) {
                $month_key = date('Y-m', strtotime($kunjungan['waktu_kunjungan']));
                if (isset($chart_total_kunjungan[$month_key])) {
                  $chart_total_kunjungan[$month_key]++;
                }
              }
            }
          }

          $chart_objek_kunjungan_label = [];
          $chart_objek_kunjungan_data = [];
          $views_objek_kunjungan = mysqli_query($conn, "SELECT objek_wisata.nama_wisata, COUNT(data_kunjungan.id) AS jumlah_data
            FROM data_kunjungan
            LEFT JOIN e_tiket ON data_kunjungan.id_e_tiket=e_tiket.id
            LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
            LEFT JOIN pemesanan_tiket ON pembayaran.id_pemesanan=pemesanan_tiket.id
            LEFT JOIN objek_wisata ON pemesanan_tiket.id_objek_wisata=objek_wisata.id
            GROUP BY objek_wisata.id, objek_wisata.nama_wisata
            ORDER BY jumlah_data DESC
            LIMIT 6");

          if ($views_objek_kunjungan instanceof mysqli_result) {
            foreach ($views_objek_kunjungan as $objek_kunjungan) {
              $chart_objek_kunjungan_label[] = $objek_kunjungan['nama_wisata'] ?: 'Tidak diketahui';
              $chart_objek_kunjungan_data[] = (int) $objek_kunjungan['jumlah_data'];
            }
          }
          
