<?
if (isset($current_user->wp_capabilities['author']) && $current_user->wp_capabilities['author']==1)
{
echo ("<h3>Извините, у вас нет права доступа к этой странице</h3>");
exit;
}
require_once("../wp-config.php");
include("config.php");
$_SITEURL = get_option('siteurl');
$Current_ID = $current_user->id;
?>
<br>Рисунок проходит в коллекцию после того как проголосуют <b><?echo $limit;?></b> модераторов. Начальный рейтинг рисунка определяется исходя из оценок модераторов. В дополнение, модераторы имеют право голосовать на самом сайте как обычные посетители. Таким образом, у модераторов есть два голоса для оценки рисунка. 
Чёрная метка блокирует появление картинки в хранилище до выяснения обстоятельств. Просим сразу оставлять комментарий о причине блокировки.

<b><a href="<?= $_SITEURL; ?>/?page_id=1148" target="_blank">Меморандум редактора</a></b><br />

<?
$result = mysql_query("select C.comment_id, C.comment_content, C.comment_date, U.display_name as author from wp_comments as C, wp_users as U where U.id = C.comment_author order by C.comment_date DESC LIMIT 50");
    $comments_output = "";
    while($r = mysql_fetch_array($result)) {
        $_date = $r['comment_date'];
        $_comment = nl2br(stripslashes($r['comment_content']));
        $_author = $r['author'];
        $_id = $r['comment_id'];
        $comments_output .= "<div style='margin-top:4px;'><span class='gr' title='".$_date."'>".$_author.":&nbsp; </span><span class='c_body'>".$_comment."</span> [<a title='стереть комментарий' href='#' onclick='deletecomment(".$_id.");'>x</a>]</div>";
    }
?>

<script type="text/javascript" src="<?= $_SITEURL; ?>/wp-includes/js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?= $_SITEURL; ?>/wp-includes/js/jquery/jquery.form.js"></script>
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
       url: "<?= $_SITEURL; ?>/wp-content/plugins/purgatory/up_vote.php",
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
       url: "<?= $_SITEURL; ?>/wp-content/plugins/purgatory/black_vote.php",
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
       url: "<?= $_SITEURL; ?>/wp-content/plugins/purgatory/down_vote.php",
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
        url:        '<?= $_SITEURL; ?>/wp-content/plugins/purgatory/add_comment.php', 
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

function sendrate(id,vote)
   {
    var myelemname = "moder_rating"+id;
    var mydiv = document.getElementById(myelemname);
    var param = "&id="+id+"&vote="+vote+"&ip="+<?= $Current_ID;?>+"";
    ajax.post("<?= $_SITEURL; ?>/wp-content/plugins/purgatory/moder_vote.php", function(html){ mydiv.innerHTML=html;jQuery('#radioset'+id).addClass('radioSetOff');},param);
   }

function sendblack(id)
   {
    var myelemname = "black"+id;
    var mydiv = document.getElementById(myelemname);
    ajax.post("<?= $_SITEURL; ?>/wp-content/plugins/purgatory/black_vote.php", function(html){ mydiv.textContent=html;},"&id="+id);
   }

function sendblack_remove(id)
   {
    ajax.post("<?= $_SITEURL; ?>/wp-content/plugins/purgatory/black_vote_remove.php?id="+id);
   }

function deletecomment(id)
   {
	var myelemname = "divToUpdate";
	var mydiv = document.getElementById(myelemname);
	ajax.post(
		"http://cartoonbank.ru/wp-content/plugins/purgatory/delete_comment.php?id="+id, 
		function(reply){ jQuery('#divToUpdate').html(reply);});
   }
   //jQuery('#divToUpdate').html('3<b>3</b>3')
   //mydiv.innerHTML=reply;
   // mydiv.textContent=reply; yes
</script>

