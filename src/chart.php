<?php

class Chart {
    private $width;
    private $height;
    private $image;
    private $backgroundColor;
    private $lineColor;
    private $textColor;
    private $lineStyle;
    private $xTitle;
    private $yTitle;
    private $xScale;
    private $yScale;
    private $data;
    private $graphOriginX;
    private $graphOriginY;
    private $graphWidth;
    private $graphHeight;
    private $fontWidth;
    private $spacingX;
    private $spacingY;
    private $maxYValue;
    private $minYValue;
    private $numYDivisions = 7;
    private $pointData = [];

    public function __construct($width = 500, $height = 500) {
        $this->width = $width;
        $this->height = $height;
        $this->image = imagecreatetruecolor($width, $height);
        $this->backgroundColor = imagecolorallocate($this->image, 255, 255, 255);
        $this->lineColor = imagecolorallocate($this->image, 0, 0, 0);
        $this->textColor = imagecolorallocate($this->image, 0, 0, 0);
        $this->graphOriginX = 100;
        $this->graphOriginY = $height - 60;
        $this->graphWidth = $width - 150;
        $this->graphHeight = $height - 100;
        $this->data = [];
        $this->fontWidth = imagefontwidth(7);
        imagefilledrectangle($this->image, 0, 0, $width, $height, $this->backgroundColor);
    }

    public function setXTitle($title) {
        $this->xTitle = $title;
    }

    public function setYTitle($title) {
        $this->yTitle = $title;
    }

    public function setLineStyle($style) {
        $this->lineStyle = $style;
    }

    private function drawLine($x1, $y1, $x2, $y2, $style = null) {
        if ($style) {
            imagesetstyle($this->image, $style);
            imageline($this->image, $x1, $y1, $x2, $y2, IMG_COLOR_STYLED);
        } else {
            imageline($this->image, $x1, $y1, $x2, $y2, $this->lineColor);
        }
    }

    private function drawTextVertically($text, $x, $y) {
        imagestringup($this->image, 7, $x, $y, $text, $this->textColor);
    }

    private function drawText($text, $x, $y) {
        imagestring($this->image, 7, $x, $y, $text, $this->textColor);
    }

    public function drawGraph() {
        $this->drawText($this->xTitle, ($this->graphOriginX + $this->graphWidth / 2) - ($this->fontWidth * strlen($this->xTitle) / 2), $this->height - 30);
        $this->drawTextVertically($this->yTitle, 20, ($this->graphOriginY - $this->graphHeight / 2) + ($this->fontWidth * strlen($this->yTitle) / 2));

        $this->drawLine($this->graphOriginX, $this->graphOriginY, $this->graphOriginX + $this->graphWidth, $this->graphOriginY);
        $this->drawLine($this->graphOriginX, $this->graphOriginY, $this->graphOriginX, $this->graphOriginY - $this->graphHeight);
        $this->fetchData();
        $this->drawScaleLines();
        $this->drawGridLines();
        $this->plotData();
    }

    public function drawScaleLines() {
        for ($i = 0; $i < count($this->data); $i++) {
            $x = $this->graphOriginX + (($i + 1) * $this->spacingX);
            $this->drawLine($x, $this->graphOriginY + 5, $x, $this->graphOriginY - 5);
            if (isset($this->data[$i]['day_of_month'])) {
                $this->drawText($this->data[$i]['day_of_month'], $x - ($this->fontWidth * strlen($this->data[$i]['day_of_month']) / 2), $this->graphOriginY + 5);
            }
        }

        $valueIncrement = ($this->maxYValue - $this->minYValue) / ($this->numYDivisions - 1);
        for ($i = 0; $i < $this->numYDivisions; $i++) {
            $y = $this->graphOriginY - (($i + 1) * $this->spacingY);
            $this->drawLine($this->graphOriginX - 5, $y, $this->graphOriginX + 5, $y);
            $value = round($this->minYValue + ($i * $valueIncrement), 2);
            $this->drawText($value, $this->graphOriginX - ($this->fontWidth * strlen($value)) - 5, $y - ($this->fontWidth / 2));
        }
    }

