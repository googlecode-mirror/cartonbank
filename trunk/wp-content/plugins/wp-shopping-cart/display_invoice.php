<?
require_once('../../../wp-config.php');
?>

<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Счет от Картунбанка</title>
</head>
<body>
<a href="<? echo SITEURL;?>"><img src="<? echo SITEURL;?>wp-admin/images/cb-logo.gif" style="border:0;margin-left:20px;"></a>
<div style="margin-left:20px;">Скачайте и распечатайте счёт, нажав кнопку внизу страницы.<br>Для возврата нажмите кнопку "назад" браузера или логотип вверху страницы.</div>
<?php
/*
: Array
(
    [uid] => 1
    [selected_country] => 
    [cart_paid] => 
    [nzshpcrt_cart] => Array
        (
            [1] => cart_item Object
                (
                    [product_id] => 2379
                    [product_variations] => 
                    [quantity] => 1
                    [name] => Крестики-нолики
                    [price] => 250.00
                    [license] => l1_price
                    [author] => Алёшин Игорь
                )

        )

    [total] => 250
    [nzshpcrt_serialized_cart] => a:1:{i:1;O:9:"cart_item":7:{s:10:"product_id";s:4:"2379";s:18:"product_variations";N;s:8:"quantity";i:1;s:4:"name";s:29:"Крестики-нолики";s:5:"price";s:6:"250.00";s:7:"license";s:8:"l1_price";s:6:"author";s:23:"Алёшин Игорь";}}
    [collected_data] => Array
        (
            [1] => Игорь
            [2] => Алёшин
            [3] => igor.aleshin@dataart.com
            [4] => 
            [5] => 
        )

    [checkoutdata] => Array
        (
            [total] => 250
            [collected_data] => Array
                (
                    [1] => Игорь
                    [2] => Алёшин
                    [3] => igor.aleshin@dataart.com
                    [4] => 
                    [5] => 
                )

            [agree] => yes
            [payment_method] => invoice
            [submitwpcheckout] => true
            [submit] =>  Оплатить заказ и скачать файлы 
        )

)
*/
if (!isset($_SESSION['nzshpcrt_cart']) || count($_SESSION['nzshpcrt_cart'])<1)
{
echo "В корзине нет товаров. Счёт выписать невозможно.";
exit;
}
/*
foreach ($_SESSION['nzshpcrt_cart'] as $it)
{

	pokazh($it->product_id);
	pokazh($it->name);
	pokazh($it->price);
	pokazh($it->license);
	pokazh($it->author);
}
*/
$abspath = 'z:/home/localhost/www/';
	$abspath_1 = ROOTDIR;
	$abspath_2 = ROOTDIR;
	$filename = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/invoice_x.html";
	$filename_pdf = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/invoice_pdf_x.html";
global $wpdb;

//pokazh($_SERVER);
//[DOCUMENT_ROOT] => /home/www/cb3/
if (strstr($_SERVER['DOCUMENT_ROOT'],'cb3/'))
{
		$abspath = $abspath_2;
		$params['database'] = 'cartoonbankru';
	}
	if (strstr($_SERVER['DOCUMENT_ROOT'],'cb3/'))
	{
		$abspath = $abspath_2;
		$params['database'] = 'cartoonbankru';
	}
	else if (strstr($_SERVER['DOCUMENT_ROOT'],'cb/')) 
	{
		$abspath = $abspath_1;
		$params['database'] = 'cartoonbankru';
	}
	else if (strstr($_SERVER['DOCUMENT_ROOT'],'/home/www/')) 
	{
		$abspath = $abspath_1;
		$params['database'] = 'cartoonbankru';
	}
	else
	{
		$params['database'] = 'cartoonbankru';
	}
		$params['hostname'] = 'localhost';
		$params['username'] = 'z58365_cbru3';
		$params['password'] = 'greenbat';

	$year = date("Y");
	$month = date("m");

	$this_date = getdate();
