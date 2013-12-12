<?php
//ales
function product_display_paginated($search_sql = '', $offset, $items_on_page)
{
    global $wpdb, $colorfilter, $totalitems,$siteurl,$sword;
    $siteurl = get_option('siteurl');
    $javascript_functions ='';
    

    

    $product_list = $GLOBALS['wpdb']->get_results($search_sql,ARRAY_A);
    
    //$totalitems =  count($product_list);

      if($product_list != null)
      {
          $preview_mode=1; // display as popup window
 //          $preview_mode=0; // display as Lightbox slideshow
          $output = "<div id='items' class='items'>";
          $counter = 0;
          foreach($product_list as $product)
          {
              if(($product['image'] !=null) and ($counter < $items_on_page))
                {
                  $imagedir = ABSPATH."wp-content/plugins/wp-shopping-cart/product_images/";
                  $image_size = @getimagesize($imagedir.$product['image']);
                  $image_link = "index.php?productid=".$product['id']."&width=".$image_size[0]."&height=".$image_size[1]."";
 
    // thumbs output

    $output .= "<div id='item".$counter."' class='item'>"; // start item

    //$vstavka = "jQuery('#bigpic').html('<img src=\'".$siteurl."/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."\'>');";
    $vstavka = "jQuery('#bigpic').html('<img src=\'http://sl.cartoonbank.ru/".$product['image']."\'>');";

    // here we prepare data for the BIGPIC preview

    if(stristr($product['image'], 'jpg') != FALSE) {
        $_file_format = 'jpg';
    } 
    if(stristr($product['image'], 'gif') != FALSE) {
        $_file_format = 'gif';
    } 
    if(stristr($product['image'], 'png') != FALSE) {
        $_file_format = 'png';
    } 
    
    $_number = $product['id'];
    
    $_description = htmlspecialchars_decode(nl2br(hilite(htmlspecialchars(stripslashes($product['description']),ENT_QUOTES))));
    $_size_warning='';


    if (isset($product['brandid']))
        {$_brandid = $product['brandid'];}
    else {$_brandid = '';}
    if (isset($product['category_id']))
        {$_category_id = $product['category_id'];}
    else {$_category_id = '';}

    $_author = "<a href=\'".$siteurl."/?page_id=29&brand=".$_brandid."\'>".$product['brand']."</a>";//$product['brand'];
    //$_name = hilite(nl2br(htmlspecialchars(stripslashes($product['name']),ENT_QUOTES)));
    $_name = nl2br(stripslashes($product['name']));

    $_avatarurl = ""; //"<a href=\"".get_option('siteurl')."/?page_id=29&brand=$_brandid\"><img src=".$product['avatarurl']." width=32 height=32 align=top border=0></a>";


    $_category = "<a href=\'".get_option('product_list_url')."&category=".$_category_id."\'>".$product['kategoria']."</a>";
    //$options .= "<a href='".get_option('product_list_url')."/&category=".$option['id']."'>".stripslashes($option['name'])."</a><br />";
 
 $product['additional_description'] = stripslashes ($product['additional_description']);
 $product['additional_description'] = htmlspecialchars($product['additional_description'],ENT_QUOTES);
 
 $_tags = hilite(nl2br($product['additional_description']));
 
 if (isset($product['sold'])){
     $psold = $product['sold'];
 }
 else{
     $psold = 0;
 }
 
 if ($psold==0){
     $_sold="";
 }
 else{
    $_sold = "<h2><b>Продажи:</b> <span class=\'sold\'>".$psold."</span></h2><br />";
 }
 
    //$_tags = hilite(nl2br(htmlspecialchars(stripslashes($product['additional_description']))));
    
    //$_tags = hilite($product[0]['additional_description']);

    $_bigpicimgalt = addslashes($_name.", ".$product['brand']);
    $_bigpicimgtitle = addslashes($_name." в категории ".$product['kategoria'].", ".$product['brand']);
        //."". ".$_description.". ".$_tags);

    $_tags_array = explode(',',$_tags);
        //$i=0;
        foreach ($_tags_array as $key => $value)
        {
            $_tags_array[$key] = "<li><a href=\'".get_option('siteurl')."/?page_id=29&cs=".trim($_tags_array[$key])."\'>".trim($_tags_array[$key])."</a></li>";
        }
    $_tags_imploded = implode(" ", $_tags_array);
    $_tags = $_tags_imploded;

    $_sharethis_html = "<div id=\'share_this\' class=\'lh2\'></div>";

    $_dimensions_html = "<div id='dimensions'><img src='".get_option('siteurl')."/img/ldng.gif' alt='loading'></div>";
    $_dimensions_html = str_replace("\"","\'",$_dimensions_html);
    $_dimensions_html = str_replace("'","\'",$_dimensions_html);


    $_rating_html = "<div id='star_rating'><img src='".get_option('siteurl')."/img/ldng.gif' alt='loading'></div>";
    $_rating_html = str_replace("\"","\'",$_rating_html);
    $_rating_html = str_replace("'","\'",$_rating_html);



    if (current_user_can('manage_options'))
                {
                    $_edid = "";
                    //$_edid = " <a href=".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-items.php&edid=".$_number."  target=_blank><img border=0 src=".get_option('siteurl')."/img/edit.jpg title=\'открыть редактор\'></a> <a href=".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-items.php&updateimage=".$_number." target=_blank><img border=0 src=".get_option('siteurl')."/img/reload.gif title=\'обновить водяной знак\'></a> <a href=".get_option('siteurl')."/ales/wordassociations/words.php?id=".$_number." target=_blank><img border=0 src=".get_option('siteurl')."/img/tags.gif title=\'добавить тэгов\'></a>";
                }
                else
                {
                    $_edid = "";
                }

$current_user = wp_get_current_user();
//    $_SESSION['id']= $current_user->ID;
//    setcookie('uid', $_SESSION['id']);

    //pokazh($_COOKIE,"cookie");
    //pokazh ($_SESSION,"session");
    //pokazh ($current_user);
    ///pokazh($_SERVER);

if (is_user_logged_in())
{
    $logged = true; //" залогинен ";
    $klop = "";//"_";
}
else
{
    $logged = false; //" не залогинен ";
    $klop = ""; //"|";
}

    if ($logged)
    {
        //$_bigpicstrip = "<div style=\'float:left;\'><b>Название: </b><h1>" .$_name."</h1>&nbsp;$klop<span id=\'thumb\' onclick=\'fave_it();\'>$klop<img src=\'".$siteurl."/img/thumbupp.jpg\' border=0 title=\'добавить в любимое\'></span></div> "."<div>№&nbsp;<a id=\'cuid\' title=\'".$product['kategoria'].", ".$_name.", ".$product['brand']."\' href=\'".get_option('siteurl')."/?page_id=29&cartoonid=".$_number."\'>".$_number."</a>&nbsp;<b>".$_author."</a></b></div>";
        $_bigpicstrip = "<div class=\'fll\'><b>Название: </b><h3>" .$_name."</h3>&nbsp;$klop</div> "."<div>№&nbsp;<a id=\'cuid\' title=\'".$product['kategoria'].", ".$_name.", ".$product['brand']."\' href=\'".get_option('siteurl')."/?page_id=29&cartoonid=".$_number."\'>".$_number."</a>&nbsp;<b>".$_author."</a></b></div>";
    }
    else
    {
        $_bigpicstrip = "<div class=\'fll\'><b>Название: </b><h3>" .$_name."</h3> $klop</div> "."<div>№&nbsp;<a id=\'cuid\' title=\'".$product['kategoria'].", ".$_name.", ".$product['brand']."\' href=\'".get_option('siteurl')."/?page_id=29&cartoonid=".$_number."\'>".$_number."</a>&nbsp;<b>".$_author."</a></b></div>";
    }

    $_bigpictext = "<h2><b>Категория: </b>".$_category."</h2><h2><b>Описание: </b></h2> ".$_description."<br /><h2><b>Тэги: </b></h2><ul>".$_tags."</ul><h2><b>Ссылка:</b></h2><a title=\'".$product['kategoria'].", ".$_name.", ".$product['brand']."\' href=\'".get_option('siteurl')."/?page_id=29&cartoonid=".$_number."\'> №&nbsp;".$_number."</a><br /><b>Размер:</b><br />".$_dimensions_html.$_sold."<b>Оценка:</b><br />".$_rating_html.$_sharethis_html.$_edid;
    
    $_bigpic =  "<img src=\'http://sl.cartoonbank.ru/".$product['image']."\' border=0 alt=\'".$_bigpicimgalt."\' title=\'".$_bigpicimgtitle."\' />";

    if($product['l1_price']=='0') {$l1_disabled = 'disabled=true';} else {$l1_disabled = '';}
    if($product['l2_price']=='0') {$l2_disabled = 'disabled=true';} else {$l2_disabled = '';}
    if($product['l3_price']=='0') {$l3_disabled = 'disabled=true';} else {$l3_disabled = '';}

    
    if($psold==0){
        $_soldd = '';
    }
    else{
        $_soldd = '<div class="cntrsold" title="продажи">'.$psold.'</div>';
    }
    
    
    
if (isset($product['not_for_sale']) && $product['not_for_sale']=='1')
{
    $_bottomstriptext = "Лицензии на это изображение временно недоступны";
}
else
{
    // отключить лицензию
    if(isset($product['l1_price']) && $product['l1_price'] != 0)
        {$l1_price_text = "<td class=\'vamtar\'><b>".round($product['l1_price'])."&nbsp;руб.</b></td>";}
    else
        {$l1_price_text = "<td class=\'vamtar\'>не доступна</td>";}

    if(isset($product['l2_price']) && $product['l2_price'] != 0)
        {$l2_price_text = "<td class=\'vamtar\'><b>".round($product['l2_price'])."&nbsp;руб.</b></td>";}
    else
        {$l2_price_text = "<td class=\'vamtar\'>не доступна</td>";}

    if(isset($product['l3_price']) && $product['l3_price'] != 0)
        {$l3_price_text = "<td class=\'vamtar\'><b>".round($product['l3_price'])."&nbsp;руб.</b></td>";}
    else
        {$l3_price_text = "<td class=\'vamtar\'>не доступна</td>";}
if ($_brandid==1 || $_brandid==6 || $_brandid==8){
    //$printdirect = "<div class=\'prdrrdr\'>.</div>";
	$printdirect = "<div class=\'fll\'><a onclick=\"prdrrdr(".$product['id'].");\";);\" rel=\'nofollow\' href=\'#\'><img src=\'/img/tshirt.jpg\' title=\'Закажите этот рисунок на кружке, футболке или другом сувенире\' alt=\'t-shirt\'></a></div><div class=\'mrktimg\'><a onclick=\"prdrrdr(".$product['id'].");\";);\" rel=\'nofollow\' href=\'#\'>Заказать сувенир<br>с этим рисунком</a>.</div>";
}
else{
    $printdirect = "";
}

$_bottomstriptext = $printdirect.$_size_warning."<div class=\'w4fr\'><form name=\'licenses\' id=\'licenses\' onsubmit=\'submitform(this);return false;\' action=\'".get_option('siteurl')."/?page_id=29\' method=\'POST\'><table class=\'licenses\'> <tr> <td class=\'wh w8vb\'><b>Выбор</b></td> <td class=\'wh tal\'><input type=\'radio\' name=\'license\' $l1_disabled value=\'l1_price\'></td> ".$l1_price_text." <td rowspan=\'2\' class=\'w20\'>&nbsp;</td> <td class=\'wh\' class=\'tal\'><input type=\'radio\' name=\'license\' $l2_disabled value=\'l2_price\'></td> ".$l2_price_text." <td rowspan=\'2\' class=\'w20\'>&nbsp;</td> <td class=\'wh\' class=\'tal\'><input type=\'radio\' name=\'license\' $l3_disabled value=\'l3_price\'></td> ".$l3_price_text." <td rowspan=\'2\' class=\'wh\' class=\'w8tarvab\'><input id=\'searchsubmit\' value=\'заказать\' type=\'submit\' class=\'buy\' title=\'Добавить рисунок в корзину заказов\'></td> </tr> <tr> <td class=\'wh vat\'><b>лицензии:</b></td> <td colspan=\'2\' class=\'pl6\'><a target=\'_blank\' href=\'".get_option('siteurl')."/?page_id=238\' title=\'подробнее об ограниченной лицензии\'>ограниченная</a></td> <td colspan=\'2\' class=\'pl6\'><a target=\'_blank\' href=\'".get_option('siteurl')."/?page_id=242\' title=\'подробнее о стандартной лицензии\'>стандартная</a></td> <td colspan=\'2\' class=\'pl6\'><a target=\'_blank\' href=\'".get_option('siteurl')."/?page_id=245\' title=\'подробнее об расширенной лицензии\'>расширенная</a></td> </tr> </table><input type=\'hidden\' value=\'".$_number."\' name=\'prodid\'> </form></div>";
}

    $_next_item = $counter + 1;
    if ($_next_item == 20)
        {
            $vstavka = "jQuery('#bigpic').html('<a title=\"следующая страница > \" href=\"".get_option('siteurl')."/cartoon/".$_number."\" onclick=\"next_page();return false;\">".$_bigpic."</a>');";
        }
        else
        {
            $vstavka = "jQuery('#bigpic').html('<a title=\"следующее изображение > \" href=\"".get_option('siteurl')."/cartoon/".$_number."\" onclick=\"get_item". $_next_item ."();return false;\">".$_bigpic."</a>');";
        }

    $vstavka .= "jQuery('#bigpictext').html('".$_bigpictext."');";
    $vstavka .= "jQuery('#bigpictopstrip').html ('".$_bigpicstrip."');";
	$vstavka .= "jQuery('#hone').html('<h1>Карикатура «".$_name."», ".$_author."</h1>');";
    $vstavka .= "jQuery('#bigpicbottomstrip').html ('".$_bottomstriptext."');";

    $output .= "<a href=\"".get_option('siteurl')."/cartoon/".$_number."\" onclick=\"get_item". ($_next_item - 1) ."();return false;\">";

    $jq_stars = ' get_5stars(); ';

    $jq_dimensions = ' get_dimensions(); ';

    $share_this = ' get_share_this(); ';

    $add_hash_2url = ' change_url(); ';

    $get_favorite = ' get_fave(); ';
    
    if (isset($sword)&&$sword!=''){
        //$highlight = " highlight(\"".$sword."\"); ";
         $highlight = " highlight(\"".htmlentities($sword, ENT_QUOTES)."\"); ";
    }
    else{$highlight = "";}
    

    $javascript_functions .= " function get_item".$counter."() { jQuery('#pt')[0].scrollIntoView(true);".$vstavka.$highlight." scrpts(); } "; 
    $_offset = $offset + 20;
    $javascript_functions .=' function next_page(){window.location = "'.get_option('siteurl').'/?'.get_url_vars().'offset='.$_offset.'";    var cuid = jQuery("#cuid").html(); jQuery("#navbar").html(cuid);}';

    
    $fiilename =ABSPATH.'/wp-content/plugins/wp-shopping-cart/images/'.$product['image'];

                    if (file_exists($fiilename))
                    {
                              $output .= "<img src='http://th.cartoonbank.ru/".$product['image']."' title='".$product['name']."' alt='".$_bigpicimgalt."' width='140' height='140' class='thumb' />";
                    }
                    else
                    {
                              $output .= "<img src='http://th.cartoonbank.ru/icon-rest.gif' width='140' height='140' class='thumb' />";
                    }
                      $output .= "</a>";

                    $output .= $_soldd."</div>"; // stop item
                }
                $counter = $counter + 1;
          }
          $output .= "</div>";
          
          $pagination = pagination($offset,$totalitems,$items_on_page);          
          return "<script>get_item0();".$javascript_functions. "</script>" . " " . $pagination . $output;
  }

  // end function output first page
}
/// ales

