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
function maintitle() {
    jQuery("body").append('<div class="modalOverlay">');
    jQuery('.modalOverlay').css("background-color","black");
    jQuery('#maintitle').css("height","466px");
    jQuery('#maintitle').css("width","600px");
    jQuery('#maintitle').center();
    jQuery('#maintitle').css("visibility","visible");
}
function login(){
    jQuery("body").append('<div class="modalOverlay">');
	jQuery('#maintitle').css("visibility","hidden");
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
    
		var image1rate = parseFloat(jQuery('#'+num1).attr('rate'));
		var image2rate = parseFloat(jQuery('#'+num2).attr('rate'));

		//alert ("clicked: "+ num1 + " image1rate: "+image1rate+ " image2rate: " + image2rate);

    if (image1rate>image2rate) {
		bang();
        jQuery("body").append('<div class="modalOverlay">');
        jQuery('#'+num1).css("border", "solid thick green");
        jQuery('#popup').css("background-color", "#C0FFC0");
		jQuery('#popup').css("background-image", "url('img/popal.jpg')");
        jQuery('#popup').html('<p>Попал!</p><p>'+jQuery('#'+num1).attr('rate')+' больше, чем ' + jQuery('#'+num2).attr('rate') + '</p><p>Попаданий: '+(rate+1)+' из ' + (clicks+1) + '</p>');
        jQuery('#popup').center();
		jQuery('#popup').css("display","none");
        jQuery('#popup').css("visibility","visible");
        jQuery('#popup').fadeIn('slow');
		jQuery.cookie('cbrate',(rate+1));
        jQuery.cookie('cbclicks',(clicks+1));
        }
        else if (image1rate<image2rate) {
		bang();
        jQuery("body").append('<div class="modalOverlay">');
        jQuery('#'+num1).css("border", "solid thick red");
		jQuery('#popup').css("background-image", "url('img/mimo.jpg')");
        jQuery('#popup').css("background-color", "#FFC0C0");
        jQuery('#popup').html('<p>Мимо!(:</p><p>'+jQuery('#'+num1).attr('rate')+' меньше, чем ' + jQuery('#'+num2).attr('rate') + '</p><p>Попаданий: '+rate+' из ' + (clicks+1) + '</p>');
        jQuery('#popup').center();
		jQuery('#popup').css("display","none");
        jQuery('#popup').css("visibility","visible");
        jQuery('#popup').fadeIn('slow');
		jQuery.cookie('cbclicks',(clicks+1));
        }
		else{
		bang();
        jQuery("body").append('<div class="modalOverlay">');
        jQuery('#'+num1).css("border", "solid thick blue");
		jQuery('#popup').css("background-image", "url('img/mimo.jpg')");
        jQuery('#popup').css("background-color", "#FFC0C0");
        jQuery('#popup').html('<p>Осечка!</p><p>'+jQuery('#'+num1).attr('rate')+' равно ' + jQuery('#'+num2).attr('rate') + '</p><p>Попаданий: '+rate+' из ' + (clicks+1) + '</p>');
        jQuery('#popup').center();
		jQuery('#popup').css("display","none");
        jQuery('#popup').css("visibility","visible");
        jQuery('#popup').fadeIn('slow');
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
        jQuery("#hi").html("Дуэлянт: " + jQuery.cookie("username") + "");
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
        jQuery("#hi").html("Дуэлянт: " + jQuery.cookie("username") + "");
    }
}
function onsave() {
	if (jQuery('#name input[name=username]').val().length == 0 || jQuery('#name input[name=secret]').val().length == 0)
	{
	alert('попробуйте ещё раз');
	jQuery.cookie("username",null);
	jQuery.cookie("secret",null)
	window.location.href = 'http://cartoonbank.ru/ales/zukerberg/index.php';
	return false;
	}
	savename();
	jQuery('.modalOverlay').remove()
}
function howtoplay() {
	jQuery('#howtoplay').removeClass('howtoplayhide')
	jQuery('#howtoplay').addClass('howtoplay');
	//css("height","600px");
	//jQuery('#howtoplay').css("width","600px");
    //jQuery("#howtoplay").css("visibility","visible");
    //jQuery("#howtoplay").css("display","block");
	//jQuery("#howtoplay").show();
	jQuery('#howtoplay').center();
}
function bang() {

	jQuery('#bang').css("height","244px");
	jQuery('#bang').css("width","320px");
	jQuery('#bang').center();
	jQuery('#bang').css("visibility","visible");
	jQuery('#bang').fadeOut(1000);
}

