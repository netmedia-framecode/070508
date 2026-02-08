<?php

require_once("../config/Base.php");
require_once("../config/Auth.php");
require_once("../config/Alert.php");
require_once("../views/redirect.php");

if (isset($_POST["utilities"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (utilities($conn, $validated_post, $action = 'update') > 0) {
    $message = "Utilitas pada website berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: utilities");
    exit();
  }
}