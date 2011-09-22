<?php

/** WordPress Administration Bootstrap */
require_once('./admin.php');
$current_user = wp_get_current_user();
include ('admin-header.php');

// current user id
$user_id = $current_user->id;
?>
<h1>Любимые рисунки</h1>
<div id="favehelp" style="margin-bottom:20px;">
В этом разделе собраны ссылки на рисунки, отмеченные вами как "любимые".<br>
<img src="<?echo get_option('siteurl');?>/img/help/fave_it1.png" width=400> <img src="<?echo get_option('siteurl');?>/img/help/fave_it2.png" style="width:400px;vertical-align:top;">
</div>
<div><h2>Вы отметили:</h2></div>
<?php
$sql = "select f.image_id, p.image from favorites as f, wp_product_list as p where f.image_id = p.id and f.user_id=".$user_id;
$faves = $wpdb->get_results($sql, ARRAY_A);
if ( ! empty( $faves))
{
	foreach ($faves as $fav)
	{
		echo "<a href='".get_option('siteurl')."/?page_id=29&cartoonid=".$fav['image_id']."' target='_blank'><img src='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/".$fav['image']."' style='border:1px silver solid'></a> ";
		//pokazh($fav);
	}
}
else
{
	echo "Не выбрано ни одной картинки.";
}
include('./admin-footer.php');
?>
