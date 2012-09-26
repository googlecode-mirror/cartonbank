<?php
    require_once("../../../wp-config.php");
    include("config.php");

    if (isset($_REQUEST['ip']))
    {$ip = $_REQUEST['ip'];}
    else 
    {$ip=$_SERVER['REMOTE_ADDR'];}

    if($_REQUEST['id'] and isset($_REQUEST['vote']))
    {
        $id=$_REQUEST['id'];
        $vote=$_REQUEST['vote'];
        $id = mysql_escape_String($id);
        $vote = mysql_escape_String($vote);

        // проверить не голосовал ли ещё
        $ip_sql=mysql_query("select ip_add from al_editors_voting_ip where mes_id_fk='$id' and ip_add='$ip'");
        $count=mysql_num_rows($ip_sql);

        if($count==0)
        {
            // добавить новый голос в рейтинг
            $temp_rand= rand();
            $sql = "INSERT ignore INTO `wp_fsr_user` (`user`, `post`, `points`, `ip`) VALUES ($temp_rand, $id, $vote, '$ip');";
            $result = mysql_query($sql) or die(mysql_error());

            // посчитать количество голосов и средний балл
            $sql="select count(post) as votescount, sum(points) as avgpoints from `wp_fsr_user` where post='$id'";
            $result = mysql_query($sql) or die(mysql_error());
            $row=mysql_fetch_array($result);
            $votescount=$row['votescount'];
            $avgpoints=$row['avgpoints'];

            // обновить средний балл и количество голосов
            $sql = "INSERT IGNORE INTO `wp_fsr_post` (`id`, `votes` ,`points`) VALUES ($id, $votescount,  $avgpoints)";
            mysql_query( $sql);
            $sql = "UPDATE `wp_fsr_post` set `votes`= $votescount,`points`=$avgpoints WHERE id=$id";
            $r = mysql_query($sql) or die(mysql_error());

            // отметить, что голосовал
            $sql_in = "insert into al_editors_voting_ip (mes_id_fk,ip_add) values ('$id','$ip')";
            $r = mysql_query($sql_in) or die(mysql_error());

            // подсчитать голоса модераторов
            $sql = "update al_editors_votes set moderator_votes=moderator_votes+1 where image_id='".$id."'";
            $r = mysql_query($sql) or die(mysql_error());

        }


        // прочитать новый суммарный рейтинг рисунка
        $sql="select count(post) as votescount, avg(points) as avgpoints, sum(points) as summpoints from `wp_fsr_user` where post='$id'";
        $result=mysql_query($sql);
        $row=mysql_fetch_array($result);
        $votescount=$row['votescount'];
        $avgpoints=$row['avgpoints'];
        $summpoints=$row['summpoints'];

        $result=mysql_query("select `moderator_votes` from al_editors_votes where image_id='$id'");
        $row=mysql_fetch_array($result);
        $up_value=$row['moderator_votes'];

        // убрать из прихожей
        //.. обновить твиттер
        //.. обновить жж
        //.. обновить facebook


        if ($up_value >= $limit) 
        {

			// email confirmation. the cartoon passed to the bank
			$sql = "SELECT b.name as artist_name, u.user_email, u.accept_notification, l.name as cartoon_name, l.image, l.category FROM wp_product_list as l, wp_product_brands as b, wp_users as u WHERE b.id=l.brand AND l.id = ".$id." AND u.id = b.user_id";
			$result=mysql_query($sql);
			$row=mysql_fetch_array($result);
			$artist_name=$row['artist_name'];
			$artist_email=$row['user_email'];
			$cartoon_name=$row['cartoon_name'];
			$image=$row['image'];
			$category=$row['category'];
			$notify_by_mail = $row['accept_notification'];

			if (($avgpoints < $block_limit) and ($category==11)){
				// disapprove it to the main collection
				$sql = "update wp_product_list set approved=0 where id='$id'";
				mysql_query( $sql);

				if ($notify_by_mail==1){
					//send_email_refused("Уважаемый $artist_name<br />Ваш рисунок № <b>".$id."</b> «".$cartoon_name."» не прошёл в Картунбанк, так как его стартовый рейтинговый балл <b>".$avgpoints."</b> ниже установленного порога похождения в рубрику 'Разное'.<br /><a href='http://cartoonbank.ru/cartoon/".$id."/><img src='http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/images/'".$image."'></a>.<br>Сообщаем вам, что согласно принятым с 1 сентября 2012 года правилам, работы в категорию \"разное\" принимаются только в том случае, если их стартовый рейтинговый балл составляет не менее +2,0. Надеемся, что вы с пониманием отнесётесь к правилам Картунбанка, регулирующим качество контента и напоминаем, что всегда рады видеть ваши новые рисунки у нас на сайте!<br>Это письмо отправлено автоматически и не требует ответа. Чтобы отписаться от этих сообщений снимите отметку в строке \"Получать сообщения о моменте приёмки изображения в Картунбанк\" вашего профиля.",$artist_email);
				}
			}
			else{
					// check if already approved
					$sql = "select approved from wp_product_list where id='$id'";
					$result = mysql_query( $sql);
					$row = mysql_fetch_array($result);
					$is_approved = $row['approved'];

					// approve it to the main collection
					$sql = "update wp_product_list set approved=1 where id='$id'";
					mysql_query( $sql);

					$sql = "select approved from wp_product_list where id='$id'";
					$result = mysql_query( $sql);
					$row = mysql_fetch_array($result);
					$is_approved = $row['approved'];

				if ($notify_by_mail==1 && $is_approved!=1){
					send_email_passed("Уважаемый $artist_name<br />Ваш рисунок № <b>".$id."</b> «".$cartoon_name."» поступил в коллекцию Картунбанка со стартовым рейтинговым баллом <b>".$avgpoints."</b>.<br /><a href='http://cartoonbank.ru/cartoon/".$id."/'><img src='http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/images/".$image."'></a>.<br>Поздравляем вас и напоминаем, что всегда рады видеть ваши новые рисунки у нас на сайте!<br>Это письмо отправлено автоматически и не требует ответа. Чтобы отписаться от этих сообщений снимите отметку в строке \"Получать сообщения о моменте приёмки изображения в Картунбанк\" вашего профиля.",$artist_email);
				}
			}

            //send update to twitter
            //'http://cartoonbank.ru/wp-content/totwit/totwit.php?artist=Vasya&cid='.$id
            //$handle = fopen("http://cartoonbank.ru/wp-content/totwit/totwit.php?cid=".$id, "r");
            //fclose($handle);

            // send update to Livejournal
            //$handle = fopen("http://cartoonbank.ru/wp-content/plugins/lj-post-ales/post_to_lj.php?id=".$id, "r");
            //fclose($handle);

            // send update to Facebook
            // $handle = fopen("http://cartoonbank.ru/wp-content/plugins/fb-post-ales/fb_post.php?id=".$id, "r");

            //fclose($handle);
        }

        // вывести новый рейтинг
        echo "<b>".round($avgpoints,2)."</b></br>(".round($summpoints,0)."/".round($votescount,0).")";
        //echo round($avgpoints,2);
    }

    function fw($text)
    {
        $fp = fopen('_kloplog.txt', 'a');
        fwrite($fp, $text);
        fclose($fp);
    }


    function post_to_lj($id)
    {
        // get text for posting
        $result=mysql_query("select l.name, l.description, l.additional_description, l.image, b.name as artist from wp_product_list as l, wp_product_brands as b where l.id='$id' and l.brand = b.id");

        $row=mysql_fetch_array($result);
        $_artist=$row['artist'];
        $_title=nl2br(stripslashes($row['name']));
        $_description=nl2br(stripslashes($row['description']));
        $_additional_description=nl2br(stripslashes($row['additional_description']));
        $_image=$row['image'];

        $subj = "";
        $text = $_artist.': «'.$_title.'» '.' http://cartoonbank.ru/?page_id=29&cartoonid='.$id.' '.$_description."<br />Тэги: ".$_additional_description;

        /* ваш ник в ЖЖ */
        $name = "_cartoonist_";
        /* ваш пароль в ЖЖ */
        $password = "basie5670659";
        /* текст который вы хотите опубликовать */
        //$text = $body; //"Некоторый текст";
        /* заголовок для текста */
        //$subj = $subj; //"заголовок";

        /* включаем библиотеку XML-RPC */

        include("lib/xmlrpc.inc");

        /* (!!!) Все данные в ЖЖ хранятся в кодировке Unicode,
        используем и в нашем случае такую же кодировку */

        $xmlrpc_internalencoding = 'UTF-8';

        /* Получаем текущее время */

        $date = time();
        $year = date("Y", $date);
        $mon = date("m", $date);
        $day = date("d", $date);
        $hour = date("G", $date);
        $min = date("i", $date);

        /* (!!!) Конвертируем текст из одной кодировки в UTF-8 
        в данном случае файл хранится в кодировке CP1251 */

        //$text = iconv("CP1251", "UTF-8", html_entity_decode($text));
        //$subj = iconv("CP1251", "UTF-8", html_entity_decode($subj));

        /* заполняем массив с необходимыми переменными */

        $post = array(
        "username" => new xmlrpcval($name, "string"),
        "password" => new xmlrpcval($password, "string"),
        "event" => new xmlrpcval($text, "string"),
        "subject" => new xmlrpcval($subj, "string"),
        "lineendings" => new xmlrpcval("unix", "string"),
        "year" => new xmlrpcval($year, "int"),
        "mon" => new xmlrpcval($mon, "int"),
        "day" => new xmlrpcval($day, "int"),
        "hour" => new xmlrpcval($hour, "int"),
        "min" => new xmlrpcval($min, "int"),
        "ver" => new xmlrpcval(2, "int")
        );

        /* на основе массива создаем структуру */

        $post2 = array(
        new xmlrpcval($post, "struct")
        );

        /* создаем XML сообщение для сервера */

        $f = new xmlrpcmsg('LJ.XMLRPC.postevent', $post2);

        /* описываем сервер */

        $c = new xmlrpc_client("/interface/xmlrpc", "www.livejournal.com", 80);
        $c->request_charset_encoding = "UTF-8";

        /* по желанию смотрим на XML-код того что отправится на сервер */

        echo nl2br(htmlentities($f->serialize()));

        /* отправляем XML сообщение на сервер */

        $r = $c->send($f);

        /* анализируем результат */

        if(!$r->faultCode())
        {
            /* сообщение принято успешно и вернулся XML-результат */
            $v = php_xmlrpc_decode($r->value());
            print_r($v);
        }
        else
        {
            /* сервер вернул ошибку */
            print "An error occurred: ";
            print "Code: ".htmlspecialchars($r->faultCode());
            print "Reason: '".htmlspecialchars($r->faultString())."'\n";
        }
    }

function send_email_refused($content,$artist_email)
{
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	$headers .= 'From: CartoonBank Robot <cartoonbank.ru@gmail.com>' . "\r\n";
	mail("cartoonbank.ru@gmail.com","Картинка не прошла в Картунбанк",$content . "<br /> Отправлено на ".$artist_email,$headers);
	mail($artist_email,"Картинка не прошла в Картунбанк",$content,$headers);
}

function send_email_passed($content,$artist_email)
{
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	$headers .= 'From: CartoonBank Robot <cartoonbank.ru@gmail.com>' . "\r\n";
	mail("bankir@cartoonbank.ru","Картинка прошла в Картунбанк (копия)",$content . "<br /> Отправлено на ".$artist_email,$headers);
	mail($artist_email,"Картинка прошла в Картунбанк",$content,$headers);
}
?>