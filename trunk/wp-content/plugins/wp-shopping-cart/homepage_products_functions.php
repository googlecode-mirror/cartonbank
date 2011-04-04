<?php
function nszhpcrt_homepage_products($content = '')
{
	global $wpdb;

	if(isset($_GET['page_id']) && $wpdb->escape($_GET['page_id'])=='29')
		{
			return $content;
		}



	if (isset($_GET['brand']) && is_numeric($_GET['brand']))
	{
		$_brand = $_GET['brand'];
	}
	else
	{
		$_brand = 1;
	}

	  $siteurl = get_option('siteurl');
	  if(get_option('permalink_structure') != '')
		{
		$seperator ="?";
		}
		else
		  {
		  $seperator ="&amp;";
		  }
	  $sql = "SELECT * FROM `wp_product_list` WHERE `active`='1'  AND `visible`='1' AND `approved`='1' ORDER BY `id` ASC LIMIT 20";
	  $sql = "SELECT post as ID, wp_product_list.image as image, wp_product_list.name AS title, wp_product_brands.name AS author, COUNT(*) AS votes, SUM(wp_fsr_user.points) AS points, AVG(points)*SQRT(COUNT(*)) AS average, vote_date FROM wp_fsr_user, wp_product_list, wp_product_brands  WHERE wp_fsr_user.post = wp_product_list.id  AND wp_product_list.brand = wp_product_brands.id  AND wp_product_list.active = 1 AND wp_product_list.visible = 1 AND wp_product_list.brand = ".$_brand." GROUP BY 1 ORDER BY 7 DESC, 5 DESC LIMIT 100";
	  $product_list = $wpdb->get_results($sql,ARRAY_A);
		
	  $output = "<div id='homepage_products' class='items'>";
	$output = '';    
	if (isset($product_list[0]))
		  {
			  $output .= "<div id='homepage_products' class='items'>";
	  if (isset($product_list[0]))
		  {
			$output .= "<div><h1>".$product_list[0]['author'].". Сто лучших работ</h1><div style='color:#818181;'>Рейт равен среднему баллу, умноженному на квадратный корень из количества поданных голосов.</div></div>";
		  }
		  }

	  foreach((array)$product_list as $product)
		{
		$output .= "<div class='item'>";
		$output .= "<a href='".get_option('product_list_url').$seperator."cartoonid=".$product['ID']."'>";
		if($product['image'] != '')
		  {
		  //$output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."' title='".$product['name']."' alt='".$product['name']."' />\n\r";
		  $output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' title='".$product['author'].". &quot;".$product['title']."&quot;. Голосов: ".$product['votes'].". Баллов: " . $product['points'] . ". Рейт: " . $product['average'] . "' class='thumb'/>";
		  }
		$output .= "</a>";
		$output .= "</div>\n\r";
		}
	  $output .= "</div>\n\r";
	  $output .= "<br style='clear: left;'>\n\r";
	  
	  return preg_replace("/\[homepage_products\]/", $output, $content);
}

