<?php

//if ($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] == "karikashop.com/cb/?page_id=29")
//{
	//header("Location: http://karikashop.com/cb/?page_id=29&offset=".rand(0,5000));
	//header("Location: http://cartoonbank.ru/?page_id=29&offset=".rand(0,5000));
//}

/**
 * Loads the WordPress environment and template.
 *
 * @package WordPress
 */

if ( !isset($wp_did_header) ) {

	$wp_did_header = true;

	require_once( dirname(__FILE__) . '/wp-load.php' );

	wp();

	require_once( ABSPATH . WPINC . '/template-loader.php' );

}

?>