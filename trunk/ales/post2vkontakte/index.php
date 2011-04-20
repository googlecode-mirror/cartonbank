<?
fw ($_GET['code']);


function fw($text)
{
	$fp = fopen('code.txt', 'w');
	fwrite($fp, $text);
	fclose($fp);
}
?>