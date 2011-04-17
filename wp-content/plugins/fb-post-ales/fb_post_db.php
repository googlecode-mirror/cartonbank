<?php

// http://www.facebook.com/developers/apps.php?app_id=264530743602 -- application
// http://www.facebook.com/apps/application.php?id=264530743602&sk=wall -- the Wall
// application settings
	$redirect_url = 'http://cartoonbank.ru/wp-content/plugins/fb-post-ales/';
	$app_id = '264530743602'; //“YOUR_APP_ID”; Cartoonist.name app
	$app_id_target = '264530743602'; // cartoonbank http://www.facebook.com/profile.php?id=100001929470986
	//$app_id_target = '100001929470986'; // cartoonbank http://www.facebook.com/profile.php?id=100001929470986
	$app_secret = '05e24c30ac95a33d726f6d087c3c00f4'; //"YOUR_APP_SECRET";
	$theMessage = 'taDA';

	global $cartoon_id, $cartoon_name, $cartoon_description, $cartoon_additional_description, $cartoon_image, $cartoon_kategoria, $cartoon_brand;

// 1
$theCode = get_code();

					//ee($theCode,"theCode");

// 2
$theAccessToken = get_token($theCode);

					//ee($theAccessToken, "theAccessToken2");

// 3
$post_id = '11735';
$result = make_post($post_id);


function fw($text)
{
	$fp = fopen('_kloplog.htm', 'w');
	fwrite($fp, '<br />');
	fwrite($fp, $text);
	fclose($fp);
}

function read_code()
{
	$filename = "/home/www/cb3/wp-content/plugins/fb-post-ales/code.txt";
	$fp = fopen($filename, 'r');
	$contents = fread($fp, filesize($filename));
	fclose($fp);
	return $contents;
}

?>

<?
function get_code()
{
	//http://sudocode.net/article/368/how-to-get-the-access-token-for-a-facebook-application-in-php/
	global $redirect_url,$app_id,$app_secret;
	$code = 'QBf8NZkGokBqk_zJFhlqggV-bBqv1xL-FJMrGPm0yaE.eyJpdiI6IkN0ZkZBVTdDeE1fUU1iWXpMN0NlaFEifQ.zH5YTazRlWZiJEmoNvwmVNfIanjUzDXJ8U0wKOD_JPVg5i27XhOGI2wgErcjcfD1Dw5q5BEj60sV0jMOe8j90tBHgb08uXqDarisYSJepKhh7HDcvmlznzrf_cUpH_BJ';

	// call FB authorize and get redirected to index.php which will right the code down to the code.txt
	$authorize_url = "https://graph.facebook.com/oauth/authorize"; 
	$parameters = "client_id=" . $app_id . '&redirect_uri=' . $redirect_url;
	$result = file_get_contents($authorize_url . "?" . $parameters);			
	
	//$result = file_get_contents($authorize_url . "?" . $parameters);
	//fw (".result = " . $result);
	//fw (".authorize_url = " . $authorize_url . "?" . $parameters); //access_code=https://graph.facebook.com/oauth/authorize?client_id=264530743602&redirect_uri=http://cartoonbank.ru/wp-content/plugins/fb-post-ales/
	
	$code = read_code();

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

function make_post($post_id)
{
	global $theAccessToken, $app_id, $app_id_target;
	global $cartoon_id, $cartoon_name, $cartoon_description, $cartoon_additional_description, $cartoon_image, $cartoon_kategoria, $cartoon_brand;

	//$apprequest_url = "https://graph.facebook.com/".$app_id_target."/feed";
	//$apprequest_url = "https://graph.facebook.com/feed";
	$apprequest_url = "https://graph.facebook.com/feed";
	
	get_cartoon($post_id);

	//$theMessage = 'Дубовский Александр';
	//$name = urlencode('Семья наркоманов');
	$caption = 'Cartoonbank';
	$description = $cartoon_kategoria . ": " . urlencode($cartoon_description) . " " . $cartoon_additional_description;
	$link = 'http://cartoonbank.ru/?page_id=29&cartoonid=' . $cartoon_id;
	$picture = 'http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/' . $cartoon_image;


	  //$parameters = "?" . $theAccessToken . "&message=" . $cartoon_brand . "&method=post" . "&picture=" . $picture . "&link=" . $link . "&name=" . $cartoon_name . "&description=" . $description . "&caption=" . $caption;
	  $parameters = "?" . $theAccessToken . "&message=777" . "&name=Name2";
	  $myurl = $apprequest_url . $parameters;
		ee ($myurl, "myurl");
	  $result = file_get_contents($myurl);
	  ee ($result, "result");
}

function ee($to_print,$comment = '')
{
	$response = "<div style='margin:2px;padding-left:6px;background-color:#FFCC66;border:1px solid #CC0066;'><pre><b>".$comment.":</b> ".print_r($to_print,true)."</pre><br>$to_print</div>";
	echo ($response); 
}
function get_cartoon($id)
{
	$sql = "SELECT `wp_product_list`.id, `wp_product_list`.name, `wp_product_list`.description, `wp_product_list`.additional_description, `wp_product_list`.image, `wp_product_brands`.`name` as brand, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` AND `wp_product_list`.`id`=".$id." LIMIT 1";

	global $cartoon_id, $cartoon_name, $cartoon_description, $cartoon_additional_description, $cartoon_image, $cartoon_kategoria, $cartoon_brand;

	include("config.php");

	$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
	mysql_set_charset('utf8',$link);

pokazh($sql);

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
	

pokazh ($cartoon_id." ".$cartoon_name." ".$cartoon_description." ".$cartoon_additional_description." ".$cartoon_image." ".$cartoon_brand." ".$cartoon_kategoria);

}

?>