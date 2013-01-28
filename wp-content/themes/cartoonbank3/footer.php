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
<li><a href="/?page_id=942"<? $pageid=='942'? selected_style_footer():"" ?> title='новости сайта'>Новости</a></li>
<li><a href="/?page_id=976"<? $pageid=='976'? selected_style():"" ?> title='как нас найти'>Контакты</a></li>
<li><a href="/?page_id=2041"<? $pageid=='2041'? selected_style_footer():"" ?> title='English'><img src="<? echo SITEURL;?>img/eng.gif" style="width:20px;border:0;" alt="English"></a></li>
</ul>
</div>

<div id="bottomb"></div>

<div style="clear:both;background: #FFFFFF url(<? echo (get_option('siteurl').'/img/w.png');?>) top center repeat;"><br /><br /></div>

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

<?php do_action('wp_footer'); ?>
<br /><br /><br /><br /><br />


<script>
    jQuery(document).ready(function(){
        if(getCookie('username')!='' && getCookie('username')!='xxx'){
        var nnn = "Здравствуйте, <a href='/wp-admin/profile.php'>"+getCookie('username');
        }
        else{
            var nnn = "Вам надо <a href='/wp-login.php'>залогиниться</a>, чтобы добавлять товары в корзину.";
        }
        jQuery('#username').html(nnn);
    });
</script>

</body></html>
