<?php 
$category_data = null;
$basepath = str_replace("/wp-admin", "" , getcwd()); 
$basepath = str_replace("\\wp-admin", "" , $basepath);

$imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/images/";
$product_images = $basepath."/wp-content/plugins/wp-shopping-cart/product_images/";
$filedir = $basepath."/wp-content/plugins/wp-shopping-cart/files/";
$preview_clips_dir = $basepath."/wp-content/plugins/wp-shopping-cart/preview_clips/";
$image = '';

// add product
if(isset($_POST['submit_action']) && $_POST['submit_action'] == 'add') {

  if($_FILES['file']['name'] != null)  {
  
      //upload_and_resize_and_watermark_images();    
      
      //transliterate file
      $_FILES['file']['name'] = rus2translit($_FILES['file']['name']); 
      //rename the file  
      $_FILES['file']['name'] = uniqid('', true).$_FILES['file']['name'];
		//$_FILES['file']['name'] = 'blah-blah'.$_FILES['file']['name'];
        //ales default upload
		if(!is_dir($product_images))
		  {
		    mkdir($product_images);
		  }
		if(function_exists("getimagesize"))
		  {
		  switch(isset($_POST['image_resize']) && $_POST['image_resize'])
			{
			case 2:
			$height = $_POST['height'];
			$width  = $_POST['width'];
			break;

			default:
			$height = get_option('product_image_height');
			$width  = get_option('product_image_width');
			break;
			}
		  copy($_FILES['file']['tmp_name'], $product_images.$_FILES['file']['name']);
		  copy($_FILES['file']['tmp_name'], $imagedir.$_FILES['file']['name']);

				$imgsize = getimagesize($product_images.$_FILES['file']['name']);
				$file_w = $imgsize[0];
				$file_h = $imgsize[1];

				//ales here we replace slides to that from LG
				$chwidth = get_option('lg_pictwidth'); // crop size
				$chheight = get_option('lg_pictheight'); // crop size
				$thatdir = $product_images; //destination dir
				$ifolder = ''; //subfolder for artist
				$file = $_FILES['file']['name']; //
				$resample_quality = 85; //image quality

				al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality);	
				$wm = $basepath."/wp-content/plugins/wp-shopping-cart/images/watermark.png";
				wtrmark($thatdir.$file,$wm);

				// ales here we replace thumbs to that from LG 
				$chwidth = $width; // crop size
				$chheight = $height; // crop size
				$thatdir = $imagedir; //destination dir

				al_create_cropped_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality);	  
				$image = $wpdb->escape($_FILES['file']['name']);

				/// ales 
		 }
		  else {
			    move_uploaded_file($_FILES['file']['tmp_name'], ($imagedir.$_FILES['file']['name']));
				$image = $wpdb->escape($_FILES['file']['name']);
		  }
		  ///ales

		$timestamp = time();
		$wpdb->query("INSERT INTO `".$wpdb->prefix."product_files` ( `id` , `filename`  , `mimetype` , `idhash` , `date` , `width`, `height`) VALUES ( '' , '', '', '', '$timestamp', '', '');");
		$fileid_raw = $wpdb->get_results("SELECT `id` FROM `".$wpdb->prefix."product_files` WHERE `date` = '$timestamp'",ARRAY_A);
		$fileid = $fileid_raw[0]['id'];
		$idhash = sha1($fileid);
		$mimetype = $_FILES['file']['type'];
		$splitname = explode(".",$_FILES['file']['name']);
		$splitname = array_reverse($splitname);
		$filename = $_FILES['file']['name'];
		
		if(move_uploaded_file($_FILES['file']['tmp_name'],($filedir.$idhash)))
		  {
		  $wpdb->query("UPDATE `".$wpdb->prefix."product_files` SET `filename` = '".$filename."', `mimetype` = '$mimetype', `idhash` = '$idhash', `width` = '$file_w', `height` = '$file_h' WHERE `id` = '$fileid' LIMIT 1");
		  }
    $file = $fileid;
 }
else
      {
      $file = '0';
      }



   if(isset($_POST['special'])&&$_POST['special'] == 'yes')
     {
     $special = 1;
     if(is_numeric($_POST['special_price']))
       {
       $special_price = $_POST['price'] - $_POST['special_price'];
       }
     }
     else
       {
       $special = 0;
       $special_price = '';
       }
       
   if(isset($_POST['notax'])&&$_POST['notax'] == 'yes')
     {
     $notax = 1;
     }
     else
       {
       $notax = 0;
       }
   if(isset($_POST['quantity']) && is_numeric($_POST['quantity']) && isset($_POST['quantity_limited']) && ($_POST['quantity_limited'] == "yes"))
     {
     $quantity_limited = 1;
     $quantity = $_POST['quantity'];
     }
     else
       {
       $quantity_limited = 0;
       $quantity = 0;
       }
   if(isset($_POST['display_frontpage']) && $_POST['display_frontpage'] == "yes")
     {
     $display_frontpage = 1;
     }
     else
       {
       $display_frontpage = 0;
       }
       
