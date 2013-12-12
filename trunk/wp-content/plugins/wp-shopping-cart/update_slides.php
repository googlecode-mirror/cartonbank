<?php
 echo "\nstarting the game";

 $imagedir = ROOTDIR."wp-content/plugins/wp-shopping-cart/images/";
 $product_images = ROOTDIR."wp-content/plugins/wp-shopping-cart/product_images/";
 $filedir = ROOTDIR."wp-content/plugins/wp-shopping-cart/files/";
 $export_dir = ROOTDIR."wp-content/plugins/wp-shopping-cart/product_images/";
 $wm = ROOTDIR."img/watermark.png";
 $chwidth = $chheight = 600;
 
 // get the list of files

 // connect to db
    $con = mysql_connect("localhost","z58365_cbru3","greenbat");
        if (!$con)
          {
          die('Could not connect: ' . mysql_error());
          }

        mysql_select_db("z58365_cbru3", $con);

        $sql = "SELECT *, f.filename, f.idhash, f.mimetype FROM `wp_product_files` as f,`wp_product_list` as l WHERE l.active='1' and f.id=l.file ORDER BY l.id ASC";  
        $result = mysql_query($sql);

            if (!$result) {
                die('Invalid query: ' . mysql_error());
            }
          $counter = 1;

    $img_array = mysql_fetch_full_result_array($result);     
    mysql_close($con);

	$i = 0;

	foreach($img_array as $row)
      {
		 
       if ($i<3300)
		  {
		    echo "\n\ncount: ".$i;
            $idhashname = $row['idhash'];
            $slidename = $row['filename'];
            $mimetype = $row['mimetype'];
			$id = $row['id'];
            $file_ext = substr($mimetype,6,strlen($mimetype));

             // get the file   
                $file_path = $filedir.$idhashname.'.'.$file_ext;
                $idhash_path = $filedir.$idhashname;    

            if(file_exists($idhash_path))
             {
                echo (" \n     Resizing. slidename: ".$slidename. " idhashname: ".$idhashname);
             
			 // resize the original
                
               al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file_path,  $idhash_path, $product_images, $slidename, $resample_quality);
 
             // make watermark
                echo "\n     Making wm: ".$slidename;
 
				//echo "\n     export_dir.slidename: ".$export_dir.$slidename;

                if(file_exists($export_dir.$slidename))
                {
                    wtrmark($export_dir.$slidename,$wm);
					echo "\n>>>>".SITEURL."wp-content/plugins/wp-shopping-cart/product_images/".$slidename;
                }
				else
                {
                    echo ("\n===Can't make watermark - no file found! ".$slidename);
                }
             }
             else
             {
                echo ('\n==='.$slidename.' not found');
             }   
             $i = $i + 1;  
		  }
      }
	

echo "\n ==== done! =====\n";
 // functions
 function wtrmark($sourcefile, $watermarkfile) {
    $logopath = ROOTDIR."img/cb-logo-300.png";
    $watermarkfile = ROOTDIR."img/watermark.png";

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

    
    $opacity = 5;
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
 
 function al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file,  $idhash_path, $product_images, $slidename, $thumb, $resample_quality = '85') 
 {
    // Default thumbs creation
    $img_location = $file;
    $export_dir = ROOTDIR."wp-content/plugins/wp-shopping-cart/product_images/";
 
    // Creating a resource image
    $path = pathinfo($img_location);

    //error_reporting(0);
    
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
					echo "\n     Jpg: copy resized image to ".$export_dir.$slidename;
                    imagejpeg($resized, $export_dir.$slidename, $resample_quality);
                    break;
                case "gif":
                    echo "\n     Gif: copy resized image to ".$export_dir.$slidename;
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
 ?>
