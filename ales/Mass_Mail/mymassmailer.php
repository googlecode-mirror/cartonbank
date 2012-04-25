<?php
if (isset($_REQUEST['prod']) && $_REQUEST['prod']=='1')
{$sql = "SELECT id, mail FROM mymail where dont_send='0' and never='0' order by id desc LIMIT 10";} // production query
else
{$sql = "SELECT id, mail FROM mymailshort where dont_send='0' and never='0' order by id desc LIMIT 10";} //test query

//$email = "igor.aleshin@gmail.com";
$headers = "From: bankir@cartoonbank.ru\r\n" .
           "X-Mailer: Cartoonbank.ru\r\n" .
           "MIME-Version: 1.0\r\n" .
           "Content-Type: text/html; charset=utf-8\r\n" .
           "Content-Transfer-Encoding: 8bit\r\n\r\n";

$subj = "Торт в подарок к дню рождения";
$mess = "
<br>
<img src='http://cartoonbank.ru/wp-admin/images/cb-logo.gif' border='0' title='Картунбанк'>
<br>
<br>
Ровно два года назад в Санкт-Петербурге была образована маленькая, но трудолюбивая компания «МедиаГрафика», у которой спустя некоторое время появился улыбчивый первенец. Счастливая мама назвала его гордым именем <a href='http://cartoonbank.ru/'>CartoonBank.ru</a>.
<br><br>
Малыш рос как на дрожжах, умнел, взрослел – и к настоящему времени превратился в любимца  самых взыскательных и строгих издательств страны.  Сегодня <i>Картунбанк</i> предлагает всем интересующимся юмористической графикой уже более 15.000 лицензионных изображений 43-х авторов из 8 стран мира. Попутно он разработал и реализовал целый ряд успешных проектов – от выставочных до издательских. Несмотря на свой малый возраст, <i>Картунбанк</i> известен федеральным и региональным СМИ как надежный партнер и товарищ.
<br><br>
В день нашего рождения мы хотим пригласить вас в гости на наш сайт <a href='http://cartoonbank.ru/'>CartoonBank.ru</a> и подарить торт. Почти в буквальном смысле. Это работа художника <i>Вячеслава Шилова</i> <a href='http://cartoonbank.ru/?page_id=29&cartoonid=1617'>«Торт»</a> вместе с лицензией на применение.
<br>
<img src='http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/4c559cc81925a5.96165950shilov42-2.jpg' border='0' title='Торт - подарок юбиляру'>
<br><br>
Скачать рисунок «Торт» (файл высокого разрешения) и официальный лицензионный договор на свое имя несложно. Достаточно <a href='http://cartoonbank.ru/wp-login.php?action=register'>зарегистрироваться</a> на сайте, после чего вы получите полноправный доступ к своему аккаунту, где вам будут автоматически предоставлены средства, достаточные для покупки одной из двух лицензий (на ваше усмотрение) к изображению «Торт» и возможность скачать карикатуру вместе с правами на ее использование.
<br>
Подробные действия необходимые для получения изображения <a href='http://cartoonbank.ru/?page_id=1010'>описаны здесь</a> (начиная с пункта 4).
<br><br>
Если вы планируете использовать изображение в СМИ, рекомендуем указать его наименование при совершении покупки. В этом случае название СМИ будет отражено в тексте лицензии.
<br>
Возможность получить рисунок в подарок сохранится вплоть до 31 мая 2012 года.
<br><br>

С уважением, 
Картунбанк

";





// configuration
include("/home/www/cb3/ales/config.php");
$counter=0;

//open db connection
$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
mysql_set_charset('utf8',$link);


// get emails
$result = mysql_query($sql);
if (!$result) {die('<br />'.$sql.'<br />Invalid select query: ' . mysql_error());}

while ($row = mysql_fetch_array($result)) 
    {
        $counter++;
        $id = $row['id'];  
        $email = $row['mail'];  
        // send mail
        //mail($email, $subj, $mess, $headers);
        echo $counter." ".$id.": ".$email."<br>";

        // update dont_send field:
        if (isset($_REQUEST['prod']) && $_REQUEST['prod']=='1')
        {$sqlupdate = "update mymail set dont_send = '1' where id = ".$id;} // production
        else
        {$sqlupdate = "update mymailshort set dont_send = '1' where id = ".$id;} // test

        $res = mysql_query($sqlupdate);
        if (!$res) {die('<br />'.$sqlupdate.'<br />Invalid update query: ' . mysql_error());}
    }
mysql_close($link);

echo "<h1>Отправлено писем: $counter</h1>";



?>
