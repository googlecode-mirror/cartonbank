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

if (isset($_REQUEST['page_id'])&&is_numeric($_REQUEST['page_id'])){
$pageid=$_REQUEST['page_id'];
}
else{
$pageid=29;
}
?>
<div id="navbar">
<ul> 
<li><a href="/?page_id=95"<? $pageid=='95'? selected_style_footer():"" ?> title='коротко о сайте Картунбанк'>О проекте</a></li>
<li><a href="/?page_id=29&amp;offset=0&amp;new=0"<? $pageid=='29' & $_new==0? selected_style_footer():"" ?> title='избранные работы'>Избранное</a></li>
<li><a href="/?page_id=29&amp;offset=0&amp;new=1"<? $pageid=='29'& $_new==1 ? selected_style_footer():"" ?> title='показать новые'>Новое</a></li>
<li><a href="/?page_id=73"<? $pageid=='73'? selected_style_footer():"" ?> title='художникам'>Авторам</a></li>
<li><a href="/?page_id=97"<? $pageid=='97'? selected_style_footer():"" ?> title='покупателям'>Клиентам</a></li>
<li><a href="/?page_id=907"<? $pageid=='907'? selected_style_footer():"" ?> title='посетителям'>Зрителям</a></li>
<li><a href="/?page_id=1215"<? $pageid=='1215'? selected_style_footer():"" ?> title='наши партнёры'>Партнёры</a></li>
<li><a href="/?page_id=1260"<? $pageid=='1260'? selected_style_footer():"" ?> title='друзья и коллеги'>Друзья</a></li>
<li><a href="/?page_id=2"<? $pageid=='2'? selected_style_footer():"" ?> title='ответы на часто задаваемые вопросы'>Ответы</a></li>
<li><a href="/?page_id=2860"<? $pageid=='2860'? selected_style_footer():"" ?> title='новости сайта'>Новости</a></li>
<li><a href="/?page_id=976"<? $pageid=='976'? selected_style():"" ?> title='как нас найти'>Контакты</a></li>
<li><a href="/?page_id=2041"<? $pageid=='2041'? selected_style_footer():"" ?> title='English'><img src="<? echo SITEURL;?>img/eng.gif" style="width:20px;border:0;" alt="English"></a></li>
</ul>
</div>

<div id="bottomb"></div>

<div style="clear:both;background: #FFFFFF url(<? echo (get_option('siteurl').'/img/w.png');?>) top center repeat;"><br /><br /></div>
<?
/*
<div id="b" style="padding-bottom:20px;background: #FFFFFF url(<? echo (get_option('siteurl').'/img/w.png');?>) top center repeat;">
 <a href="http://www.redburda.ru/" target="_blank"><img title="Красная Бурда" src="<? echo (get_option('siteurl').'/img/b/kb88x31.gif');?>" style="border:0;"></a>
 <a href="http://anekdot.ru/" target="_blank"><img title="Анекдоты из России" src="<? echo (get_option('siteurl').'/img/b/anek88x31.gif');?>" style="border:0;"></a>
 <a href="http://www.spbdnevnik.ru/" target="_blank"><img title="Петербургский дневник" src="<? echo (get_option('siteurl').'/img/b/pd88x31.gif');?>" style="border:0;"></a>
 <a href="http://spbsj.ru/" target="_blank"><img title="Союз журналистов" src="<? echo (get_option('siteurl').'/img/b/sjr88x31.jpg');?>" style="border:0;"></a>
</div>


<div id="footer" style="background: #FFFFFF url(<? echo (get_option('siteurl').'/img/w.png');?>) top center repeat;padding-bottom:100px;">
<a href="/?page_id=433">Copyright</a> &copy; 2010-2011 <a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a> <b>16+</b> <br />
Следите за нами в 
<a href="http://vk.com/club30341883">ВКонтакте <img src="<? echo SITEURL;?>img/s_vkontakte.png" title="Follow us VKontakte" style="border:0;"></a>  
<a href="http://community.livejournal.com/cartoonbank/">Живом журнале <img src="<? echo SITEURL;?>img/s_livejournal.png" title="Follow us on LiveJournal" style="border:0;"></a>  
<a href="http://twitter.com/#!/cartoonbankru">в Твиттере <img src="<? echo SITEURL;?>img/s_twitter.png" title="Follow us on Twitter" style="border:0;"></a>  
<a href="http://www.facebook.com/profile.php?id=100001929470986&amp;sk=wall">в Фейсбуке <img src="<? echo SITEURL;?>img/s_facebook.png" title="Follow us on Facebook" style="border:0;"></a>  
<a href="http://friendfeed.com/cartoonbank">в Френдфиде <img src="<? echo SITEURL;?>img/s_friendfeed.png" title="Follow us on Frienfeed" style="border:0;"></a>
<br><a href="http://feedburner.google.com/fb/a/mailverify?uri=Cartoonbankru&amp;loc=ru_RU" title="Подпишитесь на доставку новых рисунков по почте">Email-подписка</a>
</div>
*/
?>
<div id="footer" style="background: #FFFFFF url(<? echo (get_option('siteurl').'/img/w.png');?>) top center repeat;padding-bottom:100px;"><a href="/?page_id=433">Copyright</a> &copy; 2010-2013 <a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a> <b>16+</b> <a href="http://cartoonbank.ru/?page_id=95#otv">Обратная связь</a> <br /></div>


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

<!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href='http://www.liveinternet.ru/click' "+
"target=_blank><img src='//counter.yadro.ru/hit?t26.6;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' alt='' title='LiveInternet: показано число посетителей за"+
" сегодня' "+
"border='0' width='88' height='15'><\/a>")
//--></script><!--/LiveInternet-->

<!-- Rating@Mail.ru counter -->
<div style="position:absolute;left:-10000px;">
<img src="http://dc.ce.b2.a2.top.mail.ru/counter?id=2288702" style="border:0;" height="1" width="1" alt="Рейтинг@Mail.ru" /></div>
<!-- //Rating@Mail.ru counter -->

<script type="text/javascript">
window.___gcfg = {lang: 'ru'};
(function() {
var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
po.src = 'https://apis.google.com/js/plusone.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();
</script>

<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-127981-4";
urchinTracker();
</script>

<script>
    jQuery(document).ready(function(){
        if(getCookie('username')!='' && getCookie('username')!=null && getCookie('username')!='xxx'){
        var nnn = "Здравствуйте, <a href='/wp-admin/profile.php'>"+getCookie('username');
        }
        else{
            var nnn = "Вам надо <a href='/wp-login.php?redirect_to="+encodeURIComponent(document.URL)+"'>залогиниться</a>, чтобы добавлять товары в корзину.";
        }
        jQuery('#username').html(nnn);
    });
</script>


</body></html>
