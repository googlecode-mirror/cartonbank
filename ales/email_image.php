<h2>Покупатели</h2>
<form method=post action=''><input type='submit' value='отправить счёт почтой' name='email'>

<?php
if (isset($_POST['email']))
{
	echo "sent";
	$send_invoce_by_mail = true;
}
else
{
	echo "not sent";
	$send_invoce_by_mail = false;
}

$content = "test письмо";

if ($send_invoce_by_mail)
{
	send_invoce_by_mail($content);
}




function send_invoce_by_mail($content)
{
	// To send HTML mail, the Content-type header must be set
		/*
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
			$headers .= 'From: CartoonBank Robot <cartoonbank.ru@gmail.com>' . "\r\n";
			//$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
			//$headers .= "CC: susan@example.com\r\n";
		*/

	$my_file = 'test.gif';//$slidename;
	$my_path = "/home/www/cb3/ales/";
	$my_name = "cartoonbank";
	$my_mail = "cartoonbank.ru@gmail.com";
	$my_replyto = "cartoonbank.ru@gmail.com";
	$my_subject = "Счёт от Картунбанка";
	$my_message = "<div  style='padding:2px; height:200px; border-bottom:1px solid silver; background-color:#FFFF66; padding-left:4px; margin-top:8px;'>film <br><img src='cid:test.gif'><br><b> film </b> <br>film</div>";
	$mailto = "igor.aleshin@gmail.com";

	//send email
	mail_attachment1($my_file, $my_path, $mailto, $my_mail, $my_name, $my_replyto, $my_subject, $my_message);

	return true;
}

function mail_attachment1($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
    $file = $path.$filename;
    $file_size = filesize($file);
    $handle = fopen($file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));
    $uid = md5(uniqid(time()));
    $name = basename($file);

		
	$header = "MIME-Version: 1.0"."\r\n";
	$header .= "From: Igor Aleshin <igor.aleshin@gmail.com>"."\r\n";
	$header .= "Content-Type: multipart/related; boundary=20cf300256d4a5b67f04a2c1d422"."\r\n\r\n";
	$header .= "--20cf300256d4a5b67f04a2c1d422"."\r\n";
	$header .= "Content-Type: multipart/alternative; boundary=20cf300256d4a5b67a04a2c1d421"."\r\n\r\n";

	$header .= "--20cf300256d4a5b67a04a2c1d421"."\r\n";
	$header .= "Content-Type: text/plain; charset=ISO-8859-1"."\r\n\r\n";

	$header .= "111
[?]
222"."\r\n\r\n";

	$header .= "--20cf300256d4a5b67a04a2c1d421"."\r\n";
	$header .= "Content-Type: text/html; charset=ISO-8859-1"."\r\n\r\n";

	$header .= "<div>111</div><img src='cid:1A5@goomoji.gmail' style='margin-top: 0px; margin-right: 0.2ex; margin-bottom: 0px; margin-left: 0.2ex; vertical-align: middle; ' goomoji='1A5'><div>222<br><div><br></div><div><br></div></div>"."\r\n\r\n";

	$header .= "--20cf300256d4a5b67a04a2c1d421--"."\r\n";
	$header .= "--20cf300256d4a5b67f04a2c1d422"."\r\n";
	$header .= "Content-Type: image/gif; name='1A5.gif'"."\r\n";
	$header .= "Content-Transfer-Encoding: base64"."\r\n";
	$header .= "X-Attachment-Id: 1A5@goomoji.gmail"."\r\n";
	$header .= "Content-ID: <1A5@goomoji.gmail>"."\r\n\r\n";

	$header .= "R0lGODlhEAAMAKIFAF5LAP/zxAAAANyuAP/gaP///wAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQJZAAFACwAAAAAEAAMAAADO1iq0/6LhUkBmCOOQLonAJEtGyEI3dmNkom6q8Z9H1sML22OTnC+P9GNU9LFBquZJxS7NWYWZpP0qBYSACH5BAkKAAUALAAAAAAQAAwAAAM7WKrT/ouFSQGYI45AuicAkS0bIQjd2Y2Sibqrxn0fWwwvbbJNcL4/UWug84yIIlooxiiBLLXI7UEtJAAAIfkECQoABQAsAAAAABAADAAAAzhYqtP+i4VJAZgjjkC6JwCRLRshCN3ZjZKJuqvGfR9bNLQnsOWguijeKhcjDWihYgtkqUVuj2ghAQAh+QQFCgAFACwAAAAAEAAMAAADN1iq0/6LhUkBmCOOQLonAJEtGyEI3dmNkom6q8Z9H1s4dGqXw3uiu1VOpGl8QjHSzIJMkh7QQgIAIfkEBRQABQAsAAAFAAgABQAAAxFYMxpEwy0H3xBYKBZf+c2TAAAh+QQFCgAFACwEAAYABQACAAADBlgqMkEqAQAh+QQFAAAFACwGAAYAAwACAAADBChE0gkAIfkECQoABQAsAAAFAAcAAgAAAwVYuqxCCQAh+QQJCgAFACwAAAAAEAAMAAADGFi63P4wykndCESOUYbIUVB1BMFR26iuCQAh+QQFkAEFACwAAAAAEAAMAAADO1iq0/6LhUkBmCOOQLonAJEtGyEI3dmNkom6q8Z9H1sML22OTnC+P9GNU9LFBquZJxS7NWYWZpP0qBYSADs="."\r\n";
	$header .= "--20cf300256d4a5b67f04a2c1d422--"."\r\n";

	if (mail($mailto, $subject, "", $header)) {
        echo "mail send ... OK"; // or use booleans here
    } else {
        echo "mail send ... ERROR!";
    }
}


function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
    $file = $path.$filename;
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
    $header .= "Content-Type: multipart/related; boundary=\"".$uid."\"\r\n\r\n";
    //$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
	//$header .= "Content-Type: multipart/alternative; boundary=\"".$uid."\"\r\n\r\n";
	//$header .= "X-Mailer: Microsoft Office Outlook 12.0\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";

	//html
	$header .= "--".$uid."\r\n";
    $header .= "Content-type:text/html; charset=utf-8\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n";
	//$header .= "Content-Transfer-Encoding: quoted-printable\r\n";

	$header .= $message."\r\n\r\n";

	// img file
    $header .= "--".$uid."\r\n";
				//$header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n";
	$header .= "Content-Type: image/gif; name=\"".$filename."\"\r\n"; 
    $header .= "Content-Transfer-Encoding: base64\r\n";
    //$header .= "X-Attachment-Id: ".$filename."\r\n";
				//$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
    $header .= "Content-Disposition: inline; filename=\"".$filename."\"\r\n\r\n";
    $header .= "Content-ID: <".$filename.">\r\n\r\n";
    
	$header .= $content."\r\n\r\n";
    	
	$header .= "--".$uid."--";
    
	if (mail($mailto, $subject, "", $header)) {
        echo "mail send ... OK"; // or use booleans here
    } else {
        echo "mail send ... ERROR!";
    }
}

?>
