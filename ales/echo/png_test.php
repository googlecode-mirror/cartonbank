<?
// generate images for echo spb
//
// configuration
include("/home/www/cb3/ales/config.php");

//open db connection
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);

$mailto = 'igor.aleshin@gmail.com'; // destination email box

global $license_number;
$license_number = uniqid();

// get tema dnya cartoon id
$today = date('y-m-d');

$sql = "SELECT id 
		FROM  `tema_dnya` 
		WHERE `datetime` = '".$today."'";

$result = mysql_query($sql);
if (!$result) {die('<br />'.$sql.'<br />Invalid select query: ' . mysql_error());}

while ($row = mysql_fetch_array($result)) 
	{
        $id = $row['id'];  
    }

if(isset($_GET['id']) && is_numeric($_GET['id']))
{
	$image_id = $_GET['id']; //image to be sent to Echo //TBD
}
else
{
	$image_id = $id; //default image to be sent to Echo 
}


	$sql = "SELECT wp_product_list.image as image, 
		wp_product_list.id as ID,
		wp_product_list.name AS title, 
		wp_product_brands.name AS author, 
		wp_product_files.idhash,
		wp_product_list.brand AS brand,
		wp_product_files.mimetype
		FROM wp_product_list, wp_product_brands, wp_product_files 
		WHERE 
		wp_product_list.id = 12448
		AND wp_product_list.file = wp_product_files.id
		AND wp_product_list.brand = wp_product_brands.id";

	$result = mysql_query("$sql");

	if (!$result) {die('Invalid query: ' . mysql_error());}

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
		}

	// save license
	$license_text = get_echo_license('1', '1', $title, $ID, $author);
	save_license($license_text);

	//image 
	$filename = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/product_images/".$image;

	//resize the image 500
		$chwidth=500;
		$chheight=500;
		$thatdir='';
		$ifolder='';//subfolder for artist
		$file = $filename.'.'.$extension; 
		$idhash_path = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/files/".$idhash;
		$product_images='';
		$slidename = 'bbb.jpg';
		$iconname = 'sss.jpg';

		$thumb='';
		$resample_quality=100;

		//create resized image
		al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file, $idhash_path, $product_images, $slidename, $thumb, $resample_quality = '100');

		// create icon image
		$imagedir = $basepath."/home/www/cb3/ales/echo/";
        al_create_cropped_file(200, 200, $imagedir, $ifolder, $slidename, $resample_quality = '100'); 


	// Add logo
		$export_dir = "/home/www/cb3/ales/echo/";

		if(file_exists($export_dir.$slidename))
		{
			wtrmark($export_dir.$slidename,$wm,$author.' © cartoonbank.ru для echomsk.spb.ru');
		}

	//send email
		//email content
		$_link = "http://cartoonbank.ru/?page_id=29&brand=".$brand;
		$_link_cartoon = "http://cartoonbank.ru/?page_id=29&cartoonid=".$ID;

			$content = "Автор: ".$author."\n\r";
			$content .= "Название: ".$title."\n\r";
			//$content .= $_link_cartoon."\n\r";
			$content .= "Cсылка на страницу с карикатурой: ".$_link_cartoon."\n\r";

		// send email to Echo with image and license attachment
		//send_email_multi_attachments($content);


	//payment for Echo
	//pay_on_behalf_of_echo($image_id);

	mysql_close($link);


?>


<?
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
	Imagettftext($sourcefile_id, 10, 90, $sourcefile_width+12, $sourcefile_height-4, $black, '/home/www/cb3/ales/arial.ttf', $text); 


	$opacity_logo = 5;
	ImageCopyMerge($sourcefile_id, $logofile_id, $dest_x_logo, $dest_y_logo, 0, 0, $logo_width, $logo_height, $opacity_logo);
						   

    imagejpeg ($sourcefile_id,$sourcefile);

	   //Create a jpeg out of the modified picture 
	   /*switch($fileType) {
	  
		   // remember we don't need gif any more, so we use only png or jpeg.
		   // See the upsaple code immediately above to see how we handle gifs
		   case('png'):
			   imagepng ($sourcefile_id,$sourcefile);
			   break;
			  
		   default:
			   imagejpeg ($sourcefile_id,$sourcefile);
	   }*/
	 
	   imagedestroy($sourcefile_id);
	   imagedestroy($logofile_id);
}

