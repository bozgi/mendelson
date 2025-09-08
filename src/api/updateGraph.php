<?php
header('Content-Type: application/json');

$mysqli = new mysqli("mysql", "bozgi", "hujgnuj", "db");

if ($mysqli->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "DB connection failed: " . $mysqli->connect_error
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {   
    $data = json_decode(file_get_contents('php://input'), true);
    $day = $data['day'] ?? null;
    $temperature = $data['temperature'] ?? null;
    $status = $data['status'] ?? null;

    if (!$day || $temperature === null || !$status) {
        echo json_encode([
            "success" => false,
            "message" => "Missing required fields"
        ]);
        exit;
    }

    $stmt = $mysqli->prepare("
        UPDATE measurements
        SET temperature_c = ?, status = ?
        WHERE day_of_month = ?
    ");

    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => "Prepare failed: " . $mysqli->error
        ]);
        exit;
    }

    $stmt->bind_param("dsi", $temperature, $status, $day);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Measurement updated successfully"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No row updated"
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Execute failed: " . $stmt->error
        ]);
    }

    $stmt->close();
    $mysqli->close();
    exit;
}

echo json_encode([
    "success" => false,
    "message" => "Invalid request"
]);