/*
if (isset($_GET['m']) && is_numeric($_GET['m']) && $_GET['m']!='0' && isset($_GET['y']) && is_numeric($_GET['y']) && $_GET['y']!='0')
{
	$start_timestamp = mktime(0, 0, 0, $_GET['m'], 1, $_GET['y']);
	$end_timestamp = mktime(0, 0, 0, ($_GET['m']+1), 1, $_GET['y']);
	$start_timestamp2 = date("Y-m-d", mktime(23, 59, 59, $_GET['m'], 1, $_GET['y']));
	$end_timestamp2 = date("Y-m-d", mktime(0, 0, 0, ($_GET['m']+1), 1, $_GET['y']));

}
elseif (isset($_GET['m']) && $_GET['m']==0)
{
	$start_timestamp = mktime(0, 0, 0, 11, 1, $year-1);
	$end_timestamp = mktime(0, 0, 0, ($month+1), 1, $year);
	$start_timestamp2 = date("Y-m-d", mktime(0, 0, 0, 11, 1, $year-1));
	$end_timestamp2 = date("Y-m-d", mktime(0, 0, 0, ($month+1), 1, $year));
}
else
{
	$start_timestamp = mktime(0, 0, 0, $month, 1, $year);
	$end_timestamp = mktime(0, 0, 0, ($month+1), 1, $year);
	$start_timestamp2 = date("Y-m-d", mktime(0, 0, 0, $month, 1, $year));
	$end_timestamp2 = date("Y-m-d", mktime(0, 0, 0, ($month+1), 1, $year));
}
*/
if (isset($_POST['new_invoice_start_number']) && is_numeric($_POST['new_invoice_start_number']))
{
	$_invoice_start_number = trim($_POST['new_invoice_start_number']);

	// update invoice start number in database
	$sql = "update wp_options set option_value=".$_invoice_start_number." where option_name='invoice_x_number'";
	$result = $wpdb->query($sql);
	if (!$result) {die('<br />'.$sql.'<br />Invalid query: ' . mysql_error());}
}
else
{
	// начальный номер счёта 
	$_invoice_start_number = get_option('invoice_x_number');

	// update invoice start number in database
	$sql = "update wp_options set option_value=".($_invoice_start_number+1)." where option_name='invoice_x_number'";
	$result = $wpdb->query($sql);
	if (!$result) {die('<br />'.$sql.'<br />Invalid query: ' . mysql_error());}

}

if (isset($_POST['new_invoice_date']))
{
	// дата сейчас изменена 
	$_invoice_date = trim($_POST['new_invoice_date']);
	// update invoice date in database
	$sql = "update wp_options set option_value = '0' where option_name='invoice_date'";
	$result = $wpdb->query($sql);
	if (!$result) {die('<br />'.$sql.'<br />Invalid query: ' . mysql_error());}

	$sql = "update wp_options set option_value = '".$_invoice_date."' where option_name='invoice_date'";
	$result = $wpdb->query($sql);
	if (!$result) {die('<br />'.$sql.'<br />Invalid query: ' . mysql_error());}
}
else
{
	// дата по умолчанию 
	$today = getdate();
	$this_date = $today['mday'].".".$today['mon'].".".$today['year'];
	$sql = "update wp_options set option_value='".$this_date."' where option_name='invoice_date'";
	$result = $wpdb->query($sql);
	//if (!$result) {die('<br />'.$sql.'<br />Invalid query: ' . mysql_error());}
	$_invoice_date = $this_date;
}



$sql = "SELECT c.id, c.user_id, c.name, c.bank_attributes, c.contract, c.contract_date, u.user_email, u.user_url, u.wallet, u.discount
		FROM  `al_customers` AS c,  `wp_users` AS u
		WHERE u.id = ".$_SESSION['uid'];

//pokazh($sql);

$product_list = $wpdb->get_results($sql,ARRAY_A);

if (isset($product_list[0]['discount']))
{$_discount = round($product_list[0]['discount'],0);}
else{$_discount = '0';}

//pokazh($product_list);
//pokazh($_discount,"discount");

