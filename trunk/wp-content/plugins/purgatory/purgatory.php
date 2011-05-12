<?
if (isset($current_user->wp_capabilities['author']) && $current_user->wp_capabilities['author']==1)
{
echo ("<h3>Извините, у вас нет права доступа к этой странице</h3>");
exit;
}
?>

<?
include("config.php");

	$sql=mysql_query("SELECT id, name FROM wp_product_brands where active = 1 order by name");
	?>
	
	<b>Минимальный балл</b> для прохождения в коллекцию - <b><?echo $limit_plus;?></b> плюса, кандидат в «Рабочий стол» - <b><?echo $limit_minus;?></b> минуса. Чёрная метка блокирует появление картинки в хранилище до выяснения обстоятельств.

	<br /><b>Фильтр по авторам</b>: <?

	while($row=mysql_fetch_array($sql))
	{
	$id=$row['id'];
	$name=$row['name'];
	?>
	<a href="http://cartoonbank.ru/wp-admin/admin.php?page=purgatory/purgatory.php&amp;brand=<?echo $id;?>"><?echo $name;?></a>; 
	<?
	}
?>
<br /><br />
<b><a href="http://cartoonbank.ru/?page_id=1148" target="_blank">Меморандум редактора</a></b> <-подробнее <br />
Коллеги, мы не <i>оцениваем</i> тут работы. Мы просто <i>отсекаем</i> мусор. Не пускаем а) антиконституционное б) антиинтеллектуальное.

<?
$result = mysql_query("select C.comment_id, C.comment_content, C.comment_date, U.display_name as author from wp_comments as C, wp_users as U where U.id = C.comment_author order by C.comment_date DESC LIMIT 50");
	$comments_output = "";
	while($r = mysql_fetch_array($result)) {
		$_date = $r['comment_date'];
		$_comment = nl2br(stripslashes($r['comment_content']));
		$_author = $r['author'];
		$_id = $r['comment_id'];
		$comments_output .= "<div style='margin-top:4px;'><span class='gr' title='".$_date."'>".$_author.":&nbsp; </span><span class='c_body'>".$_comment."</span> [<a title='стереть комментарий' href='http://cartoonbank.ru/wp-content/plugins/purgatory/delete_comment.php?id=".$_id."'>x</a>]</div>";
	}
?>

