<?php
function nszhpcrt_homepage_products($content = '')
{
  global $wpdb;

	if (isset($_GET['brand']) && is_numeric($_GET['brand']))
	{
		$_brand = $_GET['brand'];
	}
	else
	{
		$_brand = 0;
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
	  $sql = "SELECT post as ID, wp_product_list.image as image, wp_product_list.name AS title, wp_product_brands.name AS author, COUNT(*) AS votes, SUM(wp_fsr_user.points) AS points, AVG(points)*SQRT(COUNT(*)) AS average, vote_date  
						FROM wp_fsr_user, wp_product_list, wp_product_brands 
						WHERE wp_fsr_user.post = wp_product_list.id 
						AND wp_product_list.brand = wp_product_brands.id 
						AND wp_product_list.active = 1
						AND wp_product_list.visible = 1
						AND wp_product_list.brand = ".$_brand."
						GROUP BY 1
						ORDER BY 7 DESC, 5 DESC
						LIMIT 100";
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
$_order_description = "Отсортировано по рейтингу (произведению среднего балла и корня четвёртой степени из количества голосов). По убыванию.";

switch ($_order){
	case 11:
		$_order_filter = " ORDER BY id ASC";
		$_order_description = "Отсортировано по регистрационному номеру. По возрастанию.";
		break;
	case 12:
		$_order_filter = " ORDER BY id DESC";
		$_order_description = "Отсортировано по регистрационному номеру. По убыванию.";
		break;
	case 21:
		$_order_filter = " ORDER BY image ASC";
		$_order_description = "Отсортировано по файлу изображения. По возрастанию.";
		break;
	case 22:
		$_order_filter = " ORDER BY image DESC";
		$_order_description = "Отсортировано по файлу изображения. По убыванию.";
		break;
	case 31:
		$_order_filter = " ORDER BY title ASC";
		$_order_description = "Отсортировано по названию рисунка. По возрастанию.";
		break;
	case 32:
		$_order_filter = " ORDER BY title DESC";
		$_order_description = "Отсортировано по названию рисунка. По убыванию.";
		break;
	case 41:
		$_order_filter = " ORDER BY author ASC";
		$_order_description = "Отсортировано по имени автора. По возрастанию.";
		break;
	case 42:
		$_order_filter = " ORDER BY author DESC";
		$_order_description = "Отсортировано по имени автора. По убыванию.";
		break;
	case 51:
		$_order_filter = " ORDER BY votes ASC";
		$_order_description = "Отсортировано по количеству голосов. По возрастанию.";
		break;
	case 52:
		$_order_filter = " ORDER BY votes DESC";
		$_order_description = "Отсортировано по количеству голосов. По убыванию.";
		break;
	case 61:
		$_order_filter = " ORDER BY points ASC";
		$_order_description = "Отсортировано по количеству баллов. По возрастанию.";
		break;
	case 62:
		$_order_filter = " ORDER BY points DESC";
		$_order_description = "Отсортировано по количеству баллов. По убыванию.";
		break;
	case 71:
		$_order_filter = " ORDER BY rate ASC";
		$_order_description = "Отсортировано по рейтигу (произведению среднего балла и корня четвёртой степени из количества голосов). По возрастанию.";
		break;
	case 72:
		$_order_filter = " ORDER BY rate DESC";
		$_order_description = "Отсортировано по рейтингу (произведению среднего балла и корня четвёртой степени из количества голосов). По убыванию.";
		break;
	case 81:
		$_order_filter = " ORDER BY average ASC, points DESC";
		$_order_description = "Отсортировано по среднему баллу. По возрастанию.";
		break;
	case 82:
		$_order_filter = " ORDER BY average DESC, points DESC ";
		$_order_description = "Отсортировано по среднему баллу. По убыванию.";
		break;
	case 91:
		$_order_filter = " ORDER BY vote_date ASC";
		$_order_description = "Отсортировано по дате голосования. По возрастанию.";
		break;
	case 92:
		$_order_filter = " ORDER BY vote_date DESC";
		$_order_description = "Отсортировано по дате голосования. По убыванию.";
		break;
}

$sql = "SELECT 
		post as ID, 
		wp_product_list.image as image, 
		wp_product_list.name AS title, 
		wp_product_brands.name AS author, 
		COUNT(*) AS votes, 
		SUM(wp_fsr_user.points) AS points, 
		AVG(points)*SQRT(COUNT(*)) AS rate, 
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
			  $output .= " Сто лучших работ</h1><div style='color:white;background-color:#668bb7;padding:2px;font-weight:bold;display:block;'>".$_order_description."</div><div style='color:#818181;'>Сортировать 
			  по дате последнего голосования: <a href='".$pageURL."&ord=91&br=".$_br."'>старые впереди</a>, <a href='".$pageURL."&ord=92&br=".$_br."'>новые впереди</a>; 
			  по среднему баллу: <a href='".$pageURL."&ord=81&br=".$_br."'>по возрастанию</a>, <a href='".$pageURL."&ord=82&br=".$_br."'>по убыванию</a>; 
			  по количеству баллов: <a href='".$pageURL."&ord=61&br=".$_br."'>по возрастанию</a>, <a href='".$pageURL."&ord=62&br=".$_br."'>по убыванию</a>; 
			  по количеству голосов: <a href='".$pageURL."&ord=51&br=".$_br."'>по возрастанию</a>, <a href='".$pageURL."&ord=52&br=".$_br."'>по убыванию</a>; 
			  по рейтингу: <a href='".$pageURL."&ord=71&br=".$_br."'>по возрастанию</a>, <a href='".$pageURL."&ord=72&br=".$_br."'>по убыванию</a>;
			  </div></div>";
		  }

	  foreach((array)$product_list as $product)
		{
		$output .= "<div class='item'>";
		$output .= "<a href='".get_option('product_list_url').$seperator."cartoonid=".$product['ID']."'>";
		if($product['image'] != '')
		  {
		  //$output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."' title='".$product['name']."' alt='".$product['name']."' />\n\r";
		  $output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' title='".$product['author'].". &quot;".$product['title']."&quot;. Голосов: ".$product['votes'].". Баллов: " . $product['points'] . ". Рейт: " . round($product['average'],3) . "' class='thumb'/>";
		  }
		$output .= "</a>";
		$output .= "</div>\n\r";
		}
	  $output .= "</div>\n\r";
	  $output .= "<br style='clear: left;'>\n\r";
	  
	  return preg_replace("/\[top_votes\]/", $output, $content);
}
?>
