-- Active: 1734576880718@@127.0.0.1@3306@wisata_sumba_barat_daya
-- -----------------------------------------------------------------------------
-- SECTION 1: INITIAL SCHEMA (UNALTERED EXCEPT ALLOWED FIELD ADDITIONS)
-- -----------------------------------------------------------------------------
CREATE TABLE
  utilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    logo VARCHAR(50),
    name_web VARCHAR(75),
    keyword TEXT,
    description TEXT,
    author VARCHAR(50)
  );

CREATE TABLE
  auth (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(50),
    bg VARCHAR(35),
    model INT DEFAULT 2
  );

CREATE TABLE
  user_role (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    role VARCHAR(35)
  );

INSERT INTO
  user_role (role)
VALUES
  ('Administrator'),
  ('Petugas'),
  ('Wisatawan');

CREATE TABLE
  user_status (
    id_status INT AUTO_INCREMENT PRIMARY KEY,
    status VARCHAR(35)
  );

INSERT INTO
  user_status (status)
VALUES
  ('Active'),
  ('No Active');

-- Modifikasi pada tabel users: Menambahkan no_hp dan asal_daerah sesuai ERD gambar
CREATE TABLE
  users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    id_role INT,
    id_active INT,
    en_user VARCHAR(75),
    token CHAR(6),
    name VARCHAR(100),
    image VARCHAR(100),
    email VARCHAR(75),
    password VARCHAR(100),
    no_hp VARCHAR(20) NULL, -- Tambahan dari ERD gambar
    asal_daerah VARCHAR(100) NULL, -- Tambahan dari ERD gambar
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_role) REFERENCES user_role (id_role) ON UPDATE NO ACTION ON DELETE NO ACTION,
    FOREIGN KEY (id_active) REFERENCES user_status (id_status) ON UPDATE NO ACTION ON DELETE NO ACTION
  );

CREATE TABLE
  user_menu (
    id_menu INT AUTO_INCREMENT PRIMARY KEY,
    icon VARCHAR(50),
    menu VARCHAR(50)
  );

CREATE TABLE
  user_sub_menu (
    id_sub_menu INT AUTO_INCREMENT PRIMARY KEY,
    id_menu INT,
    id_active INT,
    title VARCHAR(50),
    url VARCHAR(50),
    FOREIGN KEY (id_menu) REFERENCES user_menu (id_menu) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_active) REFERENCES user_status (id_status) ON UPDATE NO ACTION ON DELETE NO ACTION
  );

CREATE TABLE
  permissions (
    id_permission INT AUTO_INCREMENT PRIMARY KEY,
    id_role INT NOT NULL,
    id_menu INT NULL,
    id_sub_menu INT NULL,
    `view` TINYINT (1) DEFAULT 0,
    `create` TINYINT (1) DEFAULT 0,
    `edit` TINYINT (1) DEFAULT 0,
    `delete` TINYINT (1) DEFAULT 0,
    FOREIGN KEY (id_role) REFERENCES user_role (id_role) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_menu) REFERENCES user_menu (id_menu) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_sub_menu) REFERENCES user_sub_menu (id_sub_menu) ON UPDATE CASCADE ON DELETE CASCADE
  );

-- -----------------------------------------------------------------------------
-- SECTION 2: ADDITIONAL SCHEMA FROM ERD (NEW TABLES)
-- -----------------------------------------------------------------------------
-- 1. Tabel wilayah administratif
CREATE TABLE
  kabupaten_kota (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jenis ENUM('Kabupaten', 'Kota') NOT NULL,
    UNIQUE KEY uk_kabupaten_kota (nama, jenis)
  );

CREATE TABLE
  kecamatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kabupaten_kota_id INT NOT NULL,
    nama VARCHAR(100) NOT NULL,
    UNIQUE KEY uk_kecamatan (kabupaten_kota_id, nama),
    FOREIGN KEY (kabupaten_kota_id) REFERENCES kabupaten_kota (id) ON UPDATE CASCADE ON DELETE RESTRICT
  );

CREATE TABLE
  desa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kecamatan_id INT NOT NULL,
    nama VARCHAR(100) NOT NULL,
    UNIQUE KEY uk_desa (kecamatan_id, nama),
    FOREIGN KEY (kecamatan_id) REFERENCES kecamatan (id) ON UPDATE CASCADE ON DELETE RESTRICT
  );

