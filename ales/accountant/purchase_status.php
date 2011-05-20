<?
include("/home/www/cb/ales/config.php");
global $imagepath;

if(isset($_GET['sta']) && is_numeric($_GET['sta']) && isset($_GET['purch_id']) && is_numeric($_GET['purch_id']))
{
	$purch_id = $_GET['purch_id'];

	$new_processed_id = $_GET['sta'];

	// update database
	/*
	$sql = "select additional_description from wp_product_list where id=".$id." LIMIT 1";
	
	$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
	mysql_set_charset('utf8',$link);
	
	$result = mysql_query($sql);
	if (!$result) {die('Invalid query: ' . mysql_error());}

	$product=mysql_fetch_array($result);
	$original_tags = $product['additional_description'];

	$new_additional_description = $original_tags.", ".trim($_GET['sta']);
	*/

	$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
	mysql_set_charset('utf8',$link);
	
	$sql = "update wp_purchase_logs set processed = '".$new_processed_id."' where id=".$purch_id;
	fw ($sql);
	$result = mysql_query($sql);
	if (!$result) {die('Invalid query: ' . mysql_error());}
}

return;

function fw($text)
{
	$fp = fopen('_kloplog.txt', 'w');
	fwrite($fp, $text);
	fclose($fp);
}

?>