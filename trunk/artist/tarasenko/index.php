<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title> Список работ Валерия Тарасенко  </title>
	<style>
	.t{
		background-color: #EFEFEF; color: #333; font-family: 'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif; font-size: 11px; padding: 2px; 
		}
	</style>

 </head>
 <body>
<h1>Список работ Валерия Тарасенко. Карикатуры. </h1>
<h3><a href="/?p=1">1000</a> <a href="/?p=2">2000</a> <a href="/?p=3">3000</a> <a href="/?p=4">4000</a> <a href="/?p=5">5000</a></h3>
<?
include("/home/www/cb3/ales/config.php");

$p=1000;
if ($_REQUEST['p']==2)
{$p=2000;}
if ($_REQUEST['p']==3)
{$p=3000;}
if ($_REQUEST['p']==4)
{$p=4000;}
if ($_REQUEST['p']==5)
{$p=5000;}


	$sql = "SELECT `id`,`name`,`description`,`additional_description` FROM `wp_product_list` where `active`=1 and `brand`= 16 order by id LIMIT ".($p-1000)." , 1000";
///pokazh($sql);
	$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
	mysql_set_charset('utf8',$link);

	$result = mysql_query($sql);
		if (!$result) {die('Invalid query: ' . mysql_error());}

	//$cartoons=mysql_fetch_array($result);
	
	//pokazh(count($cartoons));
		$c = $p-999;
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