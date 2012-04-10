<?php

header('Content-Type: text/html; charset=utf-8');

require_once('../wp-config.php');
require_once('functions.php');
require_once('../wp-includes/functions.php');

function my_notify_artist($id, $max_down)
{
	global $mcon;

	$result = mysql_query("SELECT c.price, st.purchid as zakaz, st.downloads, " .
						  "p.id as imageid, p.image as filename, p.name as cartoonname, " .
						  "p.description as description, u.user_email as email, b.name as artist " .
						  "FROM wp_download_status AS st, wp_product_list AS p, wp_users AS u, wp_product_brands AS b, wp_cart_contents as c " .
						  "WHERE st.fileid = p.file " .
							"AND b.id = p.brand " .
							"AND b.user_id = u.id " .
							"AND c.purchaseid = st.purchid " .
							"AND st.id =".$id);
							
	if (!$result)
	{
		printf("Could not run query: %s\n", mysql_error());
		exit;
	}
	
	if (mysql_num_rows($result) == 0)
	{
		printf("Query returns 0 row.");
		exit;
	}
	
	$row = mysql_fetch_assoc($result);
	$downloads = $row["downloads"]; // actual downloads_left

	if ($downloads == $max_down)
	{
		// get option return_email
		$return_email = my_get_option($mcon, "return_email");
		$site_url = my_get_option($mcon, "siteurl");
		
		///send_email_to_artist
		$headers = "From: ".$return_email."\r\n" .
				   'X-Mailer: PHP/' . phpversion() . "\r\n" .
				   "MIME-Version: 1.0\r\n" .
				   "Content-Type: text/html; charset=utf-8\r\n" .
				   "Content-Transfer-Encoding: 8bit\r\n\r\n";
		$nice_artistname = explode(' ',$row["artist"]);
		$nice_artistname = $nice_artistname[1]." ".$nice_artistname[0];

		// License type
		$lic_type = "";
		if (round($row["price"]) == 250)
		{$lic_type = "Ограниченная";}
		elseif (round($row["price"]) == 500)
		{$lic_type = "Стандартная";}
		elseif (round($row["price"]) == 500)
		{$lic_type = "Расширенная";}
		
		$mess = "";
		$mess .= "<br>Уважаемый ".$nice_artistname."!<br><br>";
		$mess .= $lic_type." лицензия на использование вашего изображения была только что передана Картунбанком заказчику.<br>Название рисунка: <b>\"".stripslashes($row["cartoonname"])."\"</b> (".stripslashes($row["description"]).")<br>";
		$mess .= "<a href='".$site_url."/?page_id=29&cartoonid=".$row["image_id"]."'><img src='".$site_url."/wp-content/plugins/wp-shopping-cart/product_images/".$row["filename"]."'></a>";

		$mess .= "<br><br>Поздравляем вас и напоминаем, что всегда рады видеть ваши новые рисунки у нас на сайте! Полный отчёт об уже поступивших на ваше имя денежных средствах доступен в разделе <a href='".$site_url."/wp-admin/admin.php?page=wp-shopping-cart/display_artist_income.php'>Заработано</a>.<br>";
		


		$mess .= "<br><div style='font-size:0.8em;'>Это письмо отправлено автоматически и не требует ответа.<br>Чтобы отписаться от сообщений о продаже снимите отметку в строке <i>'Получать сообщения о продаже лицензии'</i> <a href='".$site_url."/wp-admin/profile.php'>вашего профиля</a>.</div>";

		// send email
		mail($email, 'Сообщение о продаже изображения на сайте Картунбанк', $mess, $headers);
		// copy for control
		mail("creasysee@yandex.ru", 'Сообщение о продаже изображения на сайте Картунбанк', $mess, $headers);			
	}
	
	mysql_free_result($result);

	return;
}

$downid = "";
$$prodid = "";
$confirm = false;
if (isset($_GET['downid']) and is_numeric($_GET['downid']))
{
	$downid = $_GET['downid'];
	$request_param = "downid=$downid";
}

if (isset($_GET['prodid']) and is_numeric($_GET['prodid']))
{
	$prodid = $_GET['prodid'];
	$request_param = "prodid=$prodid";
}

if (strlen($prodid) == 0 && strlen($downid) == 0)
{
	die('Error parameters list');
}

if (isset($_GET['confirm']) and 'true' == ($_GET['confirm']))
{
	$confirm = true;
}

$mode_go = "AND `active` = 1 AND `downloads` > 0";
if (isset($_GET['mode']) and $_GET['mode'] = 'go')
{
	$mode_go = "";
}

$mcon = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);

if (!$mcon)
{
    printf("Could not connect: '%s'", mysql_error());
    exit;
}

$db_select = mysql_select_db(DB_NAME, $mcon);

if (!$db_select)
{
    printf("Could not select db: '%s' (%s)", DB_NAME, mysql_error());
    exit;
}

// Extract max_uploads value
$max_down = my_get_option($mcon, "max_downloads");

if ($confirm)
{
	my_notify_artist($downid, $max_down);
	$result = mysql_query("UPDATE `wp_download_status` SET `downloads` = `downloads` -1 WHERE `id` = '$downid'");
	if (!$result)
	{
		printf("Could not decrement downloads field: %s\n", mysql_error());
		exit;
	}
	exit;
}

$str_query = (strlen($downid) > 0) ? "SELECT `idhash`,`date`,`mimetype`,`filename` FROM `wp_download_status` " .
		      "JOIN `wp_product_files` ON `wp_download_status`.`fileid` = `wp_product_files`.`id` " .
		      "WHERE `wp_download_status`.`id` = " . $downid . $mode_go . " LIMIT 1"
			  :
			  "SELECT `idhash`,`date`,`mimetype`,`filename` FROM `wp_product_list` " .
			  "JOIN `wp_product_files` ON `wp_product_list`.`file` = `wp_product_files`.`id` " .
			  "WHERE `wp_product_list`.`id` = " . $prodid . " LIMIT 1"; 
			  
$result = mysql_query($str_query);

if (!$result)
{
    printf("Could not run query: %s\n", mysql_error());
    exit;
}

if (mysql_num_rows($result) == 0)
{
    printf("Download data not found (may be limit download count?)");
    exit;
}

$row = mysql_fetch_assoc($result);

echo json_encode($row);

mysql_free_result($result);
mysql_close($mcon);

?>
