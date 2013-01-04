<?
// fill form and submit to anekdot.ru
//
// send top 7($howmanyemails?) rating images to anekdot.ru
//

// configuration
include("config.php");

$howmanyrows =400; // how many rows to select from database
$howmanyemails = 9; // how many images to send (desired number plus number of ignored artists)
$url = 'http://www.anekdot.ru/scripts/upload.php?t=e'; // destination url with form;
//$url = 'http://cartoonbank.ru/ales/anekdotru_form_accept.php'; // destination url with form;
$mailto = 'igor.aleshin@gmail.com'; // destination email box
//$mailto = 'cartoonbank.ru@gmail.com'; // destination email box 
$mailto1 = "verner@anekdot.ru"; // destination email box 
//$mailto2 = "Kalininskiy@yandex.ru"; // destination email box 



//open db connection
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
	wp_product_list.brand AS brand,
	wp_product_files.mimetype
		FROM wp_fsr_user,  wp_fsr_post, wp_product_list, wp_product_brands, wp_product_files 
		WHERE wp_fsr_user.post = wp_product_list.id 
		AND wp_fsr_user.post =  wp_fsr_post.ID 
		AND wp_product_list.file = wp_product_files.id
		AND wp_product_list.brand = wp_product_brands.id 
		AND wp_product_list.brand != 3 
		AND wp_product_list.brand != 13 
		AND wp_product_list.active = 1
		AND wp_product_list.visible = 1
		AND wp_fsr_post.anekdotru_date is NULL
		GROUP BY 1
		ORDER BY 7 DESC, 5 DESC
		LIMIT ".$howmanyrows;

$result = mysql_query("$sql");

if (!$result) {die('Invalid query: ' . mysql_error());}

//
$count=mysql_num_rows($result);
//


// exclude artist:
$arrAuthors = array('Светозаров Георгий','Александров Василий');
		
		while($row=mysql_fetch_array($result))
		{
				$ID = $row['ID'];
				$image = $row['image'];
				$title = $row['title'];
				$author = $row['author'];
				$brand = $row['brand'];
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
					   $extension = 'png';
					   break;
					  
				   default:
					   $extension = 'jpg';
				}

				// echo the name of the image
				if (!in_array($author, $arrAuthors))
				{
					//echo "<br />\r\n<font color='#FF00FF'><b>".count($arrAuthors).":</b> </font>".$ID." <img src='http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/images/".$image."' width='40'> <b>&quot;".$title."&quot;</b> ".$author." рейт: ".$average."\n\r<br />";

					array_push($arrAuthors, $author);

				


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
					//$slidename = $author.'_'.$idhash.'.'.$extension;
					$slidename = $idhash.'.'.$extension;
					$thumb='';
					$resample_quality=100;

					//create resized image
					al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file, $idhash_path, $product_images, $slidename, $thumb, $resample_quality = '100');

				// Add logo
					$export_dir = "/home/www/cb3/ales/";

					if(file_exists($export_dir.$slidename))
					{
						wtrmark($export_dir.$slidename,$wm,$author.' © cartoonbank.ru');
					}

				//send email
					// To send HTML mail, the Content-type header must be set
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
						$headers .= 'From: CartoonBank Robot <cartoonbank.ru@gmail.com>' . "\r\n";

					//email content
					$_link = "http://cartoonbank.ru/?page_id=29&amp;brand=".$brand;

						$content = "Автор: ".$author."\n\r";
						$content .= "Название: ".$title."\n\r";
						$content .= $_link."\n\r";
						$content .= "Код ссылки на страницу автора: <a href='".$_link."'>".$author."</a>\n\r";



					$my_file = $slidename;

					$my_path = $_SERVER['DOCUMENT_ROOT']."ales/";
					$my_name = "cartoonbank";
					$my_mail = "cartoonbank.ru@gmail.com";
					$my_replyto = "cartoonbank.ru@gmail.com";
					$my_subject = "ежедневная карикатура для анекдота.ру от картунбанка.ру";
					$my_message = $content;

	// send data to remote form
	send_form($url,$title,$author,$my_mail,$_link,$my_file);

				$count=$count-1;

					//send email
					// self
					mail_attachment($my_file, $my_path, $mailto, $my_mail, $my_name, $my_replyto, $my_subject, $my_message);
					// verner
					mail_attachment($my_file, $my_path, $mailto1, $my_mail, $my_name, $my_replyto, $my_subject, $my_message);
					// kalininsky
					// mail_attachment($my_file, $my_path, $mailto2, $my_mail, $my_name, $my_replyto, $my_subject, $my_message);


				if(file_exists($export_dir.$slidename))
					{
						unlink($export_dir.$slidename);
					}



					// Mark image as sent to the Anekdot.ru
					$update_sql = "update wp_fsr_post set anekdotru_date='".date("d.m.y H:m:s")."' where ID=".$ID;
						$res = mysql_query($update_sql);
						if (!$res) {die('<br />'.$update_sql.'<br />Invalid delete query: ' . mysql_error());}
				}

			if (count($arrAuthors) >= $howmanyemails)
			{
				pokazh($arrAuthors);
				mysql_close($link);
				exit;
			}

		}

