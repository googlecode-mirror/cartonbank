<?php
if (isset($_GET['cid']) && is_numeric($_GET['cid']))
{
$_id = $_GET['cid'];

include("config.php");

// Get the name
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);

$result=mysql_query("SELECT l.name as Title, l.description as Description,  l.additional_description as Tags, b.name as Artist FROM `wp_product_list` as l, `wp_product_brands` as b WHERE l.brand=b.id
AND l.id = ".$_id);
$row=mysql_fetch_array($result);
$_title=htmlentities($row['Title'], ENT_QUOTES | ENT_IGNORE, "UTF-8");
$_artist=htmlentities($row['Artist'], ENT_QUOTES | ENT_IGNORE, "UTF-8");
$_description=htmlentities($row['Description'], ENT_QUOTES | ENT_IGNORE, "UTF-8");

if (empty($_artist) | empty($_title))
	{
		$_message = 'New cartoon at http://cartoonbank.ru/?page_id=29&cartoonid='.$_id;
	}
else
	{
		$_message = $_artist.': «'.$_title.'» '.' http://cartoonbank.ru/?page_id=29&cartoonid='.$_id.' '.$_description;
	}

// cartoonbank.ru@gmail.com twitter:
$consumerKey    = 'KnOgRuM8K7bomo4lESUMQ'; //'<insert your consumer key';
$consumerSecret = 'ZAccab7HuF8s6efTo6WIFDESPTrXfdbUAgnu5qP4w'; //'<insert your consumer secret>';
$oAuthToken     = '199770247-dbkIh6ZdQPULQWCweKeQi4vuRbmg4PPoJ7lLtBrX'; //'<insert your access token>';
$oAuthSecret    = 'ABPlCFZtEWla7Nxiwf1ba9ubKxmdgmijLkXewbmmJNU'; //'<insert your token secret>';
 
require_once('twitteroauth.php');
 
// create a new instance
$tweet = new TwitterOAuth($consumerKey, $consumerSecret, $oAuthToken, $oAuthSecret);
 
//send a tweet
$tweet->post('statuses/update', array('status' => $_message));
/*
  $headers = "From: igor.aleshin@gmail.com\r\n" .
			   'X-Mailer: PHP/' . phpversion() . "\r\n" .
			   "MIME-Version: 1.0\r\n" .
			   "Content-Type: text/html; charset=utf-8\r\n" .
			   "Content-Transfer-Encoding: 8bit\r\n\r\n";
//mail("igor.aleshin@gmail.com", 'twit', $_message, $headers);
*/
}
?>