$visible = '0';
$_price='';
$_pnp = '';
$_international_pnp = '';

if ($_POST['visible'] == 'on')
	$visible = '1';  

if (isset($_POST['price']))
	$_price = $_POST['price'];
if (isset($_POST['pnp']))
	$_pnp = $_POST['pnp'];
if (isset($_POST['international_pnp ']))
	$_international_pnp  = $_POST['international_pnp '];

  $insertsql = "INSERT INTO `".$wpdb->prefix."product_list` ( `id` , `name` , `description` , `additional_description` , `price` , `pnp`, `international_pnp`, `file` , `image` , `category`, `brand`, `quantity_limited`, `quantity`, `special`, `special_price`,`display_frontpage`, `notax`, `visible` ) VALUES ('', '".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['name'])))."', '".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."', '".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['additional_description'])))."','".$wpdb->escape(str_replace(",","",$_price))."', '".$wpdb->escape($_pnp)."', '".$wpdb->escape($_international_pnp)."', '".$file."', '".$image."', '".$wpdb->escape($_POST['category'])."', '".$wpdb->escape($_POST['brand'])."', '$quantity_limited','$quantity','$special','$special_price','$display_frontpage','$notax', '$visible');";

  if($wpdb->query($insertsql))
    {
    $product_id_data = $wpdb->get_results("SELECT LAST_INSERT_ID() AS `id` FROM `".$wpdb->prefix."product_list` LIMIT 1",ARRAY_A);
    $product_id = $product_id_data[0]['id'];
  
  if(isset ($_FILES['extra_image']) && ($_FILES['extra_image'] != null) && function_exists('edit_submit_extra_images'))
    {
    $var = edit_submit_extra_images($product_id);
    }
  
  $variations_procesor = new nzshpcrt_variations;
  if(isset($_POST['variation_values']) && $_POST['variation_values'] != null)
    {
    $variations_procesor->add_to_existing_product($product_id,$_POST['variation_values']); 
    }
  
  $counter = 0;
  $item_list = '';
  if(count($_POST['category']) > 0)
    {
    foreach($_POST['category'] as $category_id)
      {
		$sql_delete = "DELETE `wp_item_category_associations`.* FROM `wp_item_category_associations` WHERE `product_id` = '".$product_id."'";
//		exit($sql_delete);
		$wpdb->query($sql_delete);
//        $check_existing = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."item_category_associations` WHERE `product_id` = ".$product_id." AND `category_id` = '$category_id' LIMIT 1");
//      if($check_existing == null)
//        {
        $wpdb->query("INSERT INTO `wp_item_category_associations` ( `id` , `product_id` , `category_id` ) VALUES ('', '".$product_id."', '".$category_id."');");        
//        }
      }
    }

	else
	{
	  $default_cat = get_option('default_category');
//      $check_existing = $wpdb->get_var("SELECT `id` FROM `wp_item_category_associations` WHERE `product_id` = ".$product_id." AND `category_id` = '".$default_cat."' LIMIT 1");
      $check_existing = $wpdb->get_var("SELECT `id` FROM `wp_item_category_associations` WHERE `product_id` = ".$product_id." LIMIT 1");
      if($check_existing == null)
        {
        $wpdb->query("INSERT INTO `".$wpdb->prefix."item_category_associations` ( `id` , `product_id` , `category_id` ) VALUES ('', '".$product_id."', '".$default_cat."');");        
        }
     }

 
  $display_added_product = "filleditform(".$product_id.");";
  
  echo "<div class='updated'><p align='center'>".TXT_WPSC_ITEMHASBEENADDED."</p></div>";
  }
  else
    {
    echo "<div class='updated'><p align='center'>".TXT_WPSC_ITEMHASNOTBEENADDED."</p></div>";
    }
 }

