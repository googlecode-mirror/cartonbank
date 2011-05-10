<?php
$abspath = 'z:/home/localhost/www/';
$abspath_1 = "/home/www/cb/";
$abspath_2 = "/home/www/cb3/";

// начальный номер счёта 
$_invoce_start_number=500;

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

<h2>Покупатели</h2>
<?
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

		
		echo "            <td class='t' style='width:300px;font-size:1em;'>";
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
?>
