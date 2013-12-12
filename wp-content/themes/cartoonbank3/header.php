<?
//header("Location: http://cartoonbank.ru");
/*
if ($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] == "cartoonbank.ru/?page_id=29" && !isset($_POST['cs']))
{
	header("Location: http://cartoonbank.ru/?page_id=29&offset=".rand(0,380));
}
*/
// change the header
$h = '';
$kw = '';
if (isset($_SERVER['QUERY_STRING']))
{
	// search word
	if (isset($_REQUEST['cs']) && $_REQUEST['cs'])
		{
			$h = $h."Карикатуры на тему '".$_REQUEST['cs']."'. ";
		}
	//category
	if (isset($_REQUEST['category']) && is_numeric($_REQUEST['category']))
		{
			$sql = "select name from wp_product_categories where id=".$_REQUEST['category'];
			$c = $wpdb->get_results($sql);
			$cat = $c[0]->name;
			$h = $h."Изображения категории '".$cat."' ";
		}
	//brand
	if (isset($_REQUEST['brand']) && is_numeric($_REQUEST['brand']))
		{
			$sql = "select name from wp_product_brands where id=".$_REQUEST['brand'];
			$c = $wpdb->get_results($sql);
			$brand = $c[0]->name;
			$h = $h."".$brand.". ";
		}
    //new
    if (isset($_REQUEST['new']) && is_numeric($_REQUEST['new']) && ($_REQUEST['new']=='1'))
        {
            $h = $h."Новые смешные рисунки ";
        }
    //best
    if (isset($_REQUEST['new']) && is_numeric($_REQUEST['new']) && ($_REQUEST['new']=='0'))
        {
            $h = $h."Смешные карикатуры. Лучшие карикатуры.";
        }
    //color
    if (isset($_REQUEST['color']) && ($_REQUEST['color']=='color'))
        {
            $h = $h."Цветные иллюстрации";
        }
    //color
    if (isset($_REQUEST['color']) && ($_REQUEST['color']=='bw'))
        {
            $h = $h."Чёрно-белые изображения";
        }
    //cartoonid
    if (isset($_REQUEST['cartoonid']) && is_numeric($_REQUEST['cartoonid']))
        {
            $sql = "SELECT name, description, additional_description FROM wp_product_list WHERE id = ".$_REQUEST['cartoonid'];
            $c = $wpdb->get_results($sql);
            $cartoon_title = $c[0]->name;
            $cartoon_description = $c[0]->description;
            $cartoon_tags = $c[0]->additional_description;
            $h = $h."Карикатура '".$cartoon_title."' — ". $cartoon_description;
            $kw = $kw.$cartoon_tags;
        }
    //pages
    if (isset($_REQUEST['page_id']) && is_numeric($_REQUEST['page_id']) && ($_REQUEST['page_id']!=29))
        {
            $h = $h.($post->post_title);
        }
    //offset
    if (isset($_REQUEST['offset']) && is_numeric($_REQUEST['offset']) && $_REQUEST['offset']!=0)
        {
			$page = $_REQUEST['offset']/20 + 1;
            $h = $h." Стр. ".$page.".";
        }

}
/*
$current_user = wp_get_current_user();
$_SESSION['uid']= $current_user->ID;
setcookie('uid', $_SESSION['uid']);
*/
if (isset($_REQUEST['page_id'])&&is_numeric($_REQUEST['page_id'])){
$pageid=$_REQUEST['page_id'];
}
else{
$pageid=29;
}
if (current_user_can('manage_options'))
			{
				$_edid = "";
				//$_edid = "<a href=".get_option('siteurl')."/wp-admin/post.php?post=".$pageid."&amp;action=edit target=_blank>edit</a>";
			}
			else
			{
				$_edid = "";
			}

