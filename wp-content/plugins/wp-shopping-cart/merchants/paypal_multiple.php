<?php
$nzshpcrt_gateways[$num]['name'] = 'Paypal';
$nzshpcrt_gateways[$num]['internalname'] = 'paypal_multiple';
$nzshpcrt_gateways[$num]['function'] = 'gateway_paypal_multiple';
$nzshpcrt_gateways[$num]['form'] = "form_paypal_multiple";
$nzshpcrt_gateways[$num]['submit_function'] = "submit_paypal_multiple";
/*
$current_user = wp_get_current_user();
if (isset($current_user->discount))
		$_discount = $current_user->discount;
  else
		$_discount = 0;

pokazh($current_user);
exit;
*/
function gateway_paypal_multiple($seperator, $sessionid)
  {
  global $wpdb;
  $purchase_log_sql = "SELECT * FROM `wp_purchase_logs` WHERE `sessionid`= ".$sessionid." LIMIT 1";
  $purchase_log = $wpdb->get_results($purchase_log_sql,ARRAY_A) ;

  $cart_sql = "SELECT * FROM `wp_cart_contents` WHERE `purchaseid`='".$purchase_log[0]['id']."'";
  $cart = $wpdb->get_results($cart_sql,ARRAY_A) ; 
  
  $transact_url = get_option('transact_url');
  //$transact_url = "http://cartoonbank.ru/?page_id=32";
  // paypal connection variables
  // ales $data['business'] = get_option('paypal_multiple_business');
  //$data['business'] = "igor.aleshin@gmail.com"; // ales
  $data['business'] = "cartoonbank.ru@gmail.com"; // 
  $data['return'] = $transact_url.$seperator."sessionid=".$sessionid."&gateway=paypal";
  $data['cancel_return'] = $transact_url;
  $data['notify_url'] = $transact_url;
  $data['rm'] = '2';
  //$data['image'] = 'src=\"http://www.paypal.com/en_US/i/btn/x-click-but01.gif\" name=\"submit\" alt=\"Make payments with PayPal - its fast, free and secure!\"';  // ales
  
   // look up the currency codes and local price
  //$currency_code = $wpdb->get_results("SELECT `code` FROM `wp_currency_list` WHERE `id`='".get_option(currency_type)."' LIMIT 1",ARRAY_A);
  //$local_currency_code = $currency_code[0]['code'];
  $local_currency_code = "RUB";
  //ales $paypal_currency_code = get_option('paypal_curcode');
  //$paypal_currency_code = "USD";
  $paypal_currency_code = "RUB";

  // Stupid paypal only accepts payments in one of 5 currencies. Convert from the currency of the users shopping cart to the curency which the user has specified in their paypal preferences.
  $curr=new CURRENCYCONVERTER();

  
  $data['currency_code'] = $paypal_currency_code;
  //$data['Ic'] = 'US';
  $data['Ic'] = 'RU';
  $data['bn'] = 'toolkit-php';
  $data['no_shipping'] = '1';
  $data['no_note'] = '1';
  
  switch($paypal_currency_code)
    {
    case "JPY":
    $decimal_places = 0;
    break;
    
    case "HUF":
    $decimal_places = 0;
    break;
    
    default:
    $decimal_places = 2;
    }
  
  $i = 1;
  foreach($cart as $item)
    {
    $sql = "SELECT * FROM `wp_product_list` WHERE `id`='".$item['prodid']."' LIMIT 1";
    //SELECT * FROM `wp_product_list` WHERE `id`='5900' LIMIT 1
    //pokazh($sql);
    $product_data = $wpdb->get_results($sql,ARRAY_A);
    $product_data = $product_data[0];
    //exit("<pre>" . print_r($item,true) ."</pre>");
    /*
    $variation_count = count($product_variations);
    
    $variation_sql = "SELECT * FROM `wp_cart_item_variations` WHERE `cart_id`='".$item['id']."'";
    $variation_data = $wpdb->get_results($variation_sql,ARRAY_A); 
    //exit("<pre>" . print_r($variation_data,true) ."</pre>");
    $variation_count = count($variation_data);
    if($variation_count >= 1)
      {
      $variation_list = " (";
      $j = 0;
      foreach($variation_data as $variation)
        {
        if($j > 0)
          {
          $variation_list .= ", ";
          }
        $value_id = $variation['venue_id'];
        $value_data = $wpdb->get_results("SELECT * FROM `wp_variation_values` WHERE `id`='".$value_id."' LIMIT 1",ARRAY_A);
        $variation_list .= $value_data[0]['name'];              
        $j++;
        }
      $variation_list .= ")";
      }
      else
        {
        $variation_list = '';
        }
    */
    /*
    if($product_data['special']==1)
      {
      $price_modifier = $product_data['special_price'];
      }
      else
        {
        $price_modifier = 0;
        }
    */
    
    ///$local_currency_productprice = ($product_data['price'] - $price_modifier) * get_option('gst_rate');
    
    if (isset($_POST['disc'])){
        $discount = $_POST['disc'];
    }
    else{
        $discount = 0;
    }
    
	//echo("<pre>" . print_r($item,true) ."</pre>");

    $local_currency_productprice = ceil($item['price']*(100-$discount)/100);
    
    //pokazh($discount);
    //pokazh($local_currency_productprice);
    
    
    //$local_currency_shipping = nzshpcrt_determine_item_shipping($item['prodid'], $item['quantity'], $_SESSION['selected_country']);
    if($paypal_currency_code != $local_currency_code)
      {
      $paypal_currency_productprice = $curr->convert($local_currency_productprice,$paypal_currency_code,$local_currency_code);
      //$paypal_currency_shipping = $curr->convert($local_currency_shipping,$paypal_currency_code,$local_currency_code);
      //exit("bad");
      }
      else
        {
        $paypal_currency_productprice = $local_currency_productprice;
        //$paypal_currency_shipping = $local_currency_shipping;
        //exit("good");
        }
    $data['item_name_'.$i] = $product_data['name']; //.$variation_list;
    $data['amount_'.$i] = number_format(sprintf("%01.2f", $paypal_currency_productprice),$decimal_places,'.','');
    //$data['amount_'.$i] = '10'; // ales
    $data['quantity_'.$i] = $item['quantity'];
    $data['item_number_'.$i] = $product_data['id'];
    //exit($paypal_currency_shipping);
    $data['shipping_'.$i] = ''; //number_format($paypal_currency_shipping,$decimal_places,'.','');
    $data['handling_'.$i] = '';
    $i++;
    }
  
  $data['tax'] = '';
  
  /*
  $base_shipping = nzshpcrt_determine_base_shipping(0, $_SESSION['selected_country']);
  
  if($base_shipping > 0)
    {
    $data['item_name_'.$i] = "Shipping";
    $data['amount_'.$i] = number_format(0,$decimal_places,'.','');
    $data['quantity_'.$i] = 1;
    $data['item_number_'.$i] = 0;
    $data['shipping_'.$i] = number_format($base_shipping,$decimal_places,'.','');
    $data['handling_'.$i] = '';
    }
    
  */
  
  $data['custom'] = '';
  $data['invoice'] = $sessionid;
  
  // User details
  /*
  $data['first_name'] = $_POST['firstname'];
  $data['last_name'] = $_POST['lastname'];
  */
  
  $address_data = $wpdb->get_results("SELECT `id`,`type` FROM `wp_collect_data_forms` WHERE `type` IN ('address','delivery_address') AND `active` = '1'",ARRAY_A);
  foreach((array)$address_data as $address)
    {
    $data['address1'] = $_POST['collected_data'][$address['id']];
    if($address['type'] == 'delivery_address')
      {
      break;
      }
    }
  
  $city_data = $wpdb->get_results("SELECT `id`,`type` FROM `wp_collect_data_forms` WHERE `type` IN ('city','delivery_city') AND `active` = '1'",ARRAY_A);
  foreach((array)$city_data as $city)
    {
    $data['city'] = $_POST['collected_data'][$city['id']];
    if($city['type'] == 'delivery_city')
      {
      break;
      }
    }
  $country_data = $wpdb->get_results("SELECT `id`,`type` FROM `wp_collect_data_forms` WHERE `type` IN ('country','delivery_country') AND `active` = '1'",ARRAY_A);
  foreach((array)$country_data as $country)
    {
    $data['country'] = $_POST['collected_data'][$country['id']];
    if($address['type'] == 'delivery_country')
      {
      break;
      }
    }
  //$data['country'] = $_POST['address'];
  
  // Change suggested by waxfeet@gmail.com, if email to be sent is not there, dont send an email address
  /*if($_POST['collected_data'][get_option('email_form_field')] != null)
    {
    $data['email'] = $_POST['collected_data'][get_option('email_form_field')];
    }
    */
  $data['upload'] = '1';
  $data['cmd'] = "_ext-enter";
  $data['redirect_cmd'] = "_cart";
  $datacount = count($data);
  $num = 0;
  $output = "";
  foreach($data as $key=>$value)
    {
    $amp = '&';
    $num++;
    if($num == $datacount)
      {
      $amp = '';
      }
    //$output .= $key.'='.urlencode($value).$amp;
    $output .= $key.'='.urlencode($value).$amp;
    }
  /*  
  echo("<pre>" . print_r($_POST,true) ."</pre>"); 
  echo("<pre>" . print_r($_SESSION,true) ."</pre>");
  exit("<pre>" . print_r($data,true) ."</pre>");
  */
  //header("Content-Type: text/html; charset=utf-8");
  header("Location: ".get_option('paypal_multiple_url')."?".$output);
  exit();
  }