    private function drawGridLines() {
        $black = imagecolorallocate($this->image, 0, 0, 0);
        $white = imagecolorallocate($this->image, 255, 255, 255);
        $dotted = [$black, $black, $black, $black, $white, $white, $white, $white];

        for ($i = 1; $i <= $this->numYDivisions; $i++) {
            $y = $this->graphOriginY - ($i * $this->spacingY);
            $this->drawLine($this->graphOriginX, $y, $this->graphOriginX + $this->graphWidth, $y, $dotted);
        }

        for ($i = 1; $i <= count($this->data); $i++) {
            $x = $this->graphOriginX + ($i * $this->spacingX);
            $this->drawLine($x, $this->graphOriginY, $x, $this->graphOriginY - $this->graphHeight, $dotted);
        }
    }

    public function getPointData() {
        return $this->pointData;
    }

    public function fetchData() {
        $conn = new mysqli("mysql", "bozgi", "hujgnuj", "db");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $stmt = $conn->prepare("SELECT * FROM measurements ORDER BY day_of_month ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $this->data[] = $row;
        }
        $stmt->close();
        $this->maxYValue = max(array_column($this->data, 'temperature_c'));
        $this->minYValue = min(array_filter(array_column($this->data, 'temperature_c')));
        $dataLength = count($this->data);
        $this->spacingX = $this->graphWidth / ($dataLength);
        $this->spacingY = $this->graphHeight / $this->numYDivisions;

        // echo '<pre>'; print_r($this->data); echo '</pre>';
    }

    public function plotData() {
        for ($i = 0; $i <= count($this->data) - 1; $i++) {
            if (!isset($this->data[$i]['temperature_c'])) {
                if ($this->data[$i]['status'] == 'n/a') {
                    $this->lineColor = imagecolorallocate($this->image, 107, 107, 107);
                } else {
                    $this->lineColor = imagecolorallocate($this->image, 255, 0, 0);
                }

                imagefilledarc($this->image,
                    $this->graphOriginX + (($i + 1) * $this->spacingX),
                    $this->graphOriginY,
                    10, 10, 0, 360, $this->lineColor, IMG_ARC_PIE);
                
                $this->pointData[] = [
                    'x' => $this->graphOriginX + (($i + 1) * $this->spacingX),
                    'y' => $this->graphOriginY,
                    'day_of_month' => $this->data[$i]['day_of_month'],
                    'temperature_c' => $this->data[$i]['temperature_c'],
                    'status' => $this->data[$i]['status']
                ];
            }
            if (!isset($this->data[$i]['temperature_c']) || !isset($this->data[$i + 1]['temperature_c'])) {
                continue;
            }
            $this->lineColor = imagecolorallocate($this->image, 0, 0, 255);
            $x1 = $this->graphOriginX + (($i + 1) * $this->spacingX);
            $y1 = $this->graphOriginY - (($this->data[$i]['temperature_c'] - $this->minYValue) * (($this->graphHeight - $this->spacingY) / ($this->maxYValue - $this->minYValue)) + $this->spacingY);

            $x2 = $this->graphOriginX + (($i + 2) * $this->spacingX);
            $y2 = $this->graphOriginY - (($this->data[$i + 1]['temperature_c'] - $this->minYValue) * (($this->graphHeight - $this->spacingY) / ($this->maxYValue - $this->minYValue)) + $this->spacingY);

            imagefilledarc($this->image,
                $x1,
                $y1,
                10, 10, 0, 360, $this->lineColor, IMG_ARC_PIE);
            imagefilledarc($this->image,
                $x2,
                $y2,
                10, 10, 0, 360, $this->lineColor, IMG_ARC_PIE);

            $this->pointData[] = [
                'x' => $x1,
                'y' => $y1,
                'day_of_month' => $this->data[$i]['day_of_month'],
                'temperature_c' => $this->data[$i]['temperature_c'],
                'status' => $this->data[$i]['status']
            ];
            $this->drawLine($x1, $y1, $x2, $y2, $this->lineStyle);
        }
    }

    public function output() {
        ob_start();
        imagepng($this->image);
        return ob_get_clean();
    }

    public function __destruct() {
        imagedestroy($this->image);
    }
}   