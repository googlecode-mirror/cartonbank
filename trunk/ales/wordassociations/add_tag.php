<?
include("/home/www/cb3/ales/config.php");
global $imagepath;

if(isset($_GET['wrd']) && $_GET['wrd']!='' && isset($_GET['id']) && is_numeric($_GET['id']))
{
	$id = $_GET['id'];

	// update database
	$sql = "select additional_description from wp_product_list where id=".$id." LIMIT 1";
	
	$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
	mysql_set_charset('utf8',$link);
	
	$result = mysql_query($sql);
	if (!$result) {die('Invalid query: ' . mysql_error());}

	$product=mysql_fetch_array($result);
	$original_tags = $product['additional_description'];

	$new_additional_description = $original_tags.", ".trim($_GET['wrd']);
	$sql = "update wp_product_list set additional_description = '".$new_additional_description."' where id=".$id;
	//fw ($sql);
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