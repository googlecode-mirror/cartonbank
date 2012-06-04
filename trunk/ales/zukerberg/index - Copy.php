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
  <title> Сравните два рисунка</title>
<script>
function login(){
    jQuery("body").append('<div class="modalOverlay">');
    jQuery('#login').center();
    jQuery('#login').css("visibility","visible");
    jQuery("#name input[name=username]").focus();
}
function loading(){
    jQuery("body").append('<div class="modalOverlay">');
    jQuery('#loading').center();
    jQuery('#loading').css("visibility","visible");
}
function res(num1){
    if(num1==1){
        num2=2;
		jQuery.cookie("best",jQuery.cookie("id1"));
		jQuery.cookie("worst",jQuery.cookie("id2"));
        }
        else{
            num2=1;
		jQuery.cookie("best",jQuery.cookie("id2"));
		jQuery.cookie("worst",jQuery.cookie("id1"));
        }
    

    if(jQuery.cookie('cbrate')==null){jQuery.cookie('cbrate','0');}
    if(jQuery.cookie('cbclicks')==null){jQuery.cookie('cbclicks','0');}
    var rate=parseInt(jQuery.cookie('cbrate'));
    var clicks=parseInt(jQuery.cookie('cbclicks'));
        
    if(jQuery('#'+num1).attr('rate')>jQuery('#'+num2).attr('rate')){
        jQuery("body").append('<div class="modalOverlay">');
        jQuery('#'+num1).css("border", "solid thick green");
        jQuery('#popup').css("background-color", "#C0FFC0");
        jQuery('#popup').center();
        jQuery('#popup').css("visibility","visible");
        jQuery('#popup').html('<p>Отлично!</p><p>'+jQuery('#'+num1).attr('rate')+' больше, чем ' + jQuery('#'+num2).attr('rate') + '</p><p>Угадано: '+(rate+1)+' из ' + (clicks+1) + '</p>');
        jQuery.cookie('cbrate',(rate+1));
        jQuery.cookie('cbclicks',(clicks+1));
        }
        else{
        jQuery("body").append('<div class="modalOverlay">');
        jQuery('#'+num1).css("border", "solid thick red");
        jQuery('#popup').css("background-color", "#FFC0C0");
        jQuery('#popup').center();
        jQuery('#popup').css("visibility","visible");
        jQuery('#popup').html('<p>Не угадали :(</p><p>'+jQuery('#'+num1).attr('rate')+' меньше, чем ' + jQuery('#'+num2).attr('rate') + '</p><p>Угадано: '+rate+' из ' + (clicks+1) + '</p>');
        jQuery.cookie('cbclicks',(clicks+1));
        }
    addvote();
}
jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", ((jQuery(window).height() - this.outerHeight()) / 2) + jQuery(window).scrollTop() + "px");
    this.css("left", ((jQuery(window).width() - this.outerWidth()) / 2) + jQuery(window).scrollLeft() + "px");
    return this;
}

function savename(){
    var rate = jQuery.cookie("cbrate")/Math.sqrt(Math.sqrt(parseInt(jQuery.cookie("cbclicks"))));
	var guessed = jQuery.cookie("cbrate");
	var views = jQuery.cookie("cbclicks");
    var name = jQuery("#name input[name=username]").val();
    var secret = jQuery("#name input[name=secret]").val();
    jQuery.cookie("username",name);
    jQuery.cookie("secret",secret);
    jQuery.post('http://cartoonbank.ru/ales/zukerberg/savename.php',{ name: name, rate: rate, secret: secret, guessed: guessed, views: views });
    jQuery('#login').css('visibility','hidden');
    if (jQuery.cookie("username")!=null){
        jQuery("#hi").html("Здравствуйте, <b>" + jQuery.cookie("username") + "</b>. ");
    }
}
function addvote(){
    var rate = jQuery.cookie("cbrate")/Math.sqrt(Math.sqrt(parseInt(jQuery.cookie("cbclicks"))));
	var guessed = jQuery.cookie("cbrate");
	var views = jQuery.cookie("cbclicks");
    var name = jQuery.cookie("username");
    var secret = jQuery.cookie("secret");
    var best = jQuery.cookie("best");
    var worst = jQuery.cookie("worst");
    jQuery.post('http://cartoonbank.ru/ales/zukerberg/savename.php',{ name: name, rate: rate, secret: secret, guessed: guessed, views: views, best: best, worst: worst });
    jQuery('#login').css('visibility','hidden');
    if (jQuery.cookie("username")!=null){
        jQuery("#hi").html("Здравствуйте, <b>" + jQuery.cookie("username") + "</b>. ");
    }
}
function onsave() {
	if (jQuery('#name input[name=username]').val().length == 0 || jQuery('#name input[name=secret]').val().length == 0)
	{
	alert('попробуйте ещё раз');
	jQuery.cookie("username",null);
	jQuery.cookie("secret",null)
	window.location.href = 'http://cartoonbank.ru/ales/zukerberg/';
	return false;
	}
	savename();
	jQuery('.modalOverlay').remove()
}

