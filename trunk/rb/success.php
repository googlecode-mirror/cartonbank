<?
require_once('../wp-config.php');
global $wpdb;

// чтение параметров
// read parameters
$out_summ = $_REQUEST["OutSum"];
$inv_id = $_REQUEST["InvId"];
$shp_item = $_REQUEST["Shp_item"];
$crc = $_REQUEST["SignatureValue"];
$crc = strtoupper($crc);

//pokazh($inv_id,"inv_id");

$purchase_log_sql = "SELECT * FROM `wp_purchase_logs` WHERE `id`= ".$inv_id." LIMIT 1";
//pokazh($purchase_log_sql,"purchase_log_sql");

$purchase_log = $wpdb->get_results($purchase_log_sql,ARRAY_A) ;
//pokazh($purchase_log,"purchase_log_sql");

if (isset($purchase_log[0]['id']))
	{
		$purchaseid = $purchase_log[0]['id'];
		$transact_url = get_option('transact_url');
		$sessionid = $purchase_log[0]['sessionid'];

		// send notification
		$descr = "purchaseid=$purchaseid; sessionid=$sessionid; transact_url=$transact_url; out_summ = $out_summ; inv_id=$inv_id; shp_item=$shp_item; crc=$crc;";
		send_email($descr);

		header("Location: ".$transact_url."&sessionid=".$sessionid);
		exit;
	}
else
	{
		// send notification
		$descr = "Транзакция прошла, но purchase_log[0]['id']=0; out_summ = $out_summ; inv_id=$inv_id; shp_item=$shp_item; crc=$crc;";
		send_email($descr);
		$purchaseid = '0';
		header("Location: http://cartoonbank.ru/?page_id=918&p=0");
		exit;
		
	}

//pokazh($purchaseid,"$purchaseid");

// регистрационная информация (пароль #1)
// registration info (password #1)
$mrh_pass1 = "Atljhjdbx1";


$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item"));

// проверка корректности подписи
// check signature
if ($my_crc != $crc)
{
  echo "bad sign\n";
  exit();
}


// проверка наличия номера счета в истории операций
// check of number of the order info in history of operations
$f=@fopen("order.txt","r+") or die("error");

while(!feof($f))
{
  $str=fgets($f);

  $str_exp = explode(";", $str);
  if ($str_exp[0]=="order_num :$inv_id")
  { 
	echo "Операция прошла успешно\n";
	echo "Operation of payment is successfully completed\n";
	echo $out_summ."<br />";
	echo $inv_id."<br />";
	echo $shp_item."<br />";
	echo $crc."<br />";
  }
}
fclose($f);


function send_email($description)
{
	$headers = "From: bankir@cartoonbank.ru\r\n" .
			   'X-Mailer: PHP/' . phpversion() . "\r\n" .
			   "MIME-Version: 1.0\r\n" .
			   "Content-Type: text/html; charset=utf-8\r\n" .
			   "Content-Transfer-Encoding: 8bit\r\n\r\n";
	
	$mess = $description;

	mail("igor.aleshin@gmail.com", 'успешная транзакция в робокассе', $mess, $headers);
	return;		
}
?>


