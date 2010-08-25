<?php
global $wpdb;
/*
if($_POST['country'] != null)
  {
  $_SESSION['selected_country'] = $_POST['country'];
  }
  else if($_SESSION['selected_country'] == '')
    {
    $_SESSION['selected_country'] = get_option('base_country');
    }
*/    
if(get_option('permalink_structure') != '')
  {
  $seperator ="?";
  }
  else
    {
    $seperator ="&amp;";
    }
 
$rawnum = null;
$number = null;  
$cart = null;
if (isset($_SESSION['nzshpcrt_cart']))
{
	$cart = $_SESSION['nzshpcrt_cart'];
}

function country_list($selected_country = null)
  {
  global $wpdb;
  //$output = "".get_option('base_country')."";
  $country_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."currency_list` WHERE `isocode` IN ('".get_option('base_country')."') LIMIT 1",ARRAY_A);
  $country = $country_data[0];
  if($selected_country == $country['isocode'])
    {
    $output .= "<input name='country' id='current_country' type='radio' value='".$country['isocode']."' checked='true'  onclick='submit_change_country();'/><label for='current_country'>".$country['country']."</label><br />";
    $output .= "<input name='country' id='other_country' type='radio' value='0' onclick='submit_change_country();' /><label for='other_country'>Other</label>";  
    }
    else
      {
      $output .= "<input name='country' id='current_country' type='radio' value='".$country['isocode']."' onclick='submit_change_country();' /><label for='current_country'>".$country['country']."</label><br />";
      $output .= "<input name='country'  id='other_country' type='radio' value='0' checked='true' onclick='submit_change_country();' /><label for='other_country'>Other</label>";  
      }
    
  return $output;
  }
