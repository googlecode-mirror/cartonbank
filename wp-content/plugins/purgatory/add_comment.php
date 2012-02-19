<?php
include("config.php");

$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);

// get comment
$comment = '';
if (isset($_POST['comment']))
	{$comment = mysql_escape_String($_POST['comment']);}

if($comment != '')
{
	if (isset($_POST['cartoon_id']))
		{$cartoon_id = mysql_escape_String($_POST['cartoon_id']);}

	if (isset($_POST['author_id']))
		{$author_id = mysql_escape_String($_POST['author_id']);}

	if (isset($_POST['comment_author']))
		{$cartoon_id = mysql_escape_String($_POST['comment_author']);}

/*
	<select id="tablefields" name="dummy" size="13" multiple="multiple" ondblclick="insertValueQuery()">
	<option value="`comment_ID`" title="">comment_ID</option>
	<option value="`comment_post_ID`" title="">comment_post_ID</option>
	<option value="`comment_author`" title="">comment_author</option>
	<option value="`comment_author_email`" title="">comment_author_email</option>
	<option value="`comment_author_url`" title="">comment_author_url</option>
	<option value="`comment_author_IP`" title="">comment_author_IP</option>
	<option value="`comment_date`" title="">comment_date</option>
	<option value="`comment_date_gmt`" title="">comment_date_gmt</option>
	<option value="`comment_content`" t