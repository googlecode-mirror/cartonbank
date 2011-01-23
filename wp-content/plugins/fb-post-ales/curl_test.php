<?
//STEP 1: Obtain the CODE
/*
$APPID = 'xxx';

header("Location: https://graph.facebook.com/oauth/authorize?client_id" . urlencode($APPID) . "&redirect_uri=" . urlencode(http://www.yourdomain.com/) . "&scope=offline_access,publish_stream,manage_pages");
*/
?>

<?
//STEP 2: Hardcode the resulting code as $CODE (see bellow) and using the following script you can post as administrator to the page specified by $PAGE_ID:

require_once('facebook/facebook.php');

$APPID = '264530743602';
$SECRET = '05e24c30ac95a33d726f6d087c3c00f4';
$PAGE_ID = 'me';
$CODE = '66LRNT';

echo "init\n";
$result = file_get_contents('https://graph.facebook.com/oauth/access_token?' . http_build_query(array('client_id' => $APPID, 'client_secret' => $SECRET, 'code' => $CODE, 'redirect_uri' => 'http://www.yourdomain.com/')));

echo "2\n";

if (preg_match('/^access_token=(.*)/', $result, $matches) !== FALSE)
{
	echo "3\n";
	$access_token = $matches[1];

	Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = 0;
	Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = 0;
	echo "4\n";
	$facebook = new Facebook(array('appId' => $APPID, 'secret' => $SECRET));
	echo "5\n";
/*
Fatal error: Uncaught OAuthException: An active access token must be used to query information about the current user.
  thrown in /home/www/cb3/wp-content/plugins/fb-post-ales/facebook/facebook.php on line 543
*/

	$accounts = $facebook->api('/me/accounts', 'GET', array('access_token' => $access_token));
	echo "6\n";

	foreach ($accounts['data'] as $account)
	{
		if ($account['id'] == $PAGE_ID)
		{
			$access_token = $account['access_token'];
		}
	}
		echo "7\n";

	$facebook->api("/{$PAGE_ID}/feed", 'POST', array( 'message' => "It's cool man. Yeah, it's working.", 'access_token' => $access_token));
	echo "8\n";
}
?>
