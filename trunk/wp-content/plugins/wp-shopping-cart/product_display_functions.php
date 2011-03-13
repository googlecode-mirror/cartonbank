<?php
//ales
function product_display_paginated($product_list, $group_type, $group_sql = '', $search_sql = '', $offset, $items_on_page)
{
	global $wpdb, $colorfilter;
	$siteurl = get_option('siteurl');
    $andcategory = "";
    $category='';
	if (isset($_GET['category']) && $_GET['category'] == '666')
		{
			$exclude_category_sql = " AND `wp_item_category_associations`.`category_id` != '777' ";
			$approved_or_not = "";
		}
		else
		{
			$exclude_category_sql = " AND `wp_item_category_associations`.`category_id` != '666' AND `wp_item_category_associations`.`category_id` != '777' ";
			$approved_or_not = " AND `wp_product_list`.`approved` = '1' ";
		}

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



// next page function
	isset($_GET['offset'])&&is_numeric($_GET['offset'])?$_offset=$_GET['offset']:$_offset=0;
	$_offset = $_offset + 20;

	isset($_GET['brand'])&&is_numeric($_GET['brand'])?$_brand=$_GET['brand']:$_brand='';

	if (isset($_GET['color'])&&$_GET['color']=='color')
		{$_color = 'color';}
	elseif (isset($_GET['color'])&&$_GET['color']=='bw')
		{$_color = 'bw';}
	elseif (isset($_GET['color'])&&$_GET['color']=='all')
		{$_color = 'all';}
	else {$_color = '';}

	if (isset($_GET['cs']))
		{$_cs = htmlspecialchars($_GET['cs']);}
	else
		{$_cs = '';}
	
	//$_offset = $_offset + 20;

	$javascript_functions ='';
	$javascript_functions .='function next_page(){window.location = "'.get_option('siteurl').'/?page_id=29&brand='.$_brand.'&color='.$_color.'&category='.$category.'&cs='.$_cs.'&offset='.$_offset.'";}';
   
	if ($search_sql != '')
	{
		$sql = $search_sql;
	}
	else
	{
		if (isset($_GET['brand']) && $_GET['brand'] == '')
		{
        //$sql = "SELECT `wp_product_list` . * , `wp_product_files`.`width` , `wp_product_files`.`height` , `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` AS brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list` , `wp_item_category_associations` , `wp_product_files` , `wp_product_brands` , `wp_product_categories` WHERE `wp_product_list`.`active` = '1' ".$colorfilter.$exclude_category_sql.$approved_or_not." AND `wp_product_list`.`visible` = '1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` $group_sql $andcategory ORDER BY `wp_product_list`.`id` DESC LIMIT ".$offset.",".$items_on_page; 

		$sql = "SELECT `wp_product_list`.image, `wp_product_list`.id, `wp_product_list`.description, `wp_product_list`.name, `wp_product_list`.additional_description, `wp_product_list`.l1_price, `wp_product_list`.l2_price, `wp_product_list`.l3_price, `wp_product_list`.not_for_sale, `wp_product_files`.`width` , `wp_product_files`.`height` , `wp_product_brands`.`id` AS brandid, `wp_product_brands`.`name` AS brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`, `wp_item_category_associations`, `wp_product_files`, `wp_product_brands` , `wp_product_categories` WHERE `wp_product_list`.`active` = '1' ". $colorfilter. $exclude_category_sql. $approved_or_not. " AND `wp_product_list`.`visible` = '1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id`   ORDER BY `wp_product_list`.`id` desc LIMIT ".$offset.",".$items_on_page;
		}
		else
		{ 
        $sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1' ".$colorfilter.$exclude_category_sql.$approved_or_not." AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` $group_sql $andcategory ORDER BY `wp_product_list`.`id` DESC LIMIT ".$offset.",".$items_on_page; 

		
		}
	}
					// we inject here direct link to the image
					// $_GET['cartoonid'] : &cartoonid=666
					if(isset($_GET['cartoonid']) && is_numeric($_GET['cartoonid']) && $_GET['cartoonid']!='' )
					{
						//echo("<pre>_cartoon_id ".print_r($_GET['cartoonid'],true)."</pre>");
						$_cartoon_id = $_GET['cartoonid'];
						$search_sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`name` as brand, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`id` = ".$_cartoon_id.$approved_or_not." AND `wp_product_list`.`active`='1' AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` ".$exclude_category_sql." AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id`  ORDER BY `wp_product_files`.`date` DESC "; 
					
                    $sql = $search_sql;
                    }



if (isset($_GET['category']) && $_GET['category'] == '777')
{
$sql = "
		SELECT  `wp_product_list` . * ,  `wp_product_files`.`width` ,  `wp_product_files`.`height` ,  `wp_product_brands`.`id` AS brandid,  `wp_product_brands`.`name` AS brand, `wp_item_category_associations`.`category_id` ,  `wp_product_categories`.`name` AS kategoria, `tema_dnya`.`datetime`
		FROM  
		`wp_product_list` 
		LEFT JOIN 
		`wp_item_category_associations` ON `wp_product_list`.`id` =  `wp_item_category_associations`.`product_id`  
		LEFT JOIN 
		`wp_product_files` ON `wp_product_list`.`file` =  `wp_product_files`.`id`
		LEFT JOIN 
		`wp_product_brands` ON `wp_product_brands`.`id` =  `wp_product_list`.`brand`
		LEFT JOIN 
		`wp_product_categories` ON `wp_item_category_associations`.`category_id` =  `wp_product_categories`.`id`
		LEFT JOIN `tema_dnya` ON wp_product_list.id =  tema_dnya.id 
		WHERE  
		`wp_product_list`.`active` =  '1'
		AND  `wp_product_list`.`approved` =  '1'
		AND  `wp_product_list`.`visible` =  '1'
		AND  `wp_item_category_associations`.`category_id` =  '777'
		AND  `wp_product_categories`.`id` = 777
		ORDER BY  tema_dnya.datetime DESC, `wp_product_list`.`id` DESC 
		LIMIT 0 , 20
";

//pokazh($sql,"search_sql: ");

}


//pokazh ($sql);

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

    $output .= "<div id='item".$counter."' class='item'>"; // start item
	
	//$addtocart = "<form name=$num method=POST action=".get_option('product_list_url')." onsubmit=submitform(this);return false; >";
	//$addtocart .= "<input type=hidden name=prodid value=".$product['id'].">";
	//$addtocart .= "Добавить в заказ: <input type=image border=0 src=".get_option('siteurl')."/img/cart.gif name=Buy value=".TXT_WPSC_ADDTOCART." />";
	//$addtocart .= "</form>" ;

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

	$_size_warning = '';
	if ($product['height']<800 || $product['width']<800)
		$_size_warning = "<div style=\'float:left;width:286px;padding-top:8px;font-size:0.8em;\'><a style=\'color:red;\' href=\'".get_option('siteurl')."/?page_id=771\'>Внимание! Размеры файла<br>ограничивают применение!</a></div>";


	if (isset($product['brandid']))
		{$_brandid = $product['brandid'];}
	else {$_brandid = '';}
	if (isset($product['category_id']))
		{$_category_id = $product['category_id'];}
	else {$_category_id = '';}

	$_author = "<a href=\'".$siteurl."/?page_id=29&brand=".$_brandid."\'>".$product['brand']."</a>";//$product['brand'];
	$_name = nl2br(stripslashes($product['name']));

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

	if (function_exists('five_star_rating_func_2'))
		$_rating_html = five_star_rating_func_2($_number);
	else
		$_rating_html = "";

	$_rating_html = str_replace("\"","\'",$_rating_html);
	$_rating_html = str_replace("'","\'",$_rating_html);



	//$_share_it_code = "<br><br><div class='addthis_toolbox addthis_default_style' addthis:url='".get_option('siteurl')."/?page_id=29&cartoonid=".$_number."' addthis:title='Классная картинка!'><a class='addthis_button_preferred_1'></a><a class='addthis_button_preferred_2'></a><a class='addthis_button_preferred_3'></a><a class='addthis_button_preferred_4'></a><a class='addthis_button_compact'></a></div><script type='text/javascript' src='http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4ca706da2e6d8d4d'></script>";

	if (current_user_can('manage_options'))
				{
					//$_edid = " <form method=\'post\' action=\'".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-items.php\'> <input type=\'hidden\' name=\'edid\' value=\'".$_number."\' /> <input type=\'image\'  src=\'".get_option('siteurl')."/img/edit.jpg\' title=\'edit\'></form> <a href=\'".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-items.php&updateimage=".$_number."\' target=_blank\'><img src=\'".get_option('siteurl')."/img/reload.gif\' title=\'image update\'></a>";

					$_edid = " <a href=".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-items.php&edid=".$_number."  target=_blank><img border=0 src=".get_option('siteurl')."/img/edit.jpg title=edit></a> <a href=".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-items.php&updateimage=".$_number." target=_blank><img border=0 src=".get_option('siteurl')."/img/reload.gif title=img></a>";

				}
				else
				{
					$_edid = "";
				}
	
	$_bigpicstrip = "<div style=\'float:left;\'><b>Название: </b>" .$_name."</div> "."<div>№&nbsp;<a id=\'cuid\' title=\'уникальный адрес страницы с этим изображением\' href=\'".get_option('siteurl')."/?page_id=29&cartoonid=".$_number."\'>".$_number."</a>&nbsp;<b>".$_author."</a></b></div>";

	$_bigpictext = "<b>Категория: </b><br>".$_category."<br><br><b>Описание: </b> ".$_description."<br><br><b>Тэги: </b><br>".$_tags."<br><br><b>Ссылка:</b><a title=\'уникальный адрес страницы с этим изображением\' href=\'".get_option('siteurl')."/?page_id=29&cartoonid=".$_number."\'> №&nbsp;".$_number."</a><br><br><b>Размер:</b><br>".$_size."<br><span style=\'color:#ACACAC;font-size:0.875em;\'>при печати 300dpi:<br>".$_sizesm."</span><br><br><b>Формат: </b>".$_file_format."<br><br><b>Оценка:</b><br>".$_rating_html.$_edid;
    $_bigpic =  "<img src=\'".$siteurl."/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."\' border=0>";

	if($product['l1_price']=='0') {$l1_disabled = 'disabled=true';} else {$l1_disabled = '';}
	if($product['l2_price']=='0') {$l2_disabled = 'disabled=true';} else {$l2_disabled = '';}
	if($product['l3_price']=='0') {$l3_disabled = 'disabled=true';} else {$l3_disabled = '';}


if (isset($product['not_for_sale']) && $product['not_for_sale']=='1')
{
	$_bottomstriptext = "Продажа лицензий на данное изображение не разрешена автором";
}
else
{
$_bottomstriptext = $_size_warning."<div style=\'width:450px;float:right;\'><form name=\'licenses\' id=\'licenses\' onsubmit=\'submitform(this);return false;\' action=\'".get_option('siteurl')."/?page_id=29\' method=\'POST\'><table class=\'licenses\'> <tr> <td class=\'wh\' style=\'width:80px;vertical-align:bottom;\'><b>Выбор</b></td> <td class=\'wh\' style=\'text-align:left;\'><input type=\'radio\' name=\'license\' $l1_disabled value=\'l1_price\'></td> <td style=\'vertical-align:middle;text-align:right;\'><b>".round($product['l1_price'])."&nbsp;руб.</b></td> <td rowspan=\'2\' style=\'width:20px;\'>&nbsp;</td> <td class=\'wh\' style=\'text-align:left;\'><input type=\'radio\' name=\'license\' $l2_disabled value=\'l2_price\'></td> <td style=\'vertical-align:middle;text-align:right;\'><b>".round($product['l2_price'])."&nbsp;руб.</b></td> <td rowspan=\'2\' style=\'width:20px;\'>&nbsp;</td> <td class=\'wh\' style=\'text-align:left;\'><input type=\'radio\' name=\'license\' $l3_disabled value=\'l3_price\'></td> <td style=\'vertical-align:middle;text-align:right;\'><b>".round($product['l3_price'])."&nbsp;руб.</b></td> <td rowspan=\'2\' class=\'wh\' style=\'width:80px; text-align:right; vertical-align:bottom;\'><input id=\'searchsubmit\' value=\'В заказ\' type=\'submit\' class=\'borders\'></td> </tr> <tr> <td class=\'wh\' style=\'vertical-align:top;\'><b>лицензии:</b></td> <td colspan=\'2\' style=\'padding-left:6px;\'><a target=\'_blank\'href=\'".get_option('siteurl')."/?page_id=238\' title=\'подробнее об ограниченной лицензии\'>ограниченная</a></td> <td colspan=\'2\' style=\'padding-left:6px;\'><a target=\'_blank\'href=\'".get_option('siteurl')."/?page_id=242\' title=\'подробнее о стандартной лицензии\'>стандартная</a></td> <td colspan=\'2\' style=\'padding-left:6px;\'><a target=\'_blank\'href=\'".get_option('siteurl')."/?page_id=245\' title=\'подробнее об расширенной лицензии\'>расширенная</a></td> </tr> </table><input type=\'hidden\' value=\'".$_number."\' name=\'prodid\'> </form></div>";
}

	$_next_item = $counter + 1;
	if ($_next_item == 20)
		{
			$vstavka = "document.getElementById('bigpic').innerHTML ='<a title=\"следующая страница > \" href=\"#pagetop\" onclick=\"next_page();\">".$_bigpic."</a>';";
		}
		else
		{
			$vstavka = "document.getElementById('bigpic').innerHTML ='<a title=\"следующее изображение > \" href=\"#pagetop\" onclick=\"get_item". $_next_item ."();\">".$_bigpic."</a>';";
		}

	$vstavka .= "document.getElementById('bigpictext').innerHTML ='".$_bigpictext."';";
	$vstavka .= "document.getElementById('bigpictopstrip').innerHTML ='".$_bigpicstrip."';";
	$vstavka .= "document.getElementById('bigpicbottomstrip').innerHTML ='".$_bottomstriptext."';";
	
    $output .= "<a href=\"#pagetop\"  onclick=\"get_item". ($_next_item - 1) ."();\">";
/*
	if ($_next_item == 20)
		{
			$output .= "<a href=\"#\"  onclick=\"alert('last item');next_page();\">";
		}
		else
		{
			$output .= "<a href=\"#pagetop\"  onclick=\"get_item".$_next_item."();\">";
		}

*/	
	$javascript_functions .= " function get_item".$counter."() { ".$vstavka." FSR_starlet();} "; 
	
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

					//$output .= "<a href='#' class='additional_description_link' onclick='return show_additional_description(\"description".$product['id']."\",\"link_icon".$product['id']."\");'>";
					//$output .= "<img id='link_icon".$product['id']."' style='margin-right: 3px;border:0;' src='".$siteurl."/wp-content/plugins/wp-shopping-cart/images/icon_window_expand.gif' title='".$product['name']."' alt='".$product['name']."' />";
					//$output .= "Подробнее!</a>";
						/*pop-up*/
						
						//$output .= "<div class='lev2' id='description".$product['id']."'>";

						    //$output .= "№&nbsp;".$product['id']. " <b>" . stripslashes($product['name'])."</b>";
							//$output .= "<br><span id='size'>".$product['width']."px X ".$product['height']."px</span><br>";
						    //$output .= "<span id='title'><i>".stripslashes($product['brand'])."</i></span><br>";
							//$output .= "<form name='$num' method='POST' action='".get_option('product_list_url')."&category=".$_category_id."' onsubmit='submitform(this);return false;' >";
							//$output .= "<input type='hidden' name='prodid' value='".$product['id']."'>";
							//$output .= "Добавить в заказ: <input type='image' border='0' src='".get_option('siteurl')."/img/cart.gif' name='Buy' value='".TXT_WPSC_ADDTOCART."'  />";
							//$output .= "</form>" ;
						  //title

						//$output .= nl2br(stripslashes($product['description'])) . " <br /></div>";
					$output .= "</div>"; // stop item
				}
				$counter = $counter + 1;
		  }
		  $output .= "</div>";
		  return "<script type=\"text/javascript\" language=\"JavaScript\"> ".$javascript_functions. "</script>" . " ". $output;
  }

  // end function output first page
}
/// ales

function get_random_image(){
	global $wpdb, $colorfilter;
	$sql = "SELECT `wp_product_list`.`id` FROM `wp_product_list` WHERE `active`='1' ".$colorfilter.$exclude_category_sql.$approved_or_not." AND `visible`='1' ORDER BY RAND(NOW()) LIMIT 1";
	$product_list = $wpdb->get_results($sql,ARRAY_A);
	//echo single_product_display($product_list[0]['id']);
 }
?>