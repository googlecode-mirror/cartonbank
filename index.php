<?php
if (count($_POST)==0 && $_GET['page_id']!=31) {

    $url = $_SERVER['QUERY_STRING'];
    if ($url == ''){
        $url = 'page_id=29&new=2';
        $_GET['page_id']='29';
        $_SERVER['QUERY_STRING'] = $url;
        require('./index.php');
        return;
        //header("Location: http://".$_SERVER['HTTP_HOST']."?page_id=29&offset=0&new=2");
    }
    $cache_file_to_include = 'index_'.$url;
    include('top-cache.php'); 
    $cachefile = dirname(__FILE__).'/mycache/cached-'.$cache_file_to_include.'.html';
    
    if ($_GET['page_id']!=30 && $_GET['page_id']!=31 && count($_POST)==0 && file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
        return;
    }
}
define('WP_USE_THEMES', true);

require('./wp-blog-header.php');

include('bottom-cache.php');
?>