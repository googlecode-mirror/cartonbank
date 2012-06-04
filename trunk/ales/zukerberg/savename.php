<?php 
	
if (!isset($_REQUEST['name']) || !isset($_REQUEST['rate']))
{
	echo ("sorry, no parameters provided");
	exit;
}

include("/home/www/cb3/ales/config.php");

$name="";
$rate="";
if (isset($_REQUEST['name']) && isset($_REQUEST['secret']))
{
	$name=mysql_real_escape_string($_REQUEST['name']);
	$rate=mysql_real_escape_string($_REQUEST['rate']);
    if ($rate=='NaN'){$rate=0;}
	$secret=mysql_real_escape_string($_REQUEST['secret']);
	$views=mysql_real_escape_string($_REQUEST['views']);
	$guessed=mysql_real_escape_string($_REQUEST['guessed']);
	$bestid=mysql_real_escape_string($_REQUEST['best']);
	$worstid=mysql_real_escape_string($_REQUEST['worst']);
}

//open db connection
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);

//check if user exists
$sql = "SELECT id, rate, guessed, views from comparerate WHERE name='$name' AND password='$secret'";
$res = mysql_query($sql);
if (!$res) {die('Invalid query: ' . mysql_error());}
$count=mysql_num_rows($res);

$row = mysql_fetch_row($res);
$userid = $row[0];


//pokazh($sql);

if ($count>0){
	// user exists
	$oldviews = $row[3];
	$oldguessed = $row[2];
	$oldrate = $row[1];

	if ($oldviews > 0){
	// returning user with 0 views
	$arr = array('views' => $oldviews, 'guessed' => $oldguessed, 'rate' => $oldrate);
	$output = json_encode($arr);
	echo $output;
	exit;
	}
	//update returning user with views > 0
		$sql_update = "UPDATE `comparerate` SET `rate`=$rate, `views`=$views, `guessed`=$guessed WHERE `name`='".$name."' AND `password`='".$secret."'";
		mysql_query($sql_update);
	}
	else
	{
	//insert
		$sql_insert = "INSERT ignore INTO `comparerate` (`name`, `password`, `rate`, `guessed`, `views`) VALUES ('".$name."', '".$secret."', $rate, $guessed, $views)";
		mysql_query($sql_insert);
		$userid = mysql_insert_id();

}
	//add vote
	$sql="INSERT INTO comparerategraph (bestid, worstid, userid) VALUES ($bestid, $worstid, $userid)";
	mysql_query($sql);

mysql_close($link);
{
	echo '[]';
}
exit;
?>