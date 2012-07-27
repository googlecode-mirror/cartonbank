<?php
/*
Plugin Name:WP Shopping Cart
Plugin URI: http://www.instinct.co.nz
Description: A plugin that provides a WordPress Shopping Cart. Contact <a href='http://www.instinct.co.nz/?p=16#support'>Instinct Entertainment</a> for support.
Version: 3.4.6 beta
Author: Thomas Howard of Instinct Entertainment
Author URI: http://www.instinct.co.nz
*/

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
	  global $user_level;
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
        add_menu_page('Магазин', 'Магазин', 8, $base_page);
        add_submenu_page($base_page,'Параметры магазина', 'Параметры магазина', 8, 'wp-shopping-cart/options.php');
        }
        else
          {
          $base_page = 'wp-shopping-cart/display-users.php';
          add_menu_page('Магазин', 'Магазин', 8, $base_page);
		  add_submenu_page($base_page,'Юзеры', 'Юзеры', 8, 'wp-shopping-cart/display-users.php');
          //add_submenu_page('wp-shopping-cart/display-log.php','Лог заказов', 'Лог заказов', 8, 'wp-shopping-cart/display-log.php');
          }
      add_submenu_page($base_page,'Все продажи', 'Все продажи', 8, 'wp-shopping-cart/display-sales.php');
	  add_submenu_page($base_page,'Бухгалтеру', 'Бухгалтеру', 8, 'wp-shopping-cart/display-account.php');
	  add_submenu_page($base_page,'Покупатели', 'Покупатели', 8, 'wp-shopping-cart/display-clients.php');
	  add_submenu_page($base_page,'Счета', 'Счета', 8, 'wp-shopping-cart/display-invoices.php');
	  add_submenu_page($base_page,'Авторские акты', 'Авторские акты', 8, 'wp-shopping-cart/display-author_payments.php');
  	  add_submenu_page($base_page,'Авторские выплаты', 'Авторские выплаты', 8, 'wp-shopping-cart/author_payments_complete.php');
  	  add_submenu_page($base_page,'Касса', 'Заработано', 8, 'wp-shopping-cart/display_artist_income.php');

      
      add_submenu_page($base_page,'Каталог', 'Каталог', 8, 'wp-shopping-cart/display-items.php');
      //add_submenu_page($base_page,'Категории', 'Категории', 8, 'wp-shopping-cart/display-category.php');
      add_submenu_page($base_page,'Авторы', 'Авторы', 8, 'wp-shopping-cart/display-brands.php');
      
      //add_submenu_page($base_page,'Варьирование', 'Варьирование', 8, 'wp-shopping-cart/display_variations.php');
      //add_submenu_page($base_page,'Параметры доступа', 'Параметры доступа', 8, 'wp-shopping-cart/gatewayoptions.php');
      if(get_option('nzshpcrt_first_load') != 0)
        {
        add_submenu_page($base_page,'Параметры магазина', 'Параметры магазина', 8, 'wp-shopping-cart/options.php');
        }
      if(function_exists('ext_shpcrt_options'))
        {
        ext_shpcrt_options($base_page);
        }
      //add_submenu_page($base_page,'Checkout Options', 'Checkout Options', 8, 'wp-shopping-cart/form_fields.php');
      //add_submenu_page($base_page,'Помощь/Обновить', 'Помощь/Обновить', 8, 'wp-shopping-cart/instructions.php');
      }
    return;
    }
     
  function products_page()
    {
    ob_start();
    require_once("products_page.php");
    //nzshpcrt_shopping_basket();
	echo "<p>"; // hack to remove closing </p>
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
  <?php
    }
    