function get_random_image(){
    global $wpdb, $colorfilter;
    $sql = "SELECT `wp_product_list`.`id` FROM `wp_product_list` WHERE `active`='1' ".$colorfilter.$exclude_category_sql.$approved_or_not." AND `visible`='1' ORDER BY RAND(NOW()) LIMIT 1";
    $product_list = $wpdb->get_results($sql,ARRAY_A);
 }

function pagination($offset,$totalitems,$items_on_page){
     // PAGINATION
     $page_id = $_GET['page_id'];
     
     $page = round($offset/$items_on_page)+1;

        
        if (isset($_REQUEST['new']) && $_REQUEST['new']!=1 )
        {
            $totalitems = 400;
        }
        
        
        $limit = $items_on_page;
            if (isset($_GET['category'])&&is_numeric($_GET['category'])) {$catid=$_GET['category'];}else{$catid='';}

        // Brand filter
        if (isset($_GET['brand']) && is_numeric($_GET['brand']))
        {
            $_brand = $_GET['brand'];
            $brand_group_sql = "&amp;brand=".$_brand;
        }
        elseif (isset($_POST['brand']) && is_numeric($_POST['brand']))
        {
            $_brand = $_POST['brand'];
            $brand_group_sql = "&amp;brand=".$_brand;
        }
        else
        {
            $_brand = '';
            $brand_group_sql = '';
        }


    
    //$_pages_navigation = getPaginationString($page, $totalitems, $limit, $adjacents = 1, $targetpage = get_option('siteurl'), $pagestring = "?page_id=29".$newfilter.$brand_group_sql."&amp;color=".$color.$__category.$_url_cs.$_url_cs_exact.$_url_cs_any.$_url_cs_exclude.$_url_666."&amp;offset=", $filter_list, $new, $brand_group_sql,$_category,$color_url);
    
    $_pages_navigation = getPaginationString($page, $totalitems, $limit, $adjacents = 1, $targetpage = get_option('siteurl'), $pagestring = "?".get_url_vars_amp2()."offset=");
    

    if (isset($_GET['cartoonid']) && is_numeric($_GET['cartoonid']))
    {
        $output = "";
    }
    else
    {
        $output = "<div style='clear:both;'>".$_pages_navigation."</div>";
    }
    return $output;
}

