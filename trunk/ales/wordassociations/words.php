<?
include("/home/www/cb/ales/config.php");
global $imagepath;

if(isset($_GET['id']) && $_GET['id']!='')
{
	$id = urlencode(trim($_GET['id']));
}
else
{$id = '11354';}


$tagsarray = get_cartoon($id);
//$tagsarray = array();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <script type="text/javascript" src="http://cartoonbank.ru/wp-includes/js/jquery/jquery.js"></script>
  <title> Ассоциации </title>

<script language="JavaScript">
<!--
	function sendup(wrd,id)
   {
	   	var mydiv = document.getElementById('currenttags');
		//alert (mydiv.textContent+', '+wrd);
		//jQuery.post("http://109.120.143.27/cb/ales/wordassociations/add_tag.php?id="+id, function(html){ mydiv.textContent=html;},"&wrd="+wrd);
		jQuery.post("http://109.120.143.27/cb/ales/wordassociations/add_tag.php?id="+id+"&wrd="+wrd);
		mydiv.textContent = mydiv.textContent+', '+wrd;
   }

	function senddown(wrd,id)
   {
	   	var mydiv = document.getElementById('currenttags');
		var newtext; 
		newtext = mydiv.innerHTML.replace(wrd, '');
		//alert (newtext);
		//jQuery.post("http://109.120.143.27/cb/ales/wordassociations/add_tag.php?id="+id, function(html){ mydiv.textContent=html;},"&wrd="+wrd);
		jQuery.post("http://cartoonbank.ru/ales/wordassociations/remove_tag.php?id="+id+"&wrd="+wrd);
		mydiv.innerHTML = newtext;
   }


//-->
</script>
<?
//$output .=  "<span class='td' onclick='senddown(this.innerHTML,".$id.");return false;'>".$value."</span> ";
// 1896 дворник, балет, исскуство, кризис, работа, старость, пенсия
?>

 </head>

 <body>
<style>
.t{
	background-clip: border-box; background-color: #EFEFEF; background-image: none; background-origin: padding-box; border-color: #999; border-style: solid; border-width: 1px; color: #333; cursor: pointer; display: block; float: left; font-family: 'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif; font-size: 11px; height: 18px; line-height: 18px; margin:1px; outline-color: #333; outline-style: none; outline-width: 0px; padding: 2px; 
	}
.td{
	background-clip: border-box; background-color: #FFD1C6; background-image: none; background-origin: padding-box; border-color: #999; border-style: solid; border-width: 1px; color: #333; cursor: pointer; display: block; float: left; font-family: 'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif; font-size: 11px; height: 18px; line-height: 18px; margin:1px; outline-color: #333; outline-style: none; outline-width: 0px; padding: 2px; 
	}
</style>
<table border=0 style="padding:4px;">
<tr>
	<td style="width:600px;vertical-align:top;"><div><b>Текущие тэги:</b><br /><div id="currenttags"><? echo get_currenttags($original_tags,$id); ?></div><br /></div></td>
	<td style="width:400px;vertical-align:top;" rowspan="2"><b>Предлагаем тэги:</b><br />

    <?
	//echo "<span class='t' onclick='sendup(this.innerHTML,".$id.");return false;'>test_tag</span> ";
	if (count($tagsarray)>0)
	{
		foreach ($tagsarray as $key => $value)
			{
			echo "<span class='t' onclick='sendup(this.innerHTML,".$id.");return false;'>".$value."</span> ";
			}
	}
	?>
	
	</td>
</tr>
<tr>
	<td style="vertical-align:top;width:600px;"><? echo ($imagepath);?></td>

	<!-- <td></td> -->
</tr>
</table>

 </body>
</html>


<?
function get_words($word)
{
	$url = 'http://wordassociations.ru/search?hl=ru&q='.$word.'&button=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA'; 
	$contents = file_get_contents($url); 
	$tag="a";
	return (getTextBetweenTags($tag, $contents, $strict=0));
}

function getTextBetweenTags($tag, $html, $strict=0)
{
    /*** a new dom object ***/
    //$dom = new domDocument;
	$dom = new domDocument('1.0', 'UTF-8');

    /*** load the html into the object ***/
	$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
    @$dom->loadHTML($html);

    /*** discard white space ***/
    $dom->preserveWhiteSpace = false;

    /*** the tag by its tag name ***/
    $content = $dom->getElementsByTagname($tag);

    /*** the array to return ***/
    $out = array();
    foreach ($content as $item)
    {
        /*** add node value to the out array ***/
        $out[] = $item->nodeValue;
    }
    /*** return the results ***/
    return $out;
}

function get_cartoon($id)
{
	global $mysql_hostname;
	global $mysql_user;
	global $mysql_password;
	global $mysql_database;
	global $original_tags;
	global $imagepath;

	$output= array(); // array of associations to return

	$sql = "SELECT `wp_product_list`.image, `wp_product_list`.description, `wp_product_list`.name, `wp_product_list`.additional_description, `wp_product_brands`.`name` AS brand FROM `wp_product_list`, `wp_product_files`, `wp_product_brands` WHERE  `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_product_list`.`id` = ".$id." LIMIT 1";

	$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
	mysql_set_charset('utf8',$link);
	
	$result = mysql_query($sql);
		if (!$result) {die('Invalid query: ' . mysql_error());}

	$product=mysql_fetch_array($result);
	$original_tags = $product['additional_description'];


	$imagepath = "<img src='http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."'>";

	$_tags = nl2br(stripslashes($product['additional_description']));
	$_tags_array = explode(',',$_tags);
		foreach ($_tags_array as $key => $value)
		{
			$word = urlencode(trim($value));
			$out = get_words($word); // download array of associations
			if (count($out)>36)
			{
				$out = array_slice($out,37,25); // trancate array to meaningfil words
				$output = array_merge($output, $out); // merge all association arrays
			}

		}
	//pokazh ($output);

	//unique
	$output = array_unique($output);

	return $output;

}

function get_currenttags ($additional_description,$id)
{
	$output = '';
	$_tags = nl2br(stripslashes($additional_description));
	$_tags_array = explode(',',$_tags);

	if (count($_tags_array)>0)
	{
		foreach ($_tags_array as $key => $value)
			{
			$output .=  "<span class='td' onclick='senddown(this.innerHTML,".$id.");return false;'>".$value."</span> ";
			}
	}

	return $output;
}


function fw($text)
{
	$fp = fopen('_kloplog.txt', 'w');
	fwrite($fp, $text);
	fclose($fp);
}

?>