function nzshpcrt_javascript()
    {
$siteurl = get_option('siteurl');
?><script src="<?php echo $siteurl; ?>/wp-content/plugins/wp-shopping-cart/ajax.js"></script>
<script src="<?php echo $siteurl; ?>/wp-content/plugins/wp-shopping-cart/user.js"></script>
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
      $seperator ="&";
      }
   
  /* update shopping cart*/    
  if(isset($_GET['ajax']) and ($_GET['ajax'] == "true") && ($_GET['user'] == "true") && is_numeric($_POST['prodid']))
    {
    $sql = "SELECT * FROM `wp_product_list` WHERE `id`='".$_POST['prodid']."' LIMIT 1";
    $item_data = $wpdb->get_results($sql,ARRAY_A) ;
    
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
			
			if (isset($_POST['license']))
				{$_SESSION['nzshpcrt_cart'][$cart_key]->license = $_POST['license'];}
			else
				{$_SESSION['nzshpcrt_cart'][$cart_key]->license = 'l1_price';}

			if (isset($brand_id))
			{
				$_SESSION['nzshpcrt_cart'][$cart_key]->author = get_brand($brand_id);
			}

			if($_SESSION['nzshpcrt_cart'][$cart_key]->product_variations === $variations) 
			  {
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
      $_SESSION['nzshpcrt_cart'] = Array();
	  $cart = $_SESSION['nzshpcrt_cart'];
      echo nzshpcrt_shopping_basket_internals($cart);
      exit();
      }

  /* fill product form */    
  if(isset($_POST['ajax']) and ($_POST['ajax'] == "true") && ($_POST['admin'] == "true") && isset($_POST['prodid']) && is_numeric($_POST['prodid']))
    {
	  if ($_POST['prodid']=='000')
		{echo ("<h3>Нет картинки с таким номером</h3>");
		  exit;}
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
       
  if(isset($_GET['rss']) and ($_GET['rss'] == "true") && ($_GET['action'] == "product_list"))
    {
    $sql = "SELECT id, name, description, image FROM `wp_product_list` WHERE active=1 and approved=1 and visible=1 Order by id DESC LIMIT 40";
    $product_list = $wpdb->get_results($sql,ARRAY_A);
    header("Content-Type: application/xml; charset=utf-8"); 
    header('Content-Disposition: inline; filename="cartoonbank.rss"');
    $output = '';
    $output .= "<?xml version='1.0'?>\n\r";
    $output .= "<rss version='2.0'>\n\r";

	$output .= "xmlns:content='http://purl.org/rss/1.0/modules/content/'\n\r";
	$output .= "xmlns:wfw='http://wellformedweb.org/CommentAPI/'\n\r";
	$output .= "xmlns:dc='http://purl.org/dc/elements/1.1/'\n\r";
	$output .= "xmlns:atom='http://www.w3.org/2005/Atom'\n\r";
	$output .= "xmlns:sy='http://purl.org/rss/1.0/modules/syndication/'\n\r";
	$output .= "xmlns:slash='http://purl.org/rss/1.0/modules/slash/'\n\r";
	$output .= "xmlns:georss='http://www.georss.org/georss' xmlns:geo='http://www.w3.org/2003/01/geo/wgs84_pos#' xmlns:media='http://search.yahoo.com/mrss/'>\n\r";

    $output .= "  <channel>\n\r";
    $output .= "    <title>Cartoonbank new images</title>\n\r";
    $output .= "    <link>http://cartoonbank.ru/</link>\n\r";
    $output .= "    <description>This is the Russian Cartoon Bank RSS feed</description>\n\r";
    $output .= "    <generator>Cartoonbank.ru</generator>\n\r";
    foreach($product_list as $product)
      {
      $purchase_link = get_option('product_list_url')."&cartoonid=".stripslashes($product['id']);
      $output .= "    <item>\n\r";
      $output .= "      <title>".stripslashes($product['name'])."</title>\n\r";
	  $output .= "      <link>http://cartoonbank.ru/?page_id=29&amp;cartoonid=".stripslashes($product['id'])."</link>\n\r";
      $output .= "      <description>".stripslashes($product['description'])."<![CDATA[<a href='http://cartoonbank.ru/?page_id=29&amp;cartoonid=".stripslashes($product['id'])."'><br /><img title='". stripslashes($product['name']) ."' src='http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/". stripslashes($product['image'])."' alt='". stripslashes($product['name'])."' /></a>]]></description>\n\r";
      $output .= "      <pubDate>".date("r")."</pubDate>\n\r";
      $output .= "      <guid>http://cartoonbank.ru/?page_id=29&amp;cartoonid=".stripslashes($product['id'])."</guid>\n\r";
	  $output .= '    ';
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
      $country_data = ''; //$wpdb->get_results($country_sql,ARRAY_A);
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
		$me = '';
		$values = $wpdb->get_results("SELECT * FROM `wp_product_brands` WHERE `active`='1' ORDER BY `id` ASC",ARRAY_A);
		$options .= "<option $selected value='0'>Выберите автора</option>\r\n";


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
		  $me = $option['name'];
		  }
		  $options .= "<option $selected value='".$option['id']."'>".$option['name']."</option>\r\n";
		  $selected = "";
		}

		$concat = "<select name='brand' id='brandslist' $combo_disabled'>".$options."</select>\r\n";

		if (isset($current_user->wp_capabilities['author']) && $current_user->wp_capabilities['author']==1)
		{
			return $me;
		}
		else
		{
			return $concat;
		}
	}
  
  function variationslist($current_variation = '')
    {
    global $wpdb;
    $options = "";
    $values = $wpdb->get_results("SELECT * FROM `wp_product_variations` ORDER BY `id` ASC",ARRAY_A);
    $options .= "<option  $selected value='0'>".TXT_WPSC_SELECTAVARIATION."</option>\r\n";
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
  //$sql = "SELECT * FROM `wp_product_list` WHERE `id`=$prodid LIMIT 1";
  $sql = "SELECT wp_product_list.*, wp_product_files.width, wp_product_files.height, wp_product_files.mimetype FROM wp_product_list, wp_product_files WHERE wp_product_files.id=wp_product_list.file AND wp_product_list.id=$prodid LIMIT 1";
  $product_data = $wpdb->get_results($sql,ARRAY_A) ;
  
  $product = $product_data[0];
  
  /* 
   * for security reason add to url for hires images sid - last 6 simbols of idhash
   *
   */
   $sql = "SELECT `idhash` FROM `wp_product_files` WHERE `id`=" . $product['file'];
   $idhash_data = $wpdb->get_results($sql, ARRAY_A);
   if($idhash_data != null) 
   {
		$idhash = "&sid=" . substr($idhash_data[0]['idhash'], -6);
   }			
  
  $output = "<table>\n\r";
  $output .= "<tr>\n\r";
  $output .= "<td class='r'>";
  $output .= "Автор: ";
  $output .= "</td>\n\r";
  $output .= "<td>\n\r";
  $output .= brandslist($product['brand']);

  $approved = 0;
		//pokazh($product,"product: ");
		//pokazh($current_user);

	if ($product['approved'] == '1')
	  {
		$approved = " checked='checked'";
		if (isset($current_user->wp_capabilities['administrator']) && $current_user->wp_capabilities['administrator']==1)
			{$output .= "<input type='checkbox' name='approved'".$approved."/> Утверждено.";}
		else if (isset($current_user->wp_capabilities['editor']) && $current_user->wp_capabilities['editor']==1)
			{$output .= "<input type='checkbox' name='approved'".$approved."/> Утвержено.";}
		$output .= "<div style='color:#669900'>Картинка находится в <b>хранилище банка</b></div>";
	  }
	  elseif($product['approved'] == '0')
	  {
		$approved = "";
		if (isset($current_user->wp_capabilities['administrator']) && $current_user->wp_capabilities['administrator']==1)
			{$output .= "<input type='checkbox' name='approved'".$approved."/> Утверждено.";}
		$output .= "<div style='color:#9900CC'>Картинка может находиться в <b>Рабочем столе</b></div>";
	  }
	  else
	  {
		$approved = "";
		if (isset($current_user->wp_capabilities['administrator']) && $current_user->wp_capabilities['administrator']==1)
			{$output .= "<input type='checkbox' name='approved'".$approved."/> Утверждено.";}
		$output .= "<div style='color:#FF6600'>Картинка находится в <b>прихожей банка</b> в ожидании приёма</div>";
	  }
  
  $output .= "</td>\n\r";
  $output .= "</tr>\n\r";
  
  $output .= "<tr>\n\r";
  $output .= "<td class='r'>";
  $output .= "Название рисунка: ";
  $output .= "</td>\n\r";
  $output .= "<td>";
  $output .= "<input id='productnameedit' type='text' style='width:300px;' name='title' value='".stripslashes($product['name'])."' /> # <a href='".get_option('siteurl')."/?page_id=29&cartoonid=".$product['id']."' target=_blank>".$product['id']."</a>";
  $output .= "</td>\n\r";
  $output .= "</tr>\n\r";
  
  $output .= "<tr>\n\r";
  $output .= "<td class='r'>";
  $output .= "Краткое описание: ";
  $output .= "</td>\n\r";
  $output .= "<td>";
  $output .= "<textarea id='productdescredit' name='description' cols='50' rows='4' >".stripslashes($product['description'])."</textarea>";
  $output .= "</td>\n\r";
  $output .= "</tr>\n\r";
  
  $output .= "<tr>\n\r";
  $output .= "<td class='r'>";
  $output .= "Ключевые слова,<br />разделённые запятыми:<br>";

  $output .= "<a href='".get_option('siteurl')."/ales/wordassociations/words.php?id=".$product['id']."' target=_blank>добавить<br>ассоциаций</a>";

  $output .= "</td>\n\r";
  $output .= "<td>";
  $output .= "<textarea id='tagsedit' name='additional_description' cols='50' rows='4' >".stripslashes($product['additional_description'])."</textarea>";
  $output .= "</td>\n\r";
  $output .= "</tr>\n\r";

  $visible = "";
  if ($product['visible'] == '1')
    $visible = " checked='checked'";
 
  $output .= "<tr>\n\r";
  $output .= "</tr>\n\r";

  $output .= "<tr>\n\r";
  $output .= "<td class='r'>";
  $output .= "Видно всем:";
  $output .= "</td>\n\r";
  $output .= "<td>";
  $output .= "<input type='checkbox' name='visible'".$visible."/> <span style='color:#999;'>Если выключить — не будет видно покупателям</span>";
  $output .= "</td>\n\r";
  $output .= "</tr>\n\r";

  $colored = "";
  if ($product['color'] == '1')
    $colored = " checked='checked' ";


  $temadnya = "";

	  $istemadnya_sql = "SELECT * FROM `wp_item_category_associations` where `category_id` = '777' and `product_id` = ".$product['id'];
//pokazh ($istemadnya_sql);
	  $istemadnya = $wpdb->get_results($istemadnya_sql);

  if($istemadnya != null)
    $temadnya = " checked='checked' ";

//pokazh($istemadnya,"istemadnya: ");

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

  $output .= "<tr>\n\r";
  $output .= "<td class='ralt'>";
  $output .= "Цветной рисунок:";
  $output .= "</td>\n\r";
  $output .= "<td style='background-color:#FFFF33;'>";
  $output .= "<input type='checkbox' name='colored'".$colored."/> <span style='color:#999;'>Отключите для ч/б рисунков</span>";
  $output .= "</td>\n\r";
  $output .= "</tr>\n\r";


  $output .= "<tr>\n\r";
  $output .= "<td class='r'>";
  $output .= "Не для продажи:";
  $output .= "</td>\n\r";
  $output .= "<td>\n\r";
  $output .= "<input type='checkbox' name='not_for_sale'".$not_for_sale."/> <span style='color:#999;'>Не продаётся, если включено</span>";
  $output .= "</td>\n\r";
  $output .= "</tr>\n\r";


  $output .= "<tr>\n\r";
  $output .= "<td>";

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
    $output .= "Ш х В: ".$product['width']."x".$product['height']."<br>".$product['mimetype'];

  
  $output .= "</td>\n\r";
  $output .= "<td>\n\r";
  $output .= categorylist($product['id']);
/*
if (isset($current_user->wp_capabilities['administrator']) && $current_user->wp_capabilities['administrator']==1)
	{
		$output .= categorylist($product['id']);
	}
else
	{
		$output .= "После утверждения рисунка модераторами Категория может быть изменена администратором";
		$output .= "<div  style='display:none;'>".categorylist($product['id'])."</div>";
	}
*/
  $output .= "</td>\n\r";
  $output .= "</tr>\n\r";

  $output .= "<tr>\n\r";
  $output .= "<td class='ralt'>";
  $output .= "Тема дня::";
  $output .= "</td>\n\r";
  $output .= "<td class='lalt'>";
  $output .= "<input type='checkbox' name='temadnya'".$temadnya."/> <span style='color:#999;'>считаю актуальной темой</span>";
  $output .= "</td>\n\r";
  $output .= "</tr>\n\r";
//if (isset($current_user->wp_capabilities['administrator']) && $current_user->wp_capabilities['administrator']==1){
	
  $output .= "<tr>\n\r";
  $output .= "<td class='r'>";
  $output .= "Доступны лицензии:";
  $output .= "</td>\n\r";
  $output .= "<td>\n\r";
  $output .= "&nbsp;&nbsp;&nbsp;Огр:&nbsp;<input id='license1' type='checkbox' name='license1'".$license1checked.">&nbsp;&nbsp;&nbsp;Станд:&nbsp;<input id='license2' type='checkbox' name='license2'".$license2checked.">&nbsp;&nbsp;&nbsp;Расш:&nbsp;<input id='license3' type='checkbox' name='license3'".$license3checked."><br />";
  $output .= "</td>\n\r";
  $output .= "</tr>\n\r";
//}	

  $output .= "<tr>\n\r";
  $output .= "<td colspan='2'>";

if (isset($current_user->wp_capabilities['administrator']) && $current_user->wp_capabilities['administrator']==1)
{
$output .= "<a href='admin.php?page=wp-shopping-cart/display-items.php&amp;deleteid=".$product['id']."' onclick='return conf();'><img src='../img/trash.gif' title='удалить'></a>";
}

  $output .= "<a  href='admin.php?page=wp-shopping-cart/display-items.php&updateimage=".$product['id']."' ><img src='".get_option('siteurl')."/img/reload.gif' title='Обновить иконку и слайд с водяными знаками'></a>";
   $output .= "&nbsp;<a href='index.php?admin_preview=true&product_id=".$product['id'].$idhash."' style='float: left;' ><img src='../wp-content/plugins/wp-shopping-cart/images/download.gif' title='Скачать оригинальный файл' /></a>";
  
  $output .= "</td>\n\r";
  $output .= "</tr>\n\r";

  // download original image  
  if($product['file'] > 0)
    {
    if(is_numeric($product['file']) && ($product['file'] > 0))
      {
      $file_data = $wpdb->get_results("SELECT * FROM `wp_product_files` WHERE `id`='".$product['file']."' LIMIT 1",ARRAY_A);
      if(($file_data != null) && ($file_data[0]['mimetype'] == 'audio/mpeg') && (function_exists('listen_button')))
        {
        $output .= "&nbsp;&nbsp;&nbsp;".listen_button($file_data[0]['idhash']);
        }
      }
        
    $output .= "</td>\n\r";
    $output .= "</tr>\n\r";
  
              
    $output .= "<tr>\n\r";
    $output .= "<td class='r'>";
    $output .= "Заменить файл:";
    $output .= "</td>\n\r";
    $output .= "<td>\n\r";
    $output .= "<input type='file' name='file' value='' /> <div style='color:#999;'>Это тот файл, ссылка на который<br />будет отправлена заказчику</div>";
    $output .= "</td>\n\r";
    $output .= "</tr>\n\r";
    }
  $output .= "<tr>\n\r";
  $output .= "<td>\n\r";
  $output .= "</td>\n\r";
  $output .= "<td>\n\r";
  $output .= "<input type='hidden' name='prodid' value='".$product['id']."' />";
  $output .= "<input type='hidden' name='submit_action' value='edit' />";
  $output .= "<br /><input type=\"button\" class='edit_button' style='padding:6px; background-color:#84DF88;' name='sendit' value='Сохранить изменения' onclick=\"checkthefieldsEditForm();\"/>";

if ($product['approved'] != '1' && isset($current_user->wp_capabilities['editor']) && $current_user->wp_capabilities['editor']==1)
	{
		$output .= "<br /><br /><br /><br /><a class='button' href='admin.php?page=wp-shopping-cart/display-items.php&deleteid=".$product['id']."' onclick=\"return conf();\" ><img src='".get_option('siteurl')."/img/trash.gif'> стереть изображение!</a>";
	}

if ($product['approved'] == '1' && isset($current_user->wp_capabilities['administrator']) && $current_user->wp_capabilities['administrator']==1)
	{
		$output .= "<br /><br /><br /><br /><a class='button' href='admin.php?page=wp-shopping-cart/display-items.php&deleteid=".$product['id']."' onclick=\"return conf();\" ><img src='".get_option('siteurl')."/img/trash.gif'> стереть изображение</a>";
	}

  
  $output .= "</td>\n\r";
  $output .= "</tr>\n\r";
  
  $output .= "</table>\n\r";
  
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
	$selected = "";
	$concat = "";
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
  $output .= "<textarea name='description' cols='50' rows='4' >".stripslashes($product['description'])."</textarea>";
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
  $output .= "<a class='delete_button' href='admin.php?page=wp-shopping-cart/display-category.php&deleteid=".$product['id']."' onclick=\"return conf();\" >".TXT_WPSC_DELETE."</a>";
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
  $output .= "<textarea name='description' cols='50' rows='4' >".stripslashes($product['description'])."</textarea>";
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
  $output .= "<a class='delete_button' href='admin.php?page=wp-shopping-cart/display-brands.php&deleteid=".$product['id']."' onclick=\"return conf();\" >стереть</a>";
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
  $output .= "<a class='delete_button' href='admin.php?page=wp-shopping-cart/display_variations.php&deleteid=".$variation['id']."' onclick=\"return conf();\" >".TXT_WPSC_DELETE."</a>";
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
            $bad_input_message .= "Пожалуйста, введите правильное имя<br />";
            break;
    
            case TXT_WPSC_LASTNAME:
            $bad_input_message .= "Пожалуйста, введите правильную фамилию<br />";
            break;
    
            case TXT_WPSC_EMAIL:
            $bad_input_message .= "Пожалуйста, введите правильный E-mail<br />";
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
//pokazh($cart,"cart");
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



$current_user = wp_get_current_user();   
  $_current_user_id = $current_user->id;

//pokazh($_SESSION,"SESSION");
//pokazh($_POST,"POST");
//pokazh($wpdb,"wpdb");
//exit();

/*
	wp_get_current_user(): WP_User Object
	(
		[data] => stdClass Object
			(
				[ID] => 1
				[user_login] => admin
				[user_pass] => $P$9f9It8Kpj56xOQprafuL49jfHEjvlW.
				[user_nicename] => admin
				[user_email] => admin@cartoonist.name
				[user_url] => 
				[user_registered] => 2007-01-15 15:32:35
				[user_activation_key] => 
				[user_status] => 0
				[display_name] => admin
				[wallet] => 3090.00
				[discount] => 0.00
				[contract] => aadmin
				[wp_user_level] => 10
				[wp_capabilities] => Array
					(
						[administrator] => 1
					)

				[first_name] => Игорь
				[last_name] => Алёшин
				[nickname] => admin
				[description] => 
				[jabber] => 
				[aim] => 
				[yim] => 
				[rich_editing] => true
				[skype] => lyapustin
				[icq] => 1516347
				[wp_dashboard_quick_press_last_post_id] => 86
				[plugins_last_view] => active
				[wp_usersettings] => m11=c&m10=o&m12=o&m7=o&m5=o&m8=o&m6=o&m1=c&m3=c&m4=c&imgsize=large&galfile=1&editor=html&uploader=1&urlbutton=file&align=center
				[wp_usersettingstime] => 1286619937
				[closedpostboxes_page] => Array
					(
						[0] => postcustom
						[1] => authordiv
					)

				[metaboxhidden_page] => Array
					(
						[0] => slugdiv
					)

				[closedpostboxes_dashboard] => Array
					(
						[0] => dashboard_right_now
						[1] => dashboard_recent_comments
						[2] => dashboard_incoming_links
						[3] => dashboard_recent_drafts
					)

				[metaboxhidden_dashboard] => Array
					(
					)

				[comment_shortcuts] => false
				[admin_color] => fresh
				[use_ssl] => 0
				[user_level] => 10
				[user_firstname] => Игорь
				[user_lastname] => Алёшин
				[user_description] => 
			)

		[ID] => 1
		[id] => 1
		[caps] => Array
			(
				[administrator] => 1
			)

		[cap_key] => wp_capabilities
		[roles] => Array
			(
				[0] => administrator
			)

		[allcaps] => Array
			(
				[switch_themes] => 1
				[edit_themes] => 1
				[activate_plugins] => 1
				[edit_plugins] => 1
				[edit_users] => 1
				[edit_files] => 1
				[manage_options] => 1
				[moderate_comments] => 1
				[manage_categories] => 1
				[manage_links] => 1
				[upload_files] => 1
				[import] => 1
				[unfiltered_html] => 1
				[edit_posts] => 1
				[edit_others_posts] => 1
				[edit_published_posts] => 1
				[publish_posts] => 1
				[edit_pages] => 1
				[read] => 1
				[level_10] => 1
				[level_9] => 1
				[level_8] => 1
				[level_7] => 1
				[level_6] => 1
				[level_5] => 1
				[level_4] => 1
				[level_3] => 1
				[level_2] => 1
				[level_1] => 1
				[level_0] => 1
				[exec_php] => 1
				[edit_others_pages] => 1
				[edit_published_pages] => 1
				[publish_pages] => 1
				[delete_pages] => 1
				[delete_others_pages] => 1
				[delete_published_pages] => 1
				[delete_posts] => 1
				[delete_others_posts] => 1
				[delete_published_posts] => 1
				[delete_private_posts] => 1
				[edit_private_posts] => 1
				[read_private_posts] => 1
				[delete_private_pages] => 1
				[edit_private_pages] => 1
				[read_private_pages] => 1
				[delete_users] => 1
				[create_users] => 1
				[unfiltered_upload] => 1
				[edit_dashboard] => 1
				[update_plugins] => 1
				[delete_plugins] => 1
				[install_plugins] => 1
				[update_themes] => 1
				[install_themes] => 1
				[update_core] => 1
				[list_users] => 1
				[remove_users] => 1
				[add_users] => 1
				[promote_users] => 1
				[edit_theme_options] => 1
				[delete_themes] => 1
				[export] => 1
				[administrator] => 1
			)

		[first_name] => Игорь
		[last_name] => Алёшин
		[filter] => 
		[wallet] => 3090.00
		[user_login] => admin
		[user_pass] => $P$9f9It8Kpj56xOQprafuL49jfHEjvlW.
		[user_nicename] => admin
		[user_email] => admin@cartoonist.name
		[user_url] => 
		[user_registered] => 2007-01-15 15:32:35
		[user_activation_key] => 
		[user_status] => 0
		[display_name] => admin
		[discount] => 0.00
		[contract] => aadmin
		[wp_user_level] => 10
		[wp_capabilities] => Array
			(
				[administrator] => 1
			)

		[nickname] => admin
		[description] => 
		[jabber] => 
		[aim] => 
		[yim] => 
		[rich_editing] => true
		[skype] => lyapustin
		[icq] => 1516347
		[wp_dashboard_quick_press_last_post_id] => 86
		[plugins_last_view] => active
		[wp_usersettings] => m11=c&m10=o&m12=o&m7=o&m5=o&m8=o&m6=o&m1=c&m3=c&m4=c&imgsize=large&galfile=1&editor=html&uploader=1&urlbutton=file&align=center
		[wp_usersettingstime] => 1286619937
		[closedpostboxes_page] => Array
			(
				[0] => postcustom
				[1] => authordiv
			)

		[metaboxhidden_page] => Array
			(
				[0] => slugdiv
			)

		[closedpostboxes_dashboard] => Array
			(
				[0] => dashboard_right_now
				[1] => dashboard_recent_comments
				[2] => dashboard_incoming_links
				[3] => dashboard_recent_drafts
			)

		[metaboxhidden_dashboard] => Array
			(
			)

		[comment_shortcuts] => false
		[admin_color] => fresh
		[use_ssl] => 0
		[user_level] => 10
		[user_firstname] => Игорь
		[user_lastname] => Алёшин
		[user_description] => 
	)



	POST: Array
	(
		[total] => 200
		[collected_data] => Array
			(
				[1] => Игорь
				[2] => Алёшин
				[3] => admin@cartoonist.name
				[4] => 
				[5] => 
			)

		[agree] => yes
		[payment_method] => wallet
		[submitwpcheckout] => true
		[submit] => Оплатить
	)



	SESSION: Array
	(
		[selected_country] => 
		[cart_paid] => 
		[nzshpcrt_cart] => Array
			(
				[1] => cart_item Object
					(
						[product_id] => 6155
						[product_variations] => 
						[quantity] => 1
						[name] => Ожидаемый подарок
						[price] => 200.00
						[license] => l1_price
						[author] => Тарасенко Валерий
					)

			)

		[total] => 200
		[nzshpcrt_serialized_cart] => a:1:{i:1;O:9:"cart_item":7:{s:10:"product_id";s:4:"6155";s:18:"product_variations";N;s:8:"quantity";i:1;s:4:"name";s:33:"Ожидаемый подарок";s:5:"price";s:6:"200.00";s:7:"license";s:8:"l1_price";s:6:"author";s:33:"Тарасенко Валерий";}}
		[collected_data] => Array
			(
				[1] => Игорь
				[2] => Алёшин
				[3] => admin@cartoonist.name
				[4] => 
				[5] => 
			)

		[checkoutdata] => Array
			(
				[total] => 200
				[collected_data] => Array
					(
						[1] => Игорь
						[2] => Алёшин
						[3] => admin@cartoonist.name
						[4] => 
						[5] => 
					)

				[agree] => yes
				[payment_method] => wallet
				[submitwpcheckout] => true
				[submit] => Оплатить
			)

	)


	wpdb: wpdb Object
	(
		[show_errors] => 1
		[suppress_errors] => 
		[last_error] => 
		[num_queries] => 9
		[num_rows] => 1
		[rows_affected] => 0
		[insert_id] => 0
		[last_query] => SELECT * FROM `wp_collect_data_forms` WHERE `id` = '5' LIMIT 1
		[last_result] => Array
			(
				[0] => stdClass Object
					(
						[id] => 5
						[name] => Адрес 2
						[type] => text
						[mandatory] => 0
						[display_log] => 0
						[default] => 
						[active] => 0
						[order] => 5
					)

			)

		[col_info] => Array
			(
				[0] => stdClass Object
					(
						[name] => id
						[table] => wp_collect_data_forms
						[def] => 
						[max_length] => 1
						[not_null] => 1
						[primary_key] => 1
						[multiple_key] => 0
						[unique_key] => 0
						[numeric] => 1
						[blob] => 0
						[type] => int
						[unsigned] => 1
						[zerofill] => 0
					)

				[1] => stdClass Object
					(
						[name] => name
						[table] => wp_collect_data_forms
						[def] => 
						[max_length] => 12
						[not_null] => 1
						[primary_key] => 0
						[multiple_key] => 0
						[unique_key] => 0
						[numeric] => 0
						[blob] => 0
						[type] => string
						[unsigned] => 0
						[zerofill] => 0
					)

				[2] => stdClass Object
					(
						[name] => type
						[table] => wp_collect_data_forms
						[def] => 
						[max_length] => 4
						[not_null] => 1
						[primary_key] => 0
						[multiple_key] => 0
						[unique_key] => 0
						[numeric] => 0
						[blob] => 0
						[type] => string
						[unsigned] => 0
						[zerofill] => 0
					)

				[3] => stdClass Object
					(
						[name] => mandatory
						[table] => wp_collect_data_forms
						[def] => 
						[max_length] => 1
						[not_null] => 1
						[primary_key] => 0
						[multiple_key] => 0
						[unique_key] => 0
						[numeric] => 0
						[blob] => 0
						[type] => string
						[unsigned] => 0
						[zerofill] => 0
					)

				[4] => stdClass Object
					(
						[name] => display_log
						[table] => wp_collect_data_forms
						[def] => 
						[max_length] => 1
						[not_null] => 1
						[primary_key] => 0
						[multiple_key] => 0
						[unique_key] => 0
						[numeric] => 0
						[blob] => 0
						[type] => string
						[unsigned] => 0
						[zerofill] => 0
					)

				[5] => stdClass Object
					(
						[name] => default
						[table] => wp_collect_data_forms
						[def] => 
						[max_length] => 0
						[not_null] => 1
						[primary_key] => 0
						[multiple_key] => 0
						[unique_key] => 0
						[numeric] => 0
						[blob] => 0
						[type] => string
						[unsigned] => 0
						[zerofill] => 0
					)

				[6] => stdClass Object
					(
						[name] => active
						[table] => wp_collect_data_forms
						[def] => 
						[max_length] => 1
						[not_null] => 1
						[primary_key] => 0
						[multiple_key] => 0
						[unique_key] => 0
						[numeric] => 0
						[blob] => 0
						[type] => string
						[unsigned] => 0
						[zerofill] => 0
					)

				[7] => stdClass Object
					(
						[name] => order
						[table] => wp_collect_data_forms
						[def] => 
						[max_length] => 1
						[not_null] => 1
						[primary_key] => 0
						[multiple_key] => 1
						[unique_key] => 0
						[numeric] => 1
						[blob] => 0
						[type] => int
						[unsigned] => 1
						[zerofill] => 0
					)

			)

		[queries] => 
		[prefix] => wp_
		[ready] => 1
		[blogid] => 0
		[siteid] => 0
		[tables] => Array
			(
				[0] => posts
				[1] => comments
				[2] => links
				[3] => options
				[4] => postmeta
				[5] => terms
				[6] => term_taxonomy
				[7] => term_relationships
				[8] => commentmeta
			)

		[old_tables] => Array
			(
				[0] => categories
				[1] => post2cat
				[2] => link2cat
			)

		[global_tables] => Array
			(
				[0] => users
				[1] => usermeta
			)

		[ms_global_tables] => Array
			(
				[0] => blogs
				[1] => signups
				[2] => site
				[3] => sitemeta
				[4] => sitecategories
				[5] => registration_log
				[6] => blog_versions
			)

		[comments] => wp_comments
		[commentmeta] => wp_commentmeta
		[links] => wp_links
		[options] => wp_options
		[postmeta] => wp_postmeta
		[posts] => wp_posts
		[terms] => wp_terms
		[term_relationships] => wp_term_relationships
		[term_taxonomy] => wp_term_taxonomy
		[usermeta] => wp_usermeta
		[users] => wp_users
		[blogs] => 
		[blog_versions] => 
		[registration_log] => 
		[signups] => 
		[site] => 
		[sitecategories] => 
		[sitemeta] => 
		[field_types] => Array
			(
				[post_author] => %d
				[post_parent] => %d
				[menu_order] => %d
				[term_id] => %d
				[term_group] => %d
				[term_taxonomy_id] => %d
				[parent] => %d
				[count] => %d
				[object_id] => %d
				[term_order] => %d
				[ID] => %d
				[commment_ID] => %d
				[comment_post_ID] => %d
				[comment_parent] => %d
				[user_id] => %d
				[link_id] => %d
				[link_owner] => %d
				[link_rating] => %d
				[option_id] => %d
				[blog_id] => %d
				[meta_id] => %d
				[post_id] => %d
				[user_status] => %d
				[umeta_id] => %d
				[comment_karma] => %d
				[comment_count] => %d
				[active] => %d
				[cat_id] => %d
				[deleted] => %d
				[lang_id] => %d
				[mature] => %d
				[public] => %d
				[site_id] => %d
				[spam] => %d
			)

		[charset] => utf8
		[collate] => 
		[real_escape] => 1
		[dbuser] => z58365_cbru3
		[func_call] => $db->query("SELECT * FROM `wp_collect_data_forms` WHERE `id` = '5' LIMIT 1")
		[dbh] => Resource id #5
		[base_prefix] => wp_
		[categories] => wp_categories
		[post2cat] => wp_post2cat
		[link2cat] => wp_link2cat
		[last_db_used] => other/read
		[result] => Resource id #36
	)


*/

    $sql = "INSERT INTO `wp_purchase_logs` (`id` , `totalprice` , `sessionid` , `user_id`, `firstname`, `lastname`, `email`, `date`, `shipping_country`,`address`, `phone` ) VALUES ('', '".$wpdb->escape($_SESSION['total'])."', '".$sessionid."', '".$_current_user_id."', '".$wpdb->escape($_POST['collected_data']['1'])."', '".$wpdb->escape($_POST['collected_data']['2'])."', '".$_POST['collected_data']['3']."', '".time()."', '', '".$_POST['collected_data']['5']."', '".$_POST['collected_data']['4']."')";

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
// pokazh ($product_data,"product_data");
     if($product_data['file'] > 0)
       {
       $wpdb->query("INSERT INTO `wp_download_status` ( `id` , `fileid` , `purchid` , `downloads` , `active` , `datetime` ) VALUES ( '', '".$product_data['file']."', '".$getid[0]['id']."', '$downloads', '0', NOW( ));");
       }

    $price_modifier = 0;
    
    $price = $cart_item->price; 

	$gst = ''; 
	$country = '';
    $country_data = ''; 
    $shipping = 0;

	$license_num = $getid[0]['sessionid']."_".$cart_item->product_id;
	$cartsql = "INSERT INTO `wp_cart_contents` ( `id` , `prodid` , `purchaseid`, `price`, `pnp`, `gst`, `quantity`, `license` ) VALUES ('', '".$row."', '".$getid[0]['id']."','".$price."','".$shipping."', '".$gst."','".$quantity."', '".$license_num."')";
     $wpdb->query($cartsql);

 //pokazh ($cartsql,"cartsql");
	 /*
	 $cart_id = $wpdb->get_results("SELECT LAST_INSERT_ID() AS `id` FROM `wp_product_variations` LIMIT 1",ARRAY_A);
     $cart_id = $cart_id[0]['id'];
     if($variations != null)
       {
       foreach($variations as $variation => $value)
         {
         $wpdb->query("INSERT INTO `wp_cart_item_variations` ( `id` , `cart_id` , `variation_id` , `venue_id` ) VALUES ( '', '".$cart_id."', '".$variation."', '".$value."' );");
         }
       }
	*/

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
  
  $current_user = wp_get_current_user();   

  $_wallet = $current_user->wallet;
  if (isset($current_user->discount))
		$_discount = $current_user->discount;
  else
		$_discount = 0;



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
	$discount = 0;

		
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
	  $output .= "<tr><td style='border-bottom: 1px solid #CCCC99'>".$product[0]['id']."</td><td  style='border-bottom: 1px solid #CCCC99'>".stripslashes($product[0]['name'])."</td><td align='right' style='border-bottom: 1px solid #CCCC99'>".round($cart_item->price)."</td></tr>";
      }

	if ($_discount>0)
		{
			$output .= "<tr><td>&nbsp;</td><td>ваша cкидка</td><td align='right'>".$_discount."%</td></tr>";
			$output .= "<tr><td>&nbsp;</td><td style='border-top: 1px solid #FF9966'>Итого без скидки: </td><td align='right' style='vertical-align:bottom;border-top: 1px solid #FF9966'><strike>".round($total)."</strike></td></tr>";
			$output .= "<tr><td>&nbsp;</td><td style='border-top: 1px solid #FF9966'>Итого со скидкой: </td><td align='right' style='vertical-align:bottom;border-top: 1px solid #FF9966'><b>".round($total*(100-$_discount)/100)."</b></td></tr>";
		}
		else
		{
			$output .= "<tr><td>&nbsp;</td><td style='border-top: 1px solid #FF9966'>Итого: </td><td align='right' style='border-top: 1px solid #FF9966'><b>".round($total)."</b></td></tr>";
		}
    
    $output .= "</table>";

$_SESSION['total'] = round($total*(100-$_discount)/100);

	$output .= "На Личном счёте <b>".round($_wallet)."</b> р.<br />";

if (($total > $_wallet) && ($_wallet!=0))
	$output .= "<div style='color:#CC0000;'>Не хватает средств для покупки выбранных изображений.</div>";

    if(get_option('permalink_structure') != '')
      {
      $seperator ="?";
      }
      else
         {
         $seperator ="&";
         }
	$output .= "<a class='button' href='".get_option('product_list_url')."?cart=empty' onclick='emptycart();return false;'>x Очистить корзину</a><br />";

global $user_identity;
if ($user_identity == '')
{
	$output .= "<div style='color:#CC0000;'><b>Оплата возможна только после входа</b></div>";
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
      $output .= "На Личном счёте <b>".round($_wallet)."</b> р.<br />";
      }

  return $output;
  }

