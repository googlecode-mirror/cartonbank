<?

function getPaginationString($page = 1, $totalitems, $limit = 20, $adjacents = 1, $targetpage = "/", $pagestring = "?page=")
{        
    //function to return the pagination string
    global $siteurl;
    $filter_list='';

    // if the image is single set totalitems to 1
    if (isset($_GET['cartoonid']) && is_numeric($_GET['cartoonid']))
    {$totalitems = 1;}

    //defaults
    if(!$adjacents) $adjacents = 1;
    if(!$limit) $limit = 20;
    if(!$page) $page = 1;
    if(!$targetpage) $targetpage = "/";
    
    //other vars
    $prev = $page - 1;                                    //previous page is page - 1
    $next = $page + 1;                                    //next page is page + 1
    $lastpage = ceil($totalitems / $limit);                //lastpage is = total items / items per page, rounded up.
    $lpm1 = $lastpage - 1;                                //last page minus 1
    
    /* 
        Now we apply our rules and draw the pagination object. 
        We're actually saving the code to a variable in case we want to draw it more than once.
    */
    $pagination = "";
    $margin = '0px';
    $padding = '0px';
    if($lastpage > 1)
    {    
        $pagination .= "<div class='pagination' style='padding-top:4px;'>";

        //previous button
        if ($page > 1) 
            $pagination .= "<a href=\"".$targetpage. $pagestring. ($prev*$limit - $limit). "\">«</a> Страница: ";
        else
            $pagination .= "<span class=\"disabled\">«</span> Страница: ";    
        
        //pages    
        if ($lastpage < 7 + ($adjacents * 2))    //not enough pages to bother breaking it up
        {    
            for ($counter = 1; $counter <= $lastpage; $counter++)
            {
                if ($counter == $page)
                    $pagination .= "<span class=\"current\">$counter</span>";
                else
                    $pagination .= "<a href=\"" . $targetpage . $pagestring . ($counter*$limit - $limit) . "\">$counter</a>";                    
            }
        }
        elseif($lastpage >= 7 + ($adjacents * 2))    //enough pages to hide some
        {
            //close to beginning; only hide later pages
            if($page < 1 + ($adjacents * 3))        
            {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                {
                    if ($counter == $page)
                        $pagination .= "<span class=\"current\">$counter</span>";
                    else
                        $pagination .= "<a href=\"" . $targetpage . $pagestring . ($counter*$limit - $limit) . "\">$counter</a>";                    
                }
                $pagination .= "<span class=\"elipses\">...</span>";
                $pagination .= "<a href=\"" . $targetpage . $pagestring . ($lpm1*$limit - $limit) . "\">$lpm1</a>";
                $pagination .= "<a href=\"" . $targetpage . $pagestring . ($lastpage*$limit - $limit) . "\">$lastpage</a>";        
            }
            //in middle; hide some front and some back
            elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
            {
                $pagination .= "<a href=\"" . $targetpage . $pagestring . "0\">1</a>";
                $pagination .= "<a href=\"" . $targetpage . $pagestring . "20\">2</a>";
                $pagination .= "<span class=\"elipses\">...</span>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                {
                    if ($counter == $page)
                        $pagination .= "<span class=\"current\">$counter</span>";
                    else
                        $pagination .= "<a href=\"" . $targetpage . $pagestring . ($counter*$limit - $limit) . "\">$counter</a>";                    
                }
                $pagination .= "...";
                $pagination .= "<a href=\"" . $targetpage . $pagestring . ($lpm1*$limit - $limit) . "\">$lpm1</a>";
                $pagination .= "<a href=\"" . $targetpage . $pagestring . ($lastpage*$limit - $limit) . "\">$lastpage</a>";        
            }
            //close to end; only hide early pages
            else
            {
                $pagination .= "<a href=\"" . $targetpage . $pagestring . "0\">1</a>";
                $pagination .= "<a href=\"" . $targetpage . $pagestring . "20\">2</a>";
                $pagination .= "<span class=\"elipses\">...</span>";
                for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination .= "<span class=\"current\">$counter</span>";
                    else
                        $pagination .= "<a href=\"" . $targetpage . $pagestring . ($counter*$limit - $limit) . "\">$counter</a>";                    
                }
            }
        }
        

        //next button
        if (isset($_REQUEST['new']) && $_REQUEST['new']==1)
        {
            $button_sort = "<a href='".$siteurl."/?".add_or_change_parameter('new','0')."' style='border:0px; padding:4px; color:#6C6C6C; background-color:#bfccf8;'>показать избранное</a>";
        }
        else
        {
            $button_sort = "<a href='".$siteurl."/?".add_or_change_parameter('new','1')."' style='border:0px; padding:4px; color:#6C6C6C; background-color:#bfccf8;'>сортировать по дате</a>";
        }


        if (isset($_REQUEST['cs'])&&$_REQUEST['cs']!=''){$filter_list = $_REQUEST['cs'];}else{$filter_list = '';}
        
        if ($page < $counter - 1) 
            $pagination .= "<a href=\"" . $targetpage . $pagestring . ($next*$limit - $limit) . "\">»</a>";
        else
            $pagination .= "<span class=\"disabled\">»</span>";
        if (isset($filter_list)&&$filter_list=='')
            $pagination .= " Всего: ".$totalitems. "<div style='float:right;'>".$button_sort."</div></div>";
        else
            $pagination .= " Всего: ".$totalitems. " рис.&nbsp;<b>Фильтр</b>: <span style='color:#CC3399;'>".stripslashes($filter_list)."</span><div style='float:right;'>".$button_sort."</div></div>";
    }//if($lastpage > 1)
    if ($totalitems < 20){
    if (isset($_REQUEST['cs'])&&$_REQUEST['cs']!=''){$filter_list = $_REQUEST['cs'];}else{$filter_list = '';}
    $pagination .= " Всего найдено: ".$totalitems. ".&nbsp;<b>Фильтр</b>: <span style='color:#CC3399;'>".$filter_list."</span>";
    }

    return $pagination;

}//function getPaginationString


