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
	<option value="`comment_content`" title="">comment_content</option>
	<option value="`comment_karma`" title="">comment_karma</option>
	<option value="`comment_approved`" title="">comment_approved</option>
	<option value="`comment_agent`" title="">comment_agent</option>
	<option value="`comment_type`" title="">comment_type</option>
	<option value="`comment_parent`" title="">comment_parent</option>
	<option value="`user_id`" title="">user_id</option>
	</select>
*/

	// save comment:
	$sql_insert = "insert into wp_comments (comment_post_ID, comment_content, comment_date, comment_author) values('$cartoon_id', '$comment', '".date("Y-m-d H:i:s")."','$author_id')";
	$result = mysql_query($sql_insert);


	//read comments:
	$result = mysql_query("select C.comment_id, C.comment_content, C.comment_date, U.display_name as author from wp_comments as C, wp_users as U where U.id = C.comment_author order by C.comment_date DESC LIMIT 50");

$result = mysql_query("select C.comment_id, C.comment_content, C.comment_date, U.display_name as author from wp_comments as C, wp_users as U where U.id = C.comment_author order by C.comment_date DESC LIMIT 50");
	$comments_output = "";
	while($r = mysql_fetch_array($result)) {
		$_date = $r['comment_date'];
		$_comment = nl2br(stripslashes($r['comment_content']));
		$_author = $r['author'];
		$_id = $r['comment_id'];
		$comments_output .= "<div style='margin-top:4px;'><span class='gr' title='".$_date."'>".$_author.":&nbsp; </span><span class='c_body'>".$_comment."</span> [<a title='стереть комментарий' href='http://cartoonbank.ru/wp-content/plugins/purgatory/delete_comment.php?id=".$_id."'>x</a>]</div>";
	}

echo $comments_output;
}

function fw($text)
{
	$fp = fopen('_kloplog.txt', 'a');
	fwrite($fp, $text);
	fclose($fp);
}

?>