?>
<!doctype html>
<html lang="ru">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <meta name="description" content="<?echo $h;?> Смешные карикатуры для газет, журналов и электронных СМИ. Лицензии." />
      <meta name="keywords" content="<?echo $kw;?> картунбанк, cartoonbank, карикатуры, смешные, картинки, комиксы, карикатура, шарж, caricature, cartoon, comics" />
      <title><?echo $h;?> Картунбанк, <?php if(wp_title('', false)) { echo ' '; } ?> <?php bloginfo('name'); ?>.</title>
      <meta name="generator" content="cartoonbank" />
      <link rel="Shortcut Icon" href="<?php echo get_option('home'); ?>/wp-content/themes/cartoonbank3/images/favicon.ico" type="image/x-icon" />
	  <link rel='index' title='Банк изображений' href='http://cartoonbank.ru' />
	  <link rel='alternate' type='application/rss+xml' title='Cartoonbank RSS' href='http://cartoonbank.ru//index.php?rss=true&amp;action=product_list&amp;type=rss'/>
      <link rel="stylesheet" href='http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/style.min.css?r2564' type="text/css" />
	  <link rel="stylesheet" href='http://cartoonbank.ru/wp-content/themes/cartoonbank3/style.min.css?r3252' type="text/css" />
	  <link rel="stylesheet" id='five-star-rating-CSS-css' href='http://cartoonbank.ru/wp-content/plugins/five-star-rating/assets/css/five-star-rating.min.css?ver=3.0' type='text/css' media='all' />
	  <?/*
	  //<script type='text/javascript' src='http://cartoonbank.ru/wp-includes/js/jquery/jquery.js?ver=1.4.2'></script>
      //<link rel="stylesheet" type="text/css" media="screen" href="<? echo SITEURL;?>ales/colorbox/example2/colorbox.min.css" />
	  //<script src="<? echo SITEURL;?>ales/colorbox/jquery.colorbox-min.js"></script>
	  //<script type='text/javascript' src='http://codeorigin.jquery.com/jquery-1.10.2.min.js'></script>
	  */
	  ?>
		<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
		<script type="text/javascript" src="http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/ajax.min.js"></script>
		<script type="text/javascript" src="http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/user.min.js"></script>
		<script src="http://cartoonbank.ru/wp-includes/js/jquery/jquery.highlight-3.min.js" type="text/javascript"></script>
		<script src='http://cartoonbank.ru/wp-content/plugins/five-star-rating/assets/js/five-star-rating.min.js?ver=0.1' type='text/javascript'></script>
        <script type='text/javascript'>function scrpts(){get_5stars();get_dimensions();get_share_this();get_fave();change_url();}</script>
		

      <?php 
	  /*
	  wp_head(); 

		// following content produced by wp_head();
		<link rel='stylesheet' id='five-star-rating-CSS-css'  href='http://cartoonbank.ru/wp-content/plugins/five-star-rating/assets/css/five-star-rating.min.css?ver=3.0' type='text/css' media='all' />
		<script type='text/javascript' src='http://cartoonbank.ru/wp-includes/js/jquery/jquery.js?ver=1.4.2'></script>
		<script type='text/javascript' src='http://cartoonbank.ru/wp-content/plugins/five-star-rating/assets/js/five-star-rating.min.js?ver=0.1'></script>
		<link rel='index' title='Банк изображений' href='http://cartoonbank.ru' />

		<script src="http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/ajax.min.js"></script>
		<script src="http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/user.min.js"></script>
		<link rel='alternate' type='application/rss+xml' title='Cartoonbank RSS' href='http://cartoonbank.ru//index.php?rss=true&amp;action=product_list&amp;type=rss'/>
		*/
	  
	  ?>
      <script type="text/javascript">
         var _gaq = _gaq || [];
         _gaq.push(['_setAccount', 'UA-127981-7']);
         _gaq.push(['_trackPageview']);
         (function() {
           var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
           ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
           var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
         })();
      </script>
   </head>
   <body>
<?
//Except 666:
//$cartoon_number = $wpdb->get_results("SELECT count( l.id ) AS cartoon_number FROM `wp_product_list` AS l, `wp_item_category_associations` AS a WHERE l.id = a.product_id AND l.active =1 AND l.visible =1 AND a.category_id in (select id from wp_product_categories WHERE active = 1 and id <> '666') AND l.brand in (SELECT DISTINCT id FROM `wp_product_brands` where `wp_product_brands`.active = 1)");

//Including 666:
//$cartoon_number = $wpdb->get_results("SELECT count( l.id ) AS cartoon_number FROM `wp_product_list` AS l, `wp_item_category_associations` AS a WHERE l.id = a.product_id AND l.active =1 AND l.visible =1 AND l.approved =1 AND l.brand in (SELECT DISTINCT id FROM `wp_product_brands` where `wp_product_brands`.active = 1)");

//$cartoon_number = $wpdb->get_results("SELECT count( l.id ) AS cartoon_number FROM `wp_product_list` AS l WHERE l.active = 1 AND l.visible = 1 AND l.approved = 1 AND l.brand in (SELECT DISTINCT id FROM `wp_product_brands` where `wp_product_brands`.active = 1)");

