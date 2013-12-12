<?php
include("../../../ales/config.php");

$ip = 'none';
if (isset($_GET['ip']))
    {$ip = $_GET['ip'];}
elseif (isset($_COOKIE['uid']) and $_COOKIE['uid'] != '')
    {$ip=$_COOKIE['uid'];}
    /*
elseif (isset($_SERVER['REMOTE_ADDR']) and $_SERVER['REMOTE_ADDR'] != '')
    {$ip=$_SERVER['REMOTE_ADDR'];}
    */
    
if(isset($_REQUEST['id']))
{
    $id=$_REQUEST['id'];
    $id = mysql_escape_String($id);

    $ip_sql=mysql_query("select ip_add from al_editors_voting_ip where mes_id_fk='$id' and ip_add='$ip'");
    $count=mysql_num_rows($ip_sql);
    
    $points = $_REQUEST['points'];
    
    $count=FALSE;//todo debug temp // alows to vote multiple times

    if($count==FALSE)
    {
        // if allowed to vote
         if (isset($ip) && isset($id) && isset($points))
        {
            // if we have input data
            // insert votes to the wp_fsr_user
            $sql = "insert ignore into wp_fsr_user (user, post, points, ip) values ('$ip', '$id', '$points', '$ip')";
            mysql_query($sql) or die("Could not update wp_fsr_user table");
            
            // increment # of moderators votes
            // first, insert new record for new image
            $sql = "insert ignore into al_editors_votes (image_id) values ('$id')";
            mysql_query($sql) or die("Could not insert into al_editors_votes table");
            // second, update # of votes
            $sql = "update al_editors_votes set moderator_votes=moderator_votes+1 where image_id='$id'";
            mysql_query($sql) or die("Could not update al_editors_votes table");

            // record who voted of moderators
            $sql = "insert into al_editors_voting_ip (mes_id_fk,ip_add) values ('$id','$ip')";
            mysql_query($sql) or die("Could not insert al_editors_voting_ip into database");
            
            // update total votes
            // first, insert new image id
            $sql = "insert ignore into wp_fsr_post (id) values ('$id')";
            mysql_query($sql) or die("Could not insert into wp_fsr_post table");
            // second, calculate votes and points
            $sql = "select SUM(points) as points, COUNT(*) from  wp_fsr_user where post=$id";
            $result = mysql_query($sql);
            //
            $sql = "update wp_fsr_post set votes=(select SUM(points) from wp_fsr_user where post=$id), points=(select COUNT(*) from wp_fsr_user where post=$id) where id='$id'";
            $result = mysql_query($sql);
  
        }
    }

    $result=mysql_query("select up, down from al_editors_votes where image_id='$id'");

    $row=mysql_fetch_array($result);
    $up_value=$row['up'];
    $down_value=$row['down'];
    $votes_number = $up_value+$down_value;

            if ($votes_number == $limit) // 6 - общее количество голосующих
            {
                // approve it to the main collection
                    // todo $sql = "update wp_product_list set approved=1 where id='$id'";
                    // todo mysql_query( $sql);

                    //send update to twitter
                    //'http://cartoonbank.ru/wp-content/totwit/totwit.php?artist=Vasya&cid='.$id
                    // todo $handle = fopen("http://cartoonbank.ru/wp-content/totwit/totwit.php?cid=".$id, "r");
                    // todo fclose($handle);

                    // send update to Livejournal
                    // todo $handle = fopen("http://cartoonbank.ru/wp-content/plugins/lj-post-ales/post_to_lj.php?id=".$id, "r");
                    // todo fclose($handle);

                //update cartoon rating
                
                //get current fsr rating
                $sql = "select votes, points from wp_fsr_post where id=$id limit 1";
                $result = mysql_query($sql);
                $result = mysql_fetch_array($result);
                
                if ($result != FALSE)
                {
                    $current_votes = $result['votes'];
                    $current_points = $result['points'];
                    $sql = "update wp_fsr_post set votes=$current_votes+$limit, points=$current_points+$up_value*5+$down_value where id='$id'";
                    $result = mysql_query($sql);
                }
                else                
                {
                    $current_votes = 0;
                    $current_points = 0;
                    $sql = "insert into wp_fsr_post (id, votes, points) values ($id, $limit, $up_value*5+$down_value)";
                    $result = mysql_query($sql);
                }
                
                //$rating_value = ($up_value*5+$down_value*1)/$limit;
                //$sql = "update wp_product_list set approved=1, votes=$current_votes+$limit, votes_sum=($current_points+$up_value*5+$down_value), vote_date_last='".date("Y-m-d H:i:s")."' where id='$id'";
				$sql = "update wp_product_list set approved='1', votes=$current_votes+$limit, votes_sum=($current_points+$up_value*5+$down_value) where id='$id'";
                $result = mysql_query($sql);// or die("Could not insert rating data into wp_product_list");
                
                // send update to Facebook
                // $handle = fopen("http://cartoonbank.ru/wp-content/plugins/fb-post-ales/fb_post.php?id=".$id, "r");

                //fclose($handle);
            }

    if (isset($_REQUEST['vote']) && $_REQUEST['vote']=='up')
    {
        echo $up_value;
    }

    if (isset($_REQUEST['vote']) && $_REQUEST['vote']=='down')
    {
        echo $down_value;
    }
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
?>