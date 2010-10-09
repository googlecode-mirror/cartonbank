<?
function cart_product_list_string($licensecolumn)
{
// the function is for displaying the list of products
// if $licensecolumn is False we don't diplay extra columns
	global $wpdb;
	$siteurl = get_option('siteurl');
	$current_item = 0;
	$total = 0;

	$cart = isset($_SESSION['nzshpcrt_cart'])?$_SESSION['nzshpcrt_cart']:"";
	$result = '<table class=\'productcart\'>';

	if (isset($cart) && $cart!='')
	{
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
if (!$licensecolumn)
		{
// download link start
		$link ="";
		$previous_download_ids = Array(0);  

		$sessionid = $_GET['sessionid'];
		$selectsql = "SELECT * FROM `wp_purchase_logs` WHERE `sessionid`= ".$sessionid." LIMIT 1";
		$check = $wpdb->get_results($selectsql,ARRAY_A) ;

//echo ("<pre>product_list:".print_r($product_list,true)."</pre>");
	 
	 if(isset($product_list[0]['file']) && isset($check[0]['id']) && $product_list[0]['file'] > 0)
       {
       $wpdb->query("UPDATE `wp_download_status` SET `active`='1' WHERE `fileid`='".$product_list[0]['file']."' AND `purchid` = '".$check[0]['id']."' LIMIT 1");
       $download_data = $wpdb->get_results("SELECT * FROM `wp_download_status` WHERE `fileid`='".$product_list[0]['file']."' AND `purchid`='".$check[0]['id']."' AND `id` NOT IN (".make_csv($previous_download_ids).") LIMIT 1",ARRAY_A);
       $download_data = $download_data[0];
       $link = $siteurl."?downloadid=".$download_data['id'];
       $previous_download_ids[] = $download_data['id'];
       }


/// download link stop
		}


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


		if ($licensecolumn)
		{
			$result .= "  <td width='240'>";

			$result .= "<form name='licenses' id='licenses' onsubmit='submitform(this);return false;' action='".$siteurl."/?page_id=29' method='POST'>";
			
			$ch1 = ischecked('l1_price', $product_list[0]['id']);
			$result .= "<input name='license' value='l1_price' type='radio' $ch1 />"; 
			$result .= round($product_list[0]['l1_price'])."&nbsp;руб. ";
			$result .= "<a title='ваша лицензия' href='#' onclick=\"javascript:window.open('".$siteurl."/wp-content/plugins/wp-shopping-cart/license.php?l=1&item=".$current_item."','текст ограниченной лицензии','height=480,width=640,scrollbars=yes');\">ограниченная</a> <br>";


			$ch2 = ischecked('l2_price', $product_list[0]['id']);
			$result .= "<input name='license' value='l2_price' type='radio' $ch2 />"; 
			$result .= round($product_list[0]['l2_price'])."&nbsp;руб. <a title='ваша лицензия' href='#' onclick=\"javascript:window.open('".$siteurl."/wp-content/plugins/wp-shopping-cart/license.php?l=2&item=".$current_item."','текст стандартной лицензии','height=480,width=640,scrollbars=yes');\">стандартная</a><br>";
			
			$ch3 = ischecked('l3_price', $product_list[0]['id']);
			$result .= "<input name='license' value='l3_price' type='radio'  $ch3 />"; 
			$result .= round($product_list[0]['l3_price'])."&nbsp;руб. <a title='ваша лицензия' href='#' onclick=\"javascript:window.open('".$siteurl."/wp-content/plugins/wp-shopping-cart/license.php?l=3&item=".$current_item."','текст расширенной лицензии','height=480,width=640,scrollbars=yes');\">расширенная</a>";

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
		
		else
		{
		// Download link
			$result .= "  <td width='70'>";
			$result .= "<a href='$link'>Скачать</a>";
			$result .= "  </td>";

		}
		/*
		if (isset($link))
		{
		$result .= "  <td width='70'>";
	    $result .= "<a href='$link'>Скачать</a>";
		$result .= "  </td>";
		}
		*/
	    $result .= "</tr>";

		// License text
		if (!$licensecolumn)
		{
			$license_text = get_license($current_item,1);
			$result .=  "<tr>";
			$result .= "<td colspan=3> <a id='displayText".$current_item."' href='javascript:toggle(".$current_item.");'>[+] показать текст лицензии</a><div id='toggleText".$current_item."' style='display:none;background-color:#FFFFCC;padding:8px;'><br>";
			$result .= $license_text;
			$result .= "</div></td>";
			$result .= "</tr>";
		}

	 } // end of: foreach($cart as $key => $cart_item)
}
	 $result .= '</table>';

		$result .= "<script language='javascript'> ";
		$result .= "function toggle(item) {";
		$result .= "var ele = document.getElementById('toggleText'+item);";
		$result .= "var text = document.getElementById('displayText'+item);";
		$result .= "if(ele.style.display == 'block') {";
		$result .= "		ele.style.display = 'none';";
		$result .= "	text.innerHTML = '[+] показать текст лицензии<br>';";
		$result .= "}";
		$result .= "else {";
		$result .= "	ele.style.display = 'block';";
		$result .= "	text.innerHTML = '[-] скрыть текст лицензии<br>';";
		$result .= "}";
		$result .= "} ";
		$result .= "</script>";


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

function get_license($sequence_of_image,$license_num)
{
	/*
		#agreement_number#
		#agreement_date#
		#customer_name#
		#image_number#
		#author_name#
		#media_name#
		#price#
		*/

// load unique license data
	$current_user = wp_get_current_user();    

	$license_unique_number = $_GET['sessionid']."_".$_SESSION['nzshpcrt_cart'][$sequence_of_image]->product_id;
	$agreement_number = $license_unique_number;//uniqid();
	$agreement_date = date("m.d.y");
	$customer_name = $current_user->last_name. " " . $current_user->first_name;
	$media_name = '[не указано]';

if(isset($_SESSION['nzshpcrt_cart']))
{
	$price = $_SESSION['nzshpcrt_cart'][$sequence_of_image] -> price;
	$image_name = $_SESSION['nzshpcrt_cart'][$sequence_of_image] -> name;
	$image_number = $_SESSION['nzshpcrt_cart'][$sequence_of_image] -> product_id;
	$author_name = $_SESSION['nzshpcrt_cart'][$sequence_of_image] -> author;
}

//load Livense template
$filename = '';
switch($license_num)
        {
        case 1:
        $filename = getcwd()."/wp-content/plugins/wp-shopping-cart/"."license_limited_template.htm";
        break;
        
        case 2:
        $filename = getcwd()."/wp-content/plugins/wp-shopping-cart/"."Livense_standard_template.htm";
        break;

        case 3:
        $filename = getcwd()."/wp-content/plugins/wp-shopping-cart/"."Livense_extended_template.htm";
        break;

        default:
        $filename = getcwd()."/wp-content/plugins/wp-shopping-cart/"."Livense_limited_template.htm";
        break;
}

$content=loadFile($filename);

// replace placeholders
	$content = str_replace ('#agreement_number#',$agreement_number,$content);
	$content = str_replace ('#agreement_date#',$agreement_date,$content);
	$content = str_replace ('#customer_name#',$customer_name,$content);
	$content = str_replace ('#image_number#',$image_number,$content);
	$content = str_replace ('#image_name#',$image_name,$content);
	$content = str_replace ('#author_name#',$author_name,$content);
	$content = str_replace ('#media_name#',$media_name,$content);
	$content = str_replace ('#price#',$price,$content);

// output content
return $content;
}

function loadFile($sFilename, $sCharset = 'UTF-8')
{
    if (floatval(phpversion()) >= 4.3) {
        $sData = file_get_contents($sFilename);
    } else {
        if (!file_exists($sFilename)) return -3;
        $rHandle = fopen($sFilename, 'r');
        if (!$rHandle) return -2;

        $sData = '';
        while(!feof($rHandle))
            $sData .= fread($rHandle, filesize($sFilename));
        fclose($rHandle);
    }
    if ($sEncoding = mb_detect_encoding($sData, 'auto', true) != $sCharset)
        $sData = mb_convert_encoding($sData, $sCharset, $sEncoding);
    return $sData;
}
?>