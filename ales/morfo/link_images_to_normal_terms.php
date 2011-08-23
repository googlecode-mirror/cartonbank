<?php 
// this sctipt is for finding normal terms in image description
// and saving to database link between image is and normal term id


/////////////////////////////// settings
	include("/home/www/cb3/ales/config.php");
	global $path;
	$path="/home/www/cb3/ales/morfo/";
	$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
	mysql_set_charset('utf8',$link);

	///CONST
	$wordslimit = ''; // LIMIT SQL string
	$start_record = 12501;
	$records = 10; // how many word descriptions to check at once?




////////////////////////////// TERMS association:

	for ($i = 1; $i <= 100; $i++) {

		$wordslimit = "" . $start_record.", ". $records;


		//// GET DESCRIPTION FROM IMAGES
		$sql = "select id from wp_product_list order by id desc limit ".$wordslimit;

		echo ("\n\n".$sql);
		$result = mysql_query($sql);
		while($row=mysql_fetch_array($result))
		{
			$id = $row['id'];

			$original_description = get_description_from_image_by_id($id);

			/// CLEAN UP DESCRIPTION
			$original_description = trim(preg_replace('#[^\p{L}\p{N}]+#u', ' ', $original_description));
			$original_description = trim(str_replace('quot', ' ', $original_description));

			//// GET NORMILIZED DESCRIPTION
			$normalized_description = normalize_string($original_description, $path);
			$arr_normalized_description = explode(" ", $normalized_description);

			//// SAVE LINKS TO NORMAILEZED IDS FOR IMAGE
			save_links_to_normalized_terms($id,$arr_normalized_description);
			sleep(1);
		}

		$start_record = $start_record + $records;
		sleep(6);
	}

	echo "\ndone\n";




/// FUNCTIONS


function save_normal_term($arr_normalized_description)
{
	$first_record = true;
	$sql = "insert ignore into terms_normal (term_normal) values ";
	
	foreach ($arr_normalized_description as $term)
	{
		if (mb_strlen($term,'utf-8') > 2)
		{
			// save term
			if ($first_record == true)
			{
				$sql = $sql . "('".$term."')";
			}
			else
			{
				$sql = $sql.",('".$term."')";
			}
		}
		else
		{
			//pokazh($term,"excluded");
		}
		
		$first_record = false;

	}
			//pokazh($sql);
			$result = mysql_query($sql);
}

function save_links_to_normalized_terms($id,$arr_normalized_description)
{
	$first_record = true;
	$insert_sql = "insert into term_to_image (term_id, image_id) values ";


	foreach ($arr_normalized_description as $term)
	{
		//pokazh($term);
		if (mb_strlen($term,'utf-8') > 2)
		{
			$sql = "select id from terms_normal where term_normal = '".$term."' and exclude != 1"; // check if normal term exist
			$result = mysql_query($sql);
			$_id = mysql_fetch_row($result);

			if ($_id=='')
			{
				// TODO insert new normalized term and get id
				$term_id = 0;
			}
			else
			{
				$term_id = $_id[0];
			}

			if (mb_strlen($term,'utf-8') > 2)
			{
				// save term
				if ($first_record == true)
				{
					$insert_sql = $insert_sql . "(".$term_id.",".$id.")";
				}
				else
				{
					$insert_sql = $insert_sql.",(".$term_id.",".$id.")";
				}
			}
			else
			{
				//pokazh($term,"excluded");
			}
			$first_record = false;
		}
	}
	echo ("\n".$insert_sql);

	$result = mysql_query($insert_sql);
			//pokazh($insert_sql);
}

function get_description_from_images($wordslimit='1')
{
	// TODO limit?
	$sql = "SELECT name, additional_description, description FROM `wp_product_list` order by id desc limit " . $wordslimit;
	echo ("\n".$sql);
	$result = mysql_query($sql); 
	if (!$result) {die('Invalid query: ' . mysql_error());}

	$out = '';

	while($row=mysql_fetch_array($result))
	{
		$out = $out.' '.$row['name'].' '.$row['description'].' '.$row['additional_description'];
	}
	return $out;
}

function get_description_from_image_by_id($id)
{
	// TODO limit?
	$sql = "SELECT name, additional_description, description FROM `wp_product_list` where id = ".$id;
	//pokazh($sql);
	$result = mysql_query($sql); 
	if (!$result) {die('Invalid query: ' . mysql_error());}

	$out = '';

	while($row=mysql_fetch_array($result))
	{
		$out = $out.' '.$row['name'].' '.$row['description'].' '.$row['additional_description'];
	}
	return $out;
}

