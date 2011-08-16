<h2>Заработано</h2>
<?php
$abspath = 'z:/home/localhost/www/';
$abspath_1 = "/home/www/cb/";
$abspath_2 = "/home/www/cb3/";
$filename_acceptance_certificate_pdf = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/artist_acceptance_certificate_pdf.html";
$filename_acceptance_certificate_nostamp_pdf = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/artist_acceptance_certificate_nostamp_pdf.html";
$total_all = 0; // all artists total summ
$counter_sold_year = 0; // how many items sold in a year
$counter_sold_month = 0; // how many sold in a month

global $wpdb;

//pokazh($user,"user");
//pokazh($current_user,"currentuser");
//pokazh($current_user->id,"id");
//pokazh($current_user->wp_user_level,"wp_user_level");
//pokazh($_SESSION,"SESSION");
//pokazh($_SESSION[collected_data]->'2',"lastname");
//pokazh($_SESSION,"SESSION");
//pokazh($_POST,"POST");
//pokazh($wpdb,"wpdb");

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

$customer_number = 0;

$this_date = getdate();

//////// artist
//////// who is logged on?
$sql = "SELECT id FROM `wp_product_brands` WHERE user_id = ".$current_user->id;
$userbrand = $wpdb->get_results($sql,ARRAY_A);
if($userbrand != null)
{
	$userbrandid = $userbrand[0]['id']; // logged on user brand
}
else
{
	$userbrandid = 0;
}


/*
	if ($_brand == '' | ($_brand != $userbrandid))
	{
		echo ('Извините, у вас недостаточно полномочий для доступа к этой странице.');
		exit();
	}
	//pokazh($userbrandid,"userbrandid");
	//pokazh($_brand,"_brand"); //8
	//pokazh($product['id']);
	//pokazh($current_user->id,"current_user->id"); //16
	//pokazh($current_user->wp_user_level,"current_user->wp_user_level"); //2
	//pokazh($_brand); //8

*/

/// login for admins
if (($current_user->wp_user_level == 10) && isset($_GET['brand']) && is_numeric($_GET['brand']))
{
	$_brand = $_GET['brand'];
}
else
{
	$_brand = $userbrandid;
}

?>

<b>Внимание. В полях "Автору" и "Общая сумма авторских отчислений" указано начисленное авторское вознаграждение до вычета налога НДФЛ</b><br><br>

<?

