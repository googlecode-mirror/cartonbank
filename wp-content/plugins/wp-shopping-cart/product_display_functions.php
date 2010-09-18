<?php
//ales
function product_display_paginated($product_list, $group_type, $group_sql = '', $search_sql = '', $offset, $items_on_page)
	{
        
	 global $wpdb, $colorfilter;
	 $siteurl = get_option('siteurl');
	//todo: remove special
    $andcategory = "";
    $category='';
    $num = 0;
    if (isset($_GET['category'])){$_category = $_GET['category'];}else{$_category = '';}

    
    if (isset($_category) and is_numeric($_category) and ($_category != 0))
    {
        $andcategory = " AND `wp_product_categories`.`id`=".$_category." ";
        
        $category=$_category;
    }
    else
    {
        $andcategory = "";
    }
   
	if ($search_sql != '')
	{
		$sql = $search_sql;
	}
	else
	{
		if (isset($_GET['brand']) && $_GET['brand'] == '')
		{
        $sql = "SELECT `wp_product_list` . * , `wp_product_files`.`width` , `wp_product_files`.`height` , `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` AS brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list` , `wp_item_category_associations` , `wp_product_files` , `wp_product_brands` , `wp_product_categories` WHERE `wp_product_list`.`active` = '1' ".$colorfilter." AND `wp_product_list`.`visible` = '1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` $group_sql $andcategory ORDER BY `wp_product_list`.`id` DESC LIMIT ".$offset.",".$items_on_page; 
		}
		else
		{ 
        $sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1' ".$colorfilter." AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` $group_sql $andcategory ORDER BY `wp_product_list`.`id` DESC LIMIT ".$offset.",".$items_on_page; 
		}
	}

	$product_list = $GLOBALS['wpdb']->get_results($sql,ARRAY_A);

	  if($product_list != null)
	  {
		  
		  $preview_mode=1; // display as popup window
 //		  $preview_mode=0; // display as Lightbox slideshow
		  $output = "<div id='items' class='items'>";
		  $counter = 0;
		  foreach($product_list as $product)
		  {
			  if(($product['image'] !=null) and ($counter < $items_on_page))
				{
				  $imagedir = ABSPATH."wp-content/plugins/wp-shopping-cart/product_images/";
				  $image_size = @getimagesize($imagedir.$product['image']);
				  $image_link = "index.php?productid=".$product['id']."&width=".$image_size[0]."&height=".$image_size[1]."";
 
				  // thumbs output
				  if ($preview_mode==1)
					{
					  $output .= "<div id='item' class='item'>"; // start item

	$addtocart = "<form name=$num method=POST action=".get_option('product_list_url')." onsubmit=submitform(this);return false; >";
	$addtocart .= "<input type=hidden name=prodid value=".$product['id'].">";
	$addtocart .= "Добавить в заказ: <input type=image border=0 src=".get_option('siteurl')."/img/cart.gif name=Buy value=".TXT_WPSC_ADDTOCART." />";
	$addtocart .= "</form>" ;

	$vstavka = "document.getElementById('bigpic').innerHTML = '<img src=\'".$siteurl."/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."\'>';";

    // here we prepare data for the BIGPIC preview

	if(stristr($product['image'], 'jpg') != FALSE) {
        $_file_format = 'jpg';
	} 
	if(stristr($product['image'], 'gif') != FALSE) {
        $_file_format = 'gif';
	} 
	if(stristr($product['image'], 'png') != FALSE) {
        $_file_format = 'png';
	} 
	
	$_number = $product['id'];
	$_description = nl2br(stripslashes($product['description']));
	$_size = $product['width']."px X ".$product['height']."px;";
		$_x_sm = round(($product['width']/300)*2.54, 1);
		$_y_sm = round(($product['height']/300)*2.54, 1);
		$_sizesm = $_x_sm." см X ".$_y_sm." см";
	if (isset($product['brandid']))
		{$_brandid = $product['brandid'];}
	else {$_brandid = '';}
	if (isset($product['category_id']))
		{$_category_id = $product['category_id'];}
	else {$_category_id = '';}

	$_author = "<a href=\'".$siteurl."/?page_id=29&brand=".$_brandid."\'>".$product['brand']."</a>";//$product['brand'];
	$_name = $product['name'];
						$_category = "<a href=\'".get_option('product_list_url')."&category=".$_category_id."\'>".$product['kategoria']."</a>";
					//$options .= "<a href='".get_option('product_list_url')."/&category=".$option['id']."'>".stripslashes($option['name'])."</a><br />";

	$_tags = nl2br(stripslashes($product['additional_description']));
	$_tags_array = explode(',',$_tags);
		//$i=0;
		foreach ($_tags_array as $key => $value)
		{
			$_tags_array[$key] = "<a href=\'".get_option('siteurl')."/?page_id=29&cs=".trim($_tags_array[$key])."\'>".trim($_tags_array[$key])."</a>";
		}
	$_tags_imploded = implode(", ", $_tags_array);
	$_tags = $_tags_imploded;


	$_bigpicstrip = "<div style=\'float:left;\'><b>Название: </b>" .$_name."</div> "."<div>№&nbsp;".$_number."&nbsp;<b>".$_author."</b></div>";
						
	$_bigpictext = "<b>Категория: </b><br>".$_category."<br><br><b>Описание: </b> ".$_description."<br><br><b>Тэги: </b><br>".$_tags."<br><br><b>Размер:</b><br>".$_size."<br><span style=\'color:#ACACAC;font-size:0.875em;\'>при печати 300dpi:<br>".$_sizesm."</span><br><br><b>Формат файла: </b><br>".$_file_format;
    $_bigpic =  "<img src=\'".$siteurl."/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."\'>";

// Lisence selection strip under the preview image:
$_bottomstriptext = "<div style=\'text-align:right;width:600px;float:right;\'><form name=\'licenses\' id=\'licenses\' onsubmit=\'submitform(this);return false;\' action=\'".get_option('siteurl')."/?page_id=29\' method=\'POST\'> Выбор лицензии: <input type=\'radio\' name=\'license\' value=\'l1_price\'> ".round($product['l1_price'])."&nbsp;руб. <a target=\'_blank\'href=\'".get_option('siteurl')."/?page_id=238\' title=\'ограниченная\'>[?]</a> <input type=\'radio\' name=\'license\' value=\'l2_price\'> ".round($product['l2_price'])."&nbsp;руб. <a target=\'_blank\'href=\'".get_option('siteurl')."/?page_id=242\' title=\'стандартная\'>[?]</a> <input type=\'radio\' name=\'license\' value=\'l3_price\'> ".round($product['l3_price'])."&nbsp;руб. <a target=\'_blank\'href=\'".get_option('siteurl')."/?page_id=245\' title=\'расширенная\'>[?]</a> <input type=\'hidden\' value=\'".$_number."\' name=\'prodid\'> <input id=\'searchsubmit\' value=\'В заказ\' type=\'submit\'> </form></div>";

	$vstavka = "document.getElementById('bigpic').innerHTML ='".$_bigpic."';";
	$vstavka .= "document.getElementById('bigpictext').innerHTML ='".$_bigpictext."';";
	$vstavka .= "document.getElementById('bigpictopstrip').innerHTML ='".$_bigpicstrip."';";
	$vstavka .= "document.getElementById('bigpicbottomstrip').innerHTML ='".$_bottomstriptext."';";
	
	$output .= "<a href=\"#\"  onclick=\"".$vstavka."\">";
	
$fiilename =ABSPATH.'/wp-content/plugins/wp-shopping-cart/images/'.$product['image'];

if (file_exists($fiilename))
{
		  $output .= "<img src='".$siteurl."/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' title='".$product['name']."' alt='".$product['name']."' class='thumb' />";
}
else
{
		  $output .= "<img src='".$siteurl."/wp-content/plugins/wp-shopping-cart/images/icon-rest.gif' class='thumb' />";
}
					  $output .= "</a>";
					}
					else
					{
						// Lightbox
					  $output .= "<div id='item' class='item'>"; // start item
					  $output .= "<a id='preview_link' href='".$image_link."' rel='lightbox[$num]' class='lightbox_links'>"; 
					  $output .= "<img src='".$siteurl."/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' title='".$product['name']."' alt='".$product['name']."' class='thumb' />";
					  $output .= "</a>";
					}

					$output .= "<a href='#' class='additional_description_link' onclick='return show_additional_description(\"description".$product['id']."\",\"link_icon".$product['id']."\");'>";
					$output .= "<img id='link_icon".$product['id']."' style='margin-right: 3px;border:0;' src='".$siteurl."/wp-content/plugins/wp-shopping-cart/images/icon_window_expand.gif' title='".$product['name']."' alt='".$product['name']."' />";
					$output .= TXT_WPSC_DETAILS."</a>";
						/*pop-up*/
						
						$output .= "<div class='lev2' id='description".$product['id']."'>";
							$output .= "№&nbsp;".$product['id']. " <b>" . stripslashes($product['name'])."</b>";
						    $output .= "<br><span id='size'>".$product['width']."px X ".$product['height']."px</span><br>";
						    $output .= "<span id='title'><i>".stripslashes($product['brand'])."</i></span><br>";
							$output .= "<form name='$num' method='POST' action='".get_option('product_list_url')."&category=".$_category_id."' onsubmit='submitform(this);return false;' >";
							$output .= "<input type='hidden' name='prodid' value='".$product['id']."'>";
							$output .= "Добавить в заказ: <input type='image' border='0' src='".get_option('siteurl')."/img/cart.gif' name='Buy' value='".TXT_WPSC_ADDTOCART."'  />";
							$output .= "</form>" ;
						  //title

						$output .= nl2br(stripslashes($product['description'])) . " <br /></div>";
					$output .= "</div>"; // stop item
				}
				$counter = $counter+1;
		  }
		  $output .= "</div>";
		  return $output;
  }
  // end function output first page
}
/// ales


