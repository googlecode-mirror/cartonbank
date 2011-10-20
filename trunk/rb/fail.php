<?
// send notification
$descr = "неудачная транзакция в робокассе";
send_email($descr);

header("Location: http://cartoonbank.ru/?page_id=918&p=0");
exit;

require_once('../wp-config.php');
global $wpdb;

$inv_id = $_REQUEST["InvId"];
echo "Вы отказались от оплаты. Заказ# $inv_id\n";
echo "You have refused payment. Order# $inv_id\n";

function send_email($description)
{
	$headers = "From: bankir@cartoonbank.ru\r\n" .
			   'X-Mailer: PHP/' . phpversion() . "\r\n" .
			   "MIME-Version: 1.0\r\n" .
			   "Content-Type: text/html; charset=utf-8\r\n" .
			   "Content-Transfer-Encoding: 8bit\r\n\r\n";
	
	$mess = $description;

	mail("igor.aleshin@gmail.com", 'неудачная транзакция в робокассе', $mess, $headers);
	return;		
}

?>


<a href="Location: http://cartoonbank.ru/?page_id=31&p=0">перейти на страницу выбора способа оплаты</a>
