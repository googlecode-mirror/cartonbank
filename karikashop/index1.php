=
<?
echo"<pre>";
echo $_SERVER['REQUEST_URI'];
$url = $_SERVER['REQUEST_URI'];

$newurl = str_replace("cartoonist.name", "http://cartoonists.cartoonbank.ru/", $url);
echo $newurl;

print_r ($_SERVER);
echo"</pre>";

?>
.