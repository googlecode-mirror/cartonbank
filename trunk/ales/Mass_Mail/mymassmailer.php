<?php
if (isset($_REQUEST['prod']) && $_REQUEST['prod']=='1')
{$sql = "SELECT id, mail FROM mymail where dont_send='0' and never='0' LIMIT 100";} // production query
else
{$sql = "SELECT id, mail FROM mymailshort where dont_send='0' and never='0' order by id desc LIMIT 10";} //test query

$myemail = "igor.aleshin@gmail.com";
//$myemail = "vfshilov@gmail.com";

$headers = "From: bankir@cartoonbank.ru\r\n" .
           "X-Mailer: Cartoonbank.ru\r\n" .
           "MIME-Version: 1.0\r\n" .
           "Content-Type: text/html; charset=utf-8\r\n" .
           "Content-Transfer-Encoding: 8bit\r\n\r\n";

$subj = "C новым годом!";
$mess= "
<br>
<a href='http://cartoonbank.ru/?a=1'><img src='http://cartoonbank.ru/wp-admin/images/cb-logo.gif' border='0' title='Картунбанк'></a>
<br>
<p>Новогодний подарок.</p>
<p>Картунбанк дарит каждому <a href='http://cartoonbank.ru/wp-login.php?action=register&gift'>зарегистрированному пользователю</a> рисунок <a href='http://cartoonbank.ru/brand/5/?gift'>Игоря Кийко</a> «<a href='http://cartoonbank.ru/cartoon/9263/?gift'>Подарок от Деда Мороза</a>».</p>

<img src='http://sl.cartoonbank.ru/4d185b8f05e4e5.54470753Na_D.Moroza_nadeysya.jpg' alt='Подарок от Деда Мороза '>

<p>При регистрации каждый получает в подарок на свой личный счет сумму, достаточную
для приобретения <a href='http://cartoonbank.ru/?page_id=238&gift'>ограниченной</a> или <a href='http://cartoonbank.ru/?page_id=242&gift'>стандартной</a> лицензии на этот рисунок.
Ранее зарегистрированные пользователи получают эту возможность автоматически.
Лицензия даёт вам официальное право на публикацию рисунка в СМИ.</p>
 
<p>При выборе споcоба оплаты воспользуйтесь опцией  «Личный счёт».
(См. <a href='http://cartoonbank.ru/?page_id=1010'>Инструкцию покупателя</a>).</p>
 
<p>С Новым годом, друзья!</p>
 
<p><a href='http://cartoonbank.ru/?gift'>Ваш Картунбанк</a></p>
";


//mail($myemail, $subj, $mess, $headers);
//exit;

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
        mail($email, $subj, $mess, $headers);
        echo $counter." id=".$id." email: ".$email."<br>";

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
