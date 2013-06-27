<?php
require_once('JSON.php');
define('CB_URL_QUERY', 'http://109.120.143.27/cb3/copyator/downloadinfo.php?');
define('CB_URL_404', 'http://cartoonbank.ru/404.php');
define('ERR_EML_FROM', 'admin@cartoonbank.ru');
$admin_emails = array(
					"creasysee@yandex.ru",
					"igor.aleshin@gmail.com"
					);

if( !function_exists('json_decode') ) {
    function json_decode($data) {
        $json = new Services_JSON();
        return( $json->decode($data) );
    }
}

function track_error($error_text)
{
	global $admin_emails;
	foreach($admin_emails as $mail)
	{
		$message = "Добрый день администратор.\n" .
				   "При скачивании изображения получена ошибка:\n" .
				   $error_text . " (URI='" . $_SERVER["REQUEST_URI"] . "')"; 
		$headers = "From: " . ERR_EML_FROM . "\n" . 
				   "X-Mailer: PHP/" . phpversion() . "\n" . 
				   "MIME-Version: 1.0\n" . 
				   "X-Accept-Language: ru\n";
				   "Content-Type: text/html; charset=\"utf-8\"\n" . 
				   "Content-Transfer-Encoding: 7bit\n\n"; 
		$subject = "Ошибка скачивания изображения";
		mail($mail, "=?utf-8?B?".base64_encode($subject)."?=", $message, null, '-f ' . ERR_EML_FROM);
	}
	header("Location: " . CB_URL_404);
}

function track_error_info($error_text)
{
	if (strstr($error_text, "Could not run query"))
	{
		track_error($error_text);
		exit;
	}
	if (strstr($error_text, "Download data not found"))
	{
		track_error($error_text);
		exit;
	}
	if (strstr($error_text, "Error parameters list"))
	{
		track_error($error_text);
		exit;
	}
	if (strstr($error_text, "Could not connect"))
	{
		track_error($error_text);
		exit;
	}
	if (strstr($error_text, "Could not select db"))
	{
		track_error($error_text);
		exit;
	}
	if (strstr($error_text, "Could not decrement downloads"))
	{
		track_error($error_text);
		exit;
	}
	if (strstr($error_text, "Could not run option value query"))
	{
		track_error($error_text);
		exit;
	}
	if (strstr($error_text, "Could not get option value 'max_downloads'"))
	{
		track_error($error_text);
		exit;
	}
}

if (isset($_GET['downid']) and is_numeric($_GET['downid']))
{
	$downid = $_GET['downid'];
	$request_param = "downid=$downid";

	if (isset($_GET['mail']) && ($_GET['mail'] == 1))
	{
		$request_param .= "&mail=1";
	}
}

if (isset($_GET['prodid']) and is_numeric($_GET['prodid']))
{
	$prodid = $_GET['prodid'];
	$request_param = "prodid=$prodid";
}

if (isset($_GET['sid']) and strlen($_GET['sid']) == 6)
{
	$sid = $_GET['sid'];
}
else
{
	echo $_GET['sid'];
	track_error("Error: sid is not valid.");
	exit;
}


if (!isset($request_param) or !isset($sid))
{
	track_error("Error: parameters invalid.");
	exit;
}

$decrement = true;
if (isset($_GET['mode']) and $_GET['mode'] = 'go')
{
	$decrement = false;
}

$preview = "";
if (isset($_GET['preview_track']))
{
	$preview = $_GET['preview_track'];
	$decrement = false;
}
$mode_go = "";
if (!$decrement)
{
	$mode_go = "&mode=go";
}

$ret = file_get_contents(CB_URL_QUERY . $request_param . $mode_go);

if (!$ret)
{
	track_error("Error: download info returns false. CB_URL_QUERY.request_parsm.mode_go: ".CB_URL_QUERY .$request_param . $mode_go);
	exit;
}

track_error_info($ret);


$retamerica = file_get_contents('http://s89900854.onlinehome.us/cartoonbankimages/download.php?j='.$ret);

//echo $retamerica;

$return_data = json_decode($ret);


header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Type: ' . $return_data->mimetype);
if ($preview != 'true')
{
	header('Content-Disposition: attachment; filename="' . $return_data->filename . '"');
}
else
{
	header('Content-Disposition: attachment; filename="' . $return_data->filename . '"');
}
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
ob_clean();
echo $retamerica;
flush();


$ret = file_get_contents(CB_URL_QUERY . $request_param . "&confirm=true");

?>