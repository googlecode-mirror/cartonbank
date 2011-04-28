<!-- sidebar start -->
<?php
global $wpdb;
$portfolio = false;
$brandid = '';

//filters set

// portfolio do show trash
if (isset($_GET['portf']) && is_numeric($_GET['portf']))
{
	$portfolio = true;
}

// brandid - to get to the auther section
if (isset($_GET['brand']) && is_numeric($_GET['brand']))
{
	$brandid = $_GET['brand'];
	$author_section = true;
	}
	else
	{
		$brandid = '';
		$author_section = false;
		}

?>
	<div id="sidebar">

	<h2>Поиск</h2>
	<form method="post" id="searchform" action="?page_id=29">
	<input id="s" size="25" type="text" value="введите поисковое слово..." name="cs" onfocus="this.value='';"/><br />
	<input type="hidden" id="brand" name="brand" value="<?echo $brandid;?>" />
	<input type="submit" id="searchsubmit" class='borders' value="Искать" style="margin-top:6px;margin-bottom:4px;" />
	</form>
	<a href="?page_id=927">Расширенный поиск</a>

<?
// Theme of the day
	$thedate = date("Y.m.d");

	$sql = "Select id from tema_dnya where datetime = '".$thedate."'";
	$cartoon_of_the_day = $wpdb->get_results($sql);

if ($cartoon_of_the_day!= null)
{
	$cartoon_of_the_day_id = $cartoon_of_the_day[0]->id;
	$sql = "Select image, name from wp_product_list where id = $cartoon_of_the_day_id";
	$image = $wpdb->get_results($sql);
	$image_name = $image[0]->image;
	$image_title = $image[0]->name;

}
else
{
	$cartoon_of_the_day_id = 0;
}

?>


<?if ($cartoon_of_the_day_id != 0){//border: 6px solid white;border: 1px solid #962d2a;
?>
<br /><br />
<div style="text-align:center; padding-top:6px; width:180px;height:186px;background-color:#668bb7;">
<div style="color:white; padding-bottom:4px;"><b>ТЕМА ДНЯ</b></div>
<div  style="text-align:center; margin-left:11px; padding-top:6px; width:158px;height:154px;background-color:white;"><a href="<?echo get_option('siteurl');?>/?page_id=29&category=777"><img src="<?echo get_option('siteurl')?>/wp-content/plugins/wp-shopping-cart/images/<?echo $image_name;?>" title="<?echo $image_title;?>" alt="<?echo $image_title;?>" class="thumb"></a><br /></div>
</div>

<?}?>

<br /><h2>Категории</h2> 

