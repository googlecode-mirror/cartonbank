<?php
/*
Plugin Name:WP Shopping Cart
Plugin URI: http://www.instinct.co.nz
Description: A plugin that provides a WordPress Shopping Cart. Contact <a href='http://www.instinct.co.nz/?p=16#support'>Instinct Entertainment</a> for support.
Version: 3.4.6 beta
Author: Thomas Howard of Instinct Entertainment
Author URI: http://www.instinct.co.nz
*/

### This next line needs to point at your desired language file ###


if(get_option('language_setting') != '')
  {
  include_once(ABSPATH.'wp-content/plugins/wp-shopping-cart/languages/'.get_option('language_setting'));
  }
  else
    {
    include_once(ABSPATH.'wp-content/plugins/wp-shopping-cart/languages/EN_en.php');
    }
    
    
require_once(ABSPATH.'wp-content/plugins/wp-shopping-cart/classes/variations.class.php');
require_once(ABSPATH.'wp-content/plugins/wp-shopping-cart/classes/cart.class.php');

/*
 * Handles "bad" session setups that cause the session to be initialised before the cart.class.php file is included
 * The following piece of code uses the serialized cart variable to reconstruct the cart
 * if session is initialised before cart.class.php is called, then the object name of each cart item will be __PHP_Incomplete_Class
 */
$use_serialized_cart = false;
if(isset($_SESSION['nzshpcrt_cart']) and $_SESSION['nzshpcrt_cart'] != null)
  {
  foreach($_SESSION['nzshpcrt_cart'] as $key => $item)
    {
    if(get_class($item) == "__PHP_Incomplete_Class")    
      {
      $use_serialized_cart = true;
      }
    }
  }
  else
    {
    if(isset($_SESSION['nzshpcrt_cart']) and $_SESSION['nzshpcrt_serialized_cart'] != null)
      {
      $use_serialized_cart = true;
      }
    }
if($use_serialized_cart === true)
  {
  $_SESSION['nzshpcrt_cart'] = unserialize($_SESSION['nzshpcrt_serialized_cart']);
  }

$GLOBALS['nzshpcrt_imagesize_info'] = TXT_WPSC_IMAGESIZEINFO;
$nzshpcrt_log_states[0]['name'] = TXT_WPSC_RECEIVED;
$nzshpcrt_log_states[1]['name'] = 'some name'; //TXT_WPSC_PROCESSING;
$nzshpcrt_log_states[2]['name'] = TXT_WPSC_PROCESSED;
 
class wp_shopping_cart
  {
  function wp_shopping_cart()
    {
    return;
    }
    
  function displaypages()
    {
    /*
     * Fairly standard wordpress plugin API stuff for adding the admin pages, rearrange the order to rearrange the pages
     * The bits to display the options page first on first use may be buggy, but tend not to stick around long enough to be identified and fixed
     * if you find bugs, feel free to fix them.
     *
     * If the permissions are changed here, they will likewise need to be changed for the other secions of the admin that either use ajax
     * or bypass the normal download system.
     */
    if(function_exists('add_options_page'))
      {
      if(get_option('nzshpcrt_first_load') == 0)
        {
        $base_page = 'wp-shopping-cart/options.php';
        add_menu_page(TXT_WPSC_ECOMMERCE, TXT_WPSC_ECOMMERCE, 7, $base_page);
        add_submenu_page($base_page,TXT_WPSC_OPTIONS, TXT_WPSC_OPTIONS, 7, 'wp-shopping-cart/options.php');
        }
        else
          {
          $base_page = 'wp-shopping-cart/display-log.php';
          add_menu_page(TXT_WPSC_ECOMMERCE, TXT_WPSC_ECOMMERCE, 7, $base_page);
          add_submenu_page('wp-shopping-cart/display-log.php',TXT_WPSC_PURCHASELOG, TXT_WPSC_PURCHASELOG, 7, 'wp-shopping-cart/display-log.php');
          }
      
      
      add_submenu_page($base_page,TXT_WPSC_PRODUCTS, TXT_WPSC_PRODUCTS, 7, 'wp-shopping-cart/display-items.php');
      add_submenu_page($base_page,TXT_WPSC_CATEGORIES, TXT_WPSC_CATEGORIES, 7, 'wp-shopping-cart/display-category.php');
      add_submenu_page($base_page,TXT_WPSC_BRANDS, TXT_WPSC_BRANDS, 7, 'wp-shopping-cart/display-brands.php');
      
      add_submenu_page($base_page,TXT_WPSC_VARIATIONS, TXT_WPSC_VARIATIONS, 7, 'wp-shopping-cart/display_variations.php');
      add_submenu_page($base_page,TXT_WPSC_PAYMENTGATEWAYOPTIONS, TXT_WPSC_PAYMENTGATEWAYOPTIONS, 7, 'wp-shopping-cart/gatewayoptions.php');
      if(get_option('nzshpcrt_first_load') != 0)
        {
        add_submenu_page($base_page,TXT_WPSC_OPTIONS, TXT_WPSC_OPTIONS, 7, 'wp-shopping-cart/options.php');
        }
      if(function_exists('ext_shpcrt_options'))
        {
        ext_shpcrt_options($base_page);
        }
      add_submenu_page($base_page,TXT_WPSC_FORM_FIELDS, TXT_WPSC_FORM_FIELDS, 7, 'wp-shopping-cart/form_fields.php');
      add_submenu_page($base_page,TXT_WPSC_HELPINSTALLATION, TXT_WPSC_HELPINSTALLATION, 7, 'wp-shopping-cart/instructions.php');
      }
    return;
    }
     
  function products_page()
    {
    ob_start();
    require_once("products_page.php");
    //nzshpcrt_shopping_basket();
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
    }  
    
  function shopping_cart()
    {
    ob_start();
    require_once("shopping_cart.php");
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
    }  
    
  function transaction_results()
    {
    ob_start();
    require_once("transaction_results.php");
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
    }  
    
  function checkout()
    {
    ob_start();
    require_once("checkout.php");
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
    }  
  }

function nzshpcrt_style()
    {
  ?>
  <link href='<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-shopping-cart/style.css' rel="stylesheet" type="text/css" />
  <style type="text/css" media="screen">
    <?php
    if(isset($_GET['brand']) && is_numeric($_GET['brand']) || (get_option('show_categorybrands') == 3))
    {
    $brandstate = 'block';
    $categorystate = 'none';
    }
    else
      {
    $brandstate = 'none';
    $categorystate = 'block';
      }
    ?>
    div#categorydisplay{
    display: <?php echo $categorystate; ?>;
    }
    div#branddisplay{
    display: <?php echo $brandstate; ?>;
    }
  </style>
  <?php
    }
    
function nzshpcrt_javascript()
    {
  $siteurl = get_option('siteurl'); 
    ?>
<script language='JavaScript' type='text/javascript'>
var base_url = "<?php echo $siteurl; ?>";
<?
$loadgif = get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/loading.gif";
$closegif = get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/closelabel.gif";
?>
var fileLoadingImage = "<?php echo $loadgif; ?>";    
var fileBottomNavCloseImage = "<?php echo $closegif; ?>";
var resizeSpeed = 9;
var borderSize = 10;
</script>
<script src="<?php echo $siteurl; ?>/wp-content/plugins/wp-shopping-cart/ajax.js" language='JavaScript' type="text/javascript"></script>
<script src="<?php echo $siteurl; ?>/wp-content/plugins/wp-shopping-cart/js/prototype.js" language='JavaScript' type="text/javascript"></script>
<script src="<?php echo $siteurl; ?>/wp-content/plugins/wp-shopping-cart/js/effects.js" language='JavaScript' type="text/javascript"></script>
<script src="<?php echo $siteurl; ?>/wp-content/plugins/wp-shopping-cart/js/dragdrop.js" language='JavaScript' type="text/javascript"></script>
<script src="<?php echo $siteurl; ?>/wp-content/plugins/wp-shopping-cart/js/lightbox.js" language='JavaScript' type="text/javascript"></script>
<script src="<?php echo $siteurl; ?>/wp-content/plugins/wp-shopping-cart/user.js" language='JavaScript' type="text/javascript">
</script>
<?php
  }

function nzshpcrt_css()
    {
  $siteurl = get_option('siteurl'); 
    ?>
<script language='JavaScript' type='text/javascript'>
var base_url = "<?php echo $siteurl; ?>";
<?
$loadgif = get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/loading.gif";
$closegif = get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/closelabel.gif";
?>
var fileLoadingImage = "<?php echo $loadgif; ?>";    
var fileBottomNavCloseImage = "<?php echo $closegif; ?>";
var resizeSpeed = 9;  
var borderSize = 10;
<?php
require_once('ajax.js');
echo "var TXT_WPSC_DELETE = '".TXT_WPSC_DELETE."';\n\r";
echo "var TXT_WPSC_TEXT = '".TXT_WPSC_TEXT."';\n\r";
echo "var TXT_WPSC_EMAIL = '".TXT_WPSC_EMAIL."';\n\r";
echo "var TXT_WPSC_COUNTRY = '".TXT_WPSC_COUNTRY."';\n\r";
echo "var TXT_WPSC_TEXTAREA = '".TXT_WPSC_TEXTAREA."';\n\r";
echo "var TXT_WPSC_HEADING = '".TXT_WPSC_HEADING."';\n\r";
echo "var HTML_FORM_FIELD_TYPES =\"<option value='text' >".TXT_WPSC_TEXT."</option>";
echo "<option value='email' >".TXT_WPSC_EMAIL."</option>";
echo "<option value='address' >".TXT_WPSC_ADDRESS."</option>";
echo "<option value='city' >".TXT_WPSC_CITY."</option>";
echo "<option value='country'>".TXT_WPSC_COUNTRY."</option>";
echo "<option value='delivery_address' >".TXT_WPSC_DELIVERY_ADDRESS."</option>";
echo "<option value='delivery_city' >".TXT_WPSC_DELIVERY_CITY."</option>";
echo "<option value='delivery_country'>".TXT_WPSC_DELIVERY_COUNTRY."</option>";
echo "<option value='textarea' >".TXT_WPSC_TEXTAREA."</option>";
echo "<option value='heading' >".TXT_WPSC_HEADING."</option>\";\n\r";

require_once('admin.js');
?>
</script>
<script src="<?php echo $siteurl; ?>/wp-content/plugins/wp-shopping-cart/js/prototype.js" language='JavaScript' type="text/javascript"></script>
<script src="<?php echo $siteurl; ?>/wp-content/plugins/wp-shopping-cart/js/scriptaculous.js?load=effects" language='JavaScript' type="text/javascript"></script>
<script src="<?php echo $siteurl; ?>/wp-content/plugins/wp-shopping-cart/js/scriptaculous.js?load=dragdrop" language='JavaScript' type="text/javascript"></script>
<script src="<?php echo $siteurl; ?>/wp-content/plugins/wp-shopping-cart/js/lightbox.js" language='JavaScript' type="text/javascript"></script>
<?php
    }

function nzshpcrt_displaypages()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->displaypages();
  }

function nzshpcrt_adminpage()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->adminpage();
  }
  
function nzshpcrt_additem()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->additem();
  }

function nzshpcrt_displayitems()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->displayitems();
  }
  
function nzshpcrt_instructions()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->instructions();
  }

function nzshpcrt_options()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->options();
  }

function nzshpcrt_gatewayoptions()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->gatewayoptions();
  }
  
function nzshpcrt_products_page($content = '')
  {
  if(stristr($content,'[productspage]'))
    {
    $nzshpcrt = new wp_shopping_cart;
    $GLOBALS['nzshpcrt_activateshpcrt'] = true;
    $output = $nzshpcrt->products_page();
    if(function_exists('drag_and_drop_cart'))
      { 
      add_action('wp_footer', 'drag_and_drop_cart');
      }
    return preg_replace("/\[productspage\]/",$output, $content);
    }
    else
      {
      return $content;
      }
  }

