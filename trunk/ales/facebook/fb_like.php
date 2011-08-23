<?
if (isset($_GET['url']) && is_numeric($_GET['url']))
{
	$id = $_GET['url'];
}
$url = "http%3A%2F%2Fcartoonbank.ru%2F?page_id=29&amp;cartoonid=".$id;
//$content = '<iframe src="http://www.facebook.com/plugins/like.php?app_id=225580560788972&amp;'.$url.'&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=verdana&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:150px; height:21px;" allowTransparency="true"></iframe>';

$content = '<iframe src="http://www.facebook.com/plugins/like.php?app_id=225580560788972&amp;'.$url.'&amp;send=true&amp;layout=button_count&amp;width=150&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:150px; height:21px;" allowTransparency="true"></iframe>';


echo $content;

?>