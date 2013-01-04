<?
require_once("/home/www/cb3/ales/config.php");
$cartoonsperpage = 20;


// get page number
if (isset($_REQUEST['page']))
    {$page = $_REQUEST['page'];}
    else 
    {$page=0;}

// get sorting
if (isset($_REQUEST['sort']))
    {$sortby = $_REQUEST['sort'];}
    else 
    {$sortby=" ORDER BY id DESC ";}

// calculate offset
$offset = $cartoonsperpage * $page;
	
// get cartoon ids for the current page
$sql = "SELECT id from wp_product_list".$sortby." LIMIT ".$cartoonsperpage." OFFSET ".$offset;
//pokazh($sql);

// roll out result
$result = mysql_query($sql);
if ($page==0){
	$output = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title></title><link rel="stylesheet" type="text/css" href="css/styles1.css"><link href="http://fonts.googleapis.com/css?family=Cambo" rel="stylesheet" type="text/css"><script src="http://code.jquery.com/jquery-latest.js"></script><style type="text/css"></style><script src="js/script.js"></script><script src="js/jquery.infinitescroll.min.js"></script><!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]--><script>window["_GOOG_TRANS_EXT_VER"] = "1";</script></head><body onload="setupBlocks();"><div id="container" style="width:100%;border:thin solid green;">';
}
else
{
	$output = '';
}
    while($r = mysql_fetch_array($result)) {
        //$output .= '<div class="block" id="cid'.$r["id"].'">'.$counter.": ".$r["id"].'</div>';
        $output .= '<div class="block" id="cid'.$r["id"].'"></div>';
    }


if ($page>0)
{
	echo $output;
	exit;
}

// close container
	$output .= '</div><!--#container-->';



// add more images
$nextpage = $page+1;

// load more div
$output .= '<div style="width:100%;border:thin solid red;"><br /><br /><div id="loadmoreajaxloader" style="display:none;"><center><img src="ajax-loader.gif" /></center></div><div id="pagecounter" style="display:none;">0</div></div><!--#loadmoreajaxloader-->';

$output .= '<script type="text/javascript">
$(window).scroll(function()
{
    if($(window).scrollTop() == $(document).height() - $(window).height())
    {
        var pagecounter = parseInt($("div#pagecounter").text());
        var nextpage = pagecounter + 1;
        $("div#pagecounter").text(nextpage);
        $("div#loadmoreajaxloader").show();
        $.ajax({
        url: "http://cartoonbank.ru/ales/masonry/getnextcartoons.php?page="+nextpage,
        success: function(html)
        {
            if(html)
            {
                $("#container").append(html);
                $("div#loadmoreajaxloader").hide();
				setupBlocks();
            }else
            {
                $("div#loadmoreajaxloader").html("<center>No more posts to show.</center>");
            }
        }
        });
    }
});
</script>';

$output .= '</body></html>';

    
    
    
    
    
    

echo $output;
?>