function nzshpcrt_shopping_cart($content = '')
  {
    $output = null;
  if(preg_match("/\[shoppingcart\]/",$content))
    {
    $nzshpcrt = new wp_shopping_cart;
    $output =  $nzshpcrt->shopping_cart();
    }
  return preg_replace("/\[shoppingcart\]/", $output, $content);
  }

function nzshpcrt_transaction_results($content = '')
  {
  $nzshpcrt = new wp_shopping_cart;
  $output = $nzshpcrt->transaction_results();
  return preg_replace("/\[transactionresults\]/", $output, $content);
  }
  

function nzshpcrt_addcategory()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->addcategory();
  //$GLOBALS['nzshpcrt_activateshpcrt'] = true;
  }
  
function nzshpcrt_editcategory()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->editcategory();
  //$GLOBALS['nzshpcrt_activateshpcrt'] = true;
  }
  
function nzshpcrt_editbrands()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->editbrands();
  //$GLOBALS['nzshpcrt_activateshpcrt'] = true;
  }
  
function nzshpcrt_editvariations()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->editvariations();
  //$GLOBALS['nzshpcrt_activateshpcrt'] = true;
  }
  

function nzshpcrt_checkout($content = '')
  {
  $nzshpcrt = new wp_shopping_cart;
  $output = $nzshpcrt->checkout();
  //exit($output)
  //$output = "fart";
  return preg_replace("/\[checkout\]/", $output, $content);
  }
  
