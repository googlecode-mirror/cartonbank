<script> 
	function collapseItem(itemid) {
		document.getElementById(itemid).style.display = 'none';
		document.getElementById('switch'+itemid).innerHTML = '<a href=\'javascript:expandItem(\"'+itemid+'\");\'>[+]</a>';
	}
	function expandItem(itemid) {
		document.getElementById(itemid).style.display = 'inline';
		var link = '<a href=\'javascript:collapseItem(\"'+itemid+'\");\'>[–]</a>';
		document.getElementById('switch'+itemid).innerHTML = link;
	}
</script> 
 
 <style>
.clpsd { display: none; }
 </style>


<h2>Авторские отчисления</h2>

<?
//<span id='switch".$sales['artist_id']."'><a href='javascript:expandItem(\"details".$sales['artist_id']."\");'>[+]</a></span>
//author_payments_complete.php
$abspath = 'z:/home/localhost/www/';
$abspath_1 = "/home/www/cb/";
$abspath_2 = "/home/www/cb3/";
$filename_acceptance_certificate_pdf = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/artist_acceptance_certificate_pdf.html";
$filename_acceptance_certificate_nostamp_pdf = "/home/www/cb3/wp-content/plugins/wp-shopping-cart/artist_acceptance_certificate_nostamp_pdf.html";
$total_all = 0; // all artists total summ

global $wpdb;

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



if (isset($_POST['amount']) && isset($_POST['year']) && is_numeric($_POST['year']) && isset($_POST['artists']) && isset($_POST['months']) && is_numeric($_POST['months']))
{
	make_payment($_POST['amount'],$_POST['year'],$_POST['artists'],$_POST['months']);
}





?>

<h3>Выплатить автору:</h3>
<div style="border:1px solid silver; padding:4px;width:800px;">
<form name="make_payment" method=post action="">
Автор:<?echo get_artists_list();?>
&nbsp;Сумма:<input type="text" style="width:80px;" name="amount">
&nbsp;Дата выплаты: &nbsp;месяц:<?echo get_months_list();?>
&nbsp;год:<input type="text" style="width:60px;" name="year" value=<?echo date('Y',time());?>>
<input type="submit" value="Заплатить">
</form>
</div>

<?

// Вывод таблицы сгруппированной по авторам и годам
for ($y = date('Y'); $y>=2010; $y--) 
{
	outputPaymentGroupped($y);
	//outputPayment($y);
}


function outputPaymentGroupped($year)
{
	global $wpdb;

	echo ("<br clear='left' />");
	echo ("<h1>".$year."</h1>");
	$timestamp_start = date('Y-m-d 23:59:59',mktime(0, 0, 0, 12, 31, $year-1));
	$timestamp_end = date('Y-m-d 23:59:59',mktime(0, 0, 0, 1, 0, $year+1));

		$sql = "select name, rezident, artist_id, sum(cartoons_sold) as cartoons_sold, sum(reward) as reward, sum(tax_ndfl) as tax_ndfl, sum(reward_to_pay) as reward_to_pay, sum(reward_payed) as reward_payed, sum(reward_remains) as reward_remains from artist_payments, wp_product_brands 
			where artist_payments.artist_id =  wp_product_brands.id 
			and payment_date between '$timestamp_start' and '$timestamp_end'
			group by name
			ORDER BY name Asc, payment_date DESC";
			
			//pokazh($sql);

			$payments_list = $wpdb->get_results($sql,ARRAY_A);

			//pokazh($payments_list);

	if($payments_list != null)
	{
				$out = "<div>";

					$out .= "<div>";
						$out .= "<div class='htt' >Автор</div>";//1
						$out .= "<div class='ht'>Кол-во проданных рисунков</div>";//2
						$out .= "<div class='ht'>Начислено авторское вознаграждение</div>";//3
						$out .= "<div class='ht'>Резидент</div>";//4
						$out .= "<div class='ht'>Удержан НДФЛ</div>";//5
						$out .= "<div class='ht'>К выплате</div>";//6
						$out .= "<div class='ht'>Выплачено</div>";//7
						$out .= "<div class='ht'>Остаток</div>";//8
						$out .= "<div class='ht' style='width:100px;'>Дата акта или выплаты</div>";..9
						$out .= "<div class='ht'>Номер акта</div>";
					$out .= "</div>";

					foreach($payments_list as $sales)
					{
													
							//pokazh($sales);

							// groupped list
							$reward_remains = floatval(round($sales['reward_to_pay'])) - floatval(round($sales['reward_payed']));
							//$reward_remains_total = floatval(round($sales['reward_to_pay_total'])) - floatval(round($sales['reward_payed_total']));

							$out .= "<div>";
								$out .= "<br clear='left' />";
								$out .= "<div class='tt'> <span id='switchdetails".$year.$sales['artist_id']."'><a href='javascript:expandItem(\"details".$year.$sales['artist_id']."\");'>[+]</a></span>&nbsp;<b>".$sales['name']."</b></div>";
								$out .= "<div class='t'>".$sales['cartoons_sold']."</div>";//2
								$out .= "<div class='t'>".$sales['reward']."</div>";//3
								$out .= "<div class='t'>".$sales['rezident']."</div>";//4
								$out .= "<div class='t'>".round($sales['tax_ndfl'])."</div>";//5
								$out .= "<div class='t'>".round($sales['reward_to_pay'])."</div>";//6
								$out .= "<div class='t'>".round($sales['reward_payed'])."</div>";//7
								
								if ($reward_remains>=5000)
									$out .= "<div class='t' style='color:red; font-weight:bold;background-color:#FDDE9D;'>".$reward_remains."</div>";
								else
									$out .= "<div class='t'>".$reward_remains."</div>";

								$out .= "<div class='t' style='width:100px;'>".getMonthYearNames($sales['payment_date'])."</div>";//9
								$out .= "<div class='t'>".$sales['act_number']."</div>";//9
							$out .= "</div>";
							
								$out .= "<div id='details".$year.$sales['artist_id']."' class='clpsd'>";
									$out .= "<br clear='left' />";//вкладыш
									$out .= "<div  style='width:100%'>".outputPaymentPerArtist($year,$sales['artist_id'])."</div>";
								$out .= "</div>";
					}
				$out .= "</div>";
				
				echo $out;
	}
}


