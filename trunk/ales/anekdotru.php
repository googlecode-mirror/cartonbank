<?php
//
// send top 7($howmanyimages?) rating images to anekdot.ru
//

// configuration
$howmanyimages = 7;

include("config.php");

$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);

$sql = "SELECT	post as ID, 
				wp_product_list.image as image, 
				wp_product_list.name AS title, 
				wp_product_brands.name AS author, 
				COUNT(*) AS votes, 
				SUM(wp_fsr_user.points) AS points, 
				AVG(wp_fsr_user.points)*SQRT(COUNT(*)) AS average,
				wp_product_files.idhash,
				wp_product_files.mimetype
					FROM wp_fsr_user,  wp_fsr_post, wp_product_list, wp_product_brands, wp_product_files 
					WHERE wp_fsr_user.post = wp_product_list.id 
					AND wp_fsr_user.post =  wp_fsr_post.ID 
					AND wp_product_list.file = wp_product_files.id
					AND wp_product_list.brand = wp_product_brands.id 
					AND wp_product_list.active = 1
					AND wp_product_list.visible = 1
					AND wp_fsr_post.anekdotru_date is NULL
					GROUP BY 1
					ORDER BY 7 DESC, 5 DESC
					LIMIT ".$howmanyimages;

$result = mysql_query("$sql");

if (!$result) {die('Invalid query: ' . mysql_error());}

//
$count=mysql_num_rows($result);
//

		
		while($row=mysql_fetch_array($result))
		{
				$ID = $row['ID'];
				$image = $row['image'];
				$title = $row['title'];
				$author = $row['author'];
				$votes = $row['votes'];
				$points = $row['points'];
				$average = $row['average'];
				$vote_date = $row['vote_date'];
				$idhash = $row['idhash'];
				$mimetype = $row['mimetype'];
				$extension = '';

				// get extension of the source file
				$fileType = strtolower(substr($mimetype, strlen($mimetype)-3));

				switch($fileType) {
				   case('gif'):
					   $extension = 'gif';
					   break;
					  
				   case('png'):
					   $extension = 'png';;
					   break;
					  
				   default:
					   $extension = 'jpg';;
				}

				// echo the name of the image
				echo "<br><br><font color='#FF00FF'><b>$count:</b> </font>".$ID." <img src='http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/images/".$image."' width='40'> <b>&quot;".$title."&quot;</b> ".$author." рейт: ".$average."<br>";

				//image 
				$filename = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/product_images/".$image;

				//resize the image
					$chwidth=500;
					$chheight=500;
					$thatdir='';
					$ifolder='';
					$file = $filename.'.'.$extension; 
					$idhash_path = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/files/".$idhash;
					$product_images='';
					$slidename = $author.'_'.$idhash.'.'.$extension;
					$thumb='';
					$resample_quality=100;

					//create resized image
					al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file, $idhash_path, $product_images, $slidename, $thumb, $resample_quality = '100');

				// Add logo
					$export_dir = "/home/www/cb3/ales/";

					if(file_exists($export_dir.$slidename))
					{
						wtrmark($export_dir.$slidename,$wm,$author);
						//echo "\n\r>>>> watermarked";
					}

				// Mark image as sent to the Anekdot.ru
												$update_sql = "update wp_fsr_post set anekdotru_date='".date("d.m.y H:m:s")."' where ID=".$ID;
												$res = mysql_query($update_sql);
													if (!$res) {die('<br>'.$update_sql.'<br>Invalid delete query: ' . mysql_error());}
													//echo ("<br>".$update_sql."<br>");
				
				//send email
					// To send HTML mail, the Content-type header must be set
												$headers  = 'MIME-Version: 1.0' . "\r\n";
												$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
												$headers .= 'From: CartoonBank Robot <cartoonbank.ru@gmail.com>' . "\r\n";

					//email content
												$content = "Автор: ".$author."\n\r";
												$content .= "Название: ".$title."\n\r";
												$content .= "Ссылка: http://cartoonbank.ru/?page_id=29&cartoonid=".$ID."\n\r";


					$my_file = $slidename;

					$my_path = $_SERVER['DOCUMENT_ROOT']."ales/";
					//echo "<br>my_path: ".$my_path."<br>";

					$my_name = "cartoonbank";
					$my_mail = "cartoonbank.ru@gmail.com";
					$my_replyto = "cartoonbank.ru@gmail.com";
					$my_subject = "ежедневная карикатура для анекдота.ру от картунбанка.ру";
					$my_message = $content;

					//send email 2
					mail_attachment($my_file, $my_path, $my_mail, $my_mail, $my_name, $my_replyto, $my_subject, $my_message);

				$count=$count-1;

				if(file_exists($export_dir.$slidename))
					{
						unlink($export_dir.$slidename);
						//echo "\n\r>>>> slide removed";
					}


		}

