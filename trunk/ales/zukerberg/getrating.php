<?php 
include("/home/www/cb3/ales/config.php");

//open db connection
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);

$sql = "SELECT name, password, rate, guessed, views FROM `comparerate` WHERE rate>0 ORDER BY rate DESC LIMIT 50";

$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);
	
$result = mysql_query($sql);
	if (!$result) {die('Invalid query: ' . mysql_error());}

if (isset($_REQUEST['u']) && isset($_REQUEST['s']))
{
	$u = $_REQUEST['u'];
	$s = $_REQUEST['s'];
}
$id =1;

$output="<table>";
$output .= "<tr class='tr'>";
$output .= "<td class='td'>";
$output .= "место";
$output .= "</td>";
$output .= "<td class='td'>";
$output .= "имя";
$output .= "</td>";
$output .= "<td class='td'>";
$output .= "рейт";
$output .= "</td>";
$output .= "<td class='td'>";
$output .= "угадано";
$output .= "</td>";
$output .= "<td class='td'>";
$output .= "из";
$output .= "</td>";
$output .= "</tr>";

while ($row = mysql_fetch_assoc($result)){

if ($row['name']==$u && $row['password']==$s){
	$class='trhi';
	$place = "id='myplace'";
}
else{
	$class='tr';
	$place = '';
}

	$output .= "<tr class='".$class."'>";
		$output .= "<td class='td' ".$place.">";
		$output .= $id;
		$output .= "</td>";
		$output .= "<td class='td'>";
		$output .= $row['name'];
		$output .= "</td>";
		$output .= "<td class='td'>";
		$output .= $row['rate'];
		$output .= "</td>";
		$output .= "<td class='td'>";
		$output .= $row['guessed'];
		$output .= "</td>";
		$output .= "<td class='td'>";
		$output .= $row['views'];
		$output .= "</td>";
	$output .= "</tr>";
	$id = $id+1;
}

$output .= "</table>";

mysql_close($link);

echo $output;
exit;
?>