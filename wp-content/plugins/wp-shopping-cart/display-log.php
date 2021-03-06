<?php
/*
 * this updates the processing status of an item
 */
if(isset($_GET['id']) && is_numeric($_GET['id']) && is_numeric($_GET['value']))
  {
  $stage_count_sql = "SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."purchase_statuses` WHERE `active`='1'";
  $stage_count_data = $wpdb->get_results($stage_count_sql,ARRAY_A);
  $stage_count = $stage_count_data[0]['count'];
  if(is_numeric($_GET['value']))
    {
    $newvalue = $_GET['value'];
    }
    else
      {
      $newvalue = 1;
      }
  $update_sql = "UPDATE `".$wpdb->prefix."purchase_logs` SET `processed` = '".$newvalue."' WHERE `id` = '".$_GET['id']."' LIMIT 1";
  $wpdb->query($update_sql);
  }

/*
 * this finds the earliest time in the shopping cart and sorts out the timestamp system for the month by month display
 */  
$sql = "SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."purchase_logs` WHERE `date`!='' ORDER BY `date` DESC";
$purchase_count= $wpdb->get_results($sql,ARRAY_A) ;

$earliest_record_sql = "SELECT MIN(`date`) AS `date` FROM `".$wpdb->prefix."purchase_logs` WHERE `date`!=''";
$earliest_record = $wpdb->get_results($earliest_record_sql,ARRAY_A) ;

$current_timestamp = time();
$earliest_timestamp = $earliest_record[0]['date'];

$current_year = date("Y");
$earliest_year = date("Y",$earliest_timestamp);


?>
<div class="wrap" style=''>
  <h2>Лог заказов</h2><br />
  <table style='width: 100%;'>
   <tr>  
    <td id='product_log_data'>
   <?php
  if(isset($purchase_log) && ($purchase_log == null) && isset($_GET['purchaseid']) && !is_numeric($_GET['purchaseid']))
    {
    if($earliest_record[0]['date'] != null)
      {
      $form_sql = "SELECT * FROM `".$wpdb->prefix."collect_data_forms` WHERE `active` = '1' AND `display_log` = '1';";
      $form_data = $wpdb->get_results($form_sql,ARRAY_A);
      
      $col_count = 5 + count($form_data);
      
      $i = 0;
      echo "<table class='logdisplay'>";    
    
      for($year = $current_year; $year >= $earliest_year; $year--)
        {
        for($month = 12; $month >= 1; $month--)
          {
          $start_timestamp = mktime(0, 0, 0, $month, 1, $year);
          $end_timestamp = mktime(0, 0, 0, ($month+1), 0, $year);
          if(($end_timestamp >= $earliest_timestamp) && ($start_timestamp <= $current_timestamp))
            {   
            $sql = "SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `date` BETWEEN '$start_timestamp' AND '$end_timestamp' ORDER BY `date` DESC";
            $purchase_log = $wpdb->get_results($sql,ARRAY_A) ;
            $i = 0;
            $subtotal = 0;
            echo "<tr>";
            echo " <td colspan='$col_count'>";
            echo "<h3 class='log_headers'>".date("M Y", $start_timestamp) ."</h3>";
            echo " </td>";      
            echo "</tr>";
            if($purchase_log != null)
              {
              echo "<tr class='toprow'>";
              
              echo " <td style='text-align: left;'>";
              echo TXT_WPSC_STATUS;
              echo " </td>";
                
              echo " <td>";
              echo TXT_WPSC_DATE;
              echo " </td>";
              
              foreach($form_data as $form_field)
                {
                echo " <td>";
                echo $form_field['name'];
                echo " </td>";
                }
                
              echo " <td>";
              echo TXT_WPSC_PRICE;
              echo " </td>";  
              
              if(get_option('payment_method') == 2)
                {
                echo " <td>";
                echo TXT_WPSC_PAYMENT_METHOD;
                echo " </td>";  
                }
              
              echo " <td>";
              echo TXT_WPSC_VIEWDETAILS;
              echo " </td>";
            
              echo "</tr>";
          
              foreach($purchase_log as $purchase)
                {
                $status_state = "expand";
                $status_style = "";
                $alternate = "";
                  $i++;
                  if(($i % 2) != 0)
                    {
                    $alternate = "class='alt'";
                    }
                echo "<tr $alternate>\n\r";
                
                echo " <td class='processed'>";
                if($purchase['processed'] < 1)
                  {
                  $purchase['processed'] = 1;
                  }
                $stage_sql = "SELECT * FROM `".$wpdb->prefix."purchase_statuses` WHERE `id`='".$purchase['processed']."' AND `active`='1' LIMIT 1";
                $stage_data = $wpdb->get_results($stage_sql,ARRAY_A);
                //
                echo "<a href='#' onclick='return show_status_box(\"status_box_".$purchase['id']."\",\"log_expander_icon_".$purchase['id']."\");'>";
                if($_GET['id'] == $purchase['id'])
                  {
                  $status_state = "collapse";
                  $status_style = "style='display: block;'";
                  }
                echo "<img class='log_expander_icon' id='log_expander_icon_".$purchase['id']."' src='http://th.cartoonbank.ru/icon_window_$status_state.gif' alt='' title='' />";
                if($stage_data[0]['colour'] != '')
                  {
                  $colour = "style='color: #".$stage_data[0]['colour'].";'";
                  }
                echo "<span $colour>".$stage_data[0]['name']."</span>";
                echo "</a>";
                echo " </td>\n\r";
          
                echo " <td>";
                echo date("jS M Y",$purchase['date']);
                echo " </td>\n\r";
              
                foreach($form_data as $form_field)
                  {
                  $collected_data_sql = "SELECT * FROM `".$wpdb->prefix."submited_form_data` WHERE `log_id` = '".$purchase['id']."' AND `form_id` = '".$form_field['id']."' LIMIT 1";
                  $collected_data = $wpdb->get_results($collected_data_sql,ARRAY_A);
                  $collected_data = $collected_data[0];
                  echo " <td>";
                  echo $collected_data['value'];
                  echo " </td>\n\r";
                  }
          
                echo " <td>";
                            
                if($purchase['shipping_country'] != '')
                  {
                  $country = $purchase['shipping_country'];
                  }
                  else
                    {
                    $country_sql = "SELECT * FROM `".$wpdb->prefix."submited_form_data` WHERE `log_id` = '".$purchase['id']."' AND `form_id` = '".get_option('country_form_field')."' LIMIT 1";
                    $country_data = $wpdb->get_results($country_sql,ARRAY_A);
                    $country = $country_data[0]['value'];
                    }
                //echo $country;
                echo nzshpcrt_currency_display(nzshpcrt_find_total_price($purchase['id'],$country),1);
                $subtotal += nzshpcrt_find_total_price($purchase['id'],$country);
                echo " </td>\n\r";
          
                
                if(get_option('payment_method') == 2)
                  {
                  echo " <td>";
                  $gateway_name = '';
                  foreach($GLOBALS['nzshpcrt_gateways'] as $gateway)
                    {
                    if($purchase['gateway'] != 'testmode')
                      {
                      if($gateway['internalname'] == $purchase['gateway'] )
                        {
                        $gateway_name = $gateway['name'];
                        }
                      }
                      else
                        {
                        $gateway_name = "Manual Payment";
                        }
                    }
                  echo $gateway_name;
                  echo " </td>\n\r";
                  }
                echo " <td>";
                echo "<a href='".$_SERVER['REQUEST_URI']."&purchaseid=".$purchase['id']."'>".TXT_WPSC_VIEWDETAILS."</a>";
                echo " </td>\n\r";
            
                echo "</tr>\n\r";
                
                $stage_list_sql = "SELECT * FROM `".$wpdb->prefix."purchase_statuses` ORDER BY `id` ASC";
                $stage_list_data = $wpdb->get_results($stage_list_sql,ARRAY_A);
                
                echo "<tr>\n\r";
                echo " <td colspan='$col_count'>\n\r";
                echo "  <div id='status_box_".$purchase['id']."' class='order_status' $status_style>\n\r";
                echo "  <div>\n\r";
                echo "  <strong class='form_group'>".TXT_WPSC_ORDER_STATUS."</strong>\n\r";
                echo "  <form id='form_group_".$purchase['id']."' method='GET' action='admin.php?page=wp-shopping-cart/display-log.php'>\n\r";
                echo "  <input type='hidden' name='page' value='".$_GET['page']."' />\n\r";
                echo "  <input type='hidden' name='id' value='".$purchase['id']."' />\n\r";
                echo "  <ul>\n\r";
                foreach($stage_list_data as $stage)
                  {
                  $selected = '';
                  if($stage['id'] == $purchase['processed'])
                    {
                    $selected = "checked='true'";
                    }
                  $button_id = "button_".$purchase['id']."_".$stage['id'];
                  echo "    <li><input type='radio' name='value' $selected value='".$stage['id']."' onclick='submit_status_form(\"form_group_".$purchase['id']."\");' id='".$button_id."'/><label for='$button_id'>".$stage['name']."</label>\n\r";
                  }
                echo "  </ul>\n\r";
                echo "  </form>\n\r";
                echo "  </div>\n\r";
                echo "  </div>\n\r";
                echo " </td>\n\r";
                echo "</tr>\n\r";
                }
                
              echo "<tr>";
              echo " <td colspan='$col_count'>";
              echo "<strong>Total:</strong> ".nzshpcrt_currency_display($subtotal ,1);
              echo "<br /><a class='admin_download' href='index.php?purchase_log_csv=true&rss_key=key&start_timestamp=$start_timestamp&end_timestamp=$end_timestamp' ><img align='absmiddle' src='http://th.cartoonbank.ru/download.gif' alt='' title='' /><span>".TXT_WPSC_DOWNLOAD_CSV."</span></a>";
              echo " </td>";      
              echo "</tr>";
              }
              else
                {
                echo "<tr>";
                echo " <td colspan='$col_count'>";
                echo "No transactions for this month.";
                echo " </td>";      
                echo "</tr>";
                }
            }
          }
        }
      echo " </table>";
      }
      else
        {
        echo " <table>"; 
        echo "<tr>";
        echo " <td>";     
        echo TXT_WPSC_NO_PURCHASES;
        echo " </td>";      
        echo "</tr>";
        echo " </table>";
        }
    }
    else if(isset($_GET['purchaseid']) && is_numeric($_GET['purchaseid']))
      {

      $purch_sql = "SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `id`='".$_GET['purchaseid']."'";
      $purch_data = $wpdb->get_results($purch_sql,ARRAY_A) ;

      $cartsql = "SELECT * FROM `".$wpdb->prefix."cart_contents` WHERE `purchaseid`=".$_GET['purchaseid']."";
      $cart_log = $wpdb->get_results($cartsql,ARRAY_A) ; 
      $j = 0;
      if($cart_log != null)
        {
        echo "<table class='logdisplay'>";
        echo "<tr class='toprow2'>";

        echo " <td>";
        echo TXT_WPSC_NAME;
        echo " </td>";

        echo " <td>";
        echo TXT_WPSC_QUANTITY;
        echo " </td>";
        
        echo " <td>";
        echo TXT_WPSC_PRICE;
        echo " </td>";

        echo " <td>";
        echo TXT_WPSC_GST;
        echo " </td>";

        echo " <td>";
        echo TXT_WPSC_PP;
        echo " </td>";

        echo " <td>";
        echo TXT_WPSC_TOTAL;
        echo " </td>";

        echo "</tr>";
        $endtotal = 0;
        foreach($cart_log as $cart_row)
          {
          $alternate = "";
          $j++;
          if(($j % 2) != 0)
            {
            $alternate = "class='alt'";
            }
          $productsql= "SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id`=".$cart_row['prodid']."";
          $product_data = $wpdb->get_results($productsql,ARRAY_A); 
        
          $variation_sql = "SELECT * FROM `".$wpdb->prefix."cart_item_variations` WHERE `cart_id`='".$cart_row['id']."'";
          $variation_data = $wpdb->get_results($variation_sql,ARRAY_A); 
          $variation_count = count($variation_data);
          if($variation_count > 1)
            {
            $variation_list = " (";
            $i = 0;
            foreach($variation_data as $variation)
              {
              if($i > 0)
                {
                $variation_list .= ", ";
                }
              $value_id = $variation['venue_id'];
              $value_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `id`='".$value_id."' LIMIT 1",ARRAY_A);
              $variation_list .= $value_data[0]['name'];              
              $i++;
              }
            $variation_list .= ")";
            }
            else if($variation_count == 1)
              {
              $value_id = $variation_data[0]['venue_id'];
              $value_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `id`='".$value_id."' LIMIT 1",ARRAY_A);
              $variation_list = " (".$value_data[0]['name'].")";
              }
              else
                {
                $variation_list = '';
                }
          
                           
          if($purch_data[0]['shipping_country'] != '')
            {
            $country = $purch_data[0]['shipping_country'];
            }
            else
              {          
              $country_sql = "SELECT * FROM `".$wpdb->prefix."submited_form_data` WHERE `log_id` = '".$_GET['purchaseid']."' AND `form_id` = '".get_option('country_form_field')."' LIMIT 1";
              $country_data = $wpdb->get_results($country_sql,ARRAY_A);
              $country = $country_data[0]['value'];
              }
          
          $shipping = nzshpcrt_determine_item_shipping($cart_row['prodid'], $cart_row['quantity'], $country);
          $total_shipping += $shipping;
          echo "<tr $alternate>";
      
          echo " <td>";
          echo $product_data[0]['name'];
          echo $variation_list;
          echo " </td>";
      
          echo " <td>";
          echo $cart_row['quantity'];
          echo " </td>";
      
          echo " <td>";
          $price = ($cart_row['price'] * $cart_row['quantity']) / get_option('gst_rate');
          echo nzshpcrt_currency_display($price, 1);
          echo " </td>";
      
          echo " <td>";
          
          if($product_data[0]['notax'] == 1)
            {
            $gst = 0;
            }
            else
              {
              $gst = ($cart_row['price'] * $cart_row['quantity']) - $price;
              }
          
          echo nzshpcrt_currency_display($gst, 1);
          echo " </td>";
      
          echo " <td>";
          echo nzshpcrt_currency_display($shipping, 1);
          echo " </td>";
      
          echo " <td>";
          $endtotal += $price + $gst;
          echo nzshpcrt_currency_display(($shipping + $price + $gst), 1);
          echo " </td>";
                
          echo '</tr>';
          }
         echo "<tr >";
      
          echo " <td>";
          echo " </td>";
      
          echo " <td>";
          echo " </td>";
      
          echo " <td>";
          echo " </td>";
      
          echo " <td>";
          echo "<strong>".TXT_WPSC_TOTALSHIPPING.":</strong><br />";    
          echo "<strong>".TXT_WPSC_FINALTOTAL.":</strong>";
          echo " </td>";
      
          echo " <td>";
          $total_shipping = nzshpcrt_determine_base_shipping($total_shipping, $_SESSION['selected_country']);      
          $endtotal += $total_shipping;    
          echo nzshpcrt_currency_display($total_shipping, 1) . "<br />";
          echo nzshpcrt_currency_display($endtotal,1);
          echo " </td>";
                
          echo '</tr>';
         
        echo "</table>";
        echo "<br />";
        
        

        
        echo "<strong>".TXT_WPSC_CUSTOMERDETAILS."</strong>";
        echo "<table>";
        $form_sql = "SELECT * FROM `".$wpdb->prefix."submited_form_data` WHERE  `log_id` = '".$_GET['purchaseid']."'";
        $input_data = $wpdb->get_results($form_sql,ARRAY_A);
        //exit("<pre>".print_r($input_data,true)."</pre>");
        if($input_data != null)
          {
          foreach($input_data as $form_field)
            {
            $form_sql = "SELECT * FROM `".$wpdb->prefix."collect_data_forms` WHERE `active` = '1' AND `id` = '".$form_field['form_id']."' LIMIT 1";
            $form_data = $wpdb->get_results($form_sql,ARRAY_A);
            if($form_data != null)
              {
              $form_data = $form_data[0];
              if($form_data['type'] == 'country' )
                {
                echo "  <tr><td>".$form_data['name'].":</td><td>".get_country($form_field['value'])."</td></tr>";
                }
                else
                  {
                  echo "  <tr><td>".$form_data['name'].":</td><td>".$form_field['value']."</td></tr>";
                  }
              }
            }
          }
          else
            {
            echo "  <tr><td>".TXT_WPSC_NAME.":</td><td>".$purch_data[0]['firstname']." ".$purch_data[0]['lastname']."</td></tr>";
            echo "  <tr><td>".TXT_WPSC_ADDRESS.":</td><td>".$purch_data[0]['address']."</td></tr>";
            echo "  <tr><td>".TXT_WPSC_PHONE.":</td><td>".$purch_data[0]['phone']."</td></tr>";
            echo "  <tr><td>".TXT_WPSC_EMAIL.":</td><td>".$purch_data[0]['email']."</td></tr>";
            }
       echo "<b>there bug!!!!</b>"; 
        if(get_option('payment_method') == 2)
          {
          $gateway_name = '';
          foreach($GLOBALS['nzshpcrt_gateways'] as $gateway)
            {
            if($purchase['gateway'] != 'testmode')
              {
              if($gateway['internalname'] == $purch_data[0]['gateway'] )
                {
                $gateway_name = $gateway['name'];
                }
              }
              else
                {
                $gateway_name = "Manual Payment";
                }
            }
          echo "  <tr><td>".TXT_WPSC_PAYMENT_METHOD.":</td><td>".$gateway_name."</td></tr>";
          }
        echo "</table>";
        }
        else
          {
          echo "<br />".TXT_WPSC_USERSCARTWASEMPTY;
          }
      echo "<br /><a href='admin.php?page=wp-shopping-cart/display-log.php'>".TXT_WPSC_GOBACK."</a>";
      }
      
$sql = "SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `date`!=''";
$purchase_log = $wpdb->get_results($sql,ARRAY_A) ;
  ?>
   </td>
   
    <td id='order_summary_container'>
    <strong class='order_summary'>Информация о заказах</strong>
    <div class='order_summary'> 
      <div class='order_summary_subsection'>
      <strong>Заработано в этом месяце</strong>
      <p>
      <?php 
      $year = date("Y");
      $month = date("m");
      $start_timestamp = mktime(0, 0, 0, $month, 1, $year);
      $end_timestamp = mktime(0, 0, 0, ($month+1), 0, $year);

       echo nzshpcrt_currency_display(admin_display_total_price($start_timestamp, $end_timestamp),1);
       echo " (Принятые платежи)";
       ?>
      </p>
      </div>
     
      
      <div class='order_summary_subsection'>
      <strong>Суммарный доход</strong>
      <p>
      <?php
       $total_income = $wpdb->get_results($sql,ARRAY_A);
	   //echo("<pre>sql ".print_r($sql,true)."</pre>");

       echo nzshpcrt_currency_display(admin_display_total_price(),1);
       ?>
      </p>
      </div>
    </div>
    </td>  
  </tr>
 </table>

</div>