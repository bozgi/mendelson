<?php
session_start();
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    exit;
}

$json = json_decode(file_get_contents('php://input'), true);

if (!isset($json['startDate']) || !isset($json['endDate'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Start date and end date are required"
    ]);
    exit;
}

$start_date = $json['startDate'];
$end_date = $json['endDate'];

$start_date_obj = new DateTime($start_date);
$end_date_obj = new DateTime($end_date);

if ($start_date_obj >= $end_date_obj) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Start date must be before end date"
    ]);
    exit;
}

require '../db.php';
$stmt = $conn->prepare("INSERT INTO graphs (user_id, start_date, end_date) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $_SESSION['id'], $start_date, $end_date);
$stmt->execute();

if ($stmt->affected_rows == 0) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Failed to create graph: " . $stmt->error
    ]);
    exit;
}

$period = new DatePeriod(
    $start_date_obj,
    new DateInterval("P1D"),
    (clone $end_date_obj)->modify("+1 day")
);

$values = [];
$params = [];
$types = "";
$graph_id = $conn->insert_id;

foreach ($period as $date) {
    $values[] = "(?, ?)";
    $params[] = $graph_id;
    $params[] = $date->format("Y-m-d");
    $types .= "is";
}

$sql = "INSERT INTO measurements (graph_id, date) VALUES "
     . implode(",", $values);

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode([
        "success" => true,
        "message" => "Graph created successfully"
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Failed to create graph: " . $stmt->error
    ]);
}
