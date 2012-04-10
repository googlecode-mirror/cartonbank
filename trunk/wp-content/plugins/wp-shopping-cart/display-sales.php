<?php

include_once('../wp-config.php');

$params['hostname'] = DB_HOST; 
$params['username'] = DB_USER;
$params['password'] = DB_PASSWORD;
$params['database'] = DB_NAME;

$year = date("Y");
$month = date("m");

$start_timestamp = mktime(0, 0, 0, $month-12, 1, $year);
$end_timestamp = mktime(0, 0, 0, ($month+1), 0, $year);

$sql = "SELECT f.idhash, st.datetime, c.purchaseid,  p.id,  b.name as artist, p.name as title, c.price, totalprice, u.discount, u.display_name, l.user_id,firstname, lastname, email, address, phone, s.name as processed, gateway, c.license, st.downloads, st.active,  st.id as downloadid, u.contract
	FROM `wp_purchase_logs` as l, 
		`wp_purchase_statuses` as s, 
		`wp_cart_contents` as c, 
		`wp_product_list` as p,
		`wp_download_status` as st,
		`wp_product_brands` as b,
		`wp_users` as u,
		`wp_product_files` as f
	WHERE	l.`processed`=s.`id` 
		AND l.id=c.purchaseid 
		AND p.id=c.prodid  
		AND st.purchid=c.purchaseid
		AND p.brand=b.id
		AND u.id = l.user_id
		AND p.file = f.id
	GROUP BY c.license
	ORDER BY c.purchaseid DESC";

//	AND (`date` BETWEEN '$start_timestamp' AND '$end_timestamp')


//$sql = "SELECT date, totalprice, firstname, lastname, email, address, phone, s.name as processed, gateway FROM `wp_purchase_logs` as l, `wp_purchase_statuses` as s, `wp_cart_contents` as c WHERE l.`processed`=s.`id` AND l.id=c.purchaseid  AND (`date` BETWEEN '$start_timestamp' AND '$end_timestamp') ORDER BY `date` DESC";

	//pokazh($sql,"sql");

    require_once('../wp-content/RGrid/RGrid.php');
    
    $grid = RGrid::Create($params, $sql);

    $grid->showHeaders = true;
    
    $grid->SetHeaderHTML('<div style="text-align: center">Статистика продаж</div>');


    $grid->SetDisplayNames(array('ID'       => '№',
                                 'totalprice'   => 'сумма заказа',
                                 'id'   => 'номер изобр.',
                                 'artist'   => 'автор изобр.',
                                 'discount'   => 'скидка %',
                                 'display_name'   => 'логин',
								 'user_id' => 'покупатель',
                                 'purchaseid'   => 'номер заказа',
                                 'active'   => 'активно',
                                 'datetime'   => 'дата покупки',
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
                                 'downloads'   => 'осталось скачиваний',
                                 'contract'   => 'номер договора'));

                               
    $grid->NoSpecialChars('title','downloadid');
    
    $grid->rowcallback = 'RowCallback';

	$grid->HideColumn('idhash');

    function RowCallback(&$row)
    {
		//$row['date'] = date("jS M Y",$row['date']);
		$row['datetime'] = date("d.m.y H:m:s",strtotime($row['datetime']));
		$row['title'] = "<a target='_blank' href='".get_option('siteurl')."/?page_id=29&cartoonid=".$row['id']."'>".nl2br(stripslashes($row['title']))."</a>";
		//$row['average'] = round($row['average'],2);
		$row['downloadid'] = "<a href='".get_option('siteurl')."/?downloadid=".$row['downloadid']."&sid=".substr($row['idhash'], -6)."&mode=go'>скачать</a>";
		//$link = $siteurl."?downloadid=".$download_data['id'];

    }

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
	echo 'Всего записей: ' . $grid->GetRowCount() . '<br />';
	$grid->Display() 
?>