<?
$seperator = '';
$options = '';
if (!$author_section)
{ // all categories
// Categories

	// include category 666 (rokfor)
	$categories = $wpdb->get_results("SELECT * FROM `wp_product_categories` WHERE `active`='1' AND `category_parent` = '0' ORDER BY `order` ASC",ARRAY_A);
	//$category_count = $wpdb->get_results("SELECT `wp_item_category_associations`.`category_id`, COUNT(`wp_product_list`.`id`) as count FROM `wp_product_list`,`wp_item_category_associations` WHERE `wp_product_list`.`active`='1' AND `wp_product_list`.`approved`='1' AND `wp_product_list`.visible='1' AND `wp_product_list`.`brand` in (SELECT DISTINCT id FROM `wp_product_brands`) AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` GROUP BY `wp_item_category_associations`.`category_id`;",ARRAY_A);

	$category_count = $wpdb->get_results("SELECT `wp_product_list`.`category` as category_id, COUNT(`wp_product_list`.`id`) as count FROM `wp_product_list` WHERE `wp_product_list`.`active`='1' AND `wp_product_list`.`approved`='1' AND `wp_product_list`.visible='1' AND `wp_product_list`.`brand` in (SELECT DISTINCT id FROM `wp_product_brands`) GROUP BY `wp_product_list`.`category`;",ARRAY_A);


	// number of bw cartoons
	$bw_number = $wpdb->get_results("SELECT count(id) AS bw_number FROM `wp_product_list` WHERE color=0 AND `active`=1 AND `visible`=1 AND `approved`=1");
	$bw_number = $bw_number[0]->bw_number;
}
else
{
	$categories = $wpdb->get_results("SELECT * FROM `wp_product_categories` WHERE `active`='1' AND `category_parent` = '0' ORDER BY `order` ASC",ARRAY_A);

	//Author section
	if (isset($_GET['brand']) && is_numeric($_GET['brand']))
	{
		$brand = $_GET['brand'];

		//$category_count = $wpdb->get_results("SELECT `wp_item_category_associations`.`category_id`, COUNT(`wp_product_list`.`id`) as count FROM `wp_product_list`,`wp_item_category_associations` WHERE `wp_product_list`.`active`='1' AND `wp_product_list`.brand=".$brand." AND `wp_product_list`.visible='1' AND `wp_product_list`.approved='1' AND `wp_product_list`.`brand` in (SELECT DISTINCT id FROM `wp_product_brands`) AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` GROUP BY `wp_item_category_associations`.`category_id`;",ARRAY_A);

		$category_count = $wpdb->get_results("SELECT `wp_product_list`.`category` as category_id, COUNT(`wp_product_list`.`id`) as count FROM `wp_product_list` WHERE `wp_product_list`.`active`='1' AND `wp_product_list`.brand=".$brand." AND `wp_product_list`.visible='1' AND `wp_product_list`.approved='1' AND `wp_product_list`.`brand` in (SELECT DISTINCT id FROM `wp_product_brands`) GROUP BY `wp_product_list`.`category`;",ARRAY_A);

		// number of bw cartoons
		$bw_number = $wpdb->get_results("SELECT count(id) AS bw_number FROM `wp_product_list` WHERE color=0 AND brand=".$brand." AND `active`=1 AND `visible`=1  AND `approved`=1");
		$bw_number = $bw_number[0]->bw_number;
	}
}
	$total_cartoons = 0;
	if($category_count != null)
	{
		foreach ($category_count as $cat)
		{
			$total_cartoons = $total_cartoons + $cat['count'];
		}

$total_cartoons = $total_cartoons - 1;

	$color_number = $total_cartoons - $bw_number;
    if($categories != null)
      {
      foreach($categories as $option)
        {
		$cartoon_counter = 0;
        
		if ($option['id']=='666')
		{
			$category_in_the_list = '<div style="padding-top:2px;color:red;font-size:0.8em;"><a style="color:#006600;" href="'.get_option('siteurl').'/?page_id=649" title="подробнее о категории">Что такое «Рабочий стол»</a></div>'.'<a href="#" onclick="rokfor('.$brandid.');">'.stripslashes($option['name']).'';
		}
		else
		{
			if (is_numeric($brandid))
			$category_in_the_list = "<a href='".get_option('product_list_url').$seperator."&brand=".$brandid."&category=".$option['id']."'>".stripslashes($option['name'])."";
			else
			$category_in_the_list = "<a href='".get_option('product_list_url').$seperator."&category=".$option['id']."'>".stripslashes($option['name'])."";
		}
		
		foreach ($category_count as $cat_row)
			{
				if ($cat_row['category_id'] == $option['id'])
				{
					$category_in_the_list .= " [".$cat_row['count']."]";
					$cartoon_counter = $cat_row['count'];
				}
			}

		$category_in_the_list .= "</a><br />";
		

		if ($cartoon_counter == 0)
		{
			$category_in_the_list ='';
		}
		else
		{
			$options .= $category_in_the_list;
		}
		
		$subcategory_sql = "SELECT * FROM `wp_product_categories` WHERE `active`='1' AND `category_parent` = '".$option['id']."' ORDER BY `id`";
        $subcategories = $wpdb->get_results($subcategory_sql,ARRAY_A);
        if($subcategories != null)
          {
          foreach($subcategories as $subcategory)
            {
            $options .= "<li><a class='categorylink' href='".get_option('product_list_url').$seperator."&category=".$subcategory['id']."'>-".stripslashes($subcategory['name']);
			$options .= "</a></li>";
            }
          }
        }
      }// end: if($categories != null)
	}
    echo $options;
?>



<?php

