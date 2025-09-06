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

    public function __construct($width = 500, $height = 500) {
        $this->width = $width;
        $this->height = $height;
        $this->image = imagecreatetruecolor($width, $height);
        $this->backgroundColor = imagecolorallocate($this->image, 255, 255, 255);
        $this->lineColor = imagecolorallocate($this->image, 0, 0, 0);
        $this->textColor = imagecolorallocate($this->image, 0, 0, 0);
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

    private function drawLine($x1, $y1, $x2, $y2) {
        imageline($this->image, $x1, $y1, $x2, $y2, $this->lineColor);
    }

    private function drawTextVertically($text, $x, $y) {
        imagestringup($this->image, 7, $x, $y, $text, $this->textColor);
    }

    private function drawText($text, $x, $y) {
        imagestring($this->image, 7, $x, $y, $text, $this->textColor);
    }

    public function generate() {
        // Placeholder for future implementation
    }

    public function drawGraph() {
        $graphOriginX = 50;
        $graphOriginY = $this->height - 50;
        $graphWidth = $this->width - 100;
        $graphHeight = $this->height - 100;

        $this->drawText($this->xTitle, $graphOriginX + $graphWidth / 2, $this->height - 30);
        $this->drawTextVertically($this->yTitle, 20, $graphOriginY - $graphHeight / 2);

        $this->drawLine($graphOriginX, $graphOriginY, $graphOriginX + $graphWidth, $graphOriginY);
        $this->drawLine($graphOriginX, $graphOriginY, $graphOriginX, $graphOriginY - $graphHeight);
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