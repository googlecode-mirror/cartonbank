<?
require_once('../../wp-config.php');
?>
<style>
.dpage
{
	border:1px solid silver;
	width:600px;
	height:600px;
	padding:2px;
}
.dinputword
{
		border:1px solid silver;
		width:170px;
		height:60px;
		float:left;
		padding:2px;
		margin:2px;
	}
	.dsuggestionwords
	{
		border:1px solid silver;
		width:200px;
		height:500px;
		float:left;
		background-color:#FFCCFF;
		padding:2px;
		margin:2px;
	}
	.dsearchwords
	{
		border:1px solid silver;
		width:200px;
		height:500px;
		float:left;
		display:block;
		background-color:#CCFF99;
		padding:2px;
		margin:2px;
	}
	</style>

<h1>wizard</h1>
<div id='page' class='dpage'>
<div id='inputword' class='dinputword'><form method=post action="#">
	<input type="text" id="wrd" name="inputword">
	<a href="#" onclick="addword();">добавить слово</a> 
</form></div>
<div id='suggestionwords' class='dsuggestionwords'>подсказки</div>
<div id='searchwords' class='dsearchwords'>слова для поиска</div>
</div>

<script language="JavaScript">
<!--
function addword()
{
	var inputword = document.getElementById('wrd').value;
	var searchwords = document.getElementById('searchwords');
	searchwords.innerHTML = searchwords.innerHTML+"<br>"+inputword;
}
//-->
</script>















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
?>