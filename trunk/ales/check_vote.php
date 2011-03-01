<?php
// run this several times to wipe out extra double votes

include("config.php");
global $wpdb;

$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);


$result = mysql_query("select post, user, points, ip from `wp_fsr_user` order by post, ip, points desc");

if (!$result) {
                die('Invalid query: ' . mysql_error());
            }

//
$count=mysql_num_rows($result);
pokazh($count,"всего рядов"); 

//
$row=mysql_fetch_array($result);

$current_points = $row['points'];
$current_ip = $row['ip'];
$current_post = $row['post'];

$count=$count-1;
		while($row=mysql_fetch_array($result))
		{
			//echo "<br>";
			//pokazh($count,"count");
			//echo $row['user'] . " " . $row['points']." <b>". $row['post']. "</b> " .$row['ip'].":<br>";
			
			//if ($row['post']==$current_post && $row['points']==$current_points && $row['ip']==$current_ip)
			if ($row['post']==$current_post && $row['ip']==$current_ip)
			{
				echo "<br><font color='#FF00FF'><b>this will be deleted!:</b> </font> ip='".$row['ip']."' cartoon=<a href='http://cartoonbank.ru/?page_id=29&cartoonid=".$current_post."'>".$current_post."</a>";
				$del_sql = "delete from `wp_fsr_user` where user='".$row['user']."' and ip='".$row['ip']."' and post=".$current_post;
				$res = mysql_query($del_sql);

				//  recalculate point and sum
				$_votes = $wpdb->get_var("SELECT COUNT( * ) FROM  `wp_fsr_user` WHERE post = $picture_id");
				$_points = $wpdb->get_var("SELECT SUM( points ) FROM  `wp_fsr_user` WHERE post = $picture_id");

				$wpdb->query("UPDATE `wp_fsr_post` SET votes=$_votes, points=$_points WHERE ID={$picture_id};");



				/*
				if (!$res) {
								die('<br>'.$del_sql.'<br>Invalid delete query: ' . mysql_error());
						}
				*/

			}
				$current_points = $row['points'];
				$current_ip = $row['ip'];
				$current_post = $row['post'];
			//pokazh($row,"result");
			$count=$count-1;

		}

/*
$comments_output = "";

	while($r = mysql_fetch_array($result)) {
		$_date = $r['comment_date'];
		$_comment = nl2br(stripslashes($r['comment_content']));
		$_author = $r['author'];
		$_id = $r['comment_id'];
		$comments_output .= "<div style='margin-top:4px;'><span class='gr' title='".$_date."'>".$_author.":&nbsp; </span><span class='c_body'>".$_comment."</span> [<a title='стереть комментарий' href='http://cartoonbank.ru/wp-content/plugins/purgatory/delete_comment.php?id=".$_id."'>x</a>]</div>";
	}

echo $comments_output;
*/

echo "<br>done!";

function fw($text)
{
	$fp = fopen('_kloplog.txt', 'a');
	fwrite($fp, $text);
	fclose($fp);
}

?>