if (!$author_section) // for not Author section (portfolio)
{

?><br /><h2><a href="http://cartoonbank.ru/?page_id=1427" title="Все авторы на одной странице">Авторы</a></h2><?



// Authors
    echo "<div id='branddisplay1'>";
		$options ='';
		$seperator = '';
		$brands = $wpdb->get_results("SELECT * FROM `wp_product_brands` WHERE `active`='1' ORDER BY `name` ASC",ARRAY_A);
		$cartoons_count = $wpdb->get_results("SELECT `b`.`id` , COUNT( p.id ) AS count FROM `wp_product_list` AS p, `wp_product_brands` AS b WHERE `b`.`active` =1 AND `p`.`active` = 1 AND `p`.`approved` = 1 AND `p`.`visible` = 1 AND `p`.`brand` = `b`.`id` GROUP BY `p`.`brand` ",ARRAY_A);

		$_selected = "";
		if (!isset($_GET['brand'])) {$_selected = ' selected ';}
		$authors = "<select name='authors' onchange=\"if(!options[selectedIndex].defaultSelected) location='".get_option('siteurl')."/?page_id=29&brand='+options[selectedIndex].value\" style=\"width:180px;margin-top:2px;\"><option ".$_selected." value=''>&nbsp;все авторы&nbsp;</option>";
		$_selected = "";

		foreach ($brands as $brand)
		{
			if (isset($_GET['brand']) && $brands[0]['id'] == $_GET['brand'])
				{$_selected = " selected";}
			$authors .= "<option $_selected value=".$brand['id'].">&nbsp;".$brand['name']." [".$brand['count']."]&nbsp;</option>";
			$_selected = "";
		}
		$authors .= "</select>";

		echo $authors;

    echo "</div>";
}
else
{
	// Get the Brand (author) data
	$brand_sql = "SELECT * FROM `wp_product_brands` where id = ". $brandid;
	$brand_result  = $GLOBALS['wpdb']->get_results($brand_sql,ARRAY_A);
/*
	$brands_sql = "SELECT id, name FROM `wp_product_brands` where active = 1 order by name";
	$brands_result  = $GLOBALS['wpdb']->get_results($brands_sql,ARRAY_A);
*/

	// avatar url
				
	if (isset($brand_result[0]['avatar_url']) && $brand_result[0]['avatar_url'] != '')
	{$avatar_url = "<img width=140 src='".$brand_result[0]['avatar_url']."'>";}
	else {$avatar_url = "<img width=140 src='".get_option('siteurl')."/img/avatar.gif'>";}

	// author name
	if (isset($brand_result[0]['name']) && $brand_result[0]['name'] != '')
	{$author_name = $brand_result[0]['name'];}else{$brand_result[0]['name']='';}

	// all authors dropdown
		$_selected = "";
		if (!isset($_GET['brand'])) {$_selected = ' selected ';}

		$authors = "<select name='authors' onchange=\"if(!options[selectedIndex].defaultSelected) location='".get_option('siteurl')."/?page_id=29&brand='+options[selectedIndex].value\" style=\"width:180px;margin-top:2px;\"><option ".$_selected." value=''>&nbsp;все авторы&nbsp;</option>";
		$_selected = "";

		foreach ($brands_sql as $brand)
		{
			if (isset($_GET['brand']) && $brands_sql[0]['id'] == $_GET['brand'])
				{$_selected = " selected";}
			$authors .= "<option $_selected value=".$brand['id'].">&nbsp;".$brand['name']." [".$brand['count']."]&nbsp;</option>";
			$_selected = "";
		}
		$authors .= "</select>";

?>
<br /><h2>Автор</h2> 
<?
echo $avatar_url."<br />";
echo $authors;
echo "<br /><a href='".get_option('siteurl')."/?page_id=29&brand=".$brandid."&bio=1'>Информация об авторе</a>";
echo "<br /><a href='".get_option('siteurl')."/?page_id=1284&ord=72&br=".$brandid."'>100 лучших работ</a>";
}
?>

<?
//$_rokfor_url = get_option('siteurl').'/?page_id=29&brand=8&category=666';
$_rokfor_url = get_option('siteurl').'/?page_id=29&category=666';
?>

<br /><h2>Разделы</h2>

<?
	if($categories != null && $total_cartoons > 0)
      {
		if ($author_section)
		{
		   $options = "<a href='".get_option('product_list_url').$seperator."&brand=".$brandid."&category=0&color=all'>Все изображения [".$total_cartoons."]</a><br />";
		   $options .= "<a href='".get_option('product_list_url').$seperator."&brand=".$brandid."&color=color'>Цветные [".$color_number."]</a><br />";
		   $options .= "<a href='".get_option('product_list_url').$seperator."&brand=".$brandid."&color=bw'>Чёрно-белые [".$bw_number."]</a><br />";
		}
		else
		{
		   $options = "<a href='".get_option('product_list_url').$seperator."&category=0&color=all'>Все изображения [".$total_cartoons."]</a><br />";
		   $options .= "<a href='".get_option('product_list_url').$seperator."&color=color'>Цветные [".$color_number."]</a><br />";
		   $options .= "<a href='".get_option('product_list_url').$seperator."&color=bw'>Чёрно-белые [".$bw_number."]</a><br />";
		}

	   echo $options;
	  }
?>

<br /><h2>Разное</h2>
<div id='last_sales'><a href='?page_id=1299'>100 продаж</a></div>
<div id='last_sales'><a href='?page_id=1284&ord=72&br=0'>100 лучших</a></div>
<div id='best_of_month'><a href='?page_id=643'>Рейтинг</a></div>
<div id='tags'><a href='?page_id=390'>Тэги</a></div>
<div style="float:right;width:180px;text-align:right;">
	<br /><h2>Вход</h2>
		<ul style="float:right;width:160px;text-align:right;">
		<?php wp_register(); ?>
		<?php wp_loginout(); ?>
		<?php wp_meta(); ?>
		</ul>
</div>
</div>
<!-- sidebar end -->