<style type="text/css">
    #main
    {
    height:240px; border:1px dashed #29ABE2;margin-bottom:7px;
    width:400px;
    padding:2px;
    padding-top:5px;
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
    .moder_rating{
        height:40px; 
        font-size:14px; 
        padding:2px;
        text-align:center; 
        background-color:#CCFF99; 
        margin-bottom:2px;
    }
    .radiobg{
        height:18px; 
        font-size:10px; 
        padding:2px;
        text-align:center; 
        background-color:#99FFCC; 
        margin-bottom:2px;
		cursor: pointer;
    }
    .radiobg:hover{
        background-color:#00FF99; 
    }
    .radioSetOn{
    }
    .radioSetOff{
         display:none; 
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
    padding-top: 6px; color: white;cursor: pointer;
    height:25px; font-size:24px; text-align:center; background-color:#4A4A4A; margin-top:2px;
    -moz-border-radius: 6px;-webkit-border-radius: 6px;
    }
    .black:hover
    {  
    background-color:#330000;
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



    .gr, .gr_raznoe, .gr_caricatura, .gr_cartoon, .gr_artoon, .gr_sharzh
    {
    color:#838383;
    text-decoration:none;
    padding:2px;
    margin-left:2px;
    line-height:90%;
    }

    .gr_raznoe
    {
    color:#838383;
    background-color:#FCFA9E;
    }

    .gr_caricatura
    {
    color:#000000;
    background-color:#00FFCC;
    }

    .gr_cartoon
    {
    color:#000000;
    background-color:#99FF66;
    }

    .gr_artoon
    {
    color:#99FF66;
    background-color:#00CC33;
    }

    .gr_sharzh
    {
    color:#FFFFFF;
    background-color:#FF9966;
    }

    .box1
    {
    font-family:'Georgia', Times New Roman, Times, serif;
    float:left; width:50px;
	margin-left:4px;
    }

    .box2
    {
    float:left; width:150px; text-align:left;
    margin-left:0px;height:140px;margin-top:0px;margin-right:10px;
    }

    .box3
    {
    text-align:left;
    margin-left:65px;height:140px;
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


$sql ="SELECT 
    V.image_id, V.black, 
    P.name, P.image, P.description AS Description, P.color, P.approved,
    B.name AS Artist,  
    C.name AS Category, 
    U.user_email AS email
FROM 
    al_editors_votes AS V, 
    wp_product_list AS P, 
    wp_product_brands AS B, 
    wp_product_categories AS C, 
    wp_users as U 
WHERE 
    V.image_id = P.id 
    AND P.brand = B.id
    AND V.moderator_votes < ".$limit."
    AND U.id = B.user_id
    AND C.id != '777'
    AND P.active = '1'
    AND C.id = P.category
    AND ((P.approved is NULL) OR (P.approved = '') OR (V.black >= '1'))
ORDER BY P.id DESC 
Limit 40";

//pokazh($sql);

$sql=mysql_query($sql);
        while($row=mysql_fetch_array($sql))
        {
        $msg=$row['image_id'];
        $mes_id=$row['image_id'];
        $black=$row['black'];
        $img=$row['image'];
        $email=$row['email'];
        $imgpath = $_SITEURL."/wp-content/plugins/wp-shopping-cart/images/".$img; 
        $previewpath = $_SITEURL."/wp-content/plugins/wp-shopping-cart/product_images/".$img;
        $imgname=nl2br(stripslashes($row['name']));
        $artist=$row['Artist'];
        $description=nl2br(stripslashes($row['Description']));
        $category=$row['Category'];

        
        $sql_voted = "select ip_add from al_editors_voting_ip where mes_id_fk ='".$msg."' and ip_add='".$Current_ID."'";
        $result = mysql_query($sql_voted);
        $count=mysql_num_rows($result);
        if($count==0)
        {$already_voted = False;}else{$already_voted = True;}



if ($already_voted){$current_visibility_class = "radioSetOff";}else{$current_visibility_class = "radioSetOn";}


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

        $sql_points = "SELECT votes, points FROM `wp_fsr_post` where id=$mes_id";
        
        $result1=mysql_query($sql_points);
        $row_points = mysql_fetch_array($result1);
        if ($row_points==FALSE || $row_points[0] == '0')
            {$avg_votes=0;}
        else
            {$avg_votes=round($row_points[1]/$row_points[0],2);}
    ?>

    <div id="main">
        <div class="box1">
            <div class='5radio' id='5radio<?php echo $mes_id;?>'>

                <div class="moder_rating" id="moder_rating<?php echo $mes_id;?>">
                    <? echo "<b>".$avg_votes."</b>";?></br>
                    <? echo "(".$row_points[1]."/".$row_points[0].")"?>
                </div>

                <div class="<?echo $current_visibility_class;?>" id="radioset<?php echo $mes_id;?>">

                    <div class="radiobg" onclick="sendrate(<?php echo $mes_id;?>,5)"><input type="radio" id="moder_vote_5" name="moder_votes" value="5">
                    <label title="+5">5</label></div> 

                    <div class="radiobg"onclick="sendrate(<?php echo $mes_id;?>,4)"><input type="radio" id="moder_vote_4" name="moder_votes" value="4">
                    <label title="+4">4</label></div> 
                    
                    <div class="radiobg" onclick="sendrate(<?php echo $mes_id;?>,3)"><input type="radio" id="moder_vote_3" name="moder_votes" value="3">
                    <label title="+3">3</label></div> 

                    <div class="radiobg" onclick="sendrate(<?php echo $mes_id;?>,2)"><input type="radio" id="moder_vote_2" name="moder_votes" value="2">
                    <label title="+2">2</label></div> 

                    <div class="radiobg" onclick="sendrate(<?php echo $mes_id;?>,1)"><input type="radio" id="moder_vote_1" name="moder_votes" value="1">
                    <label title="+1">1</label></div> 
                </div>

            </div>
            <!-- <div class='up'><a href="" id="up<?php echo $mes_id;?>"  onclick="sendup(<?php echo $mes_id; ?>);return false;" class="vote" id="<?php echo $mes_id; ?>" name="up"><?php echo $up; ?></a></div>
            <div class='down'><a href="" id="down<?php echo $mes_id;?>" onclick="senddown(<?php echo $mes_id; ?>);return false;" class="vote" id="<?php echo $mes_id; ?>" name="down"><?php echo $down; ?></a></div> -->

            <div class='black'><a href="" id="black<?php echo $mes_id;?>" onclick="sendblack(<?php echo $mes_id; ?>);return false;" class="vote" id="<?php echo $mes_id; ?>" name="black"><?php echo $black; ?></a>
            </div>
            <? if ($black > 0){?>
            <div class='xblack'><a href="" id="black_remove<?php echo $mes_id;?>" onclick="sendblack_remove(<?php echo $mes_id; ?>);return false;" class="vote" id="<?php echo $mes_id; ?>" name="black_remove" title="убрать чёрную метку"><img src="<? echo ($_SITEURL);?>/img/xbmark.gif"></a></div>
            <?}?>
        </div>

        <div class='box2' >
            <?php //echo $row['image_id']; ?>
            <div class="img">
                <a href="<? echo ($previewpath); ?>" target=_blank><img src="<? echo ($imgpath); ?>"></a>
            </div>
        </div>

<?
    if ($category=='Разное')     
        $cat_style = 'gr_raznoe';
    elseif ($category=='Карикатура')
        $cat_style = 'gr_caricatura';
    elseif ($category=='Cartoon')
        $cat_style = 'gr_cartoon';
    elseif ($category=='Artoon')
        $cat_style = 'gr_artoon';
    elseif ($category=='Шарж')
        $cat_style = 'gr_sharzh';
    else
        $cat_style = 'gr';
?>
        <div class="box3">
            <span class="gr">Название: </span><? echo ($imgname);?><br />
            <span class="<? echo $cat_style;?>">Категория: <? echo ($category);?></span><br />
            <span class="gr">Автор: </span><? echo ($artist);?><br />
            <span class="gr">Описание: </span><? echo ($description);?>     
            <form method="post" action="<?= $_SITEURL; ?>/wp-admin/admin.php?page=wp-shopping-cart/display-items.php"> <input type="hidden" name="edid" value="<? echo ($mes_id);?>"> <input class="borders" type="submit" value="<? echo ($mes_id);?>"> </form>
            <a href="mailto:<? echo $email;?>?subject=По%20поводу%20рисунка №<? echo ($mes_id);?>. %20Картунбанк&bcc=cartoonbank.ru@gmail.com&body=Уважаемый%20<? echo ($artist);?>!%0A%0A<? echo ($previewpath); ?>%0A%0AСпасибо,%0AКартунбанк"><img src="../img/mail.gif"></a>
        </div>
    </div>

	<?php }?>
</div>
</td>
<td valign="top"> &nbsp;
    <div class="box5"><br>
    <b>50 последних комментариев редакторов:</b>
        <div id="commentsform">
            <form action="<?= $_SITEURL; ?>/wp-content/plugins/purgatory/add_comment.php" method="post" id="commentform">
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