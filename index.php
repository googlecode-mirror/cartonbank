<?php
/*
global $time_start, $time_start, $ttt, $pred_password;
$time_start = microtime(true);
$prevtime = $time_start;
$t1=$time_start;
$ttt = '';

function timerdef($breakpoint){
    global $ttt, $time_start, $prevtime;
    $time_end = microtime(true);
    $timedif = round($time_end-$prevtime,4);
    if ($timedif>=0.0001)
    {
        $timedif = '<font size="+1" color="red"><u>'.$timedif.'</u></font>';
        $ttt = $ttt . " <br>".$breakpoint.": "  . ($time_end - $time_start). " [".$timedif."] ";
   } 
   else
   {
       //$ttt = $ttt . "<br>".$breakpoint;
   }
    $prevtime = $time_end;
}

timerdef('start');
*/

//ales

if ($_GET['page_id']!=31) {

    $url = $_SERVER['QUERY_STRING'];
    if ($url == ''){
        //$url = 'page_id=29&offset=0&new=0';
		header("Location: http://".$_SERVER['HTTP_HOST']."?page_id=29&offset=0&new=0");
    }
    $cache_file_to_include = 'index_'.$url;
    include('top-cache.php'); 
    $cachefile = dirname(__FILE__).'/mycache/cached-'.$cache_file_to_include.'.html';
    
    if ($_GET['page_id']!=30 && $_GET['page_id']!=31 && count($_POST)==0 && file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
        return;
    }
    
}

    
///ales
    
    
//include('top-cache.php'); 

/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
require('./wp-blog-header.php');

//timerdef('finish');
//$time_end = microtime(true);
//$time = $time_end - $t1;
//echo "<pre><p align=left>$ttt сек.</p></pre>";
//echo "<pre><p align=left>Total: $time сек.</p></pre>";


include('bottom-cache.php');

?>