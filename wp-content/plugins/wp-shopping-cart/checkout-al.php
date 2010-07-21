<?php
global $wpdb,$gateway_checkout_form_fields,$user_email,$userdata;
$_SESSION['cart_paid'] = false;
$checkout = $_SESSION['checkoutdata'];
if(get_option('permalink_structure') != '')
   {
   $seperator ="?";
   }
   else
   {
   $seperator ="&amp;";
   }
$currenturl = get_option('checkout_url') . $seperator .'total='.$_GET['total'];

if(is_numeric($_GET['total']) && !isset($_GET['result']))
{
	?>
	<div class="wrap">

	<?php
	if($_SESSION['nzshpcrt_checkouterr'] != null)
	  {
	  echo "<span style='color: red;'>".$_SESSION['nzshpcrt_checkouterr']."</span><br />";
	  $_SESSION['nzshpcrt_checkouterr'] = '';
	  }

	?>
	 Ссылки на файлы для скачивания будут высланы по адресу:
	 <table class="shoppingcart">
	 <!-- <form action='<?php echo  $currenturl;?>' method='POST'> --><?php
	  $form_sql = "SELECT * FROM `".$wpdb->prefix."collect_data_forms` WHERE `active` = '1' ORDER BY `order`;";
	  $form_data = $wpdb->get_results($form_sql,ARRAY_A);
	  //exit("<pre>".print_r($form_data,true)."</pre>"); 
	  foreach($form_data as $form_field)
		{
		if($form_field['type'] == 'heading')
		  {
		  echo "
		  <tr>
			<td colspan='2'>\n\r";
		  echo "<strong>".$form_field['name']."</strong>";        
		  echo "
			</td>
		  </tr>\n\r";
		  }
		  else
			{
			echo "
			<tr>
			  <td align='right'><strong>\n\r";
			echo $form_field['name'];
			if($form_field['mandatory'] == 1)
			  {
			  echo "*";
			  }
			echo "
			  :</strong></td>\n\r
			  <td>\n\r";

			switch($form_field['type'])
			  {
			  case "text":
				  if ($form_field['name'] == "Имя")
				  {
				  echo $userdata->first_name;
				  //echo "<input style='width:180px;' type='text' value='".$userdata->first_name."' name='collected_data[".$form_field['id']."]' />";
				  }
				  elseif ($form_field['name'] == "Фамилия")
				  {
					  echo $userdata->last_name;
				  //echo "<input style='width:180px;' type='text' value='".$userdata->last_name."' name='collected_data[".$form_field['id']."]' />";
				  }
				  break;
			  case "city":
			  case "delivery_city":
			  //echo "<input style='width:180px;' type='text' value='".$_SESSION['collected_data'][$form_field['id']]."' name='collected_data[".$form_field['id']."]' />";
			  break;
			  
			  case "address":
			  case "delivery_address":
			  case "textarea":
			  //echo "<textarea style='width:180px;' name='collected_data[".$form_field['id']."]'>".$_SESSION['collected_data'][$form_field['id']]."</textarea>";
			  break;
			  
			  case "country":
			  case "delivery_country":
			  //echo "<select name='collected_data[".$form_field['id']."]'>".nzshpcrt_country_list($_SESSION['collected_data'][$form_field['id']])."</select>";
			  break;
			  
			  case "email":
				  echo $user_email;
			  //echo "<input type='text' value='".$_SESSION['collected_data'][$form_field['id']]."' name='collected_data[".$form_field['id']."]' />";
			  //echo "<input style='width:180px;' type='text' value='".$user_email."' name='collected_data[".$form_field['id']."]' />";
			  break;
			  
			  default:
			  //echo "<input style='width:180px;' type='text' value='".$_SESSION['collected_data'][$form_field['id']]."' name='collected_data[".$form_field['id']."]' />";
			  break; 
			  }
			echo "
			  </td>
			</tr>\n\r";
			}
		}
	?>
		<?php
		if(isset($gateway_checkout_form_fields))
		  {
		  echo $gateway_checkout_form_fields;
		  }
		$termsandconds = get_option('terms_and_conditions');
		if($termsandconds != '')
		  {
		  ?>

		  <?php
		  }
		  else
			{
			echo "<input type='hidden' value='yes' name='agree' />";
			echo "";
			}
		?>

	</table>

	<fieldset>
<?

//echo "<pre>";
//print_r($userdata);
//echo "</pre>";

	$limit_data = get_limit($userdata->ID); 
	$stop_date = $limit_data[0]->stop_date;
	$limit = $limit_data[0]->limit;


	
?>
<legend><?php _e('Ваш <span title="Предоплаченный Вами или авансированный сайтом для последующей оплаты" style="border: 1px dashed ; cursor:help"> &nbsp;лимит&nbsp;</span> на скачивание'); ?></legend>
<label><?php _e('Количество файлов: '); echo $limit;?><br />
<!-- <input type="text" name="user_login" value="<?php echo $limit; ?>" disabled="disabled" /> -->
</label>
<label><?php _e('Дата окончания контракта: '); echo $stop_date; ?><br />
<!-- <input type="text" name="user_login" value="<?php echo $stop_date; ?>" disabled="disabled" /> -->
</label>
</fieldset> 

<br>
<fieldset>
<legend><?php _e('Выберите способ оплаты:'); ?></legend>
<?php if ($limit >0) {
	al_direct_download();
	}
	al_paypal_payment_method();
	//al_rupay_payment_method();
?>
</fieldset> 

	</div>
	<?php 
}
  else
    {
    echo TXT_WPSC_BUYPRODUCTS;
    }

