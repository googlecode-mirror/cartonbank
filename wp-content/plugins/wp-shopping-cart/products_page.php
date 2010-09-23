<?php
global $wpdb, $colorfilter, $color;

// send email from feedback form
if (isset($_REQUEST['email']) && isset($_REQUEST['message']))
{
  $email = $_REQUEST['email'] ;
  $message = $_REQUEST['message'] ;
  mail("igor.aleshin@gmail.com","_REQUEST",print_r($_REQUEST,true));
  mail("cartoonbank.ru@gmail.com", "Письмо от посетителья сайта cartoonbank.ru", $message, "From: $email" );
  //header( "Location: http://www.example.com/thankyou.html" );
}

$siteurl = get_option('siteurl');
$_SESSION['selected_country'] = '';
$brandid = '';
$_bigpictext = '';
$_bigpicstrip = '';
$_bigpic = '';
$_bottomstriptext = '';
$keywords = '';
$seperator ="?";
$portfolio = '';
$bio = '';
$cat_group_sql = '';
$filter_list = '';

// Portfolio filter
if (isset($_GET['portf']) && is_numeric($_GET['portf']) && $_GET['portf'] != '')
	switch ($_GET['portf'])
	{
		case 1:
			// portfolio is active
			$portfolio = 1;
			//$filter_list .= 'портфолио: да ';
			break;
		default:
			$portfolio = 0;
			break;
	}

// Bio filter
if (isset($_GET['bio']) && is_numeric($_GET['bio']) && $_GET['bio'] != '')
	switch ($_GET['bio'])
	{
		case 1:
			// show bio
			$bio = 1;
			//$filter_list .= 'биография: да ';
			break;
		default:
			$bio = 0;
			break;
	}

// Brand filter
if (isset($_GET['brand']) && is_numeric($_GET['brand']))
{
	$_brand = $_GET['brand'];
	$brand_group_sql = " AND `brand`='".$_brand."' ";
	}
	else
		{
			$_brand = '';
			$brand_group_sql = '';
		}
// Color filter
	$color = 'all';

	if((isset($_POST['color']) && $_POST['color']!= '') or (isset($_GET['color']) && $_GET['color']!= ''))
	{
		if(isset($_POST['color']))
		$color = $_POST['color'];

		if(isset($_GET['color']))
		$color = $_GET['color'];

		switch($color)
			{
			case 'color':
			$colorfilter = ' AND `wp_product_list`.`color`=1 '; 
			break;

			case 'bw':
			$colorfilter = ' AND `wp_product_list`.`color`=0 '; 
			break;

			default:
			$colorfilter = '';
			$color = 'all';
			break;
			}
	}
	else
		{
			$colorfilter = '';
		}

if (isset($_GET['category']))
{
	$_category = $_GET['category'];
    $cat_group_sql = " AND `wp_item_category_associations`.`category_id`=".$_category;
	}
	else
	{
		$_category = '';
		}

if(is_numeric($_brand) || (is_numeric(get_option('default_brand')) && (get_option('show_categorybrands') == 3)))
  {
  if(is_numeric($_brand))
    {
    $brandid = $_brand;
    }
    else
      {
      $brandid = get_option('default_brand');
      }
  
  $group_sql = "AND `brand`='".$brandid."'";

  $cat_sql = "SELECT * FROM `wp_product_brands` WHERE `id`='".$brandid."' LIMIT 1";
  $group_type = TXT_WPSC_BRANDNOCAP;
  }
  else if(is_numeric($_category) || (is_numeric(get_option('default_category')) && (get_option('show_categorybrands') != 3)))
    {
    if(is_numeric($_category))
      {
      $catid = $_category;
      }
      else
        {
        //$catid = get_option('default_category');
		$catid = '';
        }

		if ($catid==0)
		{
			$group_sql = "";
			$cat_sql = "SELECT * FROM `wp_product_categories`";
		}
		else
		{
			$group_sql = "AND `wp_item_category_associations`.`category_id`='".$catid."'";
			$cat_sql = "SELECT * FROM `wp_product_categories` WHERE `id`='".$catid."' LIMIT 1";
		}

	$group_type = TXT_WPSC_CATEGORYNOCAP;
    }
    else
      {
      $group_type = TXT_WPSC_BRANDNOCAP;
      }

