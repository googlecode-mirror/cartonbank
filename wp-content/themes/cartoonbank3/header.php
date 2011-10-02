<?
//header("Location: http://cartoonbank.ru");

if ($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] == "cartoonbank.ru/?page_id=29" && !isset($_POST['cs']))
{
	header("Location: http://cartoonbank.ru/?page_id=29&offset=".rand(0,380));
}

// change the header
$h = '';
if (isset($_SERVER['QUERY_STRING']))
{
	// search word
	if (isset($_GET['cs']) && $_GET['cs'])
		{
			$h = $h."(".$_GET['cs'].") ";
		}
	if (isset($_POST['cs']) && $_POST['cs'])
		{
			$h = $h."(".$_POST['cs'].") ";
		}
	//category
	if (isset($_GET['category']) && $_GET['category'] && is_numeric($_GET['category']))
		{
			$sql = "select name from wp_product_categories where id=".$_GET['category'];
			$c = $wpdb->get_results($sql);
			$cat = $c[0]->name;
			$h = $h."(".$cat.") ";
		}
	if (isset($_POST['category']) && $_POST['category'])
		{
			$sql = "select name from wp_product_categories where id=".$_GET['category'];
			$c = $wpdb->get_results($sql);
			$cat = $c[0]->name;
			$h = $h."(".$cat.") ";
		}
	//brand
	if (isset($_GET['brand']) && $_GET['brand'] && is_numeric($_GET['brand']))
		{
			$sql = "select name from wp_product_brands where id=".$_GET['brand'];
			$c = $wpdb->get_results($sql);
			$brand = $c[0]->name;
			$h = $h."".$brand.". ";
		}
	if (isset($_POST['brand']) && $_POST['brand'])
		{
			$sql = "select name from wp_product_brands where id=".$_GET['brand'];
			$c = $wpdb->get_results($sql);
			$brand = $c[0]->name;
			$h = $h."".$brand.". ";
		}
}
$current_user = wp_get_current_user();
$_SESSION['uid']= $current_user->ID;
setcookie('uid', $_SESSION['uid']);
if (current_user_can('manage_options'))
			{
				$_edid = "<a href=".get_option('siteurl')."/wp-admin/post.php?post=".$_GET['page_id']."&action=edit target=_blank>edit</a>";
			}
			else
			{
				$_edid = "";
			}

?>
<!doctype html>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="distribution" content="global" />
<meta name="robots" content="all" />
<meta name="content-language" content="ru" />
<meta name="description" content="Лицензионные карикатуры для газет, журналов и электронных СМИ" />
<meta name="keywords" content="картунбанк, cartoonbank, карикатуры, скачать, приколы, смешные, картинки, комиксы,  карикатура, ру, комикс, коллаж, шарж, стрип, caricatura, caricature, cartoon, ru, comics, comix" />

<title><?echo $h;?>Картунбанк<?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?>.  Карикатуры</title>
<meta name="generator" content="cartoonbank" />

<link rel="Shortcut Icon" href="<?php echo get_option('home'); ?>/wp-content/themes/cartoonbank3/images/favicon.ico" type="image/x-icon" />

<?php wp_head(); ?>
<style type="text/css" media="screen">
<!-- @import url( <?php bloginfo('stylesheet_url'); ?> ); -->
</style>
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
$cartoon_number = $wpdb->get_results("SELECT count( l.id ) AS cartoon_number FROM `wp_product_list` AS l WHERE l.active = 1 AND l.visible = 1 AND l.approved = 1 AND l.brand != '0'");


$cartoon_number = $cartoon_number[0]->cartoon_number;
$cartoon_number = $cartoon_number - 1; // strange difference??? strlen($cartoon_number-1)

$switcher = substr($cartoon_number,strlen($cartoon_number)-1,1);

switch ($switcher)
{
case"1":
	$license_text = "лицензионное<br />изображение";
	break;
case"2":
case"3":
case"4":
	$license_text = "лицензионных<br />изображения";
	break;
default:
	$license_text = "лицензионных<br />изображений";
	break;
}
?>



<div id="header" style="height:90px;width:960;">
<div>

<div style="font-size:.8em;color:white;vertical-align:bottom;width:185px;height:90px;background-color:#668bb7;float:left;"><span style="color:#13223f;font-size:2em;"><br><b><? echo ($cartoon_number);?></b></span><br /><?echo ($license_text);?>
</div>

<div style="width:580px;height:90px;float:left;">
	<br><a href="<?php echo get_option('home'); ?>/"><img src="<?php echo get_option('home'); ?>/wp-admin/images/cb-logo.gif" border="0"></a><br />
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
?>

<div style="width:185px;height:90px;float:left;"><a href="http://cartoonbank.ru/?page_id=893"><img src="http://cartoonbank.ru/img/b/on-line.gif" width="185" height="90" border="0"></a>
</div>

</div>

</div>

<?
function selected_style()
{
	echo " style='color:#668bb7'";
}

if (isset($_GET['new']) && is_numeric($_GET['new']))
{
	$_new=$_GET['new'];
}
else
{
	$_new=0;
}
?>

<div id="navbarbottom">
	<ul> 
		<li><a href="?page_id=95"<? $_GET['page_id']=='95'? selected_style():"" ?> title='коротко о сайте Картунбанк'>О проекте</a></li>
		<li><a href="?page_id=29&offset=0&new=0"<? $_GET['page_id']=='29' & $_new==0? selected_style():"" ?> title='избранные работы'>Избранное</a></li>
		<li><a href="?page_id=29&offset=0&new=1"<? $_GET['page_id']=='29'& $_new==1 ? selected_style():"" ?> title='показать новые'>Новое</a></li>
		<li><a href="?page_id=73"<? $_GET['page_id']=='73'? selected_style():"" ?> title='художникам'>Авторам</a></li>
		<li><a href="?page_id=97"<? $_GET['page_id']=='97'? selected_style():"" ?> title='покупателям'>Клиентам</a></li>
		<li><a href="?page_id=907"<? $_GET['page_id']=='907'? selected_style():"" ?> title='посетителям'>Зрителям</a></li>
		<li><a href="?page_id=1215"<? $_GET['page_id']=='1215'? selected_style():"" ?> title='наши партнёры'>Партнёры</a></li>
		<li><a href="?page_id=1260"<? $_GET['page_id']=='1260'? selected_style():"" ?> title='друзья и коллеги'>Друзья</a></li>
		<li><a href="?page_id=2"<? $_GET['page_id']=='2'? selected_style():"" ?> title='ответы на часто задаваемые вопросы'>Ответы</a></li>
		<li><a href="?page_id=942"<? $_GET['page_id']=='942'? selected_style():"" ?> title='новости сайта'>Новости</a></li>
		<li><a href="?page_id=976"<? $_GET['page_id']=='976'? selected_style():"" ?> title='как нас найти'>Контакты</a></li>
		<li><a href="?page_id=2041"<? $_GET['page_id']=='2041'? selected_style():"" ?> title='English'><img src="http://cartoonbank.ru/img/eng.gif" width="20" border="0" alt="English"></a></li>
		<li><? echo $_edid;?></li>
	</ul>
</div>
<A NAME="pt"></a>