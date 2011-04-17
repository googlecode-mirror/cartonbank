<?php
// http://www.facebook.com/developers/apps.php?app_id=264530743602 -- application
// http://www.facebook.com/apps/application.php?id=264530743602&sk=wall -- the Wall
// application settings
	$redirect_url = 'http://cartoonbank.ru/wp-content/plugins/fb-post-ales/';
	$app_id = '264530743602'; //“YOUR_APP_ID”; Cartoonist.name app
	$app_secret = '05e24c30ac95a33d726f6d087c3c00f4'; //"YOUR_APP_SECRET";
	$theMessage = 'DA';


// 1
$theCode = get_code();

ee($theCode,"theCode");

// 2
$theAccessToken = get_token($theCode);

ee($theAccessToken, "theAccessToken2");

// 3
//$result = publish_post($theAccessToken);

// 4
	  $apprequest_url = "https://graph.facebook.com/feed";
	  $parameters = "?" . $theAccessToken . "&message=" . $theMessage . "&id=" . $app_id . "&method=post";
	  $myurl = $apprequest_url . $parameters;
		echo $myurl;
	  $result = file_get_contents($myurl);
	  echo "result = " . $result;



exit; 
/*
		http://stackoverflow.com/questions/4729477/what-is-the-url-to-a-facebook-open-graph-post-id
		I found out, for a graph id 1099696306_140549259338782 the links is build like this: http://www.facebook.com/1099696306/posts/140549259338782
		*/

		/*  $app_id = '264530743602'; //“YOUR_APP_ID”; Cartoonist.name app
		  $app_secret = '05e24c30ac95a33d726f6d087c3c00f4'; //"YOUR_APP_SECRET";

		  $mymessage = urlencode("Hello World!");

		  $access_token_url = "https://graph.facebook.com/oauth/authorize"; 
		  $parameters = "client_id=" . $app_id . '&redirect_uri=http://cartoonist.name/';

		echo $access_token_url . "?" . $parameters;
		exit;

		  $access_token = file_get_contents($access_token_url . "?" . $parameters);
		  echo $access_token;
		  exit;
	*/
	  exit;

	  $apprequest_url = "https://graph.facebook.com/feed";
	  $parameters = "?" . $access_token . "&message=" . $mymessage . "&id=" . $ogurl . "&method=post";
	  $myurl = $apprequest_url . $parameters;

	  $result = file_get_contents($myurl);
	  echo "post_id" . $result;

	//Initialize the Curl session
		$ch = curl_init();
		
		//Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//Set the URL
		$URL = 'https://graph.facebook.com/oauth/access_token?' . http_build_query(array(
			'client_id'     => '264530743602',
			'type'          => 'web_server',
			'redirect_uri'    => 'http://cartoonist.name/',
			'client_secret' => '05e24c30ac95a33d726f6d087c3c00f4'));
		curl_setopt($ch, CURLOPT_URL, $URL);
		
		//echo "The URL is ".$URL;
		//Execute the fetch
		$theAccessToken = curl_exec($ch);
		//Close the connection
		curl_close($ch);
?>

<?
/*
	//https://api.facebook.com/method/stream.publish?access_token=66LRNT&id=264530743602&message=hiii&caption=My_App&description=Its_just_description.
	//190 Invalid OAuth 2.0 Access Token method stream.publish access_token 66LRNT id 264530743602 message hiii caption My_App description Its_just_description.


	https://api.facebook.com/method/stream.publish?
	access_token=66LRNT
	&id=264530743602
	&message=test.
	&caption=My_App
	&description=Its_just_description.


	curl -F 'access_token=...' \
		 -F 'message=Hello, Arjun. I like this new API.' \
		 https://graph.facebook.com/arjun/feed
*/
?>

