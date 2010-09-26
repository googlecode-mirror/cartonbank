<?
if(isset($_GET['w']))
{
	echo ("write to file");
	write_tags_to_file();
}
else
{
	read_tags_to_browser();
}
function read_tags_to_browser()
{
	$filename = '/home/www/cb/wp-content/tags/tagcloud.htm';
	$contents = 'no tags found';

	if (file_exists($filename)) {
		if (!$handle = fopen($filename, 'r')) {
			 echo "Cannot open file ($filename)";
			 exit;
		}

	$contents = fread($handle, filesize($filename));
	fclose($handle);

//pokazh($contents,"contents");
	echo $contents;

	} else {
		echo "The file $filename is not found";
	}
}

function write_tags_to_file()
	{
	$filename = '/home/www/cb/wp-content/tags/tagcloud.htm';

	$somecontent = create_tag_cloud();

	if (is_writable($filename)) {

		if (!$handle = fopen($filename, 'w')) {
			 echo "Cannot open file ($filename)";
			 exit;
		}

		if (fwrite($handle, $somecontent) === FALSE) {
			echo "Cannot write to file ($filename)";
			exit;
		}

		echo "Success, wrote text to file ($filename)";

		fclose($handle);

	} else {
		echo "The file $filename is not writable";
	}
	}


function create_tag_cloud()
	{
	$sql = "SELECT DISTINCT `additional_description` AS name FROM `wp_product_list` WHERE `active` = 1 AND `visible` = 1 AND `additional_description` > '' ORDER BY id DESC LIMIT 500";
		
		global $wpdb;
		$result_array = $wpdb->get_results($sql,ARRAY_A);

	//$result_array = array_map('trim',$result_array);

		$result_string = r_implode(",",$result_array);
		$result_string = str_replace(".",",",$result_string); 
		$result_string = str_replace(",,",",",$result_string); 
		$result_string = str_replace(", ","|",$result_string);
		$result_string = str_replace(",","|",$result_string);
		$tags = explode ("|",$result_string);
		array_walk($tags, 'edit_value');

		sort($tags);

	foreach ($tags as $key => $value) { 
	   if (is_null($value)) 
		{ 
				unset($tags[$key]);
				//echo "<br>unset null<br>";
		} 
	   if ($value=='') 
		{ 
				unset($tags[$key]);
				//echo "<br>unset empty<br>";
		} 
	   if ($value == ' ') 
		{ 
				unset($tags[$key]);
				//echo "<br>unset space<br>";
		} 
	}

	$cloud = new wordCloud($tags);

	return $cloud->showCloud();
}
	/*
    @wordCloud
		Author: Derek Harvey
		Website: www.lotsofcode.com

		@Description
		PHP Tag Cloud Class, a nice and simple way to create a php tag cloud, a database and non-database solution.
		*/

    class wordCloud
    {
		var $wordsArray = array();

		/*
			* PHP 5 Constructor
			*
			* @param array $words
			* @return void
			*/

		function __construct($words = false)
			{
				if ($words !== false && is_array($words))
				{
				foreach ($words as $key => $value)
				{
				$this->addWord($value);
				}
				}
			}

		/*
			* PHP 4 Constructor
			*
			* @param array $words
			* @return void
			*/

		function wordCloud($words = false)
			{
				$this->__construct($words);
			}

		/*
			* Assign word to array
			*
			* @param string $word
			* @return string
			*/

		function addWord($word, $value = 1)
			{
				$word = strtolower($word);
				if (array_key_exists($word, $this->wordsArray))
				$this->wordsArray[$word] += $value;
				else
				$this->wordsArray[$word] = $value;
				return $this->wordsArray[$word];
			}

		/*
			* Shuffle associated names in array
			*/

		function shuffleCloud()
			{
				$keys = array_keys($this->wordsArray);

				//shuffle($keys);

				if (count($keys) && is_array($keys))
				{
				$tmpArray = $this->wordsArray;
				$this->wordsArray = array();
				foreach ($keys as $key => $value)
				$this->wordsArray[$value] = $tmpArray[$value];
				}
			}

		/*
			* Calculate size of words array
			*/

		function getCloudSize()
			{
				return array_sum($this->wordsArray);
			}

		/*
			* Get the class range using a percentage
			*
			* @returns int $class
			*/

		function getClassFromPercent($percent)
			{
				if ($percent >= 99)
				$class = 1;
				else if ($percent >= 70)
				$class = 2;
				else if ($percent >= 60)
				$class = 3;
				else if ($percent >= 50)
				$class = 4;
				else if ($percent >= 40)
				$class = 5;
				else if ($percent >= 30)
				$class = 6;
				else if ($percent >= 20)
				$class = 7;
				else if ($percent >= 10)
				$class = 8;
				else if ($percent >= 5)
				$class = 9;
				else
				$class = 0;

			return $class;
			}

		/*
			* Create the HTML code for each word and apply font size.
			*
			* @returns string $spans
			*/

		function showCloud($returnType = "html")
			{
				$this->shuffleCloud();
				$this->max = max($this->wordsArray);

				if (is_array($this->wordsArray))
				{
				$return = ($returnType == "html" ? "" : ($returnType == "array" ? array() : ""));
				foreach ($this->wordsArray as $word => $popularity)
				{
				$sizeRange = $this->getClassFromPercent(($popularity / $this->max) * 100);
				if ($returnType == "array")
				{
				$return[$word]['word'] = $word;
				$return[$word]['popularity'] = $popularity;
				$return[$word]['sizeRange'] = $sizeRange;
				if ($currentColour)
				$return[$word]['randomColour'] = $currentColour;
				}
				else if ($returnType == "html")
				{
			if($popularity > 1)
					{
						$return .= " <a class='size{$sizeRange}' href='?page_id=29&cs={$word}' title='$popularity'>{$word}</a> ";
					}
					else
					{
						$return .= " <a class='size{$sizeRange}' href='?page_id=29&cs={$word}'>{$word}</a> ";
					}
				}
				}
				return $return;
				}
			}
    }