?>
<div class="wrap">
  <?php
  if(isset($_SESSION['nzshpcrt_cart']) && $_SESSION['nzshpcrt_cart'] != null)
    {
  ?>
  <span>
  <?php echo TXT_WPSC_CONFIRM_TOTALS; ?></span>
  <hr class='productcart' />
  <table class='productcart' padding='2'>
  <?php
    
  echo "<tr class='firstrow'>\n\r";
  // заголовок таблицы
  echo "  <td style='width:144px'>".TXT_WPSC_DOWNLOADABLEPRODUCT."</td>\n\r"; 
  echo "  <td>Описание</td>\n\r";
  echo "  <td>". TXT_WPSC_PRICE.":</td>\n\r";
  echo "  <td>".TXT_WPSC_REMOVE."</td>\n\r";  
  echo "</tr>\n\r";
  $num = 1;
  $total = 0;
  $total_shipping = 0;
  foreach($cart as $key => $cart_item)
    {
    $product_id = $cart_item->product_id;
    $quantity = $cart_item->quantity;
    $number =& $quantity;
    $product_variations = $cart_item->product_variations;
    $variation_count = count($product_variations);
    //exit("<pre>".print_r($product_variations,true)."</pre>");
    if($variation_count >= 1)
      {
      $variation_list = "&nbsp;(";
      $i = 0;
      foreach($product_variations as $value_id)
        {
        if($i > 0)
          {
          $variation_list .= ",&nbsp;";
          }
        $value_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `id`='".$value_id."' LIMIT 1",ARRAY_A);
        $variation_list .= str_replace(" ", "&nbsp;",$value_data[0]['name']);    
        //echo("<pre>".print_r($variation,true)."</pre>");          
        $i++;
        }
      $variation_list .= ")";
      }
      else
        {
        $variation_list = '';
        }
    $sql = "SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id`='$product_id' LIMIT 1";
    $product_list = $wpdb->get_results($sql,ARRAY_A) ;
    echo "<tr class='product_row'>\n\r";
    
    echo "  <td style='width:144px;'>\n\r";
    //$imagepath = $imagedir . $imagedata[0]['image'];
    $basepath = get_option('siteurl');
    $imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/images/";
    $previewdir = $basepath."/wp-content/plugins/wp-shopping-cart/product_images/";
    echo ("<a href='".$previewdir.$product_list[0]['image']."'><img border='0' src='".$imagedir.$product_list[0]['image']."'></a>");
    echo "  </td>\n\r";
    echo "  <td>\n\r";
    echo "№&nbsp;".$product_list[0]['id']."<br>Автор ".$product_list[0]['brand'].'.<br>Название ' .$product_list[0]['name'] ."<br><span id='size'>"."<br> Описание: ".$product_list[0]['description'].$variation_list;
    echo "  </td>\n\r";
	echo "<td>".$product_list[0]['price']." руб.</td>";
	//echo("<pre>".print_r($product_list,true)."</pre>");  
/*
(
            +[id] => 2958
            +[name] => У психиатра
            [description] => Больной и врач видят друг друга превратно
            [additional_description] => Больной, врач, доктор, психиатр, сумасшедший, страх, чудовище, представление, воображение
            $product_list[0][price] => 200.00
            [pnp] => 
            [international_pnp] => 
            [file] => 2969
            [image] => 4c6715a23be1f6.29947505cartoon0016.jpg
            [category] => 0
            [brand] => 6
            [quantity_limited] => 0
            [quantity] => 0
            [special] => 0
            [special_price] => 
            [display_frontpage] => 0
            [notax] => 0
            [active] => 1
            [color] => 1
            [visible] => 1
        )

*/

//    echo "  <td>\n\r";
//    echo  "<form class='adjustform' method='POST' action='".get_option('shopping_cart_url')."'><input type='text' value='".$number."' size='2' name='quantity' /><input type='hidden' value='".$key."' name='key' />&nbsp; <input type='submit' name='submit' value='".TXT_WPSC_APPLY."' /></form>";
//    echo "  </td>\n\r";
    
//    echo "  <td>\n\r";
//    if($product_list[0]['special']==1)
//      {
//      $price_modifier = $product_list[0]['special_price'];
//      }
//      else
//        {
        $price_modifier = 0;
//        }
//        
//    echo nzshpcrt_currency_display(($number * ($product_list[0]['price']-$price_modifier)), $product_list[0]['notax']);
//    
    if($product_list[0]['notax'] == 1)
      {
      $total += $number * ($product_list[0]['price']-$price_modifier);
      }
      else
        {
        $total += $number * ($product_list[0]['price']-$price_modifier) * get_option('gst_rate');
        }
//    
//    echo "  </td>\n\r";
//    $shipping = nzshpcrt_determine_item_shipping($product_id, $number, $_SESSION['selected_country']);
//    $total_shipping += $shipping;
    echo "  <td>\n\r";
    echo "<a href='".get_option('shopping_cart_url').$seperator."remove=".$key."'>Убрать</a>";
    echo "  </td>\n\r";
    
    echo "</tr>\n\r";
    }
    
  $siteurl = get_option('siteurl');
  
  $total_shipping = nzshpcrt_determine_base_shipping($total_shipping, $_SESSION['selected_country']);
  $total += $total_shipping;
  if(get_option('base_country') != null)
    {
    echo "<tr class='product_shipping'>\n\r";
    echo "  <td colspan='2'>\n\r";
    ?>
    <div class='select_country'>
      <?php echo TXT_WPSC_SHIPPING_COUNTRY; ?>
      <form name='change_country' action='' method='POST'>
      <?php
      echo country_list($_SESSION['selected_country']);
      ?>
      </form>
    </div>
    <?php
    echo "  </td>\n\r";
    echo "  <td colspan='2' style='vertical-align: middle;'>\n\r";
    echo "" . nzshpcrt_currency_display($total_shipping, 1) . "";
    echo "  </td>\n\r";
    echo "</tr>\n\r";
    }
    
//  //echo "<tr style='total-price'>\n\r";
//  echo "<tr class='total_price'>\n\r";
//  echo "  <td colspan='2'>\n\r";
//  //echo "".TXT_WPSC_TOTALPRICE.":";
//  echo "  </td>\n\r";
//  echo "  <td colspan='2' style='vertical-align: middle;'>\n\r";
//  echo "" . nzshpcrt_currency_display($total, 1) . "";
//  echo "  </td>\n\r";
//  echo "</tr>\n\r";
    
  echo "</table>";
  
  echo "
  <ul class='checkout_links'>";
global $user_identity;
if ($user_identity == '')
{
    echo ("<h2>Продолжить оплату и скачать изображение большого размера можно только после <a href='wp-register.php'>регистрации</a>.</h2>");
}
else
{
  echo "
    <li>
      &gt;
      <a href='".get_option('checkout_url').$seperator."total=$total'>".TXT_WPSC_MAKEPAYMENT."</a>
    </li>";
}

  echo "
    <li>
      &gt;
      <a href='".get_option('product_list_url')."'>".TXT_WPSC_CONTINUESHOPPING."</a>
    </li>
    <li>
      &gt;
      <a href='".get_option('shopping_cart_url').$seperator."cart=empty'>".TXT_WPSC_EMPTYSHOPPINGCART."</a>
    </li>
  </ul>\n\r";
  
  $_SESSION['nzshpcrt_totalprice'] = $total; 
  
    }
    else
      {
      echo TXT_WPSC_NOITEMSINTHESHOPPINGCART;
      }
  ?>
</div>