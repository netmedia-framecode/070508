<?php if (!isset($_SESSION)) {
  session_start();
}
require_once("../controller/auth.php");
if (isset($_SESSION["project_wisata_sumba_barat_daya"])) {
  unset($_SESSION["project_wisata_sumba_barat_daya"]);
  header("Location: ./");
  exit();
}
