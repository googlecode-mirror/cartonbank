<?
//FILTERS
$items_on_page = 20;
$min_points = 2.49;

if (isset($_REQUEST['offset'])&&is_numeric($_REQUEST['offset'])){$offset=$_REQUEST['offset'];}else{$offset=0;}
if (isset($_REQUEST['new'])&&$_REQUEST['new']==1){$orderBy=' ORDER BY id DESC ';}else{$orderBy=' ORDER BY votes_rate DESC ';}
if (isset($_REQUEST['color'])&&$_REQUEST['color']=='color'){$colorfilter=" AND color=1 ";}elseif(isset($_REQUEST['color'])&&$_REQUEST['color']=='bw'){$colorfilter=" AND color = '0'";}else{$colorfilter="";}
if (isset($_REQUEST['category'])&&is_numeric($_REQUEST['category'])){$categoryid_filter = " AND `wp_product_list`.`category` = '".mysql_escape_string($_REQUEST['category'])."' "; $category=$_REQUEST['category'];}else{$categoryid_filter = '';$category='5';}
if (isset($_REQUEST['brand'])&&is_numeric($_REQUEST['brand'])){$brandid_filter = " AND `wp_product_list`.`brand` = '".mysql_escape_string($_REQUEST['brand'])."' "; $brandid=mysql_escape_string($_REQUEST['brand']);}else{$brandid_filter=' AND `wp_product_list`.`brand` != 7 ';$brandid=0;}
if (isset($_REQUEST['cartoonid'])&&is_numeric($_REQUEST['cartoonid'])){$cartoonid =$_REQUEST['cartoonid'];$cartoonid_filter=" AND `wp_product_list`.`id` = ".$cartoonid." ";}else{$cartoonid=2123;$cartoonid_filter="";}
if (isset($_REQUEST['brand']) && is_numeric($_REQUEST['brand'])){$min_points_filter = "";}else{$min_points_filter = " AND votes_sum/votes>".$min_points. " ";}
if (isset($_REQUEST['cartoonid'])&&is_numeric($_REQUEST['cartoonid'])){$min_points_filter = "";}
if (isset($_REQUEST['category']) && $_REQUEST['category']=='666'){$exclude_category_sql ='';}else{$exclude_category_sql =" AND `wp_product_list`.category != '666' ";}
$approved_or_not= " AND `wp_product_list`.`approved`='1' "; 

//GET search id_list
if (isset($_REQUEST['cs'])&&!empty($_REQUEST['cs'])){
	//avoid minimal points barrier:
	$min_points_filter = "";
    $sword = mysql_escape_string($_REQUEST['cs']);
	//todo uncomment to save
    //save_search_terms($sword);
    $id_list = ssearch ($sword);
    if (strlen($id_list)>3){
    $search_keywords_filter = " AND `wp_product_list`.`id` in (".$id_list.") ";}
    else {$search_keywords_filter ="";$searchdonebutnothingfound=true;}
}
    else {$search_keywords_filter ="";
}

// FINAL SQL

$sql = "SELECT `wp_product_list`.*, `wp_product_brands`.`name` as brand, `wp_product_brands`.`id` as brandid, `wp_product_categories`.`name` as kategoria, `wp_product_list`.`category` as category_id, `wp_product_files`.`width`, `wp_product_files`.`height` FROM `wp_product_list`, `wp_product_brands`, `wp_product_categories`,`wp_product_files` WHERE `wp_product_list`.`active`='1' " . $cartoonid_filter . $brandid_filter . $categoryid_filter . $exclude_category_sql . $colorfilter . $approved_or_not .  $min_points_filter . " AND wp_product_files.id = wp_product_list.file AND `wp_product_list`.`visible`='1' ".$search_keywords_filter." AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_product_list`.`category` = `wp_product_categories`.`id`  $orderBy LIMIT ".$offset.",".$items_on_page; 



