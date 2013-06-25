<?php 

// configuration
include("/home/www/cb3/ales/config.php");

//open db connection
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);

$id = trim($_GET['p']);

$sql="
SELECT COUNT( c.prodid ) AS counter, c.prodid
FROM  `wp_cart_contents` AS c,  `wp_product_list` AS p, wp_download_status AS st, wp_purchase_logs as pl
WHERE p.id = c.prodid
AND st.purchid = c.purchaseid
AND c.prodid = p.id
AND st.fileid = p.file
AND pl.id = purchid
AND c.price >3
AND st.downloads <5
AND pl.user_id!='106'
GROUP BY c.prodid
ORDER BY counter DESC 
";

$result = mysql_query($sql);
if (!$result) {die('Invalid query: ' . mysql_error());}

while($row=mysql_fetch_array($result))
{
    $id = $row['prodid'];
    $counter = $row['counter'];

	$sql_update = "
	UPDATE wp_product_list SET sold = $counter WHERE id = $id
	";
	$result2 = mysql_query($sql_update);
	if (!$result2) {die('Invalid query: ' . mysql_error());}
}
mysql_close($link);
exit;
?>