function product_display_default($product_list, $group_type, $group_sql = '', $search_sql = '')
  {
  global $wpdb, $colorfilter;
  $siteurl = get_option('siteurl');
  
  if(function_exists('ext_shpcrt_search_sql'))
    {
    $search_sql = ext_shpcrt_search_sql();
    if($search_sql != '')
      {
      $sql = "SELECT `".$wpdb->prefix."product_list`.* FROM `".$wpdb->prefix."product_list`,`".$wpdb->prefix."item_category_associations` WHERE `".$wpdb->prefix."product_list`.`active`='1' ".$colorfilter." AND `wp_product_list`.`visible`='1' AND `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."item_category_associations`.`product_id` $search_sql ORDER BY `".$wpdb->prefix."product_list`.`special` DESC";
      }
      else
        {
        $sql = "SELECT `".$wpdb->prefix."product_list`.* FROM `".$wpdb->prefix."product_list`,`".$wpdb->prefix."item_category_associations` WHERE `".$wpdb->prefix."product_list`.`active`='1' ".$colorfilter." AND `wp_product_list`.`visible`='1' AND `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."item_category_associations`.`product_id` $group_sql ORDER BY `".$wpdb->prefix."product_list`.`special` DESC"; 
        }
    }
    else
      {
      $sql = "SELECT `".$wpdb->prefix."product_list`.* FROM `".$wpdb->prefix."product_list`,`".$wpdb->prefix."item_category_associations` WHERE `".$wpdb->prefix."product_list`.`active`='1' ".$colorfilter." AND `wp_product_list`.`visible`='1' AND `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."item_category_associations`.`product_id` $group_sql ORDER BY `".$wpdb->prefix."product_list`.`special` DESC"; 
      }         
        
  $product_list = $GLOBALS['wpdb']->get_results($sql,ARRAY_A);
  
  if($product_list != null)
    {
    $output .= "<table class='productdisplay'>";
    foreach($product_list as $product)
      {
      $num++;
      $output .= "    <tr>";
      if($category_data[0]['fee'] == 0)
        {
        $output .= "      <td class='imagecol' style='vertical-align: top;'>";
        if(get_option('show_thumbnails') == 1)
          {
          if($product['image'] !=null)
            {
            $image_size = @getimagesize($imagedir.$product['image']);
            $image_link = "index.php?productid=".$product['id']."&width=".$image_size[0]."&height=".$image_size[1]."";
            if(function_exists("ext_shpcrt_display_extra_images"))
              {
              $output .= ext_shpcrt_display_extra_images($product['id'],$num);
              }
            $output .= "<a id='preview_link' href='".$image_link."' rel='lightbox[$num]' class='lightbox_links'>";
            $output .= "<img src='".$siteurl."/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' title='".$product['name']."' alt='".$product['name']."' />";
            $output .= "</a>";
            }
            else
              {
              if(get_option('product_image_width') != '')
                {
                $output .= "<img src='".$siteurl."/wp-content/plugins/wp-shopping-cart/no-image-uploaded.gif' title='".$product['name']."' alt='".$product['name']."' width='".get_option('product_image_width')."' height='".get_option('product_image_height')."' />";
                }
                else
                  {
                  $output .= "<img src='".$siteurl."/wp-content/plugins/wp-shopping-cart/no-image-uploaded.gif' title='".$product['name']."' alt='".$product['name']."' />";
                  }
              }
          }
        $output .= "</td>";
        }
      $output .= "      <td class='textcol'>";
      if($product['special'] == 1)
        {
        $special = "<strong class='special'>".TXT_WPSC_SPECIAL." - </strong>";
        }
        else
          {
          $special = "";
          }
      $output .= "<form name='$num' method='POST' action='".get_option('product_list_url')."&category=".$_category."' onsubmit='submitform(this);return false;' >";
      $output .= "<input type='hidden' name='prodid' value='".$product['id']."'>";
      
      $imagedir = ABSPATH."wp-content/plugins/wp-shopping-cart/product_images/";
      $output .= "<div class='producttext'>$special";
      $output .= "<strong>". stripslashes($product['name']) . "</strong>";
      $output .= "<br />";
      /*
      if(($product['image'] != '') && (file_exists($imagedir.$product['image']) === true) && function_exists("getimagesize"))
        {
        $image_size = @getimagesize($imagedir.$product['image']);
        $image_link = "index.php?productid=".$product['id']."&width=".$image_size[0]."&height=".$image_size[1]."";
        $output .= "<div class='producttext'>$special";
        //$output .= $imagedir.$product['image'];
        $output .= "<a id='preview_link' href='".$image_link."' rel='lightbox[$num]' class='lightbox_links'>";
        $output .= "<strong>". stripslashes($product['name']) . "</strong>";
        $output .= "<br />";
        }
        else
          {*/
          //}
      
      if(is_numeric($product['file']) && ($product['file'] > 0))
        {
        $file_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_files` WHERE `id`='".$product['file']."' LIMIT 1",ARRAY_A);
        if(($file_data != null) && ($file_data[0]['mimetype'] == 'audio/mpeg') && (function_exists('listen_button')))
          {
          $output .= listen_button($file_data[0]['idhash']);
          }
        }
      
      
      if($product['description'] != '')
        {
        $output .= nl2br(stripslashes($product['description'])) . "<br />";
        }
        
      if($product['additional_description'] != '')
        {
        
        $output .= "<a href='#' class='additional_description_link' onclick='return show_additional_description(\"additionaldescription".$product['id']."\",\"link_icon".$product['id']."\");'>";
        $output .= "<img id='link_icon".$product['id']."' style='margin-right: 3px;' src='".$siteurl."/wp-content/plugins/wp-shopping-cart/images/icon_window_expand.gif' title='".$product['name']."' alt='".$product['name']."' />";
        $output .= TXT_WPSC_MOREDETAILS."</a>";
        
        //$output .= "<span class='additional_description' id='additionaldescription".$product['id']."'><br />";
		$output .= "<span class='additionaldescription' id='additionaldescription".$product['id']."'><br />";
        $output .= nl2br(stripslashes($product['additional_description'])) . "";
        $output .= "</span><br />";
        }
      
      if($product['special']==1)
        {
        $output .= "<span class='oldprice'>".TXT_WPSC_PRICE.": " . nzshpcrt_currency_display($product['price'], $product['notax']) . "</span><br />";
        $output .= TXT_WPSC_PRICE.": " . nzshpcrt_currency_display($product['price'], $product['notax'],false,$product['id']) . "<br />";
        }
        else
          {
          $output .= TXT_WPSC_PRICE.": " . nzshpcrt_currency_display($product['price'], $product['notax']) . "<br />";
          }
      
      $variations_procesor = new nzshpcrt_variations;
          
      $output .= $variations_procesor->display_product_variations($product['id']);
          
      if(get_option('display_pnp') == 1)
        {
        $output .= TXT_WPSC_PNP.": " . nzshpcrt_currency_display($product['pnp'], 1) . "<br />";
        }
      
      $output .= "<input type='hidden' name='item' value='".$product['id']."' />";
      //AND (`quantity_limited` = '1' AND `quantity` > '0' OR `quantity_limited` = '0' )
      if(($product['quantity_limited'] == 1) && ($product['quantity'] < 1))
        {
        $output .= TXT_WPSC_PRODUCTSOLDOUT."";
        }
        else
          {
          $output .= "<input type='submit' name='Buy' value='".TXT_WPSC_ADDTOCART."'  />";
          }
      if(get_option('product_ratings') == 1)
        {
        $output .= "<div class='product_footer'>";
        
        $output .= "<div class='product_average_vote'>";
        $output .= "<strong>".TXT_WPSC_AVGCUSTREVIEW.":</strong>";
        $output .= nzshpcrt_product_rating($product['id']);
        $output .= "</div>";
        
        $output .= "<div class='product_user_vote'>";
        $vote_output = nzshpcrt_product_vote($product['id'],"onmouseover='hide_save_indicator(\"saved_".$product['id']."_text\");'");
        if($vote_output[1] == 'voted')
          {
          $output .= "<strong><span id='rating_".$product['id']."_text'>".TXT_WPSC_YOURRATING.":</span>";
          $output .= "<span class='rating_saved' id='saved_".$product['id']."_text'> ".TXT_WPSC_RATING_SAVED."</span>";
          $output .= "</strong>";
          }
          else if($vote_output[1] == 'voting')
            {
            $output .= "<strong><span id='rating_".$product['id']."_text'>".TXT_WPSC_RATETHISITEM.":</span>";
            $output .= "<span class='rating_saved' id='saved_".$product['id']."_text'> ".TXT_WPSC_RATING_SAVED."</span>";
            $output .= "</strong>";
            }
        $output .= $vote_output[0];
        $output .= "</div>";
        $output .= "</div>";
        }
      
      $output .= "</div>";
      
      $output .= "</form>";
      $output .= "      </td>\n\r";
      $output .= "    </tr>\n\r";
      }
    $output .= "</table>";
    }
    else
      {
      if($_GET['product_search'] != null)
        {
        $output .= "<br /><strong class='cattitles'>".TXT_WPSC_YOUR_SEARCH_FOR." \"".$_GET['product_search']."\" ".TXT_WPSC_RETURNED_NO_RESULTS."</strong>";
        }
        else
          {
          $output .= "<p>".TXT_WPSC_NOTHING_FOUND."</p>";
		  }
      }
  return $output;
  }
  

