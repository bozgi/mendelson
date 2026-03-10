<?php
session_start();
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    exit;
}
header('Content-Type: application/json');

require '../db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {   
    $data = json_decode(file_get_contents('php://input'), true);
    $date = $data['date'] ?? null;
    $temperature = $data['temperature'] ?? null;
    $status = $data['status'] ?? null;
    $graph_id = $data['graphId'] ?? null;

    if (!$date || ($temperature === null && ($status != 'n/a' && $status != 'sick')) || !$status || !$graph_id) {
        echo json_encode([
            "success" => false,
            "message" => "Missing required fields"
        ]);
        exit;
    }

    if ($status == 'sick' || $status == 'n/a') {
        $temperature = null;
    }

    $stmt = $conn->prepare("
        UPDATE measurements
        JOIN graphs ON measurements.graph_id = graphs.id
        SET temperature_c = ?, status = ?
        WHERE measurements.date = ? AND graphs.user_id = ? AND graphs.id = ?
    ");

    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => "Prepare failed: " . $conn->error
        ]);
        exit;
    }

    $stmt->bind_param("dssii", $temperature, $status, $date, $_SESSION['id'], $graph_id);

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
    exit;
}

echo json_encode([
    "success" => false,
    "message" => "Invalid request"
]);
