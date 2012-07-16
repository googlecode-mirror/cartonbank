<?php 
include("/home/www/cb3/ales/config.php");
global $imagepath;

$integer = rand(1,10);
$fraction = rand(0,10)/10;
$rate = $integer + $fraction;

$rate = $integer;

$tagsarray = get_cartoon($rate);
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <script type="text/javascript" src="http://cartoonbank.ru/wp-includes/js/jquery/jquery.js"></script>
  <script type="text/javascript" src="http://cartoonbank.ru/wp-includes/js/jquery/jquery.cookie.js"></script>
  <script type="text/javascript" src="http://duel.cartoonbank.ru/scripts.js"></script>
  <link rel="stylesheet" type="text/css" href="http://duel.cartoonbank.ru/styles.css">
  <title> Дуэль карикатур. </title>
</head>

<body>

<div id='header'>
    <div>
	<span id="therating" class="therating" style="float:right;padding-right:20px;vertical-align:top;cursor:pointer;" onclick="jQuery('.rating').toggle().center();">Лучшие дуэлянты</span>
	<span id="hi"></span>
	<span id="headerrate" class="headerrate"></span>
	<!-- <span id="myrate"></span> -->
	</div>
</div>

<div style="padding:4px;width:100%;">
<div id="images" align="left">

    <?
	echo $tagsarray;
	?>
	
</div>
</div>

<div id="login" class="login"><form id="name" action="savename.php"><p class="sm">назовите себя</p><input type="text" name="username" style="font-size: 1.2em; padding:2px; font-color:#808080;text-align:center;"><p class="sm">и придумайте секретное слово</p><input type="password" name="secret" style="font-size: 1.2em; padding:2px; font-color:#808080;text-align:center;"><p><input class="btn" type="submit" value="сохранить" onclick="onsave();return false;"><input type="hidden" value=""></p></form></div>

<div class="footer"><span class="btn"><a href="#" onclick="jQuery.cookie('username',null);jQuery.cookie('cbrate',null);jQuery.cookie('cbclicks',null);addvote();loading();window.location.href = 'http://duel.cartoonbank.ru/index.php';">Обнулить всё</a></span> <span class="btn"><a href="#" onclick="loading();window.location.href = 'http://duel.cartoonbank.ru/index.php';">Следующая пара</a></span> <!-- <span class="btn"><a href="#" onclick="login();savename();">Назовите себя</a></span> -->
</div>

<div id="popup" class="popup" onclick="jQuery('#popup').css('visibility', 'hidden');loading();window.location.href = 'http://duel.cartoonbank.ru/index.php';"></div>

<div id="loading" class="loading"><p>Перезаряжаем</p></div>

<div id="maintitle" class="maintitle" onclick="login();" style="position:relative;"><div style="z-index:3000;font-size:2em;color:#3300ff;position:relative;left:420px;top:410px;" onclick="howtoplay();">Как играть?</div></div>

<div id="bang" class="bang"></div>

<div id="help" class="help"></div>

<div id="howtoplay" class="howtoplayhide" onclick="jQuery('#howtoplay').removeClass('howtoplay').addClass('howtoplayhide').center();"><h1>Как играть?</h1><p>Вам предлагается указать лучший из двух случайно выбранных рисунков.</p><p>Рисунки имеют начальный рейтинг в результате голосования на сайте <a href="http://cartoonbank.ru" target="_blank">Картунбанка</a></p> <p>Ваша цель угадать у какой картинки рейтинг выше.</p><h3>К барьеру!</h3></div>

<div id="rating" class="rating" onclick="jQuery(this).toggle();"></div>

</body>
</html>

<?
function get_cartoon($rate)
{
	global $mysql_hostname;
	global $mysql_user;
	global $mysql_password;
	global $mysql_database;
	global $original_tags;
	global $imagepath;

	$output= "";

	$sql ="SELECT id, image, votes_rate AS vr1 FROM `wp_product_list` WHERE votes_rate <= ( ".$rate." + 1 ) AND votes_rate >= ( ".$rate." - 1 ) AND active = 1 AND visible = 1 ORDER BY RAND() LIMIT 0 , 2";

	$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
	mysql_set_charset('utf8',$link);
	
	$result = mysql_query($sql);
		if (!$result) {die('Invalid query: ' . mysql_error());}
    $id =0;

    while ($row = mysql_fetch_assoc($result)){
        $output .= "<img id='".($id+1)."' class='i' src='http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/".$row['image']."' onclick='res(".($id+1).");' rate='".$row['vr1']."'><span id='image".($id+1)."' style='visibility:hidden;float:left;width:0px;'>".$row['id']."</span>";
        $id = $id+1;
    }
    
    mysql_close($link);
	return $output;
}
?>