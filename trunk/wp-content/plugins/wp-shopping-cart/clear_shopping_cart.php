<?php
global $wpdb;
//include(ROOTDIR."wp-config.php");
session_start();
$_SESSION['nzshpcrt_cart'] = Array();
$_SESSION['total'] = null;
$_SESSION['nzshpcrt_serialized_cart'] = null;
$cart = null;
echo "<strong>Корзина заказов</strong>&nbsp;<img src='http://cartoonbank.ru/img/cart.gif'><br>Корзина пуста";
?>
