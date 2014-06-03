<?php
require("config.php");
require_once("/home/www/j/inc/functions.php");

$value = json_decode($HTTP_RAW_POST_DATA, true);
$arr = implode(',',$value);

$offset = 0;
$items_on_page = ITEMS_ON_PAGE;

$search_keywords_filter = "";
$cartoonid_filter = "";
$latest = "";
$brandid_filter = "";
$categoryid_filter = "";
$exclude_category_sql = "";
$colorfilter = "";
$approved_or_not = "";
$min_points_filter = "";
$orderBy = "";

if (isset($value['offset']) && is_numeric($value['offset'])){$offset=mysql_escape_string($value['offset']);}else{$offset=' 0';}


if (isset($value['brand']) && $value['brand']==1){$orderBy=' ORDER BY id DESC ';}else{$orderBy=' ORDER BY votes_rate DESC ';}

if (isset($value['brand']) && is_numeric($value['brand']) && $value['brand']!=0 ){
    $brandid_filter = " AND ".PRODUCT_TABLE.".`brand` = '".mysql_escape_string($value['brand'])."' "; }
else{
    $brandid_filter=' AND '.PRODUCT_TABLE.'.`brand` != 36 ';
}


if (isset($value['term'])&&!empty($value['term'])){
    //avoid minimal points barrier:
    $min_points_filter = "";
    $sword = mysql_escape_string($value['term']);
    //save_search_terms($sword);
    $id_list = ssearch ($sword);
    if (strlen($id_list)>3){
    $search_keywords_filter = " AND ".PRODUCT_TABLE.".`id` in (".$id_list.") ";}
    else {$search_keywords_filter ="";$searchdonebutnothingfound=true;}}
else {$search_keywords_filter ="";}

$sql = "SELECT  SQL_CALC_FOUND_ROWS ".PRODUCT_TABLE.".*, `wp_product_brands`.`name` AS brand,  `wp_product_brands`.`id` AS brandid,  `wp_product_categories`.`name` AS kategoria,  ".PRODUCT_TABLE.".`category` AS category_id FROM  ".PRODUCT_TABLE." LEFT JOIN  `wp_product_brands` ON  ".PRODUCT_TABLE.".`brand` =  `wp_product_brands`.`id` LEFT JOIN  `wp_product_categories` ON  ".PRODUCT_TABLE.".`category` =  `wp_product_categories`.`id` WHERE  ".PRODUCT_TABLE.".`active` =  '1' " . $search_keywords_filter . $cartoonid_filter . $latest . $brandid_filter . $categoryid_filter . $exclude_category_sql . $colorfilter . $approved_or_not .  $min_points_filter . " AND  ".PRODUCT_TABLE.".`visible` =  '1' $orderBy LIMIT ".$offset.",".$items_on_page;

$sql_found = "SELECT FOUND_ROWS()";

$bd = mysql_connect($mysql_hostname, $mysql_user, $mysql_password) or die("Could not connect database");
mysql_select_db($mysql_database, $bd) or die("Could not select database");
mysql_set_charset('utf8',$bd);

$result = mysql_query($sql);    
if (!$result) {die('Invalid query: ' . mysql_error());}
$rows = array('images'=>array(),'total'=>array());
while ($row = mysql_fetch_array($result)){
    array_push($rows['images'],array(
        'id' => $row['id'],
        'image' => $row['image'],
        'name' => stripslashes($row['name']),
        'description' => stripslashes($row['description']),
        'artist' => $row['brand'],
        'tags' => cleanuptags(stripslashes($row['additional_description']))
        ));
}
// how many rows found total
$result_rows_found = mysql_query($sql_found);
if (!$result_rows_found) {die('Invalid query: ' . mysql_error());}
$rows_found = mysql_fetch_row($result_rows_found);
 array_push($rows['total'],array(
        'total' => $rows_found[0],
        'limit' => $items_on_page,
        'offset' => $offset
        ));

$output = json_encode($rows);
echo $output;



?>


