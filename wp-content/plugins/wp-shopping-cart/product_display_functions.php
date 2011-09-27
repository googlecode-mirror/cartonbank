<?php
//ales
function product_display_paginated($product_list, $group_type, $group_sql = '', $search_sql = '', $offset, $items_on_page, $orderby = '')
{
	global $wpdb, $colorfilter;

	$siteurl = get_option('siteurl');
    $andcategory = "";
    $category='';
	if (isset($_GET['category']) && $_GET['category'] == '666')
		{
			$exclude_category_sql = " ";
			$approved_or_not = "";
		}
		else
		{
			if  (!isset($_GET['cartoonid']))
			{
				$exclude_category_sql = " AND `wp_product_list`.`category` != '666' ";
			}
			else
			{
				$exclude_category_sql = " ";
			}
			$approved_or_not = " AND `wp_product_list`.`approved` = '1' ";
		}

    $num = 0;
    if (isset($_GET['category']) && is_numeric($_GET['category']) && $_GET['category'] != 0)
		{
			$_category = "&category=".$_GET['category'];
			$andcategory = " AND `wp_product_categories`.`id`=".$_GET['category']." ";
		}
		else
		{
			$_category = '';
			$andcategory = "";
		}
    

if ($orderby == '')
	{
		$orderby = " (`wp_product_list`.`votes_sum`/`wp_product_list`.`votes`)*SQRT(SQRT(`wp_product_list`.`votes`)) DESC ";//best
		//$orderby = " (`wp_product_list`.`votes_sum`/`wp_product_list`.`votes`) DESC, `wp_product_list`.`votes` DESC ";//best
	}
else
	{
		$orderby = " `wp_product_list`.`id` DESC ";//latest
	}


// next page function
	isset($_GET['offset'])&&is_numeric($_GET['offset'])?$_offset=$_GET['offset']:$_offset=0;
	$_offset = $_offset + 20;

	isset($_GET['brand'])&&is_numeric($_GET['brand'])?$_brand="&brand=".$_GET['brand']:$_brand='';

	if (isset($_GET['color'])&&$_GET['color']=='color')
		{$_color = '&color=color';}
	elseif (isset($_GET['color'])&&$_GET['color']=='bw')
		{$_color = '&color=bw';}
	elseif (isset($_GET['color'])&&$_GET['color']=='all')
		{$_color = '&color=all';}
	else {$_color = '';}
/*
	if (isset($_GET['cs']))
		{$_cs = "&cs=".htmlspecialchars($_GET['cs']);}
	else
		{$_cs = '';}
*/
	if (isset($_POST['cs']))
		{$_cs = "&cs=".htmlspecialchars($_POST['cs']);}
	else if (isset($_GET['cs']))
		{$_cs = "&cs=".htmlspecialchars($_GET['cs']);}
	else
		{$_cs = '';}
	
	if (isset($_POST['cs_exact']))
		{$_cs_exact = "&cs_exact=".htmlspecialchars($_POST['cs_exact']);}
	else if (isset($_GET['cs_exact']))
		{$_cs_exact = "&cs_exact=".htmlspecialchars($_GET['cs_exact']);}
	else
		{$_cs_exact = '';}

	if (isset($_POST['cs_any']))
		{$_cs_any = "&cs_any=".htmlspecialchars($_POST['cs_any']);}
	else if (isset($_GET['cs_any']))
		{$_cs_any = "&cs_any=".htmlspecialchars($_GET['cs_any']);}
	else
		{$_cs_any = '';}


	$_new='';

	
	if (isset($_POST['new']) && is_numeric(($_POST['new'])))
		{$_new = "&new=".htmlspecialchars($_POST['new']);}
	else if (isset($_GET['new']) && is_numeric(($_GET['new'])))
		{$_new = "&new=".htmlspecialchars($_GET['new']);}
	else
		{$_new = '';}


	//$_offset = $_offset + 20;

	$javascript_functions ='';
	$javascript_functions .='function next_page(){window.location = "'.get_option('siteurl').'/?page_id=29'.$_brand.$_color.$_new.$_category.$_cs.$_cs_exact.$_cs_any.'&offset='.$_offset.'";	var cuid = document.getElementById("cuid").innerHTML; document.getElementById("navbar").innerHTML = cuid; window.location.hash = "bububu="+cuid; 	}';
   
	if ($search_sql != '')
	{
		$sql = $search_sql;
	}
	else
	{
		if (isset($_GET['brand']) && $_GET['brand'] == '')
		{
			// All artists:

		$sql = "SELECT `wp_product_list`.image, `wp_product_list`.id, `wp_product_list`.description, `wp_product_list`.name, `wp_product_list`.additional_description, `wp_product_list`.l1_price, `wp_product_list`.l2_price, `wp_product_list`.l3_price, `wp_product_list`.not_for_sale, `wp_product_files`.`width` , `wp_product_files`.`height` , `wp_product_brands`.`id` AS brandid, `wp_product_brands`.`avatar_url` AS avatarurl, `wp_product_brands`.`name` AS brand, `wp_product_list`.`category` as category_id, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`, `wp_product_files`, `wp_product_brands` , `wp_product_categories` WHERE `wp_product_list`.`active` = '1' ". $colorfilter. $exclude_category_sql. $approved_or_not. " AND `wp_product_list`.`visible` = '1' AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_product_list`.`category` = `wp_product_categories`.`id`   ORDER BY ".$orderby." LIMIT ".$offset.",".$items_on_page;

		}
		else
		{ 
			// Single Artist

		$sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`avatar_url` AS avatarurl, `wp_product_brands`.`name` as brand, `wp_product_list`.`category` as category_id, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1' ".$colorfilter.$exclude_category_sql.$approved_or_not." AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_product_list`.`category` = `wp_product_categories`.`id` $group_sql $andcategory ORDER BY ".$orderby." LIMIT ".$offset.",".$items_on_page; 

		
		}
	}
					// we inject here direct link to the image
					// $_GET['cartoonid'] : &cartoonid=666
					if(isset($_GET['cartoonid']) && is_numeric($_GET['cartoonid']) && $_GET['cartoonid']!='' )
					{
						//echo("<pre>_cartoon_id ".print_r($_GET['cartoonid'],true)."</pre>");
						$_cartoon_id = $_GET['cartoonid'];

						$search_sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`name` as brand, `wp_product_brands`.`avatar_url` AS avatarurl, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`id` = ".$_cartoon_id.$approved_or_not." AND `wp_product_list`.`active`='1' AND `wp_product_list`.`visible`='1' ".$exclude_category_sql." AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_product_list`.`category` = `wp_product_categories`.`id` ORDER BY ".$orderby; 
					
                    $sql = $search_sql;
                    }

///pokazh($sql);

if (isset($_GET['category']) && $_GET['category'] == '777')
{
	// Tema Dnya:


		// Union to show tema_dnya picture first
		$sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`id` as brandid,`wp_product_brands`.`avatar_url` AS avatarurl,  `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria 
		FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands`, `wp_product_categories`
		WHERE `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` 
		AND `wp_product_list`.`file` = `wp_product_files`.`id` 
		AND `wp_product_brands`.`id` = `wp_product_list`.`brand` 
		AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` 
		AND  wp_product_list.id = (select id from tema_dnya where DATETIME = DATE( NOW( ) ) ) LIMIT 1
		UNION
		SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`avatar_url` AS avatarurl, `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria 
		FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands`, `wp_product_categories`
		WHERE `wp_product_list`.`active`='1'  
		AND `wp_item_category_associations`.`category_id` != '666'  
		AND `wp_product_list`.`approved` = '1'  
		AND `wp_product_list`.`tema_dnya_approved` = '1'  
		AND `wp_product_list`.`visible`='1' 
		AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` 
		AND `wp_product_list`.`file` = `wp_product_files`.`id` 
		AND `wp_product_brands`.`id` = `wp_product_list`.`brand` 
		AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` 
		AND `wp_item_category_associations`.`category_id`='777'  
		AND `wp_product_categories`.`id`=777 
		and wp_product_list.id not in (SELECT id FROM tema_dnya WHERE DATETIME = DATE( NOW( ) ) )";

							//pokazh($sql,"");
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

    $output .= "<div id='item".$counter."' class='item'>"; // start item

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
	$_description = nl2br(hilite(stripslashes($product['description'])));
	$_size = $product['width']."px X ".$product['height']."px;";
		$_x_sm = round(($product['width']/300)*2.54, 1);
		$_y_sm = round(($product['height']/300)*2.54, 1);
		$_sizesm = $_x_sm." см X ".$_y_sm." см";

	$_size_warning = '';
	if ($product['height']<800 || $product['width']<800)
		$_size_warning = "<div style=\'float:left;width:286px;padding-top:8px;font-size:0.8em;\'><a style=\'color:red;\' href=\'".get_option('siteurl')."/?page_id=771\'>Внимание! Размеры файла<br />ограничивают применение!</a></div>";


	if (isset($product['brandid']))
		{$_brandid = $product['brandid'];}
	else {$_brandid = '';}
	if (isset($product['category_id']))
		{$_category_id = $product['category_id'];}
	else {$_category_id = '';}

	$_author = "<a href=\'".$siteurl."/?page_id=29&brand=".$_brandid."\'>".$product['brand']."</a>";//$product['brand'];
	$_name = hilite(nl2br(stripslashes($product['name'])));

	$_avatarurl = ""; //"<a href=\"".get_option('siteurl')."/?page_id=29&brand=$_brandid\"><img src=".$product['avatarurl']." width=32 height=32 align=top border=0></a>";


	$_category = "<a href=\'".get_option('product_list_url')."&category=".$_category_id."\'>".$product['kategoria']."</a>";
	//$options .= "<a href='".get_option('product_list_url')."/&category=".$option['id']."'>".stripslashes($option['name'])."</a><br />";

	$_tags = hilite(nl2br(stripslashes($product['additional_description'])));

	$_bigpicimgalt = addslashes("Карикатура. ".$_name.". ".$_description.". ".$_tags);

	$_tags_array = explode(',',$_tags);
		//$i=0;
		foreach ($_tags_array as $key => $value)
		{
			$_tags_array[$key] = "<a href=\'".get_option('siteurl')."/?page_id=29&cs=".trim($_tags_array[$key])."\'>".trim($_tags_array[$key])."</a>";
		}
	$_tags_imploded = implode(", ", $_tags_array);
	$_tags = $_tags_imploded;

	$_sharethis_html = "<div id=\'share_this\' style=\'line-height:200%;\'></div>";

	$_rating_html = "<div id='star_rating'><img src='".get_option('siteurl')."/img/ldng.gif'></div>";
	$_rating_html = str_replace("\"","\'",$_rating_html);
	$_rating_html = str_replace("'","\'",$_rating_html);



	if (current_user_can('manage_options'))
				{
					$_edid = " <a href=".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-items.php&edid=".$_number."  target=_blank><img border=0 src=".get_option('siteurl')."/img/edit.jpg title=\'открыть редактор\'></a> <a href=".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-items.php&updateimage=".$_number." target=_blank><img border=0 src=".get_option('siteurl')."/img/reload.gif title=\'обновить водяной знак\'></a> <a href=".get_option('siteurl')."/ales/wordassociations/words.php?id=".$_number." target=_blank><img border=0 src=".get_option('siteurl')."/img/tags.gif title=\'добавить тэгов\'></a>";
				}
				else
				{
					$_edid = "";
				}

$current_user = wp_get_current_user();
//	$_SESSION['id']= $current_user->ID;
//	setcookie('uid', $_SESSION['id']);

	//pokazh($_COOKIE,"cookie");
	//pokazh ($_SESSION,"session");
	//pokazh ($current_user);
	///pokazh($_SERVER);

if (is_user_logged_in())
{
	$logged = true; //" залогинен ";
	$klop = "";//"_";
}
else
{
	$logged = false; //" не залогинен ";
	$klop = ""; //"|";
}

	if ($logged)
	{
		$_bigpicstrip = "<div style=\'float:left;\'><b>Название: </b>" .$_name."&nbsp;$klop<span id=\'thumb\' onclick=\'fave_it();\'>$klop<img src=\'http://cartoonbank.ru/img/thumbupp.jpg\' border=0 title=\'добавить в любимое\'></span></div> "."<div>№&nbsp;<a id=\'cuid\' title=\'уникальный адрес страницы с этим изображением\' href=\'".get_option('siteurl')."/?page_id=29&cartoonid=".$_number."\'>".$_number."</a>&nbsp;<b>".$_author."</a></b></div>";
	}
	else
	{
		$_bigpicstrip = "<div style=\'float:left;\'><b>Название: </b>" .$_name." $klop</div> "."<div>№&nbsp;<a id=\'cuid\' title=\'уникальный адрес страницы с этим изображением\' href=\'".get_option('siteurl')."/?page_id=29&cartoonid=".$_number."\'>".$_number."</a>&nbsp;<b>".$_author."</a></b></div>";
	}
	$_bigpictext = "<b>Категория: </b><br />".$_category."<br /><br /><b>Описание: </b> ".$_description."<br /><br /><b>Тэги: </b><br />".$_tags."<br /><br /><b>Ссылка:</b><a title=\'уникальный адрес страницы с этим изображением\' href=\'".get_option('siteurl')."/?page_id=29&cartoonid=".$_number."\'> №&nbsp;".$_number."</a><br /><br /><b>Размер:</b><br />".$_size."<br /><span style=\'color:#ACACAC;font-size:0.875em;\'>при печати 300dpi:<br />".$_sizesm."</span><br /><br /><b>Формат: </b>".$_file_format."<br /><br /><b>Оценка:</b><br />".$_rating_html.$_sharethis_html.$_edid;
    $_bigpic =  "<img src=\'".$siteurl."/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."\' border=0 alt=\'".$_bigpicimgalt."\' />";

	if($product['l1_price']=='0') {$l1_disabled = 'disabled=true';} else {$l1_disabled = '';}
	if($product['l2_price']=='0') {$l2_disabled = 'disabled=true';} else {$l2_disabled = '';}
	if($product['l3_price']=='0') {$l3_disabled = 'disabled=true';} else {$l3_disabled = '';}


if (isset($product['not_for_sale']) && $product['not_for_sale']=='1')
{
	$_bottomstriptext = "Продажа лицензий на данное изображение не разрешена автором";
}
else
{
	// отключить лицензию
	if(isset($product['l1_price']) && $product['l1_price'] != 0)
		{$l1_price_text = "<td style=\'vertical-align:middle;text-align:right;\'><b>".round($product['l1_price'])."&nbsp;руб.</b></td>";}
	else
		{$l1_price_text = "<td style=\'vertical-align:middle;text-align:right;\'>не доступна</td>";}

	if(isset($product['l2_price']) && $product['l2_price'] != 0)
		{$l2_price_text = "<td style=\'vertical-align:middle;text-align:right;\'><b>".round($product['l2_price'])."&nbsp;руб.</b></td>";}
	else
		{$l2_price_text = "<td style=\'vertical-align:middle;text-align:right;\'>не доступна</td>";}

	if(isset($product['l3_price']) && $product['l3_price'] != 0)
		{$l3_price_text = "<td style=\'vertical-align:middle;text-align:right;\'><b>".round($product['l3_price'])."&nbsp;руб.</b></td>";}
	else
		{$l3_price_text = "<td style=\'vertical-align:middle;text-align:right;\'>не доступна</td>";}


$_bottomstriptext = $_size_warning."<div style=\'width:450px;float:right;\'><form name=\'licenses\' id=\'licenses\' onsubmit=\'submitform(this);return false;\' action=\'".get_option('siteurl')."/?page_id=29\' method=\'POST\'><table class=\'licenses\'> <tr> <td class=\'wh\' style=\'width:80px;vertical-align:bottom;\'><b>Выбор</b></td> <td class=\'wh\' style=\'text-align:left;\'><input type=\'radio\' name=\'license\' $l1_disabled value=\'l1_price\'></td> ".$l1_price_text." <td rowspan=\'2\' style=\'width:20px;\'>&nbsp;</td> <td class=\'wh\' style=\'text-align:left;\'><input type=\'radio\' name=\'license\' $l2_disabled value=\'l2_price\'></td> ".$l2_price_text." <td rowspan=\'2\' style=\'width:20px;\'>&nbsp;</td> <td class=\'wh\' style=\'text-align:left;\'><input type=\'radio\' name=\'license\' $l3_disabled value=\'l3_price\'></td> ".$l3_price_text." <td rowspan=\'2\' class=\'wh\' style=\'width:80px; text-align:right; vertical-align:bottom;\'><input id=\'searchsubmit\' value=\'заказать\' type=\'submit\' class=\'borders\' style=\'cursor:pointer;background-color:#FFFF99;padding:6px;margin-bottom:2px;\' title=\'Добавить рисунок в корзину заказов\'></td> </tr> <tr> <td class=\'wh\' style=\'vertical-align:top;\'><b>лицензии:</b></td> <td colspan=\'2\' style=\'padding-left:6px;\'><a target=\'_blank\'href=\'".get_option('siteurl')."/?page_id=238\' title=\'подробнее об ограниченной лицензии\'>ограниченная</a></td> <td colspan=\'2\' style=\'padding-left:6px;\'><a target=\'_blank\'href=\'".get_option('siteurl')."/?page_id=242\' title=\'подробнее о стандартной лицензии\'>стандартная</a></td> <td colspan=\'2\' style=\'padding-left:6px;\'><a target=\'_blank\'href=\'".get_option('siteurl')."/?page_id=245\' title=\'подробнее об расширенной лицензии\'>расширенная</a></td> </tr> </table><input type=\'hidden\' value=\'".$_number."\' name=\'prodid\'> </form></div>";
}

	$_next_item = $counter + 1;
	if ($_next_item == 20)
		{
			$vstavka = "document.getElementById('bigpic').innerHTML ='<a title=\"следующая страница > \" href=\"#pt\" onclick=\"next_page();\">".$_bigpic."</a>';";
		}
		else
		{
			$vstavka = "document.getElementById('bigpic').innerHTML ='<a title=\"следующее изображение > \" href=\"#pt\" onclick=\"get_item". $_next_item ."();\">".$_bigpic."</a>';";
		}

	$vstavka .= "document.getElementById('bigpictext').innerHTML ='".$_bigpictext."';";
	$vstavka .= "document.getElementById('bigpictopstrip').innerHTML ='".$_bigpicstrip."';";
	$vstavka .= "document.getElementById('bigpicbottomstrip').innerHTML ='".$_bottomstriptext."';";
	
    $output .= "<a href=\"#pt\"  onclick=\"get_item". ($_next_item - 1) ."();\">";

	$jq_stars = ' get_5stars(); ';

	$share_this = ' get_share_this(); ';

	$add_hash_2url = ' change_url(); ';

	$get_favorite = ' get_fave(); ';

	$javascript_functions .= " function get_item".$counter."() { ".$vstavka.$jq_stars.$get_favorite.$share_this.$add_hash_2url." } "; 


	
	$fiilename =ABSPATH.'/wp-content/plugins/wp-shopping-cart/images/'.$product['image'];

					if (file_exists($fiilename))
					{
							  $output .= "<img src='".$siteurl."/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' title='".$product['name']."' alt='".$_bigpicimgalt."' class='thumb' />";
					}
					else
					{
							  $output .= "<img src='".$siteurl."/wp-content/plugins/wp-shopping-cart/images/icon-rest.gif' class='thumb' />";
					}
					  $output .= "</a>";

					$output .= "</div>"; // stop item
				}
				$counter = $counter + 1;
		  }
		  $output .= "</div>";
		  return "<script type=\"text/javascript\" language=\"JavaScript\"> get_item0();".$javascript_functions. "</script>" . " ". $output;
  }

  // end function output first page
}
/// ales

function get_random_image(){
	global $wpdb, $colorfilter;
	$sql = "SELECT `wp_product_list`.`id` FROM `wp_product_list` WHERE `active`='1' ".$colorfilter.$exclude_category_sql.$approved_or_not." AND `visible`='1' ORDER BY RAND(NOW()) LIMIT 1";
	$product_list = $wpdb->get_results($sql,ARRAY_A);
 }
?>
