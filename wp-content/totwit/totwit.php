<?php
if (isset($_GET['cid']) && is_numeric($_GET['cid']))
{
$_id = $_GET['cid'];
$_message = 'New cartoon at http://cartoonbank.ru/?page_id=29&cartoonid='.$_id;

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
}
?>