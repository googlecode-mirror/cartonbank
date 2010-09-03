<!-- begin sidebar -->

	<div id="sidebar">

    <!-- <h2>Поиск по сайту:</h2>
	<form id="searchform" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="text" name="s" id="s" size="20" onfocus="this.value='';" value="введите слово..."/></form> -->

	<h2>Поиск картинок:</h2>
	<form method="post" id="searchform" action="?page_id=29">
	<input id="s" size="15" type="text" value="введите слово..." name="cs" id="search_input" onfocus="this.value='';"/>
	<input type="submit" id="searchsubmit" value="Искать" />
	<div id='tags'><a href='?page_id=390'>Все тэги</a></div>
<!-- 	
	<br>
	<select id="colorselect" name="">
		<option value="all" selected>all</option>
		<option value="bw">ч/б</options>
		<option value="color">цв.</options>
	</select>
 -->	</form>
	<!-- <form id="cartoonsearchform" method="get" action="?page_id=29">
	<input type="text" name="cs" id="cs" size="20" onfocus="this.value='';" value="введите слово..." ></form> -->
<br>


<?php //wp_list_pages('title_li=<h2>Pages<h2>&exclude=30,31,32'); ?>

	<br><h2>Категории:</h2> 
<?php
global $wpdb;
$seperator = '';
$options = '';
// Categories
    $categories = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `active`='1' AND `category_parent` = '0' ORDER BY `order` ASC",ARRAY_A);
	$category_count = $wpdb->get_results("SELECT `wp_item_category_associations`.`category_id`, COUNT(`wp_product_list`.`id`) as count FROM `wp_product_list`,`wp_item_category_associations` WHERE `wp_product_list`.`active`='1' AND `wp_product_list`.visible='1' AND `wp_product_list`.`brand` in (SELECT DISTINCT id FROM `wp_product_brands`) AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` GROUP BY `wp_item_category_associations`.`category_id`;",ARRAY_A);
	$total_cartoons = 0;
	if($category_count != null)
	{
		foreach ($category_count as $cat)
		{
			$total_cartoons = $total_cartoons + $cat['count'];
		}
    if($categories != null)
      {
	   $options .= "<a href='".get_option('product_list_url').$seperator."&category=0'>Все рисунки [".$total_cartoons."]</a><br />";

      foreach($categories as $option)
        {
        $options .= "<a href='".get_option('product_list_url').$seperator."&category=".$option['id']."'>".stripslashes($option['name'])."";
		foreach ($category_count as $cat_row)
			{
				if ($cat_row['category_id'] == $option['id'])
				{
					$options .= " [".$cat_row['count']."]";
				}
			}
		$options .= "</a><br />";

		
		$subcategory_sql = "SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `active`='1' AND `category_parent` = '".$option['id']."' ORDER BY `id`";
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
      }
	}
    echo $options;
?>

<br><h2>Авторы</h2>

<?php
// Autors
    echo "<div id='branddisplay1'>";
    $options ='';
	$seperator = '';
    $brands = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_brands` WHERE `active`='1' ORDER BY `name` ASC",ARRAY_A);
	$cartoons_count = $wpdb->get_results("SELECT `b`.`id` , COUNT( * ) AS count FROM `wp_product_list` AS p, `wp_product_brands` AS b WHERE `b`.`active` =1 AND `p`.`active` = 1 AND `p`.`visible` = 1 AND `p`.`brand` = `b`.`id` GROUP BY `p`.`brand` ",ARRAY_A);
    if($brands != null && $cartoons_count != null)
      {
      foreach($brands as $option)
        {
        $options .= "<a class='categorylink' href='".get_option('product_list_url').$seperator."&brand=".$option['id']."'>".stripslashes($option['name']);
		foreach ($cartoons_count as $count_row)
			{
				if ($count_row['id'] == $option['id'])
				{
					$options .= " [".$count_row['count']."]";
				}
			}
		$options .= "</a><br />";
        }
      }
    echo $options;
    echo "</div>";
?>
<div style="float:right;">
	<br><h2>Вход</h2>
		<ul style="text-align:right;">
		<?php wp_register(); ?>
		<?php wp_loginout(); ?>
		<?php wp_meta(); ?>
		</ul>
</div>
</div>

<!-- end sidebar -->