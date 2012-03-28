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

$sql = "SELECT COUNT( * ) as cntr, temp.id, temp.name, temp.user_id, atotal FROM ( SELECT b.count as atotal, b.id, b.name, b.user_id FROM  `wp_purchase_logs` AS l,  `wp_purchase_statuses` AS s,  `wp_cart_contents` AS c,  `wp_product_list` AS p,  `wp_download_status` AS st,  `wp_product_brands` AS b, `wp_users` AS u WHERE l.`processed` = s.`id`  AND l.id = c.purchaseid AND p.id = c.prodid AND st.purchid = c.purchaseid AND p.brand = b.id AND u.id = l.user_id AND l.user_id !=  '106' AND st.downloads !=  '5' AND date BETWEEN '$start_timestamp' AND '$end_timestamp' GROUP BY c.license ORDER BY b.name ) AS temp GROUP BY temp.id order by temp.name  
";
//pokazh($sql);
$result = $wpdb->get_results($sql,ARRAY_A);
if (!$result) {die('<br />'.$del_sql.'<br />Invalid select query: ' . mysql_error());}

echo "<div style='vertical-align:top;width:500px;'><div><h3>Продажи работ с ноября 2010</h3></div>";
echo "<div style='vertical-align:top;width:500px;'>На этой странице в реальном времени показываются все продажи по факту скачивания файлов. Количество ваших продаж на странице '<a href='http://cartoonbank.ru/wp-admin/admin.php?page=wp-shopping-cart/display_artist_income.php'>Заработано</a>' обычно меньше, так как указывается с задержкой на время прихода денег в бухгалтерию Картунбанка.</div>";
?> 
<script type="text/javascript" src="//ajax.googleapis.com/ajax/static/modules/gviz/1.0/chart.js"> {"dataSourceUrl":"//docs.google.com/spreadsheet/tq?key=0AtPperB2fdv5dDZDdEk2cEtNNkpWYXhfVWVHdFM0NUE&transpose=0&headers=0&merge=COLS&range=C3%3AC19%2CT3%3AT19&gid=0&pub=1","options":{"vAxes":[{"title":null,"minValue":null,"viewWindowMode":"pretty","viewWindow":{"min":null,"max":null},"maxValue":null},{"viewWindowMode":"pretty","viewWindow":{}}],"series":{"0":{"errorBars":{"errorType":"percent"},"color":"#a64d79"}},"booleanRole":"certainty","title":"\u041f\u0440\u043e\u0434\u0430\u0436\u0438, \u0448\u0442.","animation":{"duration":500},"legend":"none","vAxis":{"format":""},"theme":"maximized","hAxis":{"format":""},"isStacked":false,"width":600,"height":371},"state":{},"chartType":"ColumnChart","chartName":"Chart 5"} </script>
<?
echo "<div><table style='width:500px;background-color:#E8E8E8;'>";
echo "<tr><td style='text-align: center;'>автор</td><td style='text-align: center;'>продано штук</td><td style='text-align: center;'> % от работ автора</td><td style='text-align: center;'>всего работ автора</td></tr>";
foreach ($result as $row)
{
	echo "<tr class='alternate'>";
	//todo
	if ($current_user->wp_user_level >1)
	{	
		echo "<td class='username' style='padding:4px;width:200px;'><a href='http://cartoonbank.ru/wp-admin/admin.php?page=wp-shopping-cart/display_artist_income.php&brand=".$row['id']."'>".$row['name']."</a></td><td class='posts' style='padding:4px;text-align:right;'>".$row['cntr']."</td><td style='padding:4px;text-align:right;'>".round($row['cntr']*100/$row['atotal'],2)."%</td><td style='padding:4px;text-align:right;'>".$row['atotal']."</td>";
	}
	else
		{	
		if ($current_user->id == $row['user_id']) 
			{
				//echo "<td class='username' style='padding:4px;width:200px;'><a href='http://cartoonbank.ru/wp-admin/admin.php?page=wp-shopping-cart/display_artist_income.php&brand=".$row['id']."'>".$row['name']."</a></td><td class='posts' style='padding:4px;wisth:200px;text-align:right;'>".$row['count'];
				echo "<td class='username' style='padding:4px;width:200px;'>".$row['name']."</td><td class='posts' style='padding:4px;wisth:200px;text-align:right;'>".$row['cntr'];
			}
			else
			{
				echo "<td class='username' style='padding:4px;width:200px;'>".$row['name']."</td><td class='posts' style='padding:4px;wisth:200px;text-align:right;'>".$row['cntr'];
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

