<?
include("config.php");

$id=0;

if($_POST['id'] or $_GET['id'])
{
if (isset($_POST['id']))
	$id=$_POST['id'];
elseif (isset($_GET['id']))
	$id=$_GET['id'];
	$id = mysql_escape_String($id);


	// get text for posting

	$result=mysql_query("SET NAMES utf8");

	$result=mysql_query("select l.name, l.description, l.additional_description, l.image, b.name as artist from wp_product_list as l, wp_product_brands as b where l.id='$id' and l.brand = b.id");

	$row=mysql_fetch_array($result);
	$_artist=$row['artist'];
	$_title=$row['name'];
	$_description=$row['description'];
	$_additional_description=$row['additional_description'];
	$_image=$row['image'];


	$subj = $_title;
	$text = "<a href='http://cartoonbank.ru/?page_id=29&cartoonid=".$id."'><img src='http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/".$_image."' border='0'></a><br><b>".$_artist."</b>: &quot;".$_title."&quot;  <br>".$_description."<br>Tags: ".$_additional_description."<br><a href='http://cartoonbank.ru' title='cartoonbank'>cartoonbank.ru</a>";

	/* ��� ��� � �� */
	$name = "_cartoonist_";
	/* ��� ������ � �� */
	$password = "basie5";
	/* ��������� */
	$usejournal = "cartoonbank";

	/* �������� ���������� XML-RPC */

	include("lib/xmlrpc.inc");

	$xmlrpc_internalencoding = 'UTF-8';

	/* �������� ������� ����� */

	$date = time();
	$year = date("Y", $date);
	$mon = date("m", $date);
	$day = date("d", $date);
	$hour = date("G", $date);
	$min = date("i", $date);

	/* (!!!) ������������ ����� �� ����� ��������� � UTF-8 
	� ������ ������ ���� �������� � ��������� CP1251 */

	//$text = iconv("CP1251", "UTF-8", html_entity_decode($text));
	//$subj = iconv("CP1251", "UTF-8", html_entity_decode($subj));

	/* ��������� ������ � ������������ ����������� */

	$post = array(
			"username" => new xmlrpcval($name, "string"),
			"usejournal" => new xmlrpcval($usejournal, "string"),
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

	/* �� ������ ������� ������� ��������� */

	$post2 = array(
		new xmlrpcval($post, "struct")
	);

	/* ������� XML ��������� ��� ������� */

	$f = new xmlrpcmsg('LJ.XMLRPC.postevent', $post2);

	/* ��������� ������ */

	$c = new xmlrpc_client("/interface/xmlrpc", "www.livejournal.com", 80);
	$c->request_charset_encoding = "UTF-8";

	/* �� ������� ������� �� XML-��� ���� ��� ���������� �� ������ */

	//echo nl2br(htmlentities($f->serialize()));

	/* ���������� XML ��������� �� ������ */

	$r = $c->send($f);
		
	/* ����������� ��������� */
		
	if(!$r->faultCode())
	{
		/* ��������� ������� ������� � �������� XML-��������� */
		$v = php_xmlrpc_decode($r->value());
		print_r($v);
	}
	else
	{
		/* ������ ������ ������ */
		print "An error occurred: ";
		print "Code: ".htmlspecialchars($r->faultCode());
		print "Reason: '".htmlspecialchars($r->faultString())."'\n";
	}


// post to cartunbank

	/* ��� ��� � �� */
	$name = "cartunbank";
	/* ��� ������ � �� */
	$password = "basie5";

	$xmlrpc_internalencoding = 'UTF-8';

	$date = time();
	$year = date("Y", $date);
	$mon = date("m", $date);
	$day = date("d", $date);
	$hour = date("G", $date);
	$min = date("i", $date);

	/* ��������� ������ � ������������ ����������� */

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

	/* �� ������ ������� ������� ��������� */

	$post2 = array(
		new xmlrpcval($post, "struct")
	);

	/* ������� XML ��������� ��� ������� */

	$f = new xmlrpcmsg('LJ.XMLRPC.postevent', $post2);

	/* ��������� ������ */

	$c = new xmlrpc_client("/interface/xmlrpc", "www.livejournal.com", 80);
	$c->request_charset_encoding = "UTF-8";

	/* ���������� XML ��������� �� ������ */

	$r = $c->send($f);
		
	/* ����������� ��������� */
		
	if(!$r->faultCode())
	{
		/* ��������� ������� ������� � �������� XML-��������� */
		$v = php_xmlrpc_decode($r->value());
		print_r($v);
	}
	else
	{
		/* ������ ������ ������ */
		print "An error occurred: ";
		print "Code: ".htmlspecialchars($r->faultCode());
		print "Reason: '".htmlspecialchars($r->faultString())."'\n";
	}
}
?>