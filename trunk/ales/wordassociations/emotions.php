<?
    include("/home/www/cb3/ales/config.php");
    global $imagepath;

    if(isset($_GET['id']) && $_GET['id']!='')
    {
        $id = urlencode(trim($_GET['id']));
    }
    else
    {$id = '11354';}


    $tagsarray = get_cartoon($id);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			<link rel="stylesheet" href="http://code.jquery.com/ui/1.8.18/themes/base/jquery-ui.css" type="text/css" media="all" />
			<link rel="stylesheet" href="http://static.jquery.com/ui/css/demo-docs-theme/ui.theme.css" type="text/css" media="all" />
			<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
 			<script src="http://code.jquery.com/ui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>
        <title> Эмоции, чувства </title>

        <script language="JavaScript">
            <!--
            function sendup(wrd,id)
            {
                var oldtext = jQuery('#currenttags').html();
                jQuery('#currenttags').html(oldtext + '<span class="td" onclick="senddown(this.innerText,' + id + ');var z=&quot;&quot;;jQuery(this).html(z);return false;">' + wrd + '</span>');
                wrd = encodeURIComponent(wrd);
                jQuery.post("http://cartoonbank.ru/ales/wordassociations/add_tag.php?id="+id+"&wrd="+wrd);
            }

            function senddown(wrd,id)
            {
                var oldtext = jQuery('#currenttags').html();
                wrd = encodeURIComponent(wrd);
                jQuery.post("http://cartoonbank.ru/ales/wordassociations/remove_tag.php?id="+id+"&wrd="+wrd);
            }

            //-->
        </script>

        <script>
    jQuery(function() {
        <? 
            echo "var availableTags = [";
            echo get_words1();
            echo "];";
        ?>
        
        jQuery( "#newtag" ).autocomplete({
            source: availableTags
        });
    });
	function keyevent(wrd,e)
        {
          if (e.keyCode == 13)
          { 
			id = <? echo $id;?>;
			sendup(wrd,id);
			jQuery("#newtag").val('');
          }
        } 
    </script>

        
        <style>
            input{
                font-size: 18px;
                font-weight: bold;
                color: green;
                border:thin solid silver;
                padding:4px;
                margin:-1px;
            }
            
            .t{
                background-clip: border-box; background-color: #EFEFEF; background-image: none; background-origin: padding-box; border-color: #999; border-style: solid; border-width: 1px; color: #333; cursor: pointer; display: block; float: left; font-family: 'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif; font-size: 11px; height: 18px; line-height: 18px; margin:1px; outline-color: #333; outline-style: none; outline-width: 0px; padding: 2px; 
            }
            .td{
                background-clip: border-box; background-color: #FFD1C6; background-image: none; background-origin: padding-box; border-color: #999; border-style: solid; border-width: 1px; color: #333; cursor: pointer; display: block; float: left; font-family: 'Lucida Grande', Verdana, Arial, 'Bitstream Vera Sans', sans-serif; font-size: 11px; height: 18px; line-height: 18px; margin:1px; outline-color: #333; outline-style: none; outline-width: 0px; padding: 2px; 
            }
             .img, .image {
                background-color: silver;
                display: block;
                float: left;
                padding: 4px;
                width: 400px;
            }

            .title{
                display: block;
				width: 120px;
                float:left;
                margin-right: 4px;
                font-weight: bold;
                font-family: Arial;
                text-align: left;
            }
            .typetag {
                clear: both;
                display: block;
                float: left;
                margin: 2px;
            }
            .currenttags{
            }
			.newtags{
				clear:both;
				margin-top:10px;
			}
            #left{
            }
			a.button{
                font-family: Arial;
                text-align: left;
				text-decoration: none;
				padding:4px;
				background-color: #d9fdce;
				color: grey;
				font-size: 12px;
			}
        </style>
    </head>

    <body>
		<div class="wrapper">        
            <div id="left" class="left">
                <div style="margin-bottom: 1em;">
                    <a class="button" href="?id=<?=($id-1);?>"><< [Предыдущий рисунок]</a>&nbsp;
                    <a class="button" href="?id=<?=rand(251, 16052);?>">[Случайный рисунок]</a>&nbsp;
                    <a class="button" href="?id=<?=($id+1);?>">[Следующий рисунок] >></a>&nbsp;
                    <a class="button" href="http://cartoonbank.ru/ales/wordassociations/emotionslist.html">[Список эмоций]</a>&nbsp;

                </div>

				<div class="currenttags">
                    <div class='title'>Текущие тэги:</div>
                    <div id="currenttags"><? echo get_currenttags($original_tags,$id); ?></div>
                </div><!-- currenttags -->

                <div class="typetag">
                            <div class='title'>Введите тэг:</div>
                            <input id="newtag" onKeyDown="javascript:keyevent(this.value, event);" >
                </div><!-- typetag -->
                             
                <div class="image">
                    <? echo ($imagepath);?>
                </div><!-- image -->


            </div> <!-- left -->

            <div class="newtags">
                <?
                    //echo "<span class='t' onclick='sendup(this.innerText,".$id.");return false;'>test_tag</span> ";
                    if (count($tagsarray)>0)
                    {
                        foreach ($tagsarray as $key => $value)
                        {
                            echo "<span class='t' onclick='sendup(jQuery(this).html(),".$id.");var z=\"\";jQuery(this).html(z);return false;'>".$value."</span> ";
                        }
                    }
                ?>
            </div><!-- newtags -->

        </div><!-- wrapper -->
    </body>
</html>


<?
    function get_words($word)
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
        $out = array();
        foreach ($content as $item)
        {
            /*** add node value to the out array ***/
            $out[] = $item->nodeValue;
        }
        /*** return the results ***/
        return $out;
    }

    function get_words1()
    {
        $url = 'http://cartoonbank.ru/ales/wordassociations/emotionslist.html'; 
        $contents = file_get_contents($url);
        $tag="a";
        return (getTextBetweenTags1($tag, $contents, $strict=0));
    }

    function getTextBetweenTags1($tag, $html, $strict=0)
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


        $imagepath = "<a href='http://cartoonbank.ru/wp-admin/admin.php?page=wp-shopping-cart/display-items.php&edid=".$id."' target='_blank' title='перейти в редактор рисунка'><img class='img' src='http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."' border='none'></a>";

        $_tags = nl2br(stripslashes($product['additional_description']));
        $_tags_array = explode(',',$_tags);
        foreach ($_tags_array as $key => $value)
        {
            $word = urlencode(trim($value));
            $out = "";//get_words($word); // download array of associations
            $out = get_words($word); // download array of associations
            if (count($out)>36)
            {
                //$out = array_slice($out,37,25); // trancate array to meaningfil words
                $output = array_merge($output, $out); // merge all association arrays
            }

        }

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
                $output .=  "<span class='td' onclick='senddown(this.innerText,".$id.");var z=\"\";jQuery(this).html(z);return false;'>".$value."</span> ";
            }
        }

        return $output;
    }


?>