<?php 

if (!isset($_GET['p']) || !is_numeric($_GET['p']))
{
    echo ("sorry, no parameters provided");
    exit;
}


// configuration
include("/home/www/cb3/ales/config.php");

//open db connection
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);

$id = trim($_GET['p']);

$sql="SELECT width, height 
FROM wp_product_files, wp_product_list
WHERE wp_product_files.id = wp_product_list.file AND
wp_product_list.id = ".$id;

$result = mysql_query("$sql");
if (!$result) {die('Invalid query: ' . mysql_error());}

$count=mysql_num_rows($result);

while($row=mysql_fetch_array($result))
{
    $width = $row['width'];
    $height = $row['height'];
}

mysql_close($link);

$_size_warning = "";

if ($width < 800 or $height < 800){
    $_size_warning = "<div style='font-size:1em;'><a style='color:red;' href='http://cartoonbank.ru/?page_id=771'>Внимание! Размеры файла<br />ограничивают применение!</a></div>";
}

$_size = $width."px X ".$height."px;";
        $_x_sm = round(($width/300)*2.54, 1);
        $_y_sm = round(($height/300)*2.54, 1);
        $_sizesm = $_x_sm." см X ".$_y_sm." см";

$out = $_size."<br /><span style='color:#ACACAC;font-size:0.875em;'>при печати 300dpi:<br />".$_sizesm.$_size_warning;

echo $out;
exit;
?>