<?
// reset votres and point in `wp_fsr_post`

include("config.php");
global $wpdb;

$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);


$result = mysql_query("select ID from `wp_fsr_post` order by ID asc LIMIT 9000, 2000");

if (!$result) {
                die('Invalid query: ' . mysql_error());
            }
$c=1;
while($row=mysql_fetch_array($result))
{
	$picture_id = $row['ID'];

	//  recalculate point and sum
	$_votes = mysql_query("SELECT COUNT( * ) FROM  `wp_fsr_user` WHERE post = $picture_id");
	$_points = mysql_query("SELECT SUM( points ) FROM  `wp_fsr_user` WHERE post = $picture_id");

	//pokazh($c);
	$_votes = mysql_result($_votes, 0);
	$_points = mysql_result($_points, 0);

	pokazh($c . ": <b>" . $picture_id. "</b> " . $_votes . "/" . $_points,"c: id _votes/points: ");
	//pokazh($_points,"_points");

	$sql = "UPDATE `wp_fsr_post` SET votes=$_votes, points=$_points WHERE ID={$picture_id};";

	//pokazh($sql,"sql");


	$rez = mysql_query($sql);
	++$c;
}


?>