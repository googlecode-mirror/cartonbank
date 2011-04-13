<?php
/*
Plugin Name: Block Bad Queries
Plugin URI: perishablepress.com/press/2009/12/22/protect-wordpress-against-malicious-url-requests/
Description: Protect WordPress Against Malicious URL Requests
Author URI: perishablepress.com/
Author: Perishable Press
Version: 1.0
*/
global $user_ID; 
if($user_ID) {
  if(!current_user_can('level_10')) {
    if (strlen($_SERVER['REQUEST_URI']) > 255 ||
      strpos($_SERVER['REQUEST_URI'], "eval(") ||
      strpos($_SERVER['REQUEST_URI'], "CONCAT") ||
      strpos($_SERVER['REQUEST_URI'], "UNION+SELECT") ||
      strpos($_SERVER['REQUEST_URI'], "base64")) {
        @header("HTTP/1.1 414 Request-URI Too Long");
	@header("Status: 414 Request-URI Too Long");
	@header("Connection: Close");
	@exit;
    }
  }
}
?>