function normalize_string($q, $path)
{
	$q = iconv('utf-8', 'windows-1251', mb_strtolower($q, 'utf-8'));
	$out = array();
	exec('echo "'.$q.'" | '.rtrim($path,'/ ').'/mystem -c', $out);
	$q = implode('', $out);
	$q = str_replace('}', ' ', $q);
	$q = trim(preg_replace('#\s+#is', ' ', $q));
	$q = explode(' ', $q);
	$out = '';
	foreach($q as $w)
	{
		$w = str_replace('?', '', $w);
		$w = explode('{', $w);
		if (count($w)<2||preg_match('#^(\d+|[a-z0-9A-Z]+)$#is', $w[0])) 
		{
			$out .= $w[0] . ' ';
		}
		else
		{
			$w = explode('|', $w[1]);
			$out .= $w[0] . ' ';
		}
	}
	return trim(iconv('windows-1251', 'utf-8', $out));
}

function highlight_term($term, $string)
{
	$string = trim(str_replace(mb_strtolower($term, 'utf-8'), "<span style='padding:1px;background-color:#EAFFFF;'>".mb_strtolower($term, 'utf-8')."</span>", mb_strtolower($string, 'utf-8')));
	return $string;
}

function search_images_by_term($term = 'доктор')
{
	global $path;
		// find ID of uniques normal term
		$term = normalize_string($term,$path);
		$id_list = "";
		//pokazh($term,"Ищем слово");

		$first_id = true;
		$sql = "select id, exclude from terms_normal where term_normal='".$term."' and exclude != 1";
		$result = mysql_query($sql);
		while($row=mysql_fetch_array($result))
			{
				$id = $row['id'];
			}
		// find image ids linked to normal term id
		if ($id == '')
		{
			echo('ничего не найдено');
			return;
		}
		else
		{
			echo "<div><h1>Рисунки со словом '".$term."'</h1></div>";
		}
		$sql = "select image_id from term_to_image where term_id='".$id."'";

		$result = mysql_query($sql);
		while($row=mysql_fetch_array($result))
			{
				if ($first_id)
				{
					$id_list = $row['image_id']." ";
				}
				else
				{
					$id_list = $id_list.", ".$row['image_id'];
				}
				$first_id=false;
			}

		// output image previews
		$sql = "select image, name, description, additional_description from wp_product_list where id in (".$id_list.")";
		$result = mysql_query($sql);
		if(!$result){die("ничего не найдено");}

		$description_set = "";

		while($row=mysql_fetch_array($result))
			{
				echo "<table width='140' style='float:left;font-size:.8em;'><tr><td>";
				echo "<img src='http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/images/".$row['image']."'>";
				echo "</td></tr><tr><td><div class='descr'>";
				$descr = highlight_term($term, mb_strtolower($row['name'], 'utf-8')."<br>".mb_strtolower($row['description'], 'utf-8')."<br>".mb_strtolower($row['additional_description'], 'utf-8'));
				echo ($descr."");
				echo "</div></td></tr></table>";

				$description_set = $description_set . $row['name']." ".$row['description']." ".$row['additional_description']. " " ;
			}
		echo "<br style='clear:both;'>";
		return $description_set;
}

function search_images_by_term_textonly($term)
{
	if (mb_strlen($k,'utf-8') < 3 && $k!='') // excluse initial Term
	{
		return;
	}

	global $path;
		// find ID of uniques normal term
		//$term = normalize_string($term,$path);
		$id_list = "";
			///pokazh($term,"Ищем слово");
		$first_id = true;
		$sql = "select id from terms_normal where term_normal='".$term."' and exclude != 1";
		$result = mysql_query($sql);
		while($row=mysql_fetch_array($result))
			{
				$id = $row['id'];
			}
		
		// find image ids linked to normal term id
		if ($id == '')
		{
			//echo('ничего не найдено');
			return;
		}
		$sql = "select image_id from term_to_image where term_id='".$id."'";

		$result = mysql_query($sql);
		while($row=mysql_fetch_array($result))
			{
				if ($first_id)
				{
					$id_list = $row['image_id']." ";
				}
				else
				{
					$id_list = $id_list.", ".$row['image_id'];
				}
				$first_id=false;
			}

		$sql = "select name, description, additional_description from wp_product_list where id in (".$id_list.")";
		$result = mysql_query($sql);
		if(!$result)
			{
			//echo("ничего не найдено");
			return;
			}

		$description_set = "";
		while($row=mysql_fetch_array($result))
			{
				$description_set = $description_set . $row['name']." ".$row['description']." ".$row['additional_description']. " " ;
			}
			//echo $description_set;
		return $description_set;
}
function remove_shorts($var)
{
	//echo $var;
	if (mb_strlen($var,'utf-8') > 2)
	{
		return $var;
	}
}
function remove_singles($var)
{
	//remove words found once only
	if ($var != 1)
	{
		return $var;
	}
}

function terms_output($arr)
{
	foreach($arr as $k=>$v)
	{
		echo "<div class='term'><a href='http://cartoonbank.ru/ales/morfo/morfo.php?s=$k'>".$k." [".$v."]</a></div>";
	}
	echo "<br style='clear:both;'";
}

?>