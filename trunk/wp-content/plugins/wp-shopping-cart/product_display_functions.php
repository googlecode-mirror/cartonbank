<?php
//ales
function product_display_paginated($product_list, $group_type, $group_sql = '', $search_sql = '', $offset, $items_on_page)
{
        
	global $wpdb, $colorfilter;
	$siteurl = get_option('siteurl');
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


					// we inject here direct link to the image
					// $_GET['cartoonid'] : &cartoonid=666
					if(isset($_GET['cartoonid']) && is_numeric($_GET['cartoonid']) && $_GET['cartoonid']!='' )
					{
						//echo("<pre>_cartoon_id ".print_r($_GET['cartoonid'],true)."</pre>");
						$_cartoon_id = $_GET['cartoonid'];
						$search_sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`name` as brand, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`id` = ".$_cartoon_id." AND `wp_product_list`.`active`='1' AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id`  ORDER BY `wp_product_list`.`id` DESC "; 
					
                    $sql = $search_sql;
                    }

                    
					//exit("<pre>sql ".print_r($sql,true)."</pre>");



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


	$_bigpicstrip = "<div style=\'float:left;\'><b>Название: </b>" .$_name."</div> "."<div>№№&nbsp;<a title='уникальный адрес страницы с этим изображением' href='".get_option('siteurl')."/?page_id=29&cartoonid=".$_number."'>".$_number."</a>&nbsp;<b>".$_author."</b></div>";
						
	$_bigpictext = "<b>Категория: </b><br>".$_category."<br><br><b>Описание: </b> ".$_description."<br><br><b>Тэги: </b><br>".$_tags."<br><br><b>Размер:</b><br>".$_size."<br><span style=\'color:#ACACAC;font-size:0.875em;\'>при печати 300dpi:<br>".$_sizesm."</span><br><br><b>Формат файла: </b><br>".$_file_format;
    $_bigpic =  "<img src=\'".$siteurl."/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."\'>";

// Lisence selection strip under the preview image:
$_bottomstriptext = "<div style=\'text-align:right;width:600px;float:right;\'><form name=\'licenses\' id=\'licenses\' onsubmit=\'submitform(this);return false;\' action=\'".get_option('siteurl')."/?page_id=29\' method=\'POST\'> Выбор лицензии: <input type=\'radio\' name=\'license\' value=\'l1_price\' checked> ".round($product['l1_price'])."&nbsp;руб. <a target=\'_blank\'href=\'".get_option('siteurl')."/?page_id=238\' title=\'ограниченная\'>[?]</a> <input type=\'radio\' name=\'license\' value=\'l2_price\'> ".round($product['l2_price'])."&nbsp;руб. <a target=\'_blank\'href=\'".get_option('siteurl')."/?page_id=242\' title=\'стандартная\'>[?]</a> <input type=\'radio\' name=\'license\' value=\'l3_price\'> ".round($product['l3_price'])."&nbsp;руб. <a target=\'_blank\'href=\'".get_option('siteurl')."/?page_id=245\' title=\'расширенная\'>[?]</a> <input type=\'hidden\' value=\'".$_number."\' name=\'prodid\'> <input id=\'searchsubmit\' value=\'В заказ\' type=\'submit\'> </form></div>";

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
//exit("<pre>product ".print_r($product,true)."</pre>");
		  return $output;
  }
  // end function output first page
}
/// ales

function get_random_image(){
	global $wpdb, $colorfilter;
	$sql = "SELECT `wp_product_list`.`id` FROM `wp_product_list` WHERE `active`='1' ".$colorfilter." AND `visible`='1' ORDER BY RAND(NOW()) LIMIT 1";
	$product_list = $wpdb->get_results($sql,ARRAY_A);
	//echo single_product_display($product_list[0]['id']);
 }
?>