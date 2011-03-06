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
  if (isset($product_list[0]))
	  {
		$output .= "<div><h1>".$product_list[0]['author'].". Сто лучших работ</h1><div style='color:#818181;'>Рейт равен среднему баллу, умноженному на квадратный корень из количества поданных голосов.</div></div>";
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
?>