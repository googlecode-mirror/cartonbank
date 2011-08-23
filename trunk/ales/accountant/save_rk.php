<?
include("/home/www/cb3/ales/config.php");

if (isset($_POST['id']) && is_numeric($_POST['id']) && isset($_POST['value']) && is_numeric($_POST['value']))
{
	if (save_new_value($_POST['id'],$_POST['value']))
		echo $_POST['value'];
	else
		echo "ошибка сохранения";
}
else
{
	echo "ошибка данных";
}







function save_new_value($id,$value)
{
	$sql = "update wp_cart_contents set actual_money = '".$value."' where purchaseid=".$id;
	//fw ($sql);
	$result = mysql_query($sql);
	if (!$result) {die('Invalid query: ' . mysql_error());}
	return true;
}

function fw($text)
{
	$fp = fopen('/home/www/cb3/ales/_kloplog.txt', 'w');
	fwrite($fp, $text);
	fclose($fp);
}


?>