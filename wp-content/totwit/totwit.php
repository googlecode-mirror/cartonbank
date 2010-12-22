<?php
if (isset($_GET['artist']) && isset($_GET['cid']) && is_numeric($_GET['cid']))
{
$_artist = $_GET['artist'];
$_id = $_GET['cid'];
$_message = 'New cartoon by '.$_artist.': http://cartoonbank.ru/?page_id=29&cartoonid='.$_id;

$consumerKey    = 'KnOgRuM8K7bomo4lESUMQ'; //'<insert your consumer key';
$consumerSecret = 'ZAccab7HuF8s6efTo6WIFDESPTrXfdbUAgnu5qP4w'; //'<insert your consumer secret>';
$oAuthToken     = '199770247-dbkIh6ZdQPULQWCweKeQi4vuRbmg4PPoJ7lLtBrX'; //'<insert your access token>';
$oAuthSecret    = 'ABPlCFZtEWla7Nxiwf1ba9ubKxmdgmijLkXewbmmJNU'; //'<insert your token secret>';
 
//require_once($_SERVER['DOCUMENT_ROOT'].'/<insert path to twitteroauth>/twitteroauth.php');
require_once('twitteroauth.php');
 
// create a new instance
$tweet = new TwitterOAuth($consumerKey, $consumerSecret, $oAuthToken, $oAuthSecret);
 
//send a tweet
$tweet->post('statuses/update', array('status' => $_message));
echo $_message;
}
else 
{
	echo "no luck";
}
?>