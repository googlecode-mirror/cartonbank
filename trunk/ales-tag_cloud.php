<div style="float:right;">
<br /><h2>������ �����</h2>
<?
function edit_value(&$value) 
{ 
    $value = trim($value); 
	$value = str_replace("\&quot;","",$value);
	$value = str_replace("\"","",$value);
	$value = trim($value);
	$value = mb_strtolower($value,"UTF8");
}

$sql = "SELECT DISTINCT `additional_description` AS name FROM `wp_product_list` WHERE `active` = 1 AND `additional_description` > ''";
	
	global $wpdb;
	$result_array = $wpdb->get_results($sql,ARRAY_A);


//$result_array = array_map('trim',$result_array);

	$result_string = r_implode(",",$result_array);
	$result_string = str_replace(",,",",",$result_string); 
	$result_string = str_replace(", ","|",$result_string);
	$result_string = str_replace(",","|",$result_string);
	$tags = explode ("|",$result_string);
	array_walk($tags, 'edit_value');

	sort($tags);

foreach ($tags as $key => $value) { 
   echo("<pre>value=".print_r($value,true)."</pre>");
   if (is_null($value) or $value=='' or $value == ' ') 
	   { 
    unset($tags[$key]);
	//exit("<br />unset<br />");
  } 
}
exit("<pre>tags<br />".print_r($tags,true)."</pre>");

//exit("<pre>tags<br />".print_r($tags,true)."</pre>");


$cloud = new wordCloud($tags);
echo $cloud->showCloud();
?> 
</div>

</div>

    <style>
    <!--
    .word {
    /*font-family: Tahoma;letter-spacing: 3px;*/
    padding: 4px 4px 4px 4px;
    
    }
    span.size1 {
    color: #000;
    font-size: 2.5em;font-weight: 800;
    }
    span.size2 {
    color: #333;
    font-size:2.2em;font-weight: 800;
    }
    span.size3 {
    color: #666;
    font-size: 2.0em;font-weight: 700;
    }
    span.size4 {
    color: #999;
    font-size: 1.0em;font-weight: 600;
    }
    span.size5 {
    color: #aaa;
    font-size: 1.6em;font-weight: 500;
    }
    span.size6 {
    color: #bbb;
    font-size: 1.4em;font-weight: 400;
    }
    span.size7 {
    color: #ccc;
    font-size: 1.2em;font-weight: 300;
    }
    span.size8 {
	color: #ddd;font-size: 0.8em;font-weight: 200;
    }
    span.size0 {
	color: #aaa; font-size: 0.7em; font-weight: 100;
	}
	//-->
    </style>
<?
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
    $return[$word]['sizeRange'] = $sizeRange;
    if ($currentColour)
    $return[$word]['randomColour'] = $currentColour;
    }
    else if ($returnType == "html")
    {
    $return .= "<span class='word size{$sizeRange}'> <a href='<? echo SITEURL;?>?page_id=29&cs={$word}'>{$word}</a> </span>";
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
?>