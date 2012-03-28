<html>
 <head>
  <title> Быстрый поиск? </title>
 </head>

 <body>

<form method="get" action="sphinx.php">
	<input type="text" name="q" value="<?$_GET['q'];?>">
	<input type="submit">
</form>

<?php 
if (isset($_GET['q']))
{
	$sword = trim($_GET['q']);
}
else
{
	$sword = 'муж';
}



$id_list = ssearch ($sword);
//echo $id_list."<br>";

include("config.php");

$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);

$sql = "SELECT id, image, name, description, additional_description, votes_rate FROM wp_product_list WHERE id in (".$id_list.") and active=1 and approved=1 ORDER BY votes_rate DESC LIMIT 20";

        if (!($res = mysql_query($sql))) {die('Invalid query: ' . mysql_error());}

$count=1;
        while ($row = mysql_fetch_array ($res)) {
            print ("<div style='font-size:12px; width: 142px;height: 300px; float: left; margin: 4px; padding: 4px; text-align: left; display:block; border:thin solid silver;clear: none;'>#".$count." id: ".$row['id']." rate:".$row['votes_rate']." <img src='http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/images/".$row['image']."'>".$row['name']." | ".$row['description']." | ".$row['additional_description']."</div>");
			$count++;
        }

mysql_close($link);
 

	
function ssearch($words){
    $hostname = "127.0.0.1:9306";
    $user = "mysql";

    $bd = mysql_connect($hostname, $user) or die("Could not connect database. ". mysql_error());

    //$searchQuery = "select id from wp1i where match('$words') OPTION  ranker=matchany, max_matches=1000";
	$searchQuery = "select id from wp1i where match('$words') LIMIT 15000 OPTION  ranker=matchany, max_matches=1000";

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
 </body>
</html>
