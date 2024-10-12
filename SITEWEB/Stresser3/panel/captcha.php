<?php

header("X-XSS-Protection: 1; mode=block");

	session_start();
	$string = "";
	for ($i = 0; $i < 6; $i++)
		$string .= chr(rand(97, 120));
	
	$_SESSION['rand_code'] = $string;

	$dir = "fonts/";

	$image = imagecreatetruecolor(120, 50);
	$black = imagecolorallocate($image, 0, 0, 0);
	$shawty = imagecolorallocate($image, 7, 2, 0);
	$gray = imagecolorallocate($image, 54,64,76);
	$color = imagecolorallocate($image, 200, 100, 90);
	$white = imagecolorallocate($image, 255, 255, 255);
	$red = imagecolorallocate($image, 225, 74, 84);
	$rev = imagecolorallocate($image, 121, 126, 166);

	imagefilledrectangle($image,0,0,399,99,$white);
for($i=0;$i<7;$i++) {
    imageline($image,0,rand()%50,300,rand()%50,$rev);
}
	for($i=0;$i<1000;$i++) {
    imagesetpixel($image,rand()%200,rand()%50,$rev);
}  
	imagettftext ($image, 23, 0, 10, 35, $shawty, $dir."atyp.ttf", $_SESSION['rand_code']);

	header("Content-type: image/png");
	imagepng($image);
?>