<?php
$redirecturl = get_option('siteurl')."/?page_id=29";
   header("Location: $redirecturl"); 
?> 

<?php get_header(); ?>

<div id="content">


<div id="contentmiddle">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>


	<!-- <div id="contentdate">
		<h3><?php the_time('M'); ?></h3>
		<h4><?php the_time('j'); ?></h4>
	</div> -->
		
	<!-- <div id="contenttitle">
		<h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
		<p><?php the_time('F j, Y'); ?> | <?php comments_popup_link('Комментировать', '1 Отклик', '% Отклики'); ?></p>
	</div> -->

	<div id="contentbody">
		<?php the_content(__('Читать дальше'));?>
	</div>

	<?php endwhile; else: ?>

	<p><?php _e('Извините, здесь пока ничего нет.'); ?></p><?php endif; ?><br />


</div>

	
	<div id="contentright">
		<!-- <h2>Ключевые слова</h2>
		<?php //jkeywords_tag_cloud(); ?> 
<br>-->
<?
$unregged ='';
if ($user_identity == '')
{
	$user_identity = 'незнакомец';
	$unregged = "<br><a href='wp-register.php'>Регистрация</a> позволяет скачивать картинки.";
}
?>
		<div id="user_info"><?php printf(__(TXT_WPSC_HELLO.'<a href="wp-admin/profile.php"><strong>%s</strong></a>.'), $user_identity); echo ($unregged); ?></div>
		<br>
		<?php echo nzshpcrt_shopping_basket(); ?>

		<?php get_sidebar(); ?>
	</div>

</div>
	


<!-- The main column ends  -->

<?php get_footer(); ?>