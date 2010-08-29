<?php
global $wpdb,$gateway_checkout_form_fields;
$_SESSION['cart_paid'] = false;
if (isset($_SESSION['checkoutdata']))
{
	$checkout = $_SESSION['checkoutdata'];
}
else{$checkout = null;}
if(get_option('permalink_structure') != '')
{
    $seperator ="?";
}
 else
{
    $seperator ="&amp;";
}
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
    $currenturl = get_option('checkout_url') . $seperator .'total='.$_GET['total'];
if(!isset($_GET['result']))
  {
?>
<div class="wrap">

<?php
 //echo TXT_WPSC_CREDITCARDHANDY;
 echo "<br /><br />";
if(isset($_SESSION['nzshpcrt_checkouterr']))
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
 <form action='<?php echo  $currenturl;?>' method='POST'><?php
  $form_sql = "SELECT * FROM `".$wpdb->prefix."collect_data_forms` WHERE `active` = '1' ORDER BY `order`;";
  $form_data = $wpdb->get_results($form_sql,ARRAY_A);
  echo "<tr><td  style='padding-bottom:5px;border-bottom: 1px solid #c8c8c8;' colspan='2'><b>Подтвердите информацию о себе:</b></td></tr>";

  foreach($form_data as $form_field)
    {
    if($form_field['type'] == 'heading')
      {

	// First row


      echo "
      <tr style='padding:5px;'>
        <td  style='padding:5px;' colspan='2'>\n\r";
      echo "<strong>".$form_field['name']."</strong>";        
      echo "
        </td>
      </tr>\n\r";
      }
      else
        {
        echo "
        <tr>
          <td style='padding-bottom:5px;'>\n\r";
        echo $form_field['name'];
        if($form_field['mandatory'] == 1)
          {
          if(!(($form_field['type'] == 'country') || ($form_field['type'] == 'delivery_country')))
            {
            echo "*";
            }
          }
        echo "
          </td>\n\r
          <td style='padding:2px;'>\n\r";
        switch($form_field['type'])
          {
          case "text":
          case "city":
          case "delivery_city":
		  echo "<input type='text' style='width: 300px; padding: 2px; border: 1px solid #c8c8c8' value='".$form_field_id."' name='collected_data[".$form_field['id']."]' />";
          break;
          
          case "address":
          case "delivery_address":
          case "textarea":
          echo "<textarea name='collected_data[".$form_field['id']."]'>".$form_field_id."</textarea>";
          break;
          
          case "country":
          case "delivery_country":
          $country_name = $wpdb->get_var("SELECT `country` FROM `".$wpdb->prefix."currency_list` WHERE `isocode`='".$_SESSION['selected_country']."' LIMIT 1");
          echo "<input type='hidden' name='collected_data[".$form_field['id']."]' value='".get_option('base_country')."'>".$country_name." ";
          break;
          
          case "email":
		  echo "<input type='text' style='width: 300px; padding: 2px; border: 1px solid #c8c8c8' value='".$form_field_id."' name='collected_data[".$form_field['id']."]' />";
          break;
          
          default:
		  echo "<input type='text' style='width: 300px; padding: 2px; border: 1px solid #c8c8c8' value='".$form_field_id."' name='collected_data[".$form_field['id']."]' />";

          break;
          }
        echo "
          </td>
        </tr>\n\r";
        }
    }
?>
    <tr>
      <td style='padding:4px;'>&nbsp;</td>
      <td style='padding:4px;'><span style='font-size: 7pt;'>Поля, отмеченные звёздочкой обязательны для заполнения.</span></td>
    </tr>
	
	<?php
    if(isset($gateway_checkout_form_fields))
      {
      echo $gateway_checkout_form_fields;
      }
    $termsandconds = get_option('terms_and_conditions');
    if($termsandconds != '')
      {
      ?>
    <tr>
      <td>
      </td>
      <td>
      <input type='checkbox' value='yes' name='agree' /> <?php echo TXT_WPSC_TERMS1;?><a target='_blank' href='' class='termsandconds' onclick='window.open("<?php
      echo get_option('siteurl')."?termsandconds=true";
       ?>","","width=550,height=600,scrollbars,resizable"); return false;'><?php echo TXT_WPSC_TERMS2;?></a>
      </td>
    </tr>
      <?php
      }
      else
        {
        echo "<input type='hidden' value='yes' name='agree' />";
        echo "";
        }

        ?>
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
            if ($_SESSION['checkoutdata']['payment_method'] == $gateway['internalname'])
                echo "checked='checked'";
         }
         
         if (/*$i == 1 temporary enabled wallet only */$gateway['internalname'] == 'wallet') 
            echo "checked='checked'"; 
         else
            echo "disabled='disabled'";
         ?> />
        <label for='payment_method_<?php echo $i ?>'><?php echo TXT_WPSC_PAY_USING;?> <b><?php echo $gateway_name; ?></b></label>
        <?php
            global $userdata;
            if ($gateway['internalname'] == "wallet")
            {
              echo "(доступно". (float) $userdata->wallet ." руб.)";
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
      <td>После нажатия на кнопку "Оплатить" произойдет уменьшение Вашего Личного Счета на размер стоимости выбранной и подтвержденной Лицензии. Вы перейдёте на страницу с прямыми ссылками на заказанные файлы высокого разрешения. На указанный вами электронный почтовый адрес будет отправлено письмо с а) ссылками на заказанные файлы б) копиями лицензионного соглашения для каждого из изображений.
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
  ?> 