function nzshpcrt_download_file()
  {
  global $wpdb,$user_level,$wp_rewrite;
  get_currentuserinfo();
  if(isset($_GET['downloadid']) and is_numeric($_GET['downloadid']) and
     isset($_GET['sid']))
    {
    $id = $_GET['downloadid'];
	$sid = $_GET['sid'];
	if (strlen($_GET['sid']) != 6)
	{
	  $siteurl = get_option('siteurl');
	  header("Location: " . $siteurl . "404.php");
	  exit();
	}
	$skip_dowload_counter = false;
	if (isset($_GET['mode']) && $_GET['mode'] = 'go')
	{
		$skip_dowload_counter = true;
	}
	if (isset($_GET['mail']) && $_GET['mail'] = 1)
	{
		$addmail = 'mail=1&';
	}
	else
		{$addmail = '';}

	
	$download_data = $wpdb->get_results("SELECT * FROM `wp_download_status` WHERE `id`='".$id."' LIMIT 1", ARRAY_A);
    isset($download_data[0])?$download_data = $download_data[0]:null;
    if($download_data != null)
     {
	  $redirect_url = get_option('redirect_download_url');
	  $add_to_url = ($skip_dowload_counter) ? "&mode=go" : "";
	  header("Location: $redirect_url?".$addmail."downid=$id&sid=$sid" . $add_to_url);
	  exit();
      }
    }
    else
      {
      if(isset($_GET['admin_preview']) and ($_GET['admin_preview'] == "true") && is_numeric($_GET['product_id'])and
		 isset($_GET['sid']) and strlen($_GET['sid']) == 6)
        {
        $product_id = $_GET['product_id'];
		$sid = $_GET['sid'];
        $product_data = $wpdb->get_results("SELECT * FROM `wp_product_list` WHERE `id` = '$product_id' LIMIT 1",ARRAY_A);

        if(is_numeric($product_data[0]['file']) && ($product_data[0]['file'] > 0))
          {
		  $preview_track = isset($_GET['admin_preview']) ? $_GET['admin_preview'] : "";	  
		  $redirect_url = get_option('redirect_download_url');
		  $preview = ($preview_track != 'true') ? "" : "&preview_track=true";
		  header("Location: $redirect_url?prodid=$product_id&sid=$sid$preview");
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
    $country_data = ''; //$wpdb->get_results("SELECT * FROM `wp_currency_list` ORDER BY `country` ASC",ARRAY_A);
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
	exit; //ales
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
  $country_data = ''; //$wpdb->get_results("SELECT * FROM `wp_currency_list` WHERE `isocode` IN ('".$country_code."') LIMIT 1",ARRAY_A);
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
  //echo "<link type='application/rss+xml' href='".get_option('siteurl')."/index.php?rss=true&rss_key=key&action=purchase_log&type=rss' title='Cartoonbank new images RSS' rel='alternate'/>";
  }
  
function nzshpcrt_product_list_rss_feed()
  {
  // our custom rss feed
  echo "<link rel='alternate' type='application/rss+xml' title='Cartoonbank RSS' href='".get_option('siteurl')."/index.php?rss=true&amp;action=product_list&amp;type=rss'/>";
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
//add_filter('the_content', 'top_votes');

//add_filter('wp_list_pages', 'nzshpcrt_hidepages');
 
add_action('wp_head', 'nzshpcrt_style');

add_action('admin_head', 'nzshpcrt_css');
if(isset($_GET['page']) and $_GET['page'] == "wp-shopping-cart/display-log.php")
  {
  //add_action('admin_head', 'nzshpcrt_product_log_rss_feed');
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

function notify_artist($id)
{
	global $wpdb;
	// this is to notify artist about download of the $id image
	$max_downloads =  get_option('max_downloads');

	// how many download attempts left?
	//$sql = "select downloads from wp_download_status where $id=".$id." and active=1 LIMIT 1";

	$sql = "SELECT c.price, st.purchid as zakaz, st.downloads, p.id as imageid, p.image as filename, p.name as cartoonname, p.description as description, 
					u.user_email as email, b.name as artist
			FROM wp_download_status AS st, wp_product_list AS p, wp_users AS u, wp_product_brands AS b, wp_cart_contents as c
			WHERE st.fileid = p.file
			AND b.id = p.brand
			AND b.user_id = u.id
			AND c.purchaseid = st.purchid
			AND st.id =".$id;

	$downloads_left = $wpdb->get_results($sql,ARRAY_A); // actual downloads_left
    $downloads = $downloads_left[0]['downloads']; // actual downloads_left

	if ($downloads == $max_downloads-1)
	{
		// if this is a first download of the file
		send_email_to_artist($downloads_left[0]['price'], $downloads_left[0]['imageid'], $downloads_left[0]['filename'], $downloads_left[0]['cartoonname'], $downloads_left[0]['description'], $downloads_left[0]['artist'], $downloads_left[0]['email']);
	}

	return;
}

function send_email_to_artist($price, $image_id, $filename, $cartoonname, $description, $artist, $email)
{
	///send_email_to_artist($downloads_left[0]['filename'], $downloads_left[0]['cartoonname'], $downloads_left[0]['description'], $downloads_left[0]['artist'], $downloads_left[0]['email'], );
	$headers = "From: ".get_option('return_email')."\r\n" .
			   'X-Mailer: PHP/' . phpversion() . "\r\n" .
			   "MIME-Version: 1.0\r\n" .
			   "Content-Type: text/html; charset=utf-8\r\n" .
			   "Content-Transfer-Encoding: 8bit\r\n\r\n";
	$nice_artistname = explode(' ',$artist);
	$nice_artistname = $nice_artistname[1]." ".$nice_artistname[0];

	// License type
	$lic_type = "";
	if (round($price) == 250)
	{$lic_type = "Ограниченная";}
	elseif (round($price) == 500)
	{$lic_type = "Стандартная";}
	elseif (round($price) == 500)
	{$lic_type = "Расширенная";}
	
	$mess = "";
	$mess .= "<br>Уважаемый ".$nice_artistname."!<br><br>";
	$mess .= $lic_type." лицензия на использование вашего изображения была только что передана Картунбанком заказчику.<br>Название рисунка: <b>\"".stripslashes($cartoonname)."\"</b> (".stripslashes($description).")<br>";
	$mess .= "<a href='".get_option('siteurl')."/?page_id=29&cartoonid=".$image_id."'><img src='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/product_images/".$filename."'></a>";

	$mess .= "<br><br>Поздравляем вас и напоминаем, что всегда рады видеть ваши новые рисунки у нас на сайте! Полный отчёт об уже поступивших на ваше имя денежных средствах доступен в разделе <a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display_artist_income.php'>Заработано</a>.<br>";

	$mess .= "<br><div style='font-size:0.8em;'>Это письмо отправлено автоматически и не требует ответа.<br>Чтобы отписаться от сообщений о продаже снимите отметку в строке <i>'Получать сообщения о продаже лицензии'</i> <a href='".get_option('siteurl')."/wp-admin/profile.php'>вашего профиля</a>.</div>";

	// send email
	mail($email, 'Сообщение о продаже изображения на сайте Картунбанк', $mess, $headers);
	// copy for control
	mail("igor.aleshin@gmail.com", 'Сообщение о продаже изображения на сайте Картунбанк', $mess, $headers);
	return;		
}
?>
