<h2>Авторские отчисления</h2>

<?php
$abspath = 'z:/home/localhost/www/';
$abspath_1 = "/home/www/cb/";
$abspath_2 = "/home/www/cb3/";
$filename_acceptance_certificate_pdf = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/artist_acceptance_certificate_pdf.html";

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
if (isset($_GET['m']) && is_numeric($_GET['m']) && $_GET['m']!='0' )
{
	$start_timestamp = mktime(0, 0, 0, $_GET['m'], 1, $year);
	$end_timestamp = mktime(0, 0, 0, ($_GET['m']+1), 1, $year);
}
elseif (isset($_GET['m']) && $_GET['m']==0)
{
	$start_timestamp = mktime(0, 0, 0, 11, 1, $year-1);
	$end_timestamp = mktime(0, 0, 0, ($month+1), 1, $year);
}
else
{
	$start_timestamp = mktime(0, 0, 0, $month, 1, $year);
	$end_timestamp = mktime(0, 0, 0, ($month+1), 1, $year);
}
*/

if (isset($_GET['m']) && is_numeric($_GET['m']) && $_GET['m']!='0' )
{
	$start_timestamp = date('y-m-d',mktime(0, 0, 0, $_GET['m'], 1, $year));
	$end_timestamp = date('y-m-d',mktime(0, 0, 0, $_GET['m']+1, 1, $year));
}
elseif (isset($_GET['m']) && $_GET['m']==0)
{
	$start_timestamp = date('y-m-d',mktime(0, 0, 0, 11, 1, $year-1));
	$end_timestamp = date('y-m-d',mktime(0, 0, 0, $month+1, 1, $year));
}
else
{
	$start_timestamp = date('y-m-d',mktime(0, 0, 0, $month, 1, $year));
	$end_timestamp = date('y-m-d',mktime(0, 0, 0, ($month+1), 1, $year));
}


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
	$this_date = $today[mday].".".$today[mon].".".$today[year];
	$sql = "update wp_options set option_value='".$this_date."' where option_name='invoice_date'";
	$result = $wpdb->query($sql);
	//if (!$result) {die('<br />'.$sql.'<br />Invalid query: ' . mysql_error());}
	$_invoice_date = $this_date;
}


	// Months navigation
		$this_date = getdate();
		$d_month_previous = date('n', mktime(0,0,0,($month-1),28,$year));         // PREVIOUS month of year (1-12)
		$d_monthname_previous = ru_month($d_month_previous, $sklon=false);
		//$d_monthname_previous = date('F', mktime(0,0,0,($month-1),28,$year));     // PREVIOUS Month Long name (July)

		$d_month_previous2 = date('n', mktime(0,0,0,($month-2),28,$year));         // PREVIOUS month of year (1-12)
		$d_monthname_previous2 = ru_month($d_month_previous2, $sklon=false);
		//$d_monthname_previous2 = date('F', mktime(0,0,0,($month-2),28,$year));     // PREVIOUS Month Long name (July)

		$d_month_previous3 = date('n', mktime(0,0,0,($month-3),28,$year));         // PREVIOUS month of year (1-12)
		$d_monthname_previous3 = ru_month($d_month_previous3, $sklon=false);
		//$d_monthname_previous3 = date('F', mktime(0,0,0,($month-3),28,$year));     // PREVIOUS Month Long name (July)


		$d_month_previous4 = date('n', mktime(0,0,0,($month-4),28,$year));         
		$d_monthname_previous4 = ru_month($d_month_previous4, $sklon=false);

		$d_month_previous5 = date('n', mktime(0,0,0,($month-5),28,$year));         
		$d_monthname_previous5 = ru_month($d_month_previous5, $sklon=false);

		$d_month_previous6 = date('n', mktime(0,0,0,($month-6),28,$year));         
		$d_monthname_previous6 = ru_month($d_month_previous6, $sklon=false);



		//echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-author_payments.php&m=0'>Показать 200 последних продаж</a> ";
		
		echo "1) Выберите отчётный месяц: ";
		echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-author_payments.php&m=".$month."'>".ru_month($this_date['mon'],false)."</a> &nbsp;";
		echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-author_payments.php&m=".$d_month_previous."'>".$d_monthname_previous."</a> &nbsp;";
		echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-author_payments.php&m=".$d_month_previous2."'>".$d_monthname_previous2."</a> &nbsp;";
		echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-author_payments.php&m=".$d_month_previous3."'>".$d_monthname_previous3."</a> &nbsp;";
		echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-author_payments.php&m=".$d_month_previous4."'>".$d_monthname_previous4."</a> &nbsp;";
		echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-author_payments.php&m=".$d_month_previous5."'>".$d_monthname_previous5."</a> &nbsp;";
		echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-author_payments.php&m=".$d_month_previous6."'>".$d_monthname_previous6."</a> &nbsp;";



		echo "<form method=post action='#'>";
		echo "2) Акты выполненных работ будут выводится <b>начиная с номера</b> ";
		echo "<input type='text' name='new_invoice_start_number' style='width:45px;' value='".$_invoice_start_number."'>";
		echo "<input type='submit' value=' изменить '>";
		echo "</form>";
