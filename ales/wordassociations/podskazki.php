<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
 <head>
  <title> New Document </title>
			<link rel="stylesheet" href="http://code.jquery.com/ui/1.8.18/themes/base/jquery-ui.css" type="text/css" media="all" />
			<link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/css" media="all" />
			<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
 			<script src="http://code.jquery.com/ui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>
		<style type="text/css">
	
	#jq-books{width:200px;float:right;margin-right:0}
	#jq-books li{line-height:1.25em;margin:1em 0 2.8em;clear:left}
	#home-content-wrapper #jq-books a.jq-bookImg{float:left;margin-right:10px;width:55px;height:70px}
	#jq-books h3{margin:0 0 .2em 0}
	#home-content-wrapper #jq-books h3 a{font-size:1em;color:black;}
	#home-content-wrapper #jq-books a.jq-buyNow{font-size:1em;color:white;display:block;background:url(http://static.jquery.com/files/rocker/images/btn_blueSheen.gif) 50% repeat-x;text-decoration:none;color:#fff;font-weight:bold;padding:.2em .8em;float:left;margin-top:.2em;}
	
	</style>

 
 </head>

 <body>

 <script>
	$(function() {
        <? 
            echo "var availableTags = [";
            echo get_words();
            echo "];";
        ?>
		
		$( "#tags" ).autocomplete({
			source: availableTags
		});
	});
	</script>

<? 
    function get_words()
    {
        $url = 'http://cartoonbank.ru/ales/wordassociations/emotionslist.html'; 
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
        ////$out = array();
        $out ="";
        foreach ($content as $item)
        {
            /*** add node value to the out array ***/
            $out .= "\"".$item->nodeValue."\", ";
            
        }
        /*** return the results ***/
        return $out;
    }

 ?>

	
<div class="demo">

<div class="ui-widget">
	<label for="tags">Tags: </label>
	<input id="tags">
</div>

</div><!-- End demo -->



<div class="demo-description" style="display: none; ">
<p>The Autocomplete widgets provides suggestions while you type into the field. Here the suggestions are tags for programming languages, give "ja" (for Java or JavaScript) a try.</p>
<p>The datasource is a simple JavaScript array, provided to the widget using the source-option.</p>
</div><!-- End demo-description -->
 
 </body>
</html>
