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
	  //$sql = "SELECT post as ID, wp_product_list.image as image, wp_product_list.name AS title, wp_product_brands.name AS author, COUNT(*) AS votes, SUM(wp_fsr_user.points) AS points, AVG(points)*SQRT(COUNT(*)) AS average, vote_date FROM wp_fsr_user, wp_product_list, wp_product_brands  WHERE wp_fsr_user.post = wp_product_list.id  AND wp_product_list.brand = wp_product_brands.id  AND wp_product_list.active = 1 AND wp_product_list.visible = 1 AND wp_product_list.brand = ".$_brand." GROUP BY 1 ORDER BY 7 DESC, 5 DESC LIMIT 100";

	  	  $sql = "SELECT post as ID, 
			wp_product_list.image as image, 
			wp_product_list.name AS title, 
			wp_product_brands.name AS author, 
			wp_product_list.votes AS votes, 
			wp_product_list.votes_sum AS points, 
			(wp_product_list.votes_sum/wp_product_list.votes) AS average, 
			wp_product_list.votes_rate as rate, 
			wp_fsr_user.vote_date 
			FROM wp_product_list, wp_fsr_user, wp_product_brands 
			WHERE 
			wp_product_list.active = 1 
			AND wp_product_list.visible = 1 
			AND wp_fsr_user.post = wp_product_list.id  
			AND wp_product_list.brand = wp_product_brands.id  
			AND wp_product_list.brand = ".$_brand." 
			GROUP BY 1 ORDER BY 7 DESC, 5 DESC 
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

$_br=0;

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
	$_order_description = "Сортировка по рейту (интегральная характеристика результатов голосования). По убыванию.";

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
			$_order_description = "Сортировка по сумме баллов. По возрастанию.";
			break;
		case 62:
			$_order_filter = " ORDER BY points DESC";
			$_order_description = "Сортировка по сумме баллов. По убыванию.";
			break;
		case 71:
			$_order_filter = " ORDER BY rate ASC";
			$_order_description = "Сортировка по рейту (интегральная характеристика результатов голосования). По возрастанию.";
			break;
		case 72:
			$_order_filter = " ORDER BY rate DESC";
			$_order_description = "Сортировка по рейту (интегральная характеристика результатов голосования). По убыванию.";
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


	// Get the Brand (author) data
		if ($_br==0)
			{
				$brand_sql = "SELECT * FROM `wp_product_brands` where active=1 order by name";
			}
		else
			{
				$brand_sql = "SELECT * FROM `wp_product_brands` where id = ". $_br;
			}
	
	$brand_result  = $GLOBALS['wpdb']->get_results($brand_sql,ARRAY_A);
			//pokazh($brand_result);
	$brands_sql = "SELECT id, name FROM `wp_product_brands` where active = 1 order by name";
	$brands_result  = $GLOBALS['wpdb']->get_results($brands_sql,ARRAY_A);
			//pokazh($brands_result);


	// author name
	if (isset($brand_result[0]['name']) && $brand_result[0]['name'] != '')
	{$author_name = $brand_result[0]['name'];}else{$brand_result[0]['name']='';}

	// all authors dropdown
	//$authors = "<select name='authors' style='font-size: 22px;font-family: &apos;Times New Roman&apos;' onchange=\"if(!options[selectedIndex].defaultSelected) location='".get_option('siteurl')."/?page_id=1284&amp;ord=72&br='+options[selectedIndex].value\"><option  value='0'>&nbsp;Все авторы&nbsp;</option>";

	$authors = "<select name='authors' style='font-size: 22px;font-family: &apos;Times New Roman&apos;' onchange=\"if(!options[selectedIndex].defaultSelected) location='".get_option('siteurl')."/?page_id=1284&ord=72&br='+options[selectedIndex].value\">";

	$_selected = "";
	
	if ($_br!=0)
	{
		$authors .= "<option value='0' style='font-size: 22px;font-family: &apos;Times New Roman&apos;;'>&nbsp;Все авторы&nbsp;</option>";
		foreach ($brands_result as $brand)
		{
			if ($brand_result[0]['id'] == $brand['id'])
				{$_selected = " selected";}
			$authors .= "<option $_selected value=".$brand['id']." style='font-size: 22px;font-family: &apos;Times New Roman&apos;;'>&nbsp;".$brand['name']."&nbsp;</option>";
			$_selected = "";
		}
	}
	else
	{
		$authors .= "<option selected value='0' style='font-size: 22px;font-family: &apos;Times New Roman&apos;;'>&nbsp;Все авторы&nbsp;</option>";
		foreach ($brands_result as $brand)
		{
			if ($brand_result[0]['id'] == $brand['id'])
				{$_selected = " ";}
			$authors .= "<option $_selected value=".$brand['id']." style='font-size: 22px;font-family: &apos;Times New Roman&apos;;'> &nbsp;".$brand['name']. "&nbsp;</option>";
			$_selected = "";
		}

	}
	$authors .= "</select>";

