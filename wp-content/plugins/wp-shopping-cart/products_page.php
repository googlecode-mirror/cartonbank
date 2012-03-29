<?
global $wpdb, $totalitems, $sword;
$sword = '';
//var_dump($_REQUEST);

// Variables
$items_on_page = 20;

//FILTERS
if (isset($_REQUEST['offset'])&&is_numeric($_REQUEST['offset'])){$offset=$_REQUEST['offset'];}else{$offset=0;};
if (isset($_REQUEST['new'])&&$_REQUEST['new']==1){$orderBy=' ORDER BY id DESC ';}else{$orderBy=' ORDER BY votes_rate ';};
if (isset($_REQUEST['color'])&&$_REQUEST['color']=='color'){$colorfilter=" AND color=1 ";}elseif(isset($_REQUEST['color'])&&$_REQUEST['color']=='bw'){$colorfilter=" AND color = '0'";}else{$colorfilter="";};
if (isset($_REQUEST['category'])&&is_numeric($_REQUEST['category'])){$categoryid_filter = " AND `wp_product_list`.`category` = '".mysql_escape_string($_REQUEST['category'])."' "; $category=$_REQUEST['category'];}else{$categoryid_filter = '';$category='5';};
if (isset($_REQUEST['brand'])&&is_numeric($_REQUEST['brand'])){$brandid_filter = " AND `wp_product_list`.`brand` = '".mysql_escape_string($_REQUEST['brand'])."' ";}else{$brandid_filter='';};
if (isset($_REQUEST['cartoonid'])&&is_numeric($_REQUEST['cartoonid'])){$cartoonid =$_REQUEST['cartoonid'];$cartoonid_filter=" AND `wp_product_list`.`id` = ".$cartoonid." ";}else{$cartoonid=2123;$cartoonid_filter="";};

$exclude_category_sql ='';
$approved_or_not= " AND `wp_product_list`.`approved`='1' "; 

//GET search id_list
if (isset($_REQUEST['cs'])&&!empty($_REQUEST['cs'])){
    $sword = mysql_escape_string($_REQUEST['cs']);
    $id_list = ssearch ($sword);
    $search_keywords_filter = " AND `wp_product_list`.`id` in (".$id_list.") ";}else {$search_keywords_filter ="";
}

// FINAL SQL
$sql = "SELECT `wp_product_list`.*, `wp_product_brands`.`name` as brand, `wp_product_brands`.`id` as brandid, `wp_product_categories`.`name` as kategoria, `wp_product_list`.`category` as category_id FROM `wp_product_list`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1' " . $cartoonid_filter . $brandid_filter . $categoryid_filter . $exclude_category_sql . $colorfilter . $approved_or_not . " AND `wp_product_list`.`visible`='1' ".$search_keywords_filter." AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_product_list`.`category` = `wp_product_categories`.`id`  $orderBy LIMIT ".$offset.",".$items_on_page; 

// FINAL sql FOR ТЕМА ДНЯ
if ($category=='777'){
$sql="SELECT `wp_product_list`.*, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`id` = `wp_item_category_associations`.`product_id`  AND `wp_product_list`.`tema_dnya_approved` = '1'  AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` AND  wp_product_list.id = (select id from tema_dnya where DATETIME = DATE( NOW( ) ) ) LIMIT 1 UNION SELECT `wp_product_list`.*, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1'  AND `wp_item_category_associations`.`category_id` != '666'  AND `wp_product_list`.`approved` = '1'  AND `wp_product_list`.`tema_dnya_approved` = '1'  AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` AND `wp_item_category_associations`.`category_id`='777'  AND `wp_product_categories`.`id`=777 and wp_product_list.id not in (SELECT id FROM tema_dnya WHERE DATETIME = DATE( NOW( ) ) )";
}

// COUNT total number of images for this query
$sql_count_total = "SELECT COUNT(`wp_product_list`.id) as count FROM `wp_product_list`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1' " . $cartoonid_filter . $brandid_filter . $categoryid_filter . $exclude_category_sql . $colorfilter . $approved_or_not . " AND `wp_product_list`.`visible`='1' ".$search_keywords_filter." AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_product_list`.`category` = `wp_product_categories`.`id`"; 
$count = $wpdb->get_results($sql_count_total,ARRAY_A);
$totalitems = $count[0]['count'];

//output top of the page with main image
$_bigpicstrip = '';
$_bigpictext = '';
$_bigpic='';
$_bottomstriptext = '';
               echo "<div id='bigpictopstrip'>".$_bigpicstrip."</div>";
               echo "<div id='bigpictext'>".$_bigpictext."</div>";
               echo "<div id='bigpic'><a href='#pt' onclick=\"get_item1();\">".$_bigpic."</a></div>";
               echo "<div style='clear:both;'></div>";
               echo "<div id='bigpicbottomstrip' style='float:right;margin-bottom:6px;'>".$_bottomstriptext."</div>";



// output the complete page with pictures:
echo product_display_paginated ($sql,$offset,$items_on_page);



//test output
var_dump($sql);
echo "<br><br>";
var_dump($sql_count_total);





pokazh($sword);
?>


<script type="text/javascript">
<!--
jQuery(document).ready(function() {
on_start();
<?if (isset($sword)&&$sword!=''){
?>highlight('<?echo $sword;?>');<?
}
?>
get_5stars();
get_dimensions();
get_share_this();
});
//-->
</script>