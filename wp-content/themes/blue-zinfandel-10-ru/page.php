<?php get_header(); ?>

<div id="content">
<?php get_sidebar(); ?>

<div id="contentmiddle">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<h2><?php the_title(); ?></h2>
	<?php the_content(__('Читать дальше'));?>

	<?php endwhile; else: ?>

	<p><?php _e('Извините, здесь пока ничего нет.'); ?></p><?php endif; ?>
</div>

<div id="contentright">
	<!-- <h2>Ключевые слова</h2>
	<?php jkeywords_tag_cloud(); ?>
	<br> -->
<?
$unregged ='';
if ($user_identity == '')
{
	$user_identity = 'незнакомец';
	$unregged = "<br>Мы рады вам, наслаждайтесь рисунками, но имейте в виду, что скачать картинку в хорошем разрешении можно только после <a href='wp-register.php'>регистрации</a>.";
}
?>
		<div id="user_info"><?php printf(__(TXT_WPSC_HELLO.'<a href="wp-admin/profile.php"><strong>%s</strong></a>.'), $user_identity); echo ($unregged); ?></div>
		<br>

	<?php echo nzshpcrt_shopping_basket(); ?>
</div>

</div>

<!-- The main column ends  -->

<?php get_footer(); ?>