if (isset($_POST['new_invoice_start_number']) && is_numeric($_POST['new_invoice_start_number']))
{
	$_invoice_start_number = trim($_POST['new_invoice_start_number']);

	// update invoice start number in database
	$sql = "update wp_options set option_value=".$_invoice_start_number." where option_name='invoice_number'";
	$result = $wpdb->query($sql);
	if (!$result) {die('<br />'.$sql.'<br />Invalid query: ' . mysql_error());}
}
else
{
	// начальный номер счёта 
	$_invoice_start_number = get_option('invoice_number');
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



	// Months navigation
		$this_date = getdate();
		$year = date("Y");
		$month = date("m");
		$mmonth = intval($month);
		for ($i = 0; $i <= 11; $i++) {

			if ($mmonth-$i <= 0)
				{
					$y = $year-1;
				}
			else
				{
					$y = $year;
				}
		}


//todo
//isset($_GET['brand'])?$_brand = $_GET['brand']:$_brand = 0;

	$sql = "SELECT id, name, contract, contract_date 
			FROM `wp_product_brands` 
			WHERE id = $_brand
			ORDER BY `name`"; 

	$response = $wpdb->get_results($sql,ARRAY_A) ;
	if($response != null)
	{
		$product = $response[0];
		//pokazh($product);
	}
/*
if ($product['id'] != $current_user->id & $current_user->wp_user_level < 10)
	{
		echo ('Извините, у вас недостаточно полномочий для доступа к этой странице.');
		exit();
	}
*/

/// get dates for payment acts
	$sql = "select payment_date, artist_id, act_number from artist_payments where artist_id=".$_brand." order by payment_date desc";
	$result = $wpdb->get_results($sql,ARRAY_A);
	 foreach($result as $r)
		{
			$payday = $r['payment_date'];
			$list = explode('-', $payday);
			$_year = $list[0];
			$_month = $list[1];
		}



echo ("<div class='t' style='padding:4px;font-size:1.1em;font-weight:bold;background-color:#E6E9FF;padding-left:4px;'>".$product['name'].". <span style='color:silver;'>Договор № ".$product['contract']." от ".date_format(date_create($product['contract_date']),'d-m-Y')."</span></div>");

	/// get dates for payment acts
	$sql = "select payment_date, artist_id, act_number from artist_payments where artist_id=".$_brand." order by payment_date desc";
	$result = $wpdb->get_results($sql,ARRAY_A);
foreach($result as $r)
	{
			$payday = $r['payment_date'];
			$list = explode('-', $payday);
			$_year = $list[0];
			$_month = $list[1];
			$_invoice_number = $r['act_number'];
			//pokazh($_invoice_number);

	$start_timestamp = date('y-m-d',mktime(0, 0, 0, $_month, 1, $_year));
	$end_timestamp = date('y-m-d',mktime(0, 0, 0, ($_month+1), 0, $_year));

	$sql = "SELECT cust.name as smi, date, st.datetime, c.purchaseid,  p.id as picture_id,  s.name as processed, processed as processed_id, b.name as artist, p.name as title, c.price, totalprice, u.discount, u.display_name, l.user_id, firstname, lastname, email, address, phone, gateway, c.license, c.actual_money, st.downloads, st.active,  st.id as downloadid, u.wallet 
	FROM `wp_purchase_logs` as l, 
		`wp_purchase_statuses` as s, 
		`wp_cart_contents` as c, 
		`wp_product_list` as p,
		`wp_download_status` as st,
		`wp_product_brands` as b,
		`wp_users` as u
	LEFT JOIN `al_customers` as cust
	ON u.id = cust.user_id
	WHERE	l.`processed`=s.`id` 
		AND l.id=c.purchaseid 
		AND p.id=c.prodid  
		AND st.purchid=c.purchaseid
		AND p.brand=b.id
		AND p.brand = '".$_brand."'
		AND u.id = l.user_id
		AND l.processed = 5
		AND l.user_id != '106'
		AND st.downloads != '5'
		AND payment_arrived_date BETWEEN '$start_timestamp' AND '$end_timestamp'
	GROUP BY c.license
	order by purchaseid";


	$product_list = $wpdb->get_results($sql,ARRAY_A);

	if($product_list != null)
	{
		echo "<h1 style='color:#FF00FF'>".ru_month($_month)." $_year</h1>";
									///pokazh($sql);
		$n = 1; // sequence number of the cartoon sold to one customer
		$count = count($product_list); // number of cartoons sold
		$total = 0; // total price with discount
		$the_list = ''; // html list of cartoons with row tags
		$contract_period = $_month.".".date("Y");

		//$_invoice_number = $_invoice_start_number + $customer_number;


	  foreach($product_list as $sales)
		{
			$discount_price = round($sales['price'],0)*((100-round($sales['discount'],0))/100);
			

			//robokassa
			//actual_money
			if ($sales['gateway']=='robokassa')
			{
				$sales['discount'] = 5; // average default discount for Robokassa

				if ($sales['actual_money']=='')
				{
					$sales['actual_money'] = ($sales['price']*.95); // average money received from Robokassa
				}
				else
				{
					$sales['actual_money'] = $sales['actual_money']; //assign corrected by accountant discount to Robokassa purchase
				}

				$discount_price = $sales['actual_money']; 
			}


			echo "<div class='t' style='font-size:0.8em;'><span style='color:silver;'>".$sales['datetime']."</span> Заказ:".$sales['purchaseid']." <b>".$sales['smi']."</b> <i>".$sales['firstname']." ".$sales['lastname']."</i> <a href='http://cartoonbank.ru/?page_id=29&cartoonid=".$sales['picture_id']."'>№".$sales['picture_id']." «".stripslashes($sales['title'])."»</a> цена:".round($sales['price'],0)." скидка:".round($sales['discount'],0)."% итого:<b>".$discount_price."</b><span style='color:#9900CC;'> Автору: ".round(0.4*($discount_price),0)." руб.</span></div>";
			
			$total = $total + round(0.4*($discount_price),0);
			$counter_sold_month++;
			
			$the_list .= '<tr style="font-size:8pt">
							<td style="padding:2px;text-align:center;">'.$n.'</td>
							<td style="font-style:bold;font-size:1em;padding:2px;padding-left:4px;">#'.$sales["picture_id"].' «'.stripslashes($sales["title"]).'»</td>
							<td style="padding:2px;padding-left:4px;text-align:left;">'.$sales["smi"].' '.$sales['firstname'].' '.$sales['lastname'].'</td>
							<td style="padding:2px;text-align:center;font-size:.8em;">1 шт.</td>
							<td style="padding:2px;text-align:center;">'.$discount_price.'</td>
							<td style="padding:2px;text-align:center;">'.round(0.4*($discount_price),0).'</td>
						</tr>';
			
			$n++;
		}//foreach($product_list as $sales)

		//$_invoice_number = $_invoice_start_number + $customer_number;
		$customer_number ++;
		echo "<div style='color:#9900CC;margin-bottom:10px;'>Всего продано $counter_sold_month шт. на сумму: <b>".$total."</b> руб.</div>";


		// Print acceptance certificate PDF
		$invoice_date = date('d-m-Y',strtotime('-1 second',strtotime('+1 month',strtotime($_month.'/01/'.date('Y').' 00:00:00'))));

		$_bank_attributes = '';
		if (isset($product['bank_attributes']))
		{
			$_bank_attributes = $product['bank_attributes'];
		}
		
		
		
		//$out = fill_invoice($filename_acceptance_certificate_pdf, $_invoice_number, $invoice_date, $product['name'], $_bank_attributes, $the_list, $total, $count, $contract_period, $product['contract'],date_format(date_create($product['contract_date']),'d-m-Y'));
		$out = fill_invoice($filename_acceptance_certificate_pdf, $_invoice_number, $invoice_date, $product['name'], $_bank_attributes, $the_list, $total, $count, $contract_period, $product['contract'],date_format(date_create($product['contract_date']),'d-m-Y'));

		echo ("<div><form method=post action='http://cartoonbank.ru/ales/tcpdf/examples/artist_acceptance_certificate.php'>
			<!-- <input type='submit' value='скачать акт № ".$_invoice_number." выполненных работ (PDF) '> -->
			<input type='submit' value='скачать акт выполненных работ № ".$_invoice_number." (PDF) '>
			<input type='hidden' name='html' value='".htmlspecialchars($out)."'>
			<input type='hidden' name='filename' value='acceptance_certificate_".$_invoice_number."'>
		</form></div>");

		$out_nostamp = fill_invoice($filename_acceptance_certificate_nostamp_pdf, $_invoice_number, $invoice_date, $product['name'], $_bank_attributes, $the_list, $total, $count, $contract_period, $product['contract'],date_format(date_create($product['contract_date']),'d-m-Y'));
		/*
		echo ("<div><form method=post action='http://cartoonbank.ru/ales/tcpdf/examples/artist_acceptance_certificate.php'>
			<input type='submit' value='скачать акт № ".$_invoice_number." выполненных работ (PDF) без печати'>
			<input type='hidden' name='html' value='".htmlspecialchars($out_nostamp)."'>
			<input type='hidden' name='filename' value='acceptance_certificate_".$_invoice_number."'>
		</form></div></div>");
		*/
		$total_all = $total_all + $total;
		$counter_sold_year = $counter_sold_year + $counter_sold_month;
		$counter_sold_month = 0;
		$total =0;

	}//if($product_list != null)
}//foreach payment date //////for ($_month = 1; $_month <= 12; $_month++)  
//}// for years

?>
<div style="margin-top:12px; padding:6px;background-color:#66CC00">
Общая сумма авторских отчислений: <b><?echo $total_all;?></b> руб.<br>Продано рисунков: <?echo $counter_sold_year;?> (деньги получены Картунбанком).
</div>
<?
function fill_invoice($filename, $invoice_number='', $invoice_date='', $smi='', $client_details='', $product_list='', $total='', $count='', $invoice_period='', $contract_number='', $contract_date='')
{
	if ($invoice_number==''){$invoice_number='____';}
	$today = getdate();
	if ($invoice_date==''){$invoice_date = $today[mday].".".$today[mon].".".$today[year];}
	if ($client_details==''){$client_details = 'данные покупателя неизвестны';}
	if ($product_list=='')
		{
		$product_list = '<tr><td colspan=6> нет </td></tr>';
		}
	if ($total==''){$total=0;}
	if ($count==''){$count=0;}

		$content=loadFile($filename); 

		$total_rub_text = "<b>".capitalizefirst(num2str($total))."</b>";

	// replace placeholders
		$content = str_replace ('{invoice_number}',$invoice_number,$content);
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
    //if ($sEncoding = mb_detect_encoding($sData, 'auto', true) != $sCharset)
    //    $sData = mb_convert_encoding($sData, $sCharset, $sEncoding);
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
	$votecontent = "<div id='invoice' style='margin:20px; padding:8px; width: 210mm; border: 1px #D6D6D6 solid; font-size: 11pt;'>".$votecontent."</div>";

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