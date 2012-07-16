<?php 
if (!isset($_REQUEST['offset'])||!isset($_REQUEST['mode']))
{
	echo ("sorry, no parameters provided");	exit;
}
if (isset($_REQUEST['lang'])&&($_REQUEST['lang'])=='en')
{
	$englishfilter = ' AND category=5 ';
}
else
{
	$englishfilter = '';
}

include("/home/www/cb3/ales/config.php");
$mode = $_REQUEST['mode'];
$name="";
$rate="";

if (isset($_REQUEST['offset']))
{
	$offset=mysql_real_escape_string($_REQUEST['offset']);
}

// best
if ($mode==1)
$sql = "SELECT id,name,image,image as mythumbnail FROM `wp_product_list` WHERE active=1 ".$englishfilter."AND visible=1 AND approved=1 ORDER BY `wp_product_list`.`votes_rate`  DESC limit ".$offset.",20";
//new
if ($mode==2)
$sql = "SELECT id,name,image,image as mythumbnail FROM `wp_product_list` WHERE active=1 ".$englishfilter."AND visible=1 AND approved=1 ORDER BY `wp_product_list`.`id`  DESC limit ".$offset.",20";
//search
if ($mode==3){
	//GET search id_list
	if (isset($_REQUEST['search'])&&!empty($_REQUEST['search'])){
		$sword = mysql_escape_string($_REQUEST['search']);
		//save_search_terms($sword);
		$id_list = ssearch ($sword);
		if (strlen($id_list)>3){
		$search_keywords_filter = " AND `wp_product_list`.`id` in (".$id_list.") ";}
		else {echo ""; exit;}
	}
		else {$search_keywords_filter ="";
	}
	$sql = "SELECT id, name, image, image as mythumbnail FROM `wp_product_list` WHERE active=1 ".$englishfilter."AND visible=1 AND approved=1 ".$search_keywords_filter." ORDER BY `wp_product_list`.`votes_rate` DESC limit 20";
}
//random
if ($mode==4)
$sql = "SELECT id,name,image,image as mythumbnail FROM `wp_product_list` WHERE active=1 ".$englishfilter."AND visible=1 AND approved=1 ORDER BY RAND() DESC limit ".$offset.",20";


//open db connection
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);

$res = mysql_query($sql);
if (!$res) {die('Invalid query: ' . mysql_error());}


for ($array = array(); $row = mysql_fetch_assoc($res); isset($row[$key_column]) ? $array[$row[$key_column]] = $row : $array[] = $row);

mysql_close($link);

$output = json_encode($array);
echo $output;
exit;

function ssearch($words){
    $hostname = "127.0.0.1:9306";
    $user = "mysql";
    $bd = mysql_connect($hostname, $user) or die("Could not connect database. ". mysql_error());
    $searchQuery = "select id from wp1i where match('$words') LIMIT 21 OPTION  ranker=matchany, max_matches=21";
    $result = mysql_query($searchQuery);
        if (!$result) {die('Invalid query: ' . mysql_error());}
    $id_list = "";
    while ($row = mysql_fetch_array ($result)) {
        $id_list .= $row['id'].", ";
    }
    if (strlen($id_list>2))
    $id_list = substr($id_list,0,-2);
    mysql_close($bd);
    return $id_list;
}



?>