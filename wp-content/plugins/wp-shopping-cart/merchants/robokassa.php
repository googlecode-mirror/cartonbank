<?
$nzshpcrt_gateways[$num]['name'] = 'Robokassa';
$nzshpcrt_gateways[$num]['internalname'] = 'robokassa';
$nzshpcrt_gateways[$num]['function'] = 'gateway_robokassa';
$nzshpcrt_gateways[$num]['form'] = "form_robokassa";
$nzshpcrt_gateways[$num]['submit_function'] = "submit_robokassa";


function gateway_robokassa($seperator, $sessionid)
{
	
	global $wpdb;
	$purchase_log_sql = "SELECT * FROM `wp_purchase_logs` WHERE `sessionid`= ".$sessionid." LIMIT 1";
	$purchase_log = $wpdb->get_results($purchase_log_sql,ARRAY_A) ;

	if (isset($purchase_log[0]['id']))
	{$purchaseid = $purchase_log[0]['id'];}
	else{$purchaseid = '0';}

//pokazh($purchase_log,"purchase_log");
	$cart_sql = "SELECT * FROM `wp_cart_contents` WHERE `purchaseid`='".$purchase_log[0]['id']."'";
	$cart = $wpdb->get_results($cart_sql,ARRAY_A) ; 
	$cart_description = json_encode($cart);

//pokazh($cart,"cart");
//exit;

// 2.
// Оплата заданной суммы с выбором валюты на сайте ROBOKASSA
// Payment of the set sum with a choice of currency on site ROBOKASSA

// регистрационная информация (логин, пароль #1)
// registration info (login, password #1)
$mrh_login = "cartoonbank";
$mrh_pass1 = "Atljhjdbx1";

// номер заказа
// number of order
$inv_id = $purchaseid;

// описание заказа
// order description
//$inv_desc = "ROBOKASSA Advanced User Guide";
$inv_desc = $cart_description;

// сумма заказа
// sum of order
//$out_summ = "200";
$out_summ = $_SESSION['total'];

// тип товара
// code of goods
$shp_item = "cartoons";

// предлагаемая валюта платежа
// default payment e-currency
$in_curr = "";

// язык
// language
$culture = "ru";

// формирование подписи
// generate signature
$crc  = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item");

// форма оплаты товара
// payment form
/*
print "<html>".
      "<form action='http://test.robokassa.ru/Index.aspx' method=POST>".
      "<input type=hidden name=MrchLogin value=$mrh_login>".
      "<input type=hidden name=OutSum value=$out_summ>".
      "<input type=hidden name=InvId value=$inv_id>".
      "<input type=hidden name=Desc value='$inv_desc'>".
      "<input type=hidden name=SignatureValue value=$crc>".
      "<input type=hidden name=Shp_item value='$shp_item'>".
      "<input type=hidden name=IncCurrLabel value=$in_curr>".
      "<input type=hidden name=Culture value=$culture>".
      "<input type=submit value='Pay'>".
      "</form></html>";
*/

$output = '';
$output .= 'MrchLogin='.$mrh_login;
$output .= '&OutSum='.$out_summ;
$output .= '&InvId='.$inv_id;
$output .= '&Desc='.$inv_desc;
$output .= '&SignatureValue='.$crc;
$output .= '&Shp_item='.$shp_item;
$output .= '&IncCurrLabel='.$in_curr;
$output .= '&Culture='.$culture;

header("Location: http://test.robokassa.ru/Index.aspx?".$output);
exit;
}


?>