?>
	<style type="text/css">
    <!--
        table.datagrid {
            font-family: Arial;
        }
        .datagrid thead th#header {
            background-color: #ddd;
        }
        .datagrid thead th {
            color: black;
            border-bottom: 1px solid #DBDBDB;
            font-size: 9pt;
            font-family: Verdana;
            text-align: center;
			padding:2px;
			margin:0px;
        }
        .datagrid thead th#header {
            border: 0;
        }
        .datagrid tbody td.altrow {
            /*background-color: #fff;*/
        }
        .datagrid tbody td.altcol
        {
			border-left:1px solid #DBDBDB;
          /*background-color: #eee;*/
        }
        .datagrid tbody td {
            padding: 2px;
			font-size: pt;
			border-bottom: 1px solid #DBDBDB;
			border-left:1px solid #DBDBDB;
			vertical-align:middle;
			text-align:center;
        }
        .datagrid a {
            text-decoration: none;
        }
		.t
		{
			padding:2px;
			border-bottom:1px solid silver;
		}
    // -->
    </style>

<?

	$_invoice_x_number = $_invoice_start_number;
	$customer_number = 1;

	$n = 1; // sequence number of the cartoon sold to one customer
	$count = count($_SESSION['nzshpcrt_cart']); // number of cartoons sold
	$total = 0; // total price with discount
	$the_list = ''; // html list of cartoons with row tags
	$contract_period = $month.".".date("Y");

	//echo ("<div class='t' style='background-color:#CCD2FF;padding-left:4px;margin-top:8px;'>".$product['name']."</div>");
