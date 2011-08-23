<?
// make payment to Echo SPB
include("/home/www/cb3/ales/config.php");

$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);


pay_on_behalf_of_echo(1111);

function pay_on_behalf_of_echo($cartoon_id)
{
	// constants
	$license_num = uniqid()."_".$cartoon_id;
	$sessionid = uniqid();
	$price = "250";
	$date_today = time();
	$date_download = date('y-m-d H:i:s');

	// wp_purchase_logs
		$sql = "INSERT INTO `cartoonbankru`.`wp_purchase_logs` (`id`, `totalprice`, `statusno`, `sessionid`, `transactid`, `authcode`, `user_id`, `firstname`, `lastname`, `email`, `address`, `phone`, `downloadid`, `processed`, `date`, `payment_arrived_date`, `gateway`, `shipping_country`, `shipping_region`) VALUES (NULL, '".$price."', '0', '".$sessionid."', '0', '0', '131', 'Валерий', 'Нечай', 'vnechay@gmail.com', 'эхомскспбру', '+7(921)9341454', '0', '1', '".$date_today."', '', 'wallet', '', '')";

		$result = mysql_query($sql);
		if (!$result) {die('Invalid query: ' . mysql_error());}

		$purchase_id = mysql_insert_id();
		if (!$purchase_id) {die('Can\'t get inserted line id: ' . mysql_error());}


	// wp_cart_contents
		$sql = "INSERT INTO `cartoonbankru`.`wp_cart_contents` (`id`, `prodid`, `purchaseid`, `price`, `pnp`, `gst`, `quantity`, `license`) VALUES (NULL, '".$cartoon_id."', '".$purchase_id."', '".$price."', '0', '', '1', '".$license_num."')";

		$result = mysql_query($sql);
		if (!$result) {die('Invalid query: ' . mysql_error());}


	// wp_status
		$sql = "INSERT INTO `cartoonbankru`.`wp_download_status` (`id`, `fileid`, `purchid`, `downloads`, `active`, `datetime`) VALUES (NULL, '".$cartoon_id."', '".$purchase_id."', '0', '0', '".$date_download."')";

		$result = mysql_query($sql);
		if (!$result) {die('Invalid query: ' . mysql_error());}

	echo "done";
}
?>
