<?php
class nzshpcrt_variations
  {
  function nzshpcrt_variations()
    {
     global $wpdb;
    }
    
  function display_variation_values($prefix,$variation_id)
    {
     global $wpdb;
    if(is_numeric($variation_id))
      {
      $variation_values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `variation_id` = '$variation_id' ORDER BY `id` ASC",ARRAY_A);
      if($variation_values != null)
        {
        $output .= "<input type='hidden' name='' value='".$variation_id."'>";
        $output .= "<table>";
        //<th>Stock</th><th>Price</th>
        $output .= "<tr><th>".TXT_WPSC_VISIBLE."</th><th>".TXT_WPSC_NAME."</th></tr>";
        foreach($variation_values as $variation_value)
          {
          $output .= "<tr>";
          $output .= "<td style='text-align: center;'><input type='checkbox' name='variation_values[".$variation_id."][".$variation_value['id']."][active]' value='1' checked='true' id='variation_active_".$variation_value['id']."' />";
          $output .= "<input type='hidden' name='variation_values[".$variation_id."][".$variation_value['id']."][blank]' value='null' />  </td>";
          $output .= "<td>".$variation_value['name']."</td>";
          //           //         $output .= "<td><input type='text' name='variation_values[".$variation_id."][".$variation_value['id']."][stock]' size='3' value='' /></td>";
          //           //         $output .= "<td><input type='text' name='variation_values[".$variation_id."][".$variation_value['id']."][price]' size='3' value='' /></td>";
          //           // onblur='tick_active(\\\"variation_active_".$variation_value['id']."\\\",this.value)
          $output .= "</tr>";
          }
      
      
        $output .= "<tr>";
        $output .= "<td colspan='4'>";
        $output .= "<a href='#' onclick='return remove_variation_value_list(\\\"$prefix\\\",\\\"$variation_id\\\");'>".TXT_WPSC_REMOVE_SET."</a>";
        $output .= "</td>";
        $output .= "</tr>";
        $output .= "</table>";
        }
      }
    return $output;
    }
    
    
  function add_to_existing_product($product_id,$variation_list)
    {
     global $wpdb;
    if(is_numeric($product_id))
      { 
      foreach($_POST['variation_values'] as $variation_id => $variation_values)
        {
        if(is_numeric($variation_id))
          { 
          $num = 0;
          $variation_assoc_sql = "INSERT INTO `".$wpdb->prefix."variation_associations` ( `id` , `type` , `name` , `associated_id` , `variation_id` ) VALUES ( '', 'product', '', '$product_id', '$variation_id');";
          
          $product_assoc_sql = "INSERT INTO `".$wpdb->prefix."variation_values_associations` ( `id` , `product_id` , `value_id` , `quantity` , `price` , `visible` , `variation_id` ) VALUES";
          foreach($variation_values as $variation_value_id => $variation_value_properties)
            {
            if(is_numeric($variation_value_id))        
              {
              switch($num)
                {
                case 0:
                $comma = '';
                break;
                
                default:
                $comma = ', ';
                break;
                }
                
              if(is_numeric($variation_value_properties['price']) && ($variation_value_properties['price'] > 0))
                {
                $price = $variation_value_properties['price'];
                }
                else
                  {
                  $price = '';
                  }
                
              if($variation_value_properties['active'] == 1)
                {
                $active = 1;
                }
                else
                  {
                  $active = 0;
                  }
              
              $product_assoc_sql .= "$comma ('', '$product_id', '$variation_value_id', '".$variation_value_properties['stock']."', '".$price."', '$active', '$variation_id')";
              $num++;
              }
            }
          $product_assoc_sql .= ";";
          $wpdb->query($product_assoc_sql);
          $wpdb->query($variation_assoc_sql);
          }
        }
      }
    return $output;
    }
    
  function display_attached_variations($product_id)
    {
     global $wpdb;
    $associated_variations = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_associations` WHERE `type` IN ('product') AND `associated_id` = '$product_id' ORDER BY `id` ASC",ARRAY_A);
    foreach($associated_variations as $associated_variation)
      {
      $associated_variation_values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values_associations` WHERE `variation_id` = '".$associated_variation['variation_id']."' AND `product_id` = '$product_id' ORDER BY `id` ASC",ARRAY_A);
      
      $variation_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_variations` WHERE `id` = '".$associated_variation['variation_id']."' ORDER BY `id` ASC LIMIT 1",ARRAY_A);
      //exit("SELECT * FROM `".$wpdb->prefix."variation_values_associations` WHERE `variation_id` = '".$associated_variation['variation_id']."' AND `product_id` = '$product_id' ORDER BY `id` ASC");
      $variation_data = $variation_data[0];
      
      $output .= "<table>";
      $output .= "<tr><th colspan='4'>".$variation_data['name']."</th></tr>";
       $output .= "<tr><th>".TXT_WPSC_VISIBLE."</th><th>".TXT_WPSC_NAME."</th></tr>";
      $num = 0;
      $not_included_in_statement = '';
      foreach($associated_variation_values as $associated_variation_value)
        {
        $product_value_id = $associated_variation_value['id'];
        $value_id = $associated_variation_value['value_id'];
        $value_stock = $associated_variation_value['quantity'];
        $value_price = $associated_variation_value['price'];
        $value_active = "";
        if($associated_variation_value['visible'] == 1)
          {
          $value_active = "checked='true'";
          }
        $value_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `id` = '$value_id' ORDER BY `id` ASC",ARRAY_A);
        $value_data = $value_data[0];
        $output .= "<tr>";
        
        $output .= "<td style='text-align: center;'><input type='checkbox' name='edit_variation_values[".$product_value_id."][active]' value='1' id='variation_active_".$value_id."' $value_active>
        <input type='hidden' name='edit_variation_values[".$product_value_id."][blank]' value='null'>
        </td>"; 
        $output .= "<td>".$value_data['name']."</td>";
        $output .= "</tr>";
        switch($num)
          {
          case 0:
          $comma = '';
          break;
          
          default:
          $comma = ', ';
          break;
          }
        $not_included_in_statement .= "$comma'$value_id'";
        $num++;
        }
      $not_included_sql = "SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `variation_id` IN ('".$associated_variation['variation_id']."') AND `id` NOT IN ($not_included_in_statement)";
      $values_not_included = $wpdb->get_results($not_included_sql,ARRAY_A);
      //$output .= "<pre>".print_r($not_included_sql,true)."</pre>";  
      $variation_id = $associated_variation['variation_id'];
      if($values_not_included != null)
        {
        foreach($values_not_included as $variation_value)
          {
          $output .= "<tr>";
          $output .= "<td style='text-align: center;'><input type='checkbox' name='edit_add_variation_values[".$variation_id."][".$variation_value['id']."][active]' value='1' id='variation_active_".$variation_value['id']."'>
          <input type='hidden' name='edit_add_variation_values[".$variation_id."][".$variation_value['id']."][blank]' value='null' />
          </td>"; 
          $output .= "<td>".$variation_value['name']."</td>";
//           $output .= "<td><input type='text' name='edit_add_variation_values[".$variation_id."][".$variation_value['id']."][stock]' size='3' value='' /></td>";
//           $output .= "<td><input type='text' name='edit_add_variation_values[".$variation_id."][".$variation_value['id']."][price]' size='3' value='' /></td>";
          $output .= "</tr>";
          }
        }
      
      $output .= "<tr>";
      $output .= "<td colspan='4'>";
      $output .= "<a href='admin.php?page=wp-shopping-cart/display-items.php&amp;submit_action=remove_set&amp;product_id=".$associated_variation_value['product_id']."&amp;variation_assoc_id=".$associated_variation['id']."'>".TXT_WPSC_REMOVE_SET."</a>";
      $output .= "</td>";
      $output .= "</tr>";
      $output .= "</table>";
      $num++;
      }
      
    
    //$output .= "<pre>".print_r($values_not_included,true)."</pre>";  
    return $output;
    }
    
  function edit_product_values($product_id,$variation_value_list)
    {
     global $wpdb;
    foreach($variation_value_list as $variation_value_id => $variation_values)
      {
      $quantity = $variation_values['stock'];
      if(is_numeric($variation_values['price']) && ($variation_values['price'] > 0))
        {
        $price = $variation_values['price'];
        }
        else
          {
          $price = '';
          }
        
      if($variation_values['active'] == 1)
        {
        $visible_state = 1;
        }
        else
          {
          $visible_state = 0;
          }
      $update_sql = "UPDATE `".$wpdb->prefix."variation_values_associations` SET `visible` = '".$visible_state."' WHERE `id` = '".$variation_value_id."' LIMIT 1 ;";
      $wpdb->query($update_sql);
      //echo "<pre>".print_r($update_sql,true)."</pre>";
      //echo "<pre>".print_r($variation_values,true)."</pre>"; 
      }
    return $output;
    }
    
   function edit_add_product_values($product_id,$variation_value_list)
    {
     global $wpdb;
    foreach($variation_value_list as $variation_id => $variation_values)
      {
      if(is_numeric($variation_id))
        { 
        foreach($variation_values as $variation_value_id => $variation_value_properties)
          {
          $quantity = $variation_value_properties['stock'];
          if(is_numeric($variation_value_properties['price']) && ($variation_value_properties['price'] > 0))
            {
            $price = $variation_value_properties['price'];
            }
            else
              {
              $price = '';
              }
            
          if($variation_value_properties['active'] == 1)
            {
            $visible_state = 1;
            }
            else
              {
              $visible_state = 0;
              }
          $product_assoc_sql = "INSERT INTO `".$wpdb->prefix."variation_values_associations` ( `id` , `product_id` , `value_id` , `quantity` , `price` , `visible` , `variation_id` ) VALUES ('', '$product_id', '$variation_value_id', '".$quantity."', '".$price."', '$visible_state', '$variation_id')";
          $wpdb->query($product_assoc_sql);
         // echo "<pre>".print_r($product_assoc_sql,true)."</pre>";
          //echo "<pre>".print_r($variation_values,true)."</pre>"; 
          }
        }
      }
    return $output;
    }
  
  function display_product_variations($product_id,$no_label = false, $no_br = false )
    {
     global $wpdb;
    $variation_assoc_sql = "SELECT * FROM `".$wpdb->prefix."variation_associations` WHERE `type` IN ('product') AND `associated_id` IN ('$product_id')";
    $variation_assoc_data = $wpdb->get_results($variation_assoc_sql,ARRAY_A);
    if($variation_assoc_data != null)
      {
      foreach($variation_assoc_data as $variation_association)
        {
        $variation_id = $variation_association['variation_id'];
        $value_assoc_sql = "SELECT * FROM `".$wpdb->prefix."variation_values_associations` WHERE `product_id` IN ('$product_id') AND `variation_id` IN ('$variation_id') AND `visible` IN ('1')";
        $value_assoc_data = $wpdb->get_results($value_assoc_sql,ARRAY_A);
        
        
        $variation_data_sql = "SELECT * FROM `".$wpdb->prefix."product_variations`  WHERE `id` IN ('$variation_id') LIMIT 1";
        $variation_data = $wpdb->get_results($variation_data_sql,ARRAY_A);
        $variation_data = $variation_data[0];
        if($no_label !== true)
          {
          $output .= $variation_data['name'] . ": ";
          }
        $output .= "<select name='variation[".$variation_data['id']."]'>";
        foreach($value_assoc_data as $value_association)
          {
          $value_id = $value_association['value_id'];
          $value_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `id` = '$value_id' ORDER BY `id` ASC",ARRAY_A);
          $value_data = $value_data[0];
          $output .= "<option value='".$value_data['id']."'>".$value_data['name']."</option>";
          }
        $output .= "</select>";
        if($no_br !== true)
          {
          $output .= "<br />";
          }
        //$output .= "<pre>".print_r($value_assoc_data,true)."</pre>";
        }
      }
    //$output .= "<pre>".print_r($variation_assoc_data,true)."</pre>";
    return $output; 
    }  
  }
?>