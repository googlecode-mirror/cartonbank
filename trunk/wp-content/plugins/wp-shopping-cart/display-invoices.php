<?php
$abspath = 'z:/home/localhost/www/';
	$abspath_1 = "/home/www/cb/";
	$abspath_2 = "/home/www/cb3/";
	$filename = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/invoice.html";
	$filename_pdf = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/invoice_pdf.html";
	$filename_nostamp_pdf = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/invoice_nostamp_pdf.html";
	$filename_acceptance_certificate_pdf = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/acceptance_certificate_pdf.html";
	$filename_acceptance_certificate_nostamp_pdf = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/acceptance_certificate_nostamp_pdf.html";

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



$sql = "SELECT c.id, c.user_id, c.name, c.bank_attributes, c.contract, c.contract_date, u.user_email, u.user_url, u.wallet, u.discount
		FROM  `al_customers` AS c,  `wp_users` AS u
		WHERE u.id = c.user_id ORDER BY c.contract";
$product_list = $wpdb->get_results($sql,ARRAY_A);

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

<h2>Счета</h2>
<?

	// Months navigation
		$this_date = getdate();
		$year = date("Y");
		$month = date("m");

		/*
			//$dateMinusOneMonth = mktime(0, 0, 0, (3-1), 31,  2007 );
			$d_month_previous = date('n', mktime(0,0,0,($month-1),28,$year));         // PREVIOUS month of year (1-12)
			$d_monthname_previous = date('F', mktime(0,0,0,($month-1),28,$year));     // PREVIOUS Month Long name (July)

			$d_month_previous2 = date('n', mktime(0,0,0,($month-2),28,$year));         // PREVIOUS month of year (1-12)
			$d_monthname_previous2 = date('F', mktime(0,0,0,($month-2),28,$year));     // PREVIOUS Month Long name (July)

			$d_month_previous3 = date('n', mktime(0,0,0,($month-3),28,$year));         // PREVIOUS month of year (1-12)
			$d_monthname_previous3 = date('F', mktime(0,0,0,($month-3),28,$year));     // PREVIOUS Month Long name (July)
		*/

		//echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-invoices.php&m=0'>Показать 200 последних продаж</a> ";
		
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
			echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-invoices.php&y=$y&m=". date('n', mktime(0,0,0,($mmonth-$i),28,$y))."'>".ru_month(date('n', mktime(0,0,0,($mmonth-$i),28,$y)), $sklon=false)." ".$y."</a> ";
			//$d_month_previous.''.$i = date('n', mktime(0,0,0,($month-$i),28,$y)); 
			//$d_monthname_previous.$i = ru_month($d_month_previous.$i, $sklon=false);
		}


/*		
		echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-invoices.php&m=".$month."'>".$this_date['month']."</a> &nbsp;";
		echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-invoices.php&m=".$d_month_previous."'>".$d_monthname_previous."</a> &nbsp;";
		echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-invoices.php&m=".$d_month_previous2."'>".$d_monthname_previous2."</a> &nbsp;";
		echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-invoices.php&m=".$d_month_previous3."'>".$d_monthname_previous3."</a> &nbsp;";
*/

		echo "<form method=post action='#'>";
		echo "2) Счета будут выводится <b>начиная с номера</b> ";
		echo "<input type='text' name='new_invoice_start_number' style='width:45px;' value='".$_invoice_start_number."'>";
		echo "<input type='submit' value=' изменить '>";
		echo "</form>";

		echo "<form method=post action='#'>";
		echo "3) <b>Дата выписки</b> счета ";
		echo "<input type='text' name='new_invoice_date' style='width:85px;' value='".$_invoice_date."'>";
		echo "<input type='submit' value=' изменить '>";
		echo " (По умолчанию сегодняшняя дата. Изменение даты действует временно)</form>";



