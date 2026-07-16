<?php
if (isset($_SESSION["project_wisata_sumba_barat_daya"]["users"])) {
  $role = strtolower($_SESSION["project_wisata_sumba_barat_daya"]["users"]["role"] ?? "");
  if ($role == "wisatawan") {
    header("Location: ../index.php");
  } else {
    header("Location: ../views/");
  }
  exit;
}
