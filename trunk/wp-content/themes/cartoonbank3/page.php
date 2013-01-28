<?
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
	<?php if (have_posts()) : while (have_posts()) : the_post();?>
	<?php the_content(__('Читать дальше'));?>
	<?php endwhile; else: ?>
	<?php _e('<br />Извините, здесь пока ничего нет.<br />'); ?><?php endif;?>
</div>
<div id="contentright">


<div id="user_info"><span id="username"><a href='wp-login.php'>Пожалуйста, залогиньтесь.</a></span></div>

<div id="shoppingcart"><img src="<? echo SITEURL.'img/loading.gif'; ?>" alt="loading"></div>

<?php get_sidebar(); ?>
</div>



<div style="clear:both;"></div>


<?
// Calendar
if (isset($_GET['page_id']) && $_GET['page_id']=='29'){
    ?>
    <div id="navbarbottom" style="border-top:5px solid #658DB5;">
	    <iframe src="https://www.google.com/calendar/embed?showTitle=0&amp;showNav=0&amp;showPrint=0&amp;showCalendars=0&amp;showTz=0&amp;height=400&amp;wkst=2&amp;bgcolor=%23FFFFFF&amp;src=9ats0457qmvp1mv5kecdut2uhs%40group.calendar.google.com&amp;color=%23182C57&amp;ctz=Europe%2FMoscow" style=" border-width:0 " width="960" height="400" frameborder="0" scrolling="no"></iframe>
    </div>
    <?
}
/// Calendar
?>

<?
// Tag cloud
if (isset($_GET['page_id']) && $_GET['page_id']=='29'){
?>
<div id="topfooter" style="padding-top:10px;border-top:1px solid #C0C0C0; background: #FFFFFF url(<? echo (SITEURL.'img/w.png');?>) top center repeat-y;"><h4>Популярные темы. Выбирайте или <a href="<? echo SITEURL;?>?page_id=927" style="color:#CC3300;border:1px solid silver;padding-left:6px;padding-right:6px;">введите своё ключевое слово</a>...</h4>
<?
	$filepath = WP_CONTENT_DIR . '/tags/ales-tag_cloud_small.php' ;
	if ( file_exists( $filepath ) )
	require_once( WP_CONTENT_DIR . '/tags/ales-tag_cloud_small.php' );
?>
</div>

<div style="text-align:right;background-color:white;border-bottom:1px solid #C0C0C0;"><a href="<?echo SITEURL;?>?page_id=390"><h4>...здесь ещё больше ключевых слов</h4></a></div>
<?
}
/// Tag cloud
?>


</div>
<!-- The main column ends  -->
<?php get_footer(); ?>
<script>
    jQuery(document).ready(function() {
    var getshoppingcarturl = '<? echo SHOPPINGCARTURL.'get_shopping_cart.php'; ?>';
    jQuery('#shoppingcart').load(getshoppingcarturl);
});

</script>