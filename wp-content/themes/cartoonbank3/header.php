<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="distribution" content="global" />
<meta name="robots" content="follow, all" />
<meta name="language" content="en, sv" />

<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />

<link rel="Shortcut Icon" href="<?php echo get_option('home'); ?>/wp-content/themes/cartoonbank2/images/favicon.ico" type="image/x-icon" />
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_get_archives('type=monthly&format=link'); ?>
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

<div id="header">
	<a href="<?php echo get_option('home'); ?>/"><img src="<?php echo get_option('home'); ?>/wp-admin/images/cb-logo.gif" border="0"></a><br />
	<?php bloginfo('description'); ?>
</div>

<?
function selected_style()
{
	//echo "style='background-color:#658cb3;padding:2px;color:#CCFFFF'";
	//echo "style='font-weight:bold;color:black'";
	//echo "style='border-bottom-style:dotted;'";
	echo "style='color:#658cb3'";
	
}
?>

<div id="navbar">
	<ul> 
		<li><a href="?page_id=95" <? $_GET['page_id']=='95'? selected_style():"" ?> >О проекте</a></li>
		<li><a href="?page_id=29" <? $_GET['page_id']=='29'? selected_style():"" ?> >Банк изображений</a></li>
		<li><a href="?page_id=73" <? $_GET['page_id']=='73'? selected_style():"" ?> >Авторам</a></li>
		<li><a href="?page_id=97" <? $_GET['page_id']=='97'? selected_style():"" ?> >Клиентам</a></li>
		<li><a href="?page_id=2" <? $_GET['page_id']=='2'? selected_style():"" ?> >Ответы</a></li>
	</ul>
</div>
<a name="pagetop">&nbsp;</a>