<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
include_once 'yandex_translate.php';
include_once 'big_text_translate.php';
$mysql_hostname = "localhost";
$mysql_user = "z58365_cbru3";
$mysql_password = "greenbat";
$mysql_database = "cartoonbanken";

  // translate cartoon by cartoonID
  //$cartoonID = 9561;
  //getCartoonTranslated($cartoonID);
  
  //echo "done";
  //exit;
  



// cartoon ids to translate
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_select_db($mysql_database, $link) or die("Could not select database");
mysql_set_charset('utf8',$link);

$sql = "SELECT id FROM  `wp_product_list`  WHERE translated = 0 and id < 16600 order by id desc limit 300";
$result = mysql_query($sql);
if ($result){
    while($r = mysql_fetch_array($result)) {
		getCartoonTranslated($r['id']);
    };
}
else{die("wrong result");}
exit;

function getCartoonTranslated($cartoonID){
	// create a record
	$textArray = getTextToTranslate($cartoonID);
	$translator = new Yandex_Translate();  
	$textArray['name'] = mysql_real_escape_string($translator->yandexTranslate('ru', 'en', $textArray['name']));  
	$textArray['desc'] = mysql_real_escape_string($translator->yandexTranslate('ru', 'en', $textArray['desc']));  
	$textArray['tags'] = mysql_real_escape_string($translator->yandexTranslate('ru', 'en', $textArray['tags']));  

//pokazh($out);
	// save translated
	//saveTranslated($out);
	saveTranslatedToDB($cartoonID,$textArray);
	//markCartoonAsTranslated($cartoonID);
}


function saveTranslatedToDB($cartoonID,$textArray){
	//echo "1<br>";
	$IsTranslated = FALSE;
	
	$IsTranslated = strpos($textArray['name'],"а");
	$IsTranslated = strpos($textArray['name'],"о");
	$IsTranslated = strpos($textArray['desc'],"а");
	$IsTranslated = strpos($textArray['desc'],"о");
	$IsTranslated = strpos($textArray['tags'],"а");
	$IsTranslated = strpos($textArray['tags'],"о");
	
		$mysql_hostname = "localhost";
		$mysql_user = "z58365_cbru3";
		$mysql_password = "greenbat";
		$mysql_database = "cartoonbanken";

		$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
		mysql_select_db($mysql_database, $link) or die("Could not select database");
		mysql_set_charset('utf8',$link);


	if ($IsTranslated==FALSE){
	//echo "2<br>";

		$sql = "UPDATE `wp_product_list` SET name = '".$textArray['name']."', description = '".$textArray['desc']."', additional_description = '".$textArray['tags']."', translated = '1' where id = ".$cartoonID;	
		$result = mysql_query($sql);
			//echo $sql."<br>";

	}
	else
	{
		$sql = "UPDATE `wp_product_list` SET translated = '2' where id = ".$cartoonID;	
		$result = mysql_query($sql);
	}
}

function markCartoonAsTranslated($cartoonID)
{
	$mysql_hostname = "localhost";
	$mysql_user = "z58365_cbru3";
	$mysql_password = "greenbat";
	$mysql_database = "cartoonbanken";

    $link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
	mysql_select_db($mysql_database, $link) or die("Could not select database");
    mysql_set_charset('utf8',$link);

    $sql = "UPDATE `wp_product_list` SET translated = '1' where id = ".$cartoonID;
    $result = mysql_query($sql);
}

function getTextToTranslate($cartoonID)
{
	$mysql_hostname = "localhost";
	$mysql_user = "z58365_cbru3";
	$mysql_password = "greenbat";
	$mysql_database = "cartoonbanken";

	$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
    mysql_select_db($mysql_database, $link) or die("Could not select database");
	mysql_set_charset('utf8',$link);

	$arr = array();

    $sql = "SELECT `name`,`description`,`additional_description` FROM `wp_product_list` where id = ".$cartoonID;
    $result = mysql_query($sql);
        while($r = mysql_fetch_array($result)) {
            $arr = array(
            'name' => $r["name"],
            'desc' => $r["description"],
            'tags' => $r["additional_description"],
            );
		}
    return $arr;
}

function saveTranslated($out){
    SaveToFile("/home/www/cb3/temp/translated.txt",$out);
}

function SaveToFile($file, $s, $mode='t') {
    $f = fopen($file, 'a+'.$mode);
    if(!$f) {
        die('Can\'t write to file '.$file);
    }
    fwrite($f, $s."\n");
    fclose($f);
}
  
function pokazh($to_print,$comment = '')
{
	$response = "<div style='margin:2px;padding-left:6px;background-color:#FFCC66;border:1px solid #CC0066;'><pre><b>".$comment.":</b> ".print_r($to_print,true)."</pre></div>";
	//echo ($response); 
}
  
?>