jQuery(document).ready(function() {
	if (jQuery.cookie("username")==null){maintitle();}
    if (jQuery.cookie("cbrate")==null){var myrate=0;jQuery.cookie("cbrate","0");}else{var myrate = jQuery.cookie("cbrate");}
    if (jQuery.cookie("cbclicks")==null){var myclicks=0;jQuery.cookie("cbclicks","0");}else{var myclicks = jQuery.cookie("cbclicks");}
    if (parseInt(jQuery.cookie("cbrate"))>0){
        jQuery("#headerrate").html('рейт: '+(myrate/Math.sqrt(Math.sqrt(parseInt(jQuery.cookie("cbclicks"))))).toFixed(4)+'. Попаданий ' + myrate + ' из ' + myclicks);
    }

	jQuery.cookie("id1",jQuery('#image1').text());
	jQuery.cookie("id2",jQuery('#image2').text());
    
    
    if (jQuery.cookie("username")!=null){
        jQuery("#hi").html("Дуэлянт: " + jQuery.cookie("username") + "");
    }
    
    jQuery('#rating').load('http://cartoonbank.ru/ales/zukerberg/getrating.php');




var sw = jQuery('body').innerWidth();//screen width
var scale = sw/1200;
var i1w = 600;
var i2w = 600;
var img1 = new Image();
var img2 = new Image();

function CreateDelegate(contextObject, delegateMethod)
{
    return function()
    {
        return delegateMethod.apply(contextObject, arguments);
    }
}

function img1_onload()
{
    //alert(this.width + " by " + this.height);
	i1w = this.width;

	if (scale<1) {
		jQuery("#1").width(i1w*scale-20);
		jQuery("#2").width(i2w*scale-20);
	}

}
img1.onload = CreateDelegate(img1, img1_onload);
img1.src = jQuery('#1').attr('src');

function img2_onload()
{
    //alert(this.width + " by " + this.height);
	i2w = this.width;

	if (scale<1) {
		jQuery("#1").width(i1w*scale-20);
		jQuery("#2").width(i2w*scale-20);
	}

}

img2.onload = CreateDelegate(img2, img2_onload);
img2.src = jQuery('#2').attr('src');


});

</script>

<script type="text/javascript" charset="utf-8">
var imgSizer = {
	Config : {
		imgCache : []
		,spacer : "/path/to/your/spacer.gif"
	}

	,collate : function(aScope) {
		var isOldIE = (document.all && !window.opera && !window.XDomainRequest) ? 1 : 0;
		if (isOldIE && document.getElementsByTagName) {
			var c = imgSizer;
			var imgCache = c.Config.imgCache;

			var images = (aScope && aScope.length) ? aScope : document.getElementsByTagName("img");
			for (var i = 0; i < images.length; i++) {
				images[i].origWidth = images[i].offsetWidth;
				images[i].origHeight = images[i].offsetHeight;

				imgCache.push(images[i]);
				c.ieAlpha(images[i]);
				images[i].style.width = "100%";
			}

			if (imgCache.length) {
				c.resize(function() {
					for (var i = 0; i < imgCache.length; i++) {
						var ratio = (imgCache[i].offsetWidth / imgCache[i].origWidth);
						imgCache[i].style.height = (imgCache[i].origHeight * ratio) + "px";
					}
				});
			}
		}
	}

	,ieAlpha : function(img) {
		var c = imgSizer;
		if (img.oldSrc) {
			img.src = img.oldSrc;
		}
		var src = img.src;
		img.style.width = img.offsetWidth + "px";
		img.style.height = img.offsetHeight + "px";
		img.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + src + "', sizingMethod='scale')"
		img.oldSrc = src;
		img.src = c.Config.spacer;
	}

	// Ghettomodified version of Simon Willison's addLoadEvent() -- http://simonwillison.net/2004/May/26/addLoadEvent/
	,resize : function(func) {
		var oldonresize = window.onresize;
		if (typeof window.onresize != 'function') {
			window.onresize = func;
		} else {
			window.onresize = function() {
				if (oldonresize) {
					oldonresize();
				}
				func();
			}
		}
	}
}

addLoadEvent(function() {
	imgSizer.collate();
});

