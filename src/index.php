<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        area {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php
    require 'chart.php';
    if (!isset($_GET["width"]) || !isset($_GET["height"])) {
        $_GET["width"] = 500;
        $_GET["height"] = 500;
    }
    $chart = new Chart($_GET["width"], $_GET["height"]);
    $chart->setXTitle("Dzień miesiąca");
    $chart->setYTitle("Temperatura [°C]");
    $chart->drawGraph();
    $im = $chart->output();
    ?>
    <map name="graph">
        <?php
            $chart->getPointData();
            foreach ($chart->getPointData() as $point) {
                echo '<area shape="circle" coords="'.$point['x'].','
                .$point['y'].',5" alt="'.$point['status'].
                '" title="'.$point['status'].'" data-day="'.$point['day_of_month'].
                '" data-temperature="'.$point['temperature_c'].
                '" data-status="'.$point['status'].
                '">';
            }
        ?>
    </map>
    <img src="data:image/x-icon;base64,<?php echo base64_encode($im); ?>" usemap="#graph"></img>
    <script >
        document.querySelectorAll('area').forEach(item => {
            item.addEventListener('click', event => {
                event.preventDefault();
                console.log(item.dataset);
                
            })
        });
    </script>
</body>
</html>
    