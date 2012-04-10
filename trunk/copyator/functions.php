<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

define('FTP_HOST', 'stlrus.com');
define('FTP_USER', 'u35514813-gena');
define('FTP_PASS', 'u35514813-gena');
define('IMGS_PATH', '/home/www/cb/wp-content/plugins/wp-shopping-cart/files/');

$con;

function ftp_copy_init()
{
    global $con;
    $con = ftp_connect(FTP_HOST);
    
    if (!$con)
    {
	prinf("Could not connect to: %s\r\n", FTP_HOST);
	die();
    }
    
    $res = ftp_login($con, FTP_USER, FTP_PASS);
    
    if (!$res)
    {
	ftp_close($con);
	printf("Could not login to: %s\r\n", FTP_HOST);
	die();
    }
}

function ftp_copy_end()
{
    global $con;
    ftp_close($con);
}

function navigate_to_dir($dirname)
{
    global $con;
    // Check existing directory
    $res = ftp_chdir($con, $dirname);

    if (!$res)
    {
	// Not exist - creating
	$res = ftp_mkdir($con, $dirname);
	
	if (!$res)
	{
	    // We in cycle for upload few images, but it's critical error -> exit
	    printf("Could not create directory: %s\r\n", $dirname);
	    die();
	}
	
	// After creation -> enter to directory
	$res = ftp_chdir($con, $dirname);
	
	if (!$res)
	{
	    // We in cycle for upload few images, but it's critical error -> exit
	    printf("Could not change directory to: %s\r\n", $dirname);
	    die();
	}
    }
}

function copy_file_to_ftp($idhash, $year, $month)
{
    global $con;
    // Returning to root path
    ftp_cdup($con);
    ftp_cdup($con);

    // Check existing directory of year
    navigate_to_dir($year);

    // Check existing directory of month
    navigate_to_dir($month);

    // Copy file
    $res = ftp_put($con, '/' . $year . '/' . $month . '/' . $idhash, IMGS_PATH . $idhash, FTP_BINARY);

    if (!$res)
    {
	// We in cycle for upload few images - if got error - we'll continue, message only
	$err = error_get_last();
	printf("Could not upload file %s\r\n%s\r\n", $idhash, $err["message"]); 
	return false;
    }

    return true;
}

function my_get_option($mcon, $option_name)
{
	$result = mysql_query("SELECT `option_value` FROM `wp_options` WHERE `option_name` = '" . $option_name . "'", $mcon);

	if (!$result)
	{
		printf("Could not run option value query: %s\n", mysql_error($mcon));
		exit;
	}

	if (mysql_num_rows($result) == 0)
	{
		printf("Could not get option value '%s'", $option_name);
		exit;
	}

	$row = mysql_fetch_assoc($result);
	$return = $row["option_value"];

	mysql_free_result($result);
	return $return;
}