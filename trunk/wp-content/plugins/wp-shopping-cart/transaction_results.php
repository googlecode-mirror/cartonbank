<?php
global $wpdb;
$curgateway = get_option('payment_gateway');
$sessionid = $_GET['sessionid'];
$errorcode = '';
$transactid = '';
//$cart = $_SESSION['nzshpcrt_cart'];


//echo("<pre>SESSION:".print_r($_SESSION,true)."</pre>");
//echo("<pre>POST:".print_r($_POST,true)."</pre>");
//echo("<pre>GET:".print_r($_GET,true)."</pre>");



if($sessionid != null)
  {

  $message = "<div class='wrap'>Спасибо, ваш заказ оформлен. Вы можете скачать заказ используя ссылки ниже.<br>
  Вы заказали следующие картинки:</div>";

	include('al_cart_function.php');

	$license = false;
	$cart_content = cart_product_list_string($license);

	$message = $message.$cart_content;

	//echo $message;


	$message_html = $message;



  $report = 'Были заказаны следующие изображения:';

  $selectsql = "SELECT * FROM `wp_purchase_logs` WHERE `sessionid`= ".$sessionid." LIMIT 1";
  $check = $wpdb->get_results($selectsql,ARRAY_A) ;


  $cartsql = "SELECT * FROM `wp_cart_contents` WHERE `purchaseid`=".$check[0]['id']."";
  $cart = $wpdb->get_results($cartsql,ARRAY_A); 
  
  // gets first email address from checkout details
  $email_form_field = $wpdb->get_results("SELECT `id`,`type` FROM `wp_collect_data_forms` WHERE `type` IN ('email') AND `active` = '1' ORDER BY `order` ASC LIMIT 1",ARRAY_A);
  $email_address = $wpdb->get_results("SELECT * FROM `wp_submited_form_data` WHERE `log_id`=".$check[0]['id']." AND `form_id` = '".$email_form_field[0]['id']."' LIMIT 1",ARRAY_A);
  $email = $email_address[0]['value'];
  }

$siteurl = get_option('siteurl');
  
$previous_download_ids = Array(0);  
  
