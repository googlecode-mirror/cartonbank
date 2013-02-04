<?php
$file = $cache_file_to_include;;
if ($file==''){$file='home';}
$cachefile = '/home/www/cb3/mycache/cached-'.$file.'.html';
//$cachefile = '/home/www/cb/mycache/cached-'.$file.'.html';
$cachetime = 18000;

// Serve from the cache if it is younger than $cachetime
if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
    include($cachefile);
    return;
}
$obstarted = TRUE;
ob_start(); // Start the output buffer
?>>