CREATE TABLE
  kelurahan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kecamatan_id INT NOT NULL,
    nama VARCHAR(100) NOT NULL,
    UNIQUE KEY uk_kelurahan (kecamatan_id, nama),
    FOREIGN KEY (kecamatan_id) REFERENCES kecamatan (id) ON UPDATE CASCADE ON DELETE RESTRICT
  );

-- Data wilayah Kabupaten Sumba Barat Daya
-- Sumber: Kepmendagri No. 300.2.2-2430 Tahun 2025 (kode kabupaten 53.18)
INSERT INTO
  kabupaten_kota (id, nama, jenis)
VALUES
  (1, 'Sumba Barat Daya', 'Kabupaten');

INSERT INTO
  kecamatan (id, kabupaten_kota_id, nama)
VALUES
  (1, 1, 'Loura'),
  (2, 1, 'Wewewa Utara'),
  (3, 1, 'Wewewa Timur'),
  (4, 1, 'Wewewa Barat'),
  (5, 1, 'Wewewa Selatan'),
  (6, 1, 'Kodi Bangedo'),
  (7, 1, 'Kodi'),
  (8, 1, 'Kodi Utara'),
  (9, 1, 'Kota Tambolaka'),
  (10, 1, 'Wewewa Tengah'),
  (11, 1, 'Kodi Balaghar');

INSERT INTO
  desa (kecamatan_id, nama)