function get_url_vars(){
    $output = '';
    foreach($_REQUEST as $key=>$val)
    {
        if ($key!='offset')
        $output .= $key."=".urlencode($val)."&";
    }
    return $output;
}///get_url_vars()


function add_or_change_parameter($parameter='', $value='') { 
  $params = array(); 
  $output = "?"; 
  $firstRun = true; 
  foreach($_GET as $key=>$val) 
  { 
   if($key != $parameter) 
   { 
    if(!$firstRun) 
    { 
     $output .= "&"; 
    } 
    else 
    { 
     $firstRun = false; 
    } 
    $output .= $key."=".urlencode($val); 
   } 
  } 
  if(!$firstRun) 
   $output .= "&"; 
  $output .= $parameter."=".urlencode($value); 
  //return $output; 
  return htmlentities($output); 
} ///add_or_change_parameter

function get_results ($sql,$key_column=null){
    $result = mysql_query($sql);

    for ($array = array(); $row = mysql_fetch_assoc($result); isset($row[$key_column]) ? $array[$row[$key_column]] = $row : $array[] = $row);


    return $array;
} ///get_results 


function prepare_tags($tags_row){
    $_tags = hilite(nl2br(stripslashes($tags_row)));
    $_tags_array = explode(',',$_tags);
    foreach ($_tags_array as $key => $value){
        $_tags_array[$key] = "<li><a href='?cs=".trim($_tags_array[$key])."'>".trim($_tags_array[$key])."</a></li>";
    }
    $_tags_imploded = implode(" ", $_tags_array);
    $_tags = $_tags_imploded;
    return '<ul>'.$_tags.'</ul>';
}///prepare_tags

function hilite($string)
{
    global $aKeywords;
    if (count($aKeywords)==0)
    {return $string;}
    foreach ($aKeywords as $key => $value)
    {
        $string = preg_replace('/('.$value.')/ui', '<span class="hilite">$1</span>', $string); 
    }
    return $string;
}///hilite

function ssearch($words){
    $host = "127.0.0.1:9306";
    $usr = "mysql";

    $bd = mysql_connect($host, $usr) or die("Could not connect database. ". mysql_error());

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
$bd = mysql_connect($mysql_hostname, $mysql_user, $mysql_password) or die("Could not connect database");
mysql_select_db($mysql_database, $bd) or die("Could not select database");
mysql_set_charset('utf8',$bd);

    return $id_list;
} ///ssearch



?>
