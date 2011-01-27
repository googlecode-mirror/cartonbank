<script type="text/javascript" src="http://cartoonbank.ru/wp-content/plugins/purgatory/jquery.js"></script>
<script type="text/javascript" src="http://cartoonbank.ru/wp-includes/js/jquery/jquery.form.js"></script>

<?
include("config.php");
?>
<br><br>

<script>

function sendup(id)
   {
	var myelemname = "up"+id;
	var mydiv = document.getElementById(myelemname);
	ajax.post("http://cartoonbank.ru/wp-content/plugins/purgatory/up_vote.php", function(html){ mydiv.textContent=html;},"&id="+id);
   }

function senddown(id)
   {
	var myelemname = "down"+id;
	var mydiv = document.getElementById(myelemname);
	ajax.post("http://cartoonbank.ru/wp-content/plugins/purgatory/down_vote.php", function(html){ mydiv.textContent=html;},"&id="+id);
   }

function sendblack(id)
   {
	var myelemname = "black"+id;
	var mydiv = document.getElementById(myelemname);
	ajax.post("http://cartoonbank.ru/wp-content/plugins/purgatory/black_vote.php", function(html){ mydiv.textContent=html;},"&id="+id);
   }

function sendcomment()
   {
	var myelemname = "comment";
	var mydiv = document.getElementById(myelemname);
	ajax.post("http://cartoonbank.ru/wp-content/plugins/purgatory/add_comment.php", function(html){ mydiv.textContent=html;},"");
   }


</script>

