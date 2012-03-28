<?
require_once('config.php');
global $wpdb;

$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);

if ($_REQUEST['b'])
{
	$brand=trim($_REQUEST['b']);
}
else
{
	$brand=1;
}


if ($_REQUEST['rate'] && $_REQUEST['rate']=1)
{
	$sql="SELECT id
	FROM  `wp_product_list` 
	WHERE brand = ".$brand."
	AND category =666
	AND approved =1";

	$result = mysql_query($sql);

//pokazh ($result);
	
	if (!$result) {die('Invalid query: ' . mysql_error());}
	while($row=mysql_fetch_array($result))
	{
		$file = fopen('http://cartoonbank.ru/wp-content/plugins/five-star-rating/fsr-ajax-stars.php?p='.$row['id'].'&fsr_stars=2', 'r');
		fclose($file);
	
		$sql1="INSERT ignore INTO  `al_editors_votes` (`image_id`) VALUES (".$row['id'].")";
		//pokazh ($sql);
		mysql_query($sql1);
	
	}
}

// Execute
if ($_REQUEST['id'] && $_REQUEST['category'])
{
	$sql="update wp_fsr_post set votes = ".$_REQUEST['votes'].", points=".$_REQUEST['points']." where id=".$_REQUEST['id'];
	//pokazh ($sql);
	$result = mysql_query($sql);
	if (!$result) {die('Invalid query: ' . mysql_error());}
	$sql="update  wp_product_list set category = ".$_REQUEST['category']." where id=".$_REQUEST['id'];
	//pokazh ($sql);
	$result = mysql_query($sql);
	if (!$result) {die('Invalid query: ' . mysql_error());}
	$sql="update  wp_item_category_associations set category_id = ".$_REQUEST['category']." where product_id=".$_REQUEST['id'];
	//pokazh ($sql);
	$result = mysql_query($sql);
	if (!$result) {die('Invalid query: ' . mysql_error());}
	header("Location: http://cartoonbank.ru/ales/666remove.php?b=".$brand);
}





$sql = "
SELECT AEV.image_id, AEV.up, AEV.down, (
AEV.up *5 + AEV.down
) / ( AEV.up + AEV.down ) AS rateModer, WFP.votes, WFP.points, (
WFP.points / WFP.votes
) AS rateVisitor, (
AEV.up *5 + AEV.down + WFP.points
) / ( AEV.up + AEV.down + WFP.votes ) AS RateTotal,
P.image
FROM  `al_editors_votes` AS AEV,  `wp_fsr_post` AS WFP,  `wp_product_list` AS P
WHERE AEV.image_id = WFP.id
AND P.id = WFP.id
AND P.category = 666
AND P.approved = '1'
AND P.brand =".$brand."
ORDER BY rateVisitor DESC
LIMIT 1
";
//pokazh($sql);
$result = mysql_query($sql);

if (!$result) {
                die('Invalid query: ' . mysql_error());
            }
echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>Смена Рабочего стола</title><style>a, a:hover{padding:2px;}a:hover{background-color:#99CCCC;}</style></head><body>';
//echo '<br><a href="http://cartoonbank.ru/ales/666remove.php">СЛЕДУЮЩАЯ>>>>></a><br><br><br><br>';
while($row=mysql_fetch_array($result))
{
	$votes = $row['up'] + $row['down'] + $row['votes'];
	$points = $row['up']*5 + $row['down'] + $row['points'];
	$rate = $row['RateTotal'];

	echo '<img src="http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/'.$row['image'].'"><br>';
	echo '# '.$row['image_id'].': новый рейтинг: '.$rate.'<br>';
	echo ' Выбрать новую категорию: <br>';
	echo ' <a href="?id='.$row['image_id'].'&category=4&votes='.$votes.'&points='.$points.'&b='.$brand.'">Карикатура</a>: <br>';
	echo ' <a href="?id='.$row['image_id'].'&category=5&votes='.$votes.'&points='.$points.'&b='.$brand.'">Картун</a>: <br>';
	echo ' <a href="?id='.$row['image_id'].'&category=11&votes='.$votes.'&points='.$points.'&b='.$brand.'">Разное</a>: <br>';
	echo ' <a href="?id='.$row['image_id'].'&category=14&votes='.$votes.'&points='.$points.'&b='.$brand.'">Шарж</a>: <br>';
	echo ' <a href="?id='.$row['image_id'].'&category=6&votes='.$votes.'&points='.$points.'&b='.$brand.'">Артун</a>: <br>';
	echo ' <a href="?id='.$row['image_id'].'&category=15&votes='.$votes.'&points='.$points.'&b='.$brand.'">Стрип</a>: <br>';
	echo ' <a href="?id='.$row['image_id'].'&category=13&votes='.$votes.'&points='.$points.'&b='.$brand.'">Коллаж</a>: <br><br><br>';
}
?>

1	<a href="?b=1">Шилов Вячеслав</a>
2	<a href="?b=2">Лемехов Сергей</a>
3	<a href="?b=3">Александров Василий</a>
4	<a href="?b=4">Смагин Максим</a>
5	<a href="?b=5">Кийко Игорь</a>
6	<a href="?b=6">Богорад Виктор</a>
7	<a href="?b=7">Мельник Леонид</a>
8	<a href="?b=8">Алёшин Игорь</a>
9	<a href="?b=9">Попов Андрей</a>
11	<a href="?b=11">Сергеев Александр</a>
12	<a href="?b=12">Осипов Евгений</a>
14	<a href="?b=14">Максименко Ирина</a>
15	<a href="?b=15">Камаев Владимир</a>
16	<a href="?b=16">Тарасенко Валерий</a>
17	<a href="?b=17">Эренбург Борис</a>
18	<a href="?b=18">Дергачёв Олег</a>
19	<a href="?b=19">Майстренко Дмитрий</a>
20	<a href="?b=20">Копельницкий Игорь</a>
21	<a href="?b=21">Ёлкин Сергей</a>
22	<a href="?b=22">Бондаренко Дмитрий</a>
23	<a href="?b=23">Гурский Аркадий</a>
24	<a href="?b=24">Бибишев Вячеслав</a>
25	<a href="?b=25">Иорш Алексей</a>
26	<a href="?b=26">Анчуков Иван</a>
27	<a href="?b=27">Дубовский Александр</a>
29	<a href="?b=29">Степанов Владимир</a>
30	<a href="?b=30">Дубинин Валентин</a>
31	<a href="?b=31">Попов Александр</a>
32	<a href="?b=32">Кононов Дмитрий</a>
33	<a href="?b=33">Новосёлов Валерий</a>	 	
34	<a href="?b=34">Шмидт Александр</a>	 	
35	<a href="?b=35">Цыганков Борис</a>	 	
36	<a href="?b=36">Иванов Владимир</a>	 	
37	<a href="?b=37">Соколов Сергей</a>	 	
38	<a href="?b=38">Валиахметов Марат</a>	
39	<a href="?b=39">Кокарев Сергей</a>	 	
40	<a href="?b=40">Батов Антон</a>	 	
41	<a href="?b=41">Егоров Александр</a>
42	<a href="?b=42">Лукьянченко Игорь</a>
43	<a href="?b=43">Никитин Игорь</a>	 
44	<a href="?b=44">Подвицкий Виталий</a>


<?




echo '</body></html>';


//http://cartoonbank.ru/wp-content/plugins/five-star-rating/fsr-ajax-stars.php?id=FSR_form_11696&value=3



?>