function outputPaymentPerArtist($year,$artist_id)
{
	global $wpdb;

	//return ("<h3>разворот</h3>");

	$timestamp_start = date('Y-m-d 23:59:59',mktime(0, 0, 0, 12, 31, $year-1));
	$timestamp_end = date('Y-m-d 23:59:59',mktime(0, 0, 0, 1, 0, $year+1));

		$sql = "select name, artist_id, cartoons_sold, reward, tax_ndfl, reward_to_pay, reward_payed, reward_remains,
			payment_date, act_number, is_payed from artist_payments, wp_product_brands 
			where artist_payments.artist_id = wp_product_brands.id 
				and artist_payments.artist_id = ".$artist_id."
				and payment_date between '$timestamp_start' and '$timestamp_end'
			order by payment_date desc, name";

		//pokazh($sql);

			

				$payments_list = $wpdb->get_results($sql,ARRAY_A);


		if($payments_list != null)
		{
					$out = "<div  style='display:none;'>";
							$out .= "<div class='tt'>Автор</div>";
							$out .= "<div class='t'>Кол-во проданных рисунков</div>";
							$out .= "<div class='t'>Начислено авторское вознаграждение</div>";
							$out .= "<div class='t'>Резидент</div>";
							$out .= "<div class='t'>Удержан НДФЛ</div>";
							$out .= "<div class='t'>К выплате</div>";
							$out .= "<div class='t'>Выплачено</div>";
							$out .= "<div class='t'>Остаток</div>";
							$out .= "<div class='t' style='width:100px;'>Дата акта или выплаты</div>";
							$out .= "<div class='t'>Номер акта</div>";
					$out .= "</div>";


			foreach($payments_list as $sales)
			{
											
					//pokazh($sales);

					// detailed list

					$out .= "<div style='width:100%;'>";

						if ($sales['cartoons_sold']==0 && $sales['reward']==0)
						{
							$out .= "<div class='tt1' style='background-color:#ECFCCF; clear:left'>".$sales['name']."</div>";
							$out .= "<div class='t1' style='background-color:#ECFCCF;'>".$sales['cartoons_sold']."</div>";
							$out .= "<div class='t1' style='background-color:#ECFCCF;'>".$sales['reward']."</div>";
							$out .= "<div class='t1' style='background-color:#ECFCCF;'>".$sales['rezident']."</div>";
							$out .= "<div class='t1' style='background-color:#ECFCCF;'>".round($sales['tax_ndfl'])."</div>";
							$out .= "<div class='t1' style='background-color:#ECFCCF;'>".round($sales['reward_to_pay'])."</div>";
							$out .= "<div class='t1' style='background-color:#ECFCCF;'>".round($sales['reward_payed'])."</div>";
							$out .= "<div class='t1' style='background-color:#ECFCCF;'>".$sales['reward_remains']."</div>";
							$out .= "<div class='t1' style='background-color:#ECFCCF; width:100px;'>".getMonthYearNames($sales['payment_date'])."</div>";
							$out .= "<div class='t1' style='background-color:#ECFCCF; color:red;'>Выплата</div>";
						}
						else
						{
							$out .= "<div class='tt1' style='clear:left'>".$sales['name']."</div>";
							$out .= "<div class='t1'>".$sales['cartoons_sold']."</div>";
							$out .= "<div class='t1'>".$sales['reward']."</div>";
							$out .= "<div class='t1'>".$sales['rezident']."</div>";
							$out .= "<div class='t1'>".round($sales['tax_ndfl'])."</div>";
							$out .= "<div class='t1'>".round($sales['reward_to_pay'])."</div>";
							$out .= "<div class='t1'>".round($sales['reward_payed'])."</div>";
							$out .= "<div class='t1'>".$sales['reward_remains']."</div>";
							$out .= "<div class='t1' style='width:100px;'>".getMonthYearNames($sales['payment_date'])."</div>";
							$out .= "<div class='t1'>".$sales['act_number']."</div>";
						}
					$out .= "</div>";

			}
			
				return $out;
	}
}


