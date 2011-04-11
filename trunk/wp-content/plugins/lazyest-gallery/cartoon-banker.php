<?php
/*
Plugin Name: Cartoon Banker
Plugin URI: none
Description: A plugin that manages Cartoonbank.
Version: 0.1
Author: Igor Ales
Author URI: http://cartoonist.name
*/
function add_menu(){
add_menu_page('Cartoon Banker','Банкир','read','lazyest-gallery/al-admin-panel.php');
//add_submenu_page('lazyest-gallery/al-admin-panel.php','Input1', 'Отправка в базу', 'read', 'lazyest-gallery/mass-upload.php');

$current_user = wp_get_current_user();
//pokazh($current_user->wp_capabilities);

if (isset($current_user->wp_capabilities['editor']) && ($current_user->wp_capabilities['editor']==1))
	add_submenu_page('lazyest-gallery/al-admin-panel.php','Прихожая', 'Прихожая', 'read', 'purgatory/purgatory.php');

if (isset($current_user->wp_capabilities['administrator']) && ($current_user->wp_capabilities['administrator']==1))
	add_submenu_page('lazyest-gallery/al-admin-panel.php','Прихожая', 'Прихожая', 'read', 'purgatory/purgatory.php');

if (isset($current_user->wp_capabilities['administrator']) && ($current_user->wp_capabilities['administrator']==1))
	add_submenu_page('lazyest-gallery/al-admin-panel.php','Тема дня', 'Тема дня', 'read', 'wp-shopping-cart/themeoftheday.php');

if (isset($current_user->wp_capabilities['editor']) && ($current_user->wp_capabilities['editor']==1))
	add_submenu_page('lazyest-gallery/al-admin-panel.php','Тема дня', 'Тема дня', 'read', 'wp-shopping-cart/themeoftheday.php');


if (isset($current_user->wp_capabilities['administrator']) && ($current_user->wp_capabilities['administrator']==1))
	add_submenu_page('lazyest-gallery/al-admin-panel.php','Продажи работ', 'Продажи работ', 'read', 'wp-shopping-cart/allsales.php');

if (isset($current_user->wp_capabilities['editor']) && ($current_user->wp_capabilities['editor']==1))
	add_submenu_page('lazyest-gallery/al-admin-panel.php','Продажи работ', 'Продажи работ', 'read', 'wp-shopping-cart/allsales.php');

if (isset($current_user->wp_capabilities['author']) && ($current_user->wp_capabilities['author']==1))
	add_submenu_page('lazyest-gallery/al-admin-panel.php','Продажи работ', 'Продажи работ', 'read', 'wp-shopping-cart/allsales.php');


//add_submenu_page('lazyest-gallery/al-admin-panel.php','Прихожая', 'Прихожая', 'read', 'purgatory/purgatory.php');
add_submenu_page('lazyest-gallery/al-admin-panel.php','Редактор базы изображений', 'Редактор базы', 'read', 'wp-shopping-cart/display-items.php');
/*
if (isset($current_user->wp_capabilities['editor']) && $current_user->wp_capabilities['editor']==1)
	add_submenu_page('lazyest-gallery/al-admin-panel.php','Прихожая', 'Прихожая', 'read', 'purgatory/purgatory.php');
else if (isset($current_user->wp_capabilities['administrator']) && $current_user->wp_capabilities['administrator']==1)
	add_submenu_page('lazyest-gallery/al-admin-panel.php','Прихожая', 'Прихожая', 'read', 'purgatory/purgatory.php');
*/
}
//pokazh($current_user);
//pokazh($wpdb);
add_action('admin_menu','add_menu');
?>