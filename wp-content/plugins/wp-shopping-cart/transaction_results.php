<?php
global $wpdb, $result_no_license_text;
$message_html = '';
$check = '';
$_total = '';
$curgateway = get_option('payment_gateway');
if (isset($_GET['sessionid'])){
    $sessionid = $_GET['sessionid'];
}
else{
    $sessionid = '';
}
$errorcode = '';
$transactid = '';

//echo("<pre>SESSION:".print_r($_SESSION,true)."</pre>");
//echo("<pre>POST:".print_r($_POST,true)."</pre>");
//echo("<pre>GET:".print_r($_GET,true)."</pre>");

if($sessionid != null)
{
  include('al_cart_function.php');
  
  $message = "<div class='wrap'>Спасибо за пользование услугами сайта cartoonbank.ru! В электронном письме содержатся тест лицензии и ссылка для скачивания. Вам доступны ".get_option('max_downloads')." попыток скачивания по ссылке в письме. Вы можете скачать ваш заказ используя ссылки ниже.<br /></div>";

    $license = false;

    $cart_content = cart_product_list_string($license);
    
    $message_html = '';
    
    if ($cart_content!='')
        {$message = $message.$cart_content;}
    else {$message = 'Корзина пуста';}

    $message_html = $message;

    $report = ""; // 'Были заказаны следующие изображения:';

    $selectsql = "SELECT * FROM `wp_purchase_logs` WHERE `sessionid`= ".$sessionid." LIMIT 1";
    $check = $wpdb->get_results($selectsql,ARRAY_A) ;

    if (isset($check[0]['totalprice']))
        $_total = $check[0]['totalprice'];
    else
        $_total = '';
  

    //pokazh ($check,"check");

  if (isset($check[0]['id']))
  {
    $cartsql = "SELECT * FROM `wp_cart_contents` WHERE `purchaseid`=".$check[0]['id']."";
    $cart = $wpdb->get_results($cartsql,ARRAY_A); 
      
  
  // gets first email address from checkout details
  $email_form_field = $wpdb->get_results("SELECT `id`,`type` FROM `wp_collect_data_forms` WHERE `type` IN ('email') AND `active` = '1' ORDER BY `order` ASC LIMIT 1",ARRAY_A);
  $email_address = $wpdb->get_results("SELECT * FROM `wp_submited_form_data` WHERE `log_id`=".$check[0]['id']." AND `form_id` = '".$email_form_field[0]['id']."' LIMIT 1",ARRAY_A);
  $email = $email_address[0]['value'];
  }
} //if($sessionid != null)
    else
    {
        echo '<div class="wrap">';
        echo '<h3>Произошла ошибка. Приносим извинения. Сообщите нам по адресу bankir@cartoonbank.ru</h3>';
        echo '</div>';
        return;
    }  

    $siteurl = get_option('siteurl');
      
    $previous_download_ids = Array(0);  

    $message_html .= "<br /><br />";
    $message_html .= "Общая стоимость с учётом скидки: ".$_total." руб.\n\r";
    $message_html .= "\n\r";

    //pokazh($cart,"cart");
    //pokazh($message,"message");
    //exit;
  
if(isset($cart) && $cart != null && $cart_content!='' && ($errorcode == 0))
  {
  $headers = "From: ".get_option('return_email')."\r\n" .
               'X-Mailer: PHP/' . phpversion() . "\r\n" .
               "MIME-Version: 1.0\r\n" .
               "Content-Type: text/html; charset=utf-8\r\n" .
               "Content-Transfer-Encoding: 8bit\r\n\r\n";

  
  $purch_sql = "SELECT * FROM `wp_purchase_logs` WHERE `id`!='".$check[0]['id']."'";
  $purch_data = $wpdb->get_results($purch_sql,ARRAY_A) ; 

  $report_user = "О заказчике. "; 
  
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

      if($email != '')
        {
            $mess = $report." <br /><br /> ".$message;
            //mail($email, 'Подтверждение покупки изображения. Cartoonbank.ru .', $message, $headers);
            //mail("igor.aleshin@gmail.com", 'Подтверждение покупки изображения. Копия.', $mess, $headers);

            // Send licenses as attchment
            send_email_multi_attachments($email, $result_no_license_text);
            send_email_multi_attachments("igor.aleshin@gmail.com", $result_no_license_text);

            send_email_multi_attachments("vfshilov@gmail.com", $result_no_license_text);

            $result_no_license_text = null;

            //mail("sales@cartoonbank.com", 'Подтверждение покупки изображения. Копия.', $mess, $headers);
        }
    // todo: 
    $_SESSION['nzshpcrt_cart'] = '';
    $_SESSION['nzshpcrt_cart'] = Array();
    $_SESSION['total'] = 0;

    echo '<div class="wrap">';
    if($sessionid != null)
    {
        echo "<h3>Транзакция прошла успешно</h3>";
        echo "<br />" . nl2br(str_replace("$",'\$',$message_html));
    }
    echo '</div>';

  } // end of: if(isset($cart) && $cart != null && ($errorcode == 0))
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
    //todo: remove to update 
    $wpdb->query($sql) ;
   }


function send_email_multi_attachments($email, $content='')
{
    global $license_names_array;
    $files = $license_names_array;

    $abspath = ROOTDIR.'licenses/'; 
    $abspath_1 = ROOTDIR."licenses/";
    $abspath_2 = "Z:/home/localhost/www/licenses/";

    if (strstr($_SERVER['SCRIPT_FILENAME'],'Z:/home'))
        {$abspath = $abspath_2;}
    else if (strstr($_SERVER['SCRIPT_FILENAME'],'cb/')) 
        {$abspath = $abspath_1;}


    $path = $abspath; //"/home/www/cb3/licenses/";

    // email fields: to, from, subject, and so on
    $to = $email;
    $to_2 = "igor.aleshin@gmail.com";
    $from = "bankir@cartoonbank.ru"; 
    $subject ="Подтверждение покупки изображения на сайте Картунбанк.ру"; 
    $message = $content;
    $headers = "From: $from";

    // boundary 
    $semi_rand = md5(time()); 
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
     
    // headers for attachment 
    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
     
    // multipart boundary 
    $message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
    $message .= "--{$mime_boundary}\n";
     
    // preparing attachments
    clearstatcache();
    for($x=0;$x<count($files);$x++){
        $file = fopen($path.$files[$x],"rb");
        $data = fread($file,filesize($path.$files[$x]));
        fclose($file);
        $data = chunk_split(base64_encode($data));
        $message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$files[$x]\"\n" . 
        "Content-Disposition: attachment;\n" . " filename=\"$files[$x]\"\n" . 
        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        $message .= "--{$mime_boundary}\n";
    }
     
    // send
    $ok = @mail($to, $subject, $message, $headers); 
}
     

//http://localhost/?page_id=31&sessionid=8061284226824
//sql:UPDATE `wp_purchase_logs` SET `statusno` = '',`transactid` = '',`authcode` = '',`date` = '1284228427' WHERE `sessionid` = 1171284228397 LIMIT 1
//echo("<pre>message:".print_r($message,true)."</pre>");
?>