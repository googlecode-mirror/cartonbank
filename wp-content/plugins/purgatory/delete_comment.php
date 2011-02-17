<?php

include("config.php");

if($_POST['id'] or $_GET['id'])
{
if (isset($_POST['id']))
	$id=$_POST['id'];
elseif (isset($_GET['id']))
	$id=$_GET['id'];


	$id = mysql_escape_String($id);


	$sql = "delete from wp_comments where Comment_ID=".$id;

	$ip_sql=mysql_query($sql);
	header('Location: http://cartoonbank.ru/wp-admin/admin.php?page=purgatory/purgatory.php');
	echo $sql;

}

function fw($text)
{
$fp = fopen('_kloplog.txt', 'a');
fwrite($fp, $text);
fclose($fp);
}

?>
