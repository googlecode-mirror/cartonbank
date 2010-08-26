<?php get_header(); ?>

<div id="content">


<div id="contentmiddle">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<!-- <h2><?php //the_title(); ?></h2> -->
	<?php the_content(__('Читать дальше'));?>

	<?php endwhile; else: ?>

	<p><?php _e('Извините, здесь пока ничего нет.'); ?></p><?php endif; ?>
</div>

<div id="contentright">
	<!-- <h2>Ключевые слова</h2>
	<?php //jkeywords_tag_cloud(); ?>
	<br> -->
<?
$unregged ='';
if ($user_identity == '')
{
	$unregged = "<br>Пожалуйста, <a href='wp-register.php'>зарегистрируйтесь</a> или <a href='wp-login.php'>залогиньтесь</a>.";
		?><div id="user_info"><? echo ($unregged); ?></div><?
}
else
{
		?><div id="user_info"><?php printf(__(TXT_WPSC_HELLO.'<a href="wp-admin/profile.php"><strong>%s</strong></a>.'), $user_identity); echo ($unregged); ?></div><?
}
?>
		<br>

<?php echo nzshpcrt_shopping_basket(); ?>
<?php get_sidebar(); ?>
</div>
</div>

<!-- The main column ends  -->

<?php get_footer(); ?>