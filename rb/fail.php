<?
header("Location: http://cartoonbank.ru/?page_id=918&p=0");
exit;

require_once('../wp-config.php');
global $wpdb;

$inv_id = $_REQUEST["InvId"];
echo "Вы отказались от оплаты. Заказ# $inv_id\n";
echo "You have refused payment. Order# $inv_id\n";
?>


<a href="Location: http://cartoonbank.ru/?page_id=31&p=0">перейти на страницу выбора способа оплаты</a>
