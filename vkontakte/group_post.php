<?php

define("VK_APP_ID", '2925633');
define("VK_ALBUM_ID", '143024983');
define("VK_GROUP_ID", '30341883');
define("USER_AGENT", 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.83 Safari/535.11');
define("PATH_TO_UPLOAD", '/home/www/cb3/wp-content/plugins/wp-shopping-cart/product_images');
define("VK_USER_NAME", 'igor.aleshin@gmail.com');
define("VK_USER_PASS", 'basie5670659');
define("PATH_TO_TEMP", '/home/www/cb3/vkontakte/temp');
define("VK_REFERER", 'http://cartoonbank.ru');
require "simple_html_dom.php";

$ckfile = 0;

function get_url($html, $token1, $token2)
{
	// parse text data from $token1 to $token2
	$i = strpos($html, $token1);
	if ($i === FALSE) { echo "<br />Error: Authorization problem: not found key '$token1'"; die; }
	$j = strpos($html, $token2, $i);
	if ($j === FALSE) { echo "<br />Error: Authorization problem: not found key '$token2'"; die; }
	$res = substr($html, $i + strlen($token1), $j - $i - strlen($token1));
	return str_replace(array("\r", "\r\n", "\n"), "", $res);
}

function curl($url, &$headers, $post = false, $vars = NULL, $first = false, $header = false)
{
	global $ckfile;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	if ($post)
	{
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
		if ($header)
			curl_setopt($ch, CURLOPT_HEADER, 1);
	}
    if ($first)
    {
		curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
		curl_setopt($ch, CURLOPT_COOKIESESSION,true);
	}
	else
		curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
	curl_setopt($ch, CURLOPT_TIMEOUT, 40);
	if (!$post)
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	$kuku = curl_exec ($ch);
	if ($kuku === false)
	{
		echo "<br />";
		echo "Curl Error (url = " . $url . ") " . curl_error($ch);
		die("<br />");
	}
	$headers = curl_getinfo($ch);
	curl_close ($ch);
	return $kuku;
}

function auth()
{
	global $ckfile;
	$token = '';
	$url_auth = "http://oauth.vk.com/authorize?client_id=" . VK_APP_ID .
				// Request right to photos and wall
				"&scope=" . urlencode("wall,photos") .
				"&redirect_uri=http://api.vk.com/blank.html&display=touch&response_type=token";

    // There we getting login & password form
	$html = file_get_html($url_auth);
	if ($html === false) {  echo "Error: 'file_get_html' returns false from '$url_auth'"; die; }
	$vars = '';

    // Fill login & password fields
	foreach($html->find('input') as $element)
	{
		$val = $element->value;
		if ($element->name == 'email')
			$val = VK_USER_NAME;
		if ($element->name == 'pass')
			$val = VK_USER_PASS;
		$vars = $vars . $element->name . '=' . $val . '&';
	}

    // Submit form
    $headers = NULL;
    $kuku = curl('https://login.vk.com/?act=login&soft=1', $headers, true, $vars, true, true);
    /*
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,'https://login.vk.com/?act=login&soft=1');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_COOKIESESSION,true);
	curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
	curl_setopt($ch, CURLOPT_TIMEOUT, 40);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$kuku = curl_exec ($ch);
	curl_close ($ch);
    */
	// Search response for redirect url
	$res = get_url($kuku, 'Location: ', "\n");

	// redirecting
	$kuku = curl($res, $headers);

	if (strpos($kuku, "Login success") === false)
	{
		// Search request for grant_access url
		$res = get_url($kuku, 'location.href = "', '";');

		// redirecting
		$kuku = curl($res, $headers);
	}
	$token = get_url($headers['url'], 'access_token=', '&expires_in');
	return $token;
}

function upload($row)
{
	global $token;
	global $ckfile;

	// query upload server
	$sRequest = 'https://api.vk.com/method/photos.getUploadServer?aid=' . VK_ALBUM_ID .
				'&gid=' . VK_GROUP_ID .
				'&access_token=' . $token;

	$headers = NULL;
	$result = curl($sRequest, $headers);
	$returned_json = strstr($result, '{');
	$data = json_decode($returned_json);
	$upload_url = $data->response->upload_url;

	// upload file to server
	$post = array(
		'file1'=> '@' . PATH_TO_UPLOAD . '/' . $row["image"],
		'file2'=>'',
		'file3'=>'',
		'file4'=>'',
		'file5'=>'',
	);
	$result = curl($upload_url, $headers, true, $post, false, false);
	$data = json_decode($result);
	$sRequest = 'https://api.vk.com/method/photos.save?aid=' . VK_ALBUM_ID .
				'&server=' . $data->server .
				'&photos_list=' . $data->photos_list .
				'&hash=' . $data->hash .
				'&gid=' . VK_GROUP_ID .
				'&owner=-' . VK_GROUP_ID .
				'&caption=t' .
				'&access_token=' . $token;

	$result = curl($sRequest, $headers);
	$returned_json = strstr($result, '{');
	$data = json_decode($returned_json);
	$pid = $data->response[0]->pid;
	$photoid = $data->response[0]->id;

	// edit image description
	$sRequest = 'https://api.vk.com/method/photos.edit?owner_id=-' . VK_GROUP_ID .
				'&pid=' . $pid .
				'&caption=' . urlencode('[club' . VK_GROUP_ID . '|Картунбанк]') . '%0A%0A' .
							  urlencode(ucfirst($row["title"])) . '%20' . urlencode('http://cartoonbank.ru/cartoon/' . $row["id"] . '/') . '%0A%0A' .
							  urlencode(ucfirst($row["descr"])) . '%0A%0A' .
							  urlencode(ucfirst($row["author"])) . '%20' . urlencode('http://cartoonbank.ru/brand/' . $row["brand"] . '/') . '%0A%0A' .
				'&from_group=1' .
				'&access_token=' . $token;
	$result = curl($sRequest, $headers);

	// upload image to wall
	$sRequest = 'https://api.vk.com/method/wall.post?owner_id=-' . VK_GROUP_ID .
				'&gid=' . VK_GROUP_ID .
				'&message=' . urlencode(ucfirst($row["author"])) . "%0A" . urlencode(ucfirst($row["title"])) .
				'&attachments=' . 'photo-' . VK_GROUP_ID . "_" . $pid .',http://cartoonbank.ru/cartoon/' . $row["id"] . '/' .
				'&from_group=1' .
				'&access_token=' . $token;

	$result = curl($sRequest, $headers);
	print_r($result);
	return true;
}

?>