if($cart != null && ($errorcode == 0))
  {
  foreach($cart as $row)
     {
/*
CREATE TABLE `wp_product_list` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `name` text NOT NULL,
  `description` longtext NOT NULL,
  `additional_description` longtext NOT NULL,
  `price` varchar(20) NOT NULL default '',
  `pnp` varchar(20) NOT NULL default '',
  `international_pnp` varchar(20) NOT NULL default '',
  `file` bigint(20) unsigned NOT NULL default '0',
  `image` text NOT NULL,
  `category` bigint(20) unsigned NOT NULL default '0',
  `brand` bigint(20) unsigned NOT NULL default '0',
  `quantity_limited` char(1) NOT NULL default '',
  `quantity` int(10) unsigned NOT NULL default '0',
  `special` char(1) NOT NULL default '',
  `special_price` varchar(20) NOT NULL default '',
  `display_frontpage` char(1) NOT NULL default '',
  `notax` char(1) NOT NULL default '0',
  `active` char(1) NOT NULL default '1',
*/
     $link ="";
     $productsql= "SELECT * FROM `wp_product_list` WHERE `id`=".$row['prodid']."";
     $product_data = $wpdb->get_results($productsql,ARRAY_A) ;
	 
	 if($product_data[0]['file'] > 0)
       {
       $wpdb->query("UPDATE `wp_download_status` SET `active`='1' WHERE `fileid`='".$product_data[0]['file']."' AND `purchid` = '".$check[0]['id']."' LIMIT 1");
       $download_data = $wpdb->get_results("SELECT * FROM `wp_download_status` WHERE `fileid`='".$product_data[0]['file']."' AND `purchid`='".$check[0]['id']."' AND `id` NOT IN (".make_csv($previous_download_ids).") LIMIT 1",ARRAY_A);
       $download_data = $download_data[0];
       $link = $siteurl."?downloadid=".$download_data['id'];
       $previous_download_ids[] = $download_data['id'];
       }
       
     
      if($link != '')
        {
		$message .= " - ". $product_data[0]['name'] . $variation_list ."  Кликните здесь, чтобы скачать: $link\n";
        $message_html .= " - #".$product_data[0]['id']. "; название: <b>".$product_data[0]['name'] . "</b>;<br> описание: ".$product_data[0]['description']. "; " . $variation_list ."<br><a href='$link'>Скачать #".$product_data[0]['id']."</a><br><p></p>";
        }
        

		$message_price = ''; //ales
		$report .= " - ". $product_data[0]['name'] ."  ".$message_price ."\n";
     }
     
  $total = '';  
  $total_shipping = ''; //nzshpcrt_determine_base_shipping($total_shipping, $country);
  $message .= "<br><br>";
  $message .= "Общая стоимость: ".nzshpcrt_currency_display($total_shipping,1,true)."\n\r";
  $message .= "Всего: ".nzshpcrt_currency_display(($total+$total_shipping),1,true)."\n\r";
  
  
  $message_html .= "\n\r";

	$headers = "From: ".get_option('return_email')."\r\n" .
			   'X-Mailer: PHP/' . phpversion() . "\r\n" .
			   "MIME-Version: 1.0\r\n" .
			   "Content-Type: text/html; charset=utf-8\r\n" .
			   "Content-Transfer-Encoding: 8bit\r\n\r\n";

  if(isset($_GET['ti']))
    {
    $message .= "Your Transaction ID: " . $_GET['ti'];
    $message_html .= "Your Transaction ID: " . $_GET['ti'];
    $report .= "Transaction ID: " . $_GET['ti'];
    }
  if($email != '')
    {
    mail($email, 'Purchase Receipt', $message, $headers);
    }
  
  $purch_sql = "SELECT * FROM `wp_purchase_logs` WHERE `id`!='".$check[0]['id']."'";
  $purch_data = $wpdb->get_results($purch_sql,ARRAY_A) ; 

  $report_user = "О заказчике."; 
  
  


  $form_sql = "SELECT * FROM `wp_submited_form_data` WHERE `log_id` = '".$check[0]['id']."'";
  $form_data = $wpdb->get_results($form_sql,ARRAY_A);
  if($form_data != null)
    {
    foreach($form_data as $form_field)
      {
      $form_sql = "SELECT * FROM `wp_collect_data_forms` WHERE `id` = '".$form_field['form_id']."' LIMIT 1";
      $form_data = $wpdb->get_results($form_sql,ARRAY_A);
      $form_data = $form_data[0];
      if($form_data['type'] == 'country' )
        {
        $report_user .= $form_data['name'].": ".get_country($form_field['value'])."\n";
        }
        else
          {
          $report_user .= $form_data['name'].": ".$form_field['value']."\n";
          }
      }
    }
  
  $report_user .= "\n\r";
  $report = $report_user . $report;
  if(get_option('purch_log_email') != null)
    {
    mail(get_option('purch_log_email'), 'Purchase Report', $report, $headers);
    }
  //$_SESSION['nzshpcrt_cart'] = '';
  //$_SESSION['nzshpcrt_cart'] = Array();
  
  echo '<div class="wrap">';
  if($sessionid != null)
    {
    echo "<h3>Транзакция прошла успешно</h3>";
    echo "<br />" . nl2br(str_replace("$",'\$',$message_html));
    }
  echo '</div>';
  }
  else
    {
    echo '<div class="wrap">';
    echo '<h3>Сначала добавьте изображение в корзину</h3>';
    echo '</div>';
    }   
  

if($check != null)
  {
	$authcode = ""; // wtf?? ales
  $sql = "UPDATE `wp_purchase_logs` SET `statusno` = '".$errorcode."',`transactid` = '".$transactid."',`authcode` = '".$authcode."',`date` = '".time()."' WHERE `sessionid` = ".$sessionid." LIMIT 1";
   //$wpdb->query($sql) ;
   }

	 
//http://localhost/?page_id=31&sessionid=8061284226824
//sql:UPDATE `wp_purchase_logs` SET `statusno` = '',`transactid` = '',`authcode` = '',`date` = '1284228427' WHERE `sessionid` = 1171284228397 LIMIT 1
//echo("<pre>message:".print_r($message,true)."</pre>");
?>