function get_artists_list()
{
	global $wpdb;
	$sql = "select name, id from wp_product_brands order by name";
	$result = $wpdb->get_results($sql,ARRAY_A);

	if ($result)
	{
		$out = "<select name='artists'>";
		foreach($result as $artist)
		{
				$out .="<option value='".$artist['id']."'>  ".$artist['name']." </option>";
		}
		$out .= "</select>";
		return $out;
	}
	else
		return "error";
}

function get_months_list()
{
	

		$out = "<select name='months'>";
		$out .="<option value='1'> Январь </option>";
		$out .="<option value='2'> Февраль </option>";
		$out .="<option value='3'> Март </option>";
		$out .="<option value='4'> Апрель </option>";
		$out .="<option value='5'> Май </option>";
		$out .="<option value='6'> Июнь </option>";
		$out .="<option value='7'> Июль </option>";
		$out .="<option value='8'> Август </option>";
		$out .="<option value='9'> Сентябрь </option>";
		$out .="<option value='10'> Октябрь </option>";
		$out .="<option value='11'> Ноябрь </option>";
		$out .="<option value='12'> Декабрь </option>";
		$out .= "</select>";
		return $out;
}

function make_payment ($amount, $y, $artist_id, $m)
{
	global $wpdb;

	$payment_day = date('y-m-d',mktime(0, 0, 0, $m, 1, $y));

		// set max act number

				$sql = "select max(act_number) as max from artist_payments";
				$result = $wpdb->get_results($sql,ARRAY_A);
				if ($result)
				{
					// get current max
					//pokazh($result[0]['max'],'max');
					$new_max_act_number = $result[0]['max'] +1;
					// set new max
					$sql = "update wp_options set option_value = '".$new_max_act_number."' where option_name = 'artist_act_number'";
					//pokazh($sql,'sql');

					$result = $wpdb->query($sql);
					//if (!$result) {die('<br />'.$sql.'<br />Can not set new max act number: ' . mysql_error());}
				}

$act_number = $new_max_act_number;

$sql = "insert into artist_payments (artist_id, payment_date, reward_payed, act_number) values (".$artist_id.", '".$payment_day."', ".$amount.", '".$act_number."')";
$result = $wpdb->query($sql);
if (!$result) {die('<br />'.$sql.'<br />Invalid insert payment query: ' . mysql_error());}
echo "платёж прошёл";

}

function getMonthYearNames($date)
{
	//$date = DateTime::createFromFormat('Y-m-j', $date);
	//2011-12-31 00:00:00
	$date = split("-",$date);
	$_month= $date[1];
	$_year =$date[0];
	$date = ru_month($_month)." ".$_year;
	return ($date);
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

<style>
	.t, .tt, .ht, .htt, .t1, .tt1
	{
		color:#2A2A2A;
		font-size:0.9em;
		border:1px solid #E0E0E0; 
		padding:2px;
		padding-right:4px;
		width:60px;
		height:20px;
		margin:1px;
		background-color:white;
		text-align:right;
		vertical-align:middle;
		float: left;
	}
	.tt, .htt,  .tt1
	{
		width:200px;
		text-align:left;
		padding-left:8px;
	}
	.ht
	{
		font-size:0.8em;
		background-color:#C4FFFF;
		height:60px;
		text-align:center;
		vertical-align:middle;
	}
	.htt
	{
		font-size:0.8em;
		background-color:#C4FFFF;
		height:60px;
		text-align:center;
		vertical-align:middle;
	}
	.t1, .tt1
	{
		background-color:#66CC99;
	}
</style>