if(isset($_GET['submit_action']) && $_GET['submit_action'] == "remove_set")
  {
  if(is_numeric($_GET['product_id']) && is_numeric($_GET['variation_assoc_id']))
    {
    $product_id = $_GET['product_id'];
    $variation_assoc_id = $_GET['variation_assoc_id'];
    $variation_association = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_associations` WHERE `id` = '$variation_assoc_id' LIMIT 1",ARRAY_A);
    if($variation_association != null)
      {
      $variation_association = $variation_association[0];
      $variation_id = $variation_association['variation_id'];
      $delete_variation_sql = "DELETE FROM `".$wpdb->prefix."variation_associations` WHERE `id` = '$variation_assoc_id' LIMIT 1";
      $delete_value_sql = "DELETE FROM `".$wpdb->prefix."variation_values_associations` WHERE `product_id` = '$product_id' AND `variation_id` = '$variation_id'";
      $wpdb->query($delete_variation_sql);
      $wpdb->query($delete_value_sql);
      echo "<div class='updated'><p align='center'>".TXT_WPSC_PRODUCTHASBEENEDITED."</p></div>";
      }
    } 
  }

// edit product 
if(isset($_POST['submit_action']) && $_POST['submit_action'] == "edit")
  {
      //transliterate file
      $_FILES['file']['name'] = rus2translit($_FILES['file']['name']); 
      //rename the file  
      $_FILES['file']['name'] = uniqid('', true).$_FILES['file']['name'];
      
      $id = $_POST['prodid'];
	  if(function_exists('edit_submit_extra_images'))
		{
		if(($_FILES['extra_image'] != null))
		  {
		  $var = edit_submit_extra_images($id);
		  }
		}
	  if(function_exists('edit_extra_images'))
		{
		$var = edit_extra_images($id);
		}
	  //$basepath = str_replace("/wp-admin", "" , getcwd()); this defined at the top of the page
	  $imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/images/";
	  $product_images = $basepath."/wp-content/plugins/wp-shopping-cart/product_images/";
	  $filedir = $basepath."/wp-content/plugins/wp-shopping-cart/files/";
	  $preview_clips_dir = $basepath."/wp-content/plugins/wp-shopping-cart/preview_clips/"; 

  if(($_FILES['file']['tmp_name'] != null) && ($_FILES['file']['name'] != null))
   {
    $id = $_POST['prodid'];
    $fileid_data = $wpdb->get_results("SELECT `file` FROM `".$wpdb->prefix."product_list` WHERE `id` = '$id' LIMIT 1",ARRAY_A);
    $fileid = $fileid_data[0]['file'];
    $file_data = $wpdb->get_results("SELECT `id`,`idhash` FROM `".$wpdb->prefix."product_files` WHERE `id` = '$fileid' LIMIT 1",ARRAY_A);
    $idhash = $file_data[0]['idhash'];
    $mimetype = $_FILES['file']['type'];

    $filename = $_FILES['file']['name'];

			if(!is_dir($product_images))
			  {
			    mkdir($product_images);
			  }
			if(function_exists("getimagesize"))
			  {
				switch(isset ($_POST['image_resize']) && $_POST['image_resize'])	
                    {
                    case 2:
                    $height = $_POST['height'];
                    $width  = $_POST['width'];
                    break;

                    default:
                    $height = get_option('product_image_height');
                    $width  = get_option('product_image_width');
                    break;
                    }
              copy($_FILES['file']['tmp_name'], $product_images.$_FILES['file']['name']);
              copy($_FILES['file']['tmp_name'], $imagedir.$_FILES['file']['name']);

					$imgsize = getimagesize($product_images.$_FILES['file']['name']);
					$file_w = $imgsize[0];
					$file_h = $imgsize[1];

                //ales here we replace slides to that from LG
                $chwidth = get_option('lg_pictwidth'); // crop size
                $chheight = get_option('lg_pictheight'); // crop size
                $thatdir = $product_images; //destination dir
                $ifolder = ''; //subfolder for artist
                $file = $_FILES['file']['name']; //
                $resample_quality = 85; //image quality

                al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality);    
                $wm = $basepath."/wp-content/plugins/wp-shopping-cart/images/watermark.png";
                wtrmark($thatdir.$file,$wm);

                // ales here we replace thumbs to that from LG 
                $chwidth = $width; // crop size
                $chheight = $height; // crop size
                $thatdir = $imagedir; //destination dir

                al_create_cropped_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality);      
                $image = $wpdb->escape($_FILES['file']['name']);

                /// ales 

                    
              }
              else {
                move_uploaded_file($_FILES['file']['tmp_name'], ($imagedir.$_FILES['file']['name']));
                $image = $wpdb->escape($_FILES['file']['name']);
          }
			  //include("image_processing.php");

		if(move_uploaded_file($_FILES['file']['tmp_name'],($filedir.$idhash)))
		  {
		  $wpdb->query("UPDATE `".$wpdb->prefix."product_files` SET `filename` = '".$filename."', `mimetype` = '$mimetype', `width` = '$file_w', `height` = '$file_h' WHERE `id` = '".$file_data[0]['id']."' LIMIT 1");
		  }
    }

  if(is_numeric($_POST['prodid']))
    {
    if(isset($_POST['image_resize']) && ($_POST['image_resize'] > 0) && ($image === ''))
      {
      $imagesql = "SELECT `image` FROM `".$wpdb->prefix."product_list` WHERE `id`=".$_POST['prodid']." LIMIT 1";
      $imagedata = $wpdb->get_results($imagesql,ARRAY_A);
      if($imagedata[0]['image'] != '')
        {
        $imagepath = $imagedir . $imagedata[0]['image'];
        switch($_POST['image_resize'])
          {
		  case 0:
		  $height = get_option('product_image_height');
		  $width  = get_option('product_image_width');
		  break;
		  
          case 1:
          $height = get_option('product_image_height');
          $width  = get_option('product_image_width');
          break;
  
          case 2:
          $height = $_POST['height'];
          $width  = $_POST['width'];
          break;
          }
        include("image_resize.php");
        }
      }
    
    if(is_numeric($_POST['prodid']))
      {
      $counter = 0;
      $item_list = '';
      if(count($_POST['category']) > 0)
        {
        foreach($_POST['category'] as $category_id)
          {
          $check_existing = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."item_category_associations` WHERE `product_id` = ".$id." AND `category_id` = '$category_id' LIMIT 1");
          if($check_existing == null)
            {
            $wpdb->query("INSERT INTO `".$wpdb->prefix."item_category_associations` ( `id` , `product_id` , `category_id` ) VALUES ('', '".$id."', '".$category_id."');");        
            }
          if($counter > 0)
            {
            $item_list .= ", ";
            }
          $item_list .= "'".$category_id."'";
          $counter++;
          }
        }
        else
          {
          $item_list = "'0'";
          }
      $wpdb->query("DELETE FROM `".$wpdb->prefix."item_category_associations` WHERE `product_id`= '$id' AND `category_id` NOT IN (".$item_list.")"); 
      }
    
   if(isset($_POST['quantity']) && is_numeric($_POST['quantity']) && ($_POST['quantity_limited'] == "yes"))
     {
     $quantity_limited = 1;
     $quantity = $_POST['quantity'];
     }
     else
       {
       $quantity_limited = 0;
       $quantity = 0;
       }
     
       
    if(isset($_POST['special']) && $_POST['special'] == 'yes') {
		  $special = 1;
		 if(is_numeric($_POST['special_price']))
		   {
		   $special_price = $_POST['price'] - $_POST['special_price'];
		   }
      }
      else {
			$special = 0;
			$special_price = '';
        }
  
    if(isset($_POST['notax']) && $_POST['notax'] == 'yes')
      {
      $notax = 1;
      }
      else
        {
        $notax = 0;
        }

     
      
   if(isset($_POST['display_frontpage']) && $_POST['display_frontpage'] == "yes")
     {
     $display_frontpage = 1;
     }
     else
       {
       $display_frontpage = 0;
       }

	$visible = '0';
	if (isset($_POST['visible']) && $_POST['visible'] == 'on')
		$visible = '1'; 

        if (isset($_POST['price'])){$_price=$_POST['price'];}else{$_price='';}
        if (isset($_POST['pnp'])){$_pnp=$_POST['pnp'];}else{$_pnp='';}
        if (isset($_POST['international_pnp'])){$_international_pnp=$_POST['international_pnp'];}else{$_international_pnp='';}
        if (isset($_POST['quantity_limited'])){$_quantity_limited=$_POST['quantity_limited'];}else{$_quantity_limited=0;}
        if (isset($_POST['quantity'])){$_quantity=$_POST['quantity'];}else{$_quantity=1;}
        
      $updatesql = "UPDATE `".$wpdb->prefix."product_list` SET `name` = '".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['title'])))."', `description` = '".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['description'])))."', `additional_description` = '".$wpdb->escape(removeCrLf(htmlspecialchars($_POST['additional_description'])))."', `price` = '".$wpdb->escape(str_replace(",","",$_price))."', `pnp` = '".$wpdb->escape($_pnp)."', `international_pnp` = '".$wpdb->escape($_international_pnp)."', `category` = '".$wpdb->escape($_POST['category'])."', `brand` = '".$wpdb->escape($_POST['brand'])."', quantity_limited = '".$_quantity_limited."', `quantity` = '".$_quantity."', `special`='$special', `special_price`='$special_price', `display_frontpage`='$display_frontpage', `notax`='$notax', `visible`='$visible'  WHERE `id`='".$_POST['prodid']."' LIMIT 1";
      //exit("<pre>".print_r($updatesql,true)."</pre>");
      $wpdb->query($updatesql);
      if($image != null)
        {
        $updatesql2 = "UPDATE `".$wpdb->prefix."product_list` SET `image` = '".$image."' WHERE `id`='".$_POST['prodid']."' LIMIT 1";
        $wpdb->query($updatesql2);
        }
      if(isset($_POST['deleteimage']) && $_POST['deleteimage'] == 1)
        {
        $updatesql2 = "UPDATE `".$wpdb->prefix."product_list` SET `image` = ''  WHERE `id`='".$_POST['prodid']."' LIMIT 1";
        $wpdb->query($updatesql2);
        }
     
     $variations_procesor = new nzshpcrt_variations;
     if(isset($_POST['variation_values']) && $_POST['variation_values'] != null)
       {
       $variations_procesor->add_to_existing_product($_POST['prodid'],$_POST['variation_values']); 
       }
     
     if(isset($_POST['edit_variation_values']) && $_POST['edit_variation_values'] != null)
       {
       $variations_procesor->edit_product_values($_POST['prodid'],$_POST['edit_variation_values']);
       }
     
     if(isset($_POST['edit_add_variation_values']) && $_POST['edit_add_variation_values'] != null)
       {
       $variations_procesor->edit_add_product_values($_POST['prodid'],$_POST['edit_add_variation_values']);
       }
     
     echo "<div class='updated'><p align='center'>".TXT_WPSC_PRODUCTHASBEENEDITED."</p></div>";
     }
  }