/*
foreach ($_SESSION['nzshpcrt_cart'] as $it)
{
	pokazh($it->product_id);
	pokazh($it->name);
	pokazh($it->price);
	pokazh($it->license);
	pokazh($it->author);
}
*/						
					
	foreach($_SESSION['nzshpcrt_cart'] as $sales)
	{
		$discount_price = round($sales->price*((100-round($_discount,0))/100)); //$sales->price; //
		
		//echo "<div class='t'>".date("d.m.y H:m:s",$sales['date'])." <b>".$sales['artist']."</b> «".stripslashes($sales['title'])."» цена:".round($sales->price,0)." скидка:".round($sales['discount'],0)." итого:<b>".$discount_price."</b></div>";
		
		$total = $total + $discount_price;
		
		//license type
		$lic_type = '';
		switch ($sales->price){
			case 250:
				$lic_type = ' Лицензия ограниченная.';
				break;
			case 500:
				$lic_type = ' Лицензия стандартная.';
				break;
			case 2500:
				$lic_type = ' Лицензия расширенная.';
				break;
		}
		
		$the_list .= '<tr>
						<td style="padding:2px;text-align:center;">'.$n.'</td>
						<td style="font-style:bold;font-size:1em;padding:2px;">«'.stripslashes($sales->name).'» (#'.$sales->product_id.') '.$sales->author.'. '.$lic_type.'</td>
						<td style="padding:2px;text-align:center;">1шт.</td>
						<td style="padding:2px;text-align:center;">'.$discount_price.'</td>
						<td style="padding:2px;text-align:center;">'.$discount_price.'</td>
					</tr>';

		$n++;
	}//foreach($product_list as $sales)
					
		// print invoice:
		
	//$out = fill_invoice($filename, $_invoice_x_number, $_invoice_date,  $product['name'], $product['bank_attributes'], $the_list, $total, $count, $contract_period, $product['contract'],date_format(date_create($product['contract_date']),'d-m-Y'));

	$out = fill_invoice($filename, "x".$_invoice_x_number, $_invoice_date,  "", "", $the_list, $total, $count, $contract_period, "" ,$this_date,$_discount);

		echo "<div id='invoice' style='background: white url(<? echo SITEURL;?>img/mg_stamp.gif) no-repeat; background-size: 21%; background-position: 87% 100%; margin:20px; padding:8px;width: 210mm; border: 1px #D6D6D6 solid; font-size: 11pt;'>";
		echo $out;
		echo "</div>";

		send_mail($out);

	// Print invoice PDF
	$out = fill_invoice($filename_pdf, "x".$_invoice_x_number, $_invoice_date, "", "", $the_list, $total, $count, $contract_period, "",$this_date,$_discount);
			echo ("<div style='margin-left:20px;'><form method=post action='<? echo SITEURL;?>ales/tcpdf/examples/ales.php'>
					<input type='submit' value='скачать счёт в формате PDF для распечатывания' style='padding:8px;background-color:#FFFF99;'>
					<input type='hidden' name='html' value='".htmlspecialchars($out)."'>
					<input type='hidden' name='filename' value='invoice_".$_invoice_x_number."'>
				</form></br></br></div>");



	$_invoice_x_number = $_invoice_start_number + $customer_number;
	$customer_number ++;


function fill_invoice($filename, $invoice_x_number='', $invoice_date='', $smi='',  $client_details='', $product_list='', $total='', $count='', $invoice_period='', $contract_number='', $contract_date='', $_discount=0)
{
	if ($invoice_x_number==''){$invoice_x_number='____';}
	$today = getdate();
	if ($invoice_date==''){$invoice_date = $today['mday'].".".$today['mon'].".".$today['year'];}
	if ($client_details==''){$client_details = '<br><br><br>';}
	if ($product_list=='')
		{
		$product_list = '<tr><td colspan=6> нет </td></tr>';
		}
	if ($total==''){$total=0;}
	if ($count==''){$count=0;}

		$content=loadFile($filename); 

		$total_rub_text = "<b>".capitalizefirst(num2str($total))."</b>";

	// replace placeholders
		$content = str_replace ('{invoice_number}',$invoice_x_number,$content);
		$content = str_replace ('{invoice_date}',$invoice_date,$content);
		$content = str_replace ('{smi}',$smi,$content);
		$content = str_replace ('{client_details}',$client_details,$content);
		$content = str_replace ('{product_list}',$product_list,$content);
		$content = str_replace ('{total}',$total,$content);
		$content = str_replace ('{count}',$count,$content);
		$content = str_replace ('{total_rub_text}',$total_rub_text,$content);
		$content = str_replace ('{invoice_period}',$invoice_period,$content);
		$content = str_replace ('{contract_number}',$contract_number,$content);
		$content = str_replace ('{contract_date}',$contract_date,$content);
		$content = str_replace ('{discount}',$_discount,$content);

		// output content
		//pokazh($content);

	return $content;
}


function loadFile($sFilename, $sCharset = 'UTF-8')
{
    if (floatval(phpversion()) >= 4.3) {
        $sData = file_get_contents($sFilename);
    } else {
        if (!file_exists($sFilename)) return -3;
        $rHandle = fopen($sFilename, 'r');
        if (!$rHandle) return -2;

        $sData = '';
        while(!feof($rHandle))
            $sData .= fread($rHandle, filesize($sFilename));
        fclose($rHandle);
    }
    if ($sEncoding = mb_detect_encoding($sData, 'auto', true) != $sCharset)
        $sData = mb_convert_encoding($sData, $sCharset, $sEncoding);
    return $sData;
}

function capitalizefirst($string)
{
	$string = mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);

	//$string = mb_convert_case($str, MB_CASE_UPPER, "UTF-8");

	return $string;
}

function num2str($inn, $stripkop=false) {
    $nol = 'ноль';
    $str[100]= array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот', 'восемьсот','девятьсот');
    $str[11] = array('','десять','одиннадцать','двенадцать','тринадцать', 'четырнадцать','пятнадцать','шестнадцать','семнадцать', 'восемнадцать','девятнадцать','двадцать');
    $str[10] = array('','десять','двадцать','тридцать','сорок','пятьдесят', 'шестьдесят','семьдесят','восемьдесят','девяносто');
    $sex = array(
        array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),// m
        array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять') // f
    );
    $forms = array(
        array('копейка', 'копейки', 'копеек', 1), // 10^-2
        array('рубль', 'рубля', 'рублей',  0), // 10^ 0
        array('тысяча', 'тысячи', 'тысяч', 1), // 10^ 3
        array('миллион', 'миллиона', 'миллионов',  0), // 10^ 6
        array('миллиард', 'миллиарда', 'миллиардов',  0), // 10^ 9
        array('триллион', 'триллиона', 'триллионов',  0), // 10^12
    );
    $out = $tmp = array();
    // Поехали!
    $tmp = explode('.', str_replace(',','.', $inn));
    $rub = number_format($tmp[ 0], 0,'','-');
    if ($rub== 0) $out[] = $nol;
    // нормализация копеек
    $kop = isset($tmp[1]) ? substr(str_pad($tmp[1], 2, '0', STR_PAD_RIGHT), 0,2) : '00';
    $segments = explode('-', $rub);
    $offset = sizeof($segments);
    if ((int)$rub== 0) { // если 0 рублей
        $o[] = $nol;
        $o[] = morph( 0, $forms[1][ 0],$forms[1][1],$forms[1][2]);
    }
    else {
        foreach ($segments as $k=>$lev) {
            $sexi= (int) $forms[$offset][3]; // определяем род
            $ri = (int) $lev; // текущий сегмент
            if ($ri== 0 && $offset>1) {// если сегмент==0 & не последний уровень(там Units)
                $offset--;
                continue;
            }
            // нормализация
            $ri = str_pad($ri, 3, '0', STR_PAD_LEFT);
            // получаем циферки для анализа
            $r1 = (int)substr($ri, 0,1); //первая цифра
            $r2 = (int)substr($ri,1,1); //вторая
            $r3 = (int)substr($ri,2,1); //третья
            $r22= (int)$r2.$r3; //вторая и третья
            // разгребаем порядки
            if ($ri>99) $o[] = $str[100][$r1]; // Сотни
            if ($r22>20) {// >20
                $o[] = $str[10][$r2];
                $o[] = $sex[ $sexi ][$r3];
            }
            else { // <=20
                if ($r22>9) $o[] = $str[11][$r22-9]; // 10-20
                elseif($r22> 0) $o[] = $sex[ $sexi ][$r3]; // 1-9
            }
            // Рубли
            $o[] = morph($ri, $forms[$offset][ 0],$forms[$offset][1],$forms[$offset][2]);
            $offset--;
        }
    }
    // Копейки
    if (!$stripkop) {
        $o[] = $kop;
        $o[] = morph($kop,$forms[ 0][ 0],$forms[ 0][1],$forms[ 0][2]);
    }
    return preg_replace("/\s{2,}/",' ',implode(' ',$o));
}
 
