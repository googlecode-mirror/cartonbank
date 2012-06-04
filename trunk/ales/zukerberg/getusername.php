<?php 
include("/home/www/cb3/ales/config.php");

if(!$_REQUEST["uid"]){
	exit;
}
else{
	$uid = $_REQUEST["uid"];
}

//open db connection
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);

$sql = "SELECT `user_login` as name FROM  `wp_users` WHERE id = ".$uid;

$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);
	
$result = mysql_query($sql);

if (!$result) {die('Invalid query: ' . mysql_error());}

while ($row = mysql_fetch_assoc($result)){
	$name = $row['name'];
}

mysql_close($link);

echo $name;
?>