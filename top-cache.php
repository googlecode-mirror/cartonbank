<?php
$file = $cache_file_to_include;;
if ($file==''){$file='home';}
$thisdir = dirname(__FILE__);
$cachefile = $thisdir.'/mycache/cached-'.$file.'.html';
$cachetime = 18000;

// Serve from the cache if it is younger than $cachetime
if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
    include($cachefile);
    return;
}
$obstarted = TRUE;
ob_start(); // Start the output buffer
?>