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
add_submenu_page('lazyest-gallery/al-admin-panel.php','Input1', 'Отправка в базу', 'read', 'lazyest-gallery/mass-upload.php');
add_submenu_page('lazyest-gallery/al-admin-panel.php','Input2', 'Редактор базы', 'read', 'wp-shopping-cart/display-items.php');
}

add_action('admin_menu','add_menu');
?>