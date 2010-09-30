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
        <td style='padding:4px;'><? if (isset($_SESSION['total'])) {echo ($_SESSION['total']." руб.");} ?></td>
      </tr>

	  <tr>
        <td style='padding-top:15px;padding-bottom:5px;border-bottom: 1px solid #c8c8c8;' colspan="2"><b>Метод оплаты</b></td>
      </tr>

	 <?php
      $i = 0;
      $curgateway = get_option('payment_gateway');
      foreach($GLOBALS['nzshpcrt_gateways'] as $gateway)
        {
          $gateway_name = $gateway['name'];
           $i = $i + 1;
      ?>
      <tr>
        <td colspan='2'>
        <input type='radio' name='payment_method' value='<?php echo $gateway['internalname']; ?>' id='payment_method_<?php echo $i ?>' <?php
         if (isset($_SESSION['checkoutdata']['payment_method']))
         {
            if ($_SESSION['checkoutdata']['payment_method'] == $gateway['internalname']  && (isset($_SESSION['WpscGatewayErrorMessage']) && $_SESSION['WpscGatewayErrorMessage']==''))
                echo "checked='checked'";
         }
         //pokazh($_SESSION['WpscGatewayErrorMessage']);
         if (/*temporary enabled wallet only */$gateway['internalname'] == 'wallet' && ($userdata->wallet >= (float) $_SESSION['total'])) 
            echo "checked='checked'"; 
         else
            echo "disabled='disabled'";
         ?> />
        <label for='payment_method_<?php echo $i ?>'>Оплата через <b><?php echo $gateway_name; ?></b></label>
        <?php
            if ($gateway['internalname'] == "wallet")
            {
              echo " (доступно ". (float) $userdata->wallet ." руб.)";
			  if (($userdata->wallet < (float) $_SESSION['total']))
				{
				  echo "<br /><span style='color: red;'>".$_SESSION['WpscGatewayErrorMessage']."</span>";
				}
			//pokazh($_SESSION);
            }
        ?>        
        </td>
      </tr>
    
      <?php
      }
    ?>
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
      <td>После нажатия на кнопку "Оплатить" произойдет уменьшение вашего Личного Счета на размер стоимости подтвержденной вами Лицензии. Вы перейдёте на страницу с прямыми ссылками на заказанные файлы высокого разрешения. На указанный вами при регистрации электронный почтовый адрес будет отправлено сообщение, содержащее:<br>1. Ссылку на выбранное изображение в виде файла качественного разрешения, доступный вам для скачивания в течение 1 недели со дня получения сообщения.<br>2. Лицензионный Договор на использование каждого выбранного изображения.
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
/*
echo("<pre>POST:".print_r($_POST,true)."</pre>");
echo("<pre>SESSION:".print_r($_SESSION,true)."</pre>");
echo("<pre>userdata:".print_r($userdata,true)."</pre>");

*/

?> 