VALUES
  (1, 'Bondo Boghila'),
  (1, 'Lete Konda'),
  (1, 'Karuni'),
  (1, 'Totok'),
  (1, 'Rama Dana'),
  (1, 'Wee Mananda'),
  (1, 'Pogo Tena'),
  (1, 'Payola Umbu'),
  (1, 'Wee Kambala'),
  (1, 'Loko Kalada'),
  (1, 'Lete Konda Selatan'),
  (2, 'Mali Mada'),
  (2, 'Wano Talla'),
  (2, 'Wee Paboba'),
  (2, 'Mata Loko'),
  (2, 'Wee Namba'),
  (2, 'Puu Potto'),
  (2, 'Bodo Ponda'),
  (2, 'Reda Wano'),
  (2, 'Odi Paurata'),
  (2, 'Pandua Tana'),
  (2, 'Djela Manu'),
  (2, 'Mawo Maliti'),
  (3, 'Kalembu Ndara Mane'),
  (3, 'Tema Tana'),
  (3, 'Mareda Kalada'),
  (3, 'Pada Eweta'),
  (3, 'Wee Limbu'),
  (3, 'Lete Kamouna'),
  (3, 'Mata Pyawu'),
  (3, 'Wee Lima'),
  (3, 'Dikira'),
  (3, 'Dangga Mangu'),
  (3, 'Mainda Ole'),
  (3, 'Kadi Wano'),
  (3, 'Nyura Lele'),
  (3, 'Lele Maya'),
  (3, 'Maliti Dari'),
  (3, 'Mawo Dana'),
  (3, 'Dede Pada'),
  (3, 'Mata Wee Lima'),
  (3, 'Kadi Wone'),
  (4, 'Waimangura'),
  (4, 'Kalembu Weri'),
  (4, 'Wali Ate'),
  (4, 'Kabali Dana'),
  (4, 'Wee Kombaka'),
  (4, 'Marokota'),
  (4, 'Watu Labara'),
  (4, 'Kalimbu Tillu'),
  (4, 'Menne Ate'),
  (4, 'Reda Pada'),
  (4, 'Raba Ege'),
  (4, 'Kalaki Kambe'),
  (4, 'Kalembu Kanaika'),
  (4, 'Laga Lete'),
  (4, 'Wee Kura'),
  (4, 'Lua Koba'),
  (4, 'Tawo Rara'),
  (4, 'Sangu Ate'),
  (4, 'Lolo Ole'),
  (4, 'Pero'),
  (5, 'Buru Deilo'),
  (5, 'Weri Lolo'),
  (5, 'Buru Kaghu'),
  (5, 'Denduka'),
  (5, 'Bondo Bela'),
  (5, 'Delo'),
  (5, 'Tena Teke'),
  (5, 'Bondo Ukka'),
  (5, 'Umbu Wangu'),
  (5, 'Milla Ate'),
  (5, 'Rita Baru'),
  (5, 'Mandungo'),
  (5, 'Wee Wulla'),
  (5, 'Wee Baghe'),
  (6, 'Dinjo'),
  (6, 'Lete Loko'),
  (6, 'Walla Ndimu'),
  (6, 'Waikadada'),
  (6, 'Rada Loko'),
  (6, 'Mata Kapore'),
  (6, 'Umbu Ngedo'),
  (6, 'Waimakaha'),
  (6, 'Waimaringi'),
  (6, 'Tana Mete'),
  (6, 'Waipaddi'),
  (6, 'Manu Toghi'),
  (6, 'Karang Indah'),
  (6, 'Rada Malando'),
  (6, 'Waikaninyo'),
  (6, 'Ana Goka'),
  (6, 'Delu Depa'),
  (6, 'Mere Kehe'),
  (6, 'Bondo Balla'),
  (6, 'Maliti Bondo Ate'),
  (6, 'Ana Lewe'),
  (7, 'Bondo Kodi'),
  (7, 'Ate Dalo'),
  (7, 'Hamonggo Lele'),
  (7, 'Homba Rande'),
  (7, 'Pero Batang'),
  (7, 'Wura Homba'),
  (7, 'Koki'),
  (7, 'Kapaka Madeta'),
  (7, 'Kawango Hari'),
  (7, 'Onggol'),
  (7, 'Mali lha'),
  (7, 'Watu Wona'),
  (7, 'Tanjung Karoso'),
  (7, 'Pero Konda'),
  (7, 'Homba Rica'),
  (7, 'Ana Kaka'),
  (7, 'Ole Ate'),
  (7, 'Kadoki Horo'),
  (7, 'Ana Engge'),
  (8, 'Hoha Wungo'),
  (8, 'Homba Karipit'),
  (8, 'Wailabubur'),
  (8, 'Kori'),
  (8, 'Kalena Rongo'),
  (8, 'Waiholo'),
  (8, 'Noha'),
  (8, 'Mangganipi'),
  (8, 'Kendu Wela'),
  (8, 'Bila Cenge'),
  (8, 'Bukambero'),
  (8, 'Homba Pare'),
  (8, 'Magho Linyo'),
  (8, 'Wee Wella'),
  (8, 'Kadu Eta'),
  (8, 'Kadaghu Tana'),
  (8, 'Hameli Ate'),
  (8, 'Waitaru'),
  (8, 'Nangga Mutu'),
  (8, 'Limbu Kembe'),
  (8, 'Moro Manduyo'),
  (9, 'Rada Mata'),
  (9, 'Kalena Wano'),
  (9, 'Wee Londa'),
  (9, 'Wee Pangali'),
  (9, 'Kadi Pada'),
  (9, 'Watu Kawula'),
  (9, 'Wee Rena'),
  (9, 'Kalembu Kaha'),
  (10, 'Kanelu'),
  (10, 'Tanggaba'),
  (10, 'Wee Kokora'),
  (10, 'Wee Rame'),
  (10, 'Lombu'),
  (10, 'Eka Pata'),
  (10, 'Bolora'),
  (10, 'Omba Rade'),
  (10, 'Kalingara'),
  (10, 'Wee Patando'),
  (10, 'Mereda Wuni'),
  (10, 'Mata Wee Karoro'),
  (10, 'Gollu Sapi'),
  (10, 'Mata Lombu'),
  (10, 'Kadi Roma'),
  (10, 'Bondo Delo'),
  (10, 'Lete Wungana'),
  (10, 'Limbu Watu'),
  (10, 'Kiku Booko'),
  (10, 'Tarra Mata'),
  (11, 'Wailangira'),
  (11, 'Kahale'),
  (11, 'Waikarara'),
  (11, 'Panenggo Ede'),
  (11, 'Waiha'),
  (11, 'Wainyapu'),
  (11, 'Loko Tali'),
  (11, 'Waipakolo');

INSERT INTO
  kelurahan (kecamatan_id, nama)
VALUES
  (9, 'Langga Lero'),
  (9, 'Waitabula');

