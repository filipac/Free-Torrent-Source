<?php

require_once("include/bittorrent.php");
require_once("include/captcha.php");


$img_width = 300;
$img_height = 120;

// The following settings are only used for TTF fonts
$min_size = 20;
$max_size = 32;

$min_angle = -30;
$max_angle = 30;

if($_GET['imagehash'] == "test") {
$imagestring = "Fts";
} else {
$query = mysql_query("SELECT * FROM regimages WHERE imagehash=".sqlesc($_GET['imagehash'])." LIMIT 1") or sqlerr(__FILE__,__LINE__);
$regimage = mysql_fetch_array($query);
$imagestring = $regimage['imagestring'];
}

$ttf_fonts = array();

// We have support for true-type fonts (FreeType 2)
if(function_exists("imagefttext")) {
// Get a list of the files in the 'catpcha_fonts' directory
$ttfdir = @opendir("include/captcha_fonts");
if($ttfdir) {
while($file = readdir($ttfdir)) {
// If this file is a ttf file, add it to the list
if(is_file("include/captcha_fonts/".$file) && get_extension($file) == "ttf") {
$ttf_fonts[] = "include/captcha_fonts/".$file;
}
}
}
}

// Have one or more TTF fonts in our array, we can use TTF captha's
if(count($ttf_fonts) > 0) {
$use_ttf = 1;
} else {
$use_ttf = 0;
}

// Check for GD >= 2, create base image
if(gd_version() >= 2) {
$im = imagecreatetruecolor($img_width, $img_height);
} else {
$im = imagecreate($img_width, $img_height);
}

// No GD support, die.
if(!$im) {
die("No GD support.");
}

// Fill the background with white
$bg_color = imagecolorallocate($im, 255, 255, 255);
imagefill($im, 0, 0, $bg_color);

// Draw random circles, squares or lines?

draw_lines($im);




// Write the image string to the image
draw_string($im, $imagestring);

// Draw a nice border around the image
$border_color = imagecolorallocate($im, 0, 0, 0);
imagerectangle($im, 0, 0, $img_width-1, $img_height-1, $border_color);

// Output the image
#header("Content-type: image/png");
imagepng($im);
imagedestroy($im);
exit;


?>