<?php

$domain = "cartoonbank.ru/ales/post2vkontakte"; //основной домен
$api_id = 2289864; //айди приложения(сайта) // application Cartoonbank
$user_id = 4077578; // Igor Aleshin
$app_secret = "pRIAAZWfP6q6hqpq2rYd"; //секретный ключ приложения
$permissions = "offline,wall"; //какие права нужны


// ID приложения:	2291953
// Защищенный ключ:	SVCpbOeZ1in7PawkEKO8
$api_id = 2291953; //айди приложения(сайта) // application Cartoonbank
$user_id = 4077578; // Igor Aleshin
$app_secret = "SVCpbOeZ1in7PawkEKO8"; //секретный ключ приложения


//приложение 567 http://vkontakte.ru/editapp?id=2292174
$app_secret =  "BcqBcdNTY4bU6pW4KaH2";
$api_id = 2292174;


fw("");


if($_REQUEST['act'] == 'callback') {
	fw("mark1:");
	if($_REQUEST['error']) {
		fw("Error: ".$_REQUEST['error']."; Description: ".$_REQUEST['error_description']);
		die("Error: ".$_REQUEST['error']."; Description: ".$_REQUEST['error_description']);
	} else if($_REQUEST['code']) {
		$result = get("https://api.vkontakte.ru/oauth/access_token?client_id={$api_id}&client_secret={$app_secret}&code={$_REQUEST['code']}");
		fw("result: ".$result);
		$json_result = json_decode($result, true);
		if($json_result["error"]) {
			fw("Error: ". $json_result["error"] . "; Description: ". $json_result["error_description"] );
			die("Error: ". $json_result["error"] . "; Description: ". $json_result["error_description"] );
		} else if ($json_result["access_token"]) {

			fw("mark2:");

			//тут можно делать запрос к апи
			$theAccessToken = $json_result["access_token"];
			post_wall($theAccessToken);

		} else {
			fw('error 77');
			die('error 77');
		}
	}
} else {
$auth_url = "https://api.vkontakte.com/oauth/authorize?client_id={$api_id}&redirect_uri=http://{$domain}/vk.php?act=callback&display=page&response_type=code&scope={$permissions}";

//http://vkontakte.com/developers.php?o=-17680044&p=OAuth%20Authorization%20Dialog
/*
* Если Вы разрабатываете Standalone-приложение и параметр response_type = "token", то в качестве параметра redirect_uri необходимо указывать адрес http://api.vkontakte.com/blank.html, на который будут переданы данные авторизации. Обратите внимание, что только в данном случае у Вас будет возможность использовать расширенные методы работы с API.

$auth_url = "https://api.vkontakte.com/oauth/authorize?client_id={$api_id}&redirect_uri=http://api.vkontakte.com/blank.html?act=callback&display=page&response_type=code&scope={$permissions}";
*/

fw("auth_url: ".$auth_url);
header('Location: '.$auth_url);

		$result = get($auth_url);
		fw("auth_url result: ".$result);
		$json_result = json_decode($result, true);
		fw("json_result: ".$json_result);
}

/*
* FUNCTIONS
*/

function get($url) {
$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_POST, 1);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
curl_close($ch);
return $result;
}

function post_wall($theAccessToken)
{
	$user_id = 4077578; // Igor Aleshin
	$message = 'проверка'; 
	$apprequest_url = "https://api.vkontakte.ru/method/wall.post";
	$parameters = "?access_token=" . $theAccessToken  . "&message=" . $message . "&owner_id=".$user_id; 

	$myurl = $apprequest_url . $parameters;

	fw("myurl: ".$myurl);

	$result = file_get_contents($myurl);
	fw("post result: ".$result);
	//post result: {"error":{"error_code":20,"error_msg":"Permission to perform this action is denied for non-standalone applications","request_params":[{"key":"oauth","value":"1"},{"key":"method","value":"wall.post"},{"key":"access_token","value":"060691490638a9430655d1cce8061a598bf063806398943cafbe38a192525e7"},{"key":"message","value":"проверка"},{"key":"owner_id","value":"4077578"}]}}
}
function fw($text)
{
	$fp = fopen('_kloplog.htm', 'a');
	fwrite($fp, '<br />');
	fwrite($fp, $text);
	fclose($fp);
}