mysql_close($link);

?>


<?
function send_form($url,$_title,$_author,$_email='cartoonbank.ru@gmail.com',$_link,$_filename)
{
	$post = array(
		"action"=>"save",
		"title"=>utf8_to_cp1251($_title),
		"author"=>utf8_to_cp1251($_author),
		"email"=>$_email,
		"link"=>$_link,
		"file"=>"ufile",
		"rules_agree"=>"1",
        "ufile"=>"@$_filename",
		);

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$postResult =  curl_exec($ch);
	curl_close($ch);
	print "$postResult";

return true;
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


		   // create an empty truecolor container
		   $tempimage = imagecreatetruecolor($sourcefile_width+20,$sourcefile_height);
			$bgColor = imagecolorallocate($tempimage, 255,255,255);
			imagefill($tempimage , 0,0 , $bgColor);
		  
		   // copy the 8-bit gif into the truecolor image
		   imagecopy($tempimage, $sourcefile_id, 0, 0, 0, 0,
							   $sourcefile_width, $sourcefile_height);
		  
		   // copy the source_id int
		   $sourcefile_id = $tempimage;

	//text
	$black = ImageColorAllocate($sourcefile_id, 200, 200, 200); 
	$white = ImageColorAllocate($sourcefile_id, 255, 255, 255); 

	//The canvas's (0,0) position is the upper left corner 
	//So this is how far down and to the right the text should start 
	$start_x = $sourcefile_width;
	$start_y = $sourcefile_height; 

	// write text
	Imagettftext($sourcefile_id, 10, 90, $sourcefile_width+11, $sourcefile_height, $black, '/home/www/cb3/ales/arial.ttf', $text); 


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

function utf8_to_cp1251($s) 
  { 
  if ((mb_detect_encoding($s,'UTF-8,CP1251')) == "UTF-8") 
    { 
    for ($c=0;$c<strlen($s);$c++) 
      { 
      $i=ord($s[$c]); 
      if ($i<=127) $out.=$s[$c]; 
      if ($byte2) 
        { 
        $new_c2=($c1&3)*64+($i&63); 
        $new_c1=($c1>>2)&5; 
        $new_i=$new_c1*256+$new_c2; 
        if ($new_i==1025) 
          { 
          $out_i=168; 
          } else { 
          if ($new_i==1105) 
            { 
            $out_i=184; 
            } else { 
            $out_i=$new_i-848; 
            } 
          } 
        $out.=chr($out_i); 
        $byte2=false; 
        } 
        if (($i>>5)==6) 
          { 
          $c1=$i; 
          $byte2=true; 
          } 
      } 
    return $out; 
    } 
  else 
    { 
    return $s; 
    } 
  } 
?>