/*
$sql = "SELECT
			post as ID, 
			wp_product_list.image as image, 
			wp_product_list.name AS title, 
			wp_product_brands.name AS author, 
			COUNT(wp_fsr_user.post) AS votes, 
			SUM(wp_fsr_user.points) AS points, 
			AVG(points)*SQRT(SQRT(COUNT(wp_fsr_user.post))) AS rate, 
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

			
			SELECT 
			post as ID, 
			wp_product_list.name AS title, 
			wp_product_brands.name AS author, 
			wp_product_list.votes AS votes, 
			wp_product_list.votes_sum AS points, 
			(wp_product_list.votes_sum/wp_product_list.votes) AS average, 
			(wp_product_list.votes_sum/wp_product_list.votes)*SQRT(wp_product_list.votes) as rate, 
			wp_fsr_user.vote_date  
			FROM wp_fsr_user, wp_product_list, wp_product_brands 
			WHERE wp_fsr_user.post = wp_product_list.id 
			AND wp_product_list.brand = wp_product_brands.id 
			AND wp_product_list.active = 1
			AND wp_product_list.visible = 1
			GROUP BY 1
			ORDER BY 7 DESC, 5 DESC;

	$sql = "SELECT
			post as ID, 
			wp_product_list.image as image, 
			wp_product_list.name AS title, 
			wp_product_brands.name AS author, 
			COUNT(wp_fsr_user.post) AS votes, 
			SUM(wp_fsr_user.points) AS points, 
			AVG(points)*SQRT(SQRT(COUNT(wp_fsr_user.post))) AS rate, 
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
*/
	$sql = "SELECT
			post as ID, 
			wp_product_list.image as image, 
			wp_product_list.name AS title, 
			wp_product_brands.name AS author, 
			wp_product_list.votes AS votes, 
			wp_product_list.votes_sum AS points, 
			(wp_product_list.votes_sum/wp_product_list.votes) AS average, 
			wp_product_list.votes_rate as rate, 
			wp_fsr_user.vote_date  
				FROM  wp_product_list, wp_fsr_user, wp_product_brands 
				WHERE wp_product_list.active = 1
				AND wp_product_list.category != 666
				AND wp_product_list.visible = 1
				AND wp_product_list.approved = 1
				AND wp_fsr_user.post = wp_product_list.id 
				AND wp_product_list.brand = wp_product_brands.id 
				".$_br_filter."
				GROUP BY 1
				".$_order_filter."
				".$_limit."
				".$_offset;




	$product_list = $wpdb->get_results($sql,ARRAY_A);
/*
	SELECT
	post as ID, 
	wp_product_list.image as image, 
	wp_product_list.name AS title, 
	wp_product_brands.name AS author, 
	COUNT(wp_fsr_user.post) AS votes, 
	SUM(wp_fsr_user.points) AS points, 
	AVG(points)*SQRT(SQRT(COUNT(*))) AS rate, 
	AVG(points) as average, 
	vote_date  
		FROM wp_fsr_user, wp_product_list, wp_product_brands 
		WHERE wp_fsr_user.post = wp_product_list.id 
		AND wp_product_list.brand = wp_product_brands.id 
		AND wp_product_list.active = 1
		AND wp_product_list.visible = 1
		
		GROUP BY 1
		 ORDER BY vote_date DESC
		 LIMIT 100;

*/

