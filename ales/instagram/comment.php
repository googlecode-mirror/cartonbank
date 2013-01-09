<?php
/*
set #russia:
http://ink361.com/proxy/media/[номеркартинки]/comments?callback=Request.JSONP.request_map.request_5&text=[тэги]
&access_token=[токен]

delete tag:
http://ink361.com/proxy/media/[номеркартинки]/comments/[номеркоммента]?callback=Request.JSONP.request_map.request_5&__method__=delete&access_token=[токен]
 
 GET http://ink361.com/proxy/media/252117228118728958_143724471/
 comments?callback=Request.JSONP.request_map.request_5&
 text=1111&access_token=143724471.2e7067c.59a7415eef274bd6a4131ac7cb436f5f HTTP/1.1
Host: ink361.com
Connection: keep-alive
 
*/
  $imagenum = '252117228118728958_143724471';
  $tags = '%23bestpictures';
  $token = '143724471.2e7067c.59a7415eef274bd6a4131ac7cb436f5f';
  
  $_SERVER['HTTP_REFERER']='http://ink361.com/';
  
  
  //if ($_GET['mode']=='set')
  //{
   $url = 'http://ink361.com/proxy/media/'.$imagenum.'/comments?callback=Request.JSONP.request_map.request_5&text='.$tags.'&access_token='.$token;
   echo '<a href="'.$url.'" target="_blank">'.$url.'</a>';
   $contents = file_get_contents($url);
   echo '<pre>'.$contents.'</pre>';
   
   $url="http://ink361.com/#/photos/".$imagenum;
   $contents = file_get_contents($url);
   echo '<pre>'.$contents.'</pre>';
   
   
  //}
  
  
?>
