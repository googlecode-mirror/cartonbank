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
	else
	{
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

		$sql=mysql_query("SELECT V.image_id, V.up, V.down, P.name, P.image, B.name AS Artist, P.approved FROM al_editors_votes AS V, wp_product_list AS P, wp_product_brands AS B 
							WHERE V.image_id=P.id 
							AND P.brand = B.id
							AND P.active = '1'
							AND ((P.approved is NULL) OR (P.approved = ''))".$sql_brand."
							ORDER BY P.id DESC
							Limit 40");

		while($row=mysql_fetch_array($sql))
		{
		$msg=$row['image_id'];
		$mes_id=$row['image_id'];
		$up=$row['up'];
		$down=$row['down'];
		$img=$row['image'];
		$imgpath = "http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/images/".$img; 
		$previewpath = "http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/".$img;
		$imgname=$row['name'];
		$artist=$row['Artist'];
		?>

	<div id="main">
		<div class="box1">
			<div class='up'><a href="" id="up<?php echo $mes_id;?>"  onclick="sendup(<?php echo $mes_id; ?>);return false;" class="vote" id="<?php echo $mes_id; ?>" name="up"><?php echo $up; ?></a></div>
			<div class='down'><a href="" id="down<?php echo $mes_id;?>" onclick="senddown(<?php echo $mes_id; ?>);return false;" class="vote" id="<?php echo $mes_id; ?>" name="down"><?php echo $down; ?></a></div>
		</div>

		<div class='box2' >
			<?php //echo $row['image_id']; ?>
			<div class="img">
				<a href="<? echo ($previewpath); ?>" target=_blank><img src="<? echo ($imgpath); ?>"></a>
			</div>
		</div>

		<div class="box3">
			Название: <? echo ($imgname);?><br>
			Автор: <? echo ($artist);?><br>
			Номер: <? echo ($mes_id);?>
		</div>


	</div>


	<?php }?>

</div>