$output = "<div id='homepage_products' class='items'>";

if (isset($product_list[0]))
{
/*
$style_unsorted = "style='vertical-align:text-bottom; background-color: #668bb7; color: #507AA5; cursor: auto; display: block; float: left; height: 20px; margin-top: 10px; margin-bottom: 4px; margin-left: 2px; margin-right: 6px; padding-left: 2px; padding-right: 2px; padding-top: 4px; padding-bottom: 0px; text-align: center; text-decoration: none; width: 138px;'";

$style_sorted   = "style='vertical-align:text-bottom; background-color: #000099; color: #507AA5; cursor: auto; display: block; float: left; height: 20px; margin-top: 10px; margin-bottom: 4px; margin-left: 2px; margin-right: 6px; padding-left: 2px; padding-right: 2px; padding-top: 4px; padding-bottom: 0px; text-align: center; text-decoration: none; width: 138px;'";
*/
$style_unsorted = "style='background-color: #668bb7; color: #507AA5; cursor: pointer; display: block; height: 16px; margin-top: 0px; margin-bottom: 1px; margin-left: 2px; margin-right: 6px; padding-left: 2px; padding-right: 2px; padding-top: 2px; padding-bottom: 0px; text-align: center; text-decoration: none; width: 138px;'";

$style_sorted   = "style='background-color: #000099; color: #507AA5; cursor: pointer; display: block; height: 16px; margin-top: 0px; margin-bottom: 1px; margin-left: 2px; margin-right: 6px; padding-left: 2px; padding-right: 2px; padding-top: 2px; padding-bottom: 0px; text-align: center; text-decoration: none; width: 138px;'";


$output .= "<div style='color:#666666;width:750px;height:80px;position: relative;margin-top:5px;'>";

	if (isset($product_list[0]) && $_br == 0)
	{
		$output .= "<h1 style='float:left;position: absolute; bottom: 0; left: 0;'>Топ сто.<br>".$authors."</h1>"; // dropdown
	}
	else
	{
		//$output .= "<div style='color:silver;'><h1>".$authors.$product_list[0]['author'].".";
		$output .= "<h1 style='float:left;position: absolute; bottom: 0; left: 0;'>Топ сто.<br>".$authors."</h1>"; // dropdown
	}

// titles
$output .= "<div id='sorter' style='float:right; position: absolute; bottom: 13px; right: 0;'>";

if ($_order == 81) $output .= "<div  ".$style_sorted."><a href='".$pageURL."&ord=82&br=".$_br."' style='color:white;'>по среднему баллу /</a></div>";
else if ($_order == 82) $output .= "<div  ".$style_sorted."><a href='".$pageURL."&ord=81&br=".$_br."' style='color:white;'>по среднему баллу \</a></div>";
else $output .= "<div  ".$style_unsorted."><a href='".$pageURL."&ord=82&br=".$_br."' style='color:white;'>по среднему баллу</a></div>";

if ($_order == 61) $output .= "<div  ".$style_sorted."><a href='".$pageURL."&ord=62&br=".$_br."' style='color:white;'>по сумме баллов /</a></div>";
else if ($_order == 62) $output .= "<div  ".$style_sorted."><a href='".$pageURL."&ord=61&br=".$_br."' style='color:white;'>по сумме баллов \</a></div>";
else $output .= "<div  ".$style_unsorted."><a href='".$pageURL."&ord=62&br=".$_br."' style='color:white;'>по сумме баллов</a></div>";

if ($_order == 51) $output .= "<div  ".$style_sorted."><a href='".$pageURL."&ord=52&br=".$_br."' style='color:white;'>по колич. голосов /</a></div>";
else if ($_order == 52) $output .= "<div  ".$style_sorted."><a href='".$pageURL."&ord=51&br=".$_br."' style='color:white;'>по колич. голосов \</a></div>";
else $output .= "<div  ".$style_unsorted."><a href='".$pageURL."&ord=52&br=".$_br."' style='color:white;'>по колич. голосов</a></div>";

if ($_order == 71) $output .= "<div  ".$style_sorted."><a href='".$pageURL."&ord=72&br=".$_br."' style='color:white;'>по рейту /</a></div>";
else if ($_order == 72) $output .= "<div  ".$style_sorted."><a href='".$pageURL."&ord=71&br=".$_br."' style='color:white;'>по рейту \</a></div>";
else $output .= "<div  ".$style_unsorted."><a href='".$pageURL."&ord=72&br=".$_br."' style='color:white;'>по рейту</a></div>";

$output .= "</div>"; //sorter
$output .= "<div style='width:300px;float:left;position: absolute; bottom: 14px; left: 280px;'>$_order_description</div></div>";// sort header div

}//if (isset($product_list[0]))

