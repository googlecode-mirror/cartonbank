<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Images &middot; jQuery Masonry</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
  <link rel="stylesheet" href="../css/style.css" />
  <!-- scripts at bottom of page -->
</head>
<body class="demos ">
  <nav id="site-nav">
    <h1><a href="../index.html">jQuery Masonry</a></h1>
    
    <h2>Docs</h2>
    <h2>Demos</h2>
    
    <ul class="demos-list">
            <li><a href="../demos/basic-single-column.html">Basic single-column</a>
            </li><li><a href="../demos/basic-multi-column.html">Basic multi-column</a>
            </li><li><a href="../demos/images.html">Images</a>
            </li><li><a href="../demos/tumblelog.html">Tumblelog example</a>
            </li><li><a href="../demos/animating-jquery.html">Animating with jQuery</a>
            </li><li><a href="../demos/animating-css-transitions.html">Animating with CSS Transitions</a>
            </li><li><a href="../demos/animating-modernizr.html">Animating with Modernizr</a>
            </li><li><a href="../demos/adding-items.html">Adding items</a>
        </li><li class="current"><a href="#content">Infinite Scroll</a></li>
            <li><a href="../demos/gutters.html">Gutters</a>
            </li><li><a href="../demos/right-to-left.html">Right-to-left</a>
            </li><li><a href="../demos/centered.html">Centered</a>
            </li><li><a href="../demos/fluid.html">Fluid</a>
            </li><li><a href="../demos/corner-stamp.html">Corner stamp</a>
    </li></ul>
  </nav>
 
  <section id="content">
    
    
      <h1>Images</h1>
    
<div id="container" class="transitions-enabled infinite-scroll">
  
  
<?

require_once("/home/www/cb3/ales/config.php");
$cartoonsperpage = 30;


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
$sql = "SELECT id, image from wp_product_list".$sortby." LIMIT ".$cartoonsperpage." OFFSET ".$offset;
//pokazh($sql);

// roll out result
$result = mysql_query($sql);

    $output = '';

    while($r = mysql_fetch_array($result)) {
        //$output .= '<div class="block" id="cid'.$r["id"].'">'.$counter.": ".$r["id"].'</div>';
        $output .= '<div class="box" id="cid'.$r["id"].'"><img src="http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/images/'.$r["image"].'"></div>';
    }


    echo $output;

?>    
    
    
    
  
</div> <!-- #container -->

<nav id="page_nav">
  <a href="../pages/2.html"></a>
</nav>


<script src="../js/jquery-1.7.1.min.js"></script>
<script src="../jquery.masonry.min.js"></script>
<script src="../js/jquery.infinitescroll.min.js"></script>
<script>
$(function(){
var $container = $('#container');
$container.imagesLoaded(function(){
$container.masonry({
itemSelector: '.box',
columnWidth: 100
});
});
$container.infinitescroll({
navSelector : '#page-nav', // selector for the paged navigation
nextSelector : '#page-nav a', // selector for the NEXT link (to page 2)
itemSelector : '.box', // selector for all items you'll retrieve
loading: {
finishedMsg: 'No more pages to load.',
img: 'http://i.imgur.com/6RMhx.gif'
}
},
// trigger Masonry as a callback
function( newElements ) {
// hide new items while they are loading
var $newElems = $( newElements ).css({ opacity: 0 });
// ensure that images load before adding to masonry layout
$newElems.imagesLoaded(function(){
// show elems now they're ready
$newElems.animate({ opacity: 1 });
$container.masonry( 'appended', $newElems, true );
});
}
);
});
</script>

    
    <footer id="site-footer">
      footer
    </footer>
    
  </section> <!-- #content -->

</body>
</html>