<?php
//http://developers.facebook.com/blog/post/465

/*
  //$feedurl = "YOUR_FEED_URL";
  $ogurl = "YOUR_OPEN_GRAPH_URL"; 
  $app_id = “YOUR_APP_ID”; 
  $app_secret = "YOUR_APP_SECRET";
*/
  $mymessage = urlencode("Hello World!");

  $access_token_url = "https://graph.facebook.com/oauth/access_token"; 
  $parameters = "type=client_credentials&client_id=" .  
  $app_id . "&client_secret=" . $app_secret;
  $access_token = file_get_contents($access_token_url . 
    "?" . $parameters);

  $apprequest_url = "https://graph.facebook.com/feed";
  $parameters = "?" . $access_token . "&message=" . 
    $mymessage . "&id=" . $ogurl . "&method=post";
  $myurl = $apprequest_url . $parameters;

  $result = file_get_contents($myurl);
  echo "post_id" . $result;

?>