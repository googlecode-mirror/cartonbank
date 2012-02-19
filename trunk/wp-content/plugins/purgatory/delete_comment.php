<?
require_once("../../../wp-config.php");
include("config.php");
$_SITEURL = get_option('siteurl');

if($_POST['id'] or $_GET['id'])
{
if (isset($_POST['id']))
	$id=$_POST['id'];
elseif (isset($_GET['id']))
	$id=$_GET['id'];
	$id = mysql_escape_String($id);

// delete comment	
	$sql = "delete from wp_comments where Comment_ID=".$id;
	$ip_sql=mysql_query($sql);


// read comments
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


	//header('Location: '.$_SITEURL.'/wp-admin/admin.php?page=purgatory/purgatory.php');
}
?>