function submit_paypal_multiple()
  {
  update_option('paypal_multiple_business', $_POST['paypal_multiple_business']);
  update_option('paypal_multiple_url', $_POST['paypal_multiple_url']);
  update_option('paypal_curcode', $_POST['paypal_curcode']);
  return true;
  }

function form_paypal_multiple()
  {
  $select_currency[get_option('paypal_curcode')] = "selected='true'";
  $output = "
  <tr>
      <td>
      PayPal Username
      </td>
      <td>
      <input type='text' size='40' value='".get_option('paypal_multiple_business')."' name='paypal_multiple_business' />
      </td>
  </tr>
  <tr>
      <td>
      PayPal Url
      </td>
      <td>
      <input type='text' size='40' value='".get_option('paypal_multiple_url')."' name='paypal_multiple_url' />
      </td>
  </tr>
  <tr>
      <td>
      </td>
      <td>
      <strong>Note:</strong>The URL to use for the paypal gateway is: https://www.paypal.com/cgi-bin/webscr
      </td>
   </tr>
  <tr>
      <td>
      PayPal Accepted Currency (e.g. USD, AUD)
      </td>
      <td>";
/*
$output .= "		<select name='paypal_curcode'>
          <option ".$select_currency['USD']." value='USD'>U.S. Dollar</option>
          <option ".$select_currency['CAD']." value='CAD'>Canadian Dollar</option>
          <option ".$select_currency['AUD']." value='AUD'>Australian Dollar</option>
          <option ".$select_currency['EUR']." value='EUR'>Euro</option>
          <option ".$select_currency['GBP']." value='GBP'>Pound Sterling</option>
          <option ".$select_currency['JPY']." value='JPY'>Yen</option>
          <option ".$select_currency['NZD']." value='NZD'>New Zealand Dollar</option>
          <option ".$select_currency['CHF']." value='CHF'>Swiss Franc</option>
          <option ".$select_currency['HKD']." value='HKD'>Hong Kong Dollar</option>
          <option ".$select_currency['SGD']." value='SGD'>Singapore Dollar</option>
          <option ".$select_currency['SEK']." value='SEK'>Swedish Krona</option>
          <option ".$select_currency['HUF']." value='HUF'>Hungarian Forint</option>
          <option ".$select_currency['DKK']." value='DKK'>Danish Krone</option>
          <option ".$select_currency['PLN']." value='PLN'>Polish Zloty</option>
          <option ".$select_currency['NOK']." value='NOK'>Norwegian Krone</option>
          <option ".$select_currency['CZK']." value='CZK'>Czech Koruna</option>
        </select>";
*/
$output .= "      </td>
   </tr>";
  return $output;
  }
  
  
  
  /*
  * 
  Array
(
    [total] => 13
    [collected_data] => Array
        (
            [1] => Игорь
            [2] => Алёшин
            [3] => igor.aleshin@dataart.com
            [4] => 
            [5] => Картунбанк
        )

    [agree] => yes
    [payment_method] => paypal_multiple
    [submitwpcheckout] => true
    [submit] =>  Оплатить заказ и скачать файлы 
)
Array
(
    [uid] => 1
    [username] => Igor
    [cart_paid] => 
    [nzshpcrt_cart] => Array
        (
            [1] => cart_item Object
                (
                    [product_id] => 5900
                    [product_variations] => 
                    [quantity] => 1
                    [name] => Каникулы Бонифация
                    [price] => 250.00
                    [license] => l1_price
                    [author] => Алёшин Игорь
                )

        )

    [total] => 13
    [nzshpcrt_serialized_cart] => a:1:{i:1;O:9:"cart_item":7:{s:10:"product_id";s:4:"5900";s:18:"product_variations";N;s:8:"quantity";i:1;s:4:"name";s:35:"Каникулы Бонифация";s:5:"price";s:6:"250.00";s:7:"license";s:8:"l1_price";s:6:"author";s:23:"Алёшин Игорь";}}
    [collected_data] => Array
        (
            [1] => Игорь
            [2] => Алёшин
            [3] => igor.aleshin@dataart.com
            [4] => 
            [5] => Картунбанк
        )

    [checkoutdata] => Array
        (
            [total] => 13
            [collected_data] => Array
                (
                    [1] => Игорь
                    [2] => Алёшин
                    [3] => igor.aleshin@dataart.com
                    [4] => 
                    [5] => Картунбанк
                )

            [agree] => yes
            [payment_method] => paypal_multiple
            [submitwpcheckout] => true
            [submit] =>  Оплатить заказ и скачать файлы 
        )

)
Array
(
    [business] => cartoonbank.ru@gmail.com
    [return] => http://test.cartoonbank.ru/?page_id=32&sessionid=1341385130836&gateway=paypal
    [cancel_return] => http://test.cartoonbank.ru/?page_id=32
    [notify_url] => http://test.cartoonbank.ru/?page_id=32
    [rm] => 2
    [currency_code] => RUB
    [Ic] => RU
    [bn] => toolkit-php
    [no_shipping] => 1
    [no_note] => 1
    [item_name_1] => Каникулы Бонифация
    [amount_1] => 9.00
    [quantity_1] => 1
    [item_number_1] => 5900
    [shipping_1] => 
    [handling_1] => 
    [tax] => 
    [custom] => 
    [invoice] => 1341385130836
    [upload] => 1
    [cmd] => _ext-enter
    [redirect_cmd] => _cart
)* 
  */
  
  ?>