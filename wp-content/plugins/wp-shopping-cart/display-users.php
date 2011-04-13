<?php
$sql = "SELECT id, user_login, user_nicename, display_name, user_email, wallet, discount, contract FROM `wp_users` ORDER BY user_registered DESC LIMIT 300";
$product_list = $wpdb->get_results($sql,ARRAY_A) ;
?>

<style>
.t
{
	padding:2px;
	border-bottom:1px solid silver;
}
</style>

<div class="wrap">
  <h2>Юзеры</h2>

<?php
echo "        <table id='itemlist'>";

echo "          <tr style='border:1px solid black; background-color:#c0c0c0;'>";
echo "            <td class='t'>";
echo "id";
echo "            </td>";

echo "            <td class='t'>";
echo "user_login";
echo "            </td>";

echo "            <td class='t'>";
echo "display_name";
echo "            </td>";

echo "            <td class='t'>";
echo "user_email";
echo "            </td>";

echo "            <td class='t'>";
echo "wallet";
echo "            </td>";

echo "            <td class='t'>";
echo "discount";
echo "            </td class='t'>";

echo "            <td class='t'>";
echo "contract";
echo "            </td>";

echo "          </tr>";


if($product_list != null)
  {
  foreach($product_list as $product)
	{
		echo "          <tr>";
		echo "            <td class='t'><a href='". get_option('siteurl')."/wp-admin/user-edit.php?user_id=".$product['id']."'>";
		echo $product['id'];
		echo "</a>            </td>";
		echo "            <td class='t'>";
		echo $product['user_login'];
		echo "            </td>";

		echo "            <td class='t'>";
		echo $product['display_name'];
		echo "            </td>";

		echo "            <td class='t'>";
		echo $product['user_email'];
		echo "            </td>";

		echo "            <td class='t'>";
		echo $product['wallet'];
		echo "            </td>";

		echo "            <td class='t'>";
		echo round($product['discount'],0);
		echo "            </td>";

		echo "            <td class='t'>";
		echo $product['contract'];
		echo "            </td>";

		echo "          </tr>";
	}
  }
  
echo "        </table>";
?>
</div>