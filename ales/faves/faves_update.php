<?php 
//http://cartoonbank.ru/ales/faves/faves_update.php?p=2123&uid=16
if (!isset($_GET['p']) || !is_numeric($_GET['p']) || !isset($_GET['uid']) || !is_numeric($_GET['uid']))
{
	echo ("sorry, no parameters provided");
	exit;
}

// configuration
include("/home/www/cb/ales/config.php");
$ret = ""; // code to return
$upd = 0; // update database

//open db connection
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);

if (isset($_GET['p']) && is_numeric($_GET['p']) && isset($_GET['uid']) && is_numeric($_GET['uid']))
{
	$uid = trim($_GET['uid']);
	$p = trim($_GET['p']);
}

if (isset($_GET['upd']) && is_numeric($_GET['upd']) && $_GET['upd']==1)
{
	$upd=1;
}

$sql="SELECT * from favorites where user_id=$uid and image_id=$p";

$result = mysql_query("$sql");
if (!$result) {die('Invalid query: ' . mysql_error());}

$count=mysql_num_rows($result);

if ($upd != 1)
{
	//request for current thumb
	if ($count>0)
	{
		// fave exists
		$ret = "<img src='http://cartoonbank.ru/img/thumbup.jpg' border='0' align=top title='удалить из списка любимых' style='cursor: pointer;'>";
	}
	else
	{
		// no faves
		$ret = "<img src='http://cartoonbank.ru/img/thumbupp.jpg' border='0' align=top title='добавить в любимое' style='cursor: pointer;'>";
	}
}
else
{
	// make changes to fave
	if ($count>0)
	{
		// unset favorite
		$sql = "delete from favorites where user_id=$uid and image_id=$p";
		$result = mysql_query("$sql");
		$ret = "<img src='http://cartoonbank.ru/img/thumbupp.jpg' border='0' align=top title='добавить в любимое' style='cursor: pointer;'>";
	}
	else
	{
		//set favotite
		$sql = "insert ignore into favorites (user_id, image_id) values ($uid, $p)";
		$result = mysql_query("$sql");
		$ret = "<img src='http://cartoonbank.ru/img/thumbup.jpg' border='0' align=top title='удалить из списка любимых' style='cursor: pointer;'>";
	}
}
mysql_close($link);
echo $ret;
exit;
?>