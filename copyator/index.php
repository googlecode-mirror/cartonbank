<?php 

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
require_once('../wp-config.php');
require_once('functions.php');

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

$result = mysql_query("SELECT * FROM `wp_product_files` WHERE `uploaded`=0", $mcon);

if (!$result)
{
    printf("Could not run query: %s\r\n", mysql_error());
    die();
}

ftp_copy_init();

while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
{
    printf("\nid: %s\nfilename: %s\nmimetype: %s\nidhash: %s\ndate: %s\n", $row["id"], $row["filename"], $row["mimetype"], $row["idhash"], $row["date"]);
    $darray = getdate($row["date"]);
    $res = copy_file_to_ftp($row["idhash"], $darray['year'], $darray['month']);
    if ($res)
    {
	// File is uploded - uncheck flag
	$result_upd = mysql_query("UPDATE `wp_product_files` SET `uploaded` = 1 WHERE `id` = " . $row["id"]);
	if (!$result_upd)
	{
	    printf("Could not update flag after upload: %s\r\n", mysql_error());
	    die();
	}
    }
}

ftp_copy_end();

mysql_close($mcon);

printf("%s\r\n", date("m.d.y H.m.s"));

?>