<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
require_once('../wp-config.php');
require_once('group_post.php');

printf("%s\r\n", date("m.d.y H:m:s"));

$mcon = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);

if (!$mcon)
{
    printf("Could not connect: %s\r\n" . mysql_error());
    die();
}

$db_selected = mysql_select_db(DB_NAME, $mcon);

if (!$db_selected)
{
    printf("Could not select db: '%s' (%s)", DB_NAME, mysql_error());
    die();
}

$sql = "SELECT *, max(rate) as maxrate FROM " .
			"(SELECT pl.id, " .
				"pl.image as image, " .
				"pl.description as descr, " .
				"pl.additional_description as tags, " .
				"pl.name AS title, " .
				"pb.name AS author, " .
				"pl.votes_rate AS rate, " .
				"pl.brand AS brand " .
			"FROM wp_product_list as pl, wp_product_brands as pb " .
			"WHERE pl.brand = pb.id " .
				"AND pl.brand != 3 " .
				"AND pl.brand != 13 " .
				"AND pl.active = 1 " .
				"AND pl.visible = 1 " .
				"AND pl.vk_uploaded = 0 " .
			"ORDER BY rate DESC " .
			"LIMIT 100) grouped " .
		"GROUP BY grouped.author " .
		"ORDER BY maxrate DESC " .
		"LIMIT 1";

$result = mysql_query($sql, $mcon);

if (!$result)
{
    printf("Could not run query: %s\r\n", mysql_error($mcon));
    die();
}

$ckfile = tempnam(PATH_TO_TEMP, "CURLCOOKIE");

$token = auth();
if (strlen($token) > 0)
{

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		printf('<br>id: %s<br />image: %s<br />descr: %s<br />tags: %s<br />title: %s<br />author: %s<br />rate: %s<br>brand: %s<br />',
			   $row["id"], $row["image"], $row["descr"], $row["tags"], $row["title"], $row["author"], $row["rate"], $row["brand"]);

		if (upload($row) == true)
		{
			$sql = "UPDATE `wp_product_list` SET `vk_uploaded` = 1 WHERE `id` = " .$row["id"];
			$res = mysql_query($sql);
			if (!$res)
			{
				printf("Could not update vk_uploaded field: %s\n", mysql_error($mcon));
				exit;
			}
		}
		sleep(1); // vkontakte have restriptions on posts to seconds
	}
	mysql_free_result($result);
}
unlink($ckfile);

mysql_close($mcon);

printf("%s\r\n", date("m.d.y H.m.s"));

?>