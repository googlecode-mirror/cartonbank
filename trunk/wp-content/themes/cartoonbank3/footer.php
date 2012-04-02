<!-- footer begins -->
<?
function selected_style_footer()
{
	echo " style='color:#668bb7'";
}

if (isset($_GET['new']) && is_numeric($_GET['new']))
{
	$_new=$_GET['new'];
}
else
{
	$_new=0;
}
?>
<div id="navbar">
<ul> 
<li><a href="?page_id=95"<? $_GET['page_id']=='95'? selected_style_footer():"" ?> title='коротко о сайте Картунбанк'>О проекте</a></li>
<li><a href="?page_id=29&amp;offset=0&amp;new=0"<? $_GET['page_id']=='29' & $_new==0? selected_style_footer():"" ?> title='избранные работы'>Избранное</a></li>
<li><a href="?page_id=29&amp;offset=0&amp;new=1"<? $_GET['page_id']=='29'& $_new==1 ? selected_style_footer():"" ?> title='показать новые'>Новое</a></li>
<li><a href="?page_id=73"<? $_GET['page_id']=='73'? selected_style_footer():"" ?> title='художникам'>Авторам</a></li>
<li><a href="?page_id=97"<? $_GET['page_id']=='97'? selected_style_footer():"" ?> title='покупателям'>Клиентам</a></li>
<li><a href="?page_id=907"<? $_GET['page_id']=='907'? selected_style_footer():"" ?> title='посетителям'>Зрителям</a></li>
<li><a href="?page_id=1215"<? $_GET['page_id']=='1215'? selected_style_footer():"" ?> title='наши партнёры'>Партнёры</a></li>
<li><a href="?page_id=1260"<? $_GET['page_id']=='1260'? selected_style_footer():"" ?> title='друзья и коллеги'>Друзья</a></li>
<li><a href="?page_id=2"<? $_GET['page_id']=='2'? selected_style_footer():"" ?> title='ответы на часто задаваемые вопросы'>Ответы</a></li>
<li><a href="?page_id=942"<? $_GET['page_id']=='942'? selected_style_footer():"" ?> title='новости сайта'>Новости</a></li>
<li><a href="?page_id=976"<? $_GET['page_id']=='976'? selected_style():"" ?> title='как нас найти'>Контакты</a></li>
<li><a href="?page_id=2041"<? $_GET['page_id']=='2041'? selected_style_footer():"" ?> title='English'><img src="http://cartoonbank.ru/img/eng.gif" style="width:20px;border:0;" alt="English"></a></li>
</ul>
</div>

<div style="clear:both;background: #FFFFFF url(<? echo (get_option('siteurl').'/img/w.png');?>) top center repeat;"><br /><br /></div>

<div id="b" style="padding-bottom:20px;background: #FFFFFF url(<? echo (get_option('siteurl').'/img/w.png');?>) top center repeat;">
 <a href="http://www.redburda.ru/" target="_blank"><img title="Красная Бурда" src="<? echo (get_option('siteurl').'/img/b/kb88x31.gif');?>" style="border:0;"></a>
 <a href="http://anekdot.ru/" target="_blank"><img title="Анекдоты из России" src="<? echo (get_option('siteurl').'/img/b/anek88x31.gif');?>" style="border:0;"></a>
 <a href="http://www.spbdnevnik.ru/" target="_blank"><img title="Петербургский дневник" src="<? echo (get_option('siteurl').'/img/b/pd88x31.gif');?>" style="border:0;"></a>
 <a href="http://www.izvestia.ru/spb/" target="_blank"><img title="Известия-СПб" src="<? echo (get_option('siteurl').'/img/b/iz88x31.gif');?>" style="border:0;"></a>
 <a href="http://spbsj.ru/" target="_blank"><img title="Союз журналистов" src="<? echo (get_option('siteurl').'/img/b/sjr88x31.jpg');?>" style="border:0;"></a>
</div>

<div id="footer" style="background: #FFFFFF url(<? echo (get_option('siteurl').'/img/w.png');?>) top center repeat;padding-bottom:100px;">
<a href="?page_id=433">Copyright</a> &copy; 2010-2011 <a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a><br />
Следите за нами в 
<a href="http://community.livejournal.com/cartoonbank/">Живом журнале <img src="http://cartoonbank.ru/img/s_livejournal.png" title="Follow us on LiveJournal" style="border:0;"></a>  
<a href="http://twitter.com/#!/cartoonbankru">в Твиттере <img src="http://cartoonbank.ru/img/s_twitter.png" title="Follow us on Twitter" style="border:0;"></a>  
<a href="http://www.facebook.com/profile.php?id=100001929470986&amp;sk=wall">в Фейсбуке <img src="http://cartoonbank.ru/img/s_facebook.png" title="Follow us on Facebook" style="border:0;"></a>  
<a href="http://friendfeed.com/cartoonbank">в Френдфиде <img src="http://cartoonbank.ru/img/s_friendfeed.png" title="Follow us on Frienfeed" style="border:0;"></a>
<br><a href="http://feedburner.google.com/fb/a/mailverify?uri=Cartoonbankru&amp;loc=ru_RU" title="Подпишитесь на доставку новых рисунков по почте">Email-подписка</a>
</div>

<?php do_action('wp_footer'); ?>
<br /><br /><br /><br /><br />

<!-- Yandex.Metrika counter --><script type="text/javascript">(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter5781658 = new Ya.Metrika({id:5781658, clickmap:true, trackLinks:true, trackHash:true, webvisor:true}); } catch(e) {} }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f); } else { f(); } })(document, window, "yandex_metrika_callbacks");</script><noscript><div><img src="//mc.yandex.ru/watch/5781658" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->

<!--Openstat-->
<span id="openstat2196559"></span>
<script type="text/javascript">
var openstat = { counter: 2196559, next: openstat };
(function(d, t, p) {
var j = d.createElement(t); j.async = true; j.type = "text/javascript";
j.src = ("https:" == p ? "https:" : "http:") + "//openstat.net/cnt.js";
var s = d.getElementsByTagName(t)[0]; s.parentNode.insertBefore(j, s);
})(document, "script", document.location.protocol);
</script>
<!--/Openstat-->

<!-- begin of Top100 code -->
<script id="top100Counter" type="text/javascript" src="http://counter.rambler.ru/top100.jcn?2529853"></script>
<noscript><a href="http://top100.rambler.ru/navi/2529853/">
<img src="http://counter.rambler.ru/top100.cnt?2529853" alt="Rambler's Top100" />
</a></noscript>
<!-- end of Top100 code -->

<script type="text/javascript">
window.___gcfg = {lang: 'ru'};
(function() {
var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
po.src = 'https://apis.google.com/js/plusone.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();
</script>
</body></html>