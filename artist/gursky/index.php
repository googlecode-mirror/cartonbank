<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title> Список работ Аркадия Гурского </title>
	<style>
	.t{
		background-color: #EFEFEF; color: #333; font-family: 'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif; font-size: 11px; padding: 2px; 
		}
	</style>

 </head>
 <body>
<h1>Список работ Аркадия Гурского</h1>
<?
include("/home/www/cb3/ales/config.php");

	$sql = "SELECT `id`,`name`,`description`,`additional_description` FROM `wp_product_list` where `active`=1 and `brand`= 23 order by id";
///pokazh($sql);
	$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
	mysql_set_charset('utf8',$link);

	$result = mysql_query($sql);
		if (!$result) {die('Invalid query: ' . mysql_error());}

	//$cartoons=mysql_fetch_array($result);
	
	//pokazh(count($cartoons));
		$c = 1;
		$out ='';	
		$out = $out . "<table>";
			$out .= "<tr>";
			$out = $out . "<td class='t'>№ п/п</td>";
			$out = $out . "<td class='t'>№ в Банке</td>";
			$out = $out . "<td class='t'>Имя</td>";
			$out = $out . "<td class='t'>Описание</td>";
			$out = $out . "<td class='t'>Ключевые слова</td>";
			$out = $out . "</tr>";

	while($cartoon=mysql_fetch_array($result))
		{
			///pokazh($cartoon);
			$out .= "<tr>";
			$out = $out . "<td class='t'>".$c."</td>";
			$out = $out . "<td class='t'><a href='http://cartoonbank.ru/?page_id=29&cartoonid=".$cartoon[id]."' target='_blank'>".$cartoon[id]."</a></td>";
			$out = $out . "<td class='t'>".stripslashes($cartoon[name])."</td>";
			$out = $out . "<td class='t'>".stripslashes($cartoon[description])."</td>";
			$out = $out . "<td class='t'>".stripslashes($cartoon[additional_description])."</td>";
			$out = $out . "</tr>";
			$c++;
		}

		$out = $out . "</table>";

	echo $out;


?>

</body>