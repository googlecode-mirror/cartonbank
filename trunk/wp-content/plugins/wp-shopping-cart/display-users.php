<?php
$sql = "SELECT id, user_login, user_nicename, display_name, user_email, wallet, discount, contract FROM `wp_users` ORDER BY user_registered DESC LIMIT 300";
$product_list = $wpdb->get_results($sql,ARRAY_A) ;
?>

<div class="wrap">
  <h2>Юзеры</h2>

<?php
echo "        <table id='itemlist' style='padding:4px;'>\n\r";

echo "          <tr style='border:1px solid black; background-color:#c0c0c0;'>\n\r";
echo "            <td class='username'>\n\r";
echo "id";
echo "            </td>\n\r";

echo "            <td class='username'>\n\r";
echo "user_login";
echo "            </td>\n\r";

echo "            <td>\n\r";
echo "user_nicename";
echo "            </td>\n\r";

echo "            <td>\n\r";
echo "display_name";
echo "            </td>\n\r";


echo "            <td>\n\r";
echo "user_email";
echo "            </td>\n\r";

echo "            <td>\n\r";
echo "wallet";
echo "            </td>\n\r";

echo "            <td>\n\r";
echo "discount";
echo "            </td>\n\r";

echo "            <td>\n\r";
echo "contract";
echo "            </td>\n\r";

echo "          </tr>\n\r";


if($product_list != null)
  {
  foreach($product_list as $product)
	{
		echo "          <tr style='padding:2px;border-bottom:1px solid silver'>\n\r";
		echo "            <td>\n\r<a href='". get_option('siteurl')."/wp-admin/user-edit.php?user_id=".$product['id']."'>";
		echo $product['id'];
		echo "</a>            </td>\n\r";
		echo "            <td>\n\r";
		echo $product['user_login'];
		echo "            </td>\n\r";

		echo "            <td>\n\r";
		echo $product['user_nicename'];
		echo "            </td>\n\r";

		echo "            <td>\n\r";
		echo $product['display_name'];
		echo "            </td>\n\r";

		echo "            <td>\n\r";
		echo $product['user_nicename'];
		echo "            </td>\n\r";

		echo "            <td>\n\r";
		echo $product['user_email'];
		echo "            </td>\n\r";

		echo "            <td>\n\r";
		echo $product['wallet'];
		echo "            </td>\n\r";

		echo "            <td>\n\r";
		echo $product['contract'];
		echo "            </td>\n\r";

		echo "          </tr>\n\r";
	}
  }
  
echo "        </table>\n\r";
?>