/*
		echo "<form method=post action='#'>";
		echo "3) <b>Дата выписки</b> акта ";
		echo "<input type='text' name='new_invoice_date' style='width:85px;' value='".$_invoice_date."'>";
		echo "<input type='submit' value=' изменить '>";
		echo " (По умолчанию сегодняшняя дата. Изменение даты действует временно)</form>";
*/
?>



<?
// If the report month known
if (isset($_GET['m']) && is_numeric($_GET['m']))
{
	$_month = $_GET['m'];

	if ($_month==0)
	{
		echo "<h3>200 последних</h3>";
	}
	else
	{
		echo "<h3 style='color:#FF00FF'>".ru_month(date('n', mktime(0,0,0,($_month),28,$year)),false)."</h3>";
	}

/*
	$sql = "SELECT u.id, b.name as artist
				FROM `wp_purchase_logs` as l, 
					`wp_purchase_statuses` as s, 
					`wp_cart_contents` as c, 
					`wp_product_list` as p,
					`wp_download_status` as st,
					`wp_product_brands` as b,
					`wp_users` as u,
					`wp_usermeta` as um
				WHERE	l.`processed`=s.`id` 
					AND l.id=c.purchaseid 
					AND p.id=c.prodid  
					AND st.purchid=c.purchaseid
					AND p.brand=b.id
					AND u.id = l.user_id
					AND u.id = um.user_id
					AND date BETWEEN '$start_timestamp' AND '$end_timestamp'
					AND um.meta_key = 'description'
					AND l.processed = 5
					AND l.user_id != '106'
					AND st.downloads != '5'
				GROUP BY c.license
				ORDER BY artist ASC";
*/
	$sql = "SELECT id, name, contract, contract_date 
			FROM `wp_product_brands` 
			WHERE active =1
			ORDER BY `name`"; 

	///pokazh($sql);

	$response = $wpdb->get_results($sql,ARRAY_A) ;
	if($response != null)
	  {
		$customer_number = 0;
		foreach($response as $product)
		{
			
			// get all sales for the given month

			$sql = "SELECT date,  c.purchaseid,  p.id as picture_id,  s.name as processed, processed as processed_id, b.name as artist, p.name as title, c.price, totalprice, u.discount, u.display_name, l.user_id, firstname, lastname, email, address, phone, gateway, c.license, st.downloads, st.active,  st.id as downloadid, u.wallet, um.meta_value as smi
				FROM `wp_purchase_logs` as l, 
					`wp_purchase_statuses` as s, 
					`wp_cart_contents` as c, 
					`wp_product_list` as p,
					`wp_download_status` as st,
					`wp_product_brands` as b,
					`wp_users` as u,
					`wp_usermeta` as um
				WHERE	l.`processed`=s.`id` 
					AND l.id=c.purchaseid 
					AND p.id=c.prodid  
					AND st.purchid=c.purchaseid
					AND p.brand=b.id
					AND u.id = l.user_id
					AND u.id = um.user_id
					AND payment_arrived_date BETWEEN '$start_timestamp' AND '$end_timestamp'
					AND um.meta_key = 'company'
					AND l.processed = 5
					AND b.id = '".$product['id']."'
					AND st.downloads != '5'
				GROUP BY c.license
				LIMIT 1000";
									///pokazh($sql);
									///pokazh("product['id']",$product['id']);

				$product_list = $wpdb->get_results($sql,ARRAY_A);






	if($product_list != null)
	{
		$n = 1; // sequence number of the cartoon sold to one customer
		$count = count($product_list); // number of cartoons sold
		$total = 0; // total price with discount
		$the_list = ''; // html list of cartoons with row tags
		$contract_period = $_month.".".date("Y");

		$_invoice_number = $_invoice_start_number + $customer_number;

		echo ("<div id='white' style='background-color:white;padding:2px;margin-bottom:12px;'><div class='t' style='font-size:1.1em;font-weight:bold;background-color:#E6E9FF;padding-left:4px;'>".$product['name'].". <span style='color:silver;'>Контракт № ".$product['contract']." от ".date_format(date_create($product['contract_date']),'d-m-Y')."</span></div>");

	  foreach($product_list as $sales)
		{
			$discount_price = round($sales['price'],0)*((100-round($sales['discount'],0))/100);
			

			echo "<div class='t'><span style='color:silver;'>".date("d.m.y",$sales['date'])."</span> Заказ:".$sales['purchaseid']." №:".$sales['picture_id']." <b>".$sales['smi']."</b> «".stripslashes($sales['title'])."» цена:".round($sales['price'],0)." скидка:".round($sales['discount'],0)." итого:<b>".$discount_price."</b><span style='color:#9900CC;'> Автору: ".round(0.4*($discount_price),0)." руб.</span></div>";
			
			$total = $total + round(0.4*($discount_price),0);
			
			$the_list .= '<tr>
							<td style="padding:2px;text-align:center;">'.$n.'</td>
							<td style="font-style:bold;font-size:1em;padding:2px;padding-left:4px;">#'.$sales["picture_id"].' «'.stripslashes($sales["title"]).'»</td>
							<td style="padding:2px;padding-left:4px;text-align:left;">'.$sales["smi"].'</td>
							<td style="padding:2px;text-align:center;font-size:.8em;">1 шт.</td>
							<td style="padding:2px;text-align:center;">'.$discount_price.'</td>
							<td style="padding:2px;text-align:center;">'.round(0.4*($discount_price),0).'</td>
						</tr>';
			
			$n++;
		}//foreach($product_list as $sales)



		//$_invoice_number = $_invoice_start_number + $customer_number;
		$customer_number ++;
		echo "<div style='color:#9900CC;margin-bottom:10px;'>Всего авторских: <b>".$total."</b></div>";


		// Print acceptance certificate PDF
		$invoice_date = date('d-m-Y',strtotime('-1 second',strtotime('+1 month',strtotime($_month.'/01/'.date('Y').' 00:00:00'))));
		$out = fill_invoice($filename_acceptance_certificate_pdf, $_invoice_number, $invoice_date, $product['name'], $product['bank_attributes'], $the_list, $total, $count, $contract_period, $product['contract'],date_format(date_create($product['contract_date']),'d-m-Y'));
		echo ("<div><form method=post action='http://cartoonbank.ru/ales/tcpdf/examples/artist_acceptance_certificate.php'>
			<input type='submit' value='скачать акт № ".$_invoice_number." выполненных работ (PDF) '>
			<input type='hidden' name='html' value='".htmlspecialchars($out)."'>
			<input type='hidden' name='filename' value='acceptance_certificate_".$_invoice_number."'>
		</form></div></div>");


		//echo "<hr>";

	}//if($product_list != null)


		}// foreach($response as $product)


	}//if($response != null)
}//if (isset($_GET['m']) && is_numeric($_GET['m']))

?>



<?
function fill_invoice($filename, $invoice_number='', $invoice_date='', $smi='',  $client_details='', $product_list='', $total='', $count='', $invoice_period='', $contract_number='', $contract_date='')
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