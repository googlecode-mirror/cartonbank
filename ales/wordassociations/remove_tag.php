<?
include("/home/www/cb/ales/config.php");
global $imagepath;

if(isset($_GET['wrd']) && $_GET['wrd']!='' && isset($_GET['id']) && is_numeric($_GET['id']))
{
	$id = $_GET['id'];
	$wrd = trim($_GET['wrd']);

	// update database
	$sql = "select additional_description from wp_product_list where id=".$id." LIMIT 1";
	
		$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
		mysql_set_charset('utf8',$link);
	
		$result = mysql_query($sql);
		if (!$result) {die('Invalid query: ' . mysql_error());}

	$product=mysql_fetch_array($result);
	$_tags = $product['additional_description'];
	
	$_tags_array = explode(',',$_tags);

	$_remove_this_one = '';
		foreach ($_tags_array as $key => $value)
		{
			$word = trim($value);

			fw("word: [".$word."]");


			if ($wrd==$word)
			{
				fw("found: [".$word."]");

				$_remove_this_one = $key;
			}
		}

	if ($_remove_this_one!='')
	{
		unset($_tags_array[$_remove_this_one]);
		$_tags_array = array_values($_tags_array);
		var_dump($_tags_array);
	}

	// remove duplcates
	$_tags_array = remove_duplicates($_tags_array);

	$original_tags = implode(", ",$_tags_array);
	$original_tags = str_replace("  ", " ", $original_tags);


/*
	$original_tags = str_replace(",", ", ", $original_tags);
	$original_tags = str_replace("  ", " ", $original_tags);
	$original_tags = str_replace($wrd, "", $original_tags);
*/	
	$sql = "update wp_product_list set additional_description = '".$original_tags."' where id=".$id;


	$result = mysql_query($sql);

		if (!$result) {die('Invalid query: ' . mysql_error());}
		mysql_close($link);
}

return;



function remove_duplicates($array)
{
	$array = array_unique($array);
	return $array;
}

function fw($text)
{
	$fp = fopen('_kloplog.txt', 'a');
	fwrite($fp, "\r\n");
	fwrite($fp, $text);
	fclose($fp);
}

?>