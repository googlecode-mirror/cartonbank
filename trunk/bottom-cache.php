<?php
// Cache the contents to a file
if (count($_POST)==0 && $_GET['page_id']!=30 && $_GET['page_id']!=31 && $_GET['page_id']!=32) {
    //don't cache for cases above
    $cached = fopen($cachefile, 'w');
    fwrite($cached, ob_get_contents());
    fclose($cached);
}

if ($_GET['page_id']!=31) {
ob_end_flush(); // Send the output to the browser
}

?>