-- 2. Tabel objek_wisata
CREATE TABLE
  objek_wisata (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_wisata VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    desa_id INT NULL,
    kelurahan_id INT NULL,
    harga_tiket INT DEFAULT 0,
    jam_buka TIME,
    jam_tutup TIME,
    gambar VARCHAR(255),
    CONSTRAINT chk_lokasi_objek_wisata CHECK (
      (desa_id IS NOT NULL AND kelurahan_id IS NULL)
      OR (desa_id IS NULL AND kelurahan_id IS NOT NULL)
    ),
    FOREIGN KEY (desa_id) REFERENCES desa (id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (kelurahan_id) REFERENCES kelurahan (id) ON UPDATE CASCADE ON DELETE RESTRICT
  );

-- 3. Tabel galeri
CREATE TABLE
  galeri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    objek_wisata_id INT NOT NULL,
    judul VARCHAR(150),
    file_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (objek_wisata_id) REFERENCES objek_wisata (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

-- 4. Tabel informasi_wisata
CREATE TABLE
  informasi_wisata (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    judul VARCHAR(150) NOT NULL,
    konten TEXT,
    tgl_posting DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users (id_user) ON UPDATE CASCADE ON DELETE CASCADE
  );

-- 5. Tabel keranjang
CREATE TABLE
  keranjang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_wisatawan INT NOT NULL,
    id_objek_wisata INT NOT NULL,
    jumlah_tiket INT NOT NULL DEFAULT 1,
    total_harga_sementara INT NOT NULL,
    FOREIGN KEY (id_wisatawan) REFERENCES users (id_user) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_objek_wisata) REFERENCES objek_wisata (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

-- 6. Tabel pemesanan_tiket
CREATE TABLE
  pemesanan_tiket (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_booking VARCHAR(50) NOT NULL UNIQUE,
    id_wisatawan INT NOT NULL,
    id_objek_wisata INT NOT NULL,
    tgl_kunjungan DATE NOT NULL,
    jumlah_tiket INT NOT NULL,
    total_tagihan INT NOT NULL,
    waktu_pesan DATETIME DEFAULT CURRENT_TIMESTAMP,
    status_pemesanan VARCHAR(50) DEFAULT 'Pending',
    FOREIGN KEY (id_wisatawan) REFERENCES users (id_user) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_objek_wisata) REFERENCES objek_wisata (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

-- 7. Tabel pembayaran
CREATE TABLE
  pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pemesanan INT NOT NULL,
    order_id VARCHAR(100) NOT NULL UNIQUE,
    snap_token VARCHAR(255) NULL,
    metode_pembayaran VARCHAR(50) NULL,
    waktu_bayar DATETIME NULL,
    status_bayar VARCHAR(50) DEFAULT 'Unpaid',
    FOREIGN KEY (id_pemesanan) REFERENCES pemesanan_tiket (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

-- 8. Tabel e_tiket
CREATE TABLE
  e_tiket (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pembayaran INT NOT NULL,
    kode_qr VARCHAR(255) NOT NULL UNIQUE,
    status_tiket VARCHAR(50) DEFAULT 'Active',
    berlaku_sampai DATETIME NULL,
    FOREIGN KEY (id_pembayaran) REFERENCES pembayaran (id) ON UPDATE CASCADE ON DELETE CASCADE
  );

-- 9. Tabel data_kunjungan
CREATE TABLE
  data_kunjungan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_e_tiket INT NOT NULL,
    id_petugas INT NOT NULL,
    waktu_kunjungan DATETIME DEFAULT CURRENT_TIMESTAMP,
    keterangan TEXT NULL,
    FOREIGN KEY (id_e_tiket) REFERENCES e_tiket (id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_petugas) REFERENCES users (id_user) ON UPDATE CASCADE ON DELETE CASCADE
  );

-- 10. Tabel riwayat_transaksi
CREATE TABLE
  riwayat_transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_wisatawan INT NOT NULL,
    id_pemesanan INT NOT NULL,
    status_akhir VARCHAR(50) NOT NULL,
    tanggal_selesai DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_wisatawan) REFERENCES users (id_user) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_pemesanan) REFERENCES pemesanan_tiket (id) ON UPDATE CASCADE ON DELETE CASCADE
  );
