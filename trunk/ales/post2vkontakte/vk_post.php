<?php
// http://habrahabr.ru/blogs/social_networks/117211/
// application settings
		global $redirect_url, $app_id, $app_secret, $user_id;
		$redirect_url = 'http://cartoonbank.ru/ales/post2vkontakte/';
		$app_secret = 'pRIAAZWfP6q6hqpq2rYd';
		$post_id = '11721';

			// http://vkontakte.ru/editapp?id=2289864
			// защищённый ключ pRIAAZWfP6q6hqpq2rYd
			// id приложения 2289864  

		$user_id = '4077578'; // Igor Aleshin
		$app_id = '2289864'; // application Cartoonbank
		
// 0 Login


//login_test();
//$post_result = send_form();

// 1 Get Code

$theCode = get_code();

				ee($theCode,"theCode");
// 2 Get Token
$theAccessToken = get_token($theCode);

			ee($theAccessToken, "theAccessToken8");

//$theAccessToken = json_decode($theAccessToken['access_token']);

$decode = json_decode($theAccessToken);
			ee($decode->access_token, "theAccessToken9");
			ee($decode->error, "theAccessToken10");

if ($decode->access_token!='')
{
// 3 Make Post
	$theAccessToken = $decode->access_token;
	$result = make_post($post_id);
}
exit;

function make_post($post_id)
{
	global $theAccessToken, $app_id, $user_id;
	global $cartoon_id, $cartoon_name, $cartoon_description, $cartoon_additional_description, $cartoon_image, $cartoon_kategoria, $cartoon_brand;
	/*
		//$get_info_url = 'http://api.vkontakte.ru/method/getProfiles?uids='.$user_id.'&fields=photo';
		$get_info_url = 'http://api.vkontakte.ru/method/getProfiles?uids='.$user_id.'&fields=uid, first_name, last_name, nickname, domain, sex, bdate, city, country, timezone, photo, photo_medium, photo_big, has_mobile, rate, contacts, education, online';
		//{"response":[{"uid":4077578,"first_name":"Игорь","last_name":"Алешин","nickname":"","domain":"id4077578","sex":"2","city":"2","country":"1","timezone":3,"photo":"http:\/\/cs228.vkontakte.ru\/u4077578\/e_cabe8ef2.jpg","photo_medium":"http:\/\/cs228.vkontakte.ru\/u4077578\/b_4180cb70.jpg","photo_big":"http:\/\/cs228.vkontakte.ru\/u4077578\/a_04bf856e.jpg","has_mobile":0,"rate":"53","university":"60","university_name":"СПбГУАП","faculty":"257","faculty_name":"Радиотехники, электроники и связи\r\n","graduation":"1985","online":1}]}

		$get_info_url = 'http://api.vkontakte.ru/method/wall.get?owner_id='.$user_id;
		*/

	$message = 'проверка'; 


	  $apprequest_url = "https://api.vkontakte.ru/method/wall.post";
	  $parameters = "?access_token=" . $theAccessToken  . "&message=" . $message . "&owner_id=".$user_id; 

	  //$parameters = "?" . $theAccessToken  . "&message=" . urlencode($cartoon_brand . ". " . $cartoon_kategoria). "&name=" . urlencode(stripslashes($cartoon_name)) ."&description=" . urlencode(stripslashes($cartoon_description) . " [" . stripslashes($cartoon_additional_description)."]")."&link=". urlencode($cartoon_link) . "&picture=http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/". $cartoon_image ."&method=post" . "&caption=".urlencode("The best of Cartoonbank.ru")."owner_id=".$user_id;

	  $myurl = $apprequest_url . $parameters;

		echo ($myurl);

	//$params = "&message=".urlencode($message);
	//$post_to_wall = 'http://api.vkontakte.ru/method/wall.post?owner_id='.$user_id.$params;


	$result = file_get_contents($myurl);
	ee($result);
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
	global $redirect_url,$app_id,$app_secret, $user_id;

	ee(read_code(),"old code ");

	$URL = 'http://api.vkontakte.ru/oauth/authorize?client_id='.$app_id.'&scope=offline&redirect_uri='.$redirect_url.'&response_type=code&display=page';

	echo ($URL);

	$result = file_get_contents($URL);
	
	//ee($result,"result");

	//$result2 = file_get_contents($result);

	//ee($result2,"result2");

	sleep(2);

	//ee($result,"result");
	
	$code = read_code();


	//ee(read_code(),"new code ");
	//$code = 'd2bf9be44d707f6277';
	return $code;
}