function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
    $file = $path.$filename;
    $file_size = filesize($file);
    $handle = fopen($file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));
    $uid = md5(uniqid(time()));
    $name = basename($file);
    $header = "From: ".$from_name." <".$from_mail.">\r\n";
    $header .= "Reply-To: ".$replyto."\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $header .= $message."\r\n\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
    $header .= "Content-Transfer-Encoding: base64\r\n";
    $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
    $header .= $content."\r\n\r\n";
    $header .= "--".$uid."--";
    if (mail($mailto, $subject, "", $header)) {
        echo "mail send ... OK"; // or use booleans here
    } else {
        echo "mail send ... ERROR!";
    }
}

function wtrmark($sourcefile, $watermarkfile, $text) {

		$logopath = "/home/www/cb3/img/cb-logo-300.png";
		$logofile_id = imagecreatefrompng($logopath);

		imageAlphaBlending($logofile_id, true);
		imageSaveAlpha($logofile_id, true);
	   
	   $fileType = strtolower(substr($sourcefile, strlen($sourcefile)-3));

	   switch($fileType) {
		   case('gif'):
			   $sourcefile_id = imagecreatefromgif($sourcefile);
			   break;
			  
		   case('png'):
			   $sourcefile_id = imagecreatefrompng($sourcefile);
			   break;
			  
		   default:
			   $sourcefile_id = imagecreatefromjpeg($sourcefile);
	   }
		  imageAlphaBlending($sourcefile_id, true);
		  imageSaveAlpha($sourcefile_id, true);


	   //Get the sizes of both pix 
	  $sourcefile_width=imageSX($sourcefile_id);
	  $sourcefile_height=imageSY($sourcefile_id);
	  $logo_width=imageSX($logofile_id);
	  $logo_height=imageSY($logofile_id);

	   $dest_x_logo = $sourcefile_width - $logo_width - 4;
	   $dest_y_logo = $sourcefile_height - $logo_height - 8;
	  
	  
	   // if a gif, we have to upsample it to a truecolor image
	   if($fileType == 'gif') {
		   // create an empty truecolor container
		   $tempimage = imagecreatetruecolor($sourcefile_width,$sourcefile_height);
		  
		   // copy the 8-bit gif into the truecolor image
		   imagecopy($tempimage, $sourcefile_id, 0, 0, 0, 0,
							   $sourcefile_width, $sourcefile_height);
		  
		   // copy the source_id int
		   $sourcefile_id = $tempimage;
	   }

	//text
	$black = ImageColorAllocate($sourcefile_id, 0, 0, 0); 
	$white = ImageColorAllocate($sourcefile_id, 255, 255, 255); 

	//The canvas's (0,0) position is the upper left corner 
	//So this is how far down and to the right the text should start 
	$start_x = $sourcefile_width - 295;//10; 
	$start_y = $sourcefile_height - 4; //20; 

	// write text
	// write white twice
	Imagettftext($sourcefile_id, 10, 0, $start_x-1, $start_y-1, $white, '/home/www/cb3/ales/arial.ttf', $text); 
	Imagettftext($sourcefile_id, 10, 0, $start_x+1, $start_y+1, $white, '/home/www/cb3/ales/arial.ttf', $text); 
	Imagettftext($sourcefile_id, 10, 0, $start_x-1, $start_y+1, $white, '/home/www/cb3/ales/arial.ttf', $text); 
	Imagettftext($sourcefile_id, 10, 0, $start_x+1, $start_y-1, $white, '/home/www/cb3/ales/arial.ttf', $text); 

	//write black over
	Imagettftext($sourcefile_id, 10, 0, $start_x, $start_y, $black, '/home/www/cb3/ales/arial.ttf', $text); 


	$opacity_logo = 30;
	ImageCopyMerge($sourcefile_id, $logofile_id, $dest_x_logo, $dest_y_logo, 0, 0, $logo_width, $logo_height, $opacity_logo);
						   


	   //Create a jpeg out of the modified picture 
	   switch($fileType) {
	  
		   // remember we don't need gif any more, so we use only png or jpeg.
		   // See the upsaple code immediately above to see how we handle gifs
		   case('png'):
			   imagepng ($sourcefile_id,$sourcefile);
			   break;
			  
		   default:
			   imagejpeg ($sourcefile_id,$sourcefile);
	   }
	 
	   imagedestroy($sourcefile_id);
	   imagedestroy($logofile_id);
}