function top_votes($content = '')
{
		  global $wpdb;

		// page_id
		if (isset($_GET['page_id']) && is_numeric($_GET['page_id']))
		{
			$_page_filter = "?page_id=".$_GET['page_id'];
		}
		else
		{
			$_page_filter = "";
		}

		$pageURL = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"].$_page_filter;

		// Brand filter 
		if (isset($_GET['br']) && is_numeric($_GET['br']) && $_GET['br'] != '0')
		{
			$_br = $_GET['br'];
			$_br_filter = " AND wp_product_list.brand = ".$_br. " ";
		}
		else
		{
			$_br = 0;
			$_br_filter = "";
		}

		// Limit filter 
		if (isset($_GET['lim']) && is_numeric($_GET['lim']))
		{
			$_limit = " LIMIT ".$_GET['lim']. " ";
		}
		else
		{
			$_limit = " LIMIT 100 ";
		}

		// Offset filter 
		if (isset($_GET['off']) && is_numeric($_GET['off']))
		{
			$_offset = " OFFSET ".$_GET['off']. " ";
		}
		else
		{
			$_offset = "";
		}



		  $siteurl = get_option('siteurl');
		  if(get_option('permalink_structure') != '')
			{
			$seperator ="?";
			}
			else
			  {
			  $seperator ="&amp;";
			  }

		// ORDER BY filter
		if (isset($_GET['ord']) && is_numeric($_GET['ord']))
		{
			$_order = $_GET['ord'];
		}
		else
		{
			$_order = 0;
		}

	$_order_filter = " ORDER BY rate DESC";
	$_order_description = "Сортировка по рейтингу (произведению среднего балла и корня четвёртой степени из количества голосов). По убыванию.";

	switch ($_order){
		case 11:
			$_order_filter = " ORDER BY id ASC";
			$_order_description = "Сортировка по регистрационному номеру. По возрастанию.";
			break;
		case 12:
			$_order_filter = " ORDER BY id DESC";
			$_order_description = "Сортировка по регистрационному номеру. По убыванию.";
			break;
		case 21:
			$_order_filter = " ORDER BY image ASC";
			$_order_description = "Сортировка по файлу изображения. По возрастанию.";
			break;
		case 22:
			$_order_filter = " ORDER BY image DESC";
			$_order_description = "Сортировка по файлу изображения. По убыванию.";
			break;
		case 31:
			$_order_filter = " ORDER BY title ASC";
			$_order_description = "Сортировка по названию рисунка. По возрастанию.";
			break;
		case 32:
			$_order_filter = " ORDER BY title DESC";
			$_order_description = "Сортировка по названию рисунка. По убыванию.";
			break;
		case 41:
			$_order_filter = " ORDER BY author ASC";
			$_order_description = "Сортировка по имени автора. По возрастанию.";
			break;
		case 42:
			$_order_filter = " ORDER BY author DESC";
			$_order_description = "Сортировка по имени автора. По убыванию.";
			break;
		case 51:
			$_order_filter = " ORDER BY votes ASC";
			$_order_description = "Сортировка по количеству голосов. По возрастанию.";
			break;
		case 52:
			$_order_filter = " ORDER BY votes DESC";
			$_order_description = "Сортировка по количеству голосов. По убыванию.";
			break;
		case 61:
			$_order_filter = " ORDER BY points ASC";
			$_order_description = "Сортировка по количеству баллов. По возрастанию.";
			break;
		case 62:
			$_order_filter = " ORDER BY points DESC";
			$_order_description = "Сортировка по количеству баллов. По убыванию.";
			break;
		case 71:
			$_order_filter = " ORDER BY rate ASC";
			$_order_description = "Сортировка по рейтингу (произведению среднего балла и корня четвёртой степени из количества голосов). По возрастанию.";
			break;
		case 72:
			$_order_filter = " ORDER BY rate DESC";
			$_order_description = "Сортировка по рейтингу (произведению среднего балла и корня четвёртой степени из количества голосов). По убыванию.";
			break;
		case 81:
			$_order_filter = " ORDER BY average ASC, points DESC";
			$_order_description = "Сортировка по среднему баллу. По возрастанию.";
			break;
		case 82:
			$_order_filter = " ORDER BY average DESC, points DESC ";
			$_order_description = "Сортировка по среднему баллу. По убыванию.";
			break;
		case 91:
			$_order_filter = " ORDER BY vote_date ASC";
			$_order_description = "Сортировка по дате голосования. От старых к новым.";
			break;
		case 92:
			$_order_filter = " ORDER BY vote_date DESC";
			$_order_description = "Сортировка по дате голосования. От новых к старым.";
			break;
	}

	$sql = "SELECT 
			post as ID, 
			wp_product_list.image as image, 
			wp_product_list.name AS title, 
			wp_product_brands.name AS author, 
			COUNT(*) AS votes, 
			SUM(wp_fsr_user.points) AS points, 
			AVG(points)*SQRT(SQRT(COUNT(*))) AS rate, 
			AVG(points) as average, 
			vote_date  
				FROM wp_fsr_user, wp_product_list, wp_product_brands 
				WHERE wp_fsr_user.post = wp_product_list.id 
				AND wp_product_list.brand = wp_product_brands.id 
				AND wp_product_list.active = 1
				AND wp_product_list.visible = 1
				".$_br_filter."
				GROUP BY 1
				".$_order_filter."
				".$_limit."
				".$_offset;
		  $product_list = $wpdb->get_results($sql,ARRAY_A);


		  $output = "<div id='homepage_products' class='items'>";
		$output = '';    
		if (isset($product_list[0]))
			  {
				  $output .= "<div id='homepage_products' class='items'>";
		  if (isset($product_list[0]) && $_br == 0)
				  {
					$output .= "<div><h1>Все авторы.";
				  }
				  else
				  {
					$output .= "<div><h1>".$product_list[0]['author'].".";
				  }
				  $output .= " Сто лучших работ</h1><div style='color:white;background-color:#668bb7;padding:2px;padding-left:6px;font-weight:bold;display:block;'>".$_order_description."</div><div style='color:#818181;padding-left:150px;'>Сортировать 
				  <b>по дате</b> последнего голосования: <a style='background-color:#FFDFFA;' href='".$pageURL."&ord=91&br=".$_br."'>старые впереди</a>, <a style='background-color:#DFFFEF;' href='".$pageURL."&ord=92&br=".$_br."'>новые впереди</a>; 
				  <br /><b>по среднему баллу</b>: <a style='background-color:#FFDFFA;' href='".$pageURL."&ord=81&br=".$_br."'>по возрастанию</a>, <a style='background-color:#DFFFEF;' href='".$pageURL."&ord=82&br=".$_br."'>по убыванию</a>; 
				  <br /><b>по количеству баллов</b>: <a style='background-color:#FFDFFA;' href='".$pageURL."&ord=61&br=".$_br."'>по возрастанию</a>, <a style='background-color:#DFFFEF;' href='".$pageURL."&ord=62&br=".$_br."'>по убыванию</a>; 
				  <br /><b>по количеству голосов</b>: <a style='background-color:#FFDFFA;' href='".$pageURL."&ord=51&br=".$_br."'>по возрастанию</a>, <a style='background-color:#DFFFEF;' href='".$pageURL."&ord=52&br=".$_br."'>по убыванию</a>; 
				  <br /><b>по рейтингу</b>: <a style='background-color:#FFDFFA;' href='".$pageURL."&ord=71&br=".$_br."'>по возрастанию</a>, <a style='background-color:#DFFFEF;' href='".$pageURL."&ord=72&br=".$_br."'>по убыванию</a>;
				  </div></div>";
			  }

		  foreach((array)$product_list as $product)
			{
			$output .= "<div class='item'>";
			$output .= "<a href='".get_option('product_list_url').$seperator."cartoonid=".$product['ID']."'>";
			if($product['image'] != '')
			  {
			  //$output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."' title='".$product['name']."' alt='".$product['name']."' />\n\r";
			  $output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' title='".$product['author'].". &quot;".$product['title']."&quot;. Голосов: ".$product['votes'].". Баллов: " . $product['points'] . ". Средний балл: " . round($product['average'],3) .". Рейт: " . round($product['rate'],3) . "' class='thumb'/>";
			  }
			$output .= "</a>";
			$output .= "</div>\n\r";
			}
		  $output .= "</div>\n\r";
		  $output .= "<br style='clear: left;'>\n\r";
		  
		  return preg_replace("/\[top_votes\]/", $output, $content);
}

