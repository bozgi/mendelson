<?php
require '../chart.php';
if (!isset($_GET["width"]) || !isset($_GET["height"])) {
    $_GET["width"] = 500;
    $_GET["height"] = 500;
}
$chart = new Chart($_GET["width"], $_GET["height"]);
$chart->setXTitle("Dzień miesiąca");
$chart->setYTitle("Temperatura [°C]");
$chart->drawGraph();
$im = $chart->output();
$points = $chart->getPointData();
header("Content-Type: application/json");
echo json_encode([
    "image" => base64_encode($im),
    "points" => $points
]);