function r_implode( $glue, $pieces )
	{
	  foreach( $pieces as $r_pieces )
	  {
		if( is_array( $r_pieces ) )
		{
		  $retVal[] = r_implode( $glue, $r_pieces );
		}
		else
		{
		  $retVal[] = $r_pieces;
		}
	  }
	  return implode( $glue, $retVal );
	}

function edit_value(&$value) 
	{ 
		$value = trim($value); 
		$value = str_replace("\&quot;","",$value);
		$value = str_replace("\"","",$value);
		$value = trim($value);
		$value = mb_strtolower($value,"UTF8");
	}
?>
		<style>
			<!--
			.word {
			/*font-family: Tahoma;letter-spacing: 3px;*/
			padding: 4px 4px 4px 4px;
			
			}
			a.size1 {
			color: #000;
			font-size: 2.6em;font-weight: 800;
			}
			a.size2 {
			color: #000;
			font-size:2.4em;font-weight: 800;
			}
			a.size3 {
			color: #000;
			font-size: 2.2em;font-weight: 700;
			}
			a.size4 {
			color: #000;
			font-size: 2.0em;font-weight: 600;
			}
			a.size5 {
			color: #000;
			font-size: 1.8em;font-weight: 500;
			}
			a.size6 {
			color: #000;
			font-size: 1.6em;font-weight: 400;
			}
			a.size7 {
			color: #000;
			font-size: 1.4em;font-weight: 300;
			}
			a.size8 {
			color: #000;font-size: 1.2em;font-weight: 200;
			}
			a.size9 {
			color: #000; font-size: 1.0em; font-weight: 100;
			}
			a.size0 {
			color: #000; font-size: 0.8em; font-weight: 100;
			}
			//-->
			</style>
