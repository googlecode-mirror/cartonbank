<script src="http://cartoonbank.ru/ales/jquery.jeditable.js" type="text/javascript" charset="utf-8"></script> 

<h2>Бухгалтеру</h2>
<div style='background-color:#FFFF99; padding:4px;font-size:0.8em;'>Продажи, за которые должны прийти деньги. Личный счёт (wallet), Робокасса (robokassa), Чек Сбербанка (check). Зеленым цветом выделен Личный счёт.</div>

<?php
$abspath = 'z:/home/localhost/www/';
$abspath_1 = ROOTDIR;
$abspath_2 = ROOTDIR;

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
	//AND st.datetime BETWEEN '2011-05-01' AND '2011-06-01'
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


$sql = "SELECT COUNT( * ) as count, temp.name FROM ( SELECT b.id, b.name FROM  `wp_purchase_logs` AS l,  `wp_purchase_statuses` AS s,  `wp_cart_contents` AS c,  `wp_product_list` AS p,  `wp_download_status` AS st,  `wp_product_brands` AS b, `wp_users` AS u WHERE l.`processed` = s.`id`  AND l.id = c.purchaseid AND p.id = c.prodid AND st.purchid = c.purchaseid AND p.brand = b.id AND u.id = l.user_id AND l.user_id !=  '106' AND st.downloads !=  '5' AND date BETWEEN '$start_timestamp' AND '$end_timestamp' GROUP BY c.license ORDER BY b.name ) AS temp GROUP BY temp.id order by temp.name";

$result = $wpdb->get_results($sql,ARRAY_A);
		///pokazh($sql);

if (!$result) {echo('<br />Продаж за этот период не найдено ' . mysql_error());}

	/// pokazh($sql);

echo "<div><h3>Сколько продано работ по авторам за выбранный период</h3>";
foreach ($result as $row)
{
	echo "<span>";
	echo $row['name']."&nbsp;[".$row['count']."], ";
	echo "</span>";
}
echo "</div>";

echo "<br>";

$itogo = get_itogo($start_timestamp2, $end_timestamp2);

/*
$sql = "SELECT date,  c.purchaseid, p.id, s.name as processed, processed as processed_id, b.name as artist, p.name as title, c.price, totalprice, u.discount, c.cart_discount, c.actual_money, u.display_name, l.user_id, l.payment_arrived_date, firstname, lastname, email, address, phone, gateway, c.license, st.downloads, st.active,  st.id as downloadid, u.contract, u.wallet, um.meta_value as smi
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
	ORDER BY `date` DESC";
*/
$sql ="SELECT date, st.datetime,  c.purchaseid, p.id, s.name as processed, processed as processed_id, b.name as artist, p.name as title, c.price, totalprice, u.discount, c.cart_discount, c.actual_money, u.display_name, l.user_id, l.payment_arrived_date, firstname, lastname, email, address, phone, gateway, c.license, st.downloads, st.active,  st.id as downloadid, u.contract, u.wallet, um.meta_value as smi
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
		AND datetime BETWEEN '$start_timestamp2' AND '$end_timestamp2'
	GROUP BY c.license
	ORDER BY `datetime` DESC";

		///pokazh($sql,"sql");

	//http://www.phpguru.org/static/datagrid.html
    require_once($abspath.'wp-content/RGrid/RGrid.php');
    
    $grid = RGrid::Create($params, $sql);

    $grid->showHeaders = true;
    
    $grid->SetHeaderHTML('<div style="text-align: center">Статистика продаж</div>');


    $grid->SetDisplayNames(array('totalprice'   => 'со скидкой',
                                 'id'   => '№ изобр.',
                                 'artist'   => 'автор изобр.',
                                 'discount'   => 'скидка юзера%',
                                 'cart_discount'   => 'скидка заказа%',
                                 'display_name'   => 'логин',
								 'user_id' => 'покупатель',
                                 'purchaseid'   => '№ заказа',
                                 'active'   => 'активно',
                                 'datetime'   => 'дата покупки',
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
                                 'actual_money'   => 'пришли от РК',
                                 'downloadid'   => 'скачать',
                                 'downloads'   => 'осталось скачиваний',
                                 'contract'   => '№ договора'));

    
	$grid->NoSpecialChars('title','downloadid','firstname','processed','actual_money');
    
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

		if ($row['cart_discount'] == '')
		{
			$row['totalprice'] = $row['price'] - $row['price'] * $row['discount']/100;
		}
		else
		{
			$row['totalprice'] = $row['price'] - $row['price'] * $row['cart_discount']/100;
		}

		
		$row['wallet'] = round($row['wallet'],0);
		$row['price'] = round($row['price'],0);
		if ($row['processed_id']==5)
		{
			$payment_date = strtotime($row['payment_arrived_date']);
			$row['processed'] = '<div title="изменить" style="background-color:#FCF798;cursor:pointer;" class="status_'.$row['purchaseid'].'" id="status_'.$row['purchaseid'].'"><div onclick="change_status(\'.status_'.$row['purchaseid'].'\');">'.$row['processed'].' в '.ru_month(date("m",$payment_date),true).' '.date("Y",$payment_date).'</div></div>';
			//date("M",$row['payment_arrived_date'])
			//'.$row['payment_arrived_date'].'
		}
		else
		{
			$row['processed'] = '<div title="изменить" style="cursor:pointer;" class="status_'.$row['purchaseid'].'" id="status_'.$row['purchaseid'].'"><div onclick="change_status(\'.status_'.$row['purchaseid'].'\');">'.$row['processed'].'</div></div>';
		}

		//robokassa
		//actual_money
		if ($row['gateway']=='robokassa')
		{
			if ($row['actual_money']=='')
			{
				$row['actual_money']='<div class="edit" id="'.$row['purchaseid'].'" style="cursor:pointer;">'.$row['totalprice']*.95.'</div>';
			}
			else
			{
				$row['actual_money']='<div class="edit" id="'.$row['purchaseid'].'" style="cursor:pointer;">'.$row['actual_money'].'</div>';
			}
		}
	}

	//$grid->HideColumn('column', ...)
	$grid->HideColumn('date','address','phone','display_name','user_id','lastname','email','downloads','active','downloadid','smi','processed_id','payment_arrived_date');

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
	// Months navigation
		$this_date = getdate();
		$year = date("Y");
		$month = date("m");
