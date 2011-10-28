<?php 

require ('/home/www/cb3/wp-includes/functions.php');

echo "please, upload files";

    //savefiles
    if (isset($_FILES) && isset($_POST)){
        
        foreach ($_FILES as $key => $_file) { 
            foreach ($_file as $vkey => $vvalue) { 
                savefiles($_file);
            }
        }
    }

// files
if (isset($_FILES))
	$result = $_FILES;
else
	$result = "no files received";

fww($result);

// forms
if (isset($_POST))
	$result = $_POST;
else
	$result = "no form fields received";

fw($result);


function fww($text)
{
	$fp = fopen('_kloplog.txt', 'a') or die('Could not open file!');
	fwrite($fp, "\n====FILES====\n") or die('Could not write to file');
	foreach ($text as $key => $value) { 
		foreach ($value as $vkey => $vvalue) { 
			$toFile = " $vkey = $vvalue \n";
			fwrite($fp, $toFile) or die('Could not write to file');
		}
		fwrite($fp, "\n") or die('Could not write to file');
	}
	fclose($fp);
}

function fw($text)
{
	$fp = fopen('_kloplog.txt', 'a') or die('Could not open file!');
	fwrite($fp, "-----POST----\n") or die('Could not write to file');
	foreach ($text as $key => $value) { 
			$toFile = " $key = $value \n";
			fwrite($fp, $toFile) or die('Could not write to file');
	}
	fclose($fp);
}

function fw1($text)
{
	$fp = fopen('_kloplog.txt', 'a') or die('Could not open file!');
	fwrite($fp, "\n-----POST[carname]----\n") or die('Could not write to file');
			fwrite($fp, $text) or die('Could not write to file');
	fclose($fp);
}

function savefiles($_file){
    global $wpdb;
    // add product
    if(isset($_POST['submit_action']) && $_POST['submit_action'] == 'add') {

        if($_file != null)  {
            $basepath = str_replace("/ales/upload", "" , getcwd());

            $imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/images/";
            $product_images = $basepath."/wp-content/plugins/wp-shopping-cart/product_images/";
            $filedir = $basepath."/wp-content/plugins/wp-shopping-cart/files/";

            //upload_and_resize_and_watermark_images();    

             $t = $_FILES['my-pic'];
            /* read data (binary) */ 
            $ifp = fopen( $t['tmp_name'], "rb" ); 
            $imageData = fread( $ifp, filesize( $t['tmp_name'] ) ); 
            fclose( $ifp ); 
            /* encode & write data (binary) */ 
            $ifp = fopen( $t['tmp_name'], "wb" ); 
            fwrite( $ifp, base64_decode( $imageData ) ); 
            fclose( $ifp );
            
            //transliterate file
            $_FILES['my-pic']['name'] = rus2translit($_FILES['my-pic']['name']); 
            //rename the file  
            $_FILES['my-pic']['name'] = uniqid('', true).$_FILES['my-pic']['name'];
            //ales default upload
            if(!is_dir($product_images))
            {
                mkdir($product_images);
            }
            if(function_exists("getimagesize"))
            {
                $height = 140;
                $width  = 140;
                copy($_FILES['my-pic']['tmp_name'], $product_images.$_FILES['my-pic']['name']);
                copy($_FILES['my-pic']['tmp_name'], $imagedir.$_FILES['my-pic']['name']);
                
                
                chmod($product_images.$_FILES['my-pic']['name'], 0666);
                //chmod($imagedir.$_FILES['my-pic']['name'], 0666);

                $imgsize = getimagesize($product_images.$_FILES['my-pic']['name']);

                $file_w = $imgsize[0];
                $file_h = $imgsize[1];

                //ales here we replace slides to that from LG
                $chwidth = 600; //get_option('lg_pictwidth'); // crop size
                $chheight = 600; //get_option('lg_pictheight'); // crop size
                $thatdir = $product_images; //destination dir
                $ifolder = ''; //subfolder for artist
                $file = $_FILES['my-pic']['name']; //
                $resample_quality = 100; //image quality

                ales_create_cropped_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality);    
                $wm = $basepath."/wp-content/plugins/wp-shopping-cart/images/watermark.png";
                wtrmark($thatdir.$file,$wm);

                // ales here we replace thumbs to that from LG 
                $chwidth = $width; // crop size
                $chheight = $height; // crop size
                $thatdir = $imagedir; //destination dir

                al_create_cropped_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality);      
                $image = $wpdb->escape($_FILES['my-pic']['name']);

                /// ales 
            }
            else {
                move_uploaded_file($_FILES['my-pic']['tmp_name'], ($imagedir.$_FILES['my-pic']['name']));
                $image = $wpdb->escape($_FILES['my-pic']['name']);
            }
            ///ales

            $timestamp = time();
            $wpdb->query("INSERT INTO `wp_product_files` ( `id` , `filename`  , `mimetype` , `idhash` , `date` , `width`, `height`) VALUES ( '' , '', '', '', '$timestamp', '', '');");
            $fileid_raw = $wpdb->get_results("SELECT `id` FROM `wp_product_files` WHERE `date` = '$timestamp'",ARRAY_A);
            $fileid = $fileid_raw[0]['id'];
            $idhash = sha1($fileid);
            $mimetype = $_FILES['my-pic']['type'];
            $splitname = explode(".",$_FILES['my-pic']['name']);
            $splitname = array_reverse($splitname);
            $filename = $_FILES['my-pic']['name'];

            if(move_uploaded_file($_FILES['my-pic']['tmp_name'],($filedir.$idhash)))
            {
                $wpdb->query("UPDATE `wp_product_files` SET `filename` = '".$filename."', `mimetype` = '$mimetype', `idhash` = '$idhash', `width` = '$file_w', `height` = '$file_h' WHERE `id` = '$fileid' LIMIT 1");
            }
            $file = $fileid;
        }    
    }

}




function ales_create_cropped_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality = '100') {
    
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
                           


       //Create a jpeg out of the modified picture 
       switch('jpg') {
      
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

?>