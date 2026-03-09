<?php
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

    require '../db.php';
    $operation_hash = uniqid("op_", true);
    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt->bind_param("ss", $email, $password_hash);
    $stmt->execute();

    $user_id = $conn->insert_id;

    $stmt = $conn->prepare(
        "INSERT INTO operations (hash, account_id, operation_type) VALUES (?, ?, 'REGISTER')"
    );
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("si", $operation_hash, $user_id);
    if (!$stmt->execute()) {
        echo json_encode([
            "success" => false,
            "message" => "Following error occurred: " . $stmt->error
        ]);
        return;
    }

    require '../mailer.php';
    $activation_link = "http://localhost:8000/login.php?operation=" . $operation_hash;
    $email_body = "Dziękujemy za rejestrację! Kliknij w poniższy link, aby aktywować swoje konto: <a href='" . $activation_link . "'>Aktywuj konto</a>";
    if (!sendEmail($email, "Aktywacja konta", $email_body)) {
        echo json_encode([
            "success" => false,
            "message" => "Nie można wysłać e-maila z linkiem aktywacyjnym."
        ]);
        return;
    } else {
        echo json_encode([
            "success" => true,
            "message" => "Rejestracja przebiegła pomyślnie! Sprawdź swoją skrzynkę e-mail, aby aktywować konto."
        ]);
        return;
    }
}
