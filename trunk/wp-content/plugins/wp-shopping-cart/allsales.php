<?php
$abspath = 'z:/home/localhost/www/';
$abspath_1 = "/home/www/cb/";
$abspath_2 = "/home/www/cb3/";

global $wpdb;

//pokazh($_SERVER);
//[DOCUMENT_ROOT] => /home/www/cb3/
if (strstr($_SERVER['DOCUMENT_ROOT'],'cb3/'))
{
	$abspath = $abspath_2;
	$params['database'] = 'cartoonbankru';
}
if (strstr($_SERVER['DOCUMENT_ROOT'],'cb3/'))
{
	$abspath = $abspath_2;
	$params['database'] = 'cartoonbankru';
}
else if (strstr($_SERVER['DOCUMENT_ROOT'],'cb/')) 
{
	$abspath = $abspath_1;
	$params['database'] = 'cartoonbankru';
}
else if (strstr($_SERVER['DOCUMENT_ROOT'],'/home/www/')) 
{
	$abspath = $abspath_1;
	$params['database'] = 'cartoonbankru';
}
else
{
	$params['database'] = 'cartoonbankru';
}
    $params['hostname'] = 'localhost';
    $params['username'] = 'z58365_cbru3';
    $params['password'] = 'greenbat';

$year = date("Y");
$month = date("m");

$this_date = getdate();

if (isset($_GET['m']) && is_numeric($_GET['m']))
{
	$start_timestamp = mktime(0, 0, 0, $_GET['m'], 1, $year);
	$end_timestamp = mktime(0, 0, 0, ($_GET['m']+1), 0, $year);
}
else
{
	//$start_timestamp = mktime(0, 0, 0, $month-12, 1, $year);
	$start_timestamp = mktime(0, 0, 0, 11, 1, 2010);
	$end_timestamp = mktime(0, 0, 0, ($month+1), 0, $year);
}

$sql = "SELECT COUNT( * ) as count, temp.id, temp.name, temp.user_id FROM ( SELECT b.id, b.name, b.user_id FROM  `wp_purchase_logs` AS l,  `wp_purchase_statuses` AS s,  `wp_cart_contents` AS c,  `wp_product_list` AS p,  `wp_download_status` AS st,  `wp_product_brands` AS b, `wp_users` AS u WHERE l.`processed` = s.`id`  AND l.id = c.purchaseid AND p.id = c.prodid AND st.purchid = c.purchaseid AND p.brand = b.id AND u.id = l.user_id AND l.user_id !=  '106' AND st.downloads !=  '5' AND date BETWEEN '$start_timestamp' AND '$end_timestamp' GROUP BY c.license ORDER BY b.name ) AS temp GROUP BY temp.id order by temp.name  
";

$result = $wpdb->get_results($sql,ARRAY_A);
if (!$result) {die('<br />'.$del_sql.'<br />Invalid select query: ' . mysql_error());}

echo "<div style='vertical-align:top;width:500px;'><div><h3>Продажи работ с ноября 2010</h3></div>";
echo "<div><table style='width:250px;background-color:#E8E8E8;'>";
foreach ($result as $row)
{
	echo "<tr class='alternate'>";
	//todo
	if ($current_user->wp_user_level ==10)
	{	
		echo "<td class='username' style='padding:4px;width:200px;'><a href='http://cartoonbank.ru/wp-admin/admin.php?page=wp-shopping-cart/display_artist_income.php&brand=".$row['id']."'>".$row['name']."</a></td><td class='posts' style='padding:4px;wisth:200px;text-align:right;'>".$row['count']."</td>";
	}
	else
		{	
		if ($current_user->id == $row['user_id']) 
			{
				//echo "<td class='username' style='padding:4px;width:200px;'><a href='http://cartoonbank.ru/wp-admin/admin.php?page=wp-shopping-cart/display_artist_income.php&brand=".$row['id']."'>".$row['name']."</a></td><td class='posts' style='padding:4px;wisth:200px;text-align:right;'>".$row['count'];
				echo "<td class='username' style='padding:4px;width:200px;'>".$row['name']."</td><td class='posts' style='padding:4px;wisth:200px;text-align:right;'>".$row['count'];
			}
			else
			{
				echo "<td class='username' style='padding:4px;width:200px;'>".$row['name']."</td><td class='posts' style='padding:4px;wisth:200px;text-align:right;'>".$row['count'];
			}

		echo "</td>";
	}
	//echo "<td class='username' style='padding:4px;width:200px;'><a href='".get_option('site_url')."/cb/wp-admin/admin.php?page=wp-shopping-cart/display_artist_income.php&brand=".$row['id']."'>".$row['name']."</a></td><td class='posts' style='padding:4px;wisth:200px;text-align:right;'>".$row['count']."</td>";

	echo "</tr>";
}
echo "</table></div>";

//pokazh($current_user->id,"id");

echo "<br></div>";

?>

