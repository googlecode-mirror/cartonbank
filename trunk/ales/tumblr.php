<?php
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,"http://cartoonbankmedia.tumblr.com/api/read/json");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
$result = curl_exec($ch);
curl_close($ch);

$result = str_replace("var tumblr_api_read = ","",$result);
$result = str_replace(';','',$result);
$result = str_replace('\u00a0','&amp;nbsp;',$result);

$jsondata = json_decode($result,true);
$posts = $jsondata['posts'];

echo '<div style="width:500px;">';
foreach($posts as $post){
$alttext=substr($post['photo-caption'],0,-4);
$alttext=substr($alttext,4);
echo '<div style="margin-top:1em;border:thin silver solid;width:500px;"><a href="'.$post['photo-url-1280'].'" target="_blank"><img src="'.$post['photo-url-500'].'" border="0" alt="'.$alttext.'"/></a></div>';
}
echo '</div>';
?>