function get_url_vars(){
    $output = '';
    foreach($_REQUEST as $key=>$val)
    {
        if ($key!='offset')
        $output .= $key."=".urlencode($val)."&";
    }
    return $output;
    //return htmlentities($output);
}

function get_url_vars_amp2(){
    $output = '';
    foreach($_REQUEST as $key=>$val)
    {
        if ($key!='offset')
        $output .= $key."=".urlencode($val)."&amp;";
    }
    return $output;
    //return htmlentities($output);
}

function add_or_change_parameter($parameter='', $value='') 
 { 
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
 }

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
}

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
            $button_sort = "<a href='".$siteurl."/".add_or_change_parameter('new','0')."' style='border:0px; padding:4px; color:#6C6C6C; background-color:#bfccf8;'>показать избранное</a>";
        }
        else
        {
            $button_sort = "<a href='".$siteurl."/".add_or_change_parameter('new','1')."' style='border:0px; padding:4px; color:#6C6C6C; background-color:#bfccf8;'>сортировать по дате</a>";
        }


        if (isset($_REQUEST['cs'])&&$_REQUEST['cs']!=''){$filter_list = $_REQUEST['cs'];}else{$filter_list = '';}
        
        if ($page < $counter - 1) 
            $pagination .= "<a href=\"" . $targetpage . $pagestring . ($next*$limit - $limit) . "\">»</a>";
        else
            $pagination .= "<span class=\"disabled\">»</span>";
        if (isset($filter_list)&&$filter_list=='')
            $pagination .= " Всего: ".$totalitems. "<div class='flr'>".$button_sort."</div></div>";
        else
            $pagination .= " Всего: ".$totalitems. " рис.&nbsp;<b>Фильтр</b>: <span style='color:#CC3399;'>".stripslashes($filter_list)."</span><div class='flr'>".$button_sort."</div></div>";
    }//if($lastpage > 1)
    if ($totalitems < 20){
    if (isset($_REQUEST['cs'])&&$_REQUEST['cs']!=''){$filter_list = $_REQUEST['cs'];}else{$filter_list = '';}
    $pagination .= " Всего найдено: ".$totalitems. ".&nbsp;<b>Фильтр</b>: <span style='color:#CC3399;'>".$filter_list."</span>";
    }

    return $pagination;

}//function getPaginationString

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
