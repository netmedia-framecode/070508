<?php

$messageTypes = ["success", "info", "warning", "danger", "dark"];

if (!isset($_SESSION["project_wisata_sumba_barat_daya"]["users"])) {
  if (isset($_SESSION["project_wisata_sumba_barat_daya"]["time_message"]) && (time() - $_SESSION["project_wisata_sumba_barat_daya"]["time_message"]) > 2) {
    foreach ($messageTypes as $type) {
      if (isset($_SESSION["project_wisata_sumba_barat_daya"]["message_$type"])) {
        unset($_SESSION["project_wisata_sumba_barat_daya"]["message_$type"]);
      }
    }
    unset($_SESSION["project_wisata_sumba_barat_daya"]["time_message"]);
  }
} else if (isset($_SESSION["project_wisata_sumba_barat_daya"]["users"])) {
  if (isset($_SESSION["project_wisata_sumba_barat_daya"]["users"]["time_message"]) && (time() - $_SESSION["project_wisata_sumba_barat_daya"]["users"]["time_message"]) > 2) {
    foreach ($messageTypes as $type) {
      if (isset($_SESSION["project_wisata_sumba_barat_daya"]["users"]["message_$type"])) {
        unset($_SESSION["project_wisata_sumba_barat_daya"]["users"]["message_$type"]);
      }
    }
    unset($_SESSION["project_wisata_sumba_barat_daya"]["users"]["time_message"]);
  }
}
