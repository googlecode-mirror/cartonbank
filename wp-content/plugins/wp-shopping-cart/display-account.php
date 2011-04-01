<?php
$abspath = 'z:/home/localhost/www/';
$abspath_1 = "/home/www/cb/";
$abspath_2 = "/home/www/cb3/";

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

if (isset($_GET['m']) && is_numeric($_GET['m']))
{
	$start_timestamp = mktime(0, 0, 0, $_GET['m'], 1, $year);
	$end_timestamp = mktime(0, 0, 0, ($_GET['m']+1), 0, $year);
}
else
{
	$start_timestamp = mktime(0, 0, 0, $month-12, 1, $year);
	$end_timestamp = mktime(0, 0, 0, ($month+1), 0, $year);
}
$sql = "SELECT date,  c.purchaseid,  p.id,  b.name as artist, p.name as title, c.price, totalprice, u.discount, u.display_name, l.user_id,firstname, lastname, email, address, phone, s.name as processed, gateway, c.license, st.downloads, st.active,  st.id as downloadid, u.contract
	FROM `wp_purchase_logs` as l, 
		`wp_purchase_statuses` as s, 
		`wp_cart_contents` as c, 
		`wp_product_list` as p,
		`wp_download_status` as st,
		`wp_product_brands` as b,
		`wp_users` as u
	WHERE	l.`processed`=s.`id` 
		AND l.id=c.purchaseid 
		AND p.id=c.prodid  
		AND st.purchid=c.purchaseid
		AND p.brand=b.id
		AND u.id = l.user_id
		AND l.user_id != '106'
        AND st.downloads != '5'
		AND date BETWEEN '$start_timestamp' AND '$end_timestamp'
	GROUP BY c.license
	ORDER BY `date` DESC
	LIMIT 100";

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
                                 'processed'   => 'прохождение заказа',
                                 'title'   => 'название ',
                                 'license'   => 'лицензия',
                                 'downloadid'   => 'скачать',
                                 'downloads'   => 'осталось скачиваний',
                                 'contract'   => 'номер договора'));

    
	$grid->NoSpecialChars('title','downloadid');
    
    $grid->rowcallback = 'RowCallback';

    function RowCallback(&$row)
    {
		//$row['date'] = date("jS M Y",$row['date']);
		$row['firstname'] = $row['firstname'].' '.$row['lastname'].' '.$row['address'].' т.'.$row['phone'];
		$row['date'] = date("d.m.y H:m:s",$row['date']);
		$row['title'] = "<a target='_blank' href='".get_option('siteurl')."/?page_id=29&cartoonid=".$row['id']."'>".nl2br(stripslashes($row['title']))."</a>";
		//$row['average'] = round($row['average'],2);
		$row['downloadid'] = "<a href='".get_option('siteurl')."/?downloadid=".$row['downloadid']."'>скачать</a>";
		//$link = $siteurl."?downloadid=".$download_data['id'];
		$row['totalprice'] = $row['price'] - $row['price'] * $row['discount']/100;
	}

	//$grid->HideColumn('column', ...)
	$grid->HideColumn('address','phone','display_name','user_id','lastname','email','processed','downloads','active','downloadid');

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


	echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-account.php&m='>Показать 100 последних продаж</a> ";
	echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-account.php&m=".$month."'>".$this_date['month']."</a> ";
	echo "<a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-account.php&m=".$d_month_previous."'>".$d_monthname_previous."</a> ";


	echo "<br />";
	echo 'Всего записей: ' . $grid->GetRowCount() . '<br />';
	$grid->Display() 
?>

