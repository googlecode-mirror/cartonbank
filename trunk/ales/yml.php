<?
include("/home/www/cb3/ales/config.php");
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);


	$sql = "SELECT l.id as id, l.name, l.description, l.image, l.category as cat, b.name AS brand, c.name AS category FROM  `wp_product_list` AS l, wp_product_brands AS b, wp_product_categories AS c WHERE c.id = l.category AND b.id = l.brand AND l.active = 1 AND l.visible = 1 AND l.approved = 1 ORDER BY l.votes_rate desc limit 500";

	$res = mysql_query($sql);
	if (!$res) {die('Invalid query: ' . mysql_error());}

	$out  = '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE yml_catalog SYSTEM "shops.dtd"><yml_catalog date="2012-12-27 15:05">';
	$out .= '<shop>';
	$out .= '<name>Картунбанк. Карикатуры</name>';
	$out .= '<company>Cartoonbank.ru</company>';
	$out .= '<url>http://cartoonbank.ru/</url>';
	$out .= '<currencies><currency id="RUR" rate="1" plus="0"/></currencies>';
	$out .= '<categories>';
	$out .= '<category id="4">Карикатура</category>';
	$out .= '<category id="5">Cartoon</category>';
	$out .= '<category id="6">Artoon</category>';
	$out .= '<category id="13">Коллаж</category>';
	$out .= '<category id="14">Шарж</category>';
	$out .= '<category id="15">Стрип</category>';
	$out .= '<category id="11">Разное</category>';
	$out .= '</categories>';

		$out .=  '<offers>';
			    
	while($row=mysql_fetch_array($res))
	{
		$out .=  '<offer id="'.$row['id'].'" type="artist.title" available="true">';
		$out .=  '<url>http://cartoonbank.ru/?page_id=29&amp;cartoonid='.$row['id'].'</url>';
		$out .=  '<price>250</price>';
		$out .=  '<currencyId>RUR</currencyId>';
		$out .=  '<categoryId type="Own">'.$row['cat'].'</categoryId>';
		$out .=  '<picture>http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/'.rawurlencode($row['image']).'</picture>';
		$out .=  '<delivery>true</delivery>';
		$out .=  '<artist>'.$row['brand'].'</artist>';
		$out .=  '<title>'.$row['name'].'</title>';
		$out .=  '<year>2013</year>';
		$out .=  '<media>карикатура</media>';
		$out .=  '<description>'.$row['description'].'</description>';
		$out .=  '</offer>';
	}
		$out .=  '</offers></shop></yml_catalog>';
echo $out;
//fw($out);

//echo '<a href="http://cartoonbank.ru/sitemap.xml">http://cartoonbank.ru/sitemap.xml</a>';

function fw($text)
{
	$fp = fopen('/home/www/cb3/yml.xml', 'w') or die('Could not open file!');
		fwrite($fp, $text) or die('Could not write to file');
	fclose($fp);
}
?>