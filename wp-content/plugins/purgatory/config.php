<?php
$mysql_hostname = "localhost";
$mysql_user = "z58365_cbru3";
$mysql_password = "greenbat";
$mysql_database = "cartoonbankru";

$limit = 3;
$limit_plus = 2;
$limit_minus = 3;
$limit_black = 1;

$bd = mysql_connect($mysql_hostname, $mysql_user, $mysql_password) or die("Could not connect database");
mysql_select_db($mysql_database, $bd) or die("Could not select database");

?>