function al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file,  $idhash_path, $product_images, $slidename, $thumb, $resample_quality = '100') {

	// Default thumbs creation
	$img_location = $file;
	$export_dir = "/home/www/cb3/ales/echo/";

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

		imagejpeg($resized, $export_dir.$slidename, $resample_quality);

			/*switch(strtolower($path["extension"])){
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
			}*/

		imagedestroy($resized);
		imagedestroy($img);

	}
	 else 
	 {
		 echo "\n     !!!!shit";
		$path["extension"] = "jpg";
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

function al_create_cropped_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality = '85') 
	{
    
    $img_location = $thatdir.$file;

    // Getting width ([0]) and height ([1]) maybe add options
    $size_bits = getimagesize($img_location);

    // Creating a resource image
    $path = pathinfo($img_location);

	$sfile = "sss.jpg";

    switch(strtolower($path["extension"])){
        case "jpeg":
        case "jpg":
            $img = imagecreatefromjpeg($img_location);
			$sfile = "sss.jpg";
            break;
        case "gif":
            $img = imagecreatefromgif($img_location);
			$sfile = "sss.gif";
            break;
        case "png":
            $img = imagecreatefrompng($img_location);
			$sfile = "sss.png";
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

		if (is_writable($thatdir.$ifolder)){
            //switch(strtolower($path["extension"])){
				switch("jpg"){
                case "jpeg":
                case "jpg":
                    imagejpeg($resized, $thatdir.$ifolder.'/'.$sfile, $resample_quality);
                    imagejpeg($resized, $thatdir.$ifolder.'/archive/'.date('y-m-d').$sfile, $resample_quality);
                    break;
                case "gif":
                    imagegif($resized, $thatdir.$ifolder.'/'.$sfile);
                    imagegif($resized, $thatdir.$ifolder.'/archive/'.date('y-m-d').$sfile, $resample_quality);
                    break;
                case "png":
                    imagepng($resized, $thatdir.$ifolder.'/'.$sfile);
                    imagepng($resized, $thatdir.$ifolder.'/archive/'.date('y-m-d').$sfile, $resample_quality);
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
        //switch(strtolower($path["extension"])){
			switch("jpg"){
            case "jpeg":
            case "jpg":
                imagejpeg($img, $thatdir.$ifolder.'/'.$file, $resample_quality);
                break;
            case "gif":
                imagegif($img, $thatdir.$ifolder.'/'.$file);
                break;
            case "png":
                imagepng($img, $thatdir.$ifolder.'/'.$file);
                break;
            default:
                break;
        }
    }
 }

function send_email_multi_attachments($content='')
{
	// array with filenames to be sent as attachment
	$files = array("/home/www/cb3/ales/echo/bbb.jpg","/home/www/cb3/ales/echo/license.htm");
 
	 // email fields: to, from, subject, and so on
	$to = "igor.aleshin@gmail.com";
	$to_2 = "vnechay@gmail.com";
	$from = "cartoonbank.ru@gmail.com"; 
	$subject ="карикатура для Эха Петербурга от Картунбанка.ру"; 
	$message = $content;
	$headers = "From: $from";

	// boundary 
	$semi_rand = md5(time()); 
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
	 
	// headers for attachment 
	$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
	 
	// multipart boundary 
	$message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
	$message .= "--{$mime_boundary}\n";
	 
	// preparing attachments
	for($x=0;$x<count($files);$x++){
		$file = fopen($files[$x],"rb");
		$data = fread($file,filesize($files[$x]));
		fclose($file);
		$data = chunk_split(base64_encode($data));
		$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$files[$x]\"\n" . 
		"Content-Disposition: attachment;\n" . " filename=\"$files[$x]\"\n" . 
		"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
		$message .= "--{$mime_boundary}\n";
	}
	 
	// send
	 
	$ok = @mail($to, $subject, $message, $headers); 
	if ($ok) { 
		echo "<p>mail sent to $to!</p>"; 
	} else { 
		echo "<p>mail could not be sent!</p>"; 
	} 

	$ok = @mail($to_2, $subject, $message, $headers); 
	if ($ok) { 
		echo "<p>mail sent to $to!</p>"; 
	} else { 
		echo "<p>mail could not be sent!</p>"; 
	} 

}

function get_echo_license($sequence_of_image='1',$license_num='1', $image_name, $image_number, $author_name)
{
	global $license_number;

	$agreement_date = date("m.d.y");
	$customer_name = "Валерий Нечай, «Эхо Петербурга»";
	$media_name = "«Эхо Петербурга»";
	$discount = 25;
	$price = 200;

	//load License template
	$filename = '';
	switch($license_num)
        {
        case 1:
        $filename = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/" . "Livense_limited_template.htm";
        break;
        
        case 2:
        $filename = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/" . "Livense_standard_template.htm";
        break;

        case 3:
        $filename = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/" . "Livense_extended_template.htm";
        break;

        default:
        $filename = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/" . "Livense_limited_template.htm";
        break;
		}

	$content=loadFile($filename); 

	// replace placeholders
		$content = str_replace ('#agreement_number#',$license_number,$content);
		$content = str_replace ('#agreement_date#',$agreement_date,$content);
		$content = str_replace ('#customer_name#',$customer_name,$content);
		$content = str_replace ('#image_number#',$image_number,$content);
		$content = str_replace ('#image_name#',$image_name,$content);
		$content = str_replace ('#author_name#',$author_name,$content);
		$content = str_replace ('#media_name#',$media_name,$content);
		$content = str_replace ('#price#',$price,$content);

	// output content
	return $content;
}

function loadFile($sFilename, $sCharset = 'UTF-8')
{
    if (floatval(phpversion()) >= 4.3) {
        $sData = file_get_contents($sFilename);
    } else {
        if (!file_exists($sFilename)) return -3;
        $rHandle = fopen($sFilename, 'r');
        if (!$rHandle) return -2;

        $sData = '';
        while(!feof($rHandle))
            $sData .= fread($rHandle, filesize($sFilename));
        fclose($rHandle);
    }
    if ($sEncoding = mb_detect_encoding($sData, 'auto', true) != $sCharset)
        $sData = mb_convert_encoding($sData, $sCharset, $sEncoding);
    return $sData;
 }

function save_license($content)
{
	$myFile = "/home/www/cb3/ales/echo/license.htm";
	$fh = fopen($myFile, 'w') or die("can't open file");
	$stringData = $content;
	fwrite($fh, $stringData);
	fclose($fh);
}

function pay_on_behalf_of_echo($cartoon_id)
{
	// constants
	global $license_number;
	$license_num = $license_number."_".$cartoon_id;
	$sessionid = uniqid();
	$price = "250";
	$date_today = time();
	$date_download = date('y-m-d H:i:s');

	// wp_purchase_logs
		$sql = "INSERT INTO `cartoonbankru`.`wp_purchase_logs` (`id`, `totalprice`, `statusno`, `sessionid`, `transactid`, `authcode`, `user_id`, `firstname`, `lastname`, `email`, `address`, `phone`, `downloadid`, `processed`, `date`, `payment_arrived_date`, `gateway`, `shipping_country`, `shipping_region`) VALUES (NULL, '".$price."', '0', '".$sessionid."', '0', '0', '131', 'Валерий', 'Нечай', 'vnechay@gmail.com', 'эхомскспбру', '+7(921)9341454', '0', '1', '".$date_today."', '', 'wallet', '', '')";

		$result = mysql_query($sql);
		if (!$result) {die('Invalid query: ' . mysql_error());}

		$purchase_id = mysql_insert_id();
		if (!$purchase_id) {die('Can\'t get inserted line id: ' . mysql_error());}


	// wp_cart_contents
		$sql = "INSERT INTO `cartoonbankru`.`wp_cart_contents` (`id`, `prodid`, `purchaseid`, `price`, `pnp`, `gst`, `quantity`, `license`) VALUES (NULL, '".$cartoon_id."', '".$purchase_id."', '".$price."', '0', '', '1', '".$license_num."')";

		$result = mysql_query($sql);
		if (!$result) {die('Invalid query: ' . mysql_error());}


	// wp_status
		$sql = "INSERT INTO `cartoonbankru`.`wp_download_status` (`id`, `fileid`, `purchid`, `downloads`, `active`, `datetime`) VALUES (NULL, '".$cartoon_id."', '".$purchase_id."', '0', '0', '".$date_download."')";

		$result = mysql_query($sql);
		if (!$result) {die('Invalid query: ' . mysql_error());}

	echo "done";
}
?>