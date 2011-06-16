<?php
//http://cartoonbank.ru/wp-content/plugins/fb-post-ales/fb_post_best.php
						// http://www.facebook.com/developers/apps.php?app_id=264530743602 -- application
						// http://www.facebook.com/apps/application.php?id=264530743602&sk=wall -- the Wall


// application settings
		global $redirect_url, $app_id, $app_secret, $cartoon_id;
		$redirect_url = 'http://cartoonbank.ru/wp-content/plugins/fb-post-ales/';
		$app_id = '264530743602'; //“YOUR_APP_ID”; Cartoonist.name app
		$app_secret = '05e24c30ac95a33d726f6d087c3c00f4'; //"YOUR_APP_SECRET";

// 1
// get Authentication Code
// Hardcoded!
$theCode = get_code();

				//ee($theCode,"theCode");

// 2
// get AccessToken
$theAccessToken = get_token($theCode);

				//ee($theAccessToken, "theAccessToken2");

// 3
// post to cartoonist.name
//$result = make_post();

// 4
// post to cartoonbank.ru
$result = make_post_cartoonbank();


function make_post_cartoonbank()
{
	// http://www.facebook.com/cartoonbank.of.russia

	global $theAccessToken, $app_id;
	global $cartoon_id, $cartoon_name, $cartoon_description, $cartoon_additional_description, $cartoon_image, $cartoon_kategoria, $cartoon_brand;

	get_cartoon();

	$cartoon_link = 'http://cartoonbank.ru/?page_id=29&cartoonid='. $cartoon_id;

	  $apprequest_url = "https://graph.facebook.com/cartoonbank.of.russia/feed";
	  $parameters = "?" . $theAccessToken  . "&message=" . urlencode($cartoon_brand . ". " . $cartoon_kategoria). "&name=" . urlencode(stripslashes($cartoon_name)) ."&description=" . urlencode(stripslashes($cartoon_description) . " [" . stripslashes($cartoon_additional_description)."]")."&link=". urlencode($cartoon_link) . "&picture=http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/". $cartoon_image ."&method=post" . "&caption=".urlencode("The best of Cartoonbank.ru");
	  $myurl = $apprequest_url . $parameters;
		
			//echo $myurl;

	if ($cartoon_id!='')
	{
		$result = file_get_contents($myurl);
		echo "result = " . $result;
		if (count($result)==1)
			update_facebook_date();
	}
	else
	{
		echo ('<br>no image to post');
	}
}



function make_post()
{
	global $theAccessToken, $app_id;
	global $cartoon_id, $cartoon_name, $cartoon_description, $cartoon_additional_description, $cartoon_image, $cartoon_kategoria, $cartoon_brand;

	get_cartoon();

	$cartoon_link = 'http://cartoonbank.ru/?page_id=29&cartoonid='. $cartoon_id;

	  $apprequest_url = "https://graph.facebook.com/feed";
	  $parameters = "?" . $theAccessToken  . "&message=" . urlencode($cartoon_brand . ". " . $cartoon_kategoria). "&name=" . urlencode(stripslashes($cartoon_name)) ."&description=" . urlencode(stripslashes($cartoon_description) . " [" . stripslashes($cartoon_additional_description)."]")."&link=". urlencode($cartoon_link) ."&id=" . $app_id . "&picture=http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/". $cartoon_image ."&method=post" . "&caption=".urlencode("The best of Cartoonbank.ru");
	  $myurl = $apprequest_url . $parameters;
		
			//echo $myurl;

	if ($cartoon_id!='')
	{
		$result = file_get_contents($myurl);
		echo "result = " . $result;
		if (count($result)==1)
			update_facebook_date();
	}
	else
	{
		echo ('<br>no image to post');
	}
}

