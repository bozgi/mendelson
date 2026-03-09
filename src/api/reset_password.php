<?php
require '../db.php';
$email = json_decode(file_get_contents('php://input'), true)["email"];

if (!isset($email)) {
    echo json_encode([
        "success" => false,
        "message" => "Nie podano adresu e-mail"
    ]);
    return;
}
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo json_encode([
        "success" => false,
        "message" => "Nie znaleziono użytkownika o podanym adresie e-mail"
    ]);
    return;
}
$user = $result->fetch_assoc();
$user_id = $user["id"];
$operation_hash = uniqid("op_", true);
$stmt = $conn->prepare("INSERT INTO operations (hash, account_id, operation_type) VALUES (?, ?, 'RESTORE_PASSWORD')");
$stmt->bind_param("si", $operation_hash, $user_id);
if ($stmt->execute()) {
    require '../mailer.php';
    $reset_link = "http://localhost:8000/login.php?operation=" . $operation_hash;
    $email_body = "Kliknij w poniższy link, aby zresetować swoje hasło: <a href='" . $reset_link . "'>Zresetuj hasło</a>";
    if (sendEmail($email, "Resetowanie hasła", $email_body)) {
        echo json_encode([
            "success" => true,
            "message" => "Link do resetowania hasła został wysłany na podany adres e-mail"
        ]);
        return;
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Wystąpił błąd podczas wysyłania e-maila. Proszę spróbować ponownie później."
        ]);
        return;
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Wystąpił błąd podczas tworzenia operacji resetowania hasła. Proszę spróbować ponownie później."
    ]);
    return;
}