<?php
$abspath = 'z:/home/localhost/www/';
$abspath_1 = "/home/www/cb/";
$abspath_2 = "/home/www/cb3/";

if (strstr($_SERVER['PHP_SELF'],'cb3/'))
	{$abspath = $abspath_2;}
else if (strstr($_SERVER['PHP_SELF'],'cb/')) 
	{$abspath = $abspath_1;}


$year = date("Y");
$month = date("m");

$start_timestamp = mktime(0, 0, 0, $month-12, 1, $year);
$end_timestamp = mktime(0, 0, 0, ($month+1), 0, $year);

$sql = "SELECT date, c.price, p.name as title, totalprice, firstname, lastname, email, address, phone, s.name as processed, gateway, c.license, st.downloads, st.active, p.id, c.purchaseid, st.id as downloadid
	FROM `wp_purchase_logs` as l, 
		`wp_purchase_statuses` as s, 
		`wp_cart_contents` as c, 
		`wp_product_list` as p,
		`wp_download_status` as st
	WHERE	l.`processed`=s.`id` 
		AND l.id=c.purchaseid 
		AND p.id=c.prodid  
		AND st.purchid=c.purchaseid
	AND (`date` BETWEEN '$start_timestamp' AND '$end_timestamp') 
	GROUP BY c.license
	ORDER BY `date` DESC";

//$sql = "SELECT date, totalprice, firstname, lastname, email, address, phone, s.name as processed, gateway FROM `wp_purchase_logs` as l, `wp_purchase_statuses` as s, `wp_cart_contents` as c WHERE l.`processed`=s.`id` AND l.id=c.purchaseid  AND (`date` BETWEEN '$start_timestamp' AND '$end_timestamp') ORDER BY `date` DESC";

	//pokazh($purchase_log,"purchase_log");

    require_once($abspath.'wp-content/RGrid/RGrid.php');
    
    $params['hostname'] = 'localhost';
    $params['username'] = 'z58365_cbru3';
    $params['password'] = 'greenbat';
    $params['database'] = 'z58365_cbru3';

    $grid = RGrid::Create($params, $sql);

    $grid->showHeaders = true;
    
    $grid->SetHeaderHTML('<div style="text-align: center">Статистика продаж</div>');


    $grid->SetDisplayNames(array('ID'       => '№',
                                 'totalprice'   => 'сумма заказа',
                                 'id'   => 'номер изобр.',
                                 'active'   => 'активно',
                                 'date'   => 'дата покупки',
                                 'price'   => 'цена картинки',
                                 'firstname'   => 'имя покупателя',
                                 'lastname'   => 'фамилия покупателя',
                                 'address'   => 'СМИ',
                                 'phone'   => 'телефон  покупателя',
                                 'gateway'   => 'метод оплаты',
                                 'processed'   => 'прохождение заказа',
                                 'title'   => 'название изображения',
                                 'license'   => 'номер лицензии',
                                 'downloadid'   => 'скачать',
                                 'downloads'   => 'осталось скачиваний'));

$grid->HideColumn('id');
$grid->HideColumn('statusno');
$grid->HideColumn('transactid');
$grid->HideColumn('authcodeid');
$grid->HideColumn('shopping_count');
$grid->HideColumn('shopping_region');
                               
    $grid->NoSpecialChars('title','downloadid');
    
    $grid->rowcallback = 'RowCallback';

    function RowCallback(&$row)
    {
		//$row['date'] = date("jS M Y",$row['date']);
		$row['date'] = date("d.m.y H:m:s",$row['date']);
		$row['title'] = "<a target='_blank' href='".get_option('siteurl')."/?page_id=29&cartoonid=".$row['id']."'>".$row['title']."</a>";
		//$row['average'] = round($row['average'],2);
		$row['downloadid'] = "<a href='".get_option('siteurl')."/?downloadid=".$row['downloadid']."'>скачать</a>";
		//$link = $siteurl."?downloadid=".$download_data['id'];
    }

    $grid->SetPerPage(40);
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
			padding:10px;
			margin:0px;
        }
        .datagrid thead th#header {
            border: 0;
        }
        .datagrid tbody td.altrow {
            background-color: #fff;
        }
        .datagrid tbody td.altcol
        {
          background-color: #eee;
        }
        .datagrid tbody td {
            padding-left: 5px;
			font-size: 8pt;
			border-bottom: 1px solid #DBDBDB;
			vertical-align:middle;
			text-align:center;
        }
        .datagrid a {
            text-decoration: none;
        }
    // -->
    </style>
<?
	echo 'Всего записей: ' . $grid->GetRowCount() . '<br>';
	$grid->Display() 
?>