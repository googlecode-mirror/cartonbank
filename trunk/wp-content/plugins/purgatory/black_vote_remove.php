<?php
fw("inin");

include("config.php");

fw("333");

$ip = 'none';
if (isset($_GET['ip']))
	{$ip = $_GET['ip'];}
elseif (isset($_SERVER['REMOTE_ADDR']) and $_SERVER['REMOTE_ADDR'] != '')
	{$ip=$_SERVER['REMOTE_ADDR'];}

fw("\n\r _GET['id']=".$_GET['id']);

if($_POST['id'] or $_GET['id'])
{
if (isset($_POST['id']))
	$id=$_POST['id'];
elseif (isset($_GET['id']))
	$id=$_GET['id'];
	$id = mysql_escape_String($id);

	$sql = "update al_editors_votes set black=0 where image_id='$id'";
	mysql_query( $sql);

	$result=mysql_query("select black from al_editors_votes where image_id='$id'");

	$row=mysql_fetch_array($result);
	$black_value=$row['black'];

fw("\n\r up_value=".$black_value);
	$sql = "update wp_product_list set visible=1 where id='$id'";
	fw("\n\r sql=".$sql);
	mysql_query( $sql);

	echo $black_value;
}

function fw($text)
{
$fp = fopen('_kloplog.txt', 'a');
fwrite($fp, $text);
fclose($fp);
}

?>
