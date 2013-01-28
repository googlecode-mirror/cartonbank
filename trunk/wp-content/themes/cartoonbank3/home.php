<?php
$redirecturl = SITEURL."?page_id=29&offset=0&new=0";
header("Location: $redirecturl"); 

$current_user = wp_get_current_user();
$_SESSION['uid']= $current_user->ID;
if(isset($current_user->user_nicename)){
    $_SESSION['username']= $current_user->user_nicename;
}
else{
    $_SESSION['username']= 'xxx';
}
setcookie('uid', $_SESSION['uid']);
setcookie('username', $_SESSION['username']);


?> 

<?php get_header(); ?>

<div id="content">


<div id="contentmiddle">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	<div id="contentbody">
		<?php the_content(__('Читать дальше'));?>
	</div>

	<?php endwhile; else: ?>

	<p><?php _e('Извините, здесь пока ничего нет.'); ?></p><?php endif; ?><br />


</div>

	
	<div id="contentright">
<?
$unregged ='';
if ($user_identity == '')
{
	$user_identity = 'незнакомец';
	$unregged = "<br /><a href='wp-register.php'>Регистрация</a> позволяет скачивать картинки.";
}
?>
		<div id="user_info"><?php printf(__(TXT_WPSC_HELLO.'<a href="wp-admin/profile.php"><strong>%s</strong></a>.'), $user_identity); echo ($unregged); ?></div>
		<br />
		<?php echo nzshpcrt_shopping_basket(); ?>

		<?php get_sidebar(); ?>
	</div>
</div>
<!-- Main column ends  -->

<?php get_footer(); ?>