function read_code()
{
		$filename = "code.txt";
		$handle = fopen($filename, "r");
		$code = fread($handle, filesize($filename));
		fclose($handle);
	return $code;
}

function get_token($code)
{
	// get Access Token
		global $redirect_url, $app_id, $app_secret;
		$access_token_url = "https://api.vkontakte.ru/oauth/access_token"; 
		/*
		https://api.vkontakte.ru/oauth/access_token? 
			client_id=APP_ID& 
			client_secret=APP_SECRET& 
			code=7a6fa4dff77a228eeda56603b8f53806c883f011c40b72630bb50df056f6479e52a
		*/
		$parameters = "grant_type=client_credentials&client_id=" . $app_id . "&client_secret=" . $app_secret;

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

		$URL = 'https://api.vkontakte.ru/oauth/access_token?' . http_build_query(array(
			'client_id'     => $app_id,
			'code'          => $code,
			'client_secret' => $app_secret));
		
		curl_setopt($ch, CURLOPT_URL, $URL);
		
		echo "<br>The URL is:<br>".$URL;


		//Execute the fetch
		$theAccessToken = curl_exec($ch);

				//ee($theAccessToken,"theAccessToken1");

		//Close the connection
		curl_close($ch);

		//theAccessToken1: {"access_token":"788091613512efbf78d3d1e4e2789c59a3c78be78be896bf9c94fe0686e9e4e","expires_in":3581,"user_id":4077578}
		$theAccessToken = $theAccessToken;

		//ee($theAccessToken); 

		return $theAccessToken;
}

function ee($to_print,$comment = '')
{
	$response = "<div style='margin:2px;padding-left:6px;background-color:#FFCC66;border:1px solid #CC0066;'><pre><b>".$comment.":</b> ".print_r($to_print,true)."</pre></div>";
	//<br>$to_print
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


function send_form()
{
/*
	<form method="POST" id="login_submit" action="http://login.vk.com/?act=login&soft=1">
	<input type="hidden" name="q" value="1">
	<input type="hidden" name="from_host" value="api.vkontakte.ru">
	<input type="hidden" name="to" value="aHR0cDovL2FwaS52a29udGFrdGUucnUvb2F1dGgvYXV0aG9yaXplP2NsaWVudF9pZD0yMjg5ODY0JnJlZGlyZWN0X3VyaT1odHRwJTNBJTJGJTJGY2FydG9vbmJhbmsucnUlMkZhbGVzJTJGcG9zdDJ2a29udGFrdGUlMkYmcmVzcG9uc2VfdHlwZT1jb2RlJnNjb3BlPTgxOTImc3RhdGU9JmRpc3BsYXk9cGFnZQ--">

	<input type="hidden" id="expire" name="expire" value="0">

	<td><input type="text" name="email" value=""></td>
	<td><input type="password" name="pass"></td>

*/
	$post = array(
		"q"=>"1",
		"from_host"=>"api.vkontakte.ru",
		"to"=>"aHR0cDovL2FwaS52a29udGFrdGUucnUvb2F1dGgvYXV0aG9yaXplP2NsaWVudF9pZD0yMjg5ODY0JnJlZGlyZWN0X3VyaT1odHRwJTNBJTJGJTJGY2FydG9vbmJhbmsucnUlMkZhbGVzJTJGcG9zdDJ2a29udGFrdGUlMkYmcmVzcG9uc2VfdHlwZT1jb2RlJnNjb3BlPTgxOTImc3RhdGU9JmRpc3BsYXk9cGFnZQ--",
		"expire"=>"0",
		"act"=>"login",
		"email"=>"igor.aleshin%40gmail.com",
		"pass"=>"basie5670659",
		);

	$ch = curl_init('http://login.vk.com/?act=login&soft=1'); //$ch = curl_init('http://login.vk.com/'); 

	curl_setopt($ch, CURLOPT_POST, 1); 
	curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.2.10) Gecko/20100914 MRA 5.7 (build 03686) Firefox/3.6.10 sputnik 2.3.0.86 "); 
	$headers = array 
	( 
	  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 
	  'Accept-Language: ru-ru,ru;q=0.8,en-us;q=0.5,en;q=0.3', 
	  'Accept-Encoding: gzip,deflate', 
	  'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7' 
	);  
	curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);  
	curl_setopt($ch, CURLOPT_REFERER, "http://vkontakte.ru/"); 

	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$postResult =  curl_exec($ch);
	curl_close($ch);
	print "$postResult";