if(isset($_GET['deleteid']) && is_numeric($_GET['deleteid']))
  {
  $deletesql = "UPDATE `".$wpdb->prefix."product_list` SET  `active` = '0' WHERE `id`='".$_GET['deleteid']."' LIMIT 1";
  $wpdb->query($deletesql);
  }
  
  
/*
 * Gets the product list, commented to make it stick out more, as it is hard to notice 
 */

$items_on_page = 10;
if(isset($_GET['offset']) && is_numeric($_GET['offset']))
	{
		$offset = $_GET['offset'];
	}
	else
	{
		$offset = 0;
	}

if(current_user_can('publish_posts'))
	$visiblesql = " ";
else
	$visiblesql = " AND `wp_product_list`.`visible`='1' ";

if(isset($_GET['catid']) && is_numeric($_GET['catid']))
  {
  // if we are getting items from only one category
  $sql = "SELECT `wp_product_list`.*,`wp_item_category_associations`.`category_id` AS `category_id` FROM `wp_product_list`, `wp_item_category_associations`  WHERE `wp_product_list`.`active`='1' ".$visiblesql." AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_item_category_associations`.`category_id`='".$_GET['catid']."' order by wp_product_list.id DESC LIMIT ".$offset.",".$items_on_page;

   $category_count = $wpdb->get_results("SELECT COUNT(*) as count FROM `wp_product_list`,`wp_item_category_associations` WHERE `wp_product_list`.`active`='1' ".$visiblesql." AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_item_category_associations`.`category_id`='".$_GET['catid']."'",ARRAY_A);
   }
   else if (isset($_GET['catid']) && is_numeric($_GET['brand']))
   {
		// if we are getting items from only one brand
		$sql = "SELECT `wp_product_list`.* FROM `wp_product_list`  WHERE `wp_product_list`.`active`='1' ".$visiblesql." AND `wp_product_list`.`brand`='".$_GET['brand']."' order by wp_product_list.id DESC LIMIT ".$offset.",".$items_on_page;

		$category_count = $wpdb->get_results("SELECT COUNT(*) as count FROM `wp_product_list` WHERE `wp_product_list`.`active`='1' ".$visiblesql." AND `wp_product_list`.`brand`='".$_GET['brand']."'",ARRAY_A);
   }
  else
    {
    // if not, get everything that is not deleted (denoted by the active column, 1 = present, 0 = deleted, no real deletion because that would screw up the product log)
    $sql = "SELECT `wp_product_list`.*, `wp_item_category_associations`.`category_id` AS `category_id` FROM `wp_product_list`, `wp_item_category_associations`  WHERE `wp_product_list`.`active`='1' ".$visiblesql." AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` order by wp_product_list.id DESC LIMIT ".$offset.",".$items_on_page;

	$category_count = $wpdb->get_results("SELECT COUNT(*) as count FROM `wp_product_list`,`wp_item_category_associations` WHERE `wp_product_list`.`active`='1' ".$visiblesql." AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id`;",ARRAY_A);

    }
