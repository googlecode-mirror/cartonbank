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
	global $redirect_url,$app_id,$app_secret;
	$code = 'QBf8NZkGokBqk_zJFhlqggV-bBqv1xL-FJMrGPm0yaE.eyJpdiI6IkN0ZkZBVTdDeE1fUU1iWXpMN0NlaFEifQ.zH5YTazRlWZiJEmoNvwmVNfIanjUzDXJ8U0wKOD_JPVg5i27XhOGI2wgErcjcfD1Dw5q5BEj60sV0jMOe8j90tBHgb08uXqDarisYSJepKhh7HDcvmlznzrf_cUpH_BJ';

	if (isset($_REQUEST["code"]))
	{
		$code=$_REQUEST["code"];
		fw ("new code = " . $code);
		return $code;
	}
	else
	{
		fw ("old code = " . $code);
		return $code;
	}

	// get Code
	// code = Bp82F2_-jiewKiK3Igewst42NyVhmpZ49ZKWOGOFrig.eyJpdiI6Im1ZcnJOSHp5cVBPaTRETGR6RXpWZFEifQ._P4JcJ7hLCYpxHi1e3R9QTLSiQIw9Wrkid_fmtxjW4Fa570efRxL9yRbGolwyv13fpz3fiI3q8NntLUOs4faZabxM8oQSe6pZniQ7Za9nlZ28HyGEuDvW2cy0NEqQepx
			$authorize_url = "https://graph.facebook.com/oauth/authorize"; 
			$parameters = "client_id=" . $app_id . '&redirect_uri=' . $redirect_url;
	
	
	//$result = file_get_contents($authorize_url . "?" . $parameters);
	//fw (".result = " . $result);
	//fw (".authorize_url = " . $authorize_url . "?" . $parameters); //access_code=https://graph.facebook.com/oauth/authorize?client_id=264530743602&redirect_uri=http://cartoonbank.ru/wp-content/plugins/fb-post-ales/

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