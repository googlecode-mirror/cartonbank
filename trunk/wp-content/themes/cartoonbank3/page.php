<?php get_header(); ?>

<div id="content">


<div id="contentmiddle">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<?php the_content(__('Читать дальше'));?>
	<?php endwhile; else: ?>
	<?php _e('<br />Извините, здесь пока ничего нет.<br />'); ?><?php endif; ?>
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
	<div id="topfooter" style="padding-top:10px;border-top:1px solid #C0C0C0; background: #FFFFFF url(<? echo (get_option('siteurl').'/img/w.png');?>) top center repeat-y;"><h1>Популярные темы:</h1>
	<?
		$filepath = WP_CONTENT_DIR . '/tags/ales-tag_cloud_small.php' ;
		if ( file_exists( $filepath ) )
		require_once( WP_CONTENT_DIR . '/tags/ales-tag_cloud_small.php' );
	?>
	</div>

</div>
<!-- The main column ends  -->

<?php get_footer(); ?>