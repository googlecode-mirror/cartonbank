<?
// for testing only
// this page accepts the post request from anekdotru_form.php
// 

include("config.php");
echo "TEST\n\r";

if ($_POST['action'])
{
/*
$content = '';
$content .= print_r($_POST['author'],"\n\r<br> author: ");
$content .= print_r($_POST['title'],"\n\r<br> title: ");
$content .= print_r($_POST['_link'],"\n\r<br> _link: ");
$content .= print_r($_POST['my_file'],"\n\r<br> ufile: ");
$content .= print_r($_POST['email'],"\n\r<br> email: ");
*/
$content = pokazh($_POST,"Post: ");

	mail("igor.aleshin@gmail.com","POST2AnekdotRu: ".$_POST['author'].$_POST['title'],$content);

	pokazh($_POST,"Post: ");
//	pokazh($_FILES,"Files: ");

// copy received file to special folder
move_uploaded_file($_FILES["ufile"]["tmp_name"],
		  "/home/www/cb3/temp/" . $_FILES["ufile"]["name"]);

$filename = $_FILES["ufile"]["name"];
$path = "/home/www/cb3/temp/";
$mailto = "igor.aleshin@gmail.com"; 
$from_mail = "cartoonbank.ru@gmail.com";
$from_name = "Картунбанк";
$replyto = = "cartoonbank.ru@gmail.com";
$subject = "image sent by post";
$message = "картинка принята";

function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {

}

/*
<table border="0" cellspacing="3" cellpadding="0">
	<tr><td style="white-space: nowrap">Название:</td><td><input name="title" type="text" size="40" value="" /></td></tr>
	<tr><td style="white-space: nowrap">Автор:</td><td><input maxlength="72" name="author" type="text" size="40" value="" /></td></tr>

	<tr><td style="white-space: nowrap">E-mail:<br /><sup>(не публикуется, нужен только для связи с редакцией)</sup></td><td><input name="email" type="text" size="40" value="" /></td></tr>
	<tr><td style="white-space: nowrap">Ссылка на сайт автора:</td><td><input name="link" type="text" size="40" value="" /></td></tr>
	<tr><td style="white-space: nowrap">Укажите файл:</td>
	<td>
	<input type="hidden" name="MAX_FILE_SIZE" value="7000000" />
	<input type="file" name="ufile" />
	</td>

	</tr>
	<tr><td colspan="2">Если вы хотите открыть обсуждение, то вам нужно <a href="http://gb.anekdot.ru/scripts/gb.php?component=registration" target="_blank">зарегистрироваться</a> и <a href="http://gb.anekdot.ru/scripts/gb.php?component=login" target="_blank">авторизоваться</a></td></tr>
				</table>
			<div style="margin: 10px 0 20px 0">Я принимаю <a href="/support/rules_car.html" target="_blank" title="Правила">правила</a> <input type="checkbox" name="rules_agree" value="1" /></div>
	<div>Редактор раздела карикатур&nbsp;&mdash;&nbsp;<a href="mailto:Kalininskiy@yandex.ru">Валентин Калининский</a></div>

		</div>
	<input type="submit" name="apply_button" value="BOT TAK!" />
*/

function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
    $file = $path.$filename;
	echo "\n\rfile: ".$file."\n\r";
    $file_size = filesize($file);
    $handle = fopen($file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));
    $uid = md5(uniqid(time()));
    $name = basename($file);
    $header = "From: ".$from_name." <".$from_mail.">\r\n";
    $header .= "Reply-To: ".$replyto."\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $header .= $message."\r\n\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
    $header .= "Content-Transfer-Encoding: base64\r\n";
    $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
    $header .= $content."\r\n\r\n";
    $header .= "--".$uid."--";
    if (mail($mailto, $subject, "", $header)) {
        echo "mail send ... OK"; // or use booleans here
    } else {
        echo "mail send ... ERROR!";
    }
}

?>