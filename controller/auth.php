<?php

require_once("../config/Base.php");
require_once("../config/Alert.php");

if (isset($_POST["register"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (register($conn, $validated_post, $action = 'insert') > 0) {
    header("Location: verification?en=" . $_SESSION['data_auth']['en_user']);
    exit();
  }
}
if (isset($_POST["re_verifikasi"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (re_verifikasi($conn, $validated_post, $action = 'update') > 0) {
    $message = "Kode token yang baru telah dikirim ke email anda.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: verification?en=" . $_SESSION['data_auth']['en_user']);
    exit();
  }
}
if (isset($_POST["verifikasi"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (verifikasi($conn, $validated_post, $action = 'update') > 0) {
    $message = "Akun anda berhasil di verifikasi.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: ./");
    exit();
  }
}
if (isset($_POST["forgot_password"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (forgot_password($conn, $validated_post, $action = 'update', $baseURL) > 0) {
    $message = "Kami telah mengirim link ke email anda untuk melakukan reset kata sandi.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: ./");
    exit();
  }
}
if (isset($_POST["new_password"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (new_password($conn, $validated_post, $action = 'update') > 0) {
    $message = "Kata sandi anda telah berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: ./");
    exit();
  }
}
if (isset($_POST["login"])) {
  if (login($conn, $_POST) > 0) {
    header("Location: ../views/");
    exit();
  }
}
