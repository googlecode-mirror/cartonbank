<?php
global $wpdb,$gateway_checkout_form_fields;
global $userdata;
/*
if (isset($userdata->ID) && is_numeric($userdata->ID))
{
	$sql = "SELECT meta_key,meta_value FROM wp_usermeta as m WHERE m.user_id = ".$userdata->ID;
	$result = $wpdb->get_results($sql,ARRAY_A);
	pokazh($result);
}
*/            
$_SESSION['cart_paid'] = false;
if (isset($_SESSION['checkoutdata']))
{
	$checkout = $_SESSION['checkoutdata'];
}
else{$checkout = null;}

if(isset($_SESSION['collected_data']))
{
	if (isset($form_field['id']))
	{
		$form_field_id = $_SESSION['collected_data'][$form_field['id']];
	}
	else {$form_field_id = null;}
}
else {$form_field_id = null;}

$currenturl = get_option('checkout_url');

if (isset($_GET['total']))       
    $currenturl = get_option('checkout_url');// . $seperator .'total='.$_GET['total'];
if(!isset($_GET['result']))
  {
?><div class="wrap">
<?php


// Errors
if(isset($_SESSION['nzshpcrt_checkouterr']) && $_SESSION['nzshpcrt_checkouterr']!='')
  {
  echo "<br /><span style='color: red;'>".$_SESSION['nzshpcrt_checkouterr']."</span>";
  $_SESSION['nzshpcrt_checkouterr'] = '';
  }
if (isset($_SESSION['wallet']))
{
    if ($_SESSION['wallet'] == 'decline')
    {
        echo "<br /><span style='color: red;'>".$_SESSION['WpscGatewayErrorMessage']."</span>";
        $_SESSION['wallet'] = '';
    }
}
?>

<!-- Table begins here -->
 <table>
 <form action='<?php echo  $currenturl;?>' method='POST'>
 <input type="hidden" name="total" value="<?echo $_SESSION['total'];?>">

 <?php
 echo "<tr><td style='padding-bottom:5px;border-bottom: 1px solid #c8c8c8;' colspan='2'><b>Подтвердите информацию о себе:</b></td></tr>";

?>
	<tr><td style="padding-bottom: 5px;">Имя*</td>
		<td style="padding: 2px;">
		<input style="width: 300px; padding: 2px; border: 1px solid rgb(200, 200, 200);" value="<?if (isset($userdata->user_firstname)){echo $userdata->user_firstname;}?>" name="collected_data[1]" type="text">
		</td>
	</tr>
	<tr><td style="padding-bottom: 5px;">Фамилия*</td>
		<td style="padding: 2px;">
		<input style="width: 300px; padding: 2px; border: 1px solid rgb(200, 200, 200);" value="<?if (isset($userdata->user_lastname)){echo $userdata->user_lastname;}?>" name="collected_data[2]" type="text">
		</td>
	</tr>
	<tr><td style="padding-bottom: 5px;">Email*</td>
		<td style="padding: 2px;">
		<input style="width: 300px; padding: 2px; border: 1px solid rgb(200, 200, 200);" value="<?if (isset($userdata->user_email)){echo $userdata->user_email;}?>" name="collected_data[3]" type="text">
		</td>
	</tr>
	<tr><td style="padding-bottom: 5px;">Телефон</td>
		<td style="padding: 2px;">
		<input style="width: 300px; padding: 2px; border: 1px solid rgb(200, 200, 200);" value="" name="collected_data[4]" type="text">
		</td>
	</tr>
	<tr><td style="padding-bottom: 5px;">СМИ</td>
		<td style="padding: 2px;">
		<input style="width: 300px; padding: 2px; border: 1px solid rgb(200, 200, 200);" value="" name="collected_data[5]" type="text">
		</td>
	</tr>


    <tr>
      <td style='padding:4px;'>&nbsp;</td>
      <td style='padding:4px;'><span style='font-size: 7pt;'>* Поля, отмеченные звёздочкой обязательны для заполнения.<br>На указанный электронный ящик будет выслана ссылка для скачивания файла(ов).</span> <input type='hidden' value='yes' name='agree'>
	  </td>
    </tr>
	
	  <tr>
        <td style='padding-top:15px;padding-bottom:5px;border-bottom: 1px solid #c8c8c8;' colspan="2"><b>Сумма оплаты</b></td>
      </tr>
	  
	  <tr>
        <td style='padding:4px;' colspan="2"><? if (isset($_SESSION['total'])) {echo ($_SESSION['total']." руб.");} ?></td>
      </tr>

	  <tr>
        <td style='padding-top:15px;padding-bottom:5px;border-bottom: 1px solid #c8c8c8;' colspan="2"><b>Методы оплаты</b> (выберите один из способов оплаты)</td>
      </tr>

	 <?php
      $i = 0;
      $curgateway = get_option('payment_gateway');

//pokazh($curgateway,"curgateway");
//pokazh($GLOBALS['nzshpcrt_gateways'],"GLOBALS['nzshpcrt_gateways']");
$rooturl = get_option('siteurl');
		?>
	  <tr>
        <td width='270'>
		<input type="radio" name="payment_method" value="wallet" id="payment_method_2"/> 
        <label for='payment_method_2'>Оплата через<br><b>Личный Счёт (по предоплате) </b></label>
		<br><img src="<?php echo($rooturl);?>/img/gate_beznal.png">
        </td>
		<td>
		После нажатия на кнопку "Оплатить" произойдет уменьшение вашего Личного Счета на размер стоимости подтвержденной вами Лицензии. Вы перейдёте на страницу с прямыми ссылками на заказанные файлы высокого разрешения. На указанный вами при регистрации электронный почтовый адрес будет отправлено сообщение, содержащее:<br>1. Ссылку на выбранное изображение в виде файла качественного разрешения, доступный вам для скачивания в течение 1 недели со дня получения сообщения.<br>2. Лицензионный Договор на использование каждого выбранного изображения.
		</td>
      </tr>
	  <tr>
        <td width='270'>
		<input type="radio" name="payment_method" value="check" id="payment_method_4"/> 
        <label for='payment_method_4'>Оплата через<br><b>Сбербанк</b></label>
		<br><!-- <img src="<?php echo($rooturl);?>/img/gate_beznal.png"> -->
        </td>
		<td>
		Распечатать бланк и оплатить его в любом отделении Сбербанка. Также возможен платёж из других российских банков. Будьте внимательны! Возврат внесённых средств не производится.
		<br>
		Для вас будет сформирован счёт на оплату. Вы можете его распечатать и оплатить в течение 5 банковских дней. Для ускорения процесса сообщите нам о факте оплаты счета на адрес cartoonbank.ru@gmail.com. Когда счет будет оплачен изображения и лицензии к ним будут отправлены на указанный вами при регистрации e-mail.
		</td>
      </tr>
	  <tr><td>
		<input type="radio" name="payment_method" value="robokassa" id="payment_method_3"><!-- disabled="disabled" -->
		<label for='payment_method_3'>Оплата через<br><b>Робокассу</b></label>
		<br><img src="<?php echo($rooturl);?>/img/gate_robokassa.png">
	  </td>
		<td>
		После нажатия на кнопку "Оплатить" вы перейдёте на сайт <a href="http://robokassa.ru/" target=_blank>Робокассы</a>, где выберете валюту оплаты. После успешной оплаты вы перейдёте на страницу с прямыми ссылками на заказанные файлы высокого разрешения. На указанный вами при регистрации электронный почтовый адрес будет отправлено сообщение, содержащее:<br>1. Ссылку на выбранное изображение в виде файла качественного разрешения, доступный вам для скачивания в течение 1 недели со дня получения сообщения.<br>2. Лицензионный Договор на использование каждого выбранного изображения.
		</td>
	  </tr>

	  <?if (WP_DEBUG) {?>
	  <tr><td>
		<input type="radio" name="payment_method" value="paypal_multiple" id="payment_method_1"><!--  disabled="disabled" -->
		<label for='payment_method_1'>Оплата через <b>PayPal</b></label>
		<br><img src="<?php echo($rooturl);?>/img/gate_paypal.png">
	  </td>
		<td>
		После нажатия на кнопку "Оплатить" вы перейдёте на сайт <a href="http://paypal.com" target=_blank>Paypal</a>, где оплатите заказ. После успешной оплаты вы перейдёте на страницу с прямыми ссылками на заказанные файлы высокого разрешения. На указанный вами при регистрации электронный почтовый адрес будет отправлено сообщение, содержащее:<br>1. Ссылку на выбранное изображение в виде файла качественного разрешения, доступный вам для скачивания в течение 1 недели со дня получения сообщения.<br>2. Лицензионный Договор на использование каждого выбранного изображения.
		</td>
	  </tr>

	  <?} ?>

    <tr>
      <td style='padding-top:5px;border-top: 1px solid #c8c8c8;'>
      </td>
      <td style='padding-top:5px;border-top: 1px solid #c8c8c8;'>
      <input type='hidden' value='true' name='submitwpcheckout' />
      <input type='submit' value='Оплатить' name='submit' />
      </td>
    </tr>
	<tr>
      <td>&nbsp;
      </td>
      <td>
      </td>
    </tr>
</table>
</form>
</div>
<?php
  }
  else
    {
    echo TXT_WPSC_BUYPRODUCTS;
    }
//echo("<pre>POST:".print_r($_POST,true)."</pre>");
//echo("<pre>SESSION:".print_r($_SESSION,true)."</pre>");
//echo("<pre>userdata:".print_r($userdata,true)."</pre>");

?> 
