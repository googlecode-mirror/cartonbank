<?php get_header(); ?>

<div id="content">


<div id="contentmiddle">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<?php the_content(__('Читать дальше'));?>
	<?php endwhile; else: ?>

	<p><?php _e('Извините, здесь пока ничего нет.'); ?></p><?php endif; ?>
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
<br>
<?php echo nzshpcrt_shopping_basket(); ?>
<?php get_sidebar(); ?>
</div>
</div>
<!-- The main column ends  -->
<?php get_footer(); ?>