<?php
function ssearch($words){
    $hostname2 = "127.0.0.1:9306";
    $user2 = "mysql";

    $bd2 = mysql_connect($hostname2, $user2) or die("Could not connect database. ". mysql_error());

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
    
    mysql_close($bd2);

    return $id_list;
}

function cleanuptags($tags){
    $tags = str_replace(",",", ",$tags);
    $tags = str_replace("  "," ",$tags);
    $tags = mb_convert_case($tags, MB_CASE_LOWER, "UTF-8"); 
    return $tags;
}

?>
