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

$id_role_permission = isset($_POST["role_permission"]) ? valid($conn, $_POST["role_permission"]) : 1;
$select_permission_matrix = "SELECT 
    user_menu.id_menu, 
    user_menu.menu, 
    user_menu.icon,
    user_sub_menu.id_sub_menu, 
    user_sub_menu.title,
    permissions.view, 
    permissions.create, 
    permissions.edit, 
    permissions.delete
  FROM user_menu
  LEFT JOIN user_sub_menu ON user_menu.id_menu = user_sub_menu.id_menu
  LEFT JOIN permissions ON user_sub_menu.id_sub_menu = permissions.id_sub_menu AND permissions.id_role = '$id_role_permission'
  ORDER BY user_menu.id_menu ASC, user_sub_menu.id_sub_menu ASC
";
$views_permissions = mysqli_query($conn, $select_permission_matrix);
if (isset($_POST["update_permission"])) {
  if (role_permission($conn, $_POST, $action = 'update') > 0) {
    $message = "Hak akses role berhasil diperbarui.";
    $message_type = "success";
    alert($message, $message_type);
    header("Location: permission");
    exit();
  }
}
