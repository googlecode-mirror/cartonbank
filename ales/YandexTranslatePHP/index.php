<?php
  // translate cartoon by cartoonID
  $cartoonID = 19547;
  $out = "";
  
error_reporting(E_ALL);
ini_set("display_errors", 1);
include_once 'yandex_translate.php';
include_once 'big_text_translate.php';
$mysql_hostname = "localhost";
$mysql_user = "z58365_cbru3";
$mysql_password = "greenbat";
$mysql_database = "cartoonbanken";


$bd = mysql_connect($mysql_hostname, $mysql_user, $mysql_password) or die("Could not connect database");
mysql_select_db($mysql_database, $bd) or die("Could not select database");

function pokazh($to_print,$comment = '')
{
    $response = "<div style='margin:2px;padding-left:6px;background-color:#FFCC66;border:1px solid #CC0066;'><pre><b>".$comment.":</b> ".print_r($to_print,true)."</pre></div>";
    echo ($response); 
}

$textArray = getTextToTranslate($cartoonID);

$translator = new Yandex_Translate();  
$translator->eolSymbol = '<br />';

// cartoon ids to translate
    $link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
    mysql_set_charset('utf8',$link);
$sql = "SELECT id FROM  `wp_product_list`  WHERE translated =0 order by id desc limit 10";
$result = mysql_query($sql);
pokazh($sql);
if (result){
    while($r = mysql_fetch_array($result)) {
        pokazh($r['id']);
    };
}
else{die("wrong result");}
exit;





// create a record
$out .= "'".$cartoonID."'";
$translatedText = $translator->yandexTranslate('ru', 'en', $textArray['name']);  
$out .= ",'".$translatedText."'";
$translatedText = $translator->yandexTranslate('ru', 'en', $textArray['desc']);  
$out .= ",'".$translatedText."'";
$translatedText = $translator->yandexTranslate('ru', 'en', $textArray['tags']);  
$out .= ",'".$translatedText."'";

// save translated
saveTranslated($out);





function getTextToTranslate($cartoonID)
{
    require_once("/home/www/cb3/ales/config.php");
    $link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
    mysql_set_charset('utf8',$link);

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
  
  
?>
