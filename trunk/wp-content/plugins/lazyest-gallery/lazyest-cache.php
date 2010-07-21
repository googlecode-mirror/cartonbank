<?php
/*
 * This file menages the cache system
 */

function createCache($thatdir, $file, $thumb) {
	global $gallery_root;

	if (strlen($thatdir) == 0)
		return;

	// this will prevent some unshown thumb
	$mem = get_option('lg_buffer_size');
	ini_set("memory_limit", $mem);

	// View that $gallery_root already comes with a final "/"
	// We remove the $currentdir the beginning one.
	if (substr($thatdir, 0, 1) == "/")
		$thatdir = substr($thatdir, 1, strlen($thatdir));

	$resample_quality = get_option('lg_resample_quality');
	if (strlen($resample_quality) == 0) {
		$resample_quality = 85;
	}

	// Thumbs (true) or Slides (false)
	if ($thumb){
		$ifolder = get_option('lg_thumb_folder');
		$chheight = get_option('lg_thumbheight');
		$chwidth = get_option('lg_thumbwidth');
	} else {
		$ifolder = get_option('lg_slide_folder');
		$chheight = get_option('lg_pictheight');
		$chwidth = get_option('lg_pictwidth');
	}

	// Removing trailing "/"
	$ifolder = substr($ifolder, 0, strlen($ifolder) -1);

	// This portion attempt to create the proper folder (slides, thumbs)
	if(!file_exists($gallery_root.$thatdir.$ifolder)){
		if (is_writable($gallery_root.$thatdir)){
			mkdir($gallery_root.$thatdir.$ifolder, 0777);
		}
		else {
			echo "<div class='error'><b>WARNING:</b> Unable to create $ifolder folder in $thatdir. <br />";
			echo "Check your permissions.</div>";
		}
	}

	// Check for cropping thumbs
	if (get_option('lg_use_cropping') == "TRUE" && $thumb){
		create_cropped_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality);
	} else {
		create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file, $thumb, $resample_quality);
	}

}

// Cropped thumbs creation (contributed by: dodo - http://pure-essence.net)
function create_cropped_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality) {
	global $gallery_root;

	$img_location = $gallery_root.$thatdir.$file;
	// Getting width ([0]) and height ([1]) maybe add options
   $size_bits = getimagesize($img_location);

//   if ($size_bits[0]>1600 or $size_bits[0]>1600)
//	{
//	   return false;
//	}

	// Creating a resource image
	$path = pathinfo($img_location);

	switch(strtolower($path["extension"])){
		case "jpeg":
		case "jpg":
			$img = imagecreatefromjpeg($img_location);
			break;
		case "gif":
			$img = imagecreatefromgif($img_location);
			break;
		case "png":
			$img = imagecreatefrompng($img_location);
			break;
		default:
			break;
	}

	if($size_bits[0] > $chwidth || $size_bits[1] > $chheight) {

		// Resize the image
		$resized = imagecreatetruecolor($chwidth, $chheight);

		$o_width = $size_bits[0];
		$o_height = $size_bits[1];

		// if the image is more wide than high
		if($o_width > $o_height) {
			// landscape image
			$out_width = $o_height;
			$out_height = $o_height;
			$cutoff = round(($o_width - $o_height) / 2);

			$out_left = $cutoff;
			$out_top = 0;
		} else {
			$cutoff = round(($o_height - $o_width) / 2);

			$out_width = $o_width;
			$out_height = $o_width;

			$out_left = 0;
			$out_top = $cutoff;
		}

		// Resampling the image
		imagecopyresampled ($resized, $img, 0, 0, $out_left, $out_top, $chwidth, $chheight, $out_width, $out_height);

		if (is_writable($gallery_root.$thatdir.$ifolder)){
			switch(strtolower($path["extension"])){
				case "jpeg":
				case "jpg":
					imagejpeg($resized, $gallery_root.$thatdir.$ifolder.'/'.$file, $resample_quality);
					break;
				case "gif":
					imagegif($resized, $gallery_root.$thatdir.$ifolder.'/'.$file);
					break;
				case "png":
					imagepng($resized, $gallery_root.$thatdir.$ifolder.'/'.$file);
					break;
				default:
					break;
			}
		} else {
			echo "<div class='error'><b>WARNING:</b> Unable to create $ifolder inside $thatdir. <br />";
			echo "Check your permissions.</div><br />";
		}

		imagedestroy($resized);
	} else {
		switch(strtolower($path["extension"])){
			case "jpeg":
			case "jpg":
				imagejpeg($img, $gallery_root.$thatdir.$ifolder.'/'.$file, $resample_quality);
				break;
			case "gif":
				imagegif($img, $gallery_root.$thatdir.$ifolder.'/'.$file);
				break;
			case "png":
				imagepng($img, $gallery_root.$thatdir.$ifolder.'/'.$file);
				break;
			default:
				break;
		}
	}
	imagedestroy($img);
	if (file_exists($gallery_root.$thatdir.$ifolder.'/'.$file)) {
		@chmod($gallery_root.$thatdir.$ifolder, 0777);
		@chmod($gallery_root.$thatdir.$ifolder.'/'.$file, 0666);
	}
}

// Default thumbs creation
function create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file, $thumb, $resample_quality) {
	global $gallery_root;

	$img_location = $gallery_root.$thatdir.$file;

	// Creating a resource image
	$path = pathinfo($img_location);

	switch(strtolower($path["extension"])){
		case "jpeg":
		case "jpg":
			$img = imagecreatefromjpeg($img_location);
			break;
		case "gif":
			$img = imagecreatefromgif($img_location);
			break;
		case "png":
			$img = imagecreatefrompng($img_location);
			break;
		default:
			break;
	}

	$xratio = $chheight/(imagesx($img));
	$yratio = $chwidth/(imagesy($img));

	if($xratio < 1 || $yratio < 1) {
		if($xratio < $yratio)
			$resized = imagecreatetruecolor($chwidth,floor(imagesy($img)*$xratio));
		else
			$resized = imagecreatetruecolor(floor(imagesx($img)*$yratio), $chheight);

		imagecopyresampled($resized, $img, 0, 0, 0, 0, imagesx($resized)+1,imagesy($resized)+1,imagesx($img),imagesy($img));

		if (is_writable($gallery_root.$thatdir.$ifolder)){
			switch(strtolower($path["extension"])){
				case "jpeg":
				case "jpg":
					imagejpeg($resized, $gallery_root.$thatdir.$ifolder.'/'.$file, $resample_quality);
					break;
				case "gif":
					imagegif($resized, $gallery_root.$thatdir.$ifolder.'/'.$file);
					break;
				case "png":
					imagepng($resized, $gallery_root.$thatdir.$ifolder.'/'.$file);
					break;
				default:
					break;
			}
		}
		else {
			echo "<div class='error'><b>WARNING:</b> Unable to create images inside $ifolder. <br />";
			echo "Check your permissions.</div>";
		}

		imagedestroy($resized);

	} else {
		switch(strtolower($path["extension"])){
			case "jpeg":
			case "jpg":
				imagejpeg($img, $gallery_root.$thatdir.$ifolder.'/'.$file, $resample_quality);
				break;
			case "gif":
				imagegif($img, $gallery_root.$thatdir.$ifolder.'/'.$file);
				break;
			case "png":
				imagepng($img, $gallery_root.$thatdir.$ifolder.'/'.$file);
				break;
			default:
				break;
		}
	}

	imagedestroy($img);

	if (file_exists($gallery_root.$thatdir.$ifolder.'/'.$file)) {
		@chmod($gallery_root.$thatdir.$ifolder, 0777);
		@chmod($gallery_root.$thatdir.$ifolder.'/'.$file, 0666);
	}
}
?>