function addLoadEvent(func) {
	var oldonload = window.onload;
	if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		window.onload = function() {
			if (oldonload) {
				oldonload();
			}
			func();
		}
	}
}
</script>

<style>
img {
	max-width: 100%;
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
.popup{
    height:360px;
    width:550px;
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
    background-color: rgba(0,0,0,0.6);
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
.headerrate, #hi{
    font-family: sans-serif;
    font-size: 1.8em;
    color: silver;
    vertical-align: top;
    margin-left: 10px;
}
.sm{
    font-size: 0.6em;
    color: silver;
}
.maintitle{
	background-image:url('img/cartoonduel.jpg');
	border:solid 10px #616161;
	height:0px;
	width:0px;
	z-index:1000;
	visibility:hidden;
	cursor:pointer;
}
.bang{
	visibility:hidden;
	height:0px;
	width:0px;
	background-image:url('img/babah.png');
	z-index:2000;
}
body{
	background-color:#9e9e9e;
}
.howtoplay{
	width:600px;
	height:600px;
	background-color:white;
	padding:20px;
	font-size:1.8em;
	color:#606060;
	font-family: serif;
	display:block;
	visibility:visible;
	z-index:1500;
	position:relative;
}
.howtoplayhide{
	width:0px;
	height:0px;
	display:none;
	visibility:hidden;
	position:relative;
	z-index:-10;
}

</style>
 </head>

 <body>

<div id="popup" class="popup" onclick="jQuery('#popup').css('visibility', 'hidden');loading();window.location.href = 'http://cartoonbank.ru/ales/zukerberg/index.php';"></div>

<div id="loading" class="loading"><p>Перезаряжаем</p></div>

<div id="maintitle" class="maintitle" onclick="login();" style="position:relative;"><div style="z-index:3000;font-size:2em;color:#3300ff;position:relative;left:420px;top:420px;" onclick="howtoplay();">Как играть?</div></div>

<div id="bang" class="bang"></div>

<div id="help" class="help"></div>

<div id="howtoplay" class="howtoplayhide" onclick="jQuery('#howtoplay').removeClass('howtoplay').addClass('howtoplayhide').center();"><h1>Как играть?</h1><p>Вам предлагается указать лучший из двух случайно выбранных рисунков.</p><p>Рисунки имеют начальный рейтинг в результате голосования на сайте <a href="http://cartoonbank.ru" target="_blank">Картунбанка</a></p> <p>Ваша цель угадать у какой картинки рейтинг выше.</p><h3>К барьеру!</h3></div>



<div id="login" class="login"><form id="name" action="savename.php"><p class="sm">назовите себя</p><input type="text" name="username" style="font-size: 1.2em; padding:2px; font-color:#808080;text-align:center;"><p class="sm">и придумайте секретное слово</p><input type="password" name="secret" style="font-size: 1.2em; padding:2px; font-color:#808080;text-align:center;"><p><input class="btn" type="submit" value="сохранить" onclick="onsave();return false;"><input type="hidden" value=""></p></form></div>


<div id='header'>
    <div><span id="hi"></span><hr><!--<img src="http://cartoonbank.ru/wp-admin/images/cb-logo.gif">--><span id="headerrate" class="headerrate"></span></div>
    <hr><span id="myrate"></span>
</div>

<div style="padding:4px;width:100%;">
<div id="images" align="left">

    <?
	echo $tagsarray;
	?>
	
</div>
</div>

<div class="footer"><span class="btn"><a href="#" onclick="jQuery.cookie('username',null);jQuery.cookie('cbrate',null);jQuery.cookie('cbclicks',null);addvote();loading();window.location.href = 'http://cartoonbank.ru/ales/zukerberg/index.php';">Обнулить всё</a></span> <span class="btn"><a href="#" onclick="loading();window.location.href = 'http://cartoonbank.ru/ales/zukerberg/index.php';">Следующая пара</a></span> <span class="btn"><a href="#" onclick="login();">Назовите себя</a></span>
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
    //$output .= "<script>function res(num1){if(num1==1){num2=2}else{num2=1};if(jQuery('#'+num1).attr('rate')>jQuery('#'+num2).attr('rate')){alert('правильно: '+jQuery('#'+num1).attr('rate')+' больше, чем ' + jQuery('#'+num2).attr('rate'))};}</script>";
	return $output;
}
?>