<script type="text/javascript" src="http://cartoonbank.ru/wp-includes/js/jquery/jquery.js"></script>
<script type="text/javascript" src="http://cartoonbank.ru/wp-includes/js/jquery/jquery.form.js"></script>
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

	if (name=='black')
	{
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

	var options = { 
		target:     '#divToUpdate', 
		url:        'http://cartoonbank.ru/wp-content/plugins/purgatory/add_comment.php', 
		success:    function() { 
			alert('Thanks for your comment!'); 
		} 
	};

	$('#commentform').ajaxForm(function() { 
      alert("Thank you for your comment!");
	  })

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

function sendblack_remove(id)
   {
	ajax.post("http://cartoonbank.ru/wp-content/plugins/purgatory/black_vote_remove.php?id="+id);
   }

function sendcomment()
   {
	var myelemname = "comment";
	var mydiv = document.getElementById(myelemname);
	ajax.post("http://cartoonbank.ru/wp-content/plugins/purgatory/add_comment.php", function(html){ mydiv.textContent=html;},"");
	mydiv.value = "";
   }

function deletecomment()
   {
	var myelemname = "comment";
	var mydiv = document.getElementById(myelemname);
	ajax.post("http://cartoonbank.ru/wp-content/plugins/purgatory/add_comment.php", function(html){ mydiv.textContent=html;},"");
	mydiv.value = "";
   }

</script>

<style type="text/css">
	#main
	{
	height:140px; border:1px dashed #29ABE2;margin-bottom:7px;
	width:400px;
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
	padding-top: 6px;
	height:25px; font-size:24px; text-align:center; background-color:#009900; margin-bottom:2px;
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
	padding-top: 6px;
	height:25px; font-size:24px; text-align:center; background-color:#cc0000; margin-top:2px;
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
	padding-top: 6px; color: white;
	height:25px; font-size:24px; text-align:center; background-color:#4A4A4A; margin-top:2px;
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

	.xblack
	{  
	padding-top: 6px;
	height:25px; font-size:24px; text-align:center; background-color:#66CCFF; margin-top:2px;
	-moz-border-radius: 6px;-webkit-border-radius: 6px;
	}

	.xblack a
	{
	color:#FFFFFF;
	text-decoration:none;
	}

	.xblack a:hover
	{
	color:#FFFFFF;
	text-decoration:none;
	}



	.gr
	{
	color:#838383;
	text-decoration:none;
	padding-top:0px;
	line-height:90%;
	}

	.box1
	{
	font-family:'Georgia', Times New Roman, Times, serif;
	float:left; height:140px; width:40px;
	}

	.box2
	{
	float:left; width:150px; text-align:left;
	margin-left:0px;height:140px;margin-top:0px;
	}

	.box3
	{
	float:left; width:200px; text-align:left;
	margin-left:10px;height:140px;margin-top:0px;
	font-size:0.8em;	
	line-height:150%;
	}

	.box4
	{
	float:left; width:200px; text-align:left;
	margin-left:10px;height:140px;margin-top:0px;
	background-color:#E2FFB7;
	}

	.box5
	{
	float:left; width:550px; text-align:left;
	margin-left:6px;height:800px;margin-top:0px;
	padding:2px;
	}

	.descr
	{
	clear:all; float:left; width:120px; text-align:left;
	height:100px;
	}

	.img
	{
	float:left; width:200px; text-align:left;
	margin-left:10px;height:140px;vertical-align: top;
	}

	.c_body
	{
		color:#990099;
	}

	img
	{
	border:none;
	}
	</style>


<table><tr><td  valign="top">&nbsp;

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

			$sql=mysql_query("SELECT V.image_id, V.up, V.down, V.black, P.name, P.image, P.description AS Description, P.color, B.name AS Artist, P.approved, C.name AS Category, U.user_email AS email
					FROM al_editors_votes AS V, wp_product_list AS P, wp_product_brands AS B, wp_product_categories AS C, wp_item_category_associations AS A, wp_users as U
					WHERE V.image_id = P.id
					AND P.brand = B.id
					AND P.id = A.product_id
					AND C.id = A.category_id
					AND U.id = B.user_id
					AND C.id != '777'
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
		$email=$row['email'];
		$imgpath = "http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/images/".$img; 
		$previewpath = "http://cartoonbank.ru/wp-content/plugins/wp-shopping-cart/product_images/".$img;
		$imgname=nl2br(stripslashes($row['name']));
		$artist=$row['Artist'];
		$description=nl2br(stripslashes($row['Description']));
		$category=$row['Category'];
		?>

<?
	/*
	$result = mysql_query("select C.comment_content, C.comment_date, U.display_name as author from wp_comments as C, wp_users as U where U.id = C.comment_author order by C.comment_date DESC LIMIT 20");

	$comments_output = "<div style='margin-top:4px;'><span class='gr' title=''></span><span class='c_body'></span></div>";
	while($r = mysql_fetch_array($result)) {
		$_date = $r['comment_date'];
		$_comment = nl2br(stripslashes($r['comment_content']));
		//$_comment = escape(removeCrLf(htmlspecialchars($r['comment_content'])));
		$_author = $r['author'];
		$comments_output .= "<div style='margin-top:4px;'><span class='gr' title='".$_date."'>".$_author.":</span><span class='c_body'>".$_comment."</span></div>";
	}
	*/
?>

	<div id="main">
		<div class="box1">
			<div class='up'><a href="" id="up<?php echo $mes_id;?>"  onclick="sendup(<?php echo $mes_id; ?>);return false;" class="vote" id="<?php echo $mes_id; ?>" name="up"><?php echo $up; ?></a></div>
			<div class='down'><a href="" id="down<?php echo $mes_id;?>" onclick="senddown(<?php echo $mes_id; ?>);return false;" class="vote" id="<?php echo $mes_id; ?>" name="down"><?php echo $down; ?></a></div>
			<div class='black'><a href="" id="black<?php echo $mes_id;?>" onclick="sendblack(<?php echo $mes_id; ?>);return false;" class="vote" id="<?php echo $mes_id; ?>" name="black"><?php echo $black; ?></a>
			</div>
			<? if ($black > 0){?>
			<div class='xblack'><a href="" id="black_remove<?php echo $mes_id;?>" onclick="sendblack_remove(<?php echo $mes_id; ?>);return false;" class="vote" id="<?php echo $mes_id; ?>" name="black_remove" title="убрать чёрную метку"><img src="<? get_option('siteurl'); ?>/img/xbmark.gif"></a></div>
			<?}?><a href="mailto:<? echo $email;?>?subject=По%20поводу%20рисунка №<? echo ($mes_id);?>. %20Картунбанк&bcc=cartoonbank.ru@gmail.com&body=Уважаемый%20<? echo ($artist);?>!%0A%0A<? echo ($previewpath); ?>%0A%0AСпасибо,%0AКартунбанк"><img src="../img/mail.gif"></a>
		</div>

		<div class='box2' >
			<?php //echo $row['image_id']; ?>
			<div class="img">
				<a href="<? echo ($previewpath); ?>" target=_blank><img src="<? echo ($imgpath); ?>"></a>
			</div>
		</div>

		<div class="box3">
			<span class="gr">Название: </span><? echo ($imgname);?><br />
			<span class="gr">Категория: </span><? echo ($category);?><br />
			<span class="gr">Автор: </span><? echo ($artist);?><br />
			<span class="gr">Описание: </span><? echo ($description);?> 	
			<form method="post" action="http://cartoonbank.ru/wp-admin/admin.php?page=wp-shopping-cart/display-items.php"> <input type="hidden" name="edid" value="<? echo ($mes_id);?>"> <input class="borders" type="submit" value="<? echo ($mes_id);?>"> </form>
		</div>

	</div>


	<?php }?>
</div>
</td>
<td valign="top"> &nbsp;
	<div class="box5">
	<b>50 последних комментариев редакторов:</b>

		<div id="commentsform">
		<? //echo("<pre>вы пишете от юзера:".print_r($current_user->last_name,true)."</pre>"); ?>
			<form action="http://cartoonbank.ru/wp-content/plugins/purgatory/add_comment.php" method="post" id="commentform">
			пишите тут, нажмите кнопку:<br />
			
			<textarea id="comment" name="comment" id="comment" cols="60" rows="3" tabindex="4"></textarea>
		  
			<p>
			<input name="submit" type="submit" value="послать">
			<input type="hidden" name="cartoon_id" value="1000">
			<input type="hidden" name="author_id" value="<? echo ($current_user->id); ?>">
			
			</p>
			<input type="hidden" id="_wp_unfiltered_html_comment" name="_wp_unfiltered_html_comment" value="5a3ab88268"><p style="display: none;"><input type="hidden" id="akismet_comment_nonce" name="akismet_comment_nonce" value="8bd460432a"></p>
			</form>
		</div>

		<div id="divToUpdate" style="padding:2px;font-size:0.9em;background-color:#FFFFD7;"><? echo ($comments_output) ?></div>


</div>

	
	</div>
</td>
</tr>
</table>

<div>

<?

//echo("<pre>тестовый вывод, не обращайте внимания:".print_r($comments_output,true)."</pre>"); 

?>
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

	function successResponse(text)
	{
		if (text!='')
		{
			$('#divToUpdate').fadeIn('slow');
		}
	}

</script>