$product_list = $wpdb->get_results($sql,ARRAY_A) ;
/*
 * The product list is stored in $product_list now
 */

	$items_count = $category_count[0]['count'];
	//if ($items_count==0){$offset=0;}
?>

<div class="wrap">
  <h2><?php echo TXT_WPSC_DISPLAYPRODUCTS;?></h2>
  <a href="" onclick="return showaddform()" class="add_item_link"><img src="../wp-content/plugins/wp-shopping-cart/images/package_add.png" alt="Добавить в базу данных" title="Добавить в базу данных">&nbsp;<span>Добавить новое изображение в базу данных (очистить форму)</span></a><br>
  
  <?php
  echo topcategorylist($offset);
  echo (" или ".al_brandslist());

  ?>

№:<input type="text" value="000" id='editpicid' style="width:60px;">
<a href="#" class="button add-new-h2" onclick="var editpicid=document.getElementById('editpicid').value;filleditform(editpicid);return false;">Редактировать по номеру</a>

  <script language='javascript' type='text/javascript'>
function conf()
  {
  var check = confirm("<?php echo TXT_WPSC_SURETODELETEPRODUCT;?>");
  if(check)
    {
    return true;
  }
  else
    {
    return false;
    }
  }
<?php
if(isset($_POST['prodid']) && is_numeric($_POST['prodid']))
  {
  echo "filleditform(".$_POST['prodid'].");";
  }
  else if(isset ($_GET['product_id']) && is_numeric($_GET['product_id']))
    {
    echo "filleditform(".$_GET['product_id'].");";
    }
  