function get_limit($user_id){
		global $current_user, $wpdb;
		$sql = "SELECT `stop_date`,`limit` FROM `al_download_limit` WHERE `user_id` = ".$user_id." LIMIT 1";
		$limit_data = $wpdb->get_results($sql);
		return $limit_data;
}  

function al_direct_download(){
	$currenturl = get_option('checkout_url').'&amp;total='.$_GET['total'];
	?>
	<label><h3>- Воспользоваться предоплатой</h3>
			<form action='<?php echo  $currenturl;?>' method='POST'>
			<input type='checkbox' value='yes' name='agree' /> 
			<?php echo TXT_WPSC_TERMS1;?>
			<a target='_blank' href='' class='termsandconds' onclick='window.open("<?php
			echo get_option('siteurl')."?termsandconds=true";
			   ?>","","width=550,height=600,scrollbars,resizable"); return false;'><?php echo TXT_WPSC_TERMS2;?>.</a>
			<input type='hidden' value='true' name='submitwpcheckout' />
			<!-- <input type='hidden' value='<? echo ($userdata->first_name); ?>' name='collected_data[0]' />
			<input type='hidden' value='<? echo ($userdata->last_name); ?>' name='collected_data[1]' />
			<input type='hidden' value='<? echo ($userdata->user_email); ?>' name='collected_data[2]' /> -->
			<input type='hidden' value='1' name='payment_method' />
			<input type='hidden' value='testmode' name='curgateway' />
			<input type='submit' value='<?php echo TXT_WPSC_MAKEPURCHASE;?>' name='submit' />
			</form>
	</label>
	<?
}

function al_paypal_payment_method(){
	$currenturl = get_option('checkout_url').'&amp;total='.$_GET['total'];
	?>
	<label><h3>- Оплатить через службу Paypal.com</h3>(карточкой или со счёта Paypal)
			<form action='<?php echo  $currenturl;?>' method='POST'>
			<input type='hidden' value='true' name='submitwpcheckout' />
			<input type='hidden' value='2' name='payment_method' />
			<input type='hidden' value='paypal_multiple' name='curgateway' />

			<!-- <input type='hidden' value='<? echo ($userdata->first_name); ?>' name='collected_data[0]' />
			<input type='hidden' value='<? echo ($userdata->last_name); ?>' name='collected_data[1]' />
			<input type='hidden' value='<? echo ($userdata->user_email); ?>' name='collected_data[2]' /> -->
			

			<!-- <input type="hidden" name="cmd" value="_xclick">
			<input type="hidden" name="business" value="aleshin@chance.ru">
			<input type="hidden" name="item_name" value="Several cartoons">
			<input type="hidden" name="currency_code" value="USD">
			<input type="hidden" name="amount" value="5.00">
			<input type="image" src="http://www.paypal.com/en_US/i/btn/x-click-but01.gif" name="submit" alt="Make payments with PayPal - it's fast, free and secure!"> -->
			</form>
	</label>
	<?
}

function al_rupay_payment_method(){
	?>
	<label><h3>- Оплатить через службу Rupay.com</h3>(электронными деньгами или через Сбербанк) 
	</label>
	<?
}
  ?> 