<style type="text/css">
	#main
	{
	height:140px; border:1px dashed #29ABE2;margin-bottom:7px;
	width:500px;
	}
	a
	{
	color:#DF3D82;
	text-decoration:none;

	}
	a:hover
	{
	color:#DF3D82;
	text-decoration:underline;

	}
	.up
	{
	padding-top: 8px;
	height:30px; font-size:24px; text-align:center; background-color:#009900; margin-bottom:2px;
	-moz-border-radius: 6px;-webkit-border-radius: 6px;
	}
	.up a
	{
	color:#FFFFFF;
	text-decoration:none;

	}
	.up a:hover
	{
	color:#FFFFFF;
	text-decoration:none;

	}
	.down
	{
	padding-top: 8px;
	height:30px; font-size:24px; text-align:center; background-color:#cc0000; margin-top:2px;
	-moz-border-radius: 6px;-webkit-border-radius: 6px;
	}

	.down a
	{
	color:#FFFFFF;
	text-decoration:none;
	}
	.down a:hover
	{
	color:#FFFFFF;
	text-decoration:none;
	}

	.black
	{  
	padding-top: 8px; color: white;
	height:30px; font-size:24px; text-align:center; background-color:#4A4A4A; margin-top:2px;
	-moz-border-radius: 6px;-webkit-border-radius: 6px;
	}

	.black a
	{
	color:#FFFFFF;
	text-decoration:none;
	}
	.black a:hover
	{
	color:#FFFFFF;
	text-decoration:none;
	}

	.gr
	{
	color:#838383;
	text-decoration:none;
	}

	.box1
	{
	font-family:'Georgia', Times New Roman, Times, serif;
	float:left; height:140px; width:50px;
	}

	.box2
	{
	float:left; width:150px; text-align:left;
	margin-left:10px;height:140px;margin-top:0px;
	font-weight:bold;

	.box3
	{
	float:left; width:200px; text-align:left;
	margin-left:10px;height:140px;margin-top:0px;
	font-weight:bold;


	.descr
	{
	clear:all; float:left; width:120px; text-align:left;
	height:100px;


	.img
	{
	float:left; width:200px; text-align:left;
	margin-left:10px;height:140px;vertical-align: top;
	}

	img
	{
	border:none;
	}
	</style>

<div style="font-size:1em;">

<?php

if (isset($_GET['brand'])&&is_numeric($_GET['brand']))
{
	$brand=$_GET['brand'];
	$sql_brand = " AND B.id = $brand ";
}
else
{
	$brand=0;
	$sql_brand = "";
}

			$sql=mysql_query("SELECT V.image_id, V.up, V.down, V.black, P.name, P.image, P.description AS Description, 
									P.color, B.name AS Artist, P.approved, C.name as Category 
							FROM al_editors_votes AS V, 
							wp_product_list AS P, 
							wp_product_brands AS B, 
							wp_product_categories AS C,  
							wp_item_category_associations AS A
							WHERE V.image_id=P.id 
							AND P.brand = B.id
							AND P.id = A.product_id
							AND C.id = A.category_id
							AND P.active = '1'
							AND ((P.approved is NULL) OR (P.approved = '') OR (V.black >= '1'))".$sql_brand."
							ORDER BY P.id DESC
							Limit 40");

		while($row=mysql_fetch_array($sql))
		{
		$msg=$row['image_id'];
		$mes_id=$row['image_id'];
		$up=$row['up'];
		$down=$row['down'];
		$black=$row['black'];
		$img=$row['image'];
		$imgpath = "http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/images/".$img; 
		$previewpath = "http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/".$img;
		$imgname=nl2br(stripslashes($row['name']));
		$artist=$row['Artist'];
		$description=nl2br(stripslashes($row['Description']));
		$category=$row['Category'];
		?>

	<div id="main">
		<div class="box1">
			<div class='up'><a href="" id="up<?php echo $mes_id;?>"  onclick="sendup(<?php echo $mes_id; ?>);return false;" class="vote" id="<?php echo $mes_id; ?>" name="up"><?php echo $up; ?></a></div>
			<div class='down'><a href="" id="down<?php echo $mes_id;?>" onclick="senddown(<?php echo $mes_id; ?>);return false;" class="vote" id="<?php echo $mes_id; ?>" name="down"><?php echo $down; ?></a></div>
			<div class='black'><a href="" id="black<?php echo $mes_id;?>" onclick="sendblack(<?php echo $mes_id; ?>);return false;" class="vote" id="<?php echo $mes_id; ?>" name="black"><?php echo $black; ?></a></div>
		</div>

		<div class='box2' >
			<?php //echo $row['image_id']; ?>
			<div class="img">
				<a href="<? echo ($previewpath); ?>" target=_blank><img src="<? echo ($imgpath); ?>"></a>
			</div>
		</div>

		<div class="box3">
			<span class="gr">Название: </span><? echo ($imgname);?><br>
			<span class="gr">Категория: </span><? echo ($category);?><br>
			<span class="gr">Автор: </span><? echo ($artist);?><br>
			<span class="gr">Описание: </span><? echo ($description);?> 	
			<form method="post" action="http://cartoonbank.ru/wp-admin/admin.php?page=wp-shopping-cart/display-items.php"> <input type="hidden" name="edid" value="<? echo ($mes_id);?>"> <input class="borders" type="submit" value="<? echo ($mes_id);?>"> </form>
		</div>


	</div>


	<?php }?>
</div>


<div id="divToUpdate" style="width:800px;height:200px;font-size:0.6em;background-color:pink;">...</div>
		<div id="commentsform">
		.<br>

			<form action="http://cartoonbank.ru/wp-content/plugins/purgatory/add_comment.php" method="post" id="commentform">
			<p>текст:<br>
			<textarea name="comment" id="comment" cols="90" rows="3" tabindex="4"></textarea>
			</p>
		  
			<p>
			<input name="submit" type="submit" value="послать">
			<input type="hidden" name="cartoon_id" value="1000">
			<input type="hidden" name="author_id" value="<? echo ($current_user->id); ?>">
			
			</p>
			<input type="hidden" id="_wp_unfiltered_html_comment" name="_wp_unfiltered_html_comment" value="5a3ab88268"><p style="display: none;"><input type="hidden" id="akismet_comment_nonce" name="akismet_comment_nonce" value="8bd460432a"></p>
			
			</form>
		</div>



</div>


<script type="text/javascript">

	jQuery(document).ready(function($){ 
		// bind form using ajaxForm 
		$('#commentform').ajaxForm({ 
			// target identifies the element(s) to update with the server response 
			 target: '#divToUpdate', 
	 
			// success identifies the function to invoke when the server response 
			// has been received; here we apply a fade-in effect to the new content 
			success: function() { 
				$('#divToUpdate').fadeIn('slow'); 
			}
			//success: successResponse
		}); 
	});

	function successResponse(responseJson)
	{
		alert (responseJson);
		var data = jQuery.parseJSON(responseJson);
		alert(data);
		alert(data[1][1]);
		
		$('#divToUpdate').fadeIn('slow');
	}

</script>

