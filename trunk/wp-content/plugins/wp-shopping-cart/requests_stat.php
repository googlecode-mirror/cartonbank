<?
//requests_stat.php

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

// Popular requests

	$sql = "SELECT term, count(id) as cnt FROM `search_terms` group by term order by cnt desc, term LIMIT 150";

	$result = $wpdb->get_results($sql,ARRAY_A);
	if (!$result) {die('<br />'.$del_sql.'<br />Invalid select query: ' . mysql_error());}

	echo "<div style='vertical-align:top;'><div><h3>Статистика поисковых запросов</h3></div>";
	echo "<a href='#new'>Ниже находится таблица с новыми запросами</a>";

	echo "<div style='padding:4px;font-size:0.8em;margin-bottom:6px;'>Суммарная статистика по поисковым словам учитывает <br>1) слова набранные в поисковой форме; 2) переходы по тэгам, указанным в описании.</div>";

	echo "<div style='vertical-align:top;float:left;'><div><a name='pop'><h3>150 популярных запросов</h3></a></div>";

		echo "<div style='width:1000px;clear:all;'>";

		$counter = 1;

		foreach ($result as $row)
		{
			echo "<div style='padding:2px;margin:2px;background-color:#D0FE7A;float:left;'>";
			//todo
				echo "<span style='padding-right:2px;color:silver;'>$counter</span>";		
				echo "<span class='username' style='padding:4px;'><a href='http://cartoonbank.ru/?page_id=29&cs=".$row['term']."'>".urldecode($row['term'])."</a></span><span class='posts' style='padding:4px;text-align:right;'>".$row['cnt']."</span>";

			echo "</div>";

			$counter = $counter + 1;
		}
		echo "</div>";
	echo "</div>";

echo "<div style='clear:all;'></div>";

// New requests

	$sql = "SELECT term, datetime, header FROM `search_terms` group by term order by datetime desc LIMIT 100";

	$result = $wpdb->get_results($sql,ARRAY_A);
	if (!$result) {die('<br />'.$del_sql.'<br />Invalid select query: ' . mysql_error());}

	echo "<div style='vertical-align:top;float:left;'><div><a name='new'><h3>100 последних запросов</h3></a></div>";
	echo "<div style='padding:4px;font-size:0.8em;margin-bottom:6px;'>Статистика содержит поисковое слово (слова), дату поиска, айпи-адрес, заголовок браузера.</div>";
	echo "<div><table style='background-color:#E8E8E8;'>";

	$counter = 1;

	foreach ($result as $row)
	{
		echo "<tr class='alternate'>";
		//todo
			echo "<td style='text-align:right; padding-right:2px;color:silver;'>$counter</td>";		
			echo "<td class='username' style='padding:4px;'><a href='http://cartoonbank.ru/?page_id=29&cs=".$row['term']."' style='padding:2px;margin:2px;background-color:#D0FE7A;float:left;'>".urldecode($row['term'])."</a></td><td class='posts' style='padding:4px;width:100px;'>".$row['datetime']."</td>";
			echo "<td class='posts' style='padding:4px;'>".$row['header']."</td>";

		echo "</tr>";

		$counter = $counter + 1;
	}
	echo "</table></div>";

	//pokazh($current_user->id,"id");

	echo "<br></div>";


?>