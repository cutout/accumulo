<?php

	// settings
	$bgs = array(
		'captcha-1.png',
		'captcha-2.png',
		'captcha-3.png',
		'captcha-4.png',
		'captcha-5.png',
	);
	
	shuffle($bgs);
	
	$font_size = 5;
	$img_w = 70;
	$img_h = 22;

	$key_num = rand (0, 12);
	$hash_string = substr (str_shuffle ('abcdefghjkmnpqrtwxy13467891346789-+='), $key_num, 5);
	$hash_md5 = md5 ($hash_string);

	session_start ();
	$_SESSION['captcha'] = $hash_md5;

	header ('Content-Type: image/png');

	$img_handle = imagecreatefrompng ('captcha-images/' . $bgs[0]);
	$text_colour = imagecolorallocate ($img_handle, 255, 255, 255);
	$text_shadow_colour = imagecolorallocate ($img_handle, 0, 0, 0);
	$horiz = round (($img_w / 2) - ((strlen ($hash_string) * imagefontwidth (5)) / 2), 1);
	$vert = round (($img_h / 2) - (imagefontheight ($font_size) / 2));

	imagestring ($img_handle, $font_size, $horiz, $vert + 1, $hash_string, $text_shadow_colour);
	imagestring ($img_handle, $font_size, $horiz, $vert - 1, $hash_string, $text_shadow_colour);
	imagestring ($img_handle, $font_size, $horiz, $vert, $hash_string, $text_colour);
	imagepng ($img_handle);
	imagedestroy ($img_handle);

?>