// FINAL sql FOR ТЕМА ДНЯ
if ($category=='777'){
$sql="SELECT `wp_product_list`.*, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`id` = `wp_item_category_associations`.`product_id`  AND `wp_product_list`.`tema_dnya_approved` = '1'  AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` AND  wp_product_list.id = (select id from tema_dnya where DATETIME = DATE( NOW( ) ) ) LIMIT 1 UNION SELECT `wp_product_list`.*, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1'  AND `wp_item_category_associations`.`category_id` != '666'  AND `wp_product_list`.`approved` = '1'  AND `wp_product_list`.`tema_dnya_approved` = '1'  AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` AND `wp_item_category_associations`.`category_id`='777'  AND `wp_product_categories`.`id`=777 and wp_product_list.id not in (SELECT id FROM tema_dnya WHERE DATETIME = DATE( NOW( ) ) )";
}

// COUNT total number of images for this query
$sql_count_total = "SELECT COUNT(`wp_product_list`.id) as count FROM `wp_product_list`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1' " . $cartoonid_filter . $brandid_filter . $categoryid_filter . $exclude_category_sql . $colorfilter . $approved_or_not . " AND `wp_product_list`.`visible`='1' ".$search_keywords_filter." AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_product_list`.`category` = `wp_product_categories`.`id`"; 


//todo $searchdonebutnothingfound???
if (!$searchdonebutnothingfound){
$count = get_results($sql_count_total);
$totalitems = $count[0]['count'];
}

$rrr = get_results($sql);
$result = mysql_query($sql);
if (!$result) {die('Invalid query: ' . mysql_error());}

$licontent = '';
while($row=mysql_fetch_array($result))
{

//dimensions
	$width=$row['width'];
	$height=$row['height'];
	$_size_warning = "";
	if ($width < 800 or $height < 800){
		$_size_warning = "<div style='font-size:1em;'><a style='color:red;' href='".SITEURL."?page_id=771'>Внимание! Размеры файла<br />ограничивают применение!</a></div>";
	}
	$_size = $width."px X ".$height."px;";
			$_x_sm = round(($width/300)*2.54, 1);
			$_y_sm = round(($height/300)*2.54, 1);
			$_sizesm = $_x_sm." см X ".$_y_sm." см";
	$sizeout = "<div id='dimensions'><b>Размер:</b><br />".$_size."<span style='color:#ACACAC;font-size:0.875em;'><br />при печати 300dpi:<br />".$_sizesm.$_size_warning."</div>";
///dimensions

$licontent .= '<li id="'.$row['id'].'" style="background-image:url(\'http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/'.$row['image'].'\');background-repeat:no-repeat;" title="'.$row['name'].'"><span class="slideinfo"><b>Номер: </b><span id="cuid_'.$row['id'].'">'.$row['id'].'</span><br /><b>Автор:</b><br /><a href="?brand='.$row['brandid'].'">'.$row['brand'].'</a><br /><b>Название:</b><br />'.stripslashes($row['name']).'<br /><b>Категория:</b><br />'.stripslashes($row['kategoria']).'<br /><b>Описание:</b><br />'.stripslashes($row['description']).'<br /><b>Тэги:</b><br />'.prepare_tags($row['additional_description']).$sizeout.'<div id="star_rating_'.$row['id'].'"><img src="http://cartoonbank.ru/img/ldng.gif"></div></span></li>';

//pokazh($row);
}




?>











<!-- main start -->
<div id="content">
<div id="contentmiddle">


	<!-- page navigator -->
	<?
	$totalitems = 200;
	if(isset($_GET['offset']) && is_numeric($_GET['offset'])){$page = $_GET['offset']/20 + 1;}else{$page=1;}
	$_pages_navigation = getPaginationString($page, $totalitems, $limit, $adjacents = 1, $targetpage = "index.php", $pagestring = "?".get_url_vars()."offset=");
	echo $_pages_navigation;
	?>
	<!-- ///page navigator -->


<div id="container">
	<div id="slides">
        <!-- Slider -->
        <ul class="bjqs">
			<?echo $licontent;?>
		</ul>
</div> <!-- ///slides -->
</div> <!-- ///container -->
</div> <!-- ///contentmiddle -->
</div> <!-- ///content -->


<!-- main end -->
