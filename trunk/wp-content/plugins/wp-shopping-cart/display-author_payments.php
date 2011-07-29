<h2>Авторские отчисления</h2>

<?php
$abspath = 'z:/home/localhost/www/';
$abspath_1 = "/home/www/cb/";
$abspath_2 = "/home/www/cb3/";
$filename_acceptance_certificate_pdf = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/artist_acceptance_certificate_pdf.html";
$filename_acceptance_certificate_nostamp_pdf = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/artist_acceptance_certificate_nostamp_pdf.html";
$total_all = 0; // all artists total summ

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


	if (isset($_POST['approve']) && $_POST['approve'] == 1 && isset($_POST['sql']))
	{
		$sql = stripslashes($_POST['sql']);
		$result = $wpdb->query($sql);
		if (!$result) {die('<br />'.$sql.'<br />Invalid insert query: ' . mysql_error());}
		//pokazh (stripslashes(htmlspecialchars_decode($_POST['sql'], ENT_QUOTES)),"decoded sql");

		// set max act number

				$sql = "select max(act_number) as max from artist_payments";
				$result = $wpdb->get_results($sql,ARRAY_A);
				if ($result)
				{
					// get current max
					$new_max_act_number = $result[0]['max'] +1;
					//pokazh($new_max_act_number);
					// set new max
					$sql = "update wp_options set option_value = ".$new_max_act_number." where option_name = 'artist_act_number'";
					$result = $wpdb->query($sql);
					if (!$result) {die('<br />'.$sql.'<br />Can not set new max act number: ' . mysql_error());}
				}
	}



$year = date("Y");
$month = date("m");

$this_date = getdate();

// dates
if (isset($_GET['m']) && is_numeric($_GET['m']) && $_GET['m']!='0' && isset($_GET['y']) && is_numeric($_GET['y']) && $_GET['y']!='0')
{
	$start_timestamp = date('y-m-d',mktime(0, 0, 0, $_GET['m'], 1, $_GET['y']));
	$end_timestamp = date('y-m-d',mktime(0, 0, 0, $_GET['m']+1, 0, $_GET['y']));
}
elseif (isset($_GET['m']) && $_GET['m']==0)
{
	$start_timestamp = date('y-m-d',mktime(0, 0, 0, 11, 1, $_GET['y']-1));
	$end_timestamp = date('y-m-d',mktime(0, 0, 0, $month+1, 0, $_GET['y']));
}
else
{
	$start_timestamp = date('y-m-d',mktime(0, 0, 0, $month, 1, $_GET['y']));
	$end_timestamp = date('y-m-d',mktime(0, 0, 0, ($month+1), 0, $_GET['y']));
}


if (isset($_POST['new_invoice_start_number']) && is_numeric($_POST['new_invoice_start_number']))
{
	$_invoice_start_number = trim($_POST['new_invoice_start_number']);

	// update invoice start number in database
	$sql = "update wp_options set option_value=".$_invoice_start_number." where option_name='artist_act_number'";
	$result = $wpdb->query($sql);
	if (!$result) {die('<br />'.$sql.'<br />Invalid query: ' . mysql_error());}
}
else
{
	// начальный номер счёта 
	$_invoice_start_number = get_option('artist_act_number');
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
	$this_date = $today[mday].".".$today[mon].".".$today[year];
	$sql = "update wp_options set option_value='".$this_date."' where option_name='invoice_date'";
	$result = $wpdb->query($sql);
	//if (!$result) {die('<br />'.$sql.'<br />Invalid query: ' . mysql_error());}
	$_invoice_date = $this_date;
}


	// Months navigation
		$this_date = getdate();
		$year = date("Y");
		$month = date("m");


		//echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-author_payments.php&m=0'>Показать 200 последних продаж</a> ";
		
		echo "1) Выберите отчётный месяц: ";


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

			//pokazh($i.': '.$mmonth.' - '.$y);
			echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-author_payments.php&y=$y&m=". date('n', mktime(0,0,0,($mmonth-$i),28,$y))."'>".ru_month(date('n', mktime(0,0,0,($mmonth-$i),28,$y)), $sklon=false)." ".$y."</a> ";
		}



		echo "<form method=post action='#'>";
		echo "2) Акты выполненных работ будут выводится <b>начиная с номера</b> ";
		echo "<input type='text' name='new_invoice_start_number' style='width:45px;' value='".$_invoice_start_number."'>";
		echo "<input type='submit' value=' изменить '>";
		echo "</form>";