function single_product_display($product_id)
  {
  global $wpdb, $colorfilter;
  $output = null;
  $siteurl = get_option('siteurl');
  if(get_option('permalink_structure') != '')
    {
    $seperator ="?";
    }
    else
      {
      $seperator ="&amp;";
      }
  if(is_numeric($product_id))
    {
    $sql = "SELECT `wp_product_list`.*, `wp_product_brands`.`name` as artist FROM `wp_product_list`, `wp_product_brands` WHERE `wp_product_list`.`id`='".$product_id."' AND `wp_product_brands`.`id` = `wp_product_list`.`brand` LIMIT 1";
    $product_list = $wpdb->get_results($sql,ARRAY_A);
    }
  
  if($product_list != null)
    {
    //$output .= "<strong class='cattitles'>".$product_list[0]['name']."</strong>"; 
    $output .= "<table class='productdisplay'>";
	$num = 0;
    foreach((array)$product_list as $product)
      {
      $num++;
      $output .= "<tr class='productdisplay'>";
      if(isset($category_data[0]['fee']) and $category_data[0]['fee'] == 0)
        {
        $output .= "<td class='imagecol' style='vertical-align: top;'>";
        if(get_option('show_thumbnails') == 1)
          {
          if($product['image'] !=null)
              {
              $output .= "<img src='".$siteurl."/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."' title='".$product['name']."' alt='".$product['name']."'  style='border: 1px solid #b2b2b2;'/>";
              }
              else
                {
                if(get_option('product_image_width') != '')
                  {
                  $output .= "<img src='".$siteurl."/wp-content/plugins/wp-shopping-cart/no-image-uploaded.gif' title='".$product['name']."' alt='".$product['name']."' width='".get_option('product_image_width')."' height='".get_option('product_image_height')."'  style='border: 1px solid #b2b2b2;'/>";
                  }
                  else
                    {
                    $output .= "<img src='".$siteurl."/wp-content/plugins/wp-shopping-cart/no-image-uploaded.gif' title='".$product['name']."' alt='".$product['name']."' style='border: 1px solid #b2b2b2;' />";
                    }
                }
          }
        $output .= "</td>";
        }
      $output .= "<td style='productdisplay'>";
	  $output .= "<div class='producttext'><u>Автор:</u><br>".$product_list[0]['artist']."<br>";
	  $output .= "<u>Название:</u><br>".$product_list[0]['name'];
      
	  if (isset($_category))
	  {
		  $cate = $_category;
	  }
	  else
	  {
		  $cate = '';
	  }

	  $output .= "<form name='$num' method='POST' action='".get_option('product_list_url')."&category=".$cate."' onsubmit='submitform(this);return false;' >";
      
      $output .= "<input type='hidden' name='prodid' value='".$product['id']."'>";
      $imagedir = ABSPATH."wp-content/plugins/wp-shopping-cart/product_images/";
      if(($product['image'] != '') && (file_exists($imagedir.$product['image']) === true) && function_exists("getimagesize"))
        {
        $image_size = @getimagesize($imagedir.$product['image']);
        $image_link = "index.php?productid=".$product['id']."&width=".$image_size[0]."&height=".$image_size[1]."";
//        $output .= "<div class='producttext'>$special";
//        $output .= "<a id='preview_link' href='".$image_link."' rel='lightbox[$num]' class='lightbox_links'>";
//        $output .= "</a>";
        }
      if($product['description'] != '')
        {
        $output .= "<u>Описание:</u><br>".nl2br(stripslashes($product['description'])) . "<br />";
        }
      if($product['additional_description'] != '')
        {                
        $output .= "<u>Ключевые слова:</u><br>";
        $output .= nl2br(stripslashes($product['additional_description'])) . "";
        //$output .= "</span><br /><br />";
        }
	    //$output .= TXT_WPSC_PRICE . ": " . nzshpcrt_currency_display($product['price'], $product['notax']) . "<br />"; 
      
      $variations_procesor = new nzshpcrt_variations;
      $output .= $variations_procesor->display_product_variations($product['id']);

      $output .= "</div><input type='hidden' name='item' value='".$product['id']."' />";
      if(($product['quantity_limited'] == 1) && ($product['quantity'] < 1))
        {
        $output .= TXT_WPSC_PRODUCTSOLDOUT."";
        }
        else
          {
          $output .= "<br><input type='submit' class='buy_button' name='Buy' value='".TXT_WPSC_ADDTOCART."'  />";
          }
      if(get_option('product_ratings') == 1)
        { 
			$output .= "<div class='product_footer'>";
			
				$output .= "<div class='product_average_vote'>";
				$output .= "<strong>".TXT_WPSC_AVGCUSTREVIEW.":</strong>";
				$output .= nzshpcrt_product_rating($product['id']);
				$output .= "</div>";
			
				$output .= "<div class='product_user_vote'>";
				$vote_output = nzshpcrt_product_vote($product['id'],"onmouseover='hide_save_indicator(\"saved_".$product['id']."_text\");'");
				if($vote_output[1] == 'voted')
				  {
				  $output .= "<strong><span id='rating_".$product['id']."_text'>".TXT_WPSC_YOURRATING.":</span>";
				  $output .= "<span class='rating_saved' id='saved_".$product['id']."_text'> ".TXT_WPSC_RATING_SAVED."</span>";
				  $output .= "</strong>";
				  }
				  else if($vote_output[1] == 'voting')
					{
					$output .= "<strong><span id='rating_".$product['id']."_text'>".TXT_WPSC_RATETHISITEM.":</span>";
					$output .= "<span class='rating_saved' id='saved_".$product['id']."_text'> ".TXT_WPSC_RATING_SAVED."</span>";
					$output .= "</strong>";
					}
				$output .= $vote_output[0];
				$output .= "</div>";
			$output .= "</div>";
        }
      
      $output .= "</div>";
      $output .= "</form>";
      $output .= "      </td>\n\r";
      $output .= "    </tr>\n\r";
      }
    $output .= "</table>";
    }
    else
      {
      //$output .= "<p>".TXT_WPSC_NOITEMSINTHIS." ".$group_type.".</p>";
      $output .= "<p>".TXT_WPSC_NOTHING_FOUND."</p>";
      }
  return $output;
  }

function get_random_image(){
	global $wpdb, $colorfilter;
	$sql = "SELECT `wp_product_list`.`id` FROM `wp_product_list` WHERE `active`='1' ".$colorfilter." AND `visible`='1' ORDER BY RAND(NOW()) LIMIT 1";
	$product_list = $wpdb->get_results($sql,ARRAY_A);
	echo single_product_display($product_list[0]['id']);
 }
?>