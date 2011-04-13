<?php
$sql = "SELECT id, user_login, user_nicename, display_name, user_email, wallet, discount, contract FROM `wp_users` ORDER BY user_registered DESC LIMIT 300";
$product_list = $wpdb->get_results($sql,ARRAY_A) ;
?>

<div class="wrap">
  <h2>Юзеры</h2>

<?php<a href="">
  $num = 0;</a>
echo "    <table id='productpage' style='padding:4px;'>\n\r";
echo "      <tr><td>\n\r";
echo "        <table id='itemlist' style='padding:4px;background-color:silver;'>\n\r";
/*
echo "          <tr class='firstrow' style='border:1px solid black;background-color:#c0c0c0;'>\n\r";
echo "            <td>\n\r";
echo "фото";
echo "            </td>\n\r";

echo "            <td>\n\r";
echo "имя";
echo "            </td>\n\r";

echo "            <td>\n\r";
echo "описание";
echo "            </td>\n\r";

echo "            <td>\n\r";
echo "user_id";
echo "            </td>\n\r";


echo "            <td>\n\r";
echo "редактировать";
echo "            </td>\n\r";

echo "          </tr>\n\r";

http://109.120.143.27/cb/wp-admin/user-edit.php?user_id=90&wp_http_referer=%2Fcb%2Fwp-admin%2Fusers.php
*/
if($product_list != null)
  {
  foreach($product_list as $product)
	{
		echo "          <tr style='background-color:white;'>\n\r";
		echo "            <td>\n\r";
		echo "            </td>\n\r";
		echo "            <td>\n\r<a href=".get_option('siteurl')"/wp-admin/user-edit.php?user_id=".$product['id'].">";
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
echo "      </td><td class='secondcol'>\n\r";
echo "      </td></tr>\n\r";
echo "     </table>\n\r";
  ?>
</div>