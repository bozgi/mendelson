<?php
session_start();
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    exit;
}

require '../db.php';
$stmt = $conn->prepare("SELECT id, start_date, end_date FROM graphs WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
$graphs = [];
while ($row = $result->fetch_assoc()) {
    $graphs[] = $row;
}
echo json_encode($graphs);