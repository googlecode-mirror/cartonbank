<?
//header("Location: http://cartoonbank.ru");

if ($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] == "cartoonbank.ru/?page_id=29" && !isset($_POST['cs']))
{
	header("Location: http://cartoonbank.ru/?page_id=29&offset=".rand(0,10000));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="distribution" content="global" />
<meta name="robots" content="follow, all" />
<meta name="language" content="en, sv" />

<title>Картунбанк<?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>
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
	$license_text = "лицензионное<br>изображение";
	break;
case"2":
case"3":
case"4":
	$license_text = "лицензионных<br>изображения";
	break;
default:
	$license_text = "лицензионных<br>изображений";
	break;
}
?>


<div id="header">
<table style="width:100%;margin:0;padding:0;">
<tr>
<td style="font-size:.8em;color:white;vertical-align:middle;width:185px;height:45px;padding:4px;background-color:#668bb7"><span style="color:#13223f;font-size:2em;"><b><? echo ($cartoon_number);?></b></span><br><?echo ($license_text);?></td>
<td>
	<a href="<?php echo get_option('home'); ?>/"><img src="<?php echo get_option('home'); ?>/wp-admin/images/cb-logo.gif" border="0"></a><br />
	<?php bloginfo('description'); ?>
</td>
<?
//<td style="font-size:.8em;color:white;vertical-align:middle;width:185px;height:45px;padding:4px;background-color:#990000">Во вторник утром<br>возможны перерывы<br>в работе сайта</td>
//<td style="vertical-align:middle;width:185px;height:45px;padding:0px;background-color:#990000"><a href="http://spbsj.ru/last-news/138-vistavki/1678-yumor.html" target="_blank" style="font-size:0.8em;color:white;">1-10 АПРЕЛЯ 2011 ГОДА<br><b>ЮМОР<br>ГОРЯЧЕГО КОПЧЕНИЯ</b></a></td>
//<td style="font-size:.8em;color:white;vertical-align:middle;width:185px;height:45px;padding:4px;background-color:#668bb7">Рисунки художников<br>из семи<br>стран мира</td>
?>
<td style="vertical-align:middle;width:185px;height:45px;padding:0px;background-color:#990000"><a href="http://spbsj.ru/last-news/138-vistavki/1678-yumor.html" target="_blank" style="font-size:0.8em;color:white;">1-10 АПРЕЛЯ 2011 ГОДА<br><b>ЮМОР<br>ГОРЯЧЕГО КОПЧЕНИЯ</b></a></td>
</tr></table>
</div>

<?
function selected_style()
{
	echo "style='color:#668bb7'";
}
?>

<div id="navbar">
	<ul> 
		<li><a href="?page_id=95"<? $_GET['page_id']=='95'? selected_style():"" ?>>О проекте</a></li>
		<li><a href="?page_id=29&offset=0"<? $_GET['page_id']=='29'? selected_style():"" ?> title='на первую страницу'>Банк изображений</a></li>
		<li><a href="?page_id=942"<? $_GET['page_id']=='942'? selected_style():"" ?>>Новости</a></li>
		<li><a href="?page_id=73"<? $_GET['page_id']=='73'? selected_style():"" ?>>Авторам</a></li>
		<li><a href="?page_id=97"<? $_GET['page_id']=='97'? selected_style():"" ?>>Клиентам</a></li>
		<li><a href="?page_id=907"<? $_GET['page_id']=='907'? selected_style():"" ?>>Зрителям</a></li>
		<li><a href="?page_id=1215"<? $_GET['page_id']=='1215'? selected_style():"" ?>>Партнёры</a></li>
		<li><a href="?page_id=1260"<? $_GET['page_id']=='1260'? selected_style():"" ?>>Друзья</a></li>
		<li><a href="?page_id=2"<? $_GET['page_id']=='2'? selected_style():"" ?>>Ответы</a></li>
		<li><a href="?page_id=976"<? $_GET['page_id']=='976'? selected_style():"" ?>>Контакты</a></li>
	</ul>
</div>
<A NAME="pt"></a>
