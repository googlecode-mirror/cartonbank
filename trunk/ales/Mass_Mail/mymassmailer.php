<<<<<<< .mine
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
<h1>Новогодний подарок</h1>
<br><br>
<strong>Картунбанк </strong>дарит каждому зарегистрированному пользователю рисунок <em><a href='http://cartoonbank.ru/?page_id=29&brand=1'>Вячеслава Шилова</a></em> <a href='http://cartoonbank.ru/?page_id=29&cartoonid=19464'>Дедушка Змей</a>.
<ul>
<li><br>
<a href='http://cartoonbank.ru/?page_id=29&cartoonid=19464'><img src='http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/50c62e92ad3f17.725105209097.jpg'></a>
</li>

<li><br>
При регистрации каждый получает в подарок символическую сумму, достаточную для приобретения <a href='http://cartoonbank.ru/?page_id=238'>ограниченной </a>или <a href='http://cartoonbank.ru/?page_id=242'>стандартной </a>лицензии на этот рисунок. Лицензия даёт право на публикацию рисунка в СМИ.
</li>
</ul><br>
При выборе споcоба оплаты воспользуйтесь опцией 'Личный счёт'. (<a href='http://cartoonbank.ru/?page_id=1010'>Инструкция покупателя</a>)
<br><br>
С Новым годом, друзья!<br>
Ваш Картунбанк.<br>

<br>
<img src='http://cartoonbank.ru/wp-admin/images/cb-logo.gif' border='0' title='Картунбанк'>
<br>
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
=======
<?php
if (isset($_REQUEST['prod']) && $_REQUEST['prod']=='1')
{$sql = "SELECT id, mail FROM mymail where dont_send='0' and never='0' order by id desc LIMIT 100";} // production query
else
{$sql = "SELECT id, mail FROM mymailshort where dont_send='0' and never='0' order by id desc LIMIT 10";} //test query

//$email = "igor.aleshin@gmail.com";
$headers = "From: bankir@cartoonbank.ru\r\n" .
           "X-Mailer: Cartoonbank.ru\r\n" .
           "MIME-Version: 1.0\r\n" .
           "Content-Type: text/html; charset=utf-8\r\n" .
           "Content-Transfer-Encoding: 8bit\r\n\r\n";

$subj = "Новогодний рисунок из Картунбанка";
$mess = "
<h1>Новогодний подарок</h1>
<br><br>
<strong>Картунбанк </strong>дарит каждому зарегистрированному пользователю рисунок <em><a href='http://cartoonbank.ru/?page_id=29&brand=1'>Вячеслава Шилова</a></em> <a href='http://cartoonbank.ru/?page_id=29&cartoonid=19464'>Дедушка Змей</a>.
<br>
<br>
<a href='http://cartoonbank.ru/?page_id=29&cartoonid=19464'><img src='http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/50c62e92ad3f17.725105209097.jpg'></a>

<br>
При регистрации каждый получает в подарок символическую сумму, достаточную для приобретения <a href='http://cartoonbank.ru/?page_id=238'>ограниченной</a> или <a href='http://cartoonbank.ru/?page_id=242'>стандартной</a> лицензии на этот рисунок. Лицензия даёт право на публикацию рисунка в СМИ.
<br>
<br>
При выборе споcоба оплаты воспользуйтесь опцией 'Личный счёт'. (<a href='http://cartoonbank.ru/?page_id=1010'>Инструкция покупателя</a>)
<br><br>
С Новым годом, друзья!<br>
Ваш Картунбанк.<br>

<br>
<img src='http://cartoonbank.ru/wp-admin/images/cb-logo.gif' border='0' title='Картунбанк'>
<br>
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
>>>>>>> .r1503
