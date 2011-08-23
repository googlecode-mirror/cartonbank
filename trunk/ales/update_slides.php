<?php
// update the SQL request first!
//$sql = "SELECT l.id, f.filename FROM `wp_product_files` as f,`wp_product_list` as l WHERE l.brand in (19,20,29,30) AND l.active=1 and f.id=l.file AND l.id > 1 ORDER BY l.id ASC limit 1500";  
//$sql = "SELECT l.id, f.filename FROM `wp_product_files` as f,`wp_product_list` as l WHERE l.brand in (23) AND l.active=1 and f.id=l.file AND l.id > 1 ORDER BY l.id ASC limit 1000";  

$sql = "SELECT l.id, f.filename FROM `wp_product_files` as f,`wp_product_list` as l WHERE l.id > 12000 and f.id=l.file AND l.active=1 ";  
 echo $sql;



 ini_set ('user_agent', $_SERVER['HTTP_USER_AGENT']); 
 include("/home/www/cb3/ales/config.php");

 echo "starting watermark update...";

 // connect to db

    $con = mysql_connect($mysql_hostname,$mysql_user,$mysql_password);
        if (!$con)
          {
          die('Could not connect: ' . mysql_error());
          }

        mysql_select_db($mysql_database, $con);


        $result = mysql_query($sql);

            if (!$result) {
                die('Invalid query: ' . mysql_error());
            }
          $counter = 1;

    $img_array = mysql_fetch_full_result_array($result);   
	$length = count($img_array);
	echo "\ntotal: ".$length." images\n";

    
	//pokazh($img_array);

$counter=0;
	foreach($img_array as $row)
      {
		  $counter = $counter+1;
		  echo "\n\nimage #".$counter." of ".$length.":\n";
		  $id = $row['id'];
		  //echo "\n";
		  echo "http://cartoonbank.ru/wp-admin/admin.php?page=wp-shopping-cart/display-items.php&edid=".$id;

		  make_files_with_watermark($id);

		  sleep(5);
	  }
echo "<br>\n==== done! =====<br>\n";
mysql_close($con);


function mysql_fetch_full_result_array($result)
{
        $table_result=array();
        $r=0;
        while($row = mysql_fetch_assoc($result)){
            $arr_row=array();
            $c=0;
            while ($c < mysql_num_fields($result)) {       
                $col = mysql_fetch_field($result, $c);   
                $arr_row[$col -> name] = $row[$col -> name];           
                $c++;
            }   
            $table_result[$r] = $arr_row;
            $r++;
        }   
        return $table_result;
}
/*
        $result = mysql_query($sql);

            if (!$result) {
                die('Invalid query: ' . mysql_error());
            }
          $counter = 1;

    $img_array = mysql_fetch_full_result_array($result);     

*/
function make_files_with_watermark($id)
{
	 $imagedir = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/images/";
	 $product_images = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/product_images/";
	 $filedir = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/files/";
	 $export_dir = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/slides/";
	 $wm = "/home/www/cb3/img/watermark.png";
	 $chwidth = $chheight = 600;

    //$fileid_data = $wpdb->get_results("SELECT `file` FROM `wp_product_list` WHERE `id` = '$id' LIMIT 1",ARRAY_A);
	$sql = "SELECT `file` FROM `wp_product_list` WHERE `id` = '$id' LIMIT 1";
	$result = mysql_query($sql);
	if (!$result) {die('Invalid query: ' . mysql_error());}

	$fileid_data = mysql_fetch_full_result_array($result);
    $fileid = $fileid_data[0]['file'];

    //$file_data = $wpdb->get_results("SELECT * FROM `wp_product_files` WHERE `id` = '$fileid' LIMIT 1",ARRAY_A);
	$sql = "SELECT * FROM `wp_product_files` WHERE `id` = '$fileid' LIMIT 1";
	$result = mysql_query($sql);
	if (!$result) {die('Invalid query: ' . mysql_error());}

	$file_data = mysql_fetch_full_result_array($result);
    $idhash = $file_data[0]['idhash'];

    if (file_exists($filedir.$idhash))
    {
        $mimetype = $file_data[0]['mimetype'];
        $filename = $file_data[0]['filename'];
        
        $height = 600;
        $width  = 600;

          //$imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/images/";
          //$product_images = $basepath."/wp-content/plugins/wp-shopping-cart/product_images/";
          //$filedir = $basepath."/wp-content/plugins/wp-shopping-cart/files/";

          copy($filedir.$idhash, $imagedir.$filename); // icon
          copy($filedir.$idhash, $product_images.$filename); // preview

                        $imgsize = getimagesize($product_images.$filename);
                        $file_w = $imgsize[0];
                        $file_h = $imgsize[1];

                    //ales here we replace slides to that from LG
                    $chwidth = 600; // crop size
                    $chheight = 600; // crop size
                    //$thatdir = $product_images; //destination dir
                    $ifolder = ''; //subfolder for artist
                    $file = $filename; //
                    $resample_quality = 100; //image quality

                    // slide
                    al_create_resized_file($chwidth, $chheight, $product_images, $ifolder, $file, $resample_quality);    
                    
                    // watremark
					$wm = $basepath."/img/watermark.png";
                    wtrmark($product_images.$file,$wm);

                    // icon
                    al_create_cropped_file(140, 140, $imagedir, $ifolder, $file, $resample_quality);      
    }
    else
    {
        echo "<div class='error'><b>WARNING:</b> original file is not found at: ".$filedir.$idhash." <br /></div>";
    }
}

