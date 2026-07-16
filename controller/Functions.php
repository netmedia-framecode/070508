<?php

function handle_error($errno, $errstr, $errfile, $errline)
{
  // Create error log file path based on the file where the error occurred
  $errorLog = dirname(__FILE__) . '/error_log.log'; // Error log file location within the project folder

  // Format error message with additional information
  $error_message = "[" . date("Y-m-d H:i:s") . "] Error [$errno]: $errstr in $errfile on line $errline" . PHP_EOL;

  // Attempt to open the error log file in append mode, creating it if it doesn't exist
  $file_handle = fopen($errorLog, 'a');
  if ($file_handle !== false) {
    // Write error message to the log file
    fwrite($file_handle, $error_message);
    // Close the file handle
    fclose($file_handle);
  }

  // Save error message in session
  $_SESSION['error_message'] = $error_message;

  // Redirect user back to the same page only if there is no error
  if (!isset($_SESSION['error_flag'])) {
    // Set error flag to prevent infinite redirection loop
    $_SESSION['error_flag'] = true;
    // Redirect user back to the same page
    header("Location: {$_SERVER['REQUEST_URI']}");
    exit(); // Stop further execution
  }
}

function valid($conn, $value)
{
  $valid = htmlspecialchars(addslashes(trim(mysqli_real_escape_string($conn, $value))));
  return $valid;
}

function separateAlphaNumeric($string)
{
  $alpha = "";
  $numeric = "";
  // Mengiterasi setiap karakter dalam string
  for ($i = 0; $i < strlen($string); $i++) {
    // Memeriksa apakah karakter adalah huruf
    if (ctype_alpha($string[$i])) {
      $alpha .= $string[$i];
    }
    // Memeriksa apakah karakter adalah angka
    if (ctype_digit($string[$i])) {
      $numeric .= $string[$i];
    }
  }
  // Mengembalikan array yang berisi huruf dan angka terpisah
  return array(
    "alpha" => $alpha,
    "numeric" => $numeric
  );
}

function generateToken()
{
  // Generate a random 6-digit number
  $token = mt_rand(100000, 999999);
  return $token;
}

function compressImage($source, $destination, $quality)
{
  // mendapatkan info image
  $imgInfo = getimagesize($source);
  $mime = $imgInfo['mime'];
  // membuat image baru
  switch ($mime) {
    // proses kode memilih tipe tipe image 
    case 'image/jpeg':
      $image = imagecreatefromjpeg($source);
      break;
    case 'image/png':
      $image = imagecreatefrompng($source);
      break;
    default:
      $image = imagecreatefromjpeg($source);
  }

  // Menyimpan image dengan ukuran yang baru
  imagejpeg($image, $destination, $quality);

  // Return image
  return $destination;
}

function hapusFolderRecursively($folderPath)
{
  if (is_dir($folderPath)) {
    $files = glob($folderPath . '/*');
    foreach ($files as $file) {
      is_dir($file) ? hapusFolderRecursively($file) : unlink($file);
    }
    rmdir($folderPath);
  }
}

function currentPermissionUrl()
{
  $requestUri = strtok($_SERVER['REQUEST_URI'], '?');
  $requestUri = trim($requestUri, '/');
  $viewsPosition = strpos($requestUri, 'views/');

  if ($viewsPosition !== false) {
    $requestUri = substr($requestUri, $viewsPosition + strlen('views/'));
  }

  $requestUri = preg_replace('/\.php$/', '', $requestUri);
  return trim($requestUri, '/');
}

function canAction($action, $url = null)
{
  global $conn;

  $allowedActions = ['view', 'create', 'edit', 'delete'];
  if (!in_array($action, $allowedActions)) {
    return false;
  }

  if (!isset($_SESSION["project_wisata_sumba_barat_daya"]["users"])) {
    return false;
  }

  $id_role = valid($conn, $_SESSION["project_wisata_sumba_barat_daya"]["users"]["id_role"]);
  $role = strtolower($_SESSION["project_wisata_sumba_barat_daya"]["users"]["role"]);

  if ($id_role == 1 || $role == 'administrator') {
    return true;
  }

  $url = $url ? trim($url, '/') : currentPermissionUrl();
  $url = preg_replace('/\.php$/', '', $url);

  $urlParts = explode('/', $url);
  $page = end($urlParts);
  $folder = count($urlParts) > 1 ? $urlParts[0] : '';
  $normalizedPage = preg_replace('/^(add|edit)-/', '', $page);
  $normalizedUrl = $folder ? $folder . '/' . $normalizedPage : $normalizedPage;

  $url = valid($conn, $url);
  $normalizedUrl = valid($conn, $normalizedUrl);

  $query = "SELECT p.`$action` AS allowed
    FROM permissions p
    JOIN user_sub_menu usm ON p.id_sub_menu=usm.id_sub_menu
    WHERE p.id_role='$id_role'
      AND (usm.url='$url' OR usm.url='$normalizedUrl')
    LIMIT 1";
  $result = mysqli_query($conn, $query);

  if (!$result || mysqli_num_rows($result) == 0) {
    return false;
  }

  $permission = mysqli_fetch_assoc($result);
  return $permission['allowed'] == 1;
}

