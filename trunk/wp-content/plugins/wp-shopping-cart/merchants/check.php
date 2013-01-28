<?php    
//require_once('./admin.php'); 
$nzshpcrt_gateways[$num]['name'] = 'Оплата через Сбербанк';
$nzshpcrt_gateways[$num]['internalname'] = 'check';
$nzshpcrt_gateways[$num]['function'] = 'gateway_check';
$nzshpcrt_gateways[$num]['form'] = "form_check";
$nzshpcrt_gateways[$num]['submit_function'] = "submit_check";

function gateway_wallet_old($seperator, $sessionid)
{
  global $userdata, $user_ID, $wpdb;
  $userdata->wallet = (float) $userdata->wallet;
  $totalsum = (float) $_SESSION['total']; 
  $transact_url = "";
  if ($userdata->wallet >= $totalsum)
  {
    $userdata->wallet = $userdata->wallet - $totalsum;
    $wpdb->query("UPDATE `".$wpdb->prefix."users` SET `wallet` = ".$userdata->wallet." WHERE `id` ='".$user_ID."' LIMIT 1 ;");
    $transact_url = get_option('transact_url');
    $sql = "UPDATE `wp_purchase_logs` SET `processed`= '2' WHERE `sessionid`=".$sessionid;
    $wpdb->query($sql);
    $_SESSION['wallet'] = 'success';
	$_SESSION['WpscGatewayErrorMessage'] = '';
  }
  else
  {
      $_SESSION['wallet'] = 'decline';
      $_SESSION['WpscGatewayErrorMessage'] = "Средств на вашем Личном Счёте недостаточно для проведения транзакции.";
      $transact_url = get_option('checkout_url'); 
  }
  header("Location: ".$transact_url.$seperator."sessionid=".$sessionid);
  exit(); 
}

function submit_check()
{
	$user_id = (int) $user_id;
	$current_user = wp_get_current_user();    print_r($current_user); exit();
      //$profileuser = get_user_to_edit($user_id); 
      //$wallet = 
  return true;
}


function get_total()
{
$total = '';
if($_SESSION['total'] != null)
	{	
		$total = $_SESSION['total'];
	}
	$total .= "руб.00коп.";
return $total;
}

function get_content()
{
	$strAllOrders = '';
	$strLicense = '';

	if($_SESSION['nzshpcrt_cart'] != null)
	{ 
	foreach($_SESSION['nzshpcrt_cart'] as $cart_key => $cart_item)
	  {
		$strAllOrders .= $_SESSION['nzshpcrt_cart'][$cart_key]->product_id . "";

		$strLicense = $_SESSION['nzshpcrt_cart'][$cart_key]->license;
		if ($strLicense == 'l1_price')
		{
			$strAllOrders .= 'огр.,';
		}
		else if ($strLicense == 'l2_price')
		{
			$strAllOrders .= 'ст.,';
		}
		else if ($strLicense == 'l3_price')
		{
			$strAllOrders .= 'рас.,';
		}
	  }
	}

return split(',',$strAllOrders);

/*
SESSION:Array
	(
		[cart_paid] => 
		[selected_country] => 
		[nzshpcrt_cart] => Array
			(
				[1] => cart_item Object
					(
						[product_id] => 6155
						[product_variations] => 
						[quantity] => 1
						[name] => Ожидаемый подарок
						[price] => 200.00
						[license] => l1_price
						[author] => Тарасенко Валерий
					)


			)*/
}


function gateway_check()
{
//pokazh($_SESSION,"session");
$label_line = "";
$label_line2 = "";
$label_line3 = "";

	$my_img = "./img/check-2.png";
	$my_img = @imagecreatefrompng($my_img);
	$text_colour = imagecolorallocate( $my_img, 0, 0, 0 );
	$line_colour = imagecolorallocate( $my_img, 128, 255, 0 );
	
	$font = imageloadfont('./fonts/ArialUnicode.gdf');

$arrContent = get_content();


$arrSize = count($arrContent);

$arr1 = array_slice($arrContent, 0, 3);

	$label_line =  implode (",",$arr1);
	if ($arrSize > 3) $label_line2 =  implode (",",array_slice($arrContent, 3, 4));
	if ($arrSize > 7) $label_line3 =  implode (",",array_slice($arrContent, 7, 4));

	$label_line = iconv("UTF-8","windows-1251", $label_line);
	$label_line2 = iconv("UTF-8","windows-1251", $label_line2);
	$label_line3 = iconv("UTF-8","windows-1251", $label_line3);
	
	imagestring( $my_img, $font, 338, 186, $label_line,  $text_colour ); //  pay for images 1
	imagestring( $my_img, $font, 338, 473, $label_line,  $text_colour ); //  pay for images 2

	imagestring( $my_img, $font, 215, 214, $label_line2,  $text_colour ); //  pay for images 1a
	imagestring( $my_img, $font, 210, 503, $label_line2,  $text_colour ); //  pay for images 2a

	imagestring( $my_img, $font, 215, 241, $label_line3,  $text_colour ); //  pay for images 1b
	imagestring( $my_img, $font, 210, 530, $label_line3,  $text_colour ); //  pay for images 2b


	$label_line =  get_total();// "9024 руб.00 коп.";
	$label_line = iconv("UTF-8","windows-1251", $label_line);
	imagestring( $my_img, $font, 258, 270, $label_line,  $text_colour ); //  Total 1

	imagestring( $my_img, $font, 258, 560, $label_line,  $text_colour ); //  Total 2
	
	header( "Content-type: image/png" );
	imagepng( $my_img );
	//imagecolordeallocate( $line_color );
	//imagecolordeallocate( $text_color );
	imagedestroy( $my_img );

}

?>
