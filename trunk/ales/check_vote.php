<?php
include("config.php");
global $wpdb;

$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);


pokazh ($wpdb->query("select * from 'wp_fsr_user' where post=5902 order by ip"),"query");

exit;

//:
$result = mysql_query("select * from 'wp_fsr_user' where post=5902 order by ip");
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