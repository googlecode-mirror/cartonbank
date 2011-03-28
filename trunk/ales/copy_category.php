<?
//copy category id from `wp_item_category_associations` to `wp_product_list`

	include("config.php");
	global $wpdb;
/*
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);
*/

$sql = "select product_id, category_id from `wp_item_category_associations` where category_id != '777'";

	$result = mysql_query($sql);
	if (!$result) {die('Invalid query: ' . mysql_error());}


while($row=mysql_fetch_array($result))
{
	$_product_id = $row['product_id'];
	$_category_id = $row['category_id'];

	if (!$_category_id != '777')
	{
		$sql_update = "UPDATE wp_product_list SET category=$_category_id WHERE id = '".$_product_id."'";
								//pokazh ($sql_update);
		$res = mysql_query($sql_update);
		if (!$res) {die('Invalid query: ' . mysql_error());}
	}
}
echo "done\n\r";
/*
SELECT category, count(category) FROM `wp_product_list` group by category

SELECT * FROM `wp_product_list` WHERE category = 777
SELECT * FROM `wp_product_list` WHERE name = 'Принуждение к миру'
SELECT * FROM `wp_product_list` WHERE id = '4800'


select product_id, category_id from `wp_item_category_associations` where category_id!='777' and  product_id = '4800'

select product_id, category_id from `wp_item_category_associations` where category_id = '777'
select product_id, category_id from `wp_item_category_associations` where product_id = '4800'

select product_id, category_id from `wp_item_category_associations` where product_id in (select product_id from `wp_item_category_associations` where category_id = '777')

SELECT id, name, category FROM `wp_product_list` WHERE id in (select product_id from `wp_item_category_associations` where product_id in (select product_id from `wp_item_category_associations` where category_id = '777'))

SELECT id, name, category FROM `wp_product_list` group by category


			id		name				category
			4800	Принуждение к миру	777
			6035	Опять война	777
			6823	Уборщица и атом (ч/б)	777
			6824	Уборщица и атом	777
			6825	Диктатура счастья	777
			7205	тест	777
			7244	ракета с дубиной	777
			7340	самолет и бомбы	777
			8390	do not disturb-не тревожить	777
			9902	Мушка	777
			10854	Перебор	777
			11061	Миллиардер	777
			11146	Распил	777
			11277	Две задницы	777
			11284	Разглаживание проблемы	777
			11327	Операция \&quot;Одиссей\&quot;	777

			category	count(category)
			0	22
			4	4410
			5	4885
			6	502
			11	269
			13	91
			14	111
			15	92
			666	704
			777	16

*/
?>