<?php
	//http://www.moskjis.com/other-platforms/publish-facebook-page-wall-from-your-site

	define('FB_APIKEY', 'ca942722a1718dbd60cb5cc112192fab');
	define('FB_SECRET', '05e24c30ac95a33d726f6d087c3c00f4');
	define('FB_SESSION', '66LRNT');

	require_once('facebook/facebook.php');
	 
	echo "post on wall";
	echo "<br/>";
	 

	 $facebook = new Facebook(FB_APIKEY, FB_SECRET);
	 $facebook->api_client->session_key = FB_SESSION;





	try {
	 $facebook = new Facebook(FB_APIKEY, FB_SECRET);
	 $facebook->api_client->session_key = FB_SESSION;
	 $facebook->api_client->expires = 0;
	 $message = '7';
	 /*
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
	 */
	 $target_id = "<Target Id>";
	 $session_key = FB_SESSION;
	 /*
	 if( $facebook->api('/me/feed', 'POST', $attachment)) {
	 echo "Added on FB Wall";
	 */

	$access_token='66LRNT';


	$result = $facebook->api(
		'/me/feed/',
		'post',
		array('access_token' => $access_token, 'message' => 'just a test...')
	);
	echo ("<br>result:".$result);

	// }

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


function fw($text)
{
	$fp = fopen('_kloplog.htm', 'w');
	fwrite($fp, '<br />');
	fwrite($fp, $text);
	fclose($fp);
}

?>

<?
function get_code()
{
	//http://sudocode.net/article/368/how-to-get-the-access-token-for-a-facebook-application-in-php/
	$code = '';	
	$ogurl = "http://www.facebook.com/pages/cartoonistname/181958139942"; // "YOUR_OPEN_GRAPH_URL"; 
	global $redirect_url,$app_id,$app_secret;

	if (isset($_REQUEST["code"]))
	{
		$code=$_REQUEST["code"];
		fw ("code = " . $code);
	}
	// get Code
	// code = Bp82F2_-jiewKiK3Igewst42NyVhmpZ49ZKWOGOFrig.eyJpdiI6Im1ZcnJOSHp5cVBPaTRETGR6RXpWZFEifQ._P4JcJ7hLCYpxHi1e3R9QTLSiQIw9Wrkid_fmtxjW4Fa570efRxL9yRbGolwyv13fpz3fiI3q8NntLUOs4faZabxM8oQSe6pZniQ7Za9nlZ28HyGEuDvW2cy0NEqQepx
	$code = 'QBf8NZkGokBqk_zJFhlqggV-bBqv1xL-FJMrGPm0yaE.eyJpdiI6IkN0ZkZBVTdDeE1fUU1iWXpMN0NlaFEifQ.zH5YTazRlWZiJEmoNvwmVNfIanjUzDXJ8U0wKOD_JPVg5i27XhOGI2wgErcjcfD1Dw5q5BEj60sV0jMOe8j90tBHgb08uXqDarisYSJepKhh7HDcvmlznzrf_cUpH_BJ';
			$authorize_url = "https://graph.facebook.com/oauth/authorize"; 
			$parameters = "client_id=" . $app_id . '&redirect_uri=' . $redirect_url;
	
	
	//$result = file_get_contents($authorize_url . "?" . $parameters);
	//fw (".result = " . $result);
	//fw (".authorize_url = " . $authorize_url . "?" . $parameters); //access_code=https://graph.facebook.com/oauth/authorize?client_id=264530743602&redirect_uri=http://cartoonbank.ru/wp-content/plugins/fb-post-ales/

return $code;
}

function get_token($code)
{
	// get Access Token
		global $redirect_url, $app_id, $app_secret;
		$access_token_url = "https://graph.facebook.com/oauth/access_token"; 
		$parameters = "type=client_credentials&client_id=" . $app_id . "&client_secret=" . $app_secret;

		if ($code!='')
		{
			$parameters = $parameters . '&code=' . $code;
		}
		
		$parameters = $parameters . '&redirect_uri=' . $redirect_url;

	  //echo "access_token_url=" . $access_token_url . "?" . $parameters . "<br />";
	  
	//Initialize the Curl session
		$ch = curl_init();
		//Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//Set the URL

		$URL = 'https://graph.facebook.com/oauth/access_token?' . http_build_query(array(
			'client_id'     => $app_id,
			'type'          => 'web_server',
			'code'          => $code,
			'redirect_uri'  => $redirect_url,
			'client_secret' => $app_secret));

		ee($URL,"access token url");
		
		curl_setopt($ch, CURLOPT_URL, $URL);
		
		//echo "The URL is ".$URL;
		//Execute the fetch
		$theAccessToken = curl_exec($ch);

		ee($theAccessToken,"theAccessToken1");

		//Close the connection
		curl_close($ch);
		
		return $theAccessToken;
}

function publish_post($theAccessToken)
{

	  //$access_token = file_get_contents($access_token_url . "?" . $parameters);
	  //echo $access_token;

	// make a post
	/*
	curl -F 'access_token=...' \
		 -F 'message=Check out this funny article' \
		 -F 'link=http://www.example.com/article.html' \
		 -F 'picture=http://www.example.com/article-thumbnail.jpg' \
		 -F 'name=Article Title' \
		 -F 'caption=Caption for the link' \
		 -F 'description=Longer description of the link' \
		 -F 'actions={"name": "View on Zombo", "link": "http://www.zombo.com"}' \
		 -F 'privacy={"value": "ALL_FRIENDS"}' \
		 -F 'targeting= {"countries":"US","regions":"6,53","locales":"6"}' \
		 https://graph.facebook.com/me/feed
		 */
		$ch = curl_init();
		//Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//Set the URL
		$URL = 'https://graph.facebook.com/me/feed?' . http_build_query(array(
			'access_token'     => $theAccessToken,
			'message'          => 'test...',
			'link'          => 'http://cartoonbank.ru'));
		curl_setopt($ch, CURLOPT_URL, $URL);
		
		ee ($URL, "The post URL");
		//Execute the fetch
		$publish = curl_exec($ch);

		fw("publish = " .$publish);
		//Close the connection
		curl_close($ch);

}

function ee($to_print,$comment = '')
{
	$response = "<div style='margin:2px;padding-left:6px;background-color:#FFCC66;border:1px solid #CC0066;'><pre><b>".$comment.":</b> ".print_r($to_print,true)."</pre><br>$to_print</div>";
	echo ($response); 
}

?>