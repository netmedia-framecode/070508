<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/../assets/vendor/autoload.php";

function smtp_mail($to, $subject, $message, $from_name, $from, $cc, $bcc, $debug = false)
{
  $mail = new PHPMailer;
  $mail->SMTPDebug = 0;
  $mail->isSMTP();
  $mail->ClearAddresses();
  $mail->ClearCCs();
  $mail->ClearBCCs();
  $mail->SMTPAuth         = true;
  $mail->Host             = "tugasakhir.my.id;103.251.44.248";
  $mail->Port             = 587;
  $mail->SMTPSecure       = "tls";
  $mail->Username         = "no-reply@tugasakhir.my.id";
  $mail->Password         = "Netmedia040700_";
  $default_email_from     = "no-reply@tugasakhir.my.id";
  $default_email_from_name = "Wisata Sumba Barat Daya";
  if (empty($from)) $mail->From = $default_email_from;
  else $mail->From = $from;
  if (empty($from_name)) $mail->FromName = $default_email_from_name;
  else $mail->FromName = $from_name;
  if (is_array($to)) {
    foreach ($to as $k => $v) {
      $mail->addAddress($v);
    }
  } else {
    $mail->addAddress($to);
  }
  if (!empty($cc)) {
    if (is_array($cc)) {
      foreach ($cc as $k => $v) {
        $mail->addCC($v);
      }
    } else {
      $mail->addCC($cc);
    }
  }
  if (!empty($bcc)) {
    if (is_array($bcc)) {
      foreach ($bcc as $k => $v) {
        $mail->addBCC($v);
      }
    } else {
      $mail->addBCC($bcc);
    }
  }
  $mail->isHTML(true);
  $mail->Subject     = $subject;
  $mail->Body       = $message;
  $mail->AltBody    = $message;
  if (!$mail->send())
    return 1;
  else
    return 0;
}
