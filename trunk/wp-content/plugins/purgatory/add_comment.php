<?php
if (isset($current_user->wp_capabilities['author']) && $current_user->wp_capabilities['author']==1)
{
echo ("<h3>Извините, у вас нет права доступа к этой странице</h3>");
exit;
}
require_once("../../../wp-config.php");
include("config.php");
$_SITEURL = get_option('siteurl');
$Current_ID = $current_user->id;


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
		$comments_output .= "<div style='margin-top:4px;'><span class='gr' title='".$_date."'>".$_author.":&nbsp; </span><span class='c_body'>".$_comment."</span> [<a title='стереть комментарий' href='#' onclick='deletecomment(".$_id.");'>x</a>]</div>";
	}

echo $comments_output;
}
?>