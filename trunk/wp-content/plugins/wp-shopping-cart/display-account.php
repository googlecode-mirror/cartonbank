<h2>Бухгалтеру</h2>
<div style='background-color:#FFFF99; padding:4px;'>Продажи, за которые должны придти деньги. Личный счёт (wallet), Робокасса (robokassa), Чек Сбербанка (check). Зеленым цветом выделен Личный счёт.</div>

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

$sql = "SELECT COUNT( * ) as count, temp.name FROM ( SELECT b.id, b.name FROM  `wp_purchase_logs` AS l,  `wp_purchase_statuses` AS s,  `wp_cart_contents` AS c,  `wp_product_list` AS p,  `wp_download_status` AS st,  `wp_product_brands` AS b, `wp_users` AS u WHERE l.`processed` = s.`id`  AND l.id = c.purchaseid AND p.id = c.prodid AND st.purchid = c.purchaseid AND p.brand = b.id AND u.id = l.user_id AND l.user_id !=  '106' AND st.downloads !=  '5' AND date BETWEEN '$start_timestamp' AND '$end_timestamp' GROUP BY c.license ORDER BY b.name ) AS temp GROUP BY temp.id order by temp.name
";

$result = $wpdb->get_results($sql,ARRAY_A);
if (!$result) {die('<br />'.$del_sql.'<br />Invalid select query: ' . mysql_error());}
echo "<div><h3>Сколько продано работ по авторам</h3>";
foreach ($result as $row)
{
	echo "<span>";
	echo $row['name']."&nbsp;[".$row['count']."], ";
	echo "</span>";
}
echo "</div>";

echo "<br>";

$sql = "SELECT date,  c.purchaseid,  p.id,  s.name as processed, processed as processed_id, b.name as artist, p.name as title, c.price, totalprice, u.discount, u.display_name, l.user_id, firstname, lastname, email, address, phone, gateway, c.license, st.downloads, st.active,  st.id as downloadid, u.contract, u.wallet, um.meta_value as smi
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
        AND st.downloads != '5'
		AND date BETWEEN '$start_timestamp' AND '$end_timestamp'
	GROUP BY c.license
	ORDER BY `date` DESC
	LIMIT 200";

	//pokazh($sql,"sql");

	//http://www.phpguru.org/static/datagrid.html
    require_once($abspath.'wp-content/RGrid/RGrid.php');
    
    $grid = RGrid::Create($params, $sql);

    $grid->showHeaders = true;
    
    $grid->SetHeaderHTML('<div style="text-align: center">Статистика продаж</div>');


    $grid->SetDisplayNames(array('ID'       => '№',
                                 'totalprice'   => 'со скидкой',
                                 'id'   => '# изобр.',
                                 'artist'   => 'автор изобр.',
                                 'discount'   => 'скидка %',
                                 'display_name'   => 'логин',
								 'user_id' => 'покупатель',
                                 'purchaseid'   => 'номер заказа',
                                 'active'   => 'активно',
                                 'date'   => 'дата покупки',
                                 'price'   => 'цена',
                                 'firstname'   => 'покупатель',
                                 'lastname'   => 'фамилия покупателя',
                                 'address'   => 'СМИ',
                                 'phone'   => 'телефон  покупателя',
                                 'gateway'   => 'метод оплаты',
                                 'processed'   => 'статус заказа',
                                 'title'   => 'название ',
                                 'license'   => 'лицензия',
                                 'wallet'   => 'на счёте',
                                 'downloadid'   => 'скачать',
                                 'downloads'   => 'осталось скачиваний',
                                 'contract'   => 'номер договора'));

    
	$grid->NoSpecialChars('title','downloadid','firstname','processed');
    
    $grid->rowcallback = 'RowCallback';

    function RowCallback(&$row)
    {
		//$row['date'] = date("jS M Y",$row['date']);
		$row['firstname'] = '<a href="http://cartoonbank.ru/wp-admin/user-edit.php?user_id='.$row['user_id'].'">'.$row['firstname'].' '.$row['lastname'].'</a> '.$row['phone'].' «'.$row['smi'].'»';
		$row['date'] = date("d.m.y H:m:s",$row['date']);
		$row['title'] = "<a target='_blank' href='".get_option('siteurl')."/?page_id=29&cartoonid=".$row['id']."'>".nl2br(stripslashes($row['title']))."</a>";
		//$row['average'] = round($row['average'],2);
		$row['downloadid'] = "<a href='".get_option('siteurl')."/?downloadid=".$row['downloadid']."'>скачать</a>";
		//$link = $siteurl."?downloadid=".$download_data['id'];
		$row['discount'] = round ($row['discount'],0);
		$row['totalprice'] = $row['price'] - $row['price'] * $row['discount']/100;
		$row['wallet'] = round($row['wallet'],0);
		$row['price'] = round($row['price'],0);
		if ($row['processed_id']==5)
		{
			$row['processed'] = '<div style="background-color:#FCF798;" class="status_'.$row['purchaseid'].'" id="status_'.$row['purchaseid'].'"><div onclick="change_status(\'.status_'.$row['purchaseid'].'\');">'.$row['processed'].'</div></div>';
		}
		else
		{
			$row['processed'] = '<div class="status_'.$row['purchaseid'].'" id="status_'.$row['purchaseid'].'"><div onclick="change_status(\'.status_'.$row['purchaseid'].'\');">'.$row['processed'].'</div></div>';
		}
	}

	//$grid->HideColumn('column', ...)
	$grid->HideColumn('address','phone','display_name','user_id','lastname','email','downloads','active','downloadid','smi','processed_id');

    $grid->SetPerPage(100);
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
    // -->
    </style>
