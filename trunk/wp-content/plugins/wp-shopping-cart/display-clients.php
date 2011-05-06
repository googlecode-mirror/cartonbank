<?php
$abspath = 'z:/home/localhost/www/';
$abspath_1 = "/home/www/cb/";
$abspath_2 = "/home/www/cb3/";

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

$sql = "SELECT c.id, c.user_id, c.name, c.bank_attributes, c.contract, c.contract_date, u.user_email, u.user_url, u.wallet, u.discount
		FROM  `al_customers` AS c,  `wp_users` AS u
		WHERE u.id = c.user_id";
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
            font-size: 8pt;
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
			font-size: 8pt;
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

<h2>Покупатели</h2>
<?
	$this_date = getdate();
	//$dateMinusOneMonth = mktime(0, 0, 0, (3-1), 31,  2007 );
	$d_month_previous = date('n', mktime(0,0,0,($month-1),28,$year));         // PREVIOUS month of year (1-12)
	$d_monthname_previous = date('F', mktime(0,0,0,($month-1),28,$year));     // PREVIOUS Month Long name (July)

	$d_month_previous2 = date('n', mktime(0,0,0,($month-2),28,$year));         // PREVIOUS month of year (1-12)
	$d_monthname_previous2 = date('F', mktime(0,0,0,($month-2),28,$year));     // PREVIOUS Month Long name (July)

	echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-clients.php&m=0'>Показать 200 последних продаж</a> ";
	echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-clients.php&m=".$month."'>".$this_date['month']."</a> ";
	echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-clients.php&m=".$d_month_previous."'>".$d_monthname_previous."</a> ";
	echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-clients.php&m=".$d_month_previous2."'>".$d_monthname_previous2."</a> ";



echo "        <table id='itemlist'>";
echo "          <tr style='border:1px solid black; background-color:#c0c0c0;'>";

echo "            <td class='t'>";
echo "#";
echo "            </td>";

echo "            <td class='t'>";
echo "юзер";
echo "            </td>";

echo "            <td class='t'>";
echo "название";
echo "            </td>";

echo "            <td class='t'>";
echo "банковские атрибуты";
echo "            </td>";

echo "            <td class='t'>";
echo "договор";
echo "            </td>";

echo "            <td class='t'>";
echo "скидка, %";
echo "            </td class='t'>";

echo "            <td class='t'>";
echo "на счёте";
echo "            </td class='t'>";

echo "            <td class='t'>";
echo "дата контракта";
echo "            </td>";

echo "          </tr>";


if($product_list != null)
  {
  foreach($product_list as $product)
	{
		echo "          <tr>";

		echo "            <td class='t'>";
		echo $product['id'];
		echo "            </td>";

		echo "            <td class='t'><a href='". get_option('siteurl')."/wp-admin/user-edit.php?user_id=".$product['user_id']."'>";
		echo $product['user_id'];
		echo "</a>            </td>";

		echo "            <td class='t' style='width:250px;'>";
		echo $product['name'];
		echo "            </td>";

		
		echo "            <td class='t' style='width:250px;'>";
		echo $product['bank_attributes'];
		echo "            </td>";

		echo "            <td class='t'>";
		echo $product['contract'];
		echo "            </td>";


		echo "            <td class='t'>";
		echo round($product['discount'],0);
		echo "            </td>";

		echo "            <td class='t'>";
		echo round($product['wallet'],0);
		echo "            </td>";


		echo "            <td class='t'>";
		echo date_format(date_create($product['contract_date']),'d-m-Y');
		echo "            </td>";


		echo "          </tr>";
	}
  }
  
echo "        </table>";

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
		echo "<h3>".date('F', mktime(0,0,0,($month),28,$year))."</h3>";
	}


	// all clients ids
	$sql = "select id, user_id, name from al_customers order by contract";
	$response = $wpdb->get_results($sql,ARRAY_A) ;
	if($response != null)
	  {
	  foreach($response as $product)
		{
			// get all sales for the given month
			$sql = "SELECT date,  c.purchaseid,  p.id,  b.name as artist, p.name as title, c.price, totalprice, u.discount, u.display_name, l.user_id, firstname, lastname, email, address, phone, s.name as processed, gateway, c.license, st.downloads, st.active,  st.id as downloadid, u.contract, u.wallet, um.meta_value as smi
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
					AND date BETWEEN '$start_timestamp' AND '$end_timestamp'
				GROUP BY c.license
				ORDER BY `date` DESC";

				$product_list = $wpdb->get_results($sql,ARRAY_A);
									///pokazh($sql);
				if($product_list != null)
					{
					echo ("<div class='t' style='background-color:#CCD2FF;padding-left:4px;margin-top:8px;'>".$product['name']."</div>");
					  foreach($product_list as $sales)
						{
							echo "<div class='t'>".date("d.m.y H:m:s",$sales['date'])." <b>".$sales['artist']."</b> «".stripslashes($sales['title'])."» цена:".round($sales['price'],0)." скидка:".round($sales['discount'],0)." итого:<b>".round($sales['price'],0)*((100-round($sales['discount'],0))/100)."</b></div>";
						}//foreach($product_list as $product)
					
						// print invoice:
						echo "<div id='invoice'>";
						echo fill_invoice();
						echo "</div>";

					}//if($product_list != null)
			
		}// foreach($response as $product)


	}//if($response != null)
}//if (isset($_GET['m']) && is_numeric($_GET['m']))


function fill_invoice($invoice_number='',$invoice_date='',$client_details='',$product_list='',$total='')
{

	if ($invoice_number==''){$invoice_number='____';}
	if ($invoice_date==''){$invoice_date = getdate();}
	if ($client_details==''({$client_details = 'данные покупателя неизвестны';}
	if ($product_list=='')
		{
		$product_list = '<tr>
			<td style="width:13mm;"></td>
			<td style="width:20mm;"></td>
			<td></td>
			<td style="width:20mm;"></td>
			<td style="width:17mm;"></td>
			<td style="width:27mm;">Цена
			<td style="width:27mm;"></td>
			</tr>';
		}
	if ($total==''){$total=0;}

		$filename = "invoice.html";
		$content=loadFile($filename); 

		$total_rub_text = $total;

	// replace placeholders
		$content = str_replace ('{invoice_number}',$invoice_number,$content);
		$content = str_replace ('{invoice_date}',$invoice_date,$content);
		$content = str_replace ('{client_details}',$client_details,$content);
		$content = str_replace ('{product_list}',$product_list,$content);
		$content = str_replace ('{total}',$total,$content);
		$content = str_replace ('{total_rub_text}',$total_rub_text,$content);

		// output content
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
?>