//echo $display_added_product ;
?>
</script>
  <?php
  
if($product_list != null){
	$shawn = sizeof($product_list);
}

$num = 1;
if (isset($_GET['brand'])){$_brand = $_GET['brand'];}else{$_brand = '';}
if (isset($_GET['catid'])){$_category = $_GET['catid'];}else{$_category = '';}

			 //pagination links
				$output = "<span id='pagination'><br>";
				$from_num = 0;
				$to_num = 0;
				if(($offset >= $items_on_page) && ($items_count>0))
				{
					// "Previous page" link
					$offset_back = $offset - $items_on_page;
					$output .= "<a href='admin.php?page=wp-shopping-cart/display-items.php&brand=".$_GET['brand']."&category=".$_GET['catid']."&offset=".$offset_back."'><< ".TXT_WPSC_PREV_PAGE."</a>&nbsp;|&nbsp;";
				}
				if(($offset < $items_count - $items_on_page) && ($items_count>0))
				{
					// "Next page" link
					$offset_forw = $offset + $items_on_page;
					$output .= "<a href='admin.php?page=wp-shopping-cart/display-items.php&brand=".$_brand."&category=".$_category."&offset=".$offset_forw."'>".TXT_WPSC_NEXT_PAGE." >></a>";
				}
				$output .= "</br></div>";

echo "    <table id='productpage'>\n\r";
echo "      <tr><td valign='top'>\n\r";
echo "        <table id='itemlist' style='width:200px;background-color:#FFFFFF;'>\n\r";
echo "          <tr>\n\r";
echo "            <td colspan='6' style='text-align: left;'>\n\r";
$from_num = $offset+1;
$to_num = $from_num + $items_on_page;
if($to_num>$items_count){$to_num=$items_count;}
if($items_count>0)
{
	$diapazon = " (показаны <strong>".$from_num."-".$to_num."</strong> из <strong>".$items_count."</strong>)";
}
echo "<strong class='form_group' style='font-size:10px;'>".TXT_WPSC_SELECT_PRODUCT."</strong>".$diapazon;
echo $output; 
echo "            </td>\n\r";
echo "          </tr>\n\r";
echo "          <tr class='firstrow'>\n\r";

echo "            <td>\n\r";
echo "#";
echo "            </td>\n\r";

echo "            <td>\n\r";
//echo TXT_WPSC_IMAGE;

echo "            </td>\n\r";

echo "            <td>\n\r";
echo TXT_WPSC_NAME;
echo "            </td>\n\r";

//echo "            <td>\n\r";
//echo TXT_WPSC_PRICE;
//echo "            </td>\n\r";

echo "            <td>\n\r";
//echo TXT_WPSC_CATEGORY;
echo "            </td>\n\r";

echo "            <td>\n\r";
//echo TXT_WPSC_EDIT;
echo "Ред.";
echo "            </td>\n\r";

echo "            <td>\n\r";
//echo "del";
echo "            </td>\n\r";

echo "          </tr>\n\r";
if($product_list != null)
  {
  foreach($product_list as $product)
    {
    echo "          <tr>\n\r";

    echo "            <td style='background-color:#DFEFCF;'>\n\r";
    echo $num+$offset;
    echo "            </td>\n\r";

	echo "            <td style='background-color:#DFEFCF;'>\n\r";
    //$basepath = str_replace("/wp-admin", "" , getcwd());  this defined at the top of the page

    $imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/images/";
    if(file_exists($imagedir.$product['image']))
      {
      echo "<img src='../wp-content/plugins/wp-shopping-cart/images/".$product['image']."' title='".$product['name']."' alt='".$product['name']."' width='35' height='35' />";
      }
      else
        {
					echo "<br><img src='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' title='".$product['name']."' alt='".$product['name']."' width='35' height='35'  /><br>";
		  }
    echo "            </td>\n\r";
    
    echo "            <td style='font-size:10px;background-color:#DFEFCF;'>\n\r";
	echo "№ ".$product['id']." ";
    echo "<b>".stripslashes($product['name'])."</b> (".$category_data[$product['category_id']].")";
    echo "            </td>\n\r";

    //echo "            <td>\n\r";
    //echo nzshpcrt_currency_display($product['price'], 1);
    //echo "            </td>\n\r";
    
    echo "            <td style='font-size:10px;background-color:#DFEFCF;'>\n\r";
    //echo "".$category_data[$product['category_id']]."";
    echo "            </td>\n\r";

    echo "            <td style='font-size:10px;background-color:#DFEFCF;'>\n\r";
    echo "<a href='#' onclick='filleditform(".$product['id'].");return false;'>".TXT_WPSC_EDIT."</a>";
    echo "<br><a href='admin.php?page=wp-shopping-cart/display-items.php&deleteid=".$product['id']."' onclick='return conf();'>del</a>";
	echo "            </td>\n\r";

	echo "            <td style='font-size:10px;'>\n\r";
    //echo "<a href='admin.php?page=wp-shopping-cart/display-items.php&deleteid=".$product['id']."' onclick='return conf();'>del</a>";
    echo "            </td>\n\r";
    
    echo "          </tr>\n\r";
	$num ++;
    }
  }
