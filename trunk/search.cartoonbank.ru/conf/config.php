<?php
$lang = 'ru';
//$lang = 'en';

if ($lang == 'en'){
	require_once 'terms.en';
	define('LANGUAGE', 'en');
	define('PRODUCT_TABLE', '`wp_product_list_en`');
}else{
	require_once 'terms.ru';
	define('LANGUAGE', 'ru');
	define('PRODUCT_TABLE', '`wp_product_list`');
}
define('ITEMS_ON_PAGE', '40');

$mysql_hostname = "localhost";
$mysql_user = "z58365_cbru3";
$mysql_password = "greenbat";
$mysql_database = "cartoonbankru";


$bd = mysql_connect($mysql_hostname, $mysql_user, $mysql_password) or die("Could not connect database");
mysql_select_db($mysql_database, $bd) or die("Could not select database");
mysql_set_charset('utf8',$bd);

function pokazh($to_print,$comment = '')
{
	$response = "<div style='margin:2px;padding-left:6px;background-color:#FFCC66;border:1px solid #CC0066;'><pre><b>".$comment.":</b> ".print_r($to_print,true)."</pre></div>";
	echo ($response); 
}

?>