<?php
require '../db.php';

$json = json_decode(file_get_contents("php://input"), true);
$operation_hash = $json["operation"];
$password = $json["password"];

$stmt = $conn->prepare("SELECT * FROM operations WHERE hash = ? AND operation_type = 'RESTORE_PASSWORD'");
$stmt->bind_param("s", $operation_hash);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo json_encode([
        "success" => false,
        "message" => "Nieprawidłowa operacja"
    ]);
    return;
}
$operation = $result->fetch_assoc();
$user_id = $operation["account_id"];
$password_hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $password_hash, $user_id);
if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Hasło zostało zresetowane pomyślnie! Możesz teraz zalogować się na swoje konto."
    ]);
    $stmt = $conn->prepare("DELETE FROM operations WHERE hash = ?");
    $stmt->bind_param("s", $operation_hash);
    $stmt->execute();
} else {
    echo json_encode([
        "success" => false,
        "message" => "Wystąpił błąd podczas resetowania hasła. Proszę spróbować ponownie później."
    ]);
    return;
}