jQuery(document).ready(function() {
	if (jQuery.cookie("username")==null){login();}
    if (jQuery.cookie("cbrate")==null){var myrate=0;jQuery.cookie("cbrate","0");}else{var myrate = jQuery.cookie("cbrate");}
    if (jQuery.cookie("cbclicks")==null){var myclicks=0;jQuery.cookie("cbclicks","0");}else{var myclicks = jQuery.cookie("cbclicks");}
    //jQuery("#myrate").text(myrate);
    //jQuery("#myrate").append('. Попыток: '+myclicks+'.');
    if (parseInt(jQuery.cookie("cbrate"))>0){
        //jQuery("#myrate").append(' Ваш текущий рейт: '+(myrate/Math.sqrt(Math.sqrt(parseInt(jQuery.cookie("cbclicks"))))).toFixed(4)+'.');
        jQuery("#headerrate").html('рейт: '+(myrate/Math.sqrt(Math.sqrt(parseInt(jQuery.cookie("cbclicks"))))).toFixed(4)+'. Угадано ' + myrate + ' из ' + myclicks);
    }

	jQuery.cookie("id1",jQuery('#image1').text());
	jQuery.cookie("id2",jQuery('#image2').text());
    
    
    if (jQuery.cookie("username")!=null){
        jQuery("#hi").html("Здравствуйте, <b>" + jQuery.cookie("username") + "</b>. ");
    }
    
    jQuery('#rating').load('http://cartoonbank.ru/ales/zukerberg/getrating.php');
    

});

</script>

 </head>

 <body>
<style>
.t{
	background-clip: border-box; background-color: #EFEFEF; background-image: none; background-origin: padding-box; border-color: #999; border-style: solid; border-width: 1px; color: #333; display: block; float: left; font-family: 'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif; font-size: 11px; height: 18px; line-height: 18px; margin:1px; outline-color: #333; outline-style: none; outline-width: 0px; padding: 2px;
	}
.td{
	background-clip: border-box; border-color: #999; border-style: solid; border-width: 1px; color: #333; font-family: 'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif; font-size: 11px; height: 18px; line-height: 18px; margin:1px; outline-color: #333; outline-style: none; outline-width: 0px; padding: 2px;  text-align:center;
	}
.i{
    border:solid thin silver;margin:4px;vertical-align: top;cursor:pointer;float:left;
    }
.popup, .login, .loading{
    width:600px;
    height:350px;
    text-align: center;
    font-size: 2em;
    position: absolute;
    background-color: white;
    border:solid thin silver;
    padding:20px;
    visibility: hidden;
    z-index:1000;
    cursor: pointer;
}
.loading{
    height:100px;
    width:300px;
}
.modalOverlay {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0px;
    left: 0px;
    background-color: rgba(0,0,0,0.3);
}
.btn{
    padding:4px;
    border:solid thin silver;
    text-decoration: none;
    background-color:#F0F0F0;
    margin:5px;
    cursor: pointer;
}
a {
    text-decoration: none;
}
.footer{
    margin-top:10px;
    margin-bottom:10px;
	clear:both;
	float:left;
}
.rating{
margin-top:10px;
}
.headerrate{
    font-family: sans-serif;
    font-size: 2.6em;
    color: silver;
    vertical-align: top;
    margin-left: 10px;
}
.sm{
    font-size: 0.6em;
    color: silver;
}
</style>
<div id="popup" class="popup" onclick="jQuery('#popup').css('visibility', 'hidden');loading();window.location.href = 'http://cartoonbank.ru/ales/zukerberg/';"></div>

<div id="loading" class="loading"><p>Грузим новую пару</p></div>

<div id="login" class="login"><form id="name" action="savename.php"><p class="sm">назовите себя</p><input type="text" name="username" style="font-size: 1.2em; padding:2px; font-color:#808080;text-align:center;"><p class="sm">и придумайте секретное слово</p><input type="password" name="secret" style="font-size: 1.2em; padding:2px; font-color:#808080;text-align:center;"><p><input class="btn" type="submit" value="сохранить" onclick="onsave();return false;"><input type="hidden" value=""></p></form></div>


<div id='header'>
    <div><img src="http://cartoonbank.ru/wp-admin/images/cb-logo.gif"><span id="headerrate" class="headerrate"></span></div>
    <div style="width:514px;"><span id="hi"></span>
    Выберите лучший рисунок. Если ваше чувство юмора совпадёт с мнением большинства, то вы получите 1 балл к карме. <span id="myrate"></span>
    </div>
</div>

<div style="padding:4px;width:100%;">
<div align="left">
	<div>

    <?
	echo $tagsarray;
	?>
	
	</div>
</div>
</div>

<div class="footer"><span class="btn"><a href="#" onclick="jQuery.cookie('cbrate',null);jQuery.cookie('cbclicks',null);addvote();loading();window.location.href = 'http://cartoonbank.ru/ales/zukerberg/';">Обнулить счётчик</a></span> <span class="btn"><a href="#" onclick="loading();window.location.href = 'http://cartoonbank.ru/ales/zukerberg/';">Следующая пара</a></span> <span class="btn"><a href="#" onclick="login();">Назовите себя</a></span>
<div id="rating" class="rating"></div>
</div>



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

	$sql = "(SELECT id, image, votes_rate AS vr1 FROM  `wp_product_list` WHERE votes_rate <= ( ".$rate." + 1 ) AND votes_rate >= ( ".$rate." - 1 ) AND active = 1 AND visible = 1 ORDER BY RAND() LIMIT 0 , 1) UNION (SELECT id, image, votes_rate AS vr2 FROM  `wp_product_list` WHERE votes_rate <= ( ".$rate." + 1 ) AND votes_rate >= ( ".$rate." - 1 ) AND votes_rate !=".$rate." AND votes_rate <11 AND votes_rate > 1 AND active = 1 AND visible = 1 ORDER BY RAND() LIMIT 0 , 1)";

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
    //$output .= "<script>function res(num1){if(num1==1){num2=2}else{num2=1};if(jQuery('#'+num1).attr('rate')>jQuery('#'+num2).attr('rate')){alert('правильно: '+jQuery('#'+num1).attr('rate')+' больше, чем ' + jQuery('#'+num2).attr('rate'))};}</script>";
	return $output;
}
?>