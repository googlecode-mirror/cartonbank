<?
https://api.facebook.com/method/stream.publish?access_token=66LRNT&id=264530743602&message=hiii&caption=My_App&description=Its_just_description.
//190 Invalid OAuth 2.0 Access Token method stream.publish access_token 66LRNT id 264530743602 message hiii caption My_App description Its_just_description.


https://api.facebook.com/method/stream.publish?
access_token=66LRNT
&id=264530743602
&message=hiii
&caption=My_App
&description=Its_just_description.


curl -F 'access_token=...' \
     -F 'message=Hello, Arjun. I like this new API.' \
     https://graph.facebook.com/arjun/feed
?>

<?php
//http://www.moskjis.com/other-platforms/publish-facebook-page-wall-from-your-site

define('FB_APIKEY', 'ca942722a1718dbd60cb5cc112192fab');
define('FB_SECRET', '05e24c30ac95a33d726f6d087c3c00f4');
define('FB_SESSION', '66LRNT');

require_once('facebook/facebook.php');
 
echo "post on wall";
echo "<br/>";
 
try {
 $facebook = new Facebook(FB_APIKEY, FB_SECRET);
 $facebook->api_client->session_key = FB_SESSION;
 $facebook->api_client->expires = 0;
 $message = '';
 
 $attachment = array(
 'name' => $_POST["name"],
 'href' => $_POST["href"],
 'description' => $_POST["description"],
 'media' => array(array('type' => 'image',
 'src' => $_POST["src"],
 'href' => $_POST["href"])));
 
 $action_links = array( array('text' => 'Visit Us', 'href' => 'cartoonbank.ru'));
 
 $attachment = json_encode($attachment);
 $action_links = json_encode($action_links);
 
 $target_id = "<Target Id>";
 $session_key = FB_SESSION;
 
 if( $facebook->api('/me/feed', 'POST', $attachment)) {
 echo "Added on FB Wall";
 }

 //if( $facebook->api_client->stream_publish($message, $attachment, $action_links, null, $target_id)) {
 //echo "Added on FB Wall";
 //}
} catch(Exception $e) {
 echo $e . "<br />";
 }

?>

<?php
exit;
define('FB_APIKEY', 'ca942722a1718dbd60cb5cc112192fab');
define('FB_SECRET', '05e24c30ac95a33d726f6d087c3c00f4');
define('FB_SESSION', '66LRNT');
require_once('facebook/facebook.php');
echo "post on wall";
try {

	$facebook = new Facebook(FB_APIKEY, FB_SECRET);
	$facebook->api_client->session_key = FB_SESSION;
	$fetch =	array(	'friends' =>
				array(	'pattern' => '.*',
						'query' => "select uid2 from friend where uid1={$user}"));

	echo $facebook->api_client->admin_setAppProperties(array('preload_fql' => json_encode($fetch)));
//Fatal error: Call to undefined method stdClass::admin_setAppProperties() in /home/www/cb3/wp-content/plugins/fb-post-ales/index.php on line 15
//lamp:/home/www/cb3/wp-content/plugins/fb-post-ales#

	$message = 'From My App: publish steven on facebook';
	if( $facebook->api_client->stream_publish($message))
	echo "Added on FB Wall";
} 
catch(Exception $e) {
	echo $e . "<br />";
}

?>

<?php
exit;

//Generate one time session key (permanent session key)

// FB_APIKEY is your facebook application api key

// FB_SECRET is your application secrete key

$FB_APIKEY="ca942722a1718dbd60cb5cc112192fab";

$FB_SECRET="05e24c30ac95a33d726f6d087c3c00f4";

$fb = new FacebookRestClient($FB_APIKEY, $FB_SECRET);

$testtoken= "66LRNT"; // Replace this value with your Token Value

$result = $fb->call_method('facebook.auth.getSession',

array('auth_token' => $testtoken, 'generate_session_secret' => true));

echo "<br /><pre>";

print_r($result);

echo $session_key = $result['session_key'];

?>