// speeden up but if the artist will be deactivated may be wrong calculation
$cartoon_number = $wpdb->get_results("SELECT count( l.id ) AS cartoon_number FROM `wp_product_list` AS l WHERE l.active = '1' AND l.visible = '1' AND l.approved = '1' AND l.brand != '0' AND l.category != '0'");


$cartoon_number = $cartoon_number[0]->cartoon_number;
$cartoon_number = $cartoon_number; // strange difference??? strlen($cartoon_number-1)

$switcher = substr($cartoon_number,strlen($cartoon_number)-1,1);

switch ($switcher)
{
case"1":
	//$license_text = "лицензионное<br />изображение";
	$license_text = "лицензионная<br />карикатура";
	break;
case"2":
case"3":
case"4":
	//$license_text = "лицензионных<br />изображения";
	$license_text = "лицензионных<br />карикатуры";
	break;
default:
	//$license_text = "лицензионных<br />изображений";
	$license_text = "лицензионных<br />карикатур";
	break;
}

$switcher2 = substr($cartoon_number,strlen($cartoon_number)-2,2);
	if ($switcher2>=10 && $switcher2<=19){$license_text = "лицензионных<br />карикатур";}


/*
<!-- <center><div id="suphead" style="float:center;height:20px;width:960px;background-color:#668bb7;"><a href="http://cartoonbank.ru/?page_id=2440" style="color:white;font-size:1.2em;line-height:1.8;">футболки, кружки, календари, открытки, альбомы карикатур</a></div></center> 

<center><div id="suphead" style="float:center;height:20px;width:960px;background-color:white;color:#9d0000;padding-top: 0.4em;"><marquee behavior="slide" direction="up" >В помощь планирующим выпуски статей мы добавили на первую страницу удобный календарь.</marquee></div></center>

<center><div id="suphead" style="float:center;height:20px;width:960px;background-color:white;color:#9d0000;padding-top: 0.4em;"><marquee behavior="slide" direction="left" ><a href="http://www.greenlamp.spb.ru/2012/12/14/non-stop-%D1%8E%D0%B1%D0%B8%D0%BB%D0%B5%D0%B9%D0%BD%D0%B0%D1%8F-%D0%B2%D1%8B%D1%81%D1%82%D0%B0%D0%B2%D0%BA%D0%B0-%D0%BF%D0%B5%D1%82%D0%B5%D1%80%D0%B1%D1%83%D1%80%D0%B3%D1%81%D0%BA%D0%BE/" target=_blank>Сегодня открытие выставки <b>NONSTOP</b> (15 лет группе «НЮАНС»)</a></marquee></div></center>
-->
*/

?>
<div id="header" style="height:90px;width:960px;">
<div>
<div style="font-size:.8em;color:white;vertical-align:bottom;width:185px;height:90px;background-color:#668bb7;float:left;"><span style="color:#13223f;font-size:2em;"><br><b><? echo ($cartoon_number);?></b></span><br /><?echo ($license_text);?></div>
<div style="width:580px;height:90px;float:left;">
   <br><a href="http://cartoonbank.ru/?page_id=29&amp;offset=0&amp;new=2"><img src="<?php echo get_option('home'); ?>/img/cb-logo-iq.png" style="border:0;" alt="Cartoonbank" width="558" height="58"></a><br />
   <?php bloginfo('description'); ?>
</div>

