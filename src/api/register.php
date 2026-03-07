<?php
include "../phpmailer.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
$mysqli = new mysqli("mysql", "bozgi", "hujgnuj", "db");

$errors = [];

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data["email"]) && isset($data["password"])   ) {
    $email = $data["email"];
    $password = $data["password"];

    if (preg_match("/.{8,}/", $password) != 1) {
        array_push($errors, "Więcej niż 8 znaków");
    }
    if (preg_match("/[a-z]/", $password) != 1) {
        array_push($errors, "Conajmniej jeden mały znak");
    }
    if (preg_match("/[A-Z]/", $password) != 1) {
        array_push($errors, "Conajmniej jeden duży znak");
    }
    if (preg_match("/[0-9]/", $password) != 1) {
        array_push($errors, "Conajmniej jedna cyfra");
    }

    if (isset($errors[0])) {
        echo json_encode([
            "success" => false,
            "message" => $errors
        ]);
        return;
    }

    // echo json_encode([
    //     "success" => true
    // ]);

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'oskaros040@gmail.com';
    $mail->Password = 'dkcm ntfd sahr ribu';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->SMTPDebug = 2; // shows server conversation
    $mail->Debugoutput = 'html';
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('oskaros040@gmail.com', 'Zespół Wykresiki');
    $mail->addAddress($email, 'User');
    // echo $email;

    $mail->isHTML(false);
    $mail->Subject = 'Test mail';
    $mail->Body = 'Hello!';

    if (!$mail->send()) {
        echo json_encode([
            "success" => false,
            "message" => "Failed to send email"
        ]);
        return;
    } else {
        echo json_encode([
            "success" => true,
            "message" => "Registration successful, email sent"
        ]);
    }

}
