<?
// регистрационная информация (пароль #2)
// registration info (password #2)
$mrh_pass2 = "Atljhjdbx2";

//установка текущего времени
//current date
$tm=getdate(time()+9*3600);
$date="$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";

// чтение параметров
// read parameters
$out_summ = $_REQUEST["OutSum"];
$inv_id = $_REQUEST["InvId"];
$shp_item = $_REQUEST["Shp_item"];
$crc = $_REQUEST["SignatureValue"];

$crc = strtoupper($crc);

// send notification
$descr = "out_summ = $out_summ; inv_id=$inv_id; shp_item=$shp_item; crc=$crc;";
send_email($descr);

$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));

// проверка корректности подписи
// check signature
if ($my_crc !=$crc)
{
  echo "bad sign\n";
  exit();
}

// признак успешно проведенной операции
// success
echo "OK$inv_id\n";

// запись в файл информации о прведенной операции
// save order info to file
$f=@fopen("order.txt","a+") or
          die("error");
fputs($f,"order_num :$inv_id;Summ :$out_summ;Date :$date\n");
fclose($f);

function send_email($description)
{
	$headers = "From: bankir@cartoonbank.ru\r\n" .
			   'X-Mailer: PHP/' . phpversion() . "\r\n" .
			   "MIME-Version: 1.0\r\n" .
			   "Content-Type: text/html; charset=utf-8\r\n" .
			   "Content-Transfer-Encoding: 8bit\r\n\r\n";
	
	$mess = $description;

	mail("igor.aleshin@gmail.com", 'транзакция (result) в робокассе', $mess, $headers);
	return;		
}

?>