if (!isset($_SESSION["project_wisata_sumba_barat_daya"]["users"])) {
  function register($conn, $data, $action)
  {
    if ($action == "insert") {
      $checkEmail = "SELECT * FROM users WHERE email='$data[email]'";
      $checkEmail = mysqli_query($conn, $checkEmail);
      if (mysqli_num_rows($checkEmail) > 0) {
        $message = "Maaf, email yang anda masukan sudah terdaftar.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      } else {
        if ($data['password'] !== $data['re_password']) {
          $message = "Maaf, konfirmasi password yang anda masukan belum sama.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        } else {
          $password = password_hash($data['password'], PASSWORD_DEFAULT);
          $token = generateToken();
          $en_user = password_hash($token, PASSWORD_DEFAULT);
          $en_user = str_replace("$", "", $en_user);
          $en_user = str_replace("/", "", $en_user);
          $en_user = str_replace(".", "", $en_user);
          $to       = $data['email'];
          $subject  = "Account Verification - Wisata Sumba Barat Daya";
          $message  = "<!doctype html>
          <html>
            <head>
                <meta name='viewport' content='width=device-width'>
                <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
                <title>Account Verification</title>
                <style>
                    @media only screen and (max-width: 620px) {
                        table[class='body'] h1 {
                            font-size: 28px !important;
                            margin-bottom: 10px !important;}
                        table[class='body'] p,
                        table[class='body'] ul,
                        table[class='body'] ol,
                        table[class='body'] td,
                        table[class='body'] span,
                        table[class='body'] a {
                            font-size: 16px !important;}
                        table[class='body'] .wrapper,
                        table[class='body'] .article {
                            padding: 10px !important;}
                        table[class='body'] .content {
                            padding: 0 !important;}
                        table[class='body'] .container {
                            padding: 0 !important;
                            width: 100% !important;}
                        table[class='body'] .main {
                            border-left-width: 0 !important;
                            border-radius: 0 !important;
                            border-right-width: 0 !important;}
                        table[class='body'] .btn table {
                            width: 100% !important;}
                        table[class='body'] .btn a {
                            width: 100% !important;}
                        table[class='body'] .img-responsive {
                            height: auto !important;
                            max-width: 100% !important;
                            width: auto !important;}}
                    @media all {
                        .ExternalClass {
                            width: 100%;}
                        .ExternalClass,
                        .ExternalClass p,
                        .ExternalClass span,
                        .ExternalClass font,
                        .ExternalClass td,
                        .ExternalClass div {
                            line-height: 100%;}
                        .apple-link a {
                            color: inherit !important;
                            font-family: inherit !important;
                            font-size: inherit !important;
                            font-weight: inherit !important;
                            line-height: inherit !important;
                            text-decoration: none !important;
                        .btn-primary table td:hover {
                            background-color: #d5075d !important;}
                        .btn-primary a:hover {
                            background-color: #000 !important;
                            border-color: #000 !important;
                            color: #fff !important;}}
                </style>
            </head>
            <body class style='background-color: #e1e3e5; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;'>
                <table role='presentation' border='0' cellpadding='0' cellspacing='0' class='body' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; background-color: #e1e3e5; width: 100%;' width='100%' bgcolor='#e1e3e5'>
                <tr>
                    <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;' valign='top'>&nbsp;</td>
                    <td class='container' style='font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto;' width='580' valign='top'>
                    <div class='content' style='box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;'>
            
                        <!-- START CENTERED WHITE CONTAINER -->
                        <table role='presentation' class='main' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; background: #ffffff; border-radius: 3px; width: 100%;' width='100%'>
            
                        <!-- START MAIN CONTENT AREA -->
                        <tr>
                            <td class='wrapper' style='font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;' valign='top'>
                            <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; width: 100%;' width='100%'>
                                <tr>
                                <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;' valign='top'>
                                    <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;'>Hi " . $data['name'] . ",</p>
                                    <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;'>Selamat akun kamu sudah terdaftar, tinggal satu langkah lagi kamu sudah bisa menggunakan akun. Silakan salin kode token dibawah ini untuk memverifikasi akun kamu.</p>
                                    <table role='presentation' border='0' cellpadding='0' cellspacing='0' class='btn btn-primary' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; box-sizing: border-box; min-width: 100%; width: 100%;' width='100%'>
                                    <tbody>
                                        <tr>
                                        <td align='left' style='font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;' valign='top'>
                                            <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: auto; width: auto;'>
                                            <tbody>
                                                <tr>
                                                <td style='font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #ffffff; border-radius: 5px; text-align: center; font-weight: bold;' valign='top' bgcolor='#ffffff' align='center'>" . $token . "</td>
                                                </tr>
                                            </tbody>
                                            </table>
                                        </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                    <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;'>Terima kasih telah mendaftar di Wisata Sumba Barat Daya.</p>
                                    <small>Peringatan! Ini adalah pesan otomatis sehingga Anda tidak dapat membalas pesan ini.</small>
                                </td>
                                </tr>
                            </table>
                            </td>
                        </tr>
            
                        <!-- END MAIN CONTENT AREA -->
                        </table>
                        
                        <!-- START FOOTER -->
                        <div class='footer' style='clear: both; margin-top: 10px; text-align: center; width: 100%;'>
                        <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; width: 100%;' width='100%'>
                            <tr>
                            <td class='content-block' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #9a9ea6; font-size: 12px; text-align: center;' valign='top' align='center'>
                                <span class='apple-link' style='color: #9a9ea6; font-size: 12px; text-align: center;'>Workarea Jln. S. K. Lerik, Kota Baru, Kupang, NTT, Indonesia. (0380) 8438423</span>
                            </td>
                            </tr>
                            <tr>
                            <td class='content-block powered-by' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #9a9ea6; font-size: 12px; text-align: center;' valign='top' align='center'>
                                Powered by <a href='https://www.netmedia-framecode.com' style='color: #9a9ea6; font-size: 12px; text-align: center; text-decoration: none;'>Netmedia Framecode</a>.
                            </td>
                            </tr>
                        </table>
                        </div>
                        <!-- END FOOTER -->
            
                    <!-- END CENTERED WHITE CONTAINER -->
                    </div>
                    </td>
                    <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;' valign='top'>&nbsp;</td>
                </tr>
                </table>
            </body>
          </html>";
          smtp_mail($to, $subject, $message, "", "", 0, 0, true);
          $_SESSION['data_auth'] = ['en_user' => $en_user];
          $sql = "INSERT INTO users(en_user,token,name,email,password) VALUES('$en_user','$token','$data[name]','$data[email]','$password')";
        }
      }
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function re_verifikasi($conn, $data, $action)
  {
    if ($action == "update") {
      $checkEN = "SELECT * FROM users WHERE en_user='$data[en_user]'";
      $checkEN = mysqli_query($conn, $checkEN);
      if (mysqli_num_rows($checkEN) == 0) {
        $message = "Maaf, sepertinya ada kesalahan saat mendaftar.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      } else if (mysqli_num_rows($checkEN) > 0) {
        $row = mysqli_fetch_assoc($checkEN);
        $name = $row['name'];
        $email = $row['email'];
        $token = generateToken();
        $reen_user = password_hash($token, PASSWORD_DEFAULT);
        $reen_user = str_replace("$", "", $reen_user);
        $reen_user = str_replace("/", "", $reen_user);
        $reen_user = str_replace(".", "", $reen_user);
        $to       = $email;
        $subject  = "Account Verification - Wisata Sumba Barat Daya";
        $message  = "<!doctype html>
        <html>
          <head>
              <meta name='viewport' content='width=device-width'>
              <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
              <title>Account Verification</title>
              <style>
                  @media only screen and (max-width: 620px) {
                      table[class='body'] h1 {
                          font-size: 28px !important;
                          margin-bottom: 10px !important;}
                      table[class='body'] p,
                      table[class='body'] ul,
                      table[class='body'] ol,
                      table[class='body'] td,
                      table[class='body'] span,
                      table[class='body'] a {
                          font-size: 16px !important;}
                      table[class='body'] .wrapper,
                      table[class='body'] .article {
                          padding: 10px !important;}
                      table[class='body'] .content {
                          padding: 0 !important;}
                      table[class='body'] .container {
                          padding: 0 !important;
                          width: 100% !important;}
                      table[class='body'] .main {
                          border-left-width: 0 !important;
                          border-radius: 0 !important;
                          border-right-width: 0 !important;}
                      table[class='body'] .btn table {
                          width: 100% !important;}
                      table[class='body'] .btn a {
                          width: 100% !important;}
                      table[class='body'] .img-responsive {
                          height: auto !important;
                          max-width: 100% !important;
                          width: auto !important;}}
                  @media all {
                      .ExternalClass {
                          width: 100%;}
                      .ExternalClass,
                      .ExternalClass p,
                      .ExternalClass span,
                      .ExternalClass font,
                      .ExternalClass td,
                      .ExternalClass div {
                          line-height: 100%;}
                      .apple-link a {
                          color: inherit !important;
                          font-family: inherit !important;
                          font-size: inherit !important;
                          font-weight: inherit !important;
                          line-height: inherit !important;
                          text-decoration: none !important;
                      .btn-primary table td:hover {
                          background-color: #d5075d !important;}
                      .btn-primary a:hover {
                          background-color: #000 !important;
                          border-color: #000 !important;
                          color: #fff !important;}}
              </style>
          </head>
          <body class style='background-color: #e1e3e5; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;'>
              <table role='presentation' border='0' cellpadding='0' cellspacing='0' class='body' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; background-color: #e1e3e5; width: 100%;' width='100%' bgcolor='#e1e3e5'>
              <tr>
                  <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;' valign='top'>&nbsp;</td>
                  <td class='container' style='font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto;' width='580' valign='top'>
                  <div class='content' style='box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;'>
          
                      <!-- START CENTERED WHITE CONTAINER -->
                      <table role='presentation' class='main' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; background: #ffffff; border-radius: 3px; width: 100%;' width='100%'>
          
                      <!-- START MAIN CONTENT AREA -->
                      <tr>
                          <td class='wrapper' style='font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;' valign='top'>
                          <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; width: 100%;' width='100%'>
                              <tr>
                              <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;' valign='top'>
                                  <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;'>Hi " . $name . ",</p>
                                  <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;'>Selamat akun kamu sudah terdaftar, tinggal satu langkah lagi kamu sudah bisa menggunakan akun. Silakan salin kode token dibawah ini untuk memverifikasi akun kamu.</p>
                                  <table role='presentation' border='0' cellpadding='0' cellspacing='0' class='btn btn-primary' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; box-sizing: border-box; min-width: 100%; width: 100%;' width='100%'>
                                  <tbody>
                                      <tr>
                                      <td align='left' style='font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;' valign='top'>
                                          <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: auto; width: auto;'>
                                          <tbody>
                                              <tr>
                                              <td style='font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #ffffff; border-radius: 5px; text-align: center; font-weight: bold;' valign='top' bgcolor='#ffffff' align='center'>" . $token . "</td>
                                              </tr>
                                          </tbody>
                                          </table>
                                      </td>
                                      </tr>
                                  </tbody>
                                  </table>
                                  <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;'>Terima kasih telah mendaftar di Wisata Sumba Barat Daya.</p>
                                  <small>Peringatan! Ini adalah pesan otomatis sehingga Anda tidak dapat membalas pesan ini.</small>
                              </td>
                              </tr>
                          </table>
                          </td>
                      </tr>
          
                      <!-- END MAIN CONTENT AREA -->
                      </table>
                      
                      <!-- START FOOTER -->
                      <div class='footer' style='clear: both; margin-top: 10px; text-align: center; width: 100%;'>
                      <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; width: 100%;' width='100%'>
                          <tr>
                          <td class='content-block' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #9a9ea6; font-size: 12px; text-align: center;' valign='top' align='center'>
                              <span class='apple-link' style='color: #9a9ea6; font-size: 12px; text-align: center;'>Workarea Jln. S. K. Lerik, Kota Baru, Kupang, NTT, Indonesia. (0380) 8438423</span>
                          </td>
                          </tr>
                          <tr>
                          <td class='content-block powered-by' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #9a9ea6; font-size: 12px; text-align: center;' valign='top' align='center'>
                              Powered by <a href='https://www.netmedia-framecode.com' style='color: #9a9ea6; font-size: 12px; text-align: center; text-decoration: none;'>Netmedia Framecode</a>.
                          </td>
                          </tr>
                      </table>
                      </div>
                      <!-- END FOOTER -->
          
                  <!-- END CENTERED WHITE CONTAINER -->
                  </div>
                  </td>
                  <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;' valign='top'>&nbsp;</td>
              </tr>
              </table>
          </body>
        </html>";
        smtp_mail($to, $subject, $message, "", "", 0, 0, true);
        $_SESSION['data_auth'] = ['en_user' => $reen_user];
        $sql = "UPDATE users SET en_user='$reen_user', token='$token', updated_at=current_timestamp WHERE en_user='$data[en_user]'";
      }
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function verifikasi($conn, $data, $action)
  {
    if ($action == "update") {
      $checkEN = "SELECT * FROM users WHERE en_user='$data[en_user]'";
      $checkEN = mysqli_query($conn, $checkEN);
      if (mysqli_num_rows($checkEN) == 0) {
        $message = "Maaf, sepertinya ada kesalahan saat mendaftar.";
        $message_type = "warning";
        alert($message, $message_type);
        return false;
      } else if (mysqli_num_rows($checkEN) > 0) {
        $row = mysqli_fetch_assoc($checkEN);
        $token_primary = $row['token'];
        $updated_at = strtotime($row['updated_at']);
        $current_time = time();
        if (($current_time - $updated_at) > (5 * 60)) {
          $message = "Maaf, waktu untuk verifikasi telah habis.";
          $message_type = "warning";
          alert($message, $message_type);
          $_SESSION["project_wisata_sumba_barat_daya"] = [
            "message-warning" => "Maaf, waktu untuk verifikasi telah habis.",
            "time-message" => time()
          ];
          return false;
        }
        if ($data['token'] !== $token_primary) {
          $message = "Maaf, kode token yang anda masukan masih salah.";
          $message_type = "warning";
          alert($message, $message_type);
          return false;
        }
        $sql = "UPDATE users SET id_active='1', updated_at=current_timestamp WHERE en_user='$data[en_user]'";
      }
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function forgot_password($conn, $data, $action, $baseURL)
  {
    if ($action == "update") {
      $checkEmail = "SELECT * FROM users WHERE email='$data[email]'";
      $checkEmail = mysqli_query($conn, $checkEmail);
      if (mysqli_num_rows($checkEmail) === 0) {
        $message = "Maaf, email yang anda masukan belum terdaftar.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      } else {
        $row = mysqli_fetch_assoc($checkEmail);
        $name = valid($conn, $row['name']);
        $token = generateToken();
        $en_user = password_hash($token, PASSWORD_DEFAULT);
        $en_user = str_replace("$", "", $en_user);
        $en_user = str_replace("/", "", $en_user);
        $en_user = str_replace(".", "", $en_user);
        $to       = $data['email'];
        $subject  = "Reset password - Wisata Sumba Barat Daya";
        $message  = "<!doctype html>
        <html>
          <head>
              <meta name='viewport' content='width=device-width'>
              <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
              <title>Reset password</title>
              <style>
                  @media only screen and (max-width: 620px) {
                      table[class='body'] h1 {
                          font-size: 28px !important;
                          margin-bottom: 10px !important;}
                      table[class='body'] p,
                      table[class='body'] ul,
                      table[class='body'] ol,
                      table[class='body'] td,
                      table[class='body'] span,
                      table[class='body'] a {
                          font-size: 16px !important;}
                      table[class='body'] .wrapper,
                      table[class='body'] .article {
                          padding: 10px !important;}
                      table[class='body'] .content {
                          padding: 0 !important;}
                      table[class='body'] .container {
                          padding: 0 !important;
                          width: 100% !important;}
                      table[class='body'] .main {
                          border-left-width: 0 !important;
                          border-radius: 0 !important;
                          border-right-width: 0 !important;}
                      table[class='body'] .btn table {
                          width: 100% !important;}
                      table[class='body'] .btn a {
                          width: 100% !important;}
                      table[class='body'] .img-responsive {
                          height: auto !important;
                          max-width: 100% !important;
                          width: auto !important;}}
                  @media all {
                      .ExternalClass {
                          width: 100%;}
                      .ExternalClass,
                      .ExternalClass p,
                      .ExternalClass span,
                      .ExternalClass font,
                      .ExternalClass td,
                      .ExternalClass div {
                          line-height: 100%;}
                      .apple-link a {
                          color: inherit !important;
                          font-family: inherit !important;
                          font-size: inherit !important;
                          font-weight: inherit !important;
                          line-height: inherit !important;
                          text-decoration: none !important;
                      .btn-primary table td:hover {
                          background-color: #d5075d !important;}
                      .btn-primary a:hover {
                          background-color: #000 !important;
                          border-color: #000 !important;
                          color: #fff !important;}}
              </style>
          </head>
          <body class style='background-color: #e1e3e5; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;'>
              <table role='presentation' border='0' cellpadding='0' cellspacing='0' class='body' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; background-color: #e1e3e5; width: 100%;' width='100%' bgcolor='#e1e3e5'>
              <tr>
                  <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;' valign='top'>&nbsp;</td>
                  <td class='container' style='font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto;' width='580' valign='top'>
                  <div class='content' style='box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;'>
          
                      <!-- START CENTERED WHITE CONTAINER -->
                      <table role='presentation' class='main' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; background: #ffffff; border-radius: 3px; width: 100%;' width='100%'>
          
                      <!-- START MAIN CONTENT AREA -->
                      <tr>
                          <td class='wrapper' style='font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;' valign='top'>
                          <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; width: 100%;' width='100%'>
                              <tr>
                              <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;' valign='top'>
                                  <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;'>Hi " . $name . ",</p>
                                  <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;'>Pesan ini secara otomatis dikirimkan kepada anda karena anda meminta untuk mereset kata sandi. Jika anda tidak sama sekali ingin mereset atau bukan anda yang ingin mereset abaikan saja. Klik tombol reset berikut untuk melanjutkan:</p>
                                  <table role='presentation' border='0' cellpadding='0' cellspacing='0' class='btn btn-primary' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; box-sizing: border-box; min-width: 100%; width: 100%;' width='100%'>
                                  <tbody>
                                      <tr>
                                      <td align='left' style='font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;' valign='top'>
                                          <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: auto; width: auto;'>
                                          <tbody>
                                              <tr>
                                                <td style='font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #ffffff; border-radius: 5px; text-align: center;' valign='top' bgcolor='#ffffff' align='center'>
                                                  <a href='" . $baseURL . "auth/new-password?en=" . $en_user . "' target='_blank' style='background-color: #ffffff; border: solid 1px #000; border-radius: 5px; box-sizing: border-box; cursor: pointer; display: inline-block; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-decoration: none; text-transform: capitalize; border-color: #000; color: #000;'>Atur Ulang Kata Sandi</a> 
                                                </td>
                                              </tr>
                                          </tbody>
                                          </table>
                                      </td>
                                      </tr>
                                  </tbody>
                                  </table>
                                  <small>Peringatan! Ini adalah pesan otomatis sehingga Anda tidak dapat membalas pesan ini.</small>
                              </td>
                              </tr>
                          </table>
                          </td>
                      </tr>
          
                      <!-- END MAIN CONTENT AREA -->
                      </table>
                      
                      <!-- START FOOTER -->
                      <div class='footer' style='clear: both; margin-top: 10px; text-align: center; width: 100%;'>
                      <table role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; width: 100%;' width='100%'>
                          <tr>
                          <td class='content-block' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #9a9ea6; font-size: 12px; text-align: center;' valign='top' align='center'>
                              <span class='apple-link' style='color: #9a9ea6; font-size: 12px; text-align: center;'>Workarea Jln. S. K. Lerik, Kota Baru, Kupang, NTT, Indonesia. (0380) 8438423</span>
                          </td>
                          </tr>
                          <tr>
                          <td class='content-block powered-by' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; color: #9a9ea6; font-size: 12px; text-align: center;' valign='top' align='center'>
                              Powered by <a href='https://www.netmedia-framecode.com' style='color: #9a9ea6; font-size: 12px; text-align: center; text-decoration: none;'>Netmedia Framecode</a>.
                          </td>
                          </tr>
                      </table>
                      </div>
                      <!-- END FOOTER -->
          
                  <!-- END CENTERED WHITE CONTAINER -->
                  </div>
                  </td>
                  <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;' valign='top'>&nbsp;</td>
              </tr>
              </table>
          </body>
        </html>";
        smtp_mail($to, $subject, $message, "", "", 0, 0, true);
        $sql = "UPDATE users SET en_user='$en_user', token='$token', updated_at=current_timestamp WHERE email='$data[email]'";
      }
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function new_password($conn, $data, $action)
  {
    if ($action == "update") {
      $lenght = strlen($data['password']);
      if ($lenght < 8) {
        $message = "Maaf, password yang anda masukan harus 8 digit atau lebih.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      } else if ($data['password'] !== $data['re_password']) {
        $message = "Maaf, konfirmasi password yang anda masukan belum sama.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      } else {
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password='$password' WHERE email='$data[email]'";
      }
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function login($conn, $data)
  {
    // check account
    $checkAccount = mysqli_query($conn, "SELECT * FROM users JOIN user_role ON users.id_role=user_role.id_role WHERE users.email='$data[email]'");
    if (mysqli_num_rows($checkAccount) == 0) {
      $message = "Maaf, akun yang anda masukan belum terdaftar.";
      $message_type = "danger";
      alert($message, $message_type);
      return false;
    } else if (mysqli_num_rows($checkAccount) > 0) {
      $row = mysqli_fetch_assoc($checkAccount);
      if (password_verify($data['password'], $row["password"])) {
        $_SESSION["project_wisata_sumba_barat_daya"]["users"] = [
          "id" => $row["id_user"],
          "id_role" => $row["id_role"],
          "role" => $row["role"],
          "email" => $row["email"],
          "name" => $row["name"],
          "image" => $row["image"]
        ];
        return mysqli_affected_rows($conn);
      } else {
        $message = "Maaf, kata sandi yang anda masukan salah.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }
    }
  }
}

if (isset($_SESSION["project_wisata_sumba_barat_daya"]["users"])) {

  function profil($conn, $data, $action, $id_user)
  {
    if ($action == "update") {
      $path = "../assets/img/profil/";
      if (!empty($_FILES['image']["name"])) {
        $fileName = basename($_FILES["image"]["name"]);
        $fileName = str_replace(" ", "-", $fileName);
        $fileName_encrypt = crc32($fileName);
        $ekstensiGambar = explode('.', $fileName);
        $ekstensiGambar = strtolower(end($ekstensiGambar));
        $imageUploadPath = $path . $fileName_encrypt . "." . $ekstensiGambar;
        $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg', 'png', 'jpeg');
        if (in_array($fileType, $allowTypes)) {
          $imageTemp = $_FILES["image"]["tmp_name"];
          compressImage($imageTemp, $imageUploadPath, 75);
          $image = $fileName_encrypt . "." . $ekstensiGambar;
        } else {
          $message = "Maaf, hanya file gambar JPG, JPEG, dan PNG yang diizinkan.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
      }
      if (!empty($_FILES['image']["name"])) {
        $unwanted_characters = "../assets/img/profil/";
        $remove_image = str_replace($unwanted_characters, "", $data['imageOld']);
        if ($remove_image != "default.svg") {
          unlink($path . $remove_image);
        }
      } else if (empty($_FILE['image']["name"])) {
        $image = $data['imageOld'];
      }
      if (!empty($data['password'])) {
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name='$data[name]', image='$image', password='$password' WHERE id_user='$id_user'";
      } else {
        $sql = "UPDATE users SET name='$data[name]', image='$image' WHERE id_user='$id_user'";
      }
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function setting($conn, $data, $action)
  {

    if ($action == "update") {
      $path = "../assets/img/auth/";
      if (!empty($_FILES['image']["name"])) {
        $fileName = basename($_FILES["image"]["name"]);
        $fileName = str_replace(" ", "-", $fileName);
        $fileName_encrypt = crc32($fileName);
        $ekstensiGambar = explode('.', $fileName);
        $ekstensiGambar = strtolower(end($ekstensiGambar));
        $imageUploadPath = $path . $fileName_encrypt . "." . $ekstensiGambar;
        $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg', 'png', 'jpeg');
        if (in_array($fileType, $allowTypes)) {
          $imageTemp = $_FILES["image"]["tmp_name"];
          move_uploaded_file($imageTemp, $imageUploadPath);
          $image = $fileName_encrypt . "." . $ekstensiGambar;
        } else {
          $message = "Maaf, hanya file gambar JPG, JPEG, dan PNG yang diizinkan.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
      }
      if (!empty($_FILES['image']["name"])) {
        $unwanted_characters = "../assets/img/auth/";
        $remove_image = str_replace($unwanted_characters, "", $data['imageOld']);
        unlink($path . $remove_image);
      } else if (empty($_FILE['image']["name"])) {
        $image = $data['imageOld'];
      }
      $sql = "UPDATE auth SET image='$image', bg='$data[bg]', model='$data[model]'";
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function utilities($conn, $data, $action)
  {

    if ($action == "update") {
      $path = "../assets/img/";
      if (!empty($_FILES['logo']["name"])) {
        $fileName = basename($_FILES["logo"]["name"]);
        $fileName = str_replace(" ", "-", $fileName);
        $fileName_encrypt = crc32($fileName);
        $ekstensiGambar = explode('.', $fileName);
        $ekstensiGambar = strtolower(end($ekstensiGambar));
        $imageUploadPath = $path . $fileName_encrypt . "." . $ekstensiGambar;
        $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg', 'png', 'jpeg');
        if (in_array($fileType, $allowTypes)) {
          $imageTemp = $_FILES["logo"]["tmp_name"];
          move_uploaded_file($imageTemp, $imageUploadPath);
          $logo = $fileName_encrypt . "." . $ekstensiGambar;
        } else {
          $message = "Maaf, hanya file gambar JPG, JPEG, dan PNG yang diizinkan.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
      }
      if (!empty($_FILES['logo']["name"])) {
        $unwanted_characters = "../assets/img/";
        $remove_image = str_replace($unwanted_characters, "", $data['logoOld']);
        unlink($path . $remove_image);
      } else if (empty($_FILE['logo']["name"])) {
        $logo = $data['logoOld'];
      }
      $sql = "UPDATE utilities SET logo='$logo', name_web='$data[name_web]', keyword='$data[keyword]', description='$data[description]', author='$data[author]'";
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function users($conn, $data, $action)
  {

    if ($action == "update") {
      $sql = "UPDATE users SET id_role='$data[id_role]', id_active='$data[id_active]' WHERE id_user='$data[id_user]'";
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function role($conn, $data, $action)
  {
    if ($action == "insert") {
      $checkRole = "SELECT * FROM user_role WHERE role LIKE '%$data[role]%'";
      $checkRole = mysqli_query($conn, $checkRole);
      if (mysqli_num_rows($checkRole) > 0) {
        $message = "Maaf, role yang anda masukan sudah ada.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      } else {
        $sql = "INSERT INTO user_role(role) VALUES('$data[role]')";
      }
    }

    if ($action == "update") {
      if ($data['role'] !== $data['roleOld']) {
        $checkRole = "SELECT * FROM user_role WHERE role LIKE '%$data[role]%'";
        $checkRole = mysqli_query($conn, $checkRole);
        if (mysqli_num_rows($checkRole) > 0) {
          $message = "Maaf, role yang anda masukan sudah ada.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
      }
      $sql = "UPDATE user_role SET role='$data[role]' WHERE id_role='$data[id_role]'";
    }

    if ($action == "delete") {
      $sql = "DELETE FROM user_role WHERE id_role='$data[id_role]'";
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function menu($conn, $data, $action)
  {
    if ($action == "insert") {
      $namaFolder = strtolower($data['menu']);
      $namaFolder = str_replace(" ", "-", $namaFolder);
      $checkMenu = "SELECT * FROM user_menu WHERE menu='$data[menu]'";
      $checkMenu = mysqli_query($conn, $checkMenu);
      if (mysqli_num_rows($checkMenu) > 0) {
        $message = "Maaf, menu yang anda masukan sudah ada.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      } else {
        $pathFolder = __DIR__ . '/../views/' . $namaFolder;
        if (!is_dir($pathFolder)) {
          mkdir($pathFolder, 0777, true);
          $file = fopen($pathFolder . '/redirect.php', "w");
          fwrite($file, '<?php if (!isset($_SESSION["project_wisata_sumba_barat_daya"]["users"])) {
            header("Location: ../../auth/");
            exit;
          }
          ');
          fclose($file);

          $file_controller = fopen("../controller/" . $namaFolder . ".php", "w");
          fwrite($file_controller, '<?php
  
          require_once("../../config/Base.php");
          require_once("../../config/Auth.php");
          require_once("../../config/Alert.php");
          require_once("../../views/' . $namaFolder . '/redirect.php");
          ');
          fclose($file_controller);
        } else {
          $message = "Folder $namaFolder sudah ada!";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
        $sql = "INSERT INTO user_menu(icon,menu) VALUES('$data[icon]','$data[menu]')";
      }
    }

    if ($action == "update") {
      $menu_baru = strtolower(str_replace(' ', '-', $data['menu']));
      $menu_lama = strtolower(str_replace(' ', '-', $data['menuOld']));
      if ($menu_baru !== $menu_lama) {
        $checkMenu = "SELECT * FROM user_menu WHERE menu='$data[menu]'";
        $checkMenu = mysqli_query($conn, $checkMenu);
        if (mysqli_num_rows($checkMenu) > 0) {
          $message = "Maaf, menu yang anda masukan sudah ada.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
        $folder_lama = __DIR__ . '/../views/' . $menu_lama;
        $folder_baru = __DIR__ . '/../views/' . $menu_baru;
        if (is_dir($folder_lama)) {
          if ($menu_baru !== $menu_lama) {
            if (rename($folder_lama, $folder_baru)) {
            } else {
              $message = "Gagal mengubah nama folder.";
              $message_type = "danger";
              alert($message, $message_type);
              return false;
            }
          }
        } else {
          $message = "Folder lama tidak ditemukan.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
      }
      $sql = "UPDATE user_menu SET icon='$data[icon]', menu='$data[menu]' WHERE id_menu='$data[id_menu]'";
    }

    if ($action == "delete") {
      $menu = strtolower(str_replace(' ', '-', $data['menu']));
      $pathFolder = __DIR__ . '/../views/' . $menu;
      unlink("../controller/" . $menu . ".php");
      hapusFolderRecursively($pathFolder);
      $sql = "DELETE FROM user_menu WHERE id_menu='$data[id_menu]'";
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function sub_menu($conn, $data, $action, $baseURL)
  {
    $url = strtolower($data['title']);
    $url = str_replace(" ", "-", $url);

    if ($action == "insert") {
      $checkSubMenu = "SELECT * FROM user_sub_menu WHERE title='$data[title]'";
      $checkSubMenu = mysqli_query($conn, $checkSubMenu);
      if (mysqli_num_rows($checkSubMenu) > 0) {
        $message = "Maaf, nama sub menu yang anda masukan sudah ada.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      } else {
        $menu = "SELECT * FROM user_menu WHERE id_menu = '$data[id_menu]'";
        $view_menu = mysqli_query($conn, $menu);
        $data_menu = mysqli_fetch_assoc($view_menu);
        $menu = strtolower($data_menu['menu']);
        $menu = str_replace(" ", "-", $menu);

        $file_views = fopen("../views/" . $menu . "/" . $url . ".php", "w");
        fwrite($file_views, '<?php require_once("../../controller/' . $menu . '.php");
        $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "' . $data['title'] . '";
        require_once("../../templates/views_top.php"); ?>

        <div class="nxl-content" style="height: 100vh;">

          <!-- [ page-header ] start -->
          <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
              <div class="page-header-title">
                <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item">' . $data_menu['menu'] . '</li>
                <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></li>
              </ul>
            </div>
            <div class="page-header-right ms-auto">
              <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                  <a href="javascript:void(0)" class="page-header-right-close-toggle">
                    <i class="feather-arrow-left me-2"></i>
                    <span>Back</span>
                  </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                  <a href="add-' . $url . '" class="btn btn-primary">
                    <i class="feather-plus me-2"></i>
                    <span>Tambah</span>
                  </a>
                </div>
              </div>
              <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                  <i class="feather-align-right fs-20"></i>
                </a>
              </div>
            </div>
          </div>
          <!-- [ page-header ] end -->

          <!-- [ Main Content ] start -->
          <div class="main-content">
          </div>
          <!-- [ Main Content ] end -->

        </div>

        <?php require_once("../../templates/views_bottom.php") ?>
        ');
        fclose($file_views);

        $file_views_add = fopen("../views/" . $menu . "/add-" . $url . ".php", "w");
        fwrite($file_views_add, '<?php require_once("../../controller/' . $menu . '.php");
        $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Tambah ' . $data['title'] . '";
        require_once("../../templates/views_top.php"); ?>

        <div class="nxl-content" style="height: 100vh;">

          <!-- [ page-header ] start -->
          <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
              <div class="page-header-title">
                <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item">' . $data['title'] . '</li>
                <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></li>
              </ul>
            </div>
          </div>
          <!-- [ page-header ] end -->

          <!-- [ Main Content ] start -->
          <div class="main-content">
          </div>
          <!-- [ Main Content ] end -->

        </div>

        <?php require_once("../../templates/views_bottom.php") ?>
        ');
        fclose($file_views_add);

        $petik = "'";
        $file_views_edit = fopen("../views/" . $menu . "/edit-" . $url . ".php", "w");
        fwrite($file_views_edit, '<?php require_once("../../controller/' . $menu . '.php");
        if(!isset($_GET["p"])){
          header("Location: menu");
          exit();
        }else{
          $id = valid($conn, $_GET["p"]); 
          $pull_data = "SELECT * FROM  WHERE  = ' . $petik . '$id' . $petik . '";
          $store_data = mysqli_query($conn, $pull_data);
          $view_data = mysqli_fetch_assoc($store_data);
        $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] = "Ubah ' . $data['title'] . '";
        require_once("../../templates/views_top.php"); ?>

        <div class="nxl-content" style="height: 100vh;">

          <!-- [ page-header ] start -->
          <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
              <div class="page-header-title">
                <h5 class="m-b-10"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"] ?></h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item">' . $data['title'] . '</li>
                <li class="breadcrumb-item"><?= $_SESSION["project_wisata_sumba_barat_daya"]["name_page"].' . $petik . ' ' . $petik . '.$view_data[""]  ?></li>
              </ul>
            </div>
          </div>
          <!-- [ page-header ] end -->

          <!-- [ Main Content ] start -->
          <div class="main-content">
          </div>
          <!-- [ Main Content ] end -->

        </div>

        <?php }
        require_once("../../templates/views_bottom.php") ?>
        ');
        fclose($file_views_edit);

        $url_sub = $menu . "/" . $url;
        $sql = "INSERT INTO user_sub_menu(id_menu,id_active,title,url) VALUES('$data[id_menu]','$data[id_active]','$data[title]','$url_sub')";
      }
    }

    if ($action == "update") {
      if ($data['title'] !== $data['titleOld']) {
        $checkSubMenu = "SELECT * FROM user_sub_menu WHERE title='$data[title]'";
        $checkSubMenu = mysqli_query($conn, $checkSubMenu);
        if (mysqli_num_rows($checkSubMenu) > 0) {
          $message = "Maaf, nama sub menu yang anda masukan sudah ada.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
      }
      $menu = "SELECT * FROM user_menu WHERE id_menu = '$data[id_menu]'";
      $view_menu = mysqli_query($conn, $menu);
      $data_menu = mysqli_fetch_assoc($view_menu);
      $menu = strtolower($data_menu['menu']);
      $menu = str_replace(" ", "-", $menu);
      rename($menu . '/' . $data['urlOld'] . '.php', $menu . '/' . $url . '.php');
      rename($menu . '/' . "add-" . $data['urlOld'] . '.php', $menu . '/' . "add-" . $url . '.php');
      rename($menu . '/' . "edit-" . $data['urlOld'] . '.php', $menu . '/' . "edit-" . $url . '.php');
      $sql = "UPDATE user_sub_menu SET id_menu='$data[id_menu]', id_active='$data[id_active]', title='$data[title]', url='$url' WHERE id_sub_menu='$data[id_sub_menu]'";
    }

    if ($action == "delete") {
      unlink("../views/" . $data['menu'] . "/" . $url . ".php");
      unlink("../views/" . $data['menu'] . "/" . "add-" . $url . ".php");
      unlink("../views/" . $data['menu'] . "/" . "edit-" . $url . ".php");
      $sql = "DELETE FROM user_sub_menu WHERE id_sub_menu='$data[id_sub_menu]'";
    }

    mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }

  function role_permission($conn, $data, $action)
  {
    if ($action == "update") {
      $id_role = valid($conn, $data['id_role']);
      $affected_rows = 0;
      $reset_sql = "UPDATE permissions SET `view`=0, `create`=0, `edit`=0, `delete`=0 WHERE id_role='$id_role'";
      mysqli_query($conn, $reset_sql);
      if (isset($data['access']) && is_array($data['access'])) {
        foreach ($data['access'] as $id_sub_menu => $actions) {
          $view = isset($actions['view']) ? 1 : 0;
          $create = isset($actions['create']) ? 1 : 0;
          $edit = isset($actions['edit']) ? 1 : 0;
          $delete = isset($actions['delete']) ? 1 : 0;
          $check_sql = "SELECT id_permission FROM permissions WHERE id_role='$id_role' AND id_sub_menu='$id_sub_menu'";
          $check_query = mysqli_query($conn, $check_sql);
          if (mysqli_num_rows($check_query) > 0) {
            $update_sql = "UPDATE permissions SET `view`='$view', `create`='$create', `edit`='$edit', `delete`='$delete' 
                         WHERE id_role='$id_role' AND id_sub_menu='$id_sub_menu'";
            mysqli_query($conn, $update_sql);
            $affected_rows++;
          } else {
            $insert_sql = "INSERT INTO permissions (id_role, id_sub_menu, `view`, `create`, `edit`, `delete`) 
                         VALUES ('$id_role', '$id_sub_menu', '$view', '$create', '$edit', '$delete')";
            mysqli_query($conn, $insert_sql);
            $affected_rows++;
          }
        }
      }
      return $affected_rows > 0 ? $affected_rows : 1;
    }
    return false;
  }

  function kabupaten_kota($conn, $data, $action)
  {
    if ($action == "insert" || $action == "update") {
      $jenis = $data['jenis'] ?? '';
      if (!in_array($jenis, ['Kabupaten', 'Kota'])) {
        alert("Jenis wilayah tidak valid.", "danger");
        return false;
      }

      $id_condition = $action == "update" ? "AND id != '$data[id]'" : "";
      $check = mysqli_query($conn, "SELECT id FROM kabupaten_kota
        WHERE nama='$data[nama]' AND jenis='$jenis' $id_condition LIMIT 1");

      if ($check && mysqli_num_rows($check) > 0) {
        alert("Data $jenis $data[nama] sudah tersedia.", "danger");
        return false;
      }
    }

    if ($action == "insert") {
      $sql = "INSERT INTO kabupaten_kota (nama, jenis)
        VALUES ('$data[nama]', '$jenis')";
    }

    if ($action == "update") {
      $sql = "UPDATE kabupaten_kota SET
        nama='$data[nama]', jenis='$jenis'
        WHERE id='$data[id]'";
    }

    if ($action == "delete") {
      $sql = "DELETE FROM kabupaten_kota WHERE id='$data[id]'";
    }

    if (!isset($sql) || !mysqli_query($conn, $sql)) {
      if ($action == "delete" && mysqli_errno($conn) == 1451) {
        alert("Data tidak dapat dihapus karena masih digunakan oleh data kecamatan.", "danger");
      } else {
        alert("Data kabupaten/kota gagal diproses.", "danger");
      }
      return false;
    }

    if ($action == "update") {
      return max(mysqli_affected_rows($conn), 1);
    }

    return mysqli_affected_rows($conn);
  }

  function kecamatan($conn, $data, $action)
  {
    if ($action == "insert" || $action == "update") {
      $kabupaten_kota_id = (int) ($data['kabupaten_kota_id'] ?? 0);
      $parent = mysqli_query($conn, "SELECT id FROM kabupaten_kota WHERE id='$kabupaten_kota_id' LIMIT 1");
      if (!$parent || mysqli_num_rows($parent) == 0) {
        alert("Kabupaten/kota yang dipilih tidak valid.", "danger");
        return false;
      }

      $id_condition = $action == "update" ? "AND id != '$data[id]'" : "";
      $check = mysqli_query($conn, "SELECT id FROM kecamatan
        WHERE kabupaten_kota_id='$kabupaten_kota_id' AND nama='$data[nama]' $id_condition LIMIT 1");
      if ($check && mysqli_num_rows($check) > 0) {
        alert("Data kecamatan $data[nama] sudah tersedia pada kabupaten/kota tersebut.", "danger");
        return false;
      }
    }

    if ($action == "insert") {
      $sql = "INSERT INTO kecamatan (kabupaten_kota_id, nama)
        VALUES ('$kabupaten_kota_id', '$data[nama]')";
    }

    if ($action == "update") {
      $sql = "UPDATE kecamatan SET
        kabupaten_kota_id='$kabupaten_kota_id', nama='$data[nama]'
        WHERE id='$data[id]'";
    }

    if ($action == "delete") {
      $sql = "DELETE FROM kecamatan WHERE id='$data[id]'";
    }

    if (!isset($sql) || !mysqli_query($conn, $sql)) {
      if ($action == "delete" && mysqli_errno($conn) == 1451) {
        alert("Data tidak dapat dihapus karena masih digunakan oleh data desa atau kelurahan.", "danger");
      } else {
        alert("Data kecamatan gagal diproses.", "danger");
      }
      return false;
    }

    if ($action == "update") {
      return max(mysqli_affected_rows($conn), 1);
    }

    return mysqli_affected_rows($conn);
  }

  function desa($conn, $data, $action)
  {
    if ($action == "insert" || $action == "update") {
      $kecamatan_id = (int) ($data['kecamatan_id'] ?? 0);
      $parent = mysqli_query($conn, "SELECT id FROM kecamatan WHERE id='$kecamatan_id' LIMIT 1");
      if (!$parent || mysqli_num_rows($parent) == 0) {
        alert("Kecamatan yang dipilih tidak valid.", "danger");
        return false;
      }

      $id_condition = $action == "update" ? "AND id != '$data[id]'" : "";
      $check = mysqli_query($conn, "SELECT id FROM desa
        WHERE kecamatan_id='$kecamatan_id' AND nama='$data[nama]' $id_condition LIMIT 1");
      if ($check && mysqli_num_rows($check) > 0) {
        alert("Data desa $data[nama] sudah tersedia pada kecamatan tersebut.", "danger");
        return false;
      }
    }

    if ($action == "insert") {
      $sql = "INSERT INTO desa (kecamatan_id, nama)
        VALUES ('$kecamatan_id', '$data[nama]')";
    }

    if ($action == "update") {
      $sql = "UPDATE desa SET
        kecamatan_id='$kecamatan_id', nama='$data[nama]'
        WHERE id='$data[id]'";
    }

    if ($action == "delete") {
      $sql = "DELETE FROM desa WHERE id='$data[id]'";
    }

    if (!isset($sql) || !mysqli_query($conn, $sql)) {
      if ($action == "delete" && mysqli_errno($conn) == 1451) {
        alert("Data tidak dapat dihapus karena masih digunakan oleh data objek wisata.", "danger");
      } else {
        alert("Data desa gagal diproses.", "danger");
      }
      return false;
    }

    if ($action == "update") {
      return max(mysqli_affected_rows($conn), 1);
    }

    return mysqli_affected_rows($conn);
  }

  function kelurahan($conn, $data, $action)
  {
    if ($action == "insert" || $action == "update") {
      $kecamatan_id = (int) ($data['kecamatan_id'] ?? 0);
      $parent = mysqli_query($conn, "SELECT id FROM kecamatan WHERE id='$kecamatan_id' LIMIT 1");
      if (!$parent || mysqli_num_rows($parent) == 0) {
        alert("Kecamatan yang dipilih tidak valid.", "danger");
        return false;
      }

      $id_condition = $action == "update" ? "AND id != '$data[id]'" : "";
      $check = mysqli_query($conn, "SELECT id FROM kelurahan
        WHERE kecamatan_id='$kecamatan_id' AND nama='$data[nama]' $id_condition LIMIT 1");
      if ($check && mysqli_num_rows($check) > 0) {
        alert("Data kelurahan $data[nama] sudah tersedia pada kecamatan tersebut.", "danger");
        return false;
      }
    }

    if ($action == "insert") {
      $sql = "INSERT INTO kelurahan (kecamatan_id, nama)
        VALUES ('$kecamatan_id', '$data[nama]')";
    }

    if ($action == "update") {
      $sql = "UPDATE kelurahan SET
        kecamatan_id='$kecamatan_id', nama='$data[nama]'
        WHERE id='$data[id]'";
    }

    if ($action == "delete") {
      $sql = "DELETE FROM kelurahan WHERE id='$data[id]'";
    }

    if (!isset($sql) || !mysqli_query($conn, $sql)) {
      if ($action == "delete" && mysqli_errno($conn) == 1451) {
        alert("Data tidak dapat dihapus karena masih digunakan oleh data objek wisata.", "danger");
      } else {
        alert("Data kelurahan gagal diproses.", "danger");
      }
      return false;
    }

    if ($action == "update") {
      return max(mysqli_affected_rows($conn), 1);
    }

    return mysqli_affected_rows($conn);
  }

  function objek_wisata($conn, $data, $action)
  {
    $upload_dir = __DIR__ . "/../assets/img/objek-wisata/";
    $gambar = $data['gambar_old'] ?? "";
    $delete_gambar = "";

    if ($action == "insert" || $action == "update") {
      $desa_id = (int) ($data['desa_id'] ?? 0);
      $kelurahan_id = (int) ($data['kelurahan_id'] ?? 0);

      if (($desa_id > 0 && $kelurahan_id > 0) || ($desa_id <= 0 && $kelurahan_id <= 0)) {
        alert("Pilih tepat satu lokasi: desa atau kelurahan.", "danger");
        return false;
      }

      if ($desa_id > 0) {
        $check_lokasi = mysqli_query($conn, "SELECT id FROM desa WHERE id='$desa_id' LIMIT 1");
        $desa_id_sql = "'$desa_id'";
        $kelurahan_id_sql = "NULL";
      } else {
        $check_lokasi = mysqli_query($conn, "SELECT id FROM kelurahan WHERE id='$kelurahan_id' LIMIT 1");
        $desa_id_sql = "NULL";
        $kelurahan_id_sql = "'$kelurahan_id'";
      }

      if (!$check_lokasi || mysqli_num_rows($check_lokasi) == 0) {
        alert("Lokasi objek wisata tidak valid.", "danger");
        return false;
      }

      if (!empty($_FILES["gambar"]["name"])) {
        if (!is_dir($upload_dir)) {
          mkdir($upload_dir, 0755, true);
        }

        $file_name = basename($_FILES["gambar"]["name"]);
        $file_name = str_replace(" ", "-", $file_name);
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png"];

        if (!in_array($file_extension, $allowed_types)) {
          $message = "Maaf, hanya file gambar JPG, JPEG, dan PNG yang diizinkan.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }

        $new_file_name = crc32($file_name . time()) . "." . $file_extension;
        $upload_path = $upload_dir . $new_file_name;

        if (!move_uploaded_file($_FILES["gambar"]["tmp_name"], $upload_path)) {
          $message = "Gambar gagal diunggah.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }

        if ($action == "update" && !empty($gambar)) {
          $old_image_path = __DIR__ . "/../" . ltrim($gambar, "/");
          if (is_file($old_image_path)) {
            unlink($old_image_path);
          }
        }

        $gambar = "assets/img/objek-wisata/" . $new_file_name;
      }

      $harga_tiket = (int) $data["harga_tiket"];
      $jam_buka_sql = !empty($data["jam_buka"]) ? "'$data[jam_buka]'" : "NULL";
      $jam_tutup_sql = !empty($data["jam_tutup"]) ? "'$data[jam_tutup]'" : "NULL";
    }

    if ($action == "insert") {
      $sql = "INSERT INTO objek_wisata
        (nama_wisata, deskripsi, desa_id, kelurahan_id, harga_tiket, jam_buka, jam_tutup, gambar)
        VALUES
        ('$data[nama_wisata]', '$data[deskripsi]', $desa_id_sql, $kelurahan_id_sql, '$harga_tiket', $jam_buka_sql, $jam_tutup_sql, '$gambar')";
    }

    if ($action == "update") {
      $sql = "UPDATE objek_wisata SET
        nama_wisata='$data[nama_wisata]',
        deskripsi='$data[deskripsi]',
        desa_id=$desa_id_sql,
        kelurahan_id=$kelurahan_id_sql,
        harga_tiket='$harga_tiket',
        jam_buka=$jam_buka_sql,
        jam_tutup=$jam_tutup_sql,
        gambar='$gambar'
        WHERE id='$data[id]'";
    }

    if ($action == "delete") {
      $delete_gambar = $data['gambar'] ?? "";
      $sql = "DELETE FROM objek_wisata WHERE id='$data[id]'";
    }

    mysqli_query($conn, $sql);

    if ($action == "delete" && mysqli_affected_rows($conn) > 0 && !empty($delete_gambar)) {
      $image_path = __DIR__ . "/../" . ltrim($delete_gambar, "/");
      if (is_file($image_path)) {
        unlink($image_path);
      }
    }

    if ($action == "update" && mysqli_errno($conn) == 0) {
      return max(mysqli_affected_rows($conn), 1);
    }

    return mysqli_affected_rows($conn);
  }

  function informasi_wisata($conn, $data, $action)
  {
    if ($action == "insert") {
      global $id_user;
      $sql = "INSERT INTO informasi_wisata (id_user, judul, konten)
        VALUES ('$id_user', '$data[judul]', '$data[konten]')";
    }

    if ($action == "update") {
      $sql = "UPDATE informasi_wisata SET judul='$data[judul]', konten='$data[konten]' WHERE id='$data[id]'";
    }

    if ($action == "delete") {
      $sql = "DELETE FROM informasi_wisata WHERE id='$data[id]'";
    }

    mysqli_query($conn, $sql);

    if ($action == "update" && mysqli_errno($conn) == 0) {
      return max(mysqli_affected_rows($conn), 1);
    }

    return mysqli_affected_rows($conn);
  }

  function galeri($conn, $data, $action)
  {
    $upload_dir = __DIR__ . "/../assets/img/galeri/";
    $file_path = $data['file_path_old'] ?? "";
    $delete_file = "";

    if ($action == "insert" || $action == "update") {
      if ($action == "insert" && empty($_FILES["file_path"]["name"])) {
        $message = "Gambar galeri wajib diunggah.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      if (!empty($_FILES["file_path"]["name"])) {
        if (!is_dir($upload_dir)) {
          mkdir($upload_dir, 0755, true);
        }

        $file_name = basename($_FILES["file_path"]["name"]);
        $file_name = str_replace(" ", "-", $file_name);
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png"];

        if (!in_array($file_extension, $allowed_types)) {
          $message = "Maaf, hanya file gambar JPG, JPEG, dan PNG yang diizinkan.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }

        $new_file_name = crc32($file_name . time()) . "." . $file_extension;
        $upload_path = $upload_dir . $new_file_name;

        if (!move_uploaded_file($_FILES["file_path"]["tmp_name"], $upload_path)) {
          $message = "Gambar galeri gagal diunggah.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }

        if ($action == "update" && !empty($file_path)) {
          $old_file_path = __DIR__ . "/../" . ltrim($file_path, "/");
          if (is_file($old_file_path)) {
            unlink($old_file_path);
          }
        }

        $file_path = "assets/img/galeri/" . $new_file_name;
      }
    }

    if ($action == "insert") {
      $sql = "INSERT INTO galeri (objek_wisata_id, judul, file_path)
        VALUES ('$data[objek_wisata_id]', '$data[judul]', '$file_path')";
    }

    if ($action == "update") {
      $sql = "UPDATE galeri SET objek_wisata_id='$data[objek_wisata_id]', judul='$data[judul]', file_path='$file_path' WHERE id='$data[id]'";
    }

    if ($action == "delete") {
      $delete_file = $data['file_path'] ?? "";
      $sql = "DELETE FROM galeri WHERE id='$data[id]'";
    }

    mysqli_query($conn, $sql);

    if ($action == "delete" && mysqli_affected_rows($conn) > 0 && !empty($delete_file)) {
      $file_path = __DIR__ . "/../" . ltrim($delete_file, "/");
      if (is_file($file_path)) {
        unlink($file_path);
      }
    }

    if ($action == "update" && mysqli_errno($conn) == 0) {
      return max(mysqli_affected_rows($conn), 1);
    }

    return mysqli_affected_rows($conn);
  }

  function keranjang($conn, $data, $action)
  {
    if ($action == "update") {
      $jumlah_tiket = (int) $data['jumlah_tiket'];
      $objek_wisata = mysqli_query($conn, "SELECT harga_tiket FROM objek_wisata WHERE id='$data[id_objek_wisata]'");
      $data_objek_wisata = mysqli_fetch_assoc($objek_wisata);
      $harga_tiket = $data_objek_wisata ? (int) $data_objek_wisata['harga_tiket'] : 0;
      $total_harga_sementara = $harga_tiket * $jumlah_tiket;
      $sql = "UPDATE keranjang SET id_objek_wisata='$data[id_objek_wisata]', jumlah_tiket='$jumlah_tiket', total_harga_sementara='$total_harga_sementara' WHERE id='$data[id]'";
    }

    if ($action == "delete") {
      $sql = "DELETE FROM keranjang WHERE id='$data[id]'";
    }

    mysqli_query($conn, $sql);

    if ($action == "update" && mysqli_errno($conn) == 0) {
      return max(mysqli_affected_rows($conn), 1);
    }

    return mysqli_affected_rows($conn);
  }

  function sinkron_riwayat_transaksi($conn, $id_pemesanan)
  {
    $query_pemesanan = mysqli_query($conn, "SELECT id, id_wisatawan, status_pemesanan FROM pemesanan_tiket WHERE id='$id_pemesanan'");
    $data_pemesanan = mysqli_fetch_assoc($query_pemesanan);

    if (!$data_pemesanan) {
      return false;
    }

    $status_akhir = $data_pemesanan['status_pemesanan'] ?: "Pending";
    $query_pembayaran = mysqli_query($conn, "SELECT pembayaran.status_bayar, e_tiket.status_tiket
      FROM pembayaran
      LEFT JOIN e_tiket ON e_tiket.id_pembayaran=pembayaran.id
      WHERE pembayaran.id_pemesanan='$id_pemesanan'
      ORDER BY pembayaran.id DESC
      LIMIT 1");
    $data_pembayaran = mysqli_fetch_assoc($query_pembayaran);

    if ($data_pembayaran) {
      if (!empty($data_pembayaran['status_tiket'])) {
        $status_akhir = $data_pembayaran['status_tiket'];
      } else if (!empty($data_pembayaran['status_bayar'])) {
        $status_akhir = $data_pembayaran['status_bayar'];
      }
    }

    $check_riwayat = mysqli_query($conn, "SELECT id FROM riwayat_transaksi WHERE id_pemesanan='$id_pemesanan'");
    $data_riwayat = mysqli_fetch_assoc($check_riwayat);

    if ($data_riwayat) {
      mysqli_query($conn, "UPDATE riwayat_transaksi SET
        id_wisatawan='$data_pemesanan[id_wisatawan]',
        status_akhir='$status_akhir',
        tanggal_selesai=current_timestamp
        WHERE id='$data_riwayat[id]'");
    } else {
      mysqli_query($conn, "INSERT INTO riwayat_transaksi
        (id_wisatawan, id_pemesanan, status_akhir)
        VALUES
        ('$data_pemesanan[id_wisatawan]', '$id_pemesanan', '$status_akhir')");
    }

    return true;
  }

  function pemesanan_tiket($conn, $data, $action)
  {
    if ($action == "insert") {
      $jumlah_tiket = (int) $data['jumlah_tiket'];
      $mode_wisatawan = $data['mode_wisatawan'] ?: "existing";

      if ($mode_wisatawan == "existing") {
        $id_wisatawan = $data['id_wisatawan'];
        $check_wisatawan = mysqli_query($conn, "SELECT id_user FROM users WHERE id_user='$id_wisatawan'");
        if (!$check_wisatawan || mysqli_num_rows($check_wisatawan) == 0) {
          $message = "Maaf, data wisatawan tidak ditemukan.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }
      } else {
        $check_email = mysqli_query($conn, "SELECT id_user FROM users WHERE email='$data[email]'");
        if (mysqli_num_rows($check_email) > 0) {
          $message = "Maaf, email wisatawan yang anda masukan sudah terdaftar. Silakan pilih wisatawan dari data yang sudah ada.";
          $message_type = "danger";
          alert($message, $message_type);
          return false;
        }

        $role_wisatawan = mysqli_query($conn, "SELECT id_role FROM user_role WHERE LOWER(TRIM(role))='wisatawan' LIMIT 1");
        $data_role_wisatawan = mysqli_fetch_assoc($role_wisatawan);
        $id_role_wisatawan = $data_role_wisatawan ? $data_role_wisatawan['id_role'] : 3;
        $token = generateToken();
        $en_user = password_hash($token, PASSWORD_DEFAULT);
        $password = password_hash("wisatawan123", PASSWORD_DEFAULT);
        $insert_wisatawan = "INSERT INTO users
          (id_role, id_active, en_user, token, name, email, password, no_hp, asal_daerah)
          VALUES
          ('$id_role_wisatawan', '1', '$en_user', '$token', '$data[nama_wisatawan]', '$data[email]', '$password', '$data[no_hp]', '$data[asal_daerah]')";
        mysqli_query($conn, $insert_wisatawan);

        if (mysqli_affected_rows($conn) <= 0) {
          return false;
        }

        $id_wisatawan = mysqli_insert_id($conn);
      }

      $objek_wisata = mysqli_query($conn, "SELECT harga_tiket FROM objek_wisata WHERE id='$data[id_objek_wisata]'");
      $data_objek_wisata = mysqli_fetch_assoc($objek_wisata);
      $harga_tiket = $data_objek_wisata ? (int) $data_objek_wisata['harga_tiket'] : 0;
      $total_tagihan = $harga_tiket * $jumlah_tiket;
      $status_pemesanan = "Pending";
      $kode_booking = "BK" . date("YmdHis") . rand(100, 999);

      $sql = "INSERT INTO pemesanan_tiket
        (kode_booking, id_wisatawan, id_objek_wisata, tgl_kunjungan, jumlah_tiket, total_tagihan, status_pemesanan)
        VALUES
        ('$kode_booking', '$id_wisatawan', '$data[id_objek_wisata]', '$data[tgl_kunjungan]', '$jumlah_tiket', '$total_tagihan', '$status_pemesanan')";

      mysqli_query($conn, $sql);
      $affected_rows = mysqli_affected_rows($conn);

      if ($affected_rows > 0) {
        sinkron_riwayat_transaksi($conn, mysqli_insert_id($conn));
      }

      return $affected_rows;
    }

    if ($action == "update") {
      $jumlah_tiket = (int) $data['jumlah_tiket'];
      $objek_wisata = mysqli_query($conn, "SELECT harga_tiket FROM objek_wisata WHERE id='$data[id_objek_wisata]'");
      $data_objek_wisata = mysqli_fetch_assoc($objek_wisata);
      $harga_tiket = $data_objek_wisata ? (int) $data_objek_wisata['harga_tiket'] : 0;
      $total_tagihan = $harga_tiket * $jumlah_tiket;
      if (isset($data['status_pemesanan'])) {
        $status_pemesanan = $data['status_pemesanan'] ?: "Pending";
      } else {
        $check_pemesanan = mysqli_query($conn, "SELECT status_pemesanan FROM pemesanan_tiket WHERE id='$data[id]'");
        $data_pemesanan = mysqli_fetch_assoc($check_pemesanan);
        $status_pemesanan = $data_pemesanan ? $data_pemesanan['status_pemesanan'] : "Pending";
      }

      $sql = "UPDATE pemesanan_tiket SET
        id_wisatawan='$data[id_wisatawan]',
        id_objek_wisata='$data[id_objek_wisata]',
        tgl_kunjungan='$data[tgl_kunjungan]',
        jumlah_tiket='$jumlah_tiket',
        total_tagihan='$total_tagihan',
        status_pemesanan='$status_pemesanan'
        WHERE id='$data[id]'";
    }

    if ($action == "delete") {
      $sql = "DELETE FROM pemesanan_tiket WHERE id='$data[id]'";
    }

    mysqli_query($conn, $sql);

    if ($action == "update" && mysqli_errno($conn) == 0) {
      sinkron_riwayat_transaksi($conn, $data['id']);
      return max(mysqli_affected_rows($conn), 1);
    }

    return mysqli_affected_rows($conn);
  }

  function sinkron_e_tiket_pembayaran($conn, $id_pembayaran, $status_bayar, $waktu_bayar = "")
  {
    $status_bayar_lower = strtolower(trim($status_bayar));
    $check_e_tiket = mysqli_query($conn, "SELECT id FROM e_tiket WHERE id_pembayaran='$id_pembayaran'");
    $data_e_tiket = mysqli_fetch_assoc($check_e_tiket);

    if ($status_bayar_lower == "paid" || $status_bayar_lower == "settlement") {
      $mulai_berlaku = !empty($waktu_bayar) ? $waktu_bayar : date("Y-m-d H:i:s");
      $berlaku_sampai = date("Y-m-d H:i:s", strtotime($mulai_berlaku . " +1 day"));

      if ($data_e_tiket) {
        mysqli_query($conn, "UPDATE e_tiket SET status_tiket='Active', berlaku_sampai='$berlaku_sampai' WHERE id='$data_e_tiket[id]'");
      } else {
        $kode_qr = "ETK-" . date("YmdHis") . rand(100, 999);
        while (mysqli_num_rows(mysqli_query($conn, "SELECT id FROM e_tiket WHERE kode_qr='$kode_qr'")) > 0) {
          $kode_qr = "ETK-" . date("YmdHis") . rand(100, 999);
        }

        mysqli_query($conn, "INSERT INTO e_tiket (id_pembayaran, kode_qr, status_tiket, berlaku_sampai) VALUES ('$id_pembayaran', '$kode_qr', 'Active', '$berlaku_sampai')");
      }
    } else if ($data_e_tiket) {
      mysqli_query($conn, "UPDATE e_tiket SET status_tiket='Inactive', berlaku_sampai=NULL WHERE id='$data_e_tiket[id]'");
    }
  }

  function pembayaran($conn, $data, $action)
  {
    if ($action == "insert") {
      $status_bayar = "Paid";
      $waktu_bayar = !empty($data['waktu_bayar']) ? str_replace("T", " ", $data['waktu_bayar']) : "";
      if ((strtolower(trim($status_bayar)) == "paid" || strtolower(trim($status_bayar)) == "settlement") && empty($waktu_bayar)) {
        $waktu_bayar = date("Y-m-d H:i:s");
      }
      $waktu_bayar_sql = !empty($waktu_bayar) ? "'$waktu_bayar'" : "NULL";
      $order_id = "ORDER-" . date("YmdHis") . rand(100, 999);
      $snap_token = "SNAP-" . bin2hex(random_bytes(16));

      while (mysqli_num_rows(mysqli_query($conn, "SELECT id FROM pembayaran WHERE order_id='$order_id'")) > 0) {
        $order_id = "ORDER-" . date("YmdHis") . rand(100, 999);
      }

      $sql = "INSERT INTO pembayaran
        (id_pemesanan, order_id, snap_token, metode_pembayaran, waktu_bayar, status_bayar)
        VALUES
        ('$data[id_pemesanan]', '$order_id', '$snap_token', '$data[metode_pembayaran]', $waktu_bayar_sql, '$status_bayar')";

      mysqli_query($conn, $sql);
      $affected_rows = mysqli_affected_rows($conn);
      $id_pembayaran = mysqli_insert_id($conn);

      if ($affected_rows > 0) {
        mysqli_query($conn, "UPDATE pemesanan_tiket SET status_pemesanan='Confirmed' WHERE id='$data[id_pemesanan]'");
        sinkron_e_tiket_pembayaran($conn, $id_pembayaran, $status_bayar, $waktu_bayar);
        sinkron_riwayat_transaksi($conn, $data['id_pemesanan']);
      }

      return $affected_rows;
    }

    if ($action == "update") {
      if (isset($data['status_bayar'])) {
        $status_bayar = $data['status_bayar'] ?: "Unpaid";
      } else {
        $check_pembayaran = mysqli_query($conn, "SELECT status_bayar FROM pembayaran WHERE id='$data[id]'");
        $data_pembayaran = mysqli_fetch_assoc($check_pembayaran);
        $status_bayar = $data_pembayaran ? $data_pembayaran['status_bayar'] : "Unpaid";
      }
      $waktu_bayar = !empty($data['waktu_bayar']) ? str_replace("T", " ", $data['waktu_bayar']) : "";
      if ((strtolower(trim($status_bayar)) == "paid" || strtolower(trim($status_bayar)) == "settlement") && empty($waktu_bayar)) {
        $waktu_bayar = date("Y-m-d H:i:s");
      }
      $waktu_bayar_sql = !empty($waktu_bayar) ? "'$waktu_bayar'" : "NULL";

      $sql = "UPDATE pembayaran SET
        metode_pembayaran='$data[metode_pembayaran]',
        waktu_bayar=$waktu_bayar_sql,
        status_bayar='$status_bayar'
        WHERE id='$data[id]'";

      mysqli_query($conn, $sql);
      $affected_rows = mysqli_affected_rows($conn);

      if (mysqli_errno($conn) == 0) {
        sinkron_e_tiket_pembayaran($conn, $data['id'], $status_bayar, $waktu_bayar);
        $query_pembayaran = mysqli_query($conn, "SELECT id_pemesanan FROM pembayaran WHERE id='$data[id]'");
        $data_pembayaran = mysqli_fetch_assoc($query_pembayaran);
        if ($data_pembayaran) {
          sinkron_riwayat_transaksi($conn, $data_pembayaran['id_pemesanan']);
        }
        return max($affected_rows, 1);
      }

      return $affected_rows;
    }

    if ($action == "delete") {
      $query_pembayaran = mysqli_query($conn, "SELECT id_pemesanan FROM pembayaran WHERE id='$data[id]'");
      $data_pembayaran = mysqli_fetch_assoc($query_pembayaran);
      $sql = "DELETE FROM pembayaran WHERE id='$data[id]'";
      mysqli_query($conn, $sql);
      $affected_rows = mysqli_affected_rows($conn);

      if ($affected_rows > 0 && $data_pembayaran) {
        sinkron_riwayat_transaksi($conn, $data_pembayaran['id_pemesanan']);
      }

      return $affected_rows;
    }

    return mysqli_affected_rows($conn);
  }

  function e_tiket($conn, $data, $action)
  {
    if ($action == "delete") {
      $query_e_tiket = mysqli_query($conn, "SELECT pembayaran.id_pemesanan
        FROM e_tiket
        LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
        WHERE e_tiket.id='$data[id]'");
      $data_e_tiket = mysqli_fetch_assoc($query_e_tiket);
      $sql = "DELETE FROM e_tiket WHERE id='$data[id]'";
    }

    mysqli_query($conn, $sql);
    $affected_rows = mysqli_affected_rows($conn);

    if ($affected_rows > 0 && isset($data_e_tiket['id_pemesanan'])) {
      sinkron_riwayat_transaksi($conn, $data_e_tiket['id_pemesanan']);
    }

    return $affected_rows;
  }

  function data_kunjungan($conn, $data, $action)
  {
    if ($action == "insert") {
      $kode_qr = valid($conn, trim($data['kode_qr'] ?? ''));
      $session_user = $_SESSION["project_wisata_sumba_barat_daya"]["users"] ?? [];
      $id_petugas = valid($conn, $session_user["id_user"] ?? $session_user["id"] ?? '');

      if (empty($kode_qr)) {
        $message = "Kode QR tidak boleh kosong.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      if (empty($id_petugas)) {
        $message = "Session petugas tidak valid. Silakan login ulang.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      $query_e_tiket = mysqli_query($conn, "SELECT e_tiket.*, pembayaran.id_pemesanan
        FROM e_tiket
        LEFT JOIN pembayaran ON e_tiket.id_pembayaran=pembayaran.id
        WHERE e_tiket.kode_qr='$kode_qr'
        LIMIT 1");

      if (!$query_e_tiket || mysqli_num_rows($query_e_tiket) == 0) {
        $message = "Kode QR tidak ditemukan.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      $data_e_tiket = mysqli_fetch_assoc($query_e_tiket);
      if (strtolower($data_e_tiket['status_tiket'] ?? '') != "active") {
        $message = "E-Tiket tidak aktif atau sudah digunakan.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      if (!empty($data_e_tiket['berlaku_sampai']) && strtotime($data_e_tiket['berlaku_sampai']) < time()) {
        mysqli_query($conn, "UPDATE e_tiket SET status_tiket='Expired' WHERE id='$data_e_tiket[id]'");
        if (!empty($data_e_tiket['id_pemesanan'])) {
          sinkron_riwayat_transaksi($conn, $data_e_tiket['id_pemesanan']);
        }

        $message = "Masa berlaku e-tiket sudah berakhir.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      $check_kunjungan = mysqli_query($conn, "SELECT id FROM data_kunjungan WHERE id_e_tiket='$data_e_tiket[id]'");
      if ($check_kunjungan && mysqli_num_rows($check_kunjungan) > 0) {
        $message = "E-Tiket ini sudah tercatat sebagai kunjungan.";
        $message_type = "danger";
        alert($message, $message_type);
        return false;
      }

      $keterangan = !empty($data['keterangan']) ? valid($conn, $data['keterangan']) : "Scan QR wisatawan";
      $sql = "INSERT INTO data_kunjungan (id_e_tiket, id_petugas, keterangan)
        VALUES ('$data_e_tiket[id]', '$id_petugas', '$keterangan')";
    }

    if (empty($sql)) {
      return false;
    }

    $query = mysqli_query($conn, $sql);
    if (!$query) {
      $message = "Data kunjungan gagal disimpan: " . mysqli_error($conn);
      $message_type = "danger";
      alert($message, $message_type);
      return false;
    }

    $affected_rows = mysqli_affected_rows($conn);

    if ($action == "insert" && $affected_rows > 0) {
      mysqli_query($conn, "UPDATE e_tiket SET status_tiket='Used' WHERE id='$data_e_tiket[id]'");
      if (!empty($data_e_tiket['id_pemesanan'])) {
        sinkron_riwayat_transaksi($conn, $data_e_tiket['id_pemesanan']);
      }
    }

    return $affected_rows;
  }

  function __name($conn, $data, $action)
  {
    if ($action == "insert") {
    }

    if ($action == "update") {
    }

    if ($action == "delete") {
    }

    // mysqli_query($conn, $sql);
    return mysqli_affected_rows($conn);
  }
}