echo "        </table>\n\r";
echo "      </td><td class='secondcol' valign='top' style='width:370px;background-color:#FFFF99'>\n\r";
?>
<div style='color:#660066'><b>Это форма для отправки новой картинки и правки старой</b></div>
<?

echo "        <div id='productform' style='background-color:#FFFF99;'>";
echo "<form method='POST'  id='editproductformtop' enctype='multipart/form-data' name='editproduct$num'>";
echo "        <table class='producttext'>\n\r";;    

echo "        </table>\n\r";
echo "        <div id='formcontent'>\n\r";
echo "        </div>\n\r";
echo "</form>";
echo "        </div>";

?>
<div id='additem'>
  <form  id='editproductform' method='POST' enctype='multipart/form-data'>
<!--   action='admin.php?page=wp-shopping-cart/display-items.php' -->
  <table class='additem' width='500'>
  <?
if(function_exists('add_multiple_image_form'))
  {
  echo add_multiple_image_form(); 
  }
?>
	<tr>
      <td>
        <?php echo TXT_WPSC_CHOOSEABRAND;?>:
      </td>
      <td>
        <?php echo brandslist(); ?>
      </td>
    </tr>
	<tr>
      <td colspan='2'>
        <strong class='form_group'><?php echo TXT_WPSC_PRODUCTDOWNLOAD;//Файл для печати?></strong>
      </td>
    </tr>
    <tr>
      <td>
        <?php echo TXT_WPSC_DOWNLOADABLEPRODUCT;//kartinka?>:
      </td>
      <td>
        <input id='fileupload' type='file' name='file' value='' />
      </td>
    </tr>

    <tr>
      <td colspan='2'>
        <strong class='form_group'><?php echo TXT_WPSC_PRODUCTDETAILS;?> <span><?php echo TXT_WPSC_ENTERPRODUCTDETAILSHERE;?></span></strong>
      </td>
    </tr>
    <tr>
      <td class='itemfirstcol'>
        <?php echo TXT_WPSC_PRODUCTNAME;?>:
      </td>
      <td>
        <input id='picturename' size='30' type='text' name='name' value='***'  />
      </td>
    </tr>
    <tr>
      <td class='itemfirstcol'>
        <?php echo TXT_WPSC_PRODUCTDESCRIPTION;?>:
      </td>
      <td>
        <textarea id='picturedescription' name='description' cols='40' rows='2'></textarea><br />
      </td>
    </tr>
    <tr>
      <td class='itemfirstcol'>
       <?php echo TXT_WPSC_ADDITIONALPRODUCTDESCRIPTION;?>:
      </td>
      <td>
        <textarea id='tags' name='additional_description' cols='40' rows='2'></textarea><br />
      </td>
    </tr>
    <tr>
      <td class='itemfirstcol'>
       Отображать в магазине
      </td>
      <td>
        <input id='visible' type="checkbox" name="visible" checked="checked"><br />
      </td>
    </tr>
    <tr>
      <td>
        <?php echo TXT_WPSC_CHOOSEACATEGORY;?>:
      </td>
      <td>
        <?php echo categorylist(); ?>
      </td>
    </tr>
    <tr>
      <td>
      </td>
      <td>
        <input type='hidden' name='submit_action' value='add' />
        <input type="button" name='sendit' value='<?php echo TXT_WPSC_ADD;?>' onclick="checkthefields();"/>
        <!-- <input type='submit' name='submit' value='<?php echo TXT_WPSC_ADD;?>' /> -->
      </td>
    </tr>
  </table>
  </form>
  </div>
<?php
echo "      </td></tr>\n\r";
echo "     </table>\n\r"

  ?>
</div>
<?