$category_data = $GLOBALS['wpdb']->get_results($cat_sql,ARRAY_A);


if(isset($_GET['cart']) && $_GET['cart']== 'empty')
  {
  $_SESSION['nzshpcrt_cart'] = '';
  $_SESSION['nzshpcrt_cart'] = Array();
  }

/*
* this is now done by the ajax function
*/
if(isset($_POST['item']) && is_numeric($_POST['item']))
  {
  $cartcount = count($_SESSION['nzshpcrt_cart']);
  $_SESSION['nzshpcrt_cart'][$cartcount + 1] = $_POST['item'];
  }

  $num = 0;
  //else if(is_numeric($_GET['category']) || (is_numeric(get_option('default_category')) && (get_option('show_categorybrands') != 3)))
  if((is_numeric($_category) || is_numeric(get_option('default_category'))) && ((get_option('show_categorybrands') == 1) || (get_option('show_categorybrands') == 2)))
    {
    $display_items = true;
    }
    else if((is_numeric($_brand) || is_numeric(get_option('default_brand'))) && ((get_option('show_categorybrands') == 3) || (get_option('show_categorybrands') == 1)))
      {
      $display_items = true;
      }
      
  if($display_items == true)
    {
			// how many records total?
			if ($_brand == '')
			{
			$sql = "SELECT COUNT(*) as count FROM `wp_product_list`,`wp_item_category_associations` WHERE `wp_product_list`.`active`='1' ".$brand_group_sql.$cat_group_sql." ".$colorfilter." AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`brand` in (SELECT DISTINCT id FROM `wp_product_brands`) AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id`"; 
			}
			else
			{
			$sql = "SELECT COUNT(*) as count FROM `wp_product_list`,`wp_item_category_associations` WHERE `wp_product_list`.`active`='1' ".$brand_group_sql.$cat_group_sql." ".$colorfilter." AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`brand` in (SELECT DISTINCT id FROM `wp_product_brands`) AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id`"; 
			}

			$items_count = $GLOBALS['wpdb']->get_results($sql,ARRAY_A);

			if (is_numeric($items_count[0]['count']))
			{
				$items_count = $items_count[0]['count'];
			}
			else
			{
				$items_count = 0;
			}

$search_sql = NULL;
				$sql = "SELECT `wp_product_list` . * , `wp_product_files`.`width` , `wp_product_files`.`height` , `wp_product_brands`.`id` AS brandid, `wp_product_brands`.`name` AS brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list` , `wp_item_category_associations` , `wp_product_files` , `wp_product_brands` , `wp_product_categories` WHERE `wp_product_list`.`active` = '1' ".$cat_group_sql.$colorfilter." AND `wp_product_list`.`visible` = '1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id`  $group_sql ORDER BY `wp_product_list`.`id` desc LIMIT 1"; 
    
    
                if (isset($_GET['offset']) && is_numeric($_GET['offset']))
                 {
                    $offset = $_GET['offset'];
                 }
                else 
                 {
                    $offset = 0;
                 }

                $items_on_page = get_option('posts_per_page');
    
    
                // SEARCH

                if((isset($_POST['cs']) && $_POST['cs']!= '') or (isset($_GET['cs']) && $_GET['cs']!= ''))
                {
                    if(isset($_POST['cs']) && $_POST['cs']!= ''){
                        $keywords = strtolower(trim($_POST['cs']));
                    }
                    if(isset($_GET['cs']) && $_GET['cs']!= ''){
                        $keywords = strtolower(trim($_GET['cs']));
                    }
					$filter_list .= 'поиск: ('.$keywords.") ";
                    // search request
                    // count found results
					if (isset($_brand) && isset($_brand)!='')
					{
						$search_sql = "SELECT COUNT(*) as count FROM wp_product_list WHERE active='1' ".$cat_group_sql.$brand_group_sql." AND `wp_product_list`.`visible`='1' ".$colorfilter." AND (id LIKE '%".$keywords."%' OR name LIKE '%".$keywords."%' OR description LIKE '%".$keywords."%' OR additional_description LIKE '%".$keywords."%')";
					}
                    else
					{
						$search_sql = "SELECT COUNT(*) as count FROM wp_product_list WHERE active='1' AND `wp_product_list`.`visible`='1' ".$colorfilter." AND (id LIKE '%".$keywords."%' OR name LIKE '%".$keywords."%' OR description LIKE '%".$keywords."%' OR additional_description LIKE '%".$keywords."%')";
					}

                    $items_count = $GLOBALS['wpdb']->get_results($search_sql,ARRAY_A);

                    if (isset($items_count[0]['count']) && is_numeric($items_count[0]['count']))
                    {
                        $items_count = $items_count[0]['count'];
                        // search request
                        //$search_sql = "SELECT * FROM wp_product_list WHERE active='1' AND `wp_product_list`.`visible`='1' AND (name LIKE '%".$keywords."%' OR description LIKE '%".$keywords."%' OR additional_description LIKE '%".$keywords."%')"; 

                        $search_sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`name` as brand, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1' ".$cat_group_sql.$colorfilter." AND `wp_product_list`.`visible`='1' AND (LOWER(`wp_product_list`.`name`) LIKE '%".$keywords."%' OR LOWER(`wp_product_list`.`id`) LIKE '%".$keywords."%' OR LOWER(`wp_product_list`.`description`) LIKE '%".$keywords."%' OR LOWER(`wp_product_list`.`additional_description`) LIKE '%".$keywords."%') AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id`  ORDER BY `wp_product_list`.`id` DESC LIMIT ".$offset.",".$items_on_page; 
                    }
                    else
                    {
                        $items_count = 0;
                        $search_sql ='';
                    }
                $sql = $search_sql;
				}
                else 
                    {
                        $keywords = '';
                    }
	// we inject here direct link to the image
	// $_GET['cartoonid'] : &cartoonid=666
	if(isset($_GET['cartoonid']) && is_numeric($_GET['cartoonid']) && $_GET['cartoonid']!='' )
	{
		//echo("<pre>_cartoon_id ".print_r($_GET['cartoonid'],true)."</pre>");
		$_cartoon_id = $_GET['cartoonid'];
		$search_sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`name` as brand, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`id` = '".$_cartoon_id."' AND `wp_product_list`.`active`='1' AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id`  ORDER BY `wp_product_list`.`id` DESC "; 
	    $sql = $search_sql;
    }

	// список картинок
    $product = $GLOBALS['wpdb']->get_results($sql,ARRAY_A);
     if ($product!=null)
     {   
		 if ($bio == 1 && $brandid > 0) // bio
		 {
		  // display portfolio
			 
			 // prepare content
				
				// Get the Brand (author) data
				$brand_sql = "SELECT * FROM `wp_product_brands` where id = ". $brandid;
				$brand_result  = $GLOBALS['wpdb']->get_results($brand_sql,ARRAY_A);

				// bio

				if (isset($brand_result[0]['bio_post_id']))
				{
				$bio_sql = "SELECT `post_content` FROM `wp_posts` WHERE id = ".$brand_result[0]['bio_post_id'] ; // todo: use page ID!
				$bio = $GLOBALS['wpdb']->get_results($bio_sql,ARRAY_A);
				}
				
				if (isset($bio[0]) ) 
				{
					$bio = $bio[0]['post_content'];
				}
				else 
					{
						$bio = 'Автор исключительно талантливый художник. Больше нам про него пока ничего не известно.<br>Ведём бесконечные переговоры об обновлении этой информации.';
					}

				// avatar url
				/*
				if (isset($brand_result[0]['avatar_url']) && $brand_result[0]['avatar_url'] != '')
				{$avatar_url = "<img width=140 src='".$brand_result[0]['avatar_url']."'>";}
				else {$avatar_url = "<img width=140 src='".get_option('siteurl')."/img/avatar.gif'>";}
				*/
				
				// email form
				//pokazh(get_option('siteurl'),"siteurl");
				$email_form = "<div id='emailform'><form method='post' action='".get_option('siteurl')."/?page_id=29&brand=".$brandid."&bio=1'>
								  Email: <input name='email' type='text' /><br />
								  Message:<br />
								  <textarea style='width:600px;' name='message' rows='15' cols='40'></textarea><br />
								  <input type='submit' />
								</form></div>";
				$email_form = '';

				// contact
				$brand_contact = '';
				if ($brand_result[0]['contact']!='')
				{
					$brand_contact = "<a href='mailto:".$brand_result[0]['contact']."'>написать письмо</a>" ;
				}
					else{$brand_contact=='';}

				$_bigpicstrip = "<b>".$product[0]['brand']. ". Информация об авторе</b>";
				$_bigpictext = "<br><br>".$brand_contact;
				$_bigpic = "<div style='width:600px;'>".$bio."</div>".$email_form; 
				$_bottomstriptext = "<span style='color:#B1B1B1;'>В Авторском разделе дополнительно представлены работы, не предназначенные для продажи, а также не вошедшие в основной раздел (Банк изображений).</span>";

			// end of portfolio
		 } 
		 else
		 {
			// normal workflow: disply big preview image
			// slide preview preparations:
				if(stristr($product[0]['image'], 'jpg') != FALSE) {
						$_file_format = 'jpg';
					} 
					if(stristr($product[0]['image'], 'gif') != FALSE) {
						$_file_format = 'gif'; 
					} 
					if(stristr($product[0]['image'], 'png') != FALSE) {
						$_file_format = 'png';
					} 
				$_number = $product[0]['id'];
                $_description = nl2br(stripslashes($product[0]['description']));
                $_size = $product[0]['width']."px X ".$product[0]['height']."px;";
				$_x_sm = round(($product[0]['width']/300)*2.54, 1);
				$_y_sm = round(($product[0]['height']/300)*2.54, 1);
				$_sizesm = $_x_sm." см X ".$_y_sm." см";
                $_author = $product[0]['brand'];
                $_name = $product[0]['name'];
				if(isset($_GET['brand']) && is_numeric($_GET['brand'])) 
						$filter_list .= "автор: <b>".$_author."</b> ";
				if (isset($product[0]['kategoria']))
				 {
					$_categor = $product[0]['kategoria'];
					if(isset($_GET['category']) && is_numeric($_GET['category'])) 
						$filter_list .= "категория: <b>".$_categor."</b> ";
				 }
				 else
				 {
					$_categor = '';
				 }
				if (isset($product[0]['brandid']))
				 {
					$_brandid = $product[0]['brandid'];
				 }
				 else
				 {
					$_brandid = '';
				 }
						
				$_category = "<a href=\'".get_option('product_list_url')."&category=".$_categor."\'>".$_categor."</a>";


                $_tags = nl2br(stripslashes($product[0]['additional_description']));
                $_tags_array = explode(',',$_tags);
                    //$i=0;
                    foreach ($_tags_array as $key => $value)
                    {
                        $_tags_array[$key] = "<a href=\"".get_option('siteurl')."/?page_id=29&cs=".trim($_tags_array[$key])."\">".trim($_tags_array[$key])."</a>";
                    }
                $_tags_imploded = implode(", ", $_tags_array);
                $_tags = $_tags_imploded;

                $_bigpicstrip = "<div style=\"float:left;\"><b>Название: </b>" .$_name."</div> "."<div>№&nbsp;<a title='уникальный адрес страницы с этим изображением' href='".get_option('siteurl')."/?page_id=29&cartoonid=".$_number."'>".$_number."</a>&nbsp;<b><a href=\"".$siteurl."/?page_id=29&brand=".$_brandid."\">".$_author."</a></b></div>";
                $_bigpictext = "<b>Категория: </b><br>".$_category."<br><br><b>Описание: </b> ".$_description."<br><br><b>Тэги: </b><br>".$_tags."<br><br><b>Размер:</b><br>".$_size."<br><span style='color:#ACACAC;font-size:0.875em;'>при печати 300dpi:<br>".$_sizesm."</span><br><br><b>Формат: </b> ".$_file_format;
                $siteurl = get_option('siteurl');
                $_bigpic =  "<img src=\"".$siteurl."/wp-content/plugins/wp-shopping-cart/product_images/".$product[0]['image']."\">";

				$_bottomstriptext = "<div style='width:450px;float:right;'><form name='licenses' id='licenses' onsubmit='submitform(this);return false;' action='".get_option('siteurl')."/?page_id=29' method='POST'><table class='licenses'>
					  <tr>
						<td class='wh' style='width:80px;vertical-align:bottom;'><b>Выбор</b></td>
						<td class='wh' style='text-align:left;'><input type='radio' name='license' value='l1_price' checked></td>
						<td style='vertical-align:middle;text-align:right;'><b>".round($product[0]['l1_price'])."&nbsp;руб.</b></td>
						<td rowspan='2' style='width:20px;'>&nbsp;</td>
						<td class='wh' style='text-align:left;'><input type='radio' name='license' value='l2_price'></td>
						<td style='vertical-align:middle;text-align:right;'><b>".round($product[0]['l2_price'])."&nbsp;руб.</b></td>
						<td rowspan='2' style='width:20px;'>&nbsp;</td>
						<td class='wh' style='text-align:left;'><input type='radio' name='license' value='l3_price'></td>
						<td style='vertical-align:middle;text-align:right;'><b>".round($product[0]['l3_price'])."&nbsp;руб.</b></td>
						<td rowspan='2' class='wh' style='width:80px; text-align:right; vertical-align:bottom;'><input id='searchsubmit' value='В заказ' type='submit' class='borders'></td>
					  </tr>
					  <tr>
						<td class='wh' style='vertical-align:top;'><b>лицензии:</b></td>
						<td colspan='2' style='padding-left:6px;'><a target='_blank'href='".get_option('siteurl')."/?page_id=238' title='подробнее об ограниченной лицензии'>ограниченная</a></td>
						<td colspan='2' style='padding-left:6px;'><a target='_blank'href='".get_option('siteurl')."/?page_id=242' title='подробнее о стандартной лицензии'>стандартная</a></td>
						<td colspan='2' style='padding-left:6px;'><a target='_blank'href='".get_option('siteurl')."/?page_id=245' title='подробнее об расширенной лицензии'>расширенная</a></td>
					  </tr>
					  </table><input type='hidden' value='".$_number."' name='prodid'>  </form></div>";
		  // end of normal workflow: disply big preview image
		 }
		 
	 }
		else
		{// no products
			if(isset($_GET['cartoonid']) && is_numeric($_GET['cartoonid']) && $_GET['cartoonid']!='' )
              {
                  echo ("<br><br>Изображения с таким номером нет.");
              }
			
		}
			
			 // ales: product page starts here
				// old code to call all items at once
				// echo product_display_default($product_list, $group_type, $group_sql, $search_sql);
                // placeholder for the slide preview
               
               $_bigpictext = str_ireplace("\\'","\"",$_bigpictext);
                
               echo "<div id='bigpictopstrip'>".$_bigpicstrip."</div>";
               echo "<div id='bigpictext'>".$_bigpictext."</div>";
               echo "<div id='bigpic'>".$_bigpic."</div>";
               echo "<div style='clear:both;'></div>";
               echo "<div id='bigpicbottomstrip' style='float:right;'>".$_bottomstriptext."</div>";

                    
                    
				if (isset($_GET['offset']) && is_numeric($_GET['offset']))
				 {
					$offset = $_GET['offset'];
				 }
				else 
				 {
					$offset = 0;
				 }

				$items_on_page = get_option('posts_per_page');

                // непонятно зачем эти параметры?
                $product_list = '';

				if((isset($_POST['cs']) && $_POST['cs']!= '') or (isset($_GET['cs']) && $_GET['cs']!= ''))
                     {$search_sql = $sql;}
				else
					 {$search_sql = '';}

                     
                     
			 // FIRST PAGE icons OUTPUT
              if(isset($_GET['cartoonid']) && is_numeric($_GET['cartoonid']) && $_GET['cartoonid']!='' )
              {
                  echo "";
              }
              else
			  {
              echo product_display_paginated(NULL /* generated notice: always NULL $product_list*/, $group_type, $group_sql, $search_sql, $offset, $items_on_page);
			 
             // PAGINATION
             $offset = $offset + $items_on_page;
			 $page_id = $_GET['page_id'];

			 //pagination links

				$output = "<div id='pagination' class='width:470px;clear:both;'><br>";
				$output .= "Всего найдено изображений: ".$items_count. ". <span style='color:#c0c0c0;'>".$filter_list."</span><br><br></div>";
				echo "<div style='clear:both;'>".$output."<br></div>";

				$page = round($offset/$items_on_page);
				$totalitems = $items_count;
				$limit = $items_on_page;

				if (isset($catid)){$catid=$catid;}else{$catid='';}

				echo "<div style='clear:both;'>".getPaginationString($page, $totalitems, $limit, $adjacents = 1, $targetpage = get_option('siteurl'), $pagestring = "?page_id=29&brand=".$brandid."&color=".$color."&category=".$catid."&cs=".$keywords."&offset=")."<br></div>";
              }
     }


function getPaginationString($page = 1, $totalitems, $limit = 15, $adjacents = 1, $targetpage = "/", $pagestring = "?page=")
{		
	//function to return the pagination string
	//getPaginationString($page = 1, $totalitems, $limit = 15, $adjacents = 1, $targetpage = get_option('siteurl'), $pagestring = "?brand=".$brandid."&category=".$catid."&offset=".$offset."&cs=".$keywords."&page_id=29");
	//defaults
	if(!$adjacents) $adjacents = 1;
	if(!$limit) $limit = 15;
	if(!$page) $page = 1;
	if(!$targetpage) $targetpage = "/";
	
	//other vars
	$prev = $page - 1;									//previous page is page - 1
	$next = $page + 1;									//next page is page + 1
	$lastpage = ceil($totalitems / $limit);				//lastpage is = total items / items per page, rounded up.
	$lpm1 = $lastpage - 1;								//last page minus 1
	
	/* 
		Now we apply our rules and draw the pagination object. 
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	$margin = '2px';
	$padding = '4px';
	if($lastpage > 1)
	{	
		$pagination .= "<div class=\"pagination\"";
		if($margin || $padding)
		{
			$pagination .= " style=\"";
			if($margin)
				$pagination .= "margin: $margin;";
			if($padding)
				$pagination .= "padding: $padding;";
			$pagination .= "\"";
		}
		$pagination .= ">";

		//previous button
		if ($page > 1) 
			$pagination .= "<a href=\"".$targetpage. $pagestring. ($prev*$limit - $limit). "\">« сюда</a>";
		else
			$pagination .= "<span class=\"disabled\">« сюда</span>";	
		
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination .= "<span class=\"current\">$counter</span>";
				else
					$pagination .= "<a href=\"" . $targetpage . $pagestring . ($counter*$limit - $limit) . "\">$counter</a>";					
			}
		}
		elseif($lastpage >= 7 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 1 + ($adjacents * 3))		
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination .= "<span class=\"current\">$counter</span>";
					else
						$pagination .= "<a href=\"" . $targetpage . $pagestring . ($counter*$limit - $limit) . "\">$counter</a>";					
				}
				$pagination .= "<span class=\"elipses\">...</span>";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . ($lpm1*$limit - $limit) . "\">$lpm1</a>";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . ($lastpage*$limit - $limit) . "\">$lastpage</a>";		
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination .= "<a href=\"" . $targetpage . $pagestring . "0\">1</a>";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . "15\">2</a>";
				$pagination .= "<span class=\"elipses\">...</span>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination .= "<span class=\"current\">$counter</span>";
					else
						$pagination .= "<a href=\"" . $targetpage . $pagestring . ($counter*$limit - $limit) . "\">$counter</a>";					
				}
				$pagination .= "...";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . ($lpm1*$limit - $limit) . "\">$lpm1</a>";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . ($lastpage*$limit - $limit) . "\">$lastpage</a>";		
			}
			//close to end; only hide early pages
			else
			{
				$pagination .= "<a href=\"" . $targetpage . $pagestring . "0\">1</a>";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . "15\">2</a>";
				$pagination .= "<span class=\"elipses\">...</span>";
				for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination .= "<span class=\"current\">$counter</span>";
					else
						$pagination .= "<a href=\"" . $targetpage . $pagestring . ($counter*$limit - $limit) . "\">$counter</a>";					
				}
			}
		}
		
		//next button
		if ($page < $counter - 1) 
			$pagination .= "<a href=\"" . $targetpage . $pagestring . ($next*$limit - $limit) . "\">туда »</a>";
		else
			$pagination .= "<span class=\"disabled\">туда »</span>";
		$pagination .= "</div>\n";
	}
	
	return $pagination;

}


function pokazh($to_print)
{
	echo ("<div style='padding:2px;background-color:#FFB366'><pre>".print_r($to_print,true)."</pre></div>"); 
}
  ?>