function update_facebook_date()
{
	global $cartoon_id;
	// Mark image as sent to the Anekdot.ru
	$update_sql = "update wp_fsr_post set facebook_date='".date("d.m.y H:m:s")."' where ID=".$cartoon_id;
		$res = mysql_query($update_sql);
		if (!$res) {die('<br />'.$update_sql.'<br />Invalid delete query: ' . mysql_error());}
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

		//can't authorize via server side request???
		// have to hardcode the Code value...

		//$authorize_url = "https://graph.facebook.com/oauth/authorize"; 
		//$parameters = "client_id=" . $app_id . '&redirect_uri=' . $redirect_url . '&type=client_cred';
		
		//http://cartoonbank.ru/wp-content/plugins/fb-post-ales/?code=SK7iwrSgPU-fYmXYLft9fBaevkadoEm0rtCsrGJCKYA.eyJpdiI6ImhSbzN1VW9QcjZWVVkyeExWc0hxMWcifQ.iGEzIiW6GhJXnxLfqaheqxroP3TOcQtHBKO7hjqi367gujkBZQ1DA9d8rOci4k7EH5qT6HBGhDp3zkhE0I6JFkrxykH3sLryrgR9-oyvK3irCYy_AmTfbmL3LuroQniA
		//QBf8NZkGokBqk_zJFhlqggV-bBqv1xL-FJMrGPm0yaE.eyJpdiI6IkN0ZkZBVTdDeE1fUU1iWXpMN0NlaFEifQ.zH5YTazRlWZiJEmoNvwmVNfIanjUzDXJ8U0wKOD_JPVg5i27XhOGI2wgErcjcfD1Dw5q5BEj60sV0jMOe8j90tBHgb08uXqDarisYSJepKhh7HDcvmlznzrf_cUpH_BJ

		//$result = file_get_contents($authorize_url . "?" . $parameters);

				//ee ($result);
		
		/*
		// get contents of a file into a string
		$filename = "/home/www/cb3/wp-content/plugins/fb-post-ales/code.txt";
		$handle = fopen($filename, "r");
		$code = fread($handle, filesize($filename));

		if(!filesize($file)>0) 
		{
			ee ("1");
			$code = 'SK7iwrSgPU-fYmXYLft9fBaevkadoEm0rtCsrGJCKYA.eyJpdiI6ImhSbzN1VW9QcjZWVVkyeExWc0hxMWcifQ.iGEzIiW6GhJXnxLfqaheqxroP3TOcQtHBKO7hjqi367gujkBZQ1DA9d8rOci4k7EH5qT6HBGhDp3zkhE0I6JFkrxykH3sLryrgR9-oyvK3irCYy_AmTfbmL3LuroQniA';
		}
		else 
		{
			ee ("2");
			$code = fread($handle, filesize($filename));
		}
		fclose($handle);
		*/
	$code = 'SK7iwrSgPU-fYmXYLft9fBaevkadoEm0rtCsrGJCKYA.eyJpdiI6ImhSbzN1VW9QcjZWVVkyeExWc0hxMWcifQ.iGEzIiW6GhJXnxLfqaheqxroP3TOcQtHBKO7hjqi367gujkBZQ1DA9d8rOci4k7EH5qT6HBGhDp3zkhE0I6JFkrxykH3sLryrgR9-oyvK3irCYy_AmTfbmL3LuroQniA';
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

function ee($to_print,$comment = '')
{
	$response = "<div style='margin:2px;padding-left:6px;background-color:#FFCC66;border:1px solid #CC0066;'><pre><b>".$comment.":</b> ".print_r($to_print,true)."</pre><br>$to_print</div>";
	echo ($response); 
}

function get_cartoon()
{
	global $cartoon_id, $cartoon_name, $cartoon_description, $cartoon_additional_description, $cartoon_image, $cartoon_kategoria, $cartoon_brand, $post_id;
		
		/*
		$sql = "SELECT  `wp_product_list`.id,  `wp_product_list`.name,  `wp_product_list`.description,  `wp_product_list`.additional_description, `wp_product_list`.image, `wp_product_brands`.`name` AS brand, `wp_product_categories`.`name` AS kategoria, COUNT( * ) AS votes, SUM( wp_fsr_user.points ) AS points, AVG( wp_fsr_user.points ) * SQRT( COUNT( * ) ) AS average
		FROM wp_fsr_user, wp_fsr_post, wp_product_list, wp_product_brands, wp_item_category_associations, wp_product_categories
		WHERE wp_fsr_user.post = wp_product_list.id
		AND wp_fsr_user.post = wp_fsr_post.ID
		AND wp_product_list.`id` =  `wp_item_category_associations`.`product_id` 
		AND wp_product_list.brand = wp_product_brands.id
		AND wp_item_category_associations.`category_id` =  `wp_product_categories`.`id` 
		AND wp_product_list.active =1
		AND wp_product_list.visible =1
		AND wp_fsr_post.facebook_date IS NULL 
		GROUP BY 1 
		ORDER BY 10 DESC , 8 DESC 
		LIMIT 1";
		*/

		$sql = "SELECT  
		`wp_product_list`.id,  
		`wp_product_list`.name,  
		`wp_product_list`.description,  
		`wp_product_list`.additional_description, 
		`wp_product_list`.image, 
		`wp_product_brands`.`name` AS brand, 
		`wp_product_categories`.`name` AS kategoria, 
		`wp_product_list`.votes AS votes, 
		`wp_product_list`.votes_sum AS points, 
		`wp_product_list`.votes_sum / `wp_product_list`.votes AS average,
		(`wp_product_list`.votes_sum / `wp_product_list`.votes) * SQRT(`wp_product_list`.votes) AS rate
		FROM wp_product_list, wp_product_brands, wp_item_category_associations, wp_product_categories, wp_fsr_user, wp_fsr_post 
		WHERE wp_product_list.active = 1
		AND wp_product_list.visible = 1
		AND wp_fsr_post.facebook_date IS NULL 
		AND wp_fsr_user.post = wp_product_list.id
		AND wp_fsr_user.post = wp_fsr_post.ID
		AND wp_product_list.`id` =  `wp_item_category_associations`.`product_id` 
		AND wp_product_list.brand = wp_product_brands.id
		AND wp_item_category_associations.`category_id` = `wp_product_categories`.`id` 
		GROUP BY 1 
		ORDER BY 11 DESC , 8 DESC 
		LIMIT 1";


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