function nzshpcrt_submit_ajax()
  {
  global $wpdb,$user_level,$wp_rewrite;
  get_currentuserinfo();
  if(get_option('permalink_structure') != '')
    {
    $seperator ="?";
    }
    else
      {
      $seperator ="&amp;";
      }
   
  /* update shopping cart*/    
  if(isset($_GET['ajax']) and ($_GET['ajax'] == "true") && ($_GET['user'] == "true") && is_numeric($_POST['prodid']))
    {
    $sql = "SELECT * FROM `wp_product_list` WHERE `id`='".$_POST['prodid']."' LIMIT 1";
    $item_data = $wpdb->get_results($sql,ARRAY_A) ;

	// echo("<pre>".print_r($item_data,true)."</pre>");
    // if(is_array($_SESSION['nzshpcrt_cart']))
    //   {
    //   $cartquantities = array_count_values($_SESSION['nzshpcrt_cart']);
    //   }
    
    
    $item_quantity = 0;
    if(isset($_SESSION['nzshpcrt_cart']) and $_SESSION['nzshpcrt_cart'] != null)
      { 
      foreach($_SESSION['nzshpcrt_cart'] as $cart_key => $cart_item)
        {
        if($cart_item->product_id == $_POST['prodid'])
          {
          $item_quantity += $_SESSION['nzshpcrt_cart'][$cart_key]->quantity;
          }
          else
            {
            $item_quantity += 0;
            }
        }
      }
	  else
	{
		  // set new session for cart
		  $_SESSION['nzshpcrt_cart'] = Array();
	}

	if (isset($_SESSION['nzshpcrt_cart']))
	
	{
	//echo("<pre>_SESSION['nzshpcrt_cart']<br>".print_r($_SESSION['nzshpcrt_cart'],true)."</pre>");
		if((($item_data[0]['quantity_limited'] == 1) && ($item_data[0]['quantity'] != 0) && ($item_data[0]['quantity'] > $item_quantity)) || ($item_data[0]['quantity_limited'] == 0)) 
		  {
		  $cartcount = count($_SESSION['nzshpcrt_cart']);

			//echo "cartcount: ".$cartcount;

		  if(isset($_POST['variation']) && is_array($_POST['variation'])) {  $variations = $_POST['variation'];  }  else  { $variations = null; }
		  
		  $updated_quantity = false;
		  if($_SESSION['nzshpcrt_cart'] != null)
			{ 
			foreach($_SESSION['nzshpcrt_cart'] as $cart_key => $cart_item)
			  {
			  if($cart_item->product_id == $_POST['prodid'])
				{
				//ales
				
				$_SESSION['nzshpcrt_cart'][$cart_key]->license = $_POST['license'];

				if (isset($brand_id))
				{
					$_SESSION['nzshpcrt_cart'][$cart_key]->author = get_brand($brand_id);
				}

				if($_SESSION['nzshpcrt_cart'][$cart_key]->product_variations === $variations) 
				  {
					/*
				  if(is_numeric($_POST['quantity']))
					{
					//$_SESSION['nzshpcrt_cart'][$cart_key]->quantity += $_POST['quantity'];
					$_SESSION['nzshpcrt_cart'][$cart_key]->quantity = 1;
					}
					else
					  {
					  $_SESSION['nzshpcrt_cart'][$cart_key]->quantity++;
					  }
					*/
				  $_SESSION['nzshpcrt_cart'][$cart_key]->quantity = 1;
				  $updated_quantity = true;
				  }
				}
			  }
			}
		  if($updated_quantity === false)
			{
			if(isset($_POST['quantity']) && is_numeric($_POST['quantity']))
			  {
			  if($_POST['quantity'] > 0)
				{
				$new_cart_item = new cart_item($_POST['prodid'],$variations,$_POST['quantity']);
				}
			  }
			  else
				{
				$new_cart_item = new cart_item($_POST['prodid'],$variations);
				}
			$_SESSION['nzshpcrt_cart'][$cartcount + 1] = $new_cart_item;
			}
		  }
		  else 
			{
			$quantity_limit = true;
			}

	}	
	
	$quantity_limit = false;
    if (isset($_SESSION['nzshpcrt_cart']))
    {
        $cart = $_SESSION['nzshpcrt_cart'];   
    }
    else
    {
        $cart = null;
    }
    echo nzshpcrt_shopping_basket_internals($cart,$quantity_limit);
    exit();
    }
    else if(isset($_POST['ajax']) and ($_POST['ajax'] == "true") && (isset($_POST['user']) and $_POST['user'] == "true") && ($_POST['emptycart'] == "true"))
      {
      //$_SESSION['nzshpcrt_cart'] = '';
      $_SESSION['nzshpcrt_cart'] = Array();
	  $cart = $_SESSION['nzshpcrt_cart'];
      echo nzshpcrt_shopping_basket_internals($cart);
      exit();
      }
      
  /* fill product form */    
  if(isset($_POST['ajax']) and ($_POST['ajax'] == "true") && ($_POST['admin'] == "true") && isset($_POST['prodid']) && is_numeric($_POST['prodid']))
    {
    echo nzshpcrt_getproductform($_POST['prodid']);
    exit();
    }  /* fill category form */   
    else if(isset($_POST['ajax']) and ($_POST['ajax'] == "true") && ($_POST['admin'] == "true") && isset($_POST['catid']) && is_numeric($_POST['catid']))
      {
      echo nzshpcrt_getcategoryform($_POST['catid']);
      exit();
      }  /* fill brand form */ 
      else if(isset($_POST['ajax']) and ($_POST['ajax'] == "true") && ($_POST['admin'] == "true") && is_numeric($_POST['brandid']))
        {  
        echo nzshpcrt_getbrandsform($_POST['brandid']);
        exit();
        }
        else if(isset($_POST['ajax']) and ($_POST['ajax'] == "true") && ($_POST['admin'] == "true") && is_numeric($_POST['variation_id']))
          {  
          echo nzshpcrt_getvariationform($_POST['variation_id']);
          exit();
          }
          
  
  if(isset($_POST['ajax']) and ($_POST['ajax'] == "true") && is_numeric($_POST['currencyid']))
    {
    $currency_data = $wpdb->get_results("SELECT `symbol`,`symbol_html`,`code` FROM `wp_currency_list` WHERE `id`='".$_POST['currencyid']."' LIMIT 1",ARRAY_A) ;
    $price_out = null;
    if($currency_data[0]['symbol'] != '')
      {
      $currency_sign = $currency_data[0]['symbol_html'];
      }
      else
        {
        $currency_sign = $currency_data[0]['code'];
        }
    echo $currency_sign;
    exit();
    }
  
  
  /* rate item */    
  if(isset($_POST['ajax']) and ($_POST['ajax'] == "true") && ($_POST['rate_item'] == "true") && is_numeric($_POST['product_id']) && is_numeric($_POST['rating']))
    {
    $nowtime = time();
    $prodid = $_POST['product_id'];
    $ip_number = $_SERVER['REMOTE_ADDR'];
    $rating = $_POST['rating'];
    
    $cookie_data = explode(",",$_COOKIE['voting_cookie'][$prodid]);
    
    if(is_numeric($cookie_data[0]) && ($cookie_data[0] > 0))
      {
      $vote_id = $cookie_data[0];
      $wpdb->query("UPDATE `wp_product_rating` SET `rated` = '".$rating."' WHERE `id` ='".$vote_id."' LIMIT 1 ;");
      }
      else
        {
        $insert_sql = "INSERT INTO `wp_product_rating` ( `id` , `ipnum`  , `productid` , `rated`, `time`) VALUES ( '', '".$ip_number."', '".$prodid."', '".$rating."', '".$nowtime."');";
        $wpdb->query($insert_sql);
        
        $data = $wpdb->get_results("SELECT `id`,`rated` FROM `wp_product_rating` WHERE `ipnum`='".$ip_number."' AND `productid` = '".$prodid."'  AND `rated` = '".$rating."' AND `time` = '".$nowtime."' ORDER BY `id` DESC LIMIT 1",ARRAY_A) ;
        
        $vote_id = $data[0]['id'];
        setcookie("voting_cookie[$prodid]", ($vote_id.",".$rating),time()+(60*60*24*360));
        }   
    
    
    
    $output[1]= $prodid;
    $output[2]= $rating;
    echo $output[1].",".$output[2];
    exit();
    }
    
  if(isset($_POST['ajax']) and ($_POST['ajax'] == "true") && ($_POST['get_rating_count'] == "true") && is_numeric($_POST['product_id']))
    {
    $prodid = $_POST['product_id'];
    $data = $wpdb->get_results("SELECT COUNT(*) AS `count` FROM `wp_product_rating` WHERE `productid` = '".$prodid."'",ARRAY_A) ;
    echo $data[0]['count'].",".$prodid;
    exit();
    }
    
  if(isset($_POST['ajax']) and ($_POST['ajax'] == "true") && ($_POST['remove_variation_value'] == "true") && is_numeric($_POST['variation_value_id']))
    {
    if($user_level >= 7)
     {
     $wpdb->query("DELETE FROM `wp_variation_values_associations` WHERE `value_id` = '".$_POST['variation_value_id']."'");
     $wpdb->query("DELETE FROM `wp_variation_values` WHERE `id` = '".$_POST['variation_value_id']."' LIMIT 1");
     exit();
     }
    }
    
  if(isset($_POST['ajax']) and ($_POST['ajax'] == "true") && ($_POST['list_variation_values'] == "true") && is_numeric($_POST['variation_id']))
    {
    if($user_level >= 7)
     {
     $variation_processor = new nzshpcrt_variations();
     //product_variations_"+(parseInt(child_element_count)+1)
     echo "variation_value_id = \"".$_POST['variation_id']."\";\n";
     echo "variation_value_html = \"".$variation_processor->display_variation_values($_POST['prefix'],$_POST['variation_id'])."\";\n";
     exit();
     }
    }
    
  if(isset($_POST['ajax']) and ($_POST['ajax'] == "true") && ($_POST['remove_form_field'] == "true") && is_numeric($_POST['form_id']))
    {
    if($user_level >= 7)
     {
     $wpdb->query("UPDATE `wp_collect_data_forms` SET `active` = '0' WHERE `id` ='".$_POST['form_id']."' LIMIT 1 ;");
     exit();
     }
    }
    
  if(isset($_POST['ajax']) and ($_POST['ajax'] == "true") && ($_POST['remove_form_field'] == "true") && is_numeric($_POST['form_id']))
    {
    if($user_level >= 7)
     {
     $wpdb->query("UPDATE `wp_collect_data_forms` SET `active` = '0' WHERE `id` ='".$_POST['form_id']."' LIMIT 1 ;");
     exit();
     }
    }
    
  if(isset($_POST['ajax']) and ($_POST['ajax'] == "true") && ($_POST['user'] == "true") && ($_POST['drag_and_drop_cart'] == "true"))
    {
    drag_and_drop_cart_contents();
    exit();
    }
  
  if(isset($_POST['ajax']) and ($_POST['ajax'] == "true") && ($_POST['get_country_tax'] == "true") && preg_match("/[a-zA-Z]{2,4}/",$_POST['country_id']))  
    {
    $country_id = $_POST['country_id'];
    $region_list = $wpdb->get_results("SELECT `wp_region_tax`.* FROM `wp_region_tax`, `wp_currency_list`  WHERE `wp_currency_list`.`isocode` IN('".$country_id."') AND `wp_currency_list`.`id` = `wp_region_tax`.`country_id`",ARRAY_A) ;
    if($region_list != null)
      {
      echo "<select name='base_region'>\n\r";
      foreach($region_list as $region)
        {
        if(get_option('base_region')  == $region['id'])
          {
          $selected = "selected='true'";
          }
          else
            {
            $selected = "";
            }
        echo "<option value='".$region['id']."' $selected>".$region['name']."</option>\n\r";
        }
      echo "</select>\n\r";    
      }
      else { echo "&nbsp;"; }
    exit();
    }
    
   if(isset($_POST['language_setting']) && ($_GET['page'] = 'wp-shopping-cart/options.php'))
    {
    if($user_level >= 7)
      {
      update_option('language_setting', $_POST['language_setting']);
      }
    }
  
  if(isset($_POST['language_setting']) && ($_GET['page'] = 'wp-shopping-cart/options.php'))
    {
    if($user_level >= 7)
      {
      update_option('language_setting', $_POST['language_setting']);
      }
    }
    
    
  if(isset($_GET['rss']) and ($_GET['rss'] == "true") && ($_GET['rss_key'] == 'key') && ($_GET['action'] == "purchase_log"))
    {
    $sql = "SELECT * FROM `wp_purchase_logs` WHERE `date`!='' ORDER BY `date` DESC";
    $purchase_log = $wpdb->get_results($sql,ARRAY_A);
    header("Content-Type: application/xml; charset=ISO-8859-1"); 
    header('Content-Disposition: inline; filename="WP_E-Commerce_Purchase_Log.rss"');
    $output = '';
    $output .= "<?xml version='1.0'?>\n\r";
    $output .= "<rss version='2.0'>\n\r";
    $output .= "  <channel>\n\r";
    $output .= "    <title>WP E-Commerce Product Log</title>\n\r";
    $output .= "    <link>".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-log.php</link>\n\r";
    $output .= "    <description>This is the WP E-Commerce Product Log RSS feed</description>\n\r";
    $output .= "    <generator>WP E-Commerce Plugin</generator>\n\r";
    
    foreach($purchase_log as $purchase)
      {
      $purchase_link = get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-log.php&amp;purchaseid=".$purchase['id'];
      $output .= "    <item>\n\r";
      $output .= "      <title>Purchase No. ".$purchase['id']."</title>\n\r";
      $output .= "      <link>$purchase_link</link>\n\r";
      $output .= "      <description>This is an entry in the purchase log.</description>\n\r";
      $output .= "      <pubDate>".date("r",$purchase['date'])."</pubDate>\n\r";
      $output .= "      <guid>$purchase_link</guid>\n\r";
      $output .= "    </item>\n\r";
      }
    $output .= "  </channel>\n\r";
    $output .= "</rss>";
    echo $output;
    exit();
    }
  
       
  if(isset($_GET['rss']) and ($_GET['rss'] == "true") && ($_GET['action'] == "product_list"))
    {
    $sql = "SELECT * FROM `wp_product_list` WHERE `active` IN('1')";
    $product_list = $wpdb->get_results($sql,ARRAY_A);
    //header("Content-Type: application/xml; charset=ISO-8859-1"); 
    //header('Content-Disposition: inline; filename="WP_E-Commerce_Product_List.rss"');
    $output = '';
    $output .= "<?xml version='1.0'?>\n\r";
    $output .= "<rss version='2.0'>\n\r";
    $output .= "  <channel>\n\r";
    $output .= "    <title>WP E-Commerce Product Log</title>\n\r";
    $output .= "    <link>".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-log.php</link>\n\r";
    $output .= "    <description>This is the WP E-Commerce Product List RSS feed</description>\n\r";
    $output .= "    <generator>WP E-Commerce Plugin</generator>\n\r";
    
    foreach($product_list as $product)
      {
      $purchase_link = get_option('product_list_url').$seperator."product_id=".$product['id'];
      $output .= "    <item>\n\r";
      $output .= "      <title>".stripslashes($product['name'])."</title>\n\r";
      $output .= "      <link>$purchase_link</link>\n\r";
      $output .= "      <description>".stripslashes($product['description'])."</description>\n\r";
      $output .= "      <pubDate>".date("r")."</pubDate>\n\r";
      $output .= "      <guid>$purchase_link</guid>\n\r";
      $output .= "    </item>\n\r";
      }
    $output .= "  </channel>\n\r";
    $output .= "</rss>";
    echo $output;
    exit();
    }
  
  if(isset($_GET['purchase_log_csv']) and ($_GET['purchase_log_csv'] == "true") && ($_GET['rss_key'] == 'key') && is_numeric($_GET['start_timestamp']) && is_numeric($_GET['end_timestamp']))
    {
    $form_sql = "SELECT * FROM `wp_collect_data_forms` WHERE `active` = '1' AND `display_log` = '1';";
    $form_data = $wpdb->get_results($form_sql,ARRAY_A);
    
    $start_timestamp = $_GET['start_timestamp'];
    $end_timestamp = $_GET['end_timestamp'];
    $data = $wpdb->get_results("SELECT * FROM `wp_purchase_logs` WHERE `date` BETWEEN '$start_timestamp' AND '$end_timestamp' ORDER BY `date` DESC",ARRAY_A);
    
     header('Content-Type: text/csv');
     header('Content-Disposition: inline; filename="Purchase Log '.date("M-d-Y", $start_timestamp).' to '.date("M-d-Y", $end_timestamp).'.csv"');      
    $output .= "\"". TXT_WPSC_PRICE ."\",";
                
    foreach($form_data as $form_field)
      {
      $output .= "\"".$form_field['name']."\",";
      }
    
        
    if(get_option('payment_method') == 2)
      {
      $output .= "\"". TXT_WPSC_PAYMENT_METHOD ."\",";
      }
    
    $output .= "\"". TXT_WPSC_STATUS ."\",";
    
    $output .= "\"". TXT_WPSC_DATE ."\"\n";
      
    foreach($data as $purchase)
      {
      $country_sql = "SELECT * FROM `wp_submited_form_data` WHERE `log_id` = '".$purchase['id']."' AND `form_id` = '".get_option('country_form_field')."' LIMIT 1";
      $country_data = $wpdb->get_results($country_sql,ARRAY_A);
      $country = $country_data[0]['value'];
           
      $output .= "\"".nzshpcrt_find_total_price($purchase['id'],$country) ."\",";
                
      foreach($form_data as $form_field)
        {
        $collected_data_sql = "SELECT * FROM `wp_submited_form_data` WHERE `log_id` = '".$purchase['id']."' AND `form_id` = '".$form_field['id']."' LIMIT 1";
        $collected_data = $wpdb->get_results($collected_data_sql,ARRAY_A);
        $collected_data = $collected_data[0];
        $output .= "\"".$collected_data['value']."\",";
        }
        
      if(get_option('payment_method') == 2)
        {
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
        $output .= "\"". $gateway_name ."\",";
        }
              
      if($purchase['processed'] < 1)
        {
        $purchase['processed'] = 1;
        }
      $stage_sql = "SELECT * FROM `wp_purchase_statuses` WHERE `id`='".$purchase['processed']."' AND `active`='1' LIMIT 1";
      $stage_data = $wpdb->get_results($stage_sql,ARRAY_A);
              
      $output .= "\"". $stage_data[0]['name'] ."\",";
      
      $output .= "\"". date("jS M Y",$purchase['date']) ."\"\n";
      }
    echo $output;
    exit();
    }
  
    if(isset($_GET['remove']) and is_numeric($_GET['remove']) && ($_SESSION['nzshpcrt_cart'] != null))
      {
      $key = $_GET['remove'];
      if(isset($_SESSION['nzshpcrt_cart'][$key]) && is_object($_SESSION['nzshpcrt_cart'][$key]))
        {
        $_SESSION['nzshpcrt_cart'][$key]->empty_item();
        }
      unset($_SESSION['nzshpcrt_cart'][$key]);
      }
    
    if(isset($_GET['cart']) and $_GET['cart']== 'empty')
      {
      $_SESSION['nzshpcrt_cart'] = '';
      $_SESSION['nzshpcrt_cart'] = Array();
      }
      
    if(isset($_POST['quantity']) and is_numeric($_POST['quantity']) && is_numeric($_POST['key']))
      {
      $quantity = $_POST['quantity'];
      $key = $_POST['key'];
      if(is_object($_SESSION['nzshpcrt_cart'][$key]))
        {
        if($quantity > 0)
          {
          $_SESSION['nzshpcrt_cart'][$key]->quantity = $quantity;
          }
          else
            {
            $_SESSION['nzshpcrt_cart'][$key]->empty_item();
            unset($_SESSION['nzshpcrt_cart'][$key]);
            }
         }
       }
  }
  

    function brandslist($current_brand = '')
    {
		global $wpdb, $user_brand, $current_user;
		$combo_disabled = '';
		$options = '';
		$selected = '';
		$values = $wpdb->get_results("SELECT * FROM `wp_product_brands` WHERE `active`='1' ORDER BY `id` ASC",ARRAY_A);
		$options .= "<option $selected value='0'>Выберите автора</option>\r\n";

		if (isset($current_user->wp_capabilities['author']) && $current_user->wp_capabilities['author']==1)
			$combo_disabled = "disabled='disabled'";

		$who_is_selected_brand = 0;
		if(isset($_GET['brand']) && is_numeric($_GET['brand'])) // we ordered selected user
		{
			$who_is_selected_brand = $_GET['brand'];
		}
		else if (isset($user_brand)) // select logged user
		{
			$who_is_selected_brand = $user_brand;
		}
		else if (isset($current_brand)) // select logged user
		{
			$who_is_selected_brand = $current_brand;
		}

	  foreach($values as $option)
		{
		if($who_is_selected_brand == $option['id'])
		  {
		  $selected = "selected='selected'";
		  }
		  $options .= "<option $selected value='".$option['id']."'>".$option['name']."</option>\r\n";
		  $selected = "";
		}

		$concat = "<select name='brand' id='brandslist' $combo_disabled'>".$options."</select>\r\n";
		return $concat;
		}
  
  function variationslist($current_variation = '')
    {
    global $wpdb;
    $options = "";
    //$options .= "<option value=''>".TXT_WPSC_SELECTACATEGORY."</option>\r\n";
    $values = $wpdb->get_results("SELECT * FROM `wp_product_variations` ORDER BY `id` ASC",ARRAY_A);
    $options .= "<option  $selected value='0'>".TXT_WPSC_SELECTAVARIATION."</option>\r\n";
    //$options .= "<option  $selected value='add'>".TXT_WPSC_NEW_VARIATION."</option>\r\n";
    if($values != null)
      {
      foreach($values as $option)
        {
        if($current_brand == $option['id'])
          {
          $selected = "selected='selected'";
          }
        $options .= "<option  $selected value='".$option['id']."'>".$option['name']."</option>\r\n";
        $selected = "";
        }
      }
    $concat .= "<select name='variations' onChange='variation_value_list(this.options[this.selectedIndex].value)'>".$options."</select>\r\n";
    return $concat;
    }

  function nzshpcrt_getproductform($prodid)
  {
  global $wpdb,$nzshpcrt_imagesize_info,$current_user;
 /*
  * makes the product form
  * has functions inside a function
  */ 
  $sql = "SELECT * FROM `wp_product_list` WHERE `id`=$prodid LIMIT 1";
  $product_data = $wpdb->get_results($sql,ARRAY_A) ;
  $product = $product_data[0];
  
  $output = "        <table>\n\r";
  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= "Автор: ";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= brandslist($product['brand']);

  $approved = 0;
	if ($product['approved'] == '1')
		$approved = " checked='checked'";
//pokazh($current_user->wp_capabilities);
	if (isset($current_user->wp_capabilities['administrator']) && $current_user->wp_capabilities['administrator']==1)
		{$output .= "<input type='checkbox' name='approved'".$approved."/> Утвержено.";}
	else if (isset($current_user->wp_capabilities['editor']) && $current_user->wp_capabilities['editor']==1)
		{$output .= "<input type='checkbox' name='approved'".$approved."/> Утвержено.";}

  
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";
  
  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= "Имя картинки (<a href='".get_option('siteurl')."/?page_id=29&cartoonid=".$product['id']."' target=_blank>".$product['id']."</a>): ";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<input id='productnameedit' type='text' style='width:300px;' name='title' value='".stripslashes($product['name'])."' />";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";
  
  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= "Описание картинки (текстовое): ";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<textarea id='productdescredit' name='description' cols='40' rows='3' >".stripslashes($product['description'])."</textarea>";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";
  
  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= "Ключевые слова, разделённые запятыми: ";

  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<textarea id='tagsedit' name='additional_description' cols='40' rows='3' >".stripslashes($product['additional_description'])."</textarea>";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";

  $visible = "";
  if ($product['visible'] == '1')
    $visible = " checked='checked'";
 
  $output .= "          <tr>\n\r";
  $output .= "          </tr>\n\r";

  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= "Видно всем:";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<input type='checkbox' name='visible'".$visible."/>";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";

  $colored = "";
  if ($product['color'] == '1')
    $colored = " checked='checked' ";


  $not_for_sale = "";
  if ($product['not_for_sale'] == '1')
    $not_for_sale = " checked='checked' ";

  $license1checked = "";
  if ($product['l1_price'] != '0')
    $license1checked = " checked='checked' ";

  $license2checked = "";
  if ($product['l2_price'] != '0')
    $license2checked = " checked='checked' ";

  $license3checked = "";
  if ($product['l3_price'] != '0')
    $license3checked = " checked='checked' ";

  $output .= "          <tr>\n\r";
  $output .= "            <td style='background-color:#FFFF33;'>\n\r";
  $output .= "Цветная:";
  $output .= "            </td>\n\r";
  $output .= "            <td style='background-color:#FFFF33;'>\n\r";
  $output .= "<input type='checkbox' name='colored'".$colored."/>";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";


  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= "Не для продажи:";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<input type='checkbox' name='not_for_sale'".$not_for_sale."/>";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";


  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";

    $basepath =  str_replace("/wp-admin", "" , getcwd());
    if(file_exists($basepath."/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']))
            {
            $image_location = "product_images/".$product['image'];
            }
            else
              {
              $image_location = "images/".$product['image'];
              }
    $preview_location = "product_images/".$product['image'];
    $icon_location = "images/".$product['image'];

    $m_image_link = get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/".$preview_location;

    $output .= "<a href='".$m_image_link."' target=_blank><img id='previewimage' src='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/$icon_location' alt='".TXT_WPSC_PREVIEW."' title='".TXT_WPSC_PREVIEW."' /></a>";

  
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= categorylist($product['id']);
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";

  /*
	
	  $check_variation_values = $wpdb->get_results("SELECT COUNT(*) as `count` FROM `wp_variation_values_associations` WHERE `product_id` = '".$product['id']."'",ARRAY_A);
	  $check_variation_value_count = $check_variation_values[0]['count'];
	  if($check_variation_value_count > 0)
		{
		$output .= "          <tr>\n\r";
		$output .= "            <td>\n\r";
		$output .= TXT_WPSC_EDIT_VAR.": ";
		$output .= "            </td>\n\r";
		$output .= "            <td>\n\r";
		$variations_procesor = new nzshpcrt_variations;
		$output .= $variations_procesor->display_attached_variations($product['id']);
		$output .= "            </td>\n\r";
		$output .= "          </tr>\n\r";
		}

*/

  $output .= "          <tr>\n\r";
  $output .= "            <td colspan='2'>\n\r";
  $output .= "<br><a  href='admin.php?page=wp-shopping-cart/display-items.php&amp;updateimage=".$product['id']."' >нажмите здесь, чтобы обновить иконку и слайд с водяными знаками</a>";
  // class='button'
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";

    

  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= "Доступны лицензии:";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "Огр:&nbsp;<input id='license1' type='checkbox' name='license1'".$license1checked.">&nbsp;&nbsp;&nbsp;Станд:&nbsp;<input id='license2' type='checkbox' name='license2'".$license2checked.">&nbsp;&nbsp;&nbsp;Расш:&nbsp;<input id='license3' type='checkbox' name='license3'".$license3checked."><br />";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";


 /*
  if(function_exists("getimagesize"))
    {
    if($product['image'] != '')
      {
      $basepath =  str_replace("/wp-admin", "" , getcwd());
      $imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/product_images/thumbnails/";
      $imagepath = $imagedir . $product['image'];
      include('getimagesize.php');
      $output .= "          <tr>\n\r";
      $output .= "            <td>\n\r";
      $output .= TXT_WPSC_RESIZEIMAGE.": <br />";
      
      $basepath =  str_replace("/wp-admin", "" , getcwd());
      $imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/product_images/thumbnails/";
      $image_size = @getimagesize($imagedir.$product['image']);
      $output .= "<span class='image_size_text'>".$image_size[0]."x".$image_size[1]."</span>";
      
      $output .= "            </td>\n\r";  
      
      $output .= "            <td>\n\r";
      $output .= "<table>";
      $output .= "  <tr>";
      $output .= "    <td>";
      
      $output .= "<table>";
      
      $output .= "  <tr>";
      $output .= "    <td>";
      $output .= "<input type='radio' checked='true' name='image_resize' value='0' id='image_resize0' class='image_resize' /> <label for='image_resize0'> ".TXT_WPSC_DONOTRESIZEIMAGE."<br />
        ";
      $output .= "    </td>";
      $output .= "  </tr>";
      
      $output .= "  <tr>";
      $output .= "    <td>";
      $output .= "<input type='radio' name='image_resize' value='1' id='image_resize1' class='image_resize' /> <label for='image_resize1'>".TXT_WPSC_USEDEFAULTHEIGHTANDWIDTH." (".get_option('product_image_height') ."x".get_option('product_image_width').")";
      $output .= "    </td>";
      $output .= "  </tr>";

      
      $output .= "</table>";
      $output .= "    </td>";

      $output .= "    <td>";
 
         if(file_exists($basepath."/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']))
                {
                $image_location = "product_images/".$product['image'];
                }
                else
                  {
                  $image_location = "images/".$product['image'];
                  }
        $preview_location = "product_images/".$product['image'];
        $icon_location = "images/".$product['image'];

        $m_image_link = get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/".$preview_location;

        $output .= "<a href='".$m_image_link."' target=_blank><img id='previewimage' src='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/$icon_location' alt='".TXT_WPSC_PREVIEW."' title='".TXT_WPSC_PREVIEW."' /></a>";


      $output .= "    </td>";
      $output .= "  </tr>";

      $output .= "</table>";
      $output .= "            </td>\n\r";
      $output .= "          </tr>\n\r";
      }
    }
    
  if(function_exists("getimagesize"))
    {
    if($product['image'] == '')
      {
      $output .= "          <tr>\n\r";
      $output .= "            <td>\n\r";
      $output .= "";
      $output .= "            </td>\n\r";
      $output .= "            <td>\n\r";
      $output .= "<input type='radio' checked='true' name='image_resize' value='1' id='image_resize1' class='image_resize' /> <label for='image_resize1'>".TXT_WPSC_USEDEFAULTHEIGHTANDWIDTH." (".get_option('product_image_height') ."x".get_option('product_image_width').")";
      $output .= "            </td>\n\r";
      $output .= "          </tr>\n\r";

      $output .= "          <tr>\n\r";
      $output .= "            <td>\n\r";
      $output .= "";
      $output .= "            </td>\n\r";
      $output .= "            <td>\n\r";
      $output .= "<input type='radio' name='image_resize' value='2' id='image_resize2' class='image_resize'  />
        <label for='image_resize2'>".TXT_WPSC_USE." </label><input onclick='checkimageresize()' type='text' size='4' name='height' value='' /><label for='image_resize2'>".TXT_WPSC_PXHEIGHTBY." </label><input onclick='checkimageresize()' type='text' size='4' name='width' value='' /><label for='image_resize2'>".TXT_WPSC_PXWIDTH."</label>";
      $output .= "            </td>\n\r";
      $output .= "          </tr>\n\r";
      }
    }

  if(function_exists('edit_multiple_image_form'))
    {
    $output .= edit_multiple_image_form($product['id']); 
    }
  
  */
  // download original image  
  if($product['file'] > 0)
    {
    $output .= "          <tr>\n\r";
    $output .= "            <td colspan='2'>\n\r";
    $output .= "<br /><strong class='form_group'>".TXT_WPSC_PRODUCTDOWNLOAD."</strong>";
    $output .= "            </td>\n\r";
    $output .= "          </tr>\n\r";
    
    $output .= "          <tr>\n\r";
    $output .= "            <td>\n\r";
    $output .= TXT_WPSC_PREVIEW_FILE.": ";
    $output .= "            </td>\n\r";
    $output .= "            <td>\n\r";    
    
    $output .= "<a class='admin_download' href='index.php?admin_preview=true&product_id=".$product['id']."' style='float: left;' ><img align='absmiddle' src='../wp-content/plugins/wp-shopping-cart/images/download.gif' alt='' title='' /><span>".TXT_WPSC_CLICKTODOWNLOAD."</span></a>";
    
    if(is_numeric($product['file']) && ($product['file'] > 0))
      {
      $file_data = $wpdb->get_results("SELECT * FROM `wp_product_files` WHERE `id`='".$product['file']."' LIMIT 1",ARRAY_A);
      if(($file_data != null) && ($file_data[0]['mimetype'] == 'audio/mpeg') && (function_exists('listen_button')))
        {
        $output .= "&nbsp;&nbsp;&nbsp;".listen_button($file_data[0]['idhash']);
        }
      }
        
    $output .= "            </td>\n\r";
    $output .= "          </tr>\n\r";
  
              
    $output .= "          <tr>\n\r";
    $output .= "            <td>\n\r";
    $output .= TXT_WPSC_REPLACE_PRODUCT.": ";
    $output .= "            </td>\n\r";
    $output .= "            <td>\n\r";
    $output .= "<input type='file' name='file' value='' /> <span class='small'><br />".TXT_WPSC_FILETOBEPRODUCT."</span><br /><br />";
    $output .= "            </td>\n\r";
    $output .= "          </tr>\n\r";
    }
  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<input type='hidden' name='prodid' value='".$product['id']."' />";
  $output .= "<input type='hidden' name='submit_action' value='edit' />";
  //$output .= "<input class='edit_button' type='submit' name='submit' value='????????? ?????????' />";
  
  
  $output .= "<br><input type=\"button\" class='edit_button' style='padding:6px; background-color:#93F273;' name='sendit' value='сохранить изменения' onclick=\"checkthefieldsEditForm();\"/>";

  $output .= "<br><br><br><br><a class='button' href='admin.php?page=wp-shopping-cart/display-items.php&amp;deleteid=".$product['id']."' onclick=\"return conf();\" >стереть изображение</a>";
  
  $output .= "            <td>\n\r";
  $output .= "          </tr>\n\r";
  
  $output .= "        </table>\n\r";
  
  // TODO: Remove before upload to the server! temp! local debug only!
  if ($_SERVER['SERVER_NAME']=='localhost')
	  {$output = Utf8ToWin($output);}
  
  return $output;
  }

function nzshpcrt_getcategoryform($catid)
  {
  global $wpdb,$nzshpcrt_imagesize_info;
  function parent_category_list($category_id, $category_parent_id)
    {
    global $wpdb,$category_data;
    $options = "";
    $values = $wpdb->get_results("SELECT * FROM `wp_product_categories` WHERE `category_parent`='0' AND `active` = '1' AND `id` != '$category_id' ORDER BY `id` ASC",ARRAY_A);
    $url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']."?page=wp-shopping-cart/display-items.php";
    $options .= "<option value='$url'>".TXT_WPSC_SELECT_PARENT."</option>\r\n";
    if($values != null)
      {
      foreach($values as $option)
        {
        $category_data[$option['id']] = $option['name'];
        if($category_parent_id == $option['id'])
          {
          $selected = "selected='selected'";
          }
        $options .= "<option $selected value='".$option['id']."'>".$option['name']."</option>\r\n";
        $selected = "";
        }
      }
    $concat .= "<select name='category_parent'>".$options."</select>\r\n";
    return $concat;
    }
  
  $sql = "SELECT * FROM `wp_product_categories` WHERE `id`=$catid LIMIT 1";
  $product_data = $wpdb->get_results($sql,ARRAY_A) ;
  $product = $product_data[0];
  $output = "        <table>\n\r";
  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= TXT_WPSC_NAME.": ";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<input type='text' name='title' value='".stripslashes($product['name'])."' />";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";

  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= TXT_WPSC_DESCRIPTION.": ";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<textarea name='description' cols='40' rows='8' >".stripslashes($product['description'])."</textarea>";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";
  $output .= "          </tr>\n\r";

  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= TXT_WPSC_CATEGORY_PARENT.": ";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= parent_category_list($product['id'], $product['category_parent']);
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";
  $output .= "          </tr>\n\r";

  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= TXT_WPSC_IMAGE.": ";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<input type='file' name='image' value='' />";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";
  $output .= "          </tr>\n\r";

  if(function_exists("getimagesize"))
    {
    if($product['image'] != '')
      {
      $basepath =  str_replace("/wp-admin", "" , getcwd());
      $imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/category_images/";
      $imagepath = $imagedir . $product['image'];
      include('getimagesize.php');
      $output .= "          <tr>\n\r";
      $output .= "            <td>\n\r";
      $output .= "            </td>\n\r";
      $output .= "            <td>\n\r";
      $output .= TXT_WPSC_HEIGHT.":<input type='text' size='6' name='height' value='".$imagetype[1]."' /> ".TXT_WPSC_WIDTH.":<input type='text' size='6' name='width' value='".$imagetype[0]."' /><br /><span class='small'>$nzshpcrt_imagesize_info</span>";
      $output .= "            </td>\n\r";
      $output .= "          </tr>\n\r";
      }
      else
        {
        $output .= "          <tr>\n\r";
        $output .= "            <td>\n\r";
        $output .= "            </td>\n\r";
        $output .= "            <td>\n\r";
        $output .= TXT_WPSC_HEIGHT.":<input type='text' size='6' name='height' value='".get_option('product_image_height')."' /> ".TXT_WPSC_WIDTH.":<input type='text' size='6' name='width' value='".get_option('product_image_width')."' /><br /><span class='small'>$nzshpcrt_imagesize_info</span>";
        $output .= "            </td>\n\r";
        $output .= "          </tr>\n\r";
        }
    }

  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= TXT_WPSC_DELETEIMAGE.": ";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<input type='checkbox' name='deleteimage' value='1' />";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";
  $output .= "          </tr>\n\r";

  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<input type='hidden' name='prodid' value='".$product['id']."' />";
  $output .= "<input type='hidden' name='submit_action' value='edit' />";
  $output .= "<input class='edit_button' type='submit' name='submit' value='".TXT_WPSC_EDIT."' />";
  $output .= "<a class='delete_button' href='admin.php?page=wp-shopping-cart/display-category.php&amp;deleteid=".$product['id']."' onclick=\"return conf();\" >".TXT_WPSC_DELETE."</a>";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";
 $output .= "        </table>\n\r"; 
  return $output;
  }

function nzshpcrt_getbrandsform($catid)
  {
  global $wpdb,$nzshpcrt_imagesize_info;

  $sql = "SELECT * FROM `wp_product_brands` WHERE `id`='$catid' LIMIT 1";
  $product_data = $wpdb->get_results($sql,ARRAY_A) ;
  $product = $product_data[0];
  $output = "        <table border='1'>\n\r";
  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= "имя: ";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<input type='text' name='title' value='".stripslashes($product['name'])."' />";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";

  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= "описание: ";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<textarea name='description' cols='40' rows='8' >".stripslashes($product['description'])."</textarea>";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";
  $output .= "          </tr>\n\r";

  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= "user_id: ";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<input name='user_id' value='".$product['user_id']."'>";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";
  $output .= "          </tr>\n\r";


  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<input type='hidden' name='prodid' value='".$product['id']."' />";
  $output .= "<input type='hidden' name='submit_action' value='edit' />";
  $output .= "<input class='edit_button' type='submit' name='submit' value='".TXT_WPSC_EDIT."' />";
  $output .= "<a class='delete_button' href='admin.php?page=wp-shopping-cart/display-brands.php&amp;deleteid=".$product['id']."' onclick=\"return conf();\" >стереть</a>";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";
 $output .= "        </table>\n\r";
  return $output;
  }
  
function nzshpcrt_getvariationform($variation_id)
  {
  global $wpdb,$nzshpcrt_imagesize_info;

  $variation_sql = "SELECT * FROM `wp_product_variations` WHERE `id`='$variation_id' LIMIT 1";
  $variation_data = $wpdb->get_results($variation_sql,ARRAY_A) ;
  $variation = $variation_data[0];
  $output .= "        <table>\n\r";
  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= TXT_WPSC_NAME.": ";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<input type='text' name='title' value='".stripslashes($variation['name'])."' />";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";

  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= TXT_WPSC_VARIATION_VALUES.": ";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $variation_values_sql = "SELECT * FROM `wp_variation_values` WHERE `variation_id`='$variation_id' ORDER BY `id` ASC";
  $variation_values = $wpdb->get_results($variation_values_sql,ARRAY_A);
  $variation_value_count = count($variation_values);
  $output .= "<div id='edit_variation_values'>";
  $num = 0;
  foreach($variation_values as $variation_value)
    {
    $output .= "<span id='variation_value_".$num."'>";
    $output .= "<input type='text' name='variation_values[".$variation_value['id']."]' value='".stripslashes($variation_value['name'])."' />";
    if($variation_value_count > 1)
      {
      $output .= " <a  class='image_link' onclick='remove_variation_value(\"variation_value_".$num."\",".$variation_value['id'].")' href='#'><img src='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/trash.gif' alt='".TXT_WPSC_DELETE."' title='".TXT_WPSC_DELETE."' /></a>";
      //admin.php?page=wp-shopping-cart/display_variations.php&amp;delete_value=true&amp;variation_id=".$variation_id."&amp;value_id=".$variation_value['id']."
      }
    $output .= "<br />";
    $output .= "</span>";
    $num++;
    }
  $output .= "</div>";
  $output .= "<a href='#'  onclick='return add_variation_value(\"edit\")'>".TXT_WPSC_ADD."</a>";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";
  $output .= "          </tr>\n\r";

  $output .= "          <tr>\n\r";
  $output .= "            <td>\n\r";
  $output .= "            </td>\n\r";
  $output .= "            <td>\n\r";
  $output .= "<input type='hidden' name='prodid' value='".$variation['id']."' />";
  $output .= "<input type='hidden' name='submit_action' value='edit' />";
  $output .= "<input class='edit_button' type='submit' name='submit' value='".TXT_WPSC_EDIT."' />";
  $output .= "<a class='delete_button' href='admin.php?page=wp-shopping-cart/display_variations.php&amp;deleteid=".$variation['id']."' onclick=\"return conf();\" >".TXT_WPSC_DELETE."</a>";
  $output .= "            </td>\n\r";
  $output .= "          </tr>\n\r";
 $output .= "        </table>\n\r";
  return $output;
  }

function nzshpcrt_submit_checkout()
  {
/*
* This is the function used for handling the submitted checkout page
*/
global $wpdb, $nzshpcrt_gateways;
$bad_input_message = ""; // Undefined variable creasysee
session_start();
  if(get_option('permalink_structure') != '')
  {
  $seperator ="?";
  }
  else
    {
    $seperator ="&";
    }
  if(isset($_POST['collected_data']) && isset($_POST['submitwpcheckout']) and ($_POST['submitwpcheckout'] == 'true'))
    {
    //exit("<pre>".print_r($_POST,true)."</pre>");
/*
Array
(
    [collected_data] => Array
        (
            [0] => igor.aleshin@gmail.com
            [1] => igor.aleshin@gmail.com
            [2] => igor.aleshin@gmail.com
        )

    [agree] => yes
    [payment_method] => wallet
    [submitwpcheckout] => true
    [submit] => Оплатить
)
*/



    $returnurl = "Location: ".get_option('checkout_url');
    if (isset($_GET['total']))
        $returnurl = "Location: ".get_option('checkout_url').$seperator."total=".$_GET['total'];
    $_SESSION['collected_data'] = $_POST['collected_data'];
    $any_bad_inputs = false;
    foreach($_POST['collected_data'] as $value_id => $value)
      {
	  //$value_id = $value_id + 1; // ales: somehow the index is wrong
      $form_sql = "SELECT * FROM `wp_collect_data_forms` WHERE `id` = '$value_id' LIMIT 1";
	  //echo $form_sql;
      $form_data = $wpdb->get_results($form_sql,ARRAY_A);
	  //echo("<pre>_POST['collected_data']".print_r($_POST['collected_data'],true)."</pre>");
	  //mail("igor.aleshin@gmail.com","_POSTcollected_data",print_r($_POST['collected_data'],true));
      $form_data = $form_data[0];
      $bad_input = false;
	  if($form_data['mandatory'] == 1)
        {        
        switch($form_data['type'])
          {
          case "email":
          if(!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-.]+\.[a-zA-Z]{2,5}$/",$value))
            {
            $any_bad_inputs = true;
            $bad_input = true;
            }

                break;
          
          default:
          if($value == null)
            {
            $any_bad_inputs = true;
            $bad_input = true;
            }
          break;
          }
        if($bad_input === true)
          {
          switch($form_data['name'])
            {
            case TXT_WPSC_FIRSTNAME:
            $bad_input_message .= "Пожалуйста, введите правильное имя<br>";
            break;
    
            case TXT_WPSC_LASTNAME:
            $bad_input_message .= "Пожалуйста, введите правильную фамилию<br>";
            break;
    
            case TXT_WPSC_EMAIL:
            $bad_input_message .= "Пожалуйста, введите правильный E-mail<br>";
            break;
    
            case TXT_WPSC_ADDRESS1:
            case TXT_WPSC_ADDRESS2:
            $bad_input_message .= TXT_WPSC_PLEASEENTERAVALIDADDRESS . "";
            break;
    
            case TXT_WPSC_CITY:
            $bad_input_message .= TXT_WPSC_PLEASEENTERAVALIDCITY . "";
            break;
    
            case TXT_WPSC_PHONE:
            $bad_input_message .= TXT_WPSC_PLEASEENTERAVALIDPHONENUMBER . "";
            break;
    
            case TXT_WPSC_COUNTRY:
            $bad_input_message .= TXT_WPSC_PLEASESELECTCOUNTRY . "";
            break;
    
            default:
            $bad_input_message .= TXT_WPSC_PLEASEENTERAVALID . " " . strtolower($form_data['name']) . ".";
            break;
            }
          }
        }
      }
    if($any_bad_inputs === true)
      {
      $_SESSION['nzshpcrt_checkouterr'] = nl2br($bad_input_message);
      header($returnurl);
      exit();     
      }
    $cart = $_SESSION['nzshpcrt_cart'];
    $_SESSION['checkoutdata'] = $_POST;
    if(isset($_POST['agree']) && $_POST['agree'] != 'yes')
      {
      $_SESSION['nzshpcrt_checkouterr'] = "Пожалуйста, ознакомьтесь с условиями лицензии и отметьте в квадратике";
      header($returnurl);
      exit();
      }
    
    if($cart == null)
      {
      $_SESSION['nzshpcrt_checkouterr'] = "Ваша корзина пуста";
      header($returnurl);
      exit();
      }
    $sessionid = (mt_rand(100,999).time());
    
    $sql = "INSERT INTO `wp_purchase_logs` (`id` , `totalprice` , `sessionid` , `firstname`, `lastname`, `email`, `date`, `shipping_country`,`address`, `phone` ) VALUES ('', '".$wpdb->escape($_SESSION['total'])."', '".$sessionid."', '".$wpdb->escape($_POST['collected_data']['1'])."', '".$wpdb->escape($_POST['collected_data']['2'])."', '".$_POST['collected_data']['3']."', '".time()."', '', '".$_POST['collected_data']['5']."', '".$_POST['collected_data']['4']."')";

   $wpdb->query($sql) ;
   
   $selectsql = "SELECT * FROM `wp_purchase_logs` WHERE `sessionid` LIKE '".$sessionid."' LIMIT 1";
   $getid = $wpdb->get_results($selectsql,ARRAY_A) ;
   foreach($_POST['collected_data'] as $value_id => $value)
    {
    $wpdb->query("INSERT INTO `wp_submited_form_data` ( `id` , `log_id` , `form_id` , `value` ) VALUES ('', '".$getid[0]['id']."', '".$value_id."', '".$value."');") ;
    }
   $downloads = get_option('max_downloads');
   foreach($cart as $cart_item)
     {
     $row = $cart_item->product_id;
     $quantity = $cart_item->quantity;
     $variations = $cart_item->product_variations;

     $product_data = $wpdb->get_results("SELECT * FROM `wp_product_list` WHERE `id` = '$row' LIMIT 1",ARRAY_A) ;
     $product_data = $product_data[0];
     if($product_data['file'] > 0)
       {
       $wpdb->query("INSERT INTO `wp_download_status` ( `id` , `fileid` , `purchid` , `downloads` , `active` , `datetime` ) VALUES ( '', '".$product_data['file']."', '".$getid[0]['id']."', '$downloads', '0', NOW( ));");
       }
      if($product_data['special']==1)
        {
        $price_modifier = $product_data['special_price'];
        }
        else
          {
          $price_modifier = 0;
          }
    
    $price = ($product_data['price'] - $price_modifier); 
    /*  
    if($product_list[0]['notax'] != 1)
      {
      $price = nzshpcrt_calculate_tax($price, $_SESSION['selected_country'], $_SESSION['selected_region']);
      if(get_option('base_country') == $_SESSION['selected_country'])
        {
        $country_data = $wpdb->get_row("SELECT * FROM `wp_currency_list` WHERE `isocode` IN('".get_option('base_country')."') LIMIT 1",ARRAY_A);
        if(($country_data['has_regions'] == 1))
          {
          if(get_option('base_region') == $_SESSION['selected_region'])
            {
            $region_data = $wpdb->get_row("SELECT `wp_region_tax`.* FROM `wp_region_tax` WHERE `wp_region_tax`.`country_id` IN('".$country_data['id']."') AND `wp_region_tax`.`id` IN('".get_option('base_region')."') ",ARRAY_A) ;
            }
          $gst =  $region_data['tax'];
          }
          else
            {
            $gst =  $country_data['tax'];
            }
          }
      }
      else { */
	  $gst = ''; 
	  /*}*/
        
            
    //$country = $wpdb->get_results("SELECT * FROM `wp_submited_form_data` WHERE `log_id`=".$getid[0]['id']." AND `form_id` = '".get_option('country_form_field')."' LIMIT 1",ARRAY_A);
    $country = '';//$country[0]['value'];
     
     $country_data = $wpdb->get_row("SELECT * FROM `wp_currency_list` WHERE `isocode` IN('".get_option('base_country')."') LIMIT 1",ARRAY_A);
     
     // $shipping = $base_shipping + ($additional_shipping * $quantity);
     $shipping = 0;
	 //$_SESSION['nzshpcrt_cart'][$cart_key]->license
/*
pokazh($getid,"getid");
pokazh($cart_item,"cart_item");	 
exit;

getid: Array
(
    [0] => Array
        (
            [id] => 166
            [totalprice] => 0
            [statusno] => 0
            [sessionid] => 5881285871982
            [transactid] => 
            [authcode] => 
            [firstname] => РќРµРєС‚Рѕ
            [lastname] => Р’ РЁР»СЏРїРµ
            [email] => aleshin@dataart.com
            [address] => bb
            [phone] => aa
            [downloadid] => 0
            [processed] => 1
            [date] => 1285871982
            [gateway] => 
            [shipping_country] => 
            [shipping_region] => 
        )

)
cart_item: cart_item Object
(
    [product_id] => 3310
    [product_variations] => 
    [quantity] => 1
    [name] => 111
    [price] => 0
    [license] => l1_price
    [author] => a1
)
*/
	$license_num = $getid[0]['sessionid']."_".$cart_item->product_id;
	$cartsql = "INSERT INTO `wp_cart_contents` ( `id` , `prodid` , `purchaseid`, `price`, `pnp`, `gst`, `quantity`, `license` ) VALUES ('', '".$row."', '".$getid[0]['id']."','".$price."','".$shipping."', '".$gst."','".$quantity."', '".$license_num."')";
     $wpdb->query($cartsql);

	 
	 $cart_id = $wpdb->get_results("SELECT LAST_INSERT_ID() AS `id` FROM `wp_product_variations` LIMIT 1",ARRAY_A);
     $cart_id = $cart_id[0]['id'];
     if($variations != null)
       {
       foreach($variations as $variation => $value)
         {
         $wpdb->query("INSERT INTO `wp_cart_item_variations` ( `id` , `cart_id` , `variation_id` , `venue_id` ) VALUES ( '', '".$cart_id."', '".$variation."', '".$value."' );");
         }
       }
     }

	if (isset($_POST['payment_method']))
		{
			$curgateway = $_POST['payment_method'];
		}
		else
		{$curgateway = '';}
   
  if(get_option('permalink_structure') != '')
    {
    $seperator ="?";
    }
    else
      {
      $seperator ="&";
      }

      
      foreach($nzshpcrt_gateways as $gateway)
        {
        if($gateway['internalname'] == $curgateway )
          {
          $gateway_used = $gateway['internalname'];
          $wpdb->query("UPDATE `wp_purchase_logs` SET `gateway` = '".$gateway_used."' WHERE `id` = '".$getid[0]['id']."' LIMIT 1 ;");
          $gateway['function']($seperator, $sessionid);
          }
        }
      
    //require_once("merchants.php");
    } //end of: if(isset($_POST['collected_data']) && isset($_POST['submitwpcheckout']) and ($_POST['submitwpcheckout'] == 'true'))
    else if(isset($_GET['termsandconds']) and $_GET['termsandconds'] === 'true')
      {
      echo stripslashes(get_option('terms_and_conditions'));
      exit();
      }
  }
  
function nzshpcrt_shopping_basket($input = null)
  {
  global $wpdb;
  $dont_add_input = null;
  if(get_option('cart_location') == 1)
    {
    if($input != '')
      {
      $cart = $_SESSION['nzshpcrt_cart'];
      echo "<div id='sideshoppingcart'><div id='shoppingcartcontents'>";
      echo nzshpcrt_shopping_basket_internals($cart);
      echo "</div></div>";
      }
    }
    else if((get_option('cart_location') == 3) || (get_option('cart_location') == 4))
      {
        if (isset ($_SESSION['nzshpcrt_cart']))
            {
                $cart = $_SESSION['nzshpcrt_cart'];
              }
            else
            {
                $cart = null;
            }
      if(get_option('cart_location') == 4)
        {
        echo $input;
        echo "<div id='widgetshoppingcart'><div id='shoppingcartcontents'>";
        echo nzshpcrt_shopping_basket_internals($cart);
        echo "</div></div>";
        if(get_option('display_specials') == 1)
          {
          nzshpcrt_specials();
          }
        $dont_add_input = true;
        }
        else
          {
          echo "<div id='sideshoppingcart'><div id='shoppingcartcontents'>";
          $basket = nzshpcrt_shopping_basket_internals($cart);
          echo $basket;
          echo "</div></div>";
          if(get_option('display_specials') == 1)
            {
            nzshpcrt_specials();
            }
          }
      }
      else
        {
        if(($GLOBALS['nzshpcrt_activateshpcrt'] === true))
          {
          $cart = $_SESSION['nzshpcrt_cart'];
          echo "<div id='shoppingcart'><div id='shoppingcartcontents'>";
          echo nzshpcrt_shopping_basket_internals($cart);
          echo "</div></div>";
          }
        }
  
  if($dont_add_input !== true)
    {
    if($input != '')
      {
      echo $input;
      }
    }
  }

 function nzshpcrt_specials($input = null)
   {
   global $wpdb;
   $siteurl = get_option('siteurl');
   $sql = "SELECT * FROM `wp_product_list` WHERE `special` = '1'  LIMIT 1";
   $product = $wpdb->get_results($sql,ARRAY_A) ;
   if($product != null)
     {
     $output = "<div id='sideshoppingcart'><div id='shoppingcartcontents'><h2>".TXT_WPSC_SPECIALS."</h2><br \>";
     foreach($product as $special)
       {
       $output .= "<strong>".$special['name']."</strong><br /> ";
       if($special['image'] != null)
         {
        $output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/images/".$special['image']."' title='".$special['name']."' alt='".$special['name']."' /><br />";
        }
       $output .= $special['description']."<br />";
       $output .= "<span class='oldprice'>".nzshpcrt_currency_display($special['price'], $special['notax'],false)."</span><br />";
//       $output .= $special['price'];

       $variations_procesor = new nzshpcrt_variations;
       $output .= $variations_procesor->display_product_variations($product['id']);
       $output .= nzshpcrt_currency_display($special['price'], $special['notax'],false,$special['id'])."<br />";
       $output .= "<form id='specials' name='$num' method='POST' action='#' onsubmit='submitform(this);return false;' >";
       $output .= "<input type='hidden' name='prodid' value='".$special['id']."'>";
       $output .= "<input type='hidden' name='item' value='".$special['id']."' />";
       
       
    //    $output .= "<input type='submit' name='Buy' value='".TXT_WPSC_BUY."'  />";
       
       if(($special['quantity_limited'] == 1) && ($special['quantity'] < 1))
         {
         $output .= TXT_WPSC_PRODUCTSOLDOUT."";
         }
         else
           {
           $output .= $variations_procesor->display_product_variations($special['id'],true);
           $output .= "<input type='submit' name='".TXT_WPSC_ADDTOCART."' value='".TXT_WPSC_ADDTOCART."'  />";
           }
       $output .= "</form>";
       }
     $output .= "</div></div>";
     }
     else
       {
       $output = '';
       }
   echo $input.$output;
   }

function nzshpcrt_shopping_basket_internals($cart,$quantity_limit = false, $title='')
  {

  global $wpdb;
  //global $current_user;
  $output = '';
  $current_url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
  switch(get_option('cart_location'))
    {
    case 1:
    $output .= "<h2>Корзина заказов</h2>&nbsp;<img src='".get_option('siteurl')."/img/cart.gif'>";
    $output .="<span id='alt_loadingindicator'><img id='alt_loadingimage' src='". get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/indicator.gif' alt='Loading' title='Loading' /> Идёт загрузка...</span></strong><br />";
    $spacing = "<br/>";
    break;
    
    case 3:
    $output .= "<strong>Корзина заказов</strong>&nbsp;<img src='".get_option('siteurl')."/img/cart.gif'>";
    $spacing = "<br/>";
    break;
    
    case 4:
    $spacing = "";
    break;
    
    default:
    $output .= "<strong>Корзина заказов</strong>&nbsp;<img src='".get_option('siteurl')."/img/cart.gif'>";
    $spacing = "<br/>";
    break;
    }  
  
  $current_user = wp_get_current_user();    //print_r($current_user); exit(); 
  $_wallet = $current_user->wallet;
  



  if($cart != null)
    {
    $output .= $spacing;
    if($quantity_limit == true)
      {
      $output .= TXT_WPSC_NUMBEROFITEMS.": &nbsp;&nbsp;".count($cart)."<br /><br />";
      $output .= TXT_WPSC_NOMOREAVAILABLE."<br /><br />";
      }
      else
        {
        $output .= TXT_WPSC_NUMBEROFITEMS.": &nbsp;&nbsp;".count($cart)."<br /><br />";
        }

// Shopping cart items content starts here

    $output .= "<table class='shoppingcart' width='100%'>";
    $output .= "<tr><td style='border-bottom: 1px solid #FF9966'>№</td><td style='border-bottom: 1px solid #FF9966'>Название</td><td  style='border-bottom: 1px solid #FF9966' align='right'>Цена</td></tr>"; 
    $total = 0;

		
	foreach($cart as $cart_item)
      {
      $product_id = $cart_item->product_id;
				//ales: hack to have just one item of each pic
				//$quantity = $cart_item->quantity;
				$quantity = 1;
      $sql = "SELECT * FROM `wp_product_list` WHERE `id` = '$product_id' LIMIT 1";
      $product = $wpdb->get_results($sql,ARRAY_A);

	  $cart_item->name = $product[0]['name'];

      $price_modifier = 0; // for compatibility



		if (isset($_POST['license']) && ($cart_item->product_id == $_POST['prodid']) && !isset($_POST['Buy']))
		  {
			switch($_POST['license'])
					{
					case 'l1_price':
					$price = $product[0]['l1_price'];
					$cart_item->price = $price;
					$cart_item->license = 'l1_price';
					break;
					
					case 'l2_price':
					$price = $product[0]['l2_price'];
					$cart_item->price = $price;
					$cart_item->license = 'l2_price';
					break;
					
					case 'l3_price':
					$price = $product[0]['l3_price'];
					$cart_item->price = $price;
					$cart_item->license = 'l3_price';
					break;
					
					default:
					$cart_item->price = $product[0]['l1_price'];
					$cart_item->license = 'l1_price';
					break;
					}
		  } 
		  else
		  {
			  //if (isset($_POST['prodid']) && !isset($_POST['license']) && ($cart_item->product_id == $_POST['prodid']) && isset($_POST['Buy']))
			  if (isset($_POST['prodid']) && ($cart_item->product_id == $_POST['prodid']))
			  {
					$price = $product[0]['l1_price'];
					$cart_item->price = $price;
					$cart_item->license = 'l1_price';
			  }
		  }

	  $total += round($cart_item->price);
      $output .= "<tr><td>".$product[0]['id']."</td><td>".stripslashes($product[0]['name'])."</td><td align='right'>".round($cart_item->price)."</td></tr>";
      }

    $output .= "<tr><td>&nbsp;</td><td style='border-top: 1px solid #FF9966'>Итого: </td><td align='right' style='border-top: 1px solid #FF9966'><b>".round($total)."</b></td></tr>";
    $output .= "</table>";

$_SESSION['total'] = $total;

	$output .= "На Личном счёте <b>".round($_wallet)."</b> р.<br>";

if ($total > $_wallet)
	$output .= "<div style='color:#CC0000;'>Не хватает средств для покупки выбранных изображений.</div>";

    if(get_option('permalink_structure') != '')
      {
      $seperator ="?";
      }
      else
         {
         $seperator ="&amp;";
         }
	$output .= "<a class='button' href='".get_option('product_list_url')."?cart=empty' onclick='emptycart();return false;'>x Очистить корзину</a><br />";

global $user_identity;
if ($user_identity == '')
{
	$output .= "Доступ к корзине возможен только после входа";
}
else
{
	if (isset($_GET['page_id']) && ($_GET['page_id']==30 || $_GET['page_id']==31))
	{$output .= "";}
	else
	{$output .= "<a class='button' href='".get_option('shopping_cart_url')."'>Оплатить и скачать</a><br />";}
}
	}
    else
      {
      $output .= $spacing;
      //$output .= "Корзина заказов пуста.<br />";
      $output .= "На Личном счёте <b>".round($_wallet)."</b> р.<br>";
      }

  return $output;
  }

function nzshpcrt_download_file()
  {
  global $wpdb,$user_level,$wp_rewrite;
  get_currentuserinfo();
  if(isset($_GET['downloadid']) and is_numeric($_GET['downloadid']))
    {
    $id = $_GET['downloadid'];
    $download_data = $wpdb->get_results("SELECT * FROM `wp_download_status` WHERE `id`='".$id."' AND `downloads` > '0' AND `active`='1' LIMIT 1",ARRAY_A) ;
    $download_data = $download_data[0];
    if($download_data != null)
      {
      $file_data = $wpdb->get_results("SELECT * FROM `wp_product_files` WHERE `id`='".$download_data['fileid']."' LIMIT 1",ARRAY_A) ;
      $file_data = $file_data[0];
      $wpdb->query("UPDATE `wp_download_status` SET `downloads` = '".($download_data['downloads']-1)."' WHERE `id` = '$id' LIMIT 1");
      $basepath =  getcwd();
      $filedir = $basepath."/wp-content/plugins/wp-shopping-cart/files/";
      
	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Type: '.$file_data['mimetype']);      
	header('Content-Disposition: attachment; filename="'.$file_data['filename'].'"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: '.filesize($filedir.$file_data['idhash']));
    ob_clean();
    flush();

      readfile($filedir.$file_data['idhash']);
      exit();
      }
    }
    else
      {
      if(isset($_GET['admin_preview']) and ($_GET['admin_preview'] == "true") && is_numeric($_GET['product_id']))
        {
        $product_id = $_GET['product_id'];
        $product_data = $wpdb->get_results("SELECT * FROM `wp_product_list` WHERE `id` = '$product_id' LIMIT 1",ARRAY_A);

        if(is_numeric($product_data[0]['file']) && ($product_data[0]['file'] > 0))
          {
          $file_data = $wpdb->get_results("SELECT * FROM `wp_product_files` WHERE `id`='".$product_data[0]['file']."' LIMIT 1",ARRAY_A) ;
          $file_data = $file_data[0];
          $basepath =  str_replace('/wp-admin','',getcwd());
          $filedir = $basepath."/wp-content/plugins/wp-shopping-cart/files/";

/*
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
*/

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');


          header('Content-Type: '.$file_data['mimetype']);      
          //header('Content-Length: '.filesize($filedir.$file_data['idhash']));
          if($_GET['preview_track'] != 'true')
            {
            header('Content-Disposition: attachment; filename="'.$file_data['filename'].'"');
            }
            else
            {
            header('Content-Disposition: inline; filename="'.$file_data['filename'].'"');
            }

    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');

    ob_clean();
    flush();

          readfile($filedir.$file_data['idhash']);
          exit();
          }
        }
      }
  }

function nzshpcrt_display_preview_image()
  {
  global $wpdb;
  if(isset($_GET['productid']) and is_numeric($_GET['productid']))
    {
     if(function_exists("getimagesize"))
      {
      $basepath =  str_replace("/wp-admin", "" , getcwd());
      $imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/product_images/";

      $imagesql = "SELECT `image` FROM `wp_product_list` WHERE `id`='".$_GET['productid']."' LIMIT 1";
      $imagedata = $wpdb->get_results($imagesql,ARRAY_A);

      $imagepath = $imagedir . $imagedata[0]['image'];
      
      $image_size = @getimagesize($imagepath);
      if(is_numeric($_GET['height']) && is_numeric($_GET['width']))
        {
        $height = $_GET['height'];
        $width = $_GET['width'];
        }
        else
          {
          $width = $image_size[0];
          $height = $image_size[1];
          }
      if(($height > 0) && ($height <= 1024) && ($width > 0) && ($width <= 1024))
       {
       include("image_preview.php");
       }
       else
         {
         $width = $image_size[0];
         $height = $image_size[1];
         include("image_preview.php");
         }
      }
    }
  }
  
  
function nzshpcrt_listdir($dirname)
    {
    /*
    lists the merchant directory
    */
     $dir = @opendir($dirname);
     $num = 0;
     while(($file = @readdir($dir)) !== false)
       {
       //filter out the dots, macintosh hidden files and any backup files
       if(($file != "..") && ($file != ".") && ($file != ".DS_Store") && !stristr($file, "~"))
         {
         $dirlist[$num] = $file;
         $num++;
         }
       }
    if($dirlist == null)
      {
      $dirlist[0] = "paypal.php";
      $dirlist[1] = "testmode.php";
      }
    return $dirlist; 
    }
    
    

function nzshpcrt_product_rating($prodid)
      {
      global $wpdb;
      $get_average = $wpdb->get_results("SELECT AVG(`rated`) AS `average`, COUNT(*) AS `count` FROM `wp_product_rating` WHERE `productid`='".$prodid."'",ARRAY_A);
      $average = floor($get_average[0]['average']);
      $count = $get_average[0]['count'];
      $output .= "  <span class='votetext'>";
      for($l=1; $l<=$average; ++$l)
        {
        $output .= "<img class='goldstar' src='". get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/gold-star.gif' alt='$l' title='$l' />";
        }
      $remainder = 5 - $average;
      for($l=1; $l<=$remainder; ++$l)
        {
        $output .= "<img class='goldstar' src='". get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/grey-star.gif' alt='$l' title='$l' />";
        }
      $output .=  "<span class='vote_total'>&nbsp;(<span id='vote_total_$prodid'>".$count."</span>)</span> \r\n";
      $output .=  "</span> \r\n";
      return $output;
      }

// this appears to have some star rating code in it
function nzshpcrt_product_vote($prodid, $starcontainer_attributes = '')
      {
      global $wpdb;
      $output = null;
      $useragent = $_SERVER['HTTP_USER_AGENT'];
      $visibility = "style='display: none;'";
      
      preg_match("/(?<=Mozilla\/)[\d]*\.[\d]*/", $useragent,$rawmozversion );
      $mozversion = $rawmozversion[0];
      if(stristr($useragent,"opera"))
        {
        $firstregexp = "Opera[\s\/]{1}\d\.[\d]+";
        }
        else
          {
          $firstregexp = "MSIE\s\d\.\d";
          }
      preg_match("/$firstregexp|Firefox\/\d\.\d\.\d|Netscape\/\d\.\d\.\d|Safari\/[\d\.]+/", $useragent,$rawbrowserinfo);
      $browserinfo = preg_split("/[\/\s]{1}/",$rawbrowserinfo[0]);
      $browsername = $browserinfo[0];
      $browserversion = $browserinfo[1];  
      
      //exit($browsername . " " . $browserversion);
       
      if(($browsername == 'MSIE') && ($browserversion < 7.0))
        {
        $starimg = ''. get_option('siteurl').'/wp-content/plugins/wp-shopping-cart/images/star.gif';
        $ie_javascript_hack = "onmouseover='ie_rating_rollover(this.id,1)' onmouseout='ie_rating_rollover(this.id,0)'";
        }
        else 
          {
          $starimg = ''. get_option('siteurl').'/wp-content/plugins/wp-shopping-cart/images/24bit-star.png';
          $ie_javascript_hack = '';
          }
       
      $cookie_data = explode(",",$_COOKIE['voting_cookie'][$prodid]);
       
      if(is_numeric($cookie_data[0]))
        {
        $vote_id = $cookie_data[0];
        }
      
      $chkrate = $wpdb->get_results("SELECT * FROM `wp_product_rating` WHERE `id`='".$vote_id."' LIMIT 1",ARRAY_A);

      if($chkrate[0]['rated'] > 0)
        {
        $rating = $chkrate[0]['rated'];
        $type = 'voted';
        }
        else
          {
          $rating = 0;
          $type = 'voting';
          }

      $output .=  "<div id='starcontainer' $starcontainer_attributes >\r\n";
      for($k=1; $k<=5; ++$k)
        {
        $style = '';
        if($k <= $rating)
          {
          $style = "style='background: url(". get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/gold-star.gif)'";
          }
        $output .= "      <a name='' id='".$prodid."and".$k."_link' onclick='rate_item(".$prodid.",".$k.")' class='star$k' $style $ie_javascript_hack ><img id='".$prodid."and".$k."' class='starimage' src='$starimg' alt='$k' title='$k' /></a>\r\n";
        }
      $output .=  "   </div>\r\n";
      $output .= "";
      $voted = TXT_WPSC_CLICKSTARSTORATE;
      
      switch($ratecount[0]['count'])
        {
        case 0:
        $votestr = TXT_WPSC_NOVOTES;
        break;
        
        case 1:
        $votestr = TXT_WPSC_1VOTE;
        break;
        
        default:
        $votestr = $ratecount[0]['count']." ".TXT_WPSC_VOTES2;
        break;
        }
        
      for($i= 5; $i>= 1; --$i)
         {
        //$tmpcount = $this->db->GetAll("SELECT COUNT(*) AS 'count' FROM `pxtrated` WHERE `pxtid`=".$dbdat['rID']." AND `rated`=$i");
            
         switch($tmpcount[0]['count'])
           {
           case 0:
           $othervotes .= "";
           break;
           
           case 1:
           $othervotes .= "<br />". $tmpcount[0]['count'] . " ".TXT_WPSC_PERSONGIVEN." $i ".TXT_WPSC_PERSONGIVEN2;
           break;
           
           default:
           $othervotes .= "<br />". $tmpcount[0]['count'] . " ".TXT_WPSC_PEOPLEGIVEN." $i ".TXT_WPSC_PEOPLEGIVEN2;
           break;
           }  
         } /*
      $output .=  "</td><td class='centerer2'>&nbsp;</td></tr>\r\n";
      $output .= "<tr><td colspan='3' class='votes' >\r\n";//id='startxtmove'
      $output .= "   <p class='votes'> ".$votestr."<br />$voted <br />
      $othervotes</p>";*/
      
      return Array($output,$type);
      } //*/
  
  function nzshpcrt_country_list($selected_country = null)
    {
    global $wpdb;
    $output = "";
    $output .= "<option value=''></option>";
    $country_data = $wpdb->get_results("SELECT * FROM `wp_currency_list` ORDER BY `country` ASC",ARRAY_A);
    foreach ($country_data as $country)
      {
      $selected ='';
      if($selected_country == $country['isocode'])
        {
        $selected = "selected='true'";
        }
      $output .= "<option value='".$country['isocode']."' $selected>".$country['country']."</option>";
      }
    return $output;
    }
  
  function nzshpcrt_region_list($selected_country = null, $selected_region = null)
    {
    global $wpdb;
    if($selected_region == null)
      {
      $selected_region = get_option('base_region');
      }
    $output = "";
    $region_list = $wpdb->get_results("SELECT `wp_region_tax`.* FROM `wp_region_tax`, `wp_currency_list`  WHERE `wp_currency_list`.`isocode` IN('".$selected_country."') AND `wp_currency_list`.`id` = `wp_region_tax`.`country_id`",ARRAY_A) ;
    if($region_list != null)
      {
      $output .= "<select name='base_region'>\n\r";
      $output .= "<option value=''>None</option>";
      foreach($region_list as $region)
        {
        if($selected_region == $region['id'])
          {
          $selected = "selected='true'";
          }
          else
            {
            $selected = "";
            }
        $output .= "<option value='".$region['id']."' $selected>".$region['name']."</option>\n\r";
        }
      $output .= "</select>\n\r";    
      }
      else
        {
        $output .= "<select name='base_region' disabled='true'><option value=''>None</option></select>\n\r";
        }
    return $output;
    }
    
  function nzshpcrt_form_field_list($selected_field = null)
    {
    global $wpdb;
    $output = "";
    $output .= "<option value=''>Please choose</option>";
    $form_sql = "SELECT * FROM `wp_collect_data_forms` WHERE `active` = '1';";
    $form_data = $wpdb->get_results($form_sql,ARRAY_A);
    foreach ($form_data as $form)
      {
      $selected ='';
      if($selected_field == $form['id'])
        {
        $selected = "selected='true'";
        }
      $output .= "<option value='".$form['id']."' $selected>".$form['name']."</option>";
      }
    return $output;
    }

 function get_country($country_code)  
  {
  global $wpdb;
  $country_data = $wpdb->get_results("SELECT * FROM `wp_currency_list` WHERE `isocode` IN ('".$country_code."') LIMIT 1",ARRAY_A);
  return $country_data[0]['country']; 
  }
  
function get_brand($brand_id)  
  {
  global $wpdb;
  $brand_data = $wpdb->get_results("SELECT `name` FROM `wp_product_brands` WHERE `id` IN ('".$brand_id."') LIMIT 1",ARRAY_A);
	if (isset($brand_data[0]['name']))
	  {return $brand_data[0]['name'];}
	else
	  {return '';}
  }


function filter_input_wp($input)
  {
  // if the input is numeric, then its probably safe
  if(is_numeric($input))
    {
    $output = $input;
    }
    else
      {
      // if its not numeric, then make it safe
      if(!get_magic_quotes_gpc())
        {
        $output = mysql_real_escape_string($input);
        }
        else
          {
          $output = mysql_real_escape_string(stripslashes($input));
          }
      }
    return $output;
    }
    
function make_csv($array)
  {
  $count = count($array);
  $num = 1;
  foreach($array as $value)
    {
    $output = "'$value'";
    if($num < $count)
      {
      $output .= ",";
      }
    $num++;
    }
  return $output;
  }   
  
function nzshpcrt_product_log_rss_feed()
  {
  echo "<link type='application/rss+xml' href='".get_option('siteurl')."/index.php?rss=true&rss_key=key&action=purchase_log&type=rss' title='WP E-Commerce Purchase Log RSS' rel='alternate'/>";
  }
  
function nzshpcrt_product_list_rss_feed()
  {
  echo "<link rel='alternate' type='application/rss+xml' title='WP E-Commerce Product List RSS' href='".get_option('siteurl')."/index.php?rss=true&action=product_list&type=rss'/>";
  }
    
require_once('processing_functions.php');
require_once('product_display_functions.php');

/* 
 * This plugin gets the merchants from the merchants directory and
 * needs to search the merchants directory for merchants, the code to do this starts here
 */
// $gateway_basepath =  str_replace("/wp-admin", "" , getcwd());
// $gateway_directory = $gateway_basepath."/wp-content/plugins/wp-shopping-cart/merchants/";
$gateway_directory = ABSPATH . 'wp-content/plugins/wp-shopping-cart/merchants';
$nzshpcrt_merchant_list = nzshpcrt_listdir($gateway_directory);
$num=0;
foreach($nzshpcrt_merchant_list as $nzshpcrt_merchant)
  {
    if (strcmp($nzshpcrt_merchant,'.svn') && strcmp($nzshpcrt_merchant,'library'))
    {
      require("merchants/".$nzshpcrt_merchant);
      $num++;
    } 
  }
/* 
 * and ends here
 */
if(get_option('cart_location') == 4)
  {
  require_once('shopping_cart_widget.php');
  }
  
$nzshpcrt_basepath =  str_replace("/wp-admin", "" , getcwd());
$nzshpcrt_basepath = $nzshpcrt_basepath."/wp-content/plugins/wp-shopping-cart/";
if(file_exists($nzshpcrt_basepath.'ext_shopping_cart.php'))
  {
  require_once('ext_shopping_cart.php');
  }
require_once("currency_converter.inc.php"); 
require_once("form_display_functions.php"); 
require_once("homepage_products_functions.php"); 

if (isset($_GET['activate']) && $_GET['activate'] == 'true')
   {
   add_action('init', 'nzshpcrt_install');
   }
   
   
add_filter('the_content', 'nzshpcrt_products_page');
add_filter('the_content', 'nzshpcrt_shopping_cart');
add_filter('the_content', 'nzshpcrt_transaction_results');
add_filter('the_content', 'nzshpcrt_checkout');
add_filter('the_content', 'nszhpcrt_homepage_products');

//add_filter('wp_list_pages', 'nzshpcrt_hidepages');
 
add_action('wp_head', 'nzshpcrt_style');

add_action('admin_head', 'nzshpcrt_css');
if(isset($_GET['page']) and $_GET['page'] == "wp-shopping-cart/display-log.php")
  {
  add_action('admin_head', 'nzshpcrt_product_log_rss_feed');
  }
add_action('wp_head', 'nzshpcrt_javascript');
add_action('wp_head', 'nzshpcrt_product_list_rss_feed');

add_action('init', 'nzshpcrt_submit_checkout');
add_action('init', 'nzshpcrt_submit_ajax');
add_action('init', 'nzshpcrt_download_file');
add_action('init', 'nzshpcrt_display_preview_image');


//this adds all the admin pages, before the code was a mess, now it is slightly less so.
add_action('admin_menu', 'nzshpcrt_displaypages');

switch(get_option('cart_location'))
  {
  case 1:
  add_action('wp_list_pages','nzshpcrt_shopping_basket');
  break;
  
  case 2:
  add_action('the_content', 'nzshpcrt_shopping_basket');
  break;
  
  case 4:
  add_action('plugins_loaded', 'widget_wp_shopping_cart_init');
  break;
  
  case 3:
  //add_action('the_content', 'nzshpcrt_shopping_basket');
  //<?php nzshpcrt_shopping_basket(); ?/>   
  break;
  
  default:
  add_action('the_content', 'nzshpcrt_shopping_basket');
  break;
  }

function rus2uni($str,$isTo = true)
    {
        $arr = array('?'=>'&#x451;','?'=>'&#x401;');
        for($i=192;$i<256;$i++)
            $arr[chr($i)] = '&#x4'.dechex($i-176).';';
        $str =preg_replace(array('@([?-?]) @i','@ ([?-?])@i'),array('$1&#x0a0;','&#x0a0;$1'),$str);
        return strtr($str,$isTo?$arr:array_flip($arr));
    }

function Utf8ToWin($fcontents) {

    $out = $c1 = '';

    $byte2 = false;

    for ($c = 0;$c < strlen($fcontents);$c++) {

        $i = ord($fcontents[$c]);

        if ($i <= 127) {

            $out .= $fcontents[$c];

        }

        if ($byte2) {

            $new_c2 = ($c1 & 3) * 64 + ($i & 63);

            $new_c1 = ($c1 >> 2) & 5;

            $new_i = $new_c1 * 256 + $new_c2;

            if ($new_i == 1025) {

                $out_i = 168;

            } else {

                if ($new_i == 1105) {

                    $out_i = 184;

                } else {

                    $out_i = $new_i - 848;

                }

            }

            $out .= chr($out_i);

            $byte2 = false;

        }

        if (($i >> 5) == 6) {

            $c1 = $i;

            $byte2 = true;

        }

    }

    return $out;

}
  
/*
 * This serializes the shopping cart variable as a backup in case the unserialized one gets butchered by various things
 */  
function serialize_shopping_cart()
  {
      if (isset($_SESSION['nzshpcrt_cart']))
      {
          $_SESSION['nzshpcrt_serialized_cart'] = serialize($_SESSION['nzshpcrt_cart']);
      }
  return true;
  }  
register_shutdown_function("serialize_shopping_cart");
?>
