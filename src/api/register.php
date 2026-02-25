<?php
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

    echo json_encode([
        "success" => true
    ]);

    

}
