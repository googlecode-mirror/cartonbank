<?
if (isset($current_user->wp_capabilities['author']) && $current_user->wp_capabilities['author']==1)
{
echo ("<h3>Извините, у вас нет права доступа к этой странице</h3>");
exit;
}
?>

<h3>Прихожая</h3>


<?
include("config.php");

	$sql=mysql_query("SELECT id, name FROM wp_product_brands where active = 1 order by name");
	?>
	
	<b>Минимальный балл</b> для прохождения в коллекцию - <b><?echo $limit_plus;?></b> плюса, кандидат в «Рабочий стол» - <b><?echo $limit_minus;?></b> минуса.

	<br><b>Фильтр по авторам</b>: <?

	while($row=mysql_fetch_array($sql))
	{
	$id=$row['id'];
	$name=$row['name'];
	?>
	<a href="http://cartoonbank.ru/wp-admin/admin.php?page=purgatory/purgatory.php&brand=<?echo $id;?>"><?echo $name;?></a>; 
	<?
	}
?><br><br>



<script type="text/javascript" src="http://cartoonbank.ru/wp-content/plugins/purgatory/jquery.js"></script>
<script type="text/javascript">
	$(function() {
	$(".vote").click(function() 
	{
	var id = $(this).attr("id");
	var name = $(this).attr("name");
	var dataString = 'id='+ id ;
	var parent = $(this);


	if(name=='up')
	{
	alert ('up');
	$(this).fadeIn(200).html('<img src="dot.gif" align="absmiddle">');
	$.ajax({
	   type: "POST",
	   url: "http://cartoonbank.ru/wp-content/plugins/purgatory/up_vote.php",
	   data: dataString,
	   cache: false,

	   success: function(html)
	   {
		parent.html(html);
	  
	  }  });
	  
	}

	if (name=='black')
	{
	alert ('black');

	$(this).fadeIn(200).html('<img src="dot.gif" align="absmiddle">');
	$.ajax({
	   type: "POST",
	   url: "http://cartoonbank.ru/wp-content/plugins/purgatory/black_vote.php",
	   data: dataString,
	   cache: false,
	   success: function(html)
	   {
		parent.html(html);	  
	   }  });
	  
	}

	if(name=='down')
	{
	alert ('down');
	$(this).fadeIn(200).html('<img src="dot.gif" align="absmiddle">');
	$.ajax({
	   type: "POST",
	   url: "http://cartoonbank.ru/wp-content/plugins/purgatory/down_vote.php",
	   data: dataString,
	   cache: false,
	   success: function(html)
	   {
		   parent.html(html);
	  }
	   
	 });

	}
	  

	return false;
		});

	});
</script>
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

<div>

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

/*
		$sql=mysql_query("SELECT V.image_id, V.up, V.down, V.black, P.name, P.image, B.name AS Artist, P.approved FROM al_editors_votes AS V, wp_product_list AS P, wp_product_brands AS B 
							WHERE V.image_id=P.id 
							AND P.brand = B.id
							AND P.active = '1'
							AND ((P.approved is NULL) OR (P.approved = '') OR (V.black >= '1'))".$sql_brand."
							ORDER BY P.id DESC
							Limit 40");
*/
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