function al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file,  $idhash_path, $product_images, $slidename, $thumb, $resample_quality = '100') {

	// Default thumbs creation
	$img_location = $file;
	$export_dir = "/home/www/cb3/ales/";

	// Creating a resource image
	$path = pathinfo($img_location);

	switch(strtolower($path["extension"])){
		case "pjpeg":
		case "jpeg":
		case "jpg":
		try
			{
				$img = imagecreatefromjpeg($idhash_path);
				break;
			}
			catch(Exception $e)
			{
				echo ("\n" .$idhash_path. " bad file");
				echo ("\n exception: " .$e);
				break;
			}
			
		case "gif":
			$img = imagecreatefromgif($idhash_path);
			break;
		case "png":
			$img = imagecreatefrompng($idhash_path);
			break;
		default:
			break;
	}

	$xratio = $chheight/(imagesx($img));
	$yratio = $chwidth/(imagesy($img));

	if($xratio < 1 || $yratio < 1) 
	{
		if($xratio < $yratio)
			$resized = imagecreatetruecolor($chwidth,floor(imagesy($img)*$xratio));
		else
			$resized = imagecreatetruecolor(floor(imagesx($img)*$yratio), $chheight);

		imagecopyresampled($resized, $img, 0, 0, 0, 0, imagesx($resized)+1,imagesy($resized)+1,imagesx($img),imagesy($img));
		
		//echo "\n   ..strtolower(path[extension]):".strtolower($path["extension"]);
			switch(strtolower($path["extension"])){
				case "jpeg":
				case "pjpeg":
				case "jpg":
					//echo "\n\r     Jpg: copy resized image to ".$export_dir.$slidename;
					imagejpeg($resized, $export_dir.$slidename, $resample_quality);
					break;
				case "gif":
					//echo "\n\r     Gif: copy resized image to ".$export_dir.$slidename;
					imagegif($resized, $export_dir.$slidename);
					break;
				case "png":
					imagepng($resized, $export_dir.$slidename);
					break;
				default:
					break;
			}

		imagedestroy($resized);
		imagedestroy($img);

	}
	 else 
	 {
		 echo "\n     !!!!shit";
		switch(strtolower($path["extension"])){
			case "jpeg":
			case "jpg":
				imagejpeg($img, $export_dir.$slidename, $resample_quality);
				break;
			case "gif":
				imagegif($img, $export_dir.$slidename);
				break;
			case "png":
				imagepng($img, $export_dir.$slidename);
				break;
			default:
				break;
		}
		imagedestroy($img);
	}
}
?>