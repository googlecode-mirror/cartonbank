<?php 

if (!isset($_GET['update']) || !is_numeric($_GET['update']))
{
	echo ("sorry, no parameters provided");
	exit;
}


// configuration
include("/home/www/cb3/ales/config.php");

//open db connection
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);


$sql="SELECT 
post as id, 
COUNT(post) AS votes, 
SUM(wp_fsr_user.points) AS sum, 
vote_date 
FROM wp_fsr_user
GROUP BY 1";


$result = mysql_query("$sql");
if (!$result) {die('Invalid query: ' . mysql_error());}

$count=mysql_num_rows($result);

while($row=mysql_fetch_array($result))
{
		$id = $row['id'];
		$votes = $row['votes'];
		$sum = $row['sum'];
		$avg = $sum/$votes;

	$sql_insert = "update wp_product_list set votes=$votes, votes_sum=$sum where id=$id";
		//pokazh ($sql_insert);

	$res = mysql_query($sql_insert);
	if (!$res) {die('<br />'.$sql_insert.'<br />Invalid insert query: ' . mysql_error());}
}

mysql_close($link);

echo $count." rows are updated";


exit;
?>