?>

<div style='background-color:#FFFF99; padding:4px;font-size:0.8em;'>Для отмечания прихода денег за рисунки надо выбрать месяц и год, когда пришли деньги в формате dd.mm.yyyy (точный день не имеет значения). При перезагрузке страницы месяц и год установлены в текущие.</div>
<div><input type="text" id="date_selector" name="date_select" value="<?echo "01.".$month.".".$year;?>"></div>

<?
		
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
			echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-account.php&y=$y&m=". date('n', mktime(0,0,0,($mmonth-$i),28,$y))."'>".ru_month(date('n', mktime(0,0,0,($mmonth-$i),28,$y)), $sklon=false)." ".$y."</a> ";
			//$d_month_previous.''.$i = date('n', mktime(0,0,0,($month-$i),28,$y)); 
			//$d_monthname_previous.$i = ru_month($d_month_previous.$i, $sklon=false);
		}


	// Month display
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


	echo 'Всего продаж: <b>' . $grid->GetRowCount() . '</b>. Сумма продаж со скидкой: <b>'.$itogo
.'</b> руб.<br />';
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
		var date_selector = jQuery('#date_selector').attr('value');
		date = encodeURIComponent(date_selector);
		
		jQuery.post("http://cartoonbank.ru/ales/accountant/purchase_status.php?purch_id="+id+"&sta="+wrd+"&date="+date);
	}

	jQuery(document).ready(function() {
		 jQuery('.edit').editable('http://cartoonbank.ru/ales/accountant/save_rk.php', {
			 indicator : 'Сохраняю...',
			 tooltip   : 'Нажмите для редактирования...'
		 });
		 jQuery('.edit_area').editable('http://cartoonbank.ru/ales/accountant/save_rk.php', { 
			 type      : 'textarea',
			 cancel    : 'Cancel',
			 submit    : 'OK',
			 indicator : '<img src="/img/ldng.gif" alt="loading">',
			 tooltip   : 'Нажмите для редактирования...'
		 });
	});



//-->
</script>


<?
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

function get_itogo($start_timestamp, $end_timestamp)
{
	// discount total

	global $wpdb;

	$itogo = 0;
/*
	$sql ="SELECT c.price, totalprice, u.discount as discount, c.cart_discount as cart_discount
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
				AND date BETWEEN $start_timestamp AND $end_timestamp
			GROUP BY c.license";
*/
$sql ="SELECT c.price,st.datetime, totalprice, u.discount as discount, c.cart_discount as cart_discount
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
		AND st.datetime BETWEEN '$start_timestamp' AND '$end_timestamp'
	GROUP BY c.license";

			///pokazh ($sql);
		$result = $wpdb->get_results($sql,ARRAY_A);
		if (!$result) 
			{
				echo('<br />Продаж за этот период не найдено ' . mysql_error());
			}

		foreach ($result as $row)
		{
			if (is_null($row['cart_discount']))
			{
				$itogo = $itogo + ($row['price'] * (100 - $row['discount']))/100;
			}
			else
			{
				$itogo = $itogo + ($row['price'] * (100 - $row['cart_discount']))/100;
			}
		}

		return $itogo;
}
?>
