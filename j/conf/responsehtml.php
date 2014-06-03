<?php
require("config.php");
require_once("/home/www/j/inc/functions.php");

$value = json_decode($HTTP_RAW_POST_DATA, true);
$arr = implode(',',$value);

$offset = 0;
$items_on_page = 200;

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

  $parts = parse_url($_GET['_escaped_fragment_']);
  $escapedfragment = $_GET['_escaped_fragment_'];
  $escaped = explode("&",$escapedfragment);
  //echo $escapedfragment;
  
  if (isset($_GET['b']))
  {
      $brand_id = mysql_escape_string($_GET['b']);
      //print_r ("b: ".$brand_id);
  }
  if (isset($_GET['s']))
  {
      $search_terms = mysql_escape_string($_GET['s']);
      //print_r ("s: ".$search_terms);
  }
  
  
  
    if (isset($brand_id) && is_numeric($brand_id)){
        $brandid_filter = " AND `wp_product_list`.`brand` = '".$brand_id."' "; }
    else{
        $brandid_filter=' AND `wp_product_list`.`brand` != 36 ';
    }
    if (isset($search_terms)&&!empty($search_terms)){
        //avoid minimal points barrier:
        $min_points_filter = "";
        $sword = mysql_escape_string($search_terms);
        //save_search_terms($sword);
        $id_list = ssearch ($sword);
        if (strlen($id_list)>3){
        $search_keywords_filter = " AND `wp_product_list`.`id` in (".$id_list.") ";}
        else {$search_keywords_filter ="";$searchdonebutnothingfound=true;}}
    else {$search_keywords_filter ="";}

    $sql = "SELECT `wp_product_list`.*, `wp_product_brands`.`name` AS brand,  `wp_product_brands`.`id` AS brandid,  `wp_product_categories`.`name` AS kategoria,  `wp_product_list`.`category` AS category_id FROM  `wp_product_list` LEFT JOIN  `wp_product_brands` ON  `wp_product_list`.`brand` =  `wp_product_brands`.`id` LEFT JOIN  `wp_product_categories` ON  `wp_product_list`.`category` =  `wp_product_categories`.`id` WHERE  `wp_product_list`.`active` =  '1' " . $search_keywords_filter . $cartoonid_filter . $latest . $brandid_filter . $categoryid_filter . $exclude_category_sql . $colorfilter . $approved_or_not .  $min_points_filter . " AND  `wp_product_list`.`visible` =  '1' $orderBy LIMIT ".$offset.",".$items_on_page;
  
    $bd = mysql_connect($mysql_hostname, $mysql_user, $mysql_password) or die("Could not connect database");
    mysql_select_db($mysql_database, $bd) or die("Could not select database");
    mysql_set_charset('utf8',$bd);

    $result = mysql_query($sql);    
    if (!$result) {die('Invalid query: ' . mysql_error());}
    $rows = array('images'=>array());
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

    //$output = print_r($rows['images']);

    ?>
<!doctype html>
<html>
 <head>
  <title> Карикатуры <?=$sword?></title>
  <meta charset="utf-8" />
 </head>
 <body>
 <h1>Карикатуры <?=$sword?></h1>
  <ul>
 <?
    $path = 'http://th.cartoonbank.ru/';
    
    foreach($rows['images'] as $key=>$img){
    //echo ("<li id=".$img['id']."> <a class='th' title='".$img['artist'].": <b>"+$img['name']."</b> <a href=http://cartoonbank.ru/cartoon/".$img['id']."/ target=_blank>купить</a>' href='http://sl.cartoonbank.ru/" . $img['image'] . "'> cartoon</a> </li>");
    //echo ("<li id=".$img['id']."> li </li>");
    echo ("<li id='".$img['id']."'><a href=http://cartoonbank.ru/cartoon/".$img['id']."/ target='_blank' title='".$img['tags']."'>".$img['name'].".</a> ".$img['artist'].". ".$img['description']." </li>");
    
    }
 ?> 
 </ul>
 </body>
 </html>
    
    
    <?
    
    
    
    
    
    echo $output;  

  
  exit; 
?>
