<?php
if (!isset($_SESSION["project_wisata_sumba_barat_daya"]["users"])) {
  header("Location: ../auth/");
  exit;
}

$role = strtolower($_SESSION["project_wisata_sumba_barat_daya"]["users"]["role"] ?? "");
if ($role == "wisatawan") {
  header("Location: ../index.php");
  exit;
}
