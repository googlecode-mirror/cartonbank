<?
global $wpdb, $totalitems, $sword;
$sword = '';
$searchdonebutnothingfound = false;
//var_dump($_REQUEST);

// Variables
$items_on_page = 20;
$min_points = 2.49;

//FILTERS
if (isset($_REQUEST['offset'])&&is_numeric($_REQUEST['offset'])){$offset=$_REQUEST['offset'];}else{$offset=0;}
if (isset($_REQUEST['new'])&&$_REQUEST['new']==1){$orderBy=' ORDER BY id DESC ';}else{$orderBy=' ORDER BY votes_rate DESC ';}
if (isset($_REQUEST['new'])&&$_REQUEST['new']==2){$latest=' AND  `wp_product_list`.id > ( SELECT MAX( id ) FROM  `wp_product_list` ) - 1000 ';}else{$latest='';}
if (isset($_REQUEST['color'])&&$_REQUEST['color']=='color'){$colorfilter=" AND color='1' ";}elseif(isset($_REQUEST['color'])&&$_REQUEST['color']=='bw'){$colorfilter=" AND color = '0'";}else{$colorfilter="";}
if (isset($_REQUEST['category'])&&is_numeric($_REQUEST['category'])){$categoryid_filter = " AND `wp_product_list`.`category` = '".mysql_escape_string($_REQUEST['category'])."' "; $category=$_REQUEST['category'];}else{$categoryid_filter = '';$category='5';}
if (isset($_REQUEST['brand'])&&is_numeric($_REQUEST['brand'])){$brandid_filter = " AND `wp_product_list`.`brand` = '".mysql_escape_string($_REQUEST['brand'])."' "; $brandid=mysql_escape_string($_REQUEST['brand']);}else{$brandid_filter=' AND `wp_product_list`.`brand` != 36 ';$brandid=0;}
if (isset($_REQUEST['cartoonid'])&&is_numeric($_REQUEST['cartoonid'])){$cartoonid =$_REQUEST['cartoonid'];$cartoonid_filter=" AND `wp_product_list`.`id` = ".$cartoonid." ";}else{$cartoonid=2123;$cartoonid_filter="";}


//if (isset($_REQUEST['cartoonid'])&&is_numeric($_REQUEST['cartoonid'])){$min_points_filter = "";}else{$min_points_filter = " AND votes_sum/votes>".$min_points. " ";}

if (isset($_REQUEST['brand']) && is_numeric($_REQUEST['brand']))
{
	$min_points_filter = "";
}
else
{
	$min_points_filter = " AND votes_sum/votes>".$min_points. " ";
}
if (isset($_REQUEST['cartoonid'])&&is_numeric($_REQUEST['cartoonid'])){$min_points_filter = "";}


if (isset($_REQUEST['category']) && $_REQUEST['category']=='666')
{
	$exclude_category_sql ='';
}
else 
{
	$exclude_category_sql =" AND `wp_product_list`.category != '666' ";
}

//$exclude_category_sql ='';
$approved_or_not= " AND `wp_product_list`.`approved`='1' "; 

//GET search id_list
if (isset($_REQUEST['cs'])&&!empty($_REQUEST['cs'])){
        //avoid minimal points barrier:
        $min_points_filter = "";
    $sword = mysql_escape_string($_REQUEST['cs']);
	save_search_terms($sword);
    $id_list = ssearch ($sword);
    if (strlen($id_list)>3){
    $search_keywords_filter = " AND `wp_product_list`.`id` in (".$id_list.") ";}
    else {$search_keywords_filter ="";$searchdonebutnothingfound=true;}
}
    else {$search_keywords_filter ="";
}

// FINAL SQL

//$sql = "SELECT `wp_product_list`.*, `wp_product_brands`.`name` as brand, `wp_product_brands`.`id` as brandid, `wp_product_categories`.`name` as kategoria, `wp_product_list`.`category` as category_id FROM `wp_product_list`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1' " . $cartoonid_filter . $latest . $brandid_filter . $categoryid_filter . $exclude_category_sql . $colorfilter . $approved_or_not .  $min_points_filter . " AND `wp_product_list`.`visible`='1' ".$search_keywords_filter." AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_product_list`.`category` = `wp_product_categories`.`id`  $orderBy LIMIT ".$offset.",".$items_on_page; 
$sql = "SELECT `wp_product_list`.*, `wp_product_brands`.`name` AS brand,  `wp_product_brands`.`id` AS brandid,  `wp_product_categories`.`name` AS kategoria,  `wp_product_list`.`category` AS category_id FROM  `wp_product_list` LEFT JOIN  `wp_product_brands` ON  `wp_product_list`.`brand` =  `wp_product_brands`.`id` LEFT JOIN  `wp_product_categories` ON  `wp_product_list`.`category` =  `wp_product_categories`.`id` WHERE  `wp_product_list`.`active` =  '1' " . $search_keywords_filter . $cartoonid_filter . $latest . $brandid_filter . $categoryid_filter . $exclude_category_sql . $colorfilter . $approved_or_not .  $min_points_filter . " AND  `wp_product_list`.`visible` =  '1' $orderBy LIMIT ".$offset.",".$items_on_page;
// FINAL sql FOR ТЕМА ДНЯ
if ($category=='777'){
$sql="SELECT `wp_product_list`.*, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`id` = `wp_item_category_associations`.`product_id`  AND `wp_product_list`.`tema_dnya_approved` = '1'  AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` AND  wp_product_list.id = (select id from tema_dnya where DATETIME = DATE( NOW( ) ) ) LIMIT 1 UNION SELECT `wp_product_list`.*, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1'  AND `wp_item_category_associations`.`category_id` != '666'  AND `wp_product_list`.`approved` = '1'  AND `wp_product_list`.`tema_dnya_approved` = '1'  AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` AND `wp_item_category_associations`.`category_id`='777'  AND `wp_product_categories`.`id`='777' and wp_product_list.id not in (SELECT id FROM tema_dnya WHERE DATETIME = DATE( NOW( ) ) )";
}