function topcategorylist($offset)
  {
  global $wpdb,$category_data;
  $options = "";
  $values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `active`='1' ORDER BY `id` ASC",ARRAY_A);
  $url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']."?page=wp-shopping-cart/display-items.php&offset=0";
  $options .= "<option value='$url'>".TXT_WPSC_ALLCATEGORIES."</option>\r\n";
  $selected = '';
  if($values != null)
    {
    foreach($values as $option)
      {
      $category_data[$option['id']] = $option['name'];
      if(isset($_GET['catid']) && $_GET['catid'] == $option['id'])
        {
        $selected = "selected='selected'";
        }
      $options .= "<option $selected value='$url&amp;catid=".$option['id']."'>".$option['name']."</option>\r\n";
      $selected = "";
      }
    }
  $concat = "<select name='category' onChange='categorylist(this.options[this.selectedIndex].value)'>".$options."</select>\r\n";
  return $concat;
  }

/*
//redeclared in wp-shopping-cart.php
function brandslist($current_brand = '')
  {
  global $wpdb;
  $options = "";
  $options .= "<option  $selected value='0'>".TXT_WPSC_SELECTABRAND."</option>\r\n";
  $values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_brands` WHERE `active`='1' ORDER BY `name` ASC",ARRAY_A);
  foreach($values as $option)
    {
    if($curent_category == $option['id'])
      {
      $selected = "selected='selected'";
       }
    $options .= "<option  $selected value='".$option['id']."'>".$option['name']."</option>\r\n";
    $selected = "";
    }
  $concat .= "<select name='brand'>".$options."</select>\r\n";
  return $concat;
  }
*/

function al_brandslist($current_brand = '')
  {
  global $wpdb;
  $options = "";
  $values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_brands` WHERE `active`='1' ORDER BY `name` ASC",ARRAY_A);
  $url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']."?page=wp-shopping-cart/display-items.php&offset=0";
  $options .= "<option value='$url'>".TXT_WPSC_SELECTABRAND."</option>\r\n";
  $selected = '';
  foreach($values as $option)
    {
    if(isset($_GET['brand']) && $_GET['brand'] == $option['id'])
      {
      $selected = "selected='selected'";
       }
      $options .= "<option $selected value='$url&amp;brand=".$option['id']."'>".$option['name']."</option>\r\n";
      $selected = "";
    }
  $concat = "<select name='brand' onChange='categorylist(this.options[this.selectedIndex].value)'>".$options."</select>\r\n";
  return $concat;
  }


/*
// redeclared in wp-shopping-cart.php  
function variationslist($current_variation = '')

    {
    global $wpdb;
    $options = "";
    //$options .= "<option value=''>".TXT_WPSC_SELECTACATEGORY."</option>\r\n";
    $values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_variations` ORDER BY `id` ASC",ARRAY_A);
    $options .= "<option  $selected value='0'>".TXT_WPSC_SELECTAVARIATION."</option>\r\n";
    //$options .= "<option  $selected value='add'>".TXT_WPSC_NEW_VARIATION."</option>\r\n";
    if($values != null)
      {
      foreach($values as $option)
        {
        if($current_brand == $option['id'])
          {
          $selected = "selected='selected'";
          }
        $options .= "<option  $selected value='".$option['id']."'>".$option['name']."</option>\r\n";
        $selected = "";
        }
      }
    $concat .= "<select name='variations' onChange='add_variation_value_list(this.options[this.selectedIndex].value)'>".$options."</select>\r\n";
    return $concat;
 }
*/
 
 function al_watermark($path)
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
	$watermarkpath = $basepath."/wp-content/plugins/wp-shopping-cart/images/watermark.gif";
	$watermark = @imagecreatefromgif($watermarkpath);
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
function al_create_cropped_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality = '85') {
	// Cropped thumbs creation (contributed by: dodo - http://pure-essence.net)
	//global $gallery_root; 
	$gallery_root = '';
	$img_location = $gallery_root.$thatdir.$file;

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
 }
function al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file, $thumb, $resample_quality = '85') {
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
	//	imagedestroy($img); 
	//
	//	if (file_exists($gallery_root.$thatdir.$ifolder.'/'.$file)) {
	//		@chmod($gallery_root.$thatdir.$ifolder, 0777);
	//		@chmod($gallery_root.$thatdir.$ifolder.'/'.$file, 0666);
	//	}
 }
function wtrmark($sourcefile, $watermarkfile) {
   #
   # $sourcefile = Filename of the picture to be watermarked.
   # $watermarkfile = Filename of the 24-bit PNG watermark file.
   #
  
   $watermarkfile_id = imagecreatefrompng($watermarkfile);

   imageAlphaBlending($watermarkfile_id, true);
   imageSaveAlpha($watermarkfile_id, true);

   
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

   $dest_x = ( $sourcefile_width / 2 ) - ( $watermarkfile_width / 2 );
   $dest_y = ( $sourcefile_height / 2 ) - ( $watermarkfile_height / 2 );
  
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

    
    $opacity = 50;
   ImageCopyMerge($sourcefile_id, $watermarkfile_id, $dest_x, $dest_y, 0, 0, $watermarkfile_width, $watermarkfile_height, $opacity);
                       
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
  
 }

?>