//$output .= "<div id='clear' style='clear:left;'>&nbsp;</div>";

$counter = 1;
$output .= "<div id='allicons' style=''>";
		  foreach((array)$product_list as $product)
			{
			$output .= "<div class='item' style='position: relative;'>";
			$output .= "<a href='".get_option('product_list_url').$seperator."cartoonid=".$product['ID']."'>";
			if($product['image'] != '')
			  {
			  //$output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."' title='".$product['name']."' alt='".$product['name']."' />\n\r";
			  $output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' title='".$counter.". ".$product['author'].". &quot;".$product['title']."&quot;. Голосов: ".$product['votes'].". Баллов: " . $product['points'] . ". Средний балл: " . round($product['average'],3) .". Рейт: " . round($product['rate'],3) . "' class='thumb'/>";
			  }
			$output .= "</a>";
			$output .= "<div id='cntr' class='cntr'>$counter</div>";
			$output .= "</div>\n\r";
			$counter = $counter + 1;
			}
$output .= "</div>"; //icons
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
	$_order_description = "Сортировка по рейту (интегральная характеристика результатов голосования). По убыванию.";

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
			$_order_description = "Сортировка по сумме баллов. По возрастанию.";
			break;
		case 62:
			$_order_filter = " ORDER BY points DESC";
			$_order_description = "Сортировка по сумме баллов. По убыванию.";
			break;
		case 71:
			$_order_filter = " ORDER BY rate ASC";
			$_order_description = "Сортировка по рейту (интегральная характеристика результатов голосования). По возрастанию.";
			break;
		case 72:
			$_order_filter = " ORDER BY rate DESC";
			$_order_description = "Сортировка по рейту (интегральная характеристика результатов голосования). По убыванию.";
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

	$sql = "SELECT b.id, b.name, p.id as ID, p.image as image, p.name AS title, b.name AS author, c.price FROM  `wp_purchase_logs` AS l,  `wp_purchase_statuses` AS s,  `wp_cart_contents` AS c,  `wp_product_list` AS p,  `wp_download_status` AS st,  `wp_product_brands` AS b, `wp_users` AS u WHERE l.`processed` = s.`id`  AND l.id = c.purchaseid AND p.id = c.prodid AND st.purchid = c.purchaseid AND p.brand = b.id AND u.id = l.user_id AND l.user_id !=  '106' AND st.downloads !=  '5' GROUP BY c.license ORDER BY datetime DESC LIMIT 100";

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
            $gift = '';
            if ($product['price']<10)
            {
                $gift='<div class="gift"><a href="http://cartoonbank.ru/?page_id=2420" style="color:white;">подарок</a></div>';
            }
			$output .= "<div class='item_gift'>";
			$output .= "<a href='".get_option('product_list_url').$seperator."cartoonid=".$product['ID']."'>";
			if($product['image'] != '')
			  {
			  //$output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."' title='".$product['name']."' alt='".$product['name']."' />\n\r";
			  $output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' title='".$product['author'].". ".$product['title']."' class='thumb'/>";
			  }
			$output .= "</a>";
			$output .= $gift."</div>\n\r";
			}
		  $output .= "</div>\n\r";
		  $output .= "<br style='clear: left;'>\n\r";
		  
		  return preg_replace("/\[last_sales\]/", $output, $content);
}
}