?>



<?
// If the report month known
if (isset($_GET['m']) && is_numeric($_GET['m']))
{
	$_month = $_GET['m'];
	$_year = $_GET['y'];

	if (isset($_GET['m']) && $_month==0)
	{
		echo "<h3 style='color:#FF00FF'>200 последних</h3>";
	}
	else if (isset($_GET['m']))
	{
		echo "<h1 style='color:#FF00FF'>".ru_month(date('n', mktime(0,0,0,($_month),28,$_year)))." $_year</h1>";
	}
	else
	{
		echo "<h1 style='color:#FF00FF'>".ru_month(date('m'))." $_year</h1>";
	}

	$sql = "SELECT id, name, contract, contract_date, rezident 
			FROM `wp_product_brands` 
			WHERE active =1
			ORDER BY `name`"; 

	$response = $wpdb->get_results($sql,ARRAY_A) ;
	if($response != null)
	  {
		$customer_number = 0;
		foreach($response as $artist)
		{
			
			// get all sales for the given month
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
				AND u.id = l.user_id
				AND payment_arrived_date BETWEEN '$start_timestamp' AND '$end_timestamp'
				AND l.processed = 5
				AND l.user_id != '106'
				AND b.id = '".$artist['id']."'
				AND st.downloads != '5'
			GROUP BY c.license
			order by purchaseid";

				///pokazh("product['id']",$artist['id']);

			$product_list = $wpdb->get_results($sql,ARRAY_A);






	if($product_list != null)
	{
									///pokazh($sql);
		$n = 1; // sequence number of the cartoon sold to one customer
		$count = count($product_list); // number of cartoons sold
		$total = 0; // total price with discount
		$the_list = ''; // html list of cartoons with row tags
		$contract_period = $_month.".".date("Y");
		$_year_month = date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime($_month.'/01/'.$_year.' 00:00:00'))))." 00:00:00";


		// 
				// make sure the payment record not exists
				$sql_artist_payments = "select id, act_number from artist_payments where payment_date = '".$_year_month."' and artist_id = ". $artist['id'];
				$result_artist_payments = $wpdb->get_results($sql_artist_payments,ARRAY_A) ;

//pokazh($result_artist_payments );

		if ($result_artist_payments) 
		{
			$_invoice_number = $result_artist_payments[0]['act_number'];
		}
		else
		{
			$_invoice_number = $_invoice_start_number + $customer_number;
		}


		echo ("<div id='white' style='background-color:white;padding:2px;margin-bottom:12px;'><div class='t' style='font-size:1.1em;font-weight:bold;background-color:#E6E9FF;padding-left:4px;'>".$artist['name'].". <span style='color:silver;'>Контракт № ".$artist['contract']." от ".date_format(date_create($artist['contract_date']),'d-m-Y')."</span></div>");

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
					$sales['actual_money'] = ($sales['totalprice']*.95); // average money received from Robokassa
				}
				else
				{
					$sales['actual_money'] = $sales['actual_money']; //assign corrected by accountant discount to Robokassa purchase
				}

				$discount_price = $sales['actual_money']; 
			}



			echo "<div class='t' style='font-size:0.8em;'><span style='color:silver;'>".$sales['datetime']."</span> Заказ:".$sales['purchaseid']." №:".$sales['picture_id']." <b>".$sales['smi']."</b> <i>".$sales['firstname']." ".$sales['lastname']."</i> «".stripslashes($sales['title'])."» цена:".round($sales['price'],0)." скидка:".round($sales['discount'],0)." итого:<b>".$discount_price."</b><span style='color:#9900CC;'> Автору: ".round(0.4*($discount_price),0)." руб.</span></div>";
			
			$total = $total + round(0.4*($discount_price),0);
			
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
		echo "<div style='color:#9900CC;margin-bottom:10px;'>Всего авторских: <b>".$total."</b></div>";



				$_cartoons_amount = $n-1;


				//pokazh("insert into artist_payments (artist_id, payment_date, reward, cartoons_sold) values (".$artist['id'].", '".$_year_month."', ".$total.", ".$_cartoons_amount.")");


				// make sure the payment record not exists
				//$sql_artist_payments = "select id from artist_payments where payment_date = '".$_year_month."' and artist_id = ". $artist['id'];
				//$result_artist_payments = $wpdb->query($sql_artist_payments);

				if ($result_artist_payments) 
				{
						echo "<div style='width:80px; text-align:center; padding:6px; background-color:silver; color:#006600;'>Утверждено</div>";
				}
				else
				{
//					pokazh($artist['rezident'] );
					if ($artist['rezident'] == '1')
					{
						$tax_ndfl = ($total * 0.7) * 0.13; // проф вычет с резидентов
					}
					else
					{
						$tax_ndfl = $total * 0.3; // проф вычет с нерезидентов
					}

					$reward_topay = $total - $tax_ndfl;

						$sql_encoded = htmlspecialchars("insert into artist_payments (reward_to_pay, tax_ndfl, act_number, artist_id, payment_date, reward, cartoons_sold) values (".$reward_topay.", ".$tax_ndfl.", ".$_invoice_number.", ".$artist['id'].", '".$_year_month."', ".$total.", ".$_cartoons_amount.")", ENT_QUOTES);
						//pokazh($sql_encoded,"sql_encoded");
						echo "<div><form method=post action='#'>
							<input type='submit' value='Утвердить' style='background-color:#FF9966;'>
							<input type='hidden' name='approve' value='1'>
							<input type='hidden' name='sql' value='".$sql_encoded."'>
						</form></div>";
				}


		// Print acceptance certificate PDF
		$invoice_date = date('d-m-Y',strtotime('-1 second',strtotime('+1 month',strtotime($_month.'/01/'.date('Y').' 00:00:00'))));
		$out = fill_invoice($filename_acceptance_certificate_pdf, $_invoice_number, $invoice_date, $artist['name'], $artist['bank_attributes'], $the_list, $total, $count, $contract_period, $artist['contract'],date_format(date_create($artist['contract_date']),'d-m-Y'));
		echo ("<div><form method=post action='http://cartoonbank.ru/ales/tcpdf/examples/artist_acceptance_certificate.php'>
			<input type='submit' value='скачать акт № ".$_invoice_number." выполненных работ (PDF) '>
			<input type='hidden' name='html' value='".htmlspecialchars($out)."'>
			<input type='hidden' name='filename' value='acceptance_certificate_".$_invoice_number."'>
		</form></div>");
		$out_nostamp = fill_invoice($filename_acceptance_certificate_nostamp_pdf, $_invoice_number, $invoice_date, $artist['name'], $artist['bank_attributes'], $the_list, $total, $count, $contract_period, $artist['contract'],date_format(date_create($artist['contract_date']),'d-m-Y'));
		echo ("<div><form method=post action='http://cartoonbank.ru/ales/tcpdf/examples/artist_acceptance_certificate.php'>
			<input type='submit' value='скачать акт № ".$_invoice_number." выполненных работ (PDF) без печати'>
			<input type='hidden' name='html' value='".htmlspecialchars($out_nostamp)."'>
			<input type='hidden' name='filename' value='acceptance_certificate_".$_invoice_number."'>
		</form></div></div>");








		$total_all = $total_all + $total;
		$total =0;

		//echo "<hr>";
	}//if($product_list != null)


		}// foreach($response as $artist)



	}//if($response != null)
}//if (isset($_GET['m']) && is_numeric($_GET['m']))


?>
<div style="padding:6px;background-color:#CCFF99;">
Общая сумма выплаченных авторских в этом месяце: <b><?echo $total_all;?></b> руб.
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