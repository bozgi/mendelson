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
    $start_date = $data['startDate'] ?? null;
    $end_date = $data['endDate'] ?? null;

    if (!$graph_id || !$start_date || !$end_date) {
        echo json_encode([
            "success" => false,
            "message" => "Missing fields"
        ]);
        exit;
    }

    $stmt = $conn->prepare("SELECT start_date, end_date FROM graphs WHERE id = ? AND user_id = ?");
    
    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => "Prepare failed: " . $conn->error
        ]);
        exit;
    }

    $db_start_date = null;
    $db_end_date = null;

    $stmt->bind_param("ii", $graph_id, $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($db_start_date, $db_end_date);

    if (!$stmt->fetch()) {
        echo json_encode([
            "success" => false,
            "message" => "Fetch failed"
        ]);
        exit;
    }

    $start_date_obj = new DateTime($start_date);
    $end_date_obj = new DateTime($end_date);

    $db_start_date_obj = new DateTime($db_start_date);
    $db_end_date_obj = new DateTime($db_end_date);

    if ($start_date_obj > $end_date_obj) {
        echo json_encode(["success" => false, "message" => "Start date must be before end date"]);
        exit;
    }

    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM measurements WHERE graph_id = ? AND (date < ? OR date > ?)");
    $stmt->bind_param("iss", $graph_id, $start_date, $end_date);
    $stmt->execute();
    $stmt->close();

    $period = new DatePeriod(
        $start_date_obj,
        new DateInterval("P1D"),
        (clone $end_date_obj)->modify("+1 day")
    );

    $stmt = $conn->prepare("INSERT IGNORE INTO measurements (graph_id, date, status) VALUES (?, ?, 'n/a')");
    foreach ($period as $date) {
        $formatted = $date->format('Y-m-d');
        $stmt->bind_param("is", $graph_id, $formatted);
        $stmt->execute();
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE graphs SET start_date = ?, end_date = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $start_date, $end_date, $graph_id, $_SESSION['id']);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["success" => true, "message" => "Date range updated"]);
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]); 
}