function last_sales($content = '')
{
		 global $wpdb;

		// page_id
		if (isset($_GET['page_id']) && is_numeric($_GET['page_id']))
		{
			$_page_filter = "?page_id=".$_GET['page_id'];
		}
		else
		{
			$_page_filter = "";
		}

		$pageURL = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"].$_page_filter;

		// Brand filter 
		if (isset($_GET['br']) && is_numeric($_GET['br']) && $_GET['br'] != '0')
		{
			$_br = $_GET['br'];
			$_br_filter = " AND wp_product_list.brand = ".$_br. " ";
		}
		else
		{
			$_br = 0;
			$_br_filter = "";
		}

		// Limit filter 
		if (isset($_GET['lim']) && is_numeric($_GET['lim']))
		{
			$_limit = " LIMIT ".$_GET['lim']. " ";
		}
		else
		{
			$_limit = " LIMIT 100 ";
		}

		// Offset filter 
		if (isset($_GET['off']) && is_numeric($_GET['off']))
		{
			$_offset = " OFFSET ".$_GET['off']. " ";
		}
		else
		{
			$_offset = "";
		}



		  $siteurl = get_option('siteurl');
		  if(get_option('permalink_structure') != '')
			{
			$seperator ="?";
			}
			else
			  {
			  $seperator ="&amp;";
			  }

		// ORDER BY filter
		if (isset($_GET['ord']) && is_numeric($_GET['ord']))
		{
			$_order = $_GET['ord'];
		}
		else
		{
			$_order = 0;
		}

	$_order_filter = " ORDER BY rate DESC";
	$_order_description = "Сортировка по рейтингу (произведению среднего балла и корня четвёртой степени из количества голосов). По убыванию.";

	switch ($_order){
		case 11:
			$_order_filter = " ORDER BY id ASC";
			$_order_description = "Сортировка по регистрационному номеру. По возрастанию.";
			break;
		case 12:
			$_order_filter = " ORDER BY id DESC";
			$_order_description = "Сортировка по регистрационному номеру. По убыванию.";
			break;
		case 21:
			$_order_filter = " ORDER BY image ASC";
			$_order_description = "Сортировка по файлу изображения. По возрастанию.";
			break;
		case 22:
			$_order_filter = " ORDER BY image DESC";
			$_order_description = "Сортировка по файлу изображения. По убыванию.";
			break;
		case 31:
			$_order_filter = " ORDER BY title ASC";
			$_order_description = "Сортировка по названию рисунка. По возрастанию.";
			break;
		case 32:
			$_order_filter = " ORDER BY title DESC";
			$_order_description = "Сортировка по названию рисунка. По убыванию.";
			break;
		case 41:
			$_order_filter = " ORDER BY author ASC";
			$_order_description = "Сортировка по имени автора. По возрастанию.";
			break;
		case 42:
			$_order_filter = " ORDER BY author DESC";
			$_order_description = "Сортировка по имени автора. По убыванию.";
			break;
		case 51:
			$_order_filter = " ORDER BY votes ASC";
			$_order_description = "Сортировка по количеству голосов. По возрастанию.";
			break;
		case 52:
			$_order_filter = " ORDER BY votes DESC";
			$_order_description = "Сортировка по количеству голосов. По убыванию.";
			break;
		case 61:
			$_order_filter = " ORDER BY points ASC";
			$_order_description = "Сортировка по количеству баллов. По возрастанию.";
			break;
		case 62:
			$_order_filter = " ORDER BY points DESC";
			$_order_description = "Сортировка по количеству баллов. По убыванию.";
			break;
		case 71:
			$_order_filter = " ORDER BY rate ASC";
			$_order_description = "Сортировка по рейтингу (произведению среднего балла и корня четвёртой степени из количества голосов). По возрастанию.";
			break;
		case 72:
			$_order_filter = " ORDER BY rate DESC";
			$_order_description = "Сортировка по рейтингу (произведению среднего балла и корня четвёртой степени из количества голосов). По убыванию.";
			break;
		case 81:
			$_order_filter = " ORDER BY average ASC, points DESC";
			$_order_description = "Сортировка по среднему баллу. По возрастанию.";
			break;
		case 82:
			$_order_filter = " ORDER BY average DESC, points DESC ";
			$_order_description = "Сортировка по среднему баллу. По убыванию.";
			break;
		case 91:
			$_order_filter = " ORDER BY vote_date ASC";
			$_order_description = "Сортировка по дате голосования. От старых к новым.";
			break;
		case 92:
			$_order_filter = " ORDER BY vote_date DESC";
			$_order_description = "Сортировка по дате голосования. От новых к старым.";
			break;
	}

	$sql = "SELECT b.id, b.name, p.id as ID, p.image as image, p.name AS title, b.name AS author FROM  `wp_purchase_logs` AS l,  `wp_purchase_statuses` AS s,  `wp_cart_contents` AS c,  `wp_product_list` AS p,  `wp_download_status` AS st,  `wp_product_brands` AS b, `wp_users` AS u WHERE l.`processed` = s.`id`  AND l.id = c.purchaseid AND p.id = c.prodid AND st.purchid = c.purchaseid AND p.brand = b.id AND u.id = l.user_id AND l.user_id !=  '106' AND st.downloads !=  '5' GROUP BY c.license ORDER BY date DESC LIMIT 100";

		  $product_list = $wpdb->get_results($sql,ARRAY_A);


		  $output = "<div id='homepage_products' class='items'>";
		$output = '';    
		if (isset($product_list[0]))
			  {
				  $output .= "<div id='homepage_products' class='items'>";
		  if (isset($product_list[0]) && $_br == 0)
				  {
					$output .= "<div><h1>";
				  }
				  else
				  {
					$output .= "<div><h1>".$product_list[0]['author'].".";
				  }
				  $output .= " Сто последних продаж</h1></div>";

		  foreach((array)$product_list as $product)
			{
			$output .= "<div class='item'>";
			$output .= "<a href='".get_option('product_list_url').$seperator."cartoonid=".$product['ID']."'>";
			if($product['image'] != '')
			  {
			  //$output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."' title='".$product['name']."' alt='".$product['name']."' />\n\r";
			  $output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' title='".$product['author'].". ".$product['title']."' class='thumb'/>";
			  }
			$output .= "</a>";
			$output .= "</div>\n\r";
			}
		  $output .= "</div>\n\r";
		  $output .= "<br style='clear: left;'>\n\r";
		  
		  return preg_replace("/\[last_sales\]/", $output, $content);
}
}
?>