// If the report month known
if (isset($_GET['m']) && is_numeric($_GET['m']))
{
	$_month = $_GET['m'];
	$_year = $_GET['y'];

	if (isset($_GET['m']) && $_month==0)
	{
		echo "<h3 style='color:#FF00FF'>Все продажи</h3>";
	}
	else if (isset($_GET['m']))
	{
		echo "<h1 style='color:#FF00FF'>".ru_month(date('n', mktime(0,0,0,($_month),28,$_year)))." $_year</h1>";
	}
	else
	{
		echo "<h1 style='color:#FF00FF'>".ru_month(date('m'))." $_year</h1>";
	}


	// all clients ids
	$sql = "select id, user_id, name, bank_attributes, contract, contract_date from al_customers order by contract";
	$response = $wpdb->get_results($sql,ARRAY_A) ;
	if($response != null)
	  {
		$_invoice_number = $_invoice_start_number;
		$customer_number = 1;
	  foreach($response as $product)
		{
			
			// get all sales for the given month
			$sql = "SELECT date, st.datetime, c.purchaseid,  p.id,  b.name as artist, p.name as title, c.price, totalprice, u.discount, u.display_name, l.user_id, firstname, lastname, email, address, phone, s.name as processed, gateway, c.license, st.downloads, st.active,  st.id as downloadid, u.contract, u.wallet, um.meta_value as smi
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
					AND um.meta_key = 'description'
					AND l.user_id != '106'
					AND u.id = '".$product['user_id']."'
					AND st.downloads != '5'
					AND gateway = 'wallet'
					AND datetime BETWEEN '$start_timestamp2' AND '$end_timestamp2'
				GROUP BY c.license
				ORDER BY datetime DESC";
				$product_list = $wpdb->get_results($sql,ARRAY_A);
									//pokazh($sql);
				if($product_list != null)
					{
						$n = 1; // sequence number of the cartoon sold to one customer
						$count = count($product_list); // number of cartoons sold
						$total = 0; // total price with discount
						$the_list = ''; // html list of cartoons with row tags
						$contract_period = $_month.".".date("Y");

						echo ("<div class='t' style='background-color:#CCD2FF;padding-left:4px;margin-top:8px;'>".$product['name']."</div>");
						
					
					  foreach($product_list as $sales)
						{
							$discount_price = round($sales['price'],0)*((100-round($sales['discount'],0))/100);
							
							//echo "<div class='t'>".date("d.m.y H:m:s",$sales['date'])." <b>".$sales['artist']."</b> «".stripslashes($sales['title'])."» цена:".round($sales['price'],0)." скидка:".round($sales['discount'],0)." итого:<b>".$discount_price."</b></div>";
							
							$total = $total + $discount_price;
                            
                            //license type
                            $lic_type = '';
                            switch ($sales['price']){
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
											<td style="padding:2px;text-align:center;">'.$sales["purchaseid"].'</td>
											<td style="font-style:bold;font-size:1em;padding:2px;">«'.stripslashes($sales["title"]).'» (#'.$sales["id"].') '.$sales["artist"].'. '.$lic_type.'</td>
											<td style="padding:2px;text-align:center;">1шт.</td>
											<td style="padding:2px;text-align:center;">'.$discount_price.'</td>
											<td style="padding:2px;text-align:center;">'.$discount_price.'</td>
										</tr>';

							$n++;
						}//foreach($product_list as $sales)
					
						// print invoice:
						
					$out = fill_invoice($filename, $_invoice_number, $_invoice_date,  $product['name'], $product['bank_attributes'], $the_list, $total, $count, $contract_period, $product['contract'],date_format(date_create($product['contract_date']),'d-m-Y'));

						echo "<div id='invoice' style='background: white url(http://cartoonbank.ru/img/mg_stamp.gif) no-repeat; background-size: 21%; background-position: 87% 100%; margin:20px; padding:8px;width: 210mm; border: 1px #D6D6D6 solid; font-size: 11pt;'>";
						echo $out;
						echo "</div>";

						//send_mail($out);

					// Print invoice PDF
					$out = fill_invoice($filename_pdf, $_invoice_number, $_invoice_date, $product['name'], $product['bank_attributes'], $the_list, $total, $count, $contract_period, $product['contract'],date_format(date_create($product['contract_date']),'d-m-Y'));
							echo ("<div><form method=post action='http://cartoonbank.ru/ales/tcpdf/examples/ales.php'>
									<input type='submit' value='скачать счёт (PDF) '>
									<input type='hidden' name='html' value='".htmlspecialchars($out)."'>
									<input type='hidden' name='filename' value='invoice_".$_invoice_number."'>
								</form></div>");

					// Print invoice no stamp PDF
					$out = fill_invoice($filename_nostamp_pdf, $_invoice_number, $_invoice_date, $product['name'], $product['bank_attributes'], $the_list, $total, $count, $contract_period, $product['contract'],date_format(date_create($product['contract_date']),'d-m-Y'));
							echo ("<div><form method=post action='http://cartoonbank.ru/ales/tcpdf/examples/ales.php'>
									<input type='submit' value='скачать счёт (PDF) без печати '>
									<input type='hidden' name='html' value='".htmlspecialchars($out)."'>
									<input type='hidden' name='filename' value='invoice_".$_invoice_number."'>
								</form></div>");

					// Print acceptance certificate PDF
					$invoice_date = date('d-m-Y',strtotime('-1 second',strtotime('+1 month',strtotime($_month.'/01/'.date('Y').' 00:00:00'))));
					$out = fill_invoice($filename_acceptance_certificate_pdf, $_invoice_number, $invoice_date, $product['name'], $product['bank_attributes'], $the_list, $total, $count, $contract_period, $product['contract'],date_format(date_create($product['contract_date']),'d-m-Y'));
							echo ("<div><form method=post action='http://cartoonbank.ru/ales/tcpdf/examples/acceptance_certificate.php'>
									<input type='submit' value='скачать акт выполненных работ (PDF) '>
									<input type='hidden' name='html' value='".htmlspecialchars($out)."'>
									<input type='hidden' name='filename' value='acceptance_certificate_".$_invoice_number."'>
								</form></div>");

					// Print acceptance certificate no stamp PDF
					$invoice_date = date('d-m-Y',strtotime('-1 second',strtotime('+1 month',strtotime($_month.'/01/'.date('Y').' 00:00:00'))));
					$out = fill_invoice($filename_acceptance_certificate_nostamp_pdf, $_invoice_number, $invoice_date, $product['name'], $product['bank_attributes'], $the_list, $total, $count, $contract_period, $product['contract'],date_format(date_create($product['contract_date']),'d-m-Y'));
							echo ("<div><form method=post action='http://cartoonbank.ru/ales/tcpdf/examples/acceptance_certificate.php'>
									<input type='submit' value='скачать акт выполненных работ (PDF) без печати '>
									<input type='hidden' name='html' value='".htmlspecialchars($out)."'>
									<input type='hidden' name='filename' value='acceptance_certificate_".$_invoice_number."'>
								</form></div>");



					$_invoice_number = $_invoice_start_number + $customer_number;
					$customer_number ++;

					}//if($product_list != null)
			
		}// foreach($response as $product)


	}//if($response != null)
}//if (isset($_GET['m']) && is_numeric($_GET['m']))


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