<?
	//<td style="font-size:.8em;color:white;vertical-align:middle;width:185px;height:45px;padding:4px;background-color:#990000">Во вторник утром<br />возможны перерывы<br />в работе сайта</td>
	//<td style="vertical-align:middle;width:185px;height:45px;padding:0px;background-color:#990000"><a href="http://spbsj.ru/last-news/138-vistavki/1678-yumor.html" target="_blank" style="font-size:0.8em;color:white;">1-10 АПРЕЛЯ 2011 ГОДА<br /><b>ЮМОР<br />ГОРЯЧЕГО КОПЧЕНИЯ</b></a></td>
	//<td style="font-size:.8em;color:white;vertical-align:middle;width:185px;height:45px;padding:4px;background-color:#668bb7">Рисунки художников<br />из семи<br />стран мира</td>
	//<td style="vertical-align:middle;width:185px;height:45px;padding:0px;background-color:#43742C"><a href="http://cartoonbank.ru/?page_id=1302" target="_blank" style="font-size:0.8em;color:white;">12-30 АПРЕЛЯ 2011 ГОДА<br /><b>В КОСМИЧЕСКОМ МАСШТАБЕ</b></a></td>
	//
	//<td style="vertical-align:top;width:185px;height:45px;padding:0px;background-color:#3366CC;"><a href="http://cartoonbank.ru/?page_id=1782" target="_blank" style="font-size:0.8em;color:white;"><div>Международный<br>конкурс<br>«Осторожно, люди!»</div></a></td>
	//<td style="vertical-align:top;width:185px;height:45px;padding:0px;background-color:#009900"><a href="http://cartoonbank.ru/?page_id=1351" target="_blank" style="font-size:0.8em;color:white;"><div>Спецпредложение:</div><div>ВАША ВЫСТАВКА —<br /> НАШИ РИСУНКИ</div></a></td>
	//<div style="width:185px;height:90px;float:left;"><a href="http://cartoonbank.ru/?page_id=2420"><img src="http://cartoonbank.ru/img/b/2012gift.gif" style="width:185px;height:90px;border:0;"></a>
	//</div>
	//<div style="width:185px;height:90px;float:left;"><a href="http://cartoonbank.ru/?page_id=2598"><img src="http://cartoonbank.ru/img/b/cake_gift.gif" style="width:185px;height:90px;border:0;"></a></div>

	//<div style="width:185px;height:90px;float:left;"><a href="http://cartoonbank.ru/?page_id=2762"><img src="http://cartoonbank.ru/img/b/2013gift.gif" style="width:185px;height:90px;border:0;"></a></div>
	//<!-- <div style="width:185px;height:90px;float:left;"><a href="http://karikashop.com/#ecwid:category=1620996&mode=category&offset=0&sort=normal" target="_blank"><img src="http://cartoonbank.ru/img/b/karikashop.gif" style="width:185px;height:90px;border:0;"></a></div> -->
?>

<div style="width:185px;height:90px;float:left;"><a href="http://cartoonbank.ru/?page_id=893"><img src="http://cartoonbank.ru/img/b/on-line.gif" style="width:185px;height:90px;border:0;" alt="продажа карикатур онлайн" width="185" height="86"></a></div>
</div>

</div>

<?
function selected_style()
{
	echo " style='color:#668bb7'";
}

if (isset($_REQUEST['new']) && is_numeric($_REQUEST['new']))
{
	$_new=$_REQUEST['new'];
}
else
{
	$_new=0;
}
?>

<div id="navbar">
   <ul>
      <li><a href="/?page_id=95"<? $pageid=='95'? selected_style():"" ?> title='коротко о сайте Картунбанк'>О проекте</a></li>
      <li><a href="/?page_id=29&amp;offset=0&amp;new=0"<? $pageid=='29' & $_new==0? selected_style():"" ?> title='избранные работы'>Избранное</a></li>
      <li><a href="/?page_id=29&amp;offset=0&amp;new=1"<? $pageid=='29'& $_new==1 ? selected_style():"" ?> title='показать новые'>Новое</a></li>
      <li><a href="/?page_id=73"<? $pageid=='73'? selected_style():"" ?> title='художникам'>Авторам</a></li>
      <li><a href="/?page_id=97"<? $pageid=='97'? selected_style():"" ?> title='покупателям'>Клиентам</a></li>
      <li><a href="/?page_id=907"<? $pageid=='907'? selected_style():"" ?> title='посетителям'>Зрителям</a></li>
      <li><a href="/?page_id=1215"<? $pageid=='1215'? selected_style():"" ?> title='наши партнёры'>Партнёры</a></li>
      <li><a href="/?page_id=1260"<? $pageid=='1260'? selected_style():"" ?> title='друзья и коллеги'>Друзья</a></li>
      <li><a href="/?page_id=2"<? $pageid=='2'? selected_style():"" ?> title='ответы на часто задаваемые вопросы'>Ответы</a></li>
      <li><a href="/?page_id=2860"<? $pageid=='2860'? selected_style():"" ?> title='новости сайта'>Новости</a></li>
      <li><a href="/?page_id=2870"<? $pageid=='2870'? selected_style():"" ?> title='видео'>Видео</a></li>
      <li><a href="/?page_id=976"<? $pageid=='976'? selected_style():"" ?> title='как нас найти'>Контакты</a></li>
      <li><a href="/?page_id=2041"<? $pageid=='2041'? selected_style():"" ?> title='English'><img src="http://cartoonbank.ru/img/eng.gif" style="width:20px;border:0;" alt="English"></a></li>
      <?/*
	  <li><? echo $_edid;?></li>
	  */?>
   </ul>
</div>
<div id="pt"></div>
