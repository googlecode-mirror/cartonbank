<?
require_once("/home/www/cb3/ales/config.php");
// get cartoonid
if (isset($_REQUEST['cid']))
    {$cid = $_REQUEST['cid'];}
    else 
    {$cid=0;}

// get cartoon data
$sql = "SELECT `wp_product_list`.*, `wp_product_brands`.`name` as brand, `wp_product_brands`.`id` as brandid, `wp_product_categories`.`name` as kategoria, `wp_product_list`.`category` as category_id FROM `wp_product_list`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`id`='".$cid."' AND `wp_product_list`.`active`=1 LIMIT 1";

//pokazh($sql);

// roll out result
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);
$result = mysql_query($sql);

	while($r = mysql_fetch_array($result)) {
        $title = $r["name"];
        $image = $r["image"];
        $artist = $r["brand"];
        $category = $r["kategoria"];
        $description = $r["description"];
        $tags = $r["additional_description"];
    }

//$output = '<div class="icon"><img src=http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/images/'.$image.'></div>';
$output = '<div class="box"><img src=http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/images/'.$image.'></div>';
//$output .= '<div class="ititle">'.$title.'</div>';
//$output .= '<div class="description">'.$description.'</div>';
//$output .= '<div class="tags">'.$tags.'</div>';
//$output .= '<div class="artist">'.$artist.'</div>';


echo $output;






?>
<!-- <div><img src="http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/images/506ad08e74b0b0.34528270strah11.jpg"></div> -->