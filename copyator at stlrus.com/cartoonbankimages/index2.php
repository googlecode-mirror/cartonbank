<?php
//phpinfo();
//PHP Version 5.4.16
ini_set('display_errors', 1);
ini_set('default_socket_timeout', 240);
error_reporting(E_ALL);

//mail('igor.aleshin@gmail.com', 'ашипка', 'testttttt');
$url = 'http://109.120.143.27/cb3/copyator/downloadinfo.php?downid=4161&';



//1
$ret = file_get_contents($url);

//2 
//$ret = file_get_contents_curl($url);


//3
// Create a stream
$options = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Accept-language: en\r\n" .
			  "Connection: Keep-Alive\r\n" .
              "Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
              "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad 
  )
);

$context = stream_context_create($options);
//$file = file_get_contents($url, false, $context);


//echo $file;


echo $ret;

echo "<br>exit</br>";







function file_get_contents_curl($url) {

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Устанавливаем параметр, чтобы curl возвращал данные, вместо того, чтобы выводить их в браузер.
	curl_setopt($ch, CURLOPT_URL, $url);

	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}



?>