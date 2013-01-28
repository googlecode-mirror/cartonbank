<?
function includeTrailingCharacter($string, $character)
{
    if (strlen($string) > 0) {
        if (substr($string, -1) !== $character) {
            return $string . $character;
        } else {
            return $string;
        }
    } else {
        return $character;
    }
}

function cart_product_list_string($licensecolumn)
{
	// the function is for displaying the list of products
	// if $licensecolumn is False we don't diplay extra columns
	global $wpdb, $result_no_license_text;
	$siteurl = get_option('siteurl');
	$current_item = 0;
	$total = 0;

	$cart = isset($_SESSION['nzshpcrt_cart'])?$_SESSION['nzshpcrt_cart']:"";
	$result = '<table class=\'productcart\'>';
	$result_no_license_text = '<table class=\'productcart\'>';
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


		$sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`name` as brand, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE wp_product_list.id='$product_id' AND `wp_product_list`.`active`='1' AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_product_list`.`category` = `wp_product_categories`.`id`  ORDER BY `wp_product_list`.`id` DESC LIMIT 1";

		$product_list = $wpdb->get_results($sql,ARRAY_A) ;

		if (!$licensecolumn)
		{
			// download link start
			$link ="";
			$previous_download_ids = Array(0);  

			$sessionid = $_GET['sessionid'];
			$selectsql = "SELECT * FROM `wp_purchase_logs` WHERE `sessionid`= ".$sessionid." LIMIT 1";
			$check = $wpdb->get_results($selectsql,ARRAY_A) ;

			//pokazh($product_list,"product_list");
	 
		 if(isset($product_list[0]['file']) && isset($check[0]['id']) && $product_list[0]['file'] > 0)
		   {
		   $wpdb->query("UPDATE `wp_download_status` SET `active`='1' WHERE `fileid`='".$product_list[0]['file']."' AND `purchid` = '".$check[0]['id']."' LIMIT 1");
		   $download_data = $wpdb->get_results("SELECT * FROM `wp_download_status` WHERE `fileid`='".$product_list[0]['file']."' AND `purchid`='".$check[0]['id']."' AND `id` NOT IN (".make_csv($previous_download_ids).") LIMIT 1",ARRAY_A);
		   $download_data = $download_data[0];
		   
		  /* 
		   * for security reason add to url for hires images sid - last 6 simbols of idhash
		   *
		   */
		   $sql = "SELECT `idhash` FROM `wp_product_files` WHERE `id`=" . $product_list[0]['file'] . " LIMIT 1";
		   $idhash_data = $wpdb->get_results($sql, ARRAY_A);
		   if($idhash_data != null) 
		   {
				$idhash = "&sid=" . substr($idhash_data[0]['idhash'], -6);
		   }	

		   $site_tmp = includeTrailingCharacter($siteurl, "/");
		   
		   $link = $site_tmp."?downloadid=".$download_data['id'] . $idhash;
		   $previous_download_ids[] = $download_data['id'];
		   }


			/// download link stop
		}


	    $result .=  "<tr>";
	    $result .= "  <td style='width:144px;'>";
	    $result_no_license_text .=  "<tr>";
	    $result_no_license_text .= "  <td style='width:144px;'>";


		$basepath = get_option('siteurl');
	    $imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/images/";
	    $previewdir = $basepath."/wp-content/plugins/wp-shopping-cart/product_images/";
	    
		$result .= ("<a href='".$previewdir.$product_list[0]['image']."'><img border='0' src='".$imagedir.$product_list[0]['image']."'></a>");
	    $result .= "  </td>";
		$result .= "  <td>";
		$result_no_license_text .= ("<a href='".$previewdir.$product_list[0]['image']."'><img border='0' src='".$imagedir.$product_list[0]['image']."'></a>");
	    $result_no_license_text .= "  </td>";
		$result_no_license_text .= "  <td>";

		$_size = $product_list[0]['width']."px X ".$product_list[0]['height']."px;";

		$_bigpictext = "<b>Номер:</b> ".$product_list[0]['id'];
		$_bigpictext .= "<br /><b>Автор:</b> ".$product_list[0]['brand'];
		$_bigpictext .= "<br /><b>Название: </b> ".nl2br(stripslashes($product_list[0]['name']));
		$_bigpictext .= "<br /><b>Категория: </b> ".$product_list[0]['kategoria'];
		$_bigpictext .= "<br /><b>Описание: </b> ".nl2br(stripslashes($product_list[0]['description']));
		$_bigpictext .= "<br /><b>Тэги: </b>".nl2br(stripslashes($product_list[0]['additional_description']));
		$_bigpictext .= "<br /><b>Размер:</b> ".$_size;
		$_bigpictext .= "<br /><b>Цена (без скидки):</b> ".$_SESSION['nzshpcrt_cart'][$key]->price." руб.";
		$_bigpictext .= "<br /><b>Лицензия:</b> ".license_name($_SESSION['nzshpcrt_cart'][$key]->license);

		$_SESSION['nzshpcrt_cart'][$key]->author  = $product_list[0]['brand'];


		$result .= "<div style='font-size: 8pt !important;'>".$_bigpictext."</div>";
	    $result .= "  </td>";
		$result_no_license_text .= "<div style='font-size: 8pt !important;'>".$_bigpictext."</div>";
	    $result_no_license_text .= "  </td>";

		if ($licensecolumn)
		{
			$result .= "  <td width='240'>";
			$result .= "<form name='licenses' id='licenses' onsubmit='submitform(this);return false;' action='".$siteurl."/?page_id=29' method='POST'>";
			$result_no_license_text .= "  <td width='240'>";
			$result_no_license_text .= "<form name='licenses' id='licenses' onsubmit='submitform(this);return false;' action='".$siteurl."/?page_id=29' method='POST'>";
			
			$ch1 = ischecked('l1_price', $product_list[0]['id']);

			$result .= "<input name='license' value='l1_price' type='radio' $ch1 />"; 
			$result .= round($product_list[0]['l1_price'])."&nbsp;руб. ";
			$result .= "<a title='ваша лицензия' href='#' onclick=\"javascript:window.open('".$siteurl."/wp-content/plugins/wp-shopping-cart/license.php?l=1&item=".$current_item."','текст ограниченной лицензии','height=480,width=640,scrollbars=yes');\">ограниченная</a> <br />";
			$result_no_license_text .= "<input name='license' value='l1_price' type='radio' $ch1 />"; 
			$result_no_license_text .= round($product_list[0]['l1_price'])."&nbsp;руб. ";
			$result_no_license_text .= "<a title='ваша лицензия' href='#' onclick=\"javascript:window.open('".$siteurl."/wp-content/plugins/wp-shopping-cart/license.php?l=1&item=".$current_item."','текст ограниченной лицензии','height=480,width=640,scrollbars=yes');\">ограниченная</a> <br />";

			$ch2 = ischecked('l2_price', $product_list[0]['id']);
			$result .= "<input name='license' value='l2_price' type='radio' $ch2 />"; 
			$result .= round($product_list[0]['l2_price'])."&nbsp;руб. <a title='ваша лицензия' href='#' onclick=\"javascript:window.open('".$siteurl."/wp-content/plugins/wp-shopping-cart/license.php?l=2&item=".$current_item."','текст стандартной лицензии','height=480,width=640,scrollbars=yes');\">стандартная</a><br />";
			$result_no_license_text .= "<input name='license' value='l2_price' type='radio' $ch2 />"; 
			$result_no_license_text .= round($product_list[0]['l2_price'])."&nbsp;руб. <a title='ваша лицензия' href='#' onclick=\"javascript:window.open('".$siteurl."/wp-content/plugins/wp-shopping-cart/license.php?l=2&item=".$current_item."','текст стандартной лицензии','height=480,width=640,scrollbars=yes');\">стандартная</a><br />";
			
			$ch3 = ischecked('l3_price', $product_list[0]['id']);
			$result .= "<input name='license' value='l3_price' type='radio'  $ch3 />"; 
			$result .= round($product_list[0]['l3_price'])."&nbsp;руб. <a title='ваша лицензия' href='#' onclick=\"javascript:window.open('".$siteurl."/wp-content/plugins/wp-shopping-cart/license.php?l=3&item=".$current_item."','текст расширенной лицензии','height=480,width=640,scrollbars=yes');\">расширенная</a>";
			$result .= "<input value='".$product_list[0]['id']."' name='prodid' type='hidden'> <br /><br />";
			$result .= "<input id='searchsubmit' value='Сменить лицензию' type='submit'> </form>";
			$result .= " </td>";
			$result_no_license_text .= "<input name='license' value='l3_price' type='radio'  $ch3 />"; 
			$result_no_license_text .= round($product_list[0]['l3_price'])."&nbsp;руб. <a title='ваша лицензия' href='#' onclick=\"javascript:window.open('".$siteurl."/wp-content/plugins/wp-shopping-cart/license.php?l=3&item=".$current_item."','текст расширенной лицензии','height=480,width=640,scrollbars=yes');\">расширенная</a>";
			$result_no_license_text .= "<input value='".$product_list[0]['id']."' name='prodid' type='hidden'> <br /><br />";
			$result_no_license_text .= "<input id='searchsubmit' value='Сменить лицензию' type='submit'> </form>";
			$result_no_license_text .= " </td>";
		}
		if (isset($product_list[0]['price']))
	    {
			$total += $number * ($product_list[0]['price']);
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
			$current_user = wp_get_current_user();
			$result .= "  <td width='140'>";
			$result_no_license_text .= "  <td width='140'>";

			if (isset($current_user) && $current_user->ID == '106')
			{
				$result .= "<a href='".get_option('siteurl')."/demo/demo.jpg'  style='background-color:#33ff99;padding:7px;border:1px #a3a598 solid;text-decoration:none;margin-top:16px;'>Скачать demo</a>";
				$result_no_license_text .= "<a href='".get_option('siteurl')."/demo/demo.jpg'  style='background-color:#33ff99;padding:7px;border:1px #a3a598 solid;text-decoration:none;margin-top:16px;'>Скачать demo</a>";
			}
			else
			{
				$result .= "<a href='$link'  style='background-color:#33ff99;padding:7px;border:1px #a3a598 solid;text-decoration:none;margin-top:16px;'>Скачать</a>";
				$result_no_license_text .= "<a href='$link'  style='background-color:#33ff99;padding:7px;border:1px #a3a598 solid;text-decoration:none;margin-top:16px;'>Скачать</a>";
			}
			$result .= "  </td>";
			$result_no_license_text .= "  </td>";
		}

	    $result .= "</tr>";
	    $result_no_license_text .= "</tr>";

		// License text
		if (!$licensecolumn)
		{
            if ($_SESSION['nzshpcrt_cart'][$current_item]->price == '250.00')
            {
                $lic_type = 1;
            }
            elseif ($_SESSION['nzshpcrt_cart'][$current_item]->price == '500.00')
            {
                $lic_type = 2;
            }
            elseif ($_SESSION['nzshpcrt_cart'][$current_item]->price == '2500.00')
            {
                $lic_type = 3;
            }
            else 
            {
                $lic_type = 1;
            }
        
            
			$license_text = get_license($current_item,$lic_type);
			$result .=  "<tr>";
			$result .= "<td colspan=3> <a id='displayText".$current_item."' href='javascript:toggle(".$current_item.");'>[+] показать текст лицензии</a><div id='toggleText".$current_item."' style='display:none;background-color:#FFFFCC;padding:8px;'><br />";
			$result .= $license_text;
			$result .= "</div></td>";
			$result .= "</tr>";
		}

	 } // end of: foreach($cart as $key => $cart_item)
	} //if (isset($cart) && $cart!='')
	else
	{
		return '';
	}
	 $result .= '</table>';
	 $result_no_license_text .= '</table>';
	
		$result .= "<script language='javascript'> ";
		$result .= "function toggle(item) {";
		$result .= "var ele = document.getElementById('toggleText'+item);";
		$result .= "var text = document.getElementById('displayText'+item);";
		$result .= "if(ele.style.display == 'block') {";
		$result .= "		ele.style.display = 'none';";
		$result .= "	text.innerHTML = '[+] показать текст лицензии<br />';";
		$result .= "}";
		$result .= "else {";
		$result .= "	ele.style.display = 'block';";
		$result .= "	text.innerHTML = '[-] скрыть текст лицензии<br />';";
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
	global $license_names_array;

	// load unique license data
	$current_user = wp_get_current_user();    

	$license_unique_number = $_GET['sessionid']."_".$_SESSION['nzshpcrt_cart'][$sequence_of_image]->product_id;
	$agreement_number = $license_unique_number;//uniqid();
	$agreement_date = date("m.d.y");

	
	$customer_name = $current_user->last_name. " " . $current_user->first_name;

	if (trim($customer_name)=='')
	{
		if (isset( $_SESSION['collected_data']))
		{
			$customer_name_from_form = trim($_SESSION['collected_data'][1])." ".trim($_SESSION['collected_data'][2]);
			$customer_name = $customer_name_from_form;
		}
	}

	//echo("<pre>".print_r($current_user,true)."</pre>");
	//echo("<pre>".print_r($_SESSION['nzshpcrt_cart'],true)."</pre>");
	//echo("<pre>".print_r($_SESSION['collected_data'],true)."</pre>");

	if (isset($_SESSION['collected_data'][5]) && trim($_SESSION['collected_data'][5])!='')
		$media_name = "«".trim($_SESSION['collected_data'][5])."»";
	elseif (isset($current_user->user_description) && $current_user->user_description!='')
		$media_name = "«".$current_user->user_description."»";
	else
		$media_name = "[не указано]";


	if (isset($current_user->discount))
		$_discount = $current_user->discount;
	else
		$_discount = 0;

	if(isset($_SESSION['nzshpcrt_cart']))
	{
		$price = $_SESSION['nzshpcrt_cart'][$sequence_of_image] -> price;
		if (isset($current_user->discount))
		{
			$price = round($price*(100-$_discount)/100);
		}
		
		$image_name = $_SESSION['nzshpcrt_cart'][$sequence_of_image] -> name;
		$image_number = $_SESSION['nzshpcrt_cart'][$sequence_of_image] -> product_id;
		$author_name = $_SESSION['nzshpcrt_cart'][$sequence_of_image] -> author;

	}

	//load License template
	$filename = '';
	//pokazh($license_num,"license_num");
	switch($license_num)
			{
			case 1:
			$filename = getcwd()."/wp-content/plugins/wp-shopping-cart/"."license_limited_template.htm";
			break;
			
			case 2:
			$filename = getcwd()."/wp-content/plugins/wp-shopping-cart/"."license_standard_template.htm";
			break;

			case 3:
			$filename = getcwd()."/wp-content/plugins/wp-shopping-cart/"."license_extended_template.htm";
			break;

			default:
			$filename = getcwd()."/wp-content/plugins/wp-shopping-cart/"."license_limited_template.htm";
			break;
	}

		if (isset($current_user) && $current_user->ID == 106)
		{
			$content = "<div style='color:red;'>Демонстрационная лицензия</div>";
			$content .= loadFile($filename);
		}
		else
		{
			$content = loadFile($filename);
		}


	if (isset($current_user) && $current_user->ID == 106)
		{
			$agreement_number = "XXXXXXX";
			$customer_name = "Демо-пользователь";
			$image_number = "XXXX-номер изображения";
			$image_name = "Название рисунка";
			$author_name = "Имя автора";
			$media_name = "Название компании покупателя";
			$price = "XXX-Цена";
		}


	// replace placeholders
		$content = str_replace ('#agreement_number#',$agreement_number,$content);
		$content = str_replace ('#agreement_date#',$agreement_date,$content);
		$content = str_replace ('#customer_name#',$customer_name,$content);
		$content = str_replace ('#image_number#',$image_number,$content);
		$content = str_replace ('#image_name#',$image_name,$content);
		$content = str_replace ('#author_name#',$author_name,$content);
		$content = str_replace ('#media_name#',$media_name,$content);
		$content = str_replace ('#price#',$price,$content);

	// save_license
	$license_filename = $agreement_number.".htm";
	save_license($content,$license_filename);
	$license_names_array[] = $license_filename;

	// output content
	return $content;
}

function license_name($license_code)
{
	switch($license_code)
        {
        case 'l1_price':
        $license_name = "Ограниченная";
        break;
        
        case 'l2_price':
        $license_name = "Стандартная";
        break;

        case 'l3_price':
        $license_name = "Расширенная";
        break;

        default:
        $license_name = "";
        break;
		}
	return $license_name;
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

function save_license($content,$filename)
{
	$abspath = ROOTDIR.'licenses/'; 
	$abspath_1 = ROOTDIR."licenses/";
	$abspath_2 = "Z:/home/localhost/www/licenses/";


	if (strstr($_SERVER['SCRIPT_FILENAME'],'Z:/home'))
		{$abspath = $abspath_2;}
	else if (strstr($_SERVER['SCRIPT_FILENAME'],'cb/')) 
		{$abspath = $abspath_1;}

	$myFile = $abspath.$filename;
	$fh = fopen($myFile, 'w') or die("can't open file");
	$stringData = $content;
	fwrite($fh, $stringData);
	fclose($fh);
}

?>