/**
 * Склоняем словоформу
 */
function morph($n, $f1, $f2, $f5) {
    $n = abs($n) % 100;
    $n1= $n % 10;
    if ($n>10 && $n<20) return $f5;
    if ($n1>1 && $n1<5) return $f2;
    if ($n1==1) return $f1;
    return $f5;
}

function send_mail($votecontent)
{
	$votecontent = "<div id='invoice' style='margin:20px; padding:8px; width: 210mm; border: 1px #D6D6D6 solid; font-size: 11pt;'>".$votecontent."</div><div>Выписан счёт на имя: ".$_SESSION['collected_data'][1]." ".$_SESSION['collected_data'][2]." [#".$_SESSION['uid']."] ".$_SESSION['collected_data'][3]." ".$_SESSION['collected_data'][4]." ".$_SESSION['collected_data'][5]."</div>";

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	$headers .= 'From: CartoonBank Robot <cartoonbank.ru@gmail.com>' . "\r\n";
	mail("igor.aleshin@gmail.com","Новый счёт от Картунбанка",$votecontent,$headers);
}
function ru_month ($month, $sklon=false)
{
	switch ($month){
		case 1:
			if ($sklon) return 'январе';
			else return 'январь';
			break;
		case 2:
			if ($sklon) return 'феврале';
			else return 'февраль';
			break;
		case 3:
			if ($sklon) return 'марте';
			else return 'март';
			break;
		case 4:
			if ($sklon) return 'апреле';
			else return 'апрель';
			break;
		case 5:
			if ($sklon) return 'мае';
			else return 'май';
			break;
		case 6:
			if ($sklon) return 'июне';
			else return 'июнь';
			break;
		case 7:
			if ($sklon) return 'июле';
			else return 'июль';
			break;
		case 8:
			if ($sklon) return 'августе';
			else return 'август';
			break;
		case 9:
			if ($sklon) return 'сентябре';
			else return 'сентябрь';
			break;
		case 10:
			if ($sklon) return 'октябре';
			else return 'октябрь';
			break;
		case 11:
			if ($sklon) return 'ноябре';
			else return 'ноябрь';
			break;
		case 12:
			if ($sklon) return 'декабре';
			else return 'декабрь';
			break;
	}

}

?>
</body>
</html>