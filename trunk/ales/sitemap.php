<?
include("/home/www/cb3/ales/config.php");
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);

//$timestamp = time();

	$sql = "SELECT id FROM `wp_product_list` WHERE `active`=1 and `visible`=1 and`approved`=1 order by id";
	$result = mysql_query($sql);
	if (!$result) {die('Invalid query: ' . mysql_error());}

		$out =  '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			
	while($row=mysql_fetch_array($result))
	{
		$out .=  '<url>';
		$out .=  '<loc>http://cartoonbank.ru/?page_id=29&amp;cartoonid='.$row['id'].'</loc>';
		//$out .=  '<lastmod>'.$timestamp.'<lastmod>';
		$out .=  '<changefreq>daily</changefreq>';
		$out .=  '<priority>0.8</priority>';
		$out .=  '</url>';
	}
		$out .=  '</urlset>';

fw($out);

echo '<a href="http://cartoonbank.ru/sitemap.xml">http://cartoonbank.ru/sitemap.xml</a>';

function fw($text)
{
	$fp = fopen('/home/www/cb3/sitemap.xml', 'w') or die('Could not open file!');
		fwrite($fp, $text) or die('Could not write to file');
	fclose($fp);
}
?>