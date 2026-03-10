<?php
session_start();
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    exit;
}

require '../db.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {   
    $data = json_decode(file_get_contents('php://input'), true);
    $graph_id = $data['graphId'] ?? null;

    if (!$graph_id) {
        echo json_encode([
            "success" => false,
            "message" => "Missing graph ID"
        ]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM graphs WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => "Prepare failed: " . $conn->error
        ]);
        exit;
    }
    $stmt->bind_param("ii", $graph_id, $_SESSION['id']);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Graph deleted successfully"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No graph deleted"
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Execute failed: " . $stmt->error
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]); 
}