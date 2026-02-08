<?php require_once("redirect.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>

  <?php require_once("../sections/auth_head.php") ?>

</head>

<body>
  <?php foreach ($messageTypes as $type) {
    if (isset($_SESSION["project_wisata_sumba_barat_daya"]["message_$type"])) {
      echo "<div class='message-$type' data-message-$type='{$_SESSION["project_wisata_sumba_barat_daya"]["message_$type"]}'></div>";
    }
  }

  // Set error handler to handle errors
  set_error_handler('handle_error');

  // Check if there is an error message in session and display it
  if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    // Clear error message from session
    unset($_SESSION['error_message']);
    // Display error message
    echo "<div class='alert alert-danger m-3'><span class='badge bg-danger text-white'>Peringatan!!</span> Web Builder - WASD Netmedia Framecode mendeteksi pesan kesalahan sebagai berikut:<br>$error_message</div>";
  } ?>