function all_artists($content = '')
{
	global $wpdb;

	if(isset($_GET['page_id']) && $wpdb->escape($_GET['page_id'])=='29')
		{
			return $content;
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
	  $sql = "SELECT `id`, `name`, `avatar_url`, `bio_post_id`, `count` FROM `wp_product_brands` WHERE active=1 ORDER BY name";
	  $product_list = $wpdb->get_results($sql,ARRAY_A);
		
	  $output = "<div id='homepage_products' class='items'>";
	$output = '';    
	if (isset($product_list[0]))
		  {
			  $output .= "<div id='homepage_products' class='items'>";
		  }

	  foreach((array)$product_list as $product)
		{
		$output .= "<div class='item'>";
		//http://cartoonbank.ru/?page_id=29&brand=22
		$output .= "<a href='".get_option('product_list_url')."?page_id=29&brand=".$product['id']."'>";
		if($product['avatar_url'] != '')
		  {
		  $output .= "<img src='".$product['avatar_url']."' title='".$product['name']." (".$product['count']." рис.)' class='thumb' style='width:140px;height:140px;'/>";
		  }
		$output .= "</a>";
		$output .= "</div>\n\r";
		}
	  $output .= "</div>\n\r";
	  $output .= "<br style='clear: left;'>\n\r";
	  
	  return preg_replace("/\[all_artists\]/", $output, $content);
}

function temy_dnya($content = '')
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

		  $siteurl = get_option('siteurl');
		  if(get_option('permalink_structure') != '')
			{
			$seperator ="?";
			}
			else
			  {
			  $seperator ="&amp;";
			  }


	$sql = "SELECT DISTINCT b.id, b.name, td.datetime, td.id as ID, td.comment, p.image AS image, p.name AS title, b.name AS author
FROM  tema_dnya as td, `wp_product_list` AS p, `wp_product_brands` AS b
WHERE td.id = p.id 
AND p.brand = b.id
ORDER BY td.datetime DESC 
LIMIT 0, 100";

		  $product_list = $wpdb->get_results($sql,ARRAY_A);


		  $output = "<div id='homepage_products' class='items'>";
		$output = '';    
		if (isset($product_list[0]))
		{
				  $output .= "<div id='item_wrap'><div id='homepage_products' class='items'>";
		  if (isset($product_list[0]) && $_br == 0)
				  {
					$output .= "<div><h1>";
				  }
				  else
				  {
					$output .= "<div><h1>".$product_list[0]['author'].".";
				  }
				  $output .= "Архив тем дня за три месяца</h1></div>";

		  foreach((array)$product_list as $product)
			{
			$date_first_part = explode(" ",$product['datetime']);
			$date_parse = explode("-",$date_first_part[0]);
			$d = $date_parse[2].".".$date_parse[1].".".$date_parse[0];
			if (mb_strlen($product['comment'],'utf-8')==0)
				{$description = 'без ссылки';}
			else
				{
					$description = $product['comment'];
				}

			$output .= "<div class='item'><div id='date' style='color:#666666;'>".$d.":</div><div id='descr' style='font-size:.8em;'>".stripslashes($description)."</div>";
			$output .= "<a href='".get_option('product_list_url').$seperator."cartoonid=".$product['ID']."'>";
			if($product['image'] != '')
			  {
			  $output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' title='".$product['author'].". ".$product['title']."' class='thumb'/>";
			  }
			$output .= "</a>";
			$output .= "</div>\n\r";
			}
		  $output .= "</div></div>\n\r";
		  $output .= "<br style='clear: left;'>\n\r";
		  
		  return preg_replace("/\[temy_dnya\]/", $output, $content);
		}
}

?>