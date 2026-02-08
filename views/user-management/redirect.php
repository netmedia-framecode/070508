<?php
if (!isset($_SESSION["project_wisata_sumba_barat_daya"]["users"])) {
  header("Location: ../../auth/");
  exit;
}
