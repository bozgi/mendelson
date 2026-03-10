<?php
session_start();
if (!isset($_SESSION['id']) || !isset($_GET['id'])) {
    http_response_code(401);
    exit;
}

require_once '../db.php';
$stmt = $conn->prepare("SELECT id FROM graphs WHERE user_id = ? AND id = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("ii", $_SESSION['id'], $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "No graph found for this user"
    ]);
    exit;
}

require '../chart.php';
if (!isset($_GET["width"]) || !isset($_GET["height"])) {
    $_GET["width"] = 500;
    $_GET["height"] = 500;
}
$chart = new Chart($_GET["width"], $_GET["height"]);
$chart->setXTitle("Data");
$chart->setYTitle("Temperatura [°C]");
$chart->drawGraph($_GET['id']);
$im = $chart->output();
$points = $chart->getPointData();
header("Content-Type: application/json");
echo json_encode([
    "success" => true,
    "image" => base64_encode($im),
    "points" => $points
]);