return true;
}


function login_test()
{
	//http://www.softtime.ru/forum/read.php?id_forum=1&id_theme=75508&page=1
	$maile="igor.aleshin@gmail.com"; // пишем свой E-mail 
	$pass="basie5670659"; // пишем свой Пароль 

	$ua = "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.14) Gecko/2009082707 Firefox/3.0.14"; 
	$ch = curl_init("http://vkontakte.ru/index.php"); 
	curl_setopt($ch, CURLOPT_USERAGENT, $ua); 
	curl_setopt ($ch, CURLOPT_HEADER, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$output = curl_exec ($ch); 
	curl_close($ch); 

	$ch = curl_init("http://login.vk.com/?vk="); 
	curl_setopt($ch, CURLOPT_USERAGENT, $ua); 
	curl_setopt($ch, CURLOPT_REFERER, "http://vkontakte.ru/index.php"); 
	curl_setopt ($ch, CURLOPT_HEADER, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$output = curl_exec ($ch); 
	curl_close($ch); 

	$ch = curl_init("http://vkontakte.ru/login.php?op=slogin&nonenone=1"); 
	curl_setopt($ch, CURLOPT_USERAGENT, $ua); 
	curl_setopt($ch, CURLOPT_REFERER, "http://login.vk.com/?vk="); 
	curl_setopt ($ch, CURLOPT_HEADER, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_COOKIE, "remixchk=5"); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, "s=nonenone"); 
	$output = curl_exec ($ch); 
	curl_close($ch); 

	$ch = curl_init("http://vkontakte.ru/login.php"); 
	curl_setopt($ch, CURLOPT_USERAGENT, $ua); 
	curl_setopt($ch, CURLOPT_REFERER, "http://vkontakte.ru/index.php"); 
	curl_setopt ($ch, CURLOPT_HEADER, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_COOKIE, "remixchk=5; remixsid=nonenone"); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, "op=a_login_attempt"); 
	$output = curl_exec ($ch); 
	curl_close($ch); 

	$ch = curl_init("http://login.vk.com/?act=login"); 
	curl_setopt($ch, CURLOPT_USERAGENT, $ua); 
	curl_setopt($ch, CURLOPT_REFERER, "http://vkontakte.ru/index.php"); 
	curl_setopt ($ch, CURLOPT_HEADER, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, "email=".$maile."&pass=".$pass."&expire=&vk="); 
	$output = curl_exec ($ch); 
	curl_close($ch); 

	preg_match_all ('|Set-Cookie: l=([^;]*); expires=([^;]*); path=/; domain=login.vk.com 
	Set-Cookie: p=([^;]*); expires=([^;]*); path=/;|isU',$output,$content_com); 

	preg_match_all ("|input type='hidden' name='s' value='([^']*)'|isU",$output,$content_com_1); 

	$ch = curl_init("http://vkontakte.ru/login.php"); 
	curl_setopt($ch, CURLOPT_USERAGENT, $ua); 
	curl_setopt($ch, CURLOPT_REFERER, "http://login.vk.com/?act=login"); 
	curl_setopt ($ch, CURLOPT_HEADER, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_COOKIE, "remixchk=5; remixsid=nonenone"); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, "s=".$content_com_1[1][0]."&op=slogin&redirect=1&expire=0&to="); 
	$output = curl_exec ($ch); 
	curl_close($ch); 

	preg_match_all ('|Location: /id(\d*)|is',$output,$content_com_2); 
/*
	$ch = curl_init("http://vkontakte.ru/id".$content_com_2[1][0]); 
	curl_setopt($ch, CURLOPT_USERAGENT, $ua); 
	curl_setopt($ch, CURLOPT_REFERER, "http://login.vk.com/?act=login"); 
	curl_setopt ($ch, CURLOPT_HEADER, 1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_COOKIE, "remixchk=5; remixsid=".$content_com_1[1][0]); 
	$output = curl_exec ($ch); 

	curl_close($ch); 
*/
	print_r('<pre>'); 
	print_r($output); 
}

?>