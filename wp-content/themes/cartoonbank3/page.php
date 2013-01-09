<?
$current_user = wp_get_current_user();
$_SESSION['uid']= $current_user->ID;
setcookie('uid', $_SESSION['uid']);
?>
<?php get_header(); ?>

<div id="content">
<div id="contentmiddle">
	<?php if (have_posts()) : while (have_posts()) : the_post();?>
	<?php the_content(__('Читать дальше'));?>
	<?php endwhile; else: ?>
	<?php _e('<br />Извините, здесь пока ничего нет.<br />'); ?><?php endif;?>
</div>
<div id="contentright">
<?
$unregged ='';
if ($user_identity == '')
{
$unregged = "<a href='wp-login.php'>Вход</a>";
?><div id="user_info"><? echo ($unregged); ?></div><?
}
else
{
?><div id="user_info"><?php printf(__(TXT_WPSC_HELLO.'<a href="wp-admin/profile.php"><strong>%s</strong></a>.'), $user_identity); echo ($unregged); ?></div><?
}
?>
<br />
<?php echo nzshpcrt_shopping_basket(); ?>
<?php get_sidebar(); ?>
</div>

<div style="clear:both;"></div>
<div id="navbarbottom" style="border-top:5px solid #658DB5;">
	<iframe src="https://www.google.com/calendar/embed?showTitle=0&amp;showNav=0&amp;showPrint=0&amp;showCalendars=0&amp;showTz=0&amp;height=400&amp;wkst=2&amp;bgcolor=%23FFFFFF&amp;src=9ats0457qmvp1mv5kecdut2uhs%40group.calendar.google.com&amp;color=%23182C57&amp;ctz=Europe%2FMoscow" style=" border-width:0 " width="960" height="400" frameborder="0" scrolling="no"></iframe>
</div>

<div id="topfooter" style="padding-top:10px;border-top:1px solid #C0C0C0; background: #FFFFFF url(<? echo (get_option('siteurl').'/img/w.png');?>) top center repeat-y;"><h4>Популярные темы. Выбирайте или <a href="http://cartoonbank.ru/?page_id=927" style="color:#CC3300;border:1px solid silver;padding-left:6px;padding-right:6px;">введите своё ключевое слово</a>...</h4>
<?
	$filepath = WP_CONTENT_DIR . '/tags/ales-tag_cloud_small.php' ;
	if ( file_exists( $filepath ) )
	require_once( WP_CONTENT_DIR . '/tags/ales-tag_cloud_small.php' );
?>
</div>
<div style="text-align:right;background-color:white;border-bottom:1px solid #C0C0C0;"><a href="http://cartoonbank.ru/?page_id=390"><h4>...здесь ещё больше ключевых слов</h4></a></div>

</div>
<!-- The main column ends  -->
<?php get_footer(); ?>