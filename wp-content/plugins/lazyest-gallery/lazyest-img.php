<?php
require_once('../../../wp-config.php');

generateImg($_GET['file'], $_GET['thumb']);

function generateImg($img, $thumb) {

	$gallery_root = ABSPATH.get_settings('lg_gallery_folder');

	// this will prevent some unshown thumb
	$mem = get_option('lg_buffer_size');
	ini_set("memory_limit", $mem);

	if($thumb) {
		$height = get_option('lg_thumbheight');
		$width = get_option('lg_thumbwidth');
	}
	else {
		$height = get_option('lg_pictheight');
		$width = get_option('lg_pictwidth');
	}
	$img = $gallery_root.$img;
	$path = pathinfo($img);

	switch(strtolower($path["extension"])){
		case "jpeg":
		case "jpg":
			Header("Content-type: image/jpeg");
			$img = imagecreatefromjpeg($img);
			break;
		case "gif":
			Header("Content-type: image/gif");
			$img = imagecreatefromgif($img);
			break;
		case "png":
			Header("Content-type: image/png");
			$img = imagecreatefrompng($img);
			break;
		default:
			break;
	}

	$xratio = $width/(imagesx($img));
	$yratio = $height/(imagesy($img));

	if($xratio < 1 || $yratio < 1) {
		if($xratio < $yratio)	// If image is more wide than hi
			$resized = imagecreatetruecolor($width, floor(imagesy($img) * $xratio));
		else
			$resized = imagecreatetruecolor(floor (imagesx($img) * $yratio), $height);

		imagecopyresampled($resized, $img, 0, 0, 0, 0, imagesx($resized)+1,imagesy($resized)+1,imagesx($img),imagesy($img));

		switch(strtolower($path["extension"])){
			case "jpeg":
			case "jpg":
				imagejpeg($resized);
				break;
			case "gif":
				imagegif($resized);
				break;
			case "png":
				imagepng($resized);
				break;
			default:
				break;
		}

		imagedestroy($resized);
	}
	else {
		switch(strtolower($path["extension"])){
			case "jpeg":
			case "jpg":
				imagejpeg($img);
				break;
			case "gif":
				imagegif($img);
				break;
			case "png":
				imagepng($img);
				break;
			default:
				break;
		}

		//imagejpeg($img);
		imagedestroy($resized);
	}


	imagedestroy($img);
}
?>
