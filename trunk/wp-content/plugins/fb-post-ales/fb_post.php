<?php
$post_id = '0';
if (isset($_GET['id']) && is_numeric($_GET['id']))
	$post_id=$_GET['id'];


						// http://www.facebook.com/developers/apps.php?app_id=264530743602 -- application
						// http://www.facebook.com/apps/application.php?id=264530743602&sk=wall -- the Wall
// application settings
		global $redirect_url, $app_id, $app_secret;
		$redirect_url = 'http://cartoonbank.ru/wp-content/plugins/fb-post-ales/';
		$app_id = '264530743602'; //“YOUR_APP_ID”; Cartoonist.name app
		$app_secret = '05e24c30ac95a33d726f6d087c3c00f4'; //"YOUR_APP_SECRET";

if ($post_id!='0')
{
// 1
$theCode = get_code();

				//ee($theCode,"theCode");

// 2
$theAccessToken = get_token($theCode);

				//ee($theAccessToken, "theAccessToken2");

// 3
$result = make_post($post_id);
}

function make_post($post_id)
{
	global $theAccessToken, $app_id;
	global $cartoon_id, $cartoon_name, $cartoon_description, $cartoon_additional_description, $cartoon_image, $cartoon_kategoria, $cartoon_brand;

	get_cartoon($post_id);

$cartoon_link = 'http://cartoonbank.ru/?page_id=29&cartoonid='. $post_id;

	  $apprequest_url = "https://graph.facebook.com/feed";
	  $parameters = "?" . $theAccessToken  . "&message=" . urlencode($cartoon_brand . ". " . $cartoon_kategoria). "&name=" . urlencode(stripslashes($cartoon_name)) ."&description=" . urlencode(stripslashes($cartoon_description) . " [" . stripslashes($cartoon_additional_description)."]")."&link=". urlencode($cartoon_link) ."&id=" . $app_id . "&picture=http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/". $cartoon_image ."&method=post" . "&caption=Cartoonbank";
	  $myurl = $apprequest_url . $parameters;
echo $myurl;
	if ($cartoon_id!='')
	{
		$result = file_get_contents($myurl);
		echo "result = " . $result;
	}
	else
	{
		echo ('<br>no image to post');
	}
}

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

				//ee($URL,"access token url");
		
		curl_setopt($ch, CURLOPT_URL, $URL);
		
		//echo "The URL is ".$URL;
		//Execute the fetch
		$theAccessToken = curl_exec($ch);

				//ee($theAccessToken,"theAccessToken1");

		//Close the connection
		curl_close($ch);
		
		return $theAccessToken;
}

function ee($to_print,$comment = '')
{
	$response = "<div style='margin:2px;padding-left:6px;background-color:#FFCC66;border:1px solid #CC0066;'><pre><b>".$comment.":</b> ".print_r($to_print,true)."</pre><br>$to_print</div>";
	echo ($response); 
}

function get_cartoon($post_id)
{
	global $cartoon_id, $cartoon_name, $cartoon_description, $cartoon_additional_description, $cartoon_image, $cartoon_kategoria, $cartoon_brand, $post_id;
		
	$sql = "SELECT `wp_product_list`.id, `wp_product_list`.name, `wp_product_list`.description, `wp_product_list`.additional_description, `wp_product_list`.image, `wp_product_brands`.`name` as brand, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` AND `wp_product_list`.`id`='".$post_id."' LIMIT 1";


	include("config.php");

	$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
	mysql_set_charset('utf8',$link);

				//pokazh($sql);

	$result = mysql_query($sql);

	if (!$result) {die('Invalid query: ' . mysql_error());}

	$row=mysql_fetch_array($result);
	$cartoon_id = $row['id'];
	$cartoon_name = $row['name'];
	$cartoon_description = $row['description'];
	$cartoon_additional_description = $row['additional_description'];
	$cartoon_image = $row['image'];
	$cartoon_kategoria = $row['kategoria'];
	$cartoon_brand = $row['brand'];
	

				//pokazh ($cartoon_id." ".$cartoon_name." ".$cartoon_description." ".$cartoon_additional_description." ".$cartoon_image." ".$cartoon_brand." ".$cartoon_kategoria);

}
?>