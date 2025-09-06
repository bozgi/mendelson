<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    require 'chart.php';
    if (!isset($_GET["width"]) || !isset($_GET["height"])) {
        $_GET["width"] = 500;
        $_GET["height"] = 500;
    }
    $chart = new Chart($_GET["width"], $_GET["height"]);
    $chart->setXTitle("X Axis");
    $chart->setYTitle("Y Axis");
    $chart->drawGraph();
    $im = $chart->output();
    $chart->fetchData();
    ?>
    <img src="data:image/x-icon;base64,<?php echo base64_encode($im); ?>"></img>
</body>
</html>
    