function al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file, $thumb, $resample_quality = '100') {
    // Default thumbs creation
    // global $gallery_root;
    $gallery_root = '';
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
 }

 function wtrmark($sourcefile, $watermarkfile) 
	 {
	   #
	   # $sourcefile = Filename of the picture to be watermarked.
	   # $watermarkfile = Filename of the 24-bit PNG watermark file.
	   #
	   
		$logopath = "/home/www/cb3/img/cb-logo-300.png";
		$watermarkfile = "/home/www/cb3/img/watermark.png";

		$logofile_id = imagecreatefrompng($logopath);
	  
	   $watermarkfile_id = imagecreatefrompng($watermarkfile);

	   imageAlphaBlending($watermarkfile_id, true);
	   imageSaveAlpha($watermarkfile_id, true);

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
	  $watermarkfile_width=imageSX($watermarkfile_id);
	  $watermarkfile_height=imageSY($watermarkfile_id);
	  $logo_width=imageSX($logofile_id);
	  $logo_height=imageSY($logofile_id);

	   $dest_x = ( $sourcefile_width / 2 ) - ( $watermarkfile_width / 2 );
	   $dest_y = ( $sourcefile_height / 2 ) - ( $watermarkfile_height / 2 );
	   $dest_x_logo = $sourcefile_width - $logo_width - 4;
	   $dest_y_logo = $sourcefile_height - $logo_height - 4;
	  
	  
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

	   //imagecopy($sourcefile_id, $watermarkfile_id, $dest_x, $dest_y, 0, 0,
		//                   $watermarkfile_width, $watermarkfile_height);

		
		$opacity = 8;
		$opacity_logo = 50;

	   ImageCopyMerge($sourcefile_id, $watermarkfile_id, $dest_x, $dest_y, 0, 0, $watermarkfile_width, $watermarkfile_height, $opacity);
	  
	   ImageCopyMerge($sourcefile_id, $logofile_id, $dest_x_logo, $dest_y_logo, 0, 0, $logo_width, $logo_height, $opacity_logo);
						   


	   //Create a jpeg out of the modified picture 
	   switch($fileType) {
	  
		   // remember we don't need gif any more, so we use only png or jpeg.
		   // See the upsaple code immediately above to see how we handle gifs
		   case('png'):
			   //header("Content-type: image/png");
			   imagepng ($sourcefile_id,$sourcefile);
			   break;
			  
		   default:
			   //header("Content-type: image/jpg"); 
			   imagejpeg ($sourcefile_id,$sourcefile);
	   }
	 
		   imagedestroy($sourcefile_id);
		   imagedestroy($watermarkfile_id);
		   imagedestroy($logofile_id);
	  
}

function al_create_cropped_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality = '100') {
    
    $img_location = $thatdir.$file;

    // Getting width ([0]) and height ([1]) maybe add options
    $size_bits = getimagesize($img_location);

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

        if (is_writable($thatdir.$ifolder)){
            switch(strtolower($path["extension"])){
                case "jpeg":
                case "jpg":
                    imagejpeg($resized, $thatdir.$ifolder.'/'.$file, $resample_quality);
                    break;
                case "gif":
                    imagegif($resized, $thatdir.$ifolder.'/'.$file);
                    break;
                case "png":
                    imagepng($resized, $thatdir.$ifolder.'/'.$file);
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