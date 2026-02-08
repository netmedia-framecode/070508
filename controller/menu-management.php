<?php

require_once("../config/Base.php");
require_once("../config/Auth.php");
require_once("../config/Alert.php");

$select_user_role = "SELECT * FROM user_role";
$views_user_role = mysqli_query($conn, $select_user_role);
$select_menu = "SELECT * 
FROM user_menu 
ORDER BY menu ASC
";
$views_menu = mysqli_query($conn, $select_menu);
if (isset($_POST["add_menu"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (menu($conn, $validated_post, $action = 'insert') > 0) {
    $message = "Menu baru berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: menu");
    exit();
  }
}
if (isset($_POST["edit_menu"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (menu($conn, $validated_post, $action = 'update') > 0) {
    $message = "Menu " . $_POST['menuOld'] . " berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: menu");
    exit();
  }
}
if (isset($_POST["delete_menu"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (menu($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Menu " . $_POST['menu'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: menu");
    exit();
  }
}

$select_sub_menu = "SELECT user_sub_menu.*, user_menu.menu, user_status.status 
    FROM user_sub_menu 
    JOIN user_menu ON user_sub_menu.id_menu=user_menu.id_menu 
    JOIN user_status ON user_sub_menu.id_active=user_status.id_status 
    ORDER BY user_sub_menu.title ASC
  ";
$views_sub_menu = mysqli_query($conn, $select_sub_menu);
$select_user_status = "SELECT * FROM user_status";
$views_user_status = mysqli_query($conn, $select_user_status);
if (isset($_POST["add_sub_menu"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (sub_menu($conn, $validated_post, $action = 'insert', $baseURL) > 0) {
    $message = "Sub Menu baru berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: sub-menu");
    exit();
  }
}
if (isset($_POST["edit_sub_menu"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (sub_menu($conn, $validated_post, $action = 'update', $baseURL) > 0) {
    $message = "Sub Menu " . $_POST['titleOld'] . " berhasil diubah.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: sub-menu");
    exit();
  }
}
if (isset($_POST["delete_sub_menu"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (sub_menu($conn, $validated_post, $action = 'delete', $baseURL) > 0) {
    $message = "Sub Menu " . $_POST['title'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: sub-menu");
    exit();
  }
}

$select_user_access_menu = "SELECT user_access_menu.*, user_role.role, user_menu.menu
            FROM user_access_menu 
            JOIN user_role ON user_access_menu.id_role = user_role.id_role 
            JOIN user_menu ON user_access_menu.id_menu = user_menu.id_menu";
$views_user_access_menu = mysqli_query($conn, $select_user_access_menu);
$select_menu_check = "SELECT user_menu.* 
FROM user_menu 
ORDER BY user_menu.menu ASC
";
$views_menu_check = mysqli_query($conn, $select_menu_check);
if (isset($_POST["add_menu_access"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (menu_access($conn, $validated_post, $action = 'insert') > 0) {
    $message = "Akses ke menu berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: menu-access");
    exit();
  }
}
if (isset($_POST["delete_menu_access"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (menu_access($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Akses menu " . $_POST['menu'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: menu-access");
    exit();
  }
}

$select_user_access_sub_menu = "SELECT user_access_sub_menu.*, user_role.role, user_sub_menu.title, user_menu.menu
            FROM user_access_sub_menu 
            JOIN user_role ON user_access_sub_menu.id_role=user_role.id_role 
            JOIN user_sub_menu ON user_access_sub_menu.id_sub_menu=user_sub_menu.id_sub_menu
            JOIN user_menu ON user_sub_menu.id_menu=user_menu.id_menu";
$views_user_access_sub_menu = mysqli_query($conn, $select_user_access_sub_menu);
$select_sub_menu_check = "SELECT user_sub_menu.*, user_menu.menu
          FROM user_sub_menu 
          JOIN user_menu ON user_sub_menu.id_menu=user_menu.id_menu
          ORDER BY user_menu.menu ASC";
$views_sub_menu_check = mysqli_query($conn, $select_sub_menu_check);
if (isset($_POST["add_sub_menu_access"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (sub_menu_access($conn, $validated_post, $action = 'insert') > 0) {
    $message = "Akses ke sub menu berhasil ditambahkan.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: sub-menu-access");
    exit();
  }
}
if (isset($_POST["delete_sub_menu_access"])) {
  $validated_post = array_map(function ($value) use ($conn) {
    return valid($conn, $value);
  }, $_POST);
  if (sub_menu_access($conn, $validated_post, $action = 'delete') > 0) {
    $message = "Akses sub menu " . $_POST['title'] . " berhasil dihapus.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: sub-menu-access");
    exit();
  }
}
