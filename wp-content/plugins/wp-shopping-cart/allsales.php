<?php
$abspath = 'z:/home/localhost/www/';
$abspath_1 = ROOTDIR;
$abspath_2 = ROOTDIR;

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


//"height":320
//"width":700
//"tooltip":{"trigger":"yes"}
/*

"domainAxis":{"direction":1},"legend":"none","annotations":{"domain":{}},"tooltip":{"trigger":"yes"}},"state":{},"view":{},"isDefaultVisualization":true,"chartType":"ComboChart","chartName":"Chart 5"} </script>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/static/modules/gviz/1.0/chart.js"> {"dataSourceUrl":"//docs.google.com/spreadsheet/tq?key=0AtPperB2fdv5dDZDdEk2cEtNNkpWYXhfVWVHdFM0NUE&transpose=0&headers=0&merge=COLS&range=C3%3AC39%2CT3%3AT39%2CAC3%3AAC39&gid=0&pub=1","options":{"series":[{"type":"bars"}],"animation":{"duration":500},"backgroundColor":{"fill":"#efefef"},"width":700,"theme":"maximized","type":"line","hAxis":{"title":"","useFormatFromData":true,"minValue":null,"viewWindow":{"max":null,"min":null},"maxValue":null},"vAxes":[{"useFormatFromData":true,"minValue":null,"viewWindow":{"max":null,"min":null},"maxValue":null},{"useFormatFromData":true,"minValue":null,"viewWindow":{"max":null,"min":null},"maxValue":null}],"booleanRole":"certainty","title":"\u041f\u0440\u043e\u0434\u0430\u0436\u0438 \u043f\u043e \u043c\u0435\u0441\u044f\u0446\u0430\u043c, \u0448\u0442.","height":320,"interpolateNulls":false,"domainAxis":{"direction":1},"legend":"none","annotations":{"domain":{}},"tooltip":{"trigger":"yes"}},"state":{},"view":{},"isDefaultVisualization":true,"chartType":"ComboChart","chartName":"Chart 5"} </script>*/
?> 


<script type="text/javascript" src="//ajax.googleapis.com/ajax/static/modules/gviz/1.0/chart.js"> {"dataSourceUrl":"//docs.google.com/spreadsheet/tq?key=0AtPperB2fdv5dDZDdEk2cEtNNkpWYXhfVWVHdFM0NUE&transpose=0&headers=0&merge=COLS&range=C3%3AC40%2CT3%3AT40%2CAC3%3AAC40&gid=0&pub=1","options":{"series":[{"type":"bars"}],"animation":{"duration":500},"backgroundColor":{"fill":"#efefef"},"width":700,"theme":"maximized","type":"line","hAxis":{"title":"","useFormatFromData":true,"minValue":null,"viewWindow":{"max":null,"min":null},"maxValue":null},"vAxes":[{"useFormatFromData":true,"minValue":null,"viewWindow":{"max":null,"min":null},"maxValue":null},{"useFormatFromData":true,"minValue":null,"viewWindow":{"max":null,"min":null},"maxValue":null}],"booleanRole":"certainty","title":"\u041f\u0440\u043e\u0434\u0430\u0436\u0438 \u043f\u043e \u043c\u0435\u0441\u044f\u0446\u0430\u043c, \u0448\u0442.","height":320,"interpolateNulls":false,"domainAxis":{"direction":1},"legend":"none","annotations":{"domain":{}},"tooltip":{"trigger":"yes"}},"state":{},"view":{},"isDefaultVisualization":true,"chartType":"ComboChart","chartName":"Chart 5"} </script>



<?
echo "<div><table style='width:500px;background-color:#E8E8E8;'>";
echo "<tr><td style='text-align: center;'>автор</td><td style='text-align: center;'>продано штук</td><td style='text-align: center;'> % от работ автора</td><td style='text-align: center;'>всего работ автора</td></tr>";
foreach ($result as $row)
{
	echo "<tr class='alternate'>";
	//todo
	if ($current_user->wp_user_level >1)
	{	
		echo "<td class='username' style='padding:4px;width:200px;'><a href='http://cartoonbank.ru/wp-admin/admin.php?page=wp-shopping-cart/display_artist_income.php&brand=".$row['id']."'>".$row['name']."</a></td><td class='posts' style='padding:4px;text-align:right;'><a href='http://cartoonbank.ru/?page_id=1299&br=".$row['id']."' target='_blank'>".$row['cntr']."</a></td><td style='padding:4px;text-align:right;'>".round($row['cntr']*100/$row['atotal'],2)."%</td><td style='padding:4px;text-align:right;'>".$row['atotal']."</td>";
	}
	else
		{	
		if ($current_user->id == $row['user_id']) 
			{
				echo "<td class='username' style='padding:4px;width:200px;'>".$row['name']."</td><td class='posts' style='padding:4px;wisth:200px;text-align:right;'>".$row['cntr'];
			}
			else
			{
				echo "<td class='username' style='padding:4px;width:200px;'>".$row['name']."</td><td class='posts' style='padding:4px;wisth:200px;text-align:right;'>".$row['cntr'];
			}

		echo "</td>";
	}

	echo "</tr>";
}
echo "</table></div>";

//pokazh($current_user->id,"id");

echo "<br></div>";

?>

