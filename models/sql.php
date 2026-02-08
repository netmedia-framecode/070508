<?php

if (!isset($_SESSION["project_wisata_sumba_barat_daya"]["users"])) {
  function alert($message, $message_type)
  {
    $_SESSION["project_wisata_sumba_barat_daya"] = [
      "message_$message_type" => $message,
      "time_message" => time()
    ];

    return true;
  }
}

if (isset($_SESSION["project_wisata_sumba_barat_daya"]["users"])) {
  function alert($message, $message_type)
  {
    global $conn;
    $id_user = valid($conn, $_SESSION["project_wisata_sumba_barat_daya"]["users"]["id"]);
    $id_role = valid($conn, $_SESSION["project_wisata_sumba_barat_daya"]["users"]["id_role"]);
    $role = valid($conn, $_SESSION["project_wisata_sumba_barat_daya"]["users"]["role"]);
    $email = valid($conn, $_SESSION["project_wisata_sumba_barat_daya"]["users"]["email"]);
    $name = valid($conn, $_SESSION["project_wisata_sumba_barat_daya"]["users"]["name"]);
    $image = valid($conn, $_SESSION["project_wisata_sumba_barat_daya"]["users"]["image"]);

    $_SESSION["project_wisata_sumba_barat_daya"]["users"] = [
      "id" => $id_user,
      "id_role" => $id_role,
      "role" => $role,
      "email" => $email,
      "name" => $name,
      "image" => $image,
      "message_$message_type" => $message,
      "time_message" => time()
    ];
  }
}
