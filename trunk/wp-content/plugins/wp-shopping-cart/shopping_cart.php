<?php
global $wpdb;

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
  Проверьте ваш заказ. Скачивая изображения, вы соглашаетесь с условиями, описанными в соответствующей лицензии. Переходите к следующей странице со списком возможных способов оплаты.</span>
  <hr class='productcart' />
  <table id='productcart' class='productcart'>
  <?php
   
  $num = 1;
  $total = 0;
  $total_shipping = 0;
  

  require('al_cart_function.php');

  //cart_product_list();
  $license = true;
  echo cart_product_list_string($license);
  
  $siteurl = get_option('siteurl');
  
  $total_shipping = nzshpcrt_determine_base_shipping($total_shipping, "");
  $total += $total_shipping;   

  echo "<tr><td colspan='2'>&nbsp;</td><td colspan='2'>";
?>

<tr><td colspan='2'>
<input type='checkbox' value='yes' name='agree' onclick='if (this.checked==true){document.getElementById("paybutton").style.display="block";}else{document.getElementById("paybutton").style.display="none";};' /> Я соглашаюсь с лицензионными условиями.<br /><br />
</td><td colspan='2'>	   
   
<?

global $user_identity;
if ($user_identity == '')
{
    echo ("<div><h2>Продолжить оплату и скачать изображение большого размера можно только после <a href='wp-register.php'>регистрации</a>.</h2>");
}
else
{
  echo "<div align='right' width='100%'><div id='paybutton' style='display:none;clear:both;width:100%;float:right;margin-bottom:4px;'><a href='".get_option('checkout_url')."' class='button' style='background-color:#CCFF00;'>Подтвердить выбор и перейти к оплате ></a></div><br />";

}
  echo "<div style='clear:both;width:100%;float:right;margin-bottom:4px;'><a href='".get_option('product_list_url')."' class='button'>< Продолжить выбор</a></div>";
  echo "<div style='clear:both;width:100%;float:right;margin-bottom:4px;'><a href='".get_option('shopping_cart_url').$seperator."cart=empty' class='button'>x Очистить корзину</a></div></div>";
  

  echo "</td></tr>";
echo "</table>";

  $_SESSION['total'] = $total; 
  
    }
    else
      {
      echo TXT_WPSC_NOITEMSINTHESHOPPINGCART;
      }
  ?>
</div>
<?
//echo("<pre>SESSION:".print_r($_SESSION,true)."</pre>");
?>