<?
	$this_date = getdate();
	//$dateMinusOneMonth = mktime(0, 0, 0, (3-1), 31,  2007 );
	$d_month_previous = date('n', mktime(0,0,0,($month-1),28,$year));         // PREVIOUS month of year (1-12)
	$d_monthname_previous = date('F', mktime(0,0,0,($month-1),28,$year));     // PREVIOUS Month Long name (July)

	$d_month_previous2 = date('n', mktime(0,0,0,($month-2),28,$year));         // PREVIOUS month of year (1-12)
	$d_monthname_previous2 = date('F', mktime(0,0,0,($month-2),28,$year));     // PREVIOUS Month Long name (July)

	echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-account.php&m=0'>Показать 100 последних продаж</a> ";
	echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-account.php&m=".$month."'>".$this_date['month']."</a> ";
	echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-account.php&m=".$d_month_previous."'>".$d_monthname_previous."</a> ";
	echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-account.php&m=".$d_month_previous2."'>".$d_monthname_previous2."</a> ";


	echo "<br />";
	echo 'Всего записей: ' . $grid->GetRowCount() . '<br />';
	$grid->Display() 
?>

<script language="JavaScript">
<!--
	function change_status(purchaseid)
	{
		var id = purchaseid.split('_')[1];
		if (jQuery(purchaseid).text().indexOf("Платёж прошёл")==0)
		{
			jQuery(purchaseid).html('<div onclick="change_status(\'' + purchaseid +'\');">Деньги получены</div>');
			sendup('5',id);
			jQuery(purchaseid).css('background-color','#FCF798');
		}
		else if (jQuery(purchaseid).text().indexOf("Заказано")==0)
		{
			jQuery(purchaseid).html('<div onclick="change_status(\'' + purchaseid +'\');">Деньги получены</div>');
			sendup('5',id);
			jQuery(purchaseid).css('background-color','#FCF798');
		}
		else if (jQuery(purchaseid).text().indexOf("Деньги получены")==0)
		{
			jQuery(purchaseid).html('<div onclick="change_status(\'' + purchaseid +'\');">Платёж прошёл</div>');
			sendup('2',id);
			jQuery(purchaseid).css('background-color','#e1ffc2');
		}

	}

	function sendup(wrd,id)
	{
		// send purchase status update
		wrd = encodeURIComponent(wrd);
		jQuery.post("http://cartoonbank.ru/ales/accountant/purchase_status.php?purch_id="+id+"&sta="+wrd);
	}

//-->
</script>