// COUNT total number of images for this query
$sql_count_total = "SELECT COUNT(`wp_product_list`.id) as count FROM `wp_product_list`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1' " . $cartoonid_filter . $brandid_filter . $categoryid_filter . $exclude_category_sql . $colorfilter . $approved_or_not . " AND `wp_product_list`.`visible`='1' ".$search_keywords_filter.$min_points_filter." AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_product_list`.`category` = `wp_product_categories`.`id`"; 
if (!$searchdonebutnothingfound){
$count = $wpdb->get_results($sql_count_total,ARRAY_A);
$totalitems = $count[0]['count'];
}

//output top of the page with main image
$_bigpicstrip = '';
$_bigpictext = '';
$_bigpic='';
$_bottomstriptext = '';
	echo "<div id='bigpictopstrip'>".$_bigpicstrip."</div>";
	echo "<div id='bigpictext'>".$_bigpictext."</div>";
	echo "<div id='bigpic'><a href='".SITEURL."cartoon/".$cartoonid."#pt' onclick=\"get_item1();\">".$_bigpic."</a></div>";
	echo "<div class='clrb'></div>";
	echo "<div id='hone' style='float:right;'></div>";
	echo "<div id='bigpicbottomstrip' style='float:right;margin-bottom:6px;'>".$_bottomstriptext."</div>";

// output the complete page with pictures:

if (!$searchdonebutnothingfound){
echo product_display_paginated ($sql,$offset,$items_on_page);
}
else{
    echo "<div>По вашему поиску ничего не найдено. Пожалуйста, попробуйте другие термины.</div>";
}


//test output
/*
var_dump($sql);
echo "<br><br>";
var_dump($sql_count_total);
pokazh($sword);
*/




?>


<script type="text/javascript">
<!--
jQuery(document).ready(function() {
on_start();
<?if (isset($sword)&&$sword!=''){
?>highlight('<?echo $sword;?>');<?}?>
scrpts();
});
//-->
</script>



<?

// bio    
$_artist="художник";
$bio = 'Автор исключительно талантливый художник. Больше нам про него пока ничего не известно.<br />Ведём бесконечные переговоры об обновлении этой информации.';
// Get the Brand (author) data
if (isset($brandid) && is_numeric($brandid)) 
{
    $brand_sql = "SELECT * FROM `wp_product_brands` where id = ". $brandid;
    $brand_result  = $GLOBALS['wpdb']->get_results($brand_sql,ARRAY_A);

    $_artist = "";
    if (isset($brand_result[0]['name'])){$_artist = $brand_result[0]['name'];}
    
    if (isset($brand_result[0]['bio_post_id']))
    {
        $bio_sql = "SELECT `post_content` FROM `wp_posts` WHERE id = ".$brand_result[0]['bio_post_id'] ; // todo: use page ID!
        $bio = $GLOBALS['wpdb']->get_results($bio_sql,ARRAY_A);
        $bio = $bio[0]['post_content'];
    }
}


function save_search_terms($terms)
{
    if (isset($terms) && $terms!='')
    {
        $_header = $_SERVER['HTTP_USER_AGENT'];
        $_remote_ip = $_SERVER['REMOTE_ADDR'];

        if (!(strstr($_header,'bot') || strstr($_header,'Yahoo') || strstr($_header,'DotBot') || strstr($terms,'%') || strstr($_header,'search') ))
        {
            $terms = trim($terms);
            if (substr($terms,-2)=='pt')
            {
                $terms = substr($terms,0,-2);
            }
            
            $sql = "INSERT INTO  `search_terms` (
                        `id` ,
                        `term` ,
                        `datetime`,
                        `header`
                        )
                        VALUES (
                        NULL ,  '".$terms."', 
                        CURRENT_TIMESTAMP,
                        '".$_remote_ip." ".$_header."'
                        );";
            
            $result = $GLOBALS['wpdb']->get_results($sql,ARRAY_A);
        }
    }
}

/*

<script>
    jQuery(document).ready(function(){
        jQuery("a[rel='slideshow']").colorbox({slideshow:true});        jQuery(".example8").colorbox({width:"50%", inline:true, href:"#hidden_bio"});
        jQuery(".cb_emailform").colorbox({width:"50%", inline:true, href:"#emailform1"});})
</script>
*/

?>



<div style='display:none;background-color:f0f0f0;padding:20px;'>
	<a href="" onclick="jQuery('.divc').hide();return false;" style="padding-left: 506px;">X закрыть окно</a><br>
    <div id='hidden_bio' style='text-align:left; padding:10px; background-color:#f0f0f0;'>
        <? echo ($bio); 
		/*
		<br>
        <p><a class='cb_emailform' href="#">Отправить письмо</a></p>
		*/?>
    </div>
</div>

<?/*
<div style='display:none'>
    <div id='emailform1' style='text-align:left; padding:10px; background:#fff;'>
            <h3>Письмо автору Картунбанка</h3>
            <form method='post' action='<? echo SITEURL;?>?page_id=29&amp;brand=<? echo $brandid;?>&amp;bio=1'>Email для обратной связи: <input name='email' type='text' class='w100'/><br /><br />
            <textarea class='w100' name='message' rows='15' cols='30'>Уважаемый <? echo $_artist;?>!</textarea><br />
            напишите любую цифру: <input type='text' name='klop' value=''>
            <input type='submit' value='Отправить письмо' class='borders'/>
            </form>
    </div>
</div>
*/
?>