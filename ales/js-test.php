<script type='text/javascript' src='http://cartoonbank.ru/wp-includes/js/jquery/jquery.js?ver=1.4.2'></script> 

<style>
.to_replace
{<br>
	background-color: white; 
	border:1 solid silver;
	width:666;
	height:200;
}
</style>

<div id='scr' class='to_replace'></div>

<br>button_count<br>
<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#appId=130643610343080&amp;xfbml=1"></script><fb:like href="http://cartoonbank.ru/?page_id=29"  width="137" show_faces="true" layout="button_count"></fb:like>

<br>iframe<br>

<iframe src="http://www.facebook.com/plugins/like.php?app_id=218533078165990&amp;href=http%3A%2F%2Fcartoonbank.ru%2F%3Fpage_id%3D29&amp;send=true&amp;layout=standard&amp;width=120&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font&amp;height=80" scrolling="no" frameborder="0" style="border:1 solid red; overflow:hidden; width:120px; height:80px;" allowTransparency="true"></iframe>

<br>XFBML

<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#appId=218533078165990&amp;xfbml=1"></script><fb:like href="http://cartoonbank.ru/?page_id=29" send="true" width="120" show_faces="true" font=""></fb:like>

<script>
get_scr();
	function get_scr()
	{
	jQuery(document).ready(function() {
		var starturl = 'http://cartoonbank.ru/ales/facebook/fb_like.php?url=2123';
		var content = jQuery('#scr').load(starturl);
	});
	}
</script>
