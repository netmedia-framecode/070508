<?php require_once("redirect.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>

  <?php require_once(__DIR__."/../sections/views_head.php") ?>

</head>

<body>
  <?php foreach ($messageTypes as $type) {
    if (isset($_SESSION["project_wisata_sumba_barat_daya"]["users"]["message_$type"])) {
      echo "<div class='message-$type' data-message-$type='{$_SESSION["project_wisata_sumba_barat_daya"]["users"]["message_$type"]}'></div>";
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

  <!-- Sidebar -->
  <?php require_once(__DIR__."/../sections/views_sidebar.php") ?>
  <!-- End of Sidebar -->

  <!-- Topbar -->
  <?php require_once(__DIR__."/../sections/views_topbar.php") ?>
  <!-- End of Topbar -->

  <main class="nxl-container">
