<?php
header('Content-Type: image/png');
$im = imagecreatetruecolor(500, 500);
$r = imagecolorallocate($im, 255, 0, 0);
$b = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im, 0, 0, 500, 500, $r);
imagestringup($im, 7, 200, 200, "Ala miała kota miau", $b);
imagestringup($im, 7, 200, 200, "Ale Ala zgłodniała" , $b);
$arr = [$b, $b, $b, $r, $r, $r];
imagesetstyle($im, $arr);
imageline($im, 0, 100, 500, 100, $b);

imageline($im, 0, 200, 500, 200, IMG_COLOR_STYLED);
imagesetstyle($im, $arr);

imageline($im, 0, 202, 500, 202, IMG_COLOR_STYLED);
imagesetstyle($im, $arr);

imageline($im, 0, 204, 500, 204, IMG_COLOR_STYLED);

imagepng($im);
?>