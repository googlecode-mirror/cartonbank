<?php
// $imagepath = watermark($imagepath); 

  $imagetype = getimagesize($imagepath);
  switch($imagetype[2])
    {
    case IMAGETYPE_JPEG:
    //$extension = ".jpg";
    $src_img = imagecreatefromjpeg($imagepath);
    $pass_imgtype = true;
    break;

    case IMAGETYPE_GIF:
    //$extension = ".gif";
    $src_img = imagecreatefromgif($imagepath);
    $pass_imgtype = true;
    break;

    case IMAGETYPE_PNG:
    //$extension = ".png";
    $src_img = imagecreatefrompng($imagepath);
    imagesavealpha($src_img,true);
    ImageAlphaBlending($src_img, false);
    $pass_imgtype = true;
    break;

    default:
    $pass_imgtype = false;
    break;
    }

  if($pass_imgtype === true)
    {
    $source_w = imagesx($src_img);
    $source_h = imagesy($src_img);

    $dst_img = ImageCreateTrueColor($width,$height);
    if($imagetype[2] == IMAGETYPE_PNG)
      {
      imagesavealpha($dst_img,true);
      ImageAlphaBlending($dst_img, false);
      }
      else if($imagetype[2] == IMAGETYPE_GIF)
      {
      ImageAlphaBlending($dst_img, true);
      }

    ImageCopyResampled($dst_img,$src_img,0,0,0,0,$width,$height,$source_w,$source_h);
    header("Content-type: image/png");
    ImagePNG($dst_img);
    exit();
    }

// ales
function watermark($path)
{
	// this script creates a watermarked image from an image file - can be a .jpg .gif or .png file
	// where watermark.gif is a mostly transparent gif image with the watermark - goes in the same directory as this script
	// where this script is named watermark.php
	// call this script with an image tag
	// <img src="watermark.php?path=imagepath"> where path is a relative path such as subdirectory/image.jpg
	//$imagesource =  $_GET['path'];
	$imagesource =  $path;
	$filetype = substr($imagesource,strlen($imagesource)-4,4);
	$filetype = strtolower($filetype);
	if($filetype == ".gif")  $image = @imagecreatefromgif($imagesource);  
	if($filetype == ".jpg")  $image = @imagecreatefromjpeg($imagesource);  
	if($filetype == ".png")  $image = @imagecreatefrompng($imagesource);  
	if (!$image) die();
	$watermark = @imagecreatefromgif($basepath.'/wp-content/plugins/wp-shopping-cart/images/watermark.gif');
	$imagewidth = imagesx($image);
	$imageheight = imagesy($image);  
	$watermarkwidth =  imagesx($watermark);
	$watermarkheight =  imagesy($watermark);
	$startwidth = (($imagewidth - $watermarkwidth)/2);
	$startheight = (($imageheight - $watermarkheight)/2);
	imagecopy($image, $watermark,  $startwidth, $startheight, 0, 0, $watermarkwidth, $watermarkheight);
	imagejpeg($image);
	imagedestroy($image);
	imagedestroy($watermark);
}

///ales

?>
