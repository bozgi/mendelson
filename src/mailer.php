<?php
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'secret.php';

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'oskaros040@gmail.com';
$mail->Password = $password;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
// $mail->SMTPDebug = 2;
// $mail->Debugoutput = 'html'; 
$mail->CharSet = 'UTF-8';
$mail->setFrom('oskaros040@gmail.com', 'Zespół Wykresiki');

function sendEmail($to, $subject, $body) {
    global $mail;
    $mail->isHTML(true);
    $mail->clearAddresses();
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $body;
    if ($mail->send()) return true;
    else {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}