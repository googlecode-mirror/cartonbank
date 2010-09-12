<?
function cart_product_list_string($licensecolumn)
{
// the function is for displaying the list of products
// if $licensecolumn is False we don't diplay extra columns
	global $wpdb;
	$current_item = 0;
	$total = 0;

	$cart = $_SESSION['nzshpcrt_cart'];
	$result = '<table class=\'productcart\'>';
	foreach($cart as $key => $cart_item)
	{
	    $current_item = $current_item +1;
	    $product_id = $cart_item->product_id;
	    $quantity = $cart_item->quantity;
	    $number =& $quantity;
	    $product_variations = $cart_item->product_variations;
	    $variation_count = count($product_variations);
	    if($variation_count >= 1)
	      {
				  $variation_list = "&nbsp;(";
				  $i = 0;
				  foreach($product_variations as $value_id)
				{
				if($i > 0)
				  {
				  $variation_list .= ",&nbsp;";
				  }
				$value_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `id`='".$value_id."' LIMIT 1",ARRAY_A);
				$variation_list .= str_replace(" ", "&nbsp;",stripslashes($value_data[0]['name']));    
				$i++;
				}
				  $variation_list .= ")";
	      }
	      else
		{
		$variation_list = '';
		}

	    $sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`name` as brand, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE wp_product_list.id='$product_id' AND `wp_product_list`.`active`='1' AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id`  ORDER BY `wp_product_list`.`id` DESC LIMIT 1";

		$product_list = $wpdb->get_results($sql,ARRAY_A) ;
	    $result .=  "<tr>";
	    
	    $result .= "  <td style='width:144px;'>";

	    $basepath = get_option('siteurl');
	    $imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/images/";
	    $previewdir = $basepath."/wp-content/plugins/wp-shopping-cart/product_images/";
	    $result .= ("<a href='".$previewdir.$product_list[0]['image']."'><img border='0' src='".$imagedir.$product_list[0]['image']."'></a>");
	    $result .= "  </td>";

	    $result .= "  <td>";

		$_size = $product_list[0]['width']."px X ".$product_list[0]['height']."px;";

		$_bigpictext = "<b>Номер:</b> ".$product_list[0]['id']."<br><b>Автор:</b> ".$product_list[0]['brand']."<br><b>Название: </b> ".$product_list[0]['name']."<br><b>Категория: </b> ".$product_list[0]['kategoria']."<br><b>Описание: </b> ".$product_list[0]['description']."<br><b>Тэги: </b>".$product_list[0]['additional_description']."<br><b>Размер:</b> ".$_size;

		$_SESSION['nzshpcrt_cart'][$key]->author  = $product_list[0]['brand'];


		$result .= "<div style='font-size: 8pt !important;'>".$_bigpictext."</div>";

	    $result .= "  </td>";

		$siteurl = get_option('siteurl');

		if ($licensecolumn)
		{

			$result .= "  <td width='240'>";
			
			
			$result .= "<form name='licenses' id='licenses' onsubmit='submitform(this);return false;' action='".$siteurl."/?page_id=29' method='POST'>";
			
			$ch1 = ischecked('l1_price', $product_list[0]['id']);
			$result .= "<input name='license' value='l1_price' type='radio' $ch1 />"; 
			$result .= round($product_list[0]['l1_price'])."&nbsp;руб. <a href='#' onclick='javascript:window.open('".$siteurl."/wp-content/plugins/wp-shopping-cart/license.php?l=1&item=".$current_item."','текст ограниченной лицензии','height=480,width=640,scrollbars=yes');'>ограниченная</a> <br>";

			$ch2 = ischecked('l2_price', $product_list[0]['id']);
			$result .= "<input name='license' value='l2_price' type='radio' $ch2 />"; 
			$result .= round($product_list[0]['l2_price'])."&nbsp;руб. <a href='#' onclick='javascript:window.open('".$siteurl."/wp-content/plugins/wp-shopping-cart/license.php?l=2&item=".$current_item."','текст стандартной лицензии','height=480,width=640,scrollbars=yes');'>стандартная</a><br>";
			
			$ch3 = ischecked('l3_price', $product_list[0]['id']);
			$result .= "<input name='license' value='l3_price' type='radio'  $ch3 />"; 
			$result .= round($product_list[0]['l3_price'])."&nbsp;руб. <a href='#' onclick='javascript:window.open('".$siteurl."/wp-content/plugins/wp-shopping-cart/license.php?l=3&item=".$current_item."','текст стандартной лицензии','height=480,width=640,scrollbars=yes');'>расширенная</a>";

			$result .= "<input value='".$product_list[0]['id']."' name='prodid' type='hidden'> <br><br>";
			
			$result .= "<input id='searchsubmit' value='Сменить лицензию' type='submit'> </form>";

			$result .= " </td>";

		}

		$price_modifier = 0;
	    
		if($product_list[0]['notax'] == 1)
	      {
	      $total += $number * ($product_list[0]['price']-$price_modifier);
	      }
	      else
		{
		$total += $number * ($product_list[0]['price']-$price_modifier) * get_option('gst_rate');
		}

		if ($licensecolumn)
		{
		// License select
		$result .= "  <td width='70'>";
	    $result .= "<a href='".get_option('shopping_cart_url')."&remove=".$key."'>Убрать из заказа</a>";
	    $result .= "  </td>";
	    }
		/*
		else
		{
		// Download link
		$result .= "  <td width='70'>";
	    $result .= "<a href=''>Скачать</a>";
		$result .= "  </td>";
		}
		*/
	    $result .= "</tr>";
	 }

	 $result .= '</table>';

	//$result .= ("<pre>SESSION:".print_r($_SESSION,true)."</pre>");

	return $result;
}

function ischecked ($license,$picture_id)
{
	$cart = $_SESSION['nzshpcrt_cart'];
	foreach($cart as $key => $cart_item)
    {
		if (($license == $cart_item->license) && ($picture_id == $cart_item->product_id))
		{
			$ch = 'checked';
            return $ch;
		}
		else
		{
			$ch = '';
		}
	}
    return $ch;
}

function ischecked2 ()
{
	return "checked";
}
?>