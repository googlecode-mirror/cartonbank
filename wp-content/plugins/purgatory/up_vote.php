<?php
include("config.php");

$ip = 'none';
if (isset($_GET['ip']))
	{$ip = $_GET['ip'];}
elseif (isset($_SERVER['REMOTE_ADDR']) and $_SERVER['REMOTE_ADDR'] != '')
	{$ip=$_SERVER['REMOTE_ADDR'];}

if($_POST['id'] or $_GET['id'])
{
if (isset($_POST['id']))
	$id=$_POST['id'];
elseif (isset($_GET['id']))
	$id=$_GET['id'];
	$id = mysql_escape_String($id);


	$ip_sql=mysql_query("select ip_add from al_editors_voting_ip where mes_id_fk='$id' and ip_add='$ip'");
	$count=mysql_num_rows($ip_sql);

	if($count==0)
	{
		$sql = "update al_editors_votes set up=up+1  where image_id='$id'";
		mysql_query( $sql);

		$sql_in = "insert into al_editors_voting_ip (mes_id_fk,ip_add) values ('$id','$ip')";
		mysql_query( $sql_in);
	}

	$result=mysql_query("select up from al_editors_votes where image_id='$id'");

	$row=mysql_fetch_array($result);
	$up_value=$row['up'];

			//fw("\n\r up_value=".$up_value);
			if ($up_value == $limit_plus) // 3 плюса - проходит, 3 минуса - не проходит
			{
				// approve it to the main collection
				$sql = "update wp_product_list set approved=1 where id='$id'";
				//fw("\n\r sql=".$sql);
				mysql_query( $sql);

				//send update to twitter
				//'http://cartoonbank.ru/wp-content/totwit/totwit.php?artist=Vasya&cid='.$id
				$handle = fopen("http://cartoonbank.ru/wp-content/totwit/totwit.php?cid=".$id, "r");
				fclose($handle);

				// send update to Livejournal
				$handle = fopen("http://cartoonbank.ru/wp-content/plugins/lj-post-ales/post_to_lj.php?id=".$id, "r");
				fclose($handle);

				// send update to Facebook
				// $handle = fopen("http://cartoonbank.ru/wp-content/plugins/fb-post-ales/fb_post.php?id=".$id, "r");

				//fclose($handle);
			}

	echo $up_value;
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