<?php
$newwidth = 500;
$newheight = 500;
$thumb = imagecreatetruecolor($newwidth, $newheight);
imagealphablending($thumb, false);
imagesavealpha($thumb, true);  

$source = imagecreatefrompng("base.png");
imagealphablending($source, true);
list($width, $height) = getimagesize("base.png");

imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

imagepng($thumb,"b2.png");
echo "<img src=\"b2.png\" />";
?>