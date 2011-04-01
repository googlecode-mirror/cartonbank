<?php
if (isset($_FILES['file']['tmp_name']))
{
	$imagetype = getimagesize($_FILES['file']['tmp_name']); //previously exif_imagetype()
}
else
{
	$imagetype = '';
}
 
if(isset($_FILES['file']['tmp_name']) && isset($height) && is_numeric($height) && isset($width) && is_numeric($width))
  {
	$image = $wpdb->escape($_FILES['file']['name']);
	$destdir = $imagedir.$image;
  switch($imagetype[2])
    {
    case IMAGETYPE_JPEG:
    //$extension = ".jpg";

			
			 //ales 
//			ini_set('error_reporting', E_ALL); 
//			ini_set('display_errors', TRUE); 
//
//			set_time_limit (100);
//			echo ("max_execution_time=".ini_get('max_execution_time')."<br />");
//			echo (ini_get('memory_limit'));
//			if(ini_set( 'memory_limit', '3000M' ));
//				{
//				//set_time_limit (100);
//				echo ("<br />".ini_get('memory_limit')."<br />");
//				echo ("<br />".ini_get('max_execution_time')."<br />");
//				$src_img = imagecreatefromjpeg($_FILES['file']['tmp_name']);
////				exit ("<b>success<br />");
//				}

				echo ("<br />".ini_get('memory_limit')."<br />");
				echo ("<br />".ini_get('max_execution_time')."<br />");
				$src_img = imagecreatefromjpeg($_FILES['file']['tmp_name']);

			///ales 
	
	$pass_imgtype = true;
    break;

    case IMAGETYPE_GIF:
    //$extension = ".gif";
    $src_img = imagecreatefromgif($_FILES['file']['tmp_name']);
    $pass_imgtype = true;
    break;

    case IMAGETYPE_PNG:
    //$extension = ".png";
    $src_img = imagecreatefrompng($_FILES['file']['tmp_name']);
    imagesavealpha($src_img,true);
    ImageAlphaBlending($src_img, false);
    $pass_imgtype = true;
    break;

    default:
    move_uploaded_file($_FILES['file']['tmp_name'], ($imagedir.$_FILES['file']['name']));
    $pass_imgtype = false;
    break;
    }

  if($pass_imgtype === true)
    {
    $source_w = imagesx($src_img);
    $source_h = imagesy($src_img);




								$xratio = $width/(imagesx($src_img));
								$yratio = $height/(imagesy($src_img));

								if($xratio < 1 || $yratio < 1) {


//									if($xratio < $yratio)	// If image is more wide than hi
//									{
//										$dst_img = imagecreatetruecolor($width, floor(imagesy($src_img) * $xratio));
//									}
//									else
//									{
//										$dst_img = imagecreatetruecolor(floor (imagesx($src_img) * $yratio), $height);
//									}

									switch(strtolower($path["extension"])){
										case "jpeg":
										case "jpg":
											imagejpeg($dst_img);
											break;
										case "gif":
											imagegif($dst_img);
											break;
										case "png":
											imagepng($dst_img);
											break;
										default:
											break;
									}
									//imagedestroy($dst_img);
								}
								else {
									switch(strtolower($path["extension"])){
										case "jpeg":
										case "jpg":
											imagejpeg($src_img);
											break;
										case "gif":
											imagegif($src_img);
											break;
										case "png":
											imagepng($src_img);
											break;
										default:
											break;
									}
									//imagedestroy($dst_img);
								}




    $dst_img = ImageCreateTrueColor($width,$height);





    if($imagetype[2] == IMAGETYPE_PNG)
      {
      imagesavealpha($dst_img,true);
      ImageAlphaBlending($dst_img, false);
      }

	imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, imagesx($dst_img)+1,imagesy($dst_img)+1,imagesx($src_img),imagesy($src_img));

   // ImageCopyResampled($dst_img,$src_img,0,0,0,0,$width,$height,$source_w,$source_h);
    //exit($destdir);
    switch($imagetype[2])
      {
      case IMAGETYPE_JPEG:
      imagejpeg($dst_img, $destdir, 75);
      break;

      case IMAGETYPE_GIF:
      if(function_exists("ImageGIF"))
        {
        //ImageGIF($dst_img, $imagepath);
		ImageGIF($dst_img,$destdir);
        }
        else
          {
          ImageAlphaBlending($dst_img, false);
          ImagePNG($dst_img, $destdir);
          }
      break;

      case IMAGETYPE_PNG:
      imagepng($dst_img, $destdir);
      break;
      }
    usleep(250000);  //wait 0.25 of of a second to process and save the new image
    }
  }
  elseif (isset($_FILES['file']['tmp_name']))
    {
    move_uploaded_file($_FILES['file']['tmp_name'], ($imagedir.$_FILES['file']['name']));
    $image = $wpdb->escape($_FILES['file']['name']);
    }

?>
