<?
// updates option values
	include("config.php");
	global $wpdb;

/*

Update total cartoon number

*/

	//insert into wp_options ('option_name') values ('total_cartoons_to_show')


		$sql = "SELECT COUNT(*) as count FROM `wp_product_list`,`wp_item_category_associations` WHERE `wp_product_list`.`active`='1'  AND `wp_item_category_associations`.`category_id` != '666' AND `wp_item_category_associations`.`category_id` != '777'   AND `wp_product_list`.`approved` = '1'  AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`brand` in (SELECT DISTINCT id FROM `wp_product_brands`) AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id`";

		if (!($result = mysql_query($sql))) {die('Invalid query: ' . mysql_error());}

		$row=mysql_fetch_array($result);

		$_total_cartoons_to_show = $row['count'];

							//pokazh($_total_cartoons_to_show,"_total_cartoons_to_show"); 

		if (is_numeric($_total_cartoons_to_show)) 
		{
			$sql = "update wp_options set option_value = $_total_cartoons_to_show where option_name = 'total_cartoons_to_show'";
							//pokazh ($sql);
			if (!($result = mysql_query($sql))) {die('Invalid query: ' . mysql_error());}
		}


/*

Update categories cartoon number

*/

	//insert into wp_options (option_name) values ('cartoons_by_category')

		
		$sql = "SELECT `wp_item_category_associations`.`category_id`, COUNT(`wp_product_list`.`id`) as count FROM `wp_product_list`,`wp_item_category_associations` WHERE `wp_product_list`.`active`='1' AND `wp_product_list`.`approved`='1' AND `wp_product_list`.visible='1' AND `wp_product_list`.`brand` in (SELECT DISTINCT id FROM `wp_product_brands`) AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` GROUP BY `wp_item_category_associations`.`category_id`";

		if (!($result = mysql_query($sql))) {die('Invalid query: ' . mysql_error());}

		$sql_update = "";

		while($row=mysql_fetch_array($result))
		{
			$_category_id = $row['category_id'];
			$_image_number = $row['count'];

			$sql_update = " update wp_product_categories set image_number=$_image_number where id=$_category_id; ";

								//pokazh($sql_update,"sql");

			$r = mysql_query($sql_update) or die(mysql_error());
		}


/*

Update cartoon number per author

*/

	$sql = "SELECT `b`.`id` , COUNT( * ) AS count FROM `wp_product_list` AS p, `wp_product_brands` AS b WHERE `b`.`active` =1 AND `p`.`active` = 1 AND `p`.`approved` = 1 AND `p`.`visible` = 1 AND `p`.`brand` = `b`.`id` GROUP BY `p`.`brand`";

	if (!($result = mysql_query($sql))) {die('Invalid query: ' . mysql_error());}

	$sql_update = "";

	while($row=mysql_fetch_array($result))
	{
		$_brand_id = $row['id'];
		$_count = $row['count'];

		$sql_update = " update wp_product_brands set count=$_count where id=$_brand_id; ";

							//pokazh($sql_update,"sql");

		$r = mysql_query($sql_update) or die(mysql_error());
	}


?>