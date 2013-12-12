<?
if (isset($current_user->wp_capabilities['author']) && $current_user->wp_capabilities['author']==1 || $current_user->tema_dnya_access == '0')
{
echo ("<h3>Извините, у вас нет права доступа к этой странице</h3>");
exit;
}



// settings
	//
		$grace_days = 7; //how many days the theme of the day picture is active
		$thedate = date("d.m.y"); 	// current date
		$thedate_day = date("d"); 	// current day number
			//pokazh ($thedate,"thedate: ");
			$sqlthedate = date("Y.m.d"); 	// current date
			//pokazh ($sqlthedate,"sqlthedate: ");
		$expirationdate  = date("y.m.d", mktime(0, 0, 0, date("m"), date("d")-$grace_days, date("Y")));
			//pokazh ($expirationdate,"expirationdate: ");
		$tomorrow = date("Y.m.d", mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
		$tomorrowh = date("d.m.y", mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
		$tomorrow_day = date("d", mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));

	//
//


// Approve image to tema dnya
if (isset($_POST['tema_dnya_approved']) && is_numeric($_POST['image_id']))
{
	// add id to the theme of the day
	$_product_id = trim($_POST['image_id']);

	// visibility trigger
	if (trim($_POST['tema_dnya_approved']) == 1)
	{
		$_is_approved = 0; // uncheck if true
	}
	else
	{
		$_is_approved = 1; // check if false
	}

	$sql = "update wp_product_list set tema_dnya_approved = ".$_is_approved." where id = ". $_product_id;
	$wpdb->query($sql);
		//pokazh ($sql,"sql: ");
}



// Add image to tema dnya
if (isset($_POST['addid']) && is_numeric($_POST['addid']))
{
	// add id to the theme of the day
	$_product_id = trim($_POST['addid']);
	
	// check if exists
	//$sql = "select category_id from wp_item_category_associations where product_id ='".$_product_id."'";
	//$_category_id = $wpdb->get_results($sql);

		$sql = "insert into wp_item_category_associations (product_id,category_id) values ('".$_product_id."','777')";
		$wpdb->query($sql);
		//pokazh ($sql,"sql: ");
}

if (isset($_POST['temadnyaid']) && isset($_POST['temadnyadate']))
{
	if (isset($_POST['comment']) && $_POST['comment']!='')
	{
		$comment = $_POST['comment'];
		if (isset($_POST['comment_url']) && $_POST['comment_url']!='')
		{
			$comment_short = substr($comment,0,33).'...'; 
			$comment = addslashes("<a title='".$comment."' href='".trim($_POST['comment_url'])."'>".$comment_short."</a>");
		}
	}
	else
	{
		$comment = "...";
	}

	$sql = "delete from `tema_dnya` where datetime = '".$_POST['temadnyadate']."'";
	$wpdb->query($sql);
	$sql = "insert into `tema_dnya` (id, datetime, comment) values('".trim($_POST['temadnyaid'])."','".$_POST['temadnyadate']."','".$comment."')";
	$wpdb->query($sql);
	//pokazh ($sql,"sql");
}

// delete from tema dnya

if (isset($_POST['deleteid']) && is_numeric($_POST['deleteid']))
{
	$sql = "delete from wp_item_category_associations where category_id='777' and product_id='".$_POST['deleteid']."'";
	$wpdb->query($sql);
	//pokazh ($sql,"sql: ");
}

?>



<style type="text/css" media="all">
	div.item{
		width:142px;
		float:left;
		padding:4px;
		text-align: center;
	}
	img.thumb{
		border: 3px solid #b2b2b2;
		margin: 4px;
		text-align: center;
	}
	img.thumb1{
		border: 3px solid #FF00FF;
		margin: 4px;
		text-align: center;
	}
</style>


<?

//check if there is a cartoon of the day
	$sql = "Select id, comment from tema_dnya where datetime = '".$sqlthedate."'";
	//pokazh ($sql,"sql: ");
	$cartoon_of_the_day = $wpdb->get_results($sql);

if ($cartoon_of_the_day!= null)
{
	$cartoon_of_the_day_id = $cartoon_of_the_day[0]->id;
	$cartoon_of_the_day_comment = $cartoon_of_the_day[0]->comment;
}
else
{
	$cartoon_of_the_day_id = 0;
	$cartoon_of_the_day_comment = '';
}

//find cartoon of tomorrow
	$sql = "Select id, comment from tema_dnya where datetime = '".$tomorrow."'";
	//pokazh ($sql,"sql: ");
	$cartoon_of_tomorrow = $wpdb->get_results($sql);

if ($cartoon_of_tomorrow!= null)
{
	$cartoon_of_tomorrow_id = $cartoon_of_tomorrow[0]->id;
	$cartoon_of_tomorrow_comment = $cartoon_of_tomorrow[0]->comment;
}
else
{
	$cartoon_of_tomorrow_id = 0;
	$cartoon_of_tomorrow_comment = '';
}


// get all images for the theme of the date
//$sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1'  AND `wp_item_category_associations`.`category_id` != '666'  AND `wp_product_list`.`approved` = '1'  AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` AND `wp_item_category_associations`.`category_id`='777'  AND `wp_product_categories`.`id`=777  ORDER BY `wp_product_list`.`id` DESC LIMIT 0,20";
$sql = "SELECT `wp_product_list`.*, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1'  AND `wp_item_category_associations`.`category_id` != '666'  AND `wp_product_list`.`approved` = '1'  AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` AND `wp_item_category_associations`.`category_id`='777'  AND `wp_product_categories`.`id`=777  ORDER BY `wp_product_list`.`id` DESC LIMIT 0,20";

//pokazh ($sql,"sql: ");

$product_list = $GLOBALS['wpdb']->get_results($sql,ARRAY_A);


  echo "<div style='width=700px; text-align:left;'>";

  echo "<div><h2>Картинки на темы дня</h2></div>";
  
	// wdd image by id
	echo "<div style='background-color:#FFFF99; padding:4px;font-size:0.8em;'>Добавить картинку по номеру в темы дня. Чтобы добавить подпись к картинке дня надо заполнить форму под картинкой дня и нажать розовую кнопку «тема выбрана». Подпись не обязательна.</div>";
	echo "<div><form method=post action=''><input type='text' name='addid'><input type='submit' value='добавить в темы дня'></form></div>";

if($product_list != null)
{
/*
	$strlen_total = strlen(utf8_decode($cartoon_of_tomorrow_comment));
	$strlen_comment = strlen(utf8_decode(strip_tags($cartoon_of_tomorrow_comment)));
	$strlen_beforeurl = 9;
	$strlen_afterurl = 20;
	$cut=15;

	$cartoon_of_the_day_comment_text = htmlspecialchars(stripslashes(strip_tags($cartoon_of_tomorrow_comment)));
	//$cartoon_of_the_day_comment_url = htmlspecialchars(substr(utf8_decode($cartoon_of_tomorrow_comment),$strlen_beforeurl,$strlen_total-$strlen_comment-$strlen_beforeurl-$strlen_afterurl));
	$cartoon_of_the_day_comment_url = htmlspecialchars(substr(utf8_decode($cartoon_of_tomorrow_comment),$strlen_beforeurl,$strlen_total-$strlen_comment-$cut));

pokazh($cartoon_of_tomorrow_comment);
pokazh(utf8_decode($cartoon_of_tomorrow_comment),"utf8_decode");
pokazh(strip_tags($cartoon_of_tomorrow_comment),"strip_tags");
pokazh($strlen_total,"strlen_total");
pokazh($strlen_comment,"strlen_comment");
pokazh($cartoon_of_the_day_comment_text);
pokazh($cartoon_of_the_day_comment_url);

*/
	$strlen_total = strlen(utf8_decode($cartoon_of_the_day_comment));
	$strlen_comment = strlen(utf8_decode(strip_tags($cartoon_of_the_day_comment)));
	$strlen_beforeurl = 9;
	$cut=15;

	$cartoon_of_the_day_comment_text = htmlspecialchars(stripslashes(strip_tags($cartoon_of_the_day_comment)));
	$cartoon_of_the_day_comment_url = htmlspecialchars(substr(utf8_decode($cartoon_of_the_day_comment),$strlen_beforeurl,$strlen_total-$strlen_comment-$cut));
	
	//htmlspecialchars(substr($cartoon_of_the_day_comment,$strlen_beforeurl,$strlen_total-$strlen_comment-$strlen_beforeurl-$strlen_afterurl));

	
  echo "<div><h3 style='color:#FF33CC;background-color:#FFFF99; padding:4px;'>Сегодняшняя тема дня (".$thedate.")</h3></div>";

  if (is_odd($thedate_day))
	  echo "<div style='background-color:#CCFF00;padding:2px;padding-left:6px;width:200px;'>День Эха Петербурга</div>";


  // pokazh ($product_list);
  // pokazh ($_POST);

$is_approved = false;

  foreach($product_list as $product)
  {
		if ($product['tema_dnya_approved'] == '1')
		{
			$is_approved = true;
		}
		else
		{
			$is_approved = false;
		}
		//<input type='submit' value='сохранить подпись' style='background-color:#CC99CC;color:white;'>
	  if ($cartoon_of_the_day_id == $product['id'])
	  {
		echo "<div class='item' style='text-align:center; background-color:#FFCCFF; width:160px; margin:2px;padding:4px; border: 1px solid #b2b2b2;'><a href='http://sl.cartoonbank.ru/".$product['image']."' target='_blank'><img src='http://th.cartoonbank.ru/".$product['image']."' width='140' height='140' class='thumb1'  title='".$product['id']."'></a>";
	  echo "<form method=post action=''>

		<div style='text-align:center;font-size:0.8em;'>Подпись:<br>
		<input type='text' name='comment' id='comment_today'  value='".$cartoon_of_the_day_comment_text."' style='width:144px;'>  <a href='".$cartoon_of_the_day_comment_url."'>URL</a>: 
		<textarea rows='4' name='comment_url' id='comment_url' style='width:144px;'>" .$cartoon_of_the_day_comment_url. "</textarea></div>

		<input type='hidden' name='temadnyaid' value='".$product['id']."'>
		<input type='hidden' name='temadnyadate' value='".$sqlthedate."'>
		<input type='submit' value='тема выбрана!' style='background-color:#FF00FF;color:white;'></form>
		<input type='hidden' name='deleteid' value='".$product['id']."'>
		<input type='image' src='../img/trash.gif' title='уже не убрать'>";

		echo "<form method=post action=''>";
			echo "<input type='hidden' name='tema_dnya_approved' value='".$is_approved."'>";
			echo "<input type='hidden' name='image_id' value='".$product['id']."'>";
			if ($is_approved){echo "<input type='image' src='../img/checked.gif' title='видно'>";}else{echo "<input type='image' src='../img/unchecked.gif' title='не видно до утверждения редактором раздела'>";}
		echo "</form>";
	  }
	  else
	  {
		echo "<div class='item' style='width:160px; background-color:#E6E6E6; margin:2px;padding:4px; border: 1px solid #b2b2b2;'><a href='http://sl.cartoonbank.ru/".$product['image']."' target='_blank'><img src='http://th.cartoonbank.ru/".$product['image']."' width='140' height='140' class='thumb' title='".$product['id']."'></a>";
	  echo "<form method=post action=''>
		<input type='hidden' name='temadnyaid' value='".$product['id']."'>
		<input type='hidden' name='temadnyadate' value='".$sqlthedate."'>
		<input type='submit' value='это тема!'></form>
		<form method=post action=''>
		<input type='hidden' name='deleteid' value='".$product['id']."'>
		<input type='image' src='../img/trash.gif' alt='убрать' title='убрать'></form>";

		echo "<form method=post action=''>";
			echo "<input type='hidden' name='tema_dnya_approved' value='".$is_approved."'>";
			echo "<input type='hidden' name='image_id' value='".$product['id']."'>";
			if ($is_approved){echo "<input type='image' src='../img/checked.gif' title='видно'>";}else{echo "<input type='image' src='../img/unchecked.gif' title='не видно до утверждения редактором раздела'>";}
		echo "</form>";
	  }
	  echo "</div>";

	  $is_approved = false;
  }
  echo "</div><!-- items -->";
  

echo "<div style='clear:both;'></div>";

  echo "<div><h3 style='color:#FF33CC;background-color:#FFFF99; padding:4px;'>Завтрашняя тема дня (".$tomorrowh.")</h3></div>";


	$strlen_total = strlen(utf8_decode($cartoon_of_tomorrow_comment));
	$strlen_comment = strlen(utf8_decode(strip_tags($cartoon_of_tomorrow_comment)));
	$strlen_beforeurl = 9;
	$strlen_afterurl = 20;
	$cut=15;

	$cartoon_of_the_day_comment_text = htmlspecialchars(stripslashes(strip_tags($cartoon_of_tomorrow_comment)));
	//$cartoon_of_the_day_comment_url = htmlspecialchars(substr(utf8_decode($cartoon_of_tomorrow_comment),$strlen_beforeurl,$strlen_total-$strlen_comment-$strlen_beforeurl-$strlen_afterurl));
	$cartoon_of_the_day_comment_url = htmlspecialchars(substr(utf8_decode($cartoon_of_tomorrow_comment),$strlen_beforeurl,$strlen_total-$strlen_comment-$cut));
/*
pokazh($cartoon_of_tomorrow_comment);
pokazh(utf8_decode($cartoon_of_tomorrow_comment),"utf8_decode");
pokazh(strip_tags($cartoon_of_tomorrow_comment),"strip_tags");
pokazh($strlen_total,"strlen_total");
pokazh($strlen_comment,"strlen_comment");
pokazh($cartoon_of_the_day_comment_text);
pokazh($cartoon_of_the_day_comment_url);
*/
  if (is_odd($tomorrow_day))
	  echo "<div style='background-color:#CCFF00;padding:2px;padding-left:6px;width:200px;'>День Эха Петербурга</div>";

$is_approved = false;


  foreach($product_list as $product)
  {
		if ($product['tema_dnya_approved'] == '1')
		{
			$is_approved = true;
		}
		else
		{
			$is_approved = false;
		}

	  if ($cartoon_of_tomorrow_id == $product['id'])
	  {
		echo "<div class='item' style='text-align:center; background-color:#FFCCFF; width:160px; margin:2px;padding:4px; border: 1px solid #b2b2b2;'><a href='http://sl.cartoonbank.ru/".$product['image']."' target='_blank'><img src='http://th.cartoonbank.ru/".$product['image']."' width='140' height='140' class='thumb1' title='".$product['id']."'></a>";
	  echo "<form method=post action=''>

		<div style='text-align:center;font-size:0.8em;'>Подпись:<br>
		<input type='text' name='comment' id='comment_today'  value='".$cartoon_of_the_day_comment_text."' style='width:144px;'> <a href='".$cartoon_of_the_day_comment_url."'>URL</a>: 
		<textarea rows='4' name='comment_url' id='comment_url' style='width:144px;'>" .$cartoon_of_the_day_comment_url. "</textarea></div>

		<input type='hidden' name='temadnyaid' value='".$product['id']."'>
		<input type='hidden' name='temadnyadate' value='".$tomorrow."'>
		<input type='submit' value='тема выбрана!' style='background-color:#FF00FF;color:white;'></form>
		<input type='hidden' name='deleteid' value='".$product['id']."'>
		<input type='image' src='../img/trash.gif' title='уже не убрать'>";

		echo "<form method=post action=''>";
			echo "<input type='hidden' name='tema_dnya_approved' value='".$is_approved."'>";
			echo "<input type='hidden' name='image_id' value='".$product['id']."'>";
			if ($is_approved){echo "<input type='image' src='../img/checked.gif' title='видно'>";}else{echo "<input type='image' src='../img/unchecked.gif' title='не видно до утверждения редактором раздела'>";}
		echo "</form>";
	  }
	  else
	  {
		echo "<div class='item' style='width:160px; background-color:#E6E6E6; margin:2px;padding:4px; border: 1px solid #b2b2b2;'><a href='http://sl.cartoonbank.ru/".$product['image']."' target='_blank'><img src='http://th.cartoonbank.ru/".$product['image']."' width='140' height='140' class='thumb'  title='".$product['id']."'></a>";
		echo "<form method=post action=''>

		<input type='hidden' name='temadnyaid' value='".$product['id']."'>
		<input type='hidden' name='temadnyadate' value='".$tomorrow."'>
		<input type='submit' value='это тема!'></form>
		<form method=post action=''>
		<input type='hidden' name='deleteid' value='".$product['id']."'>
		<input type='image' src='../img/trash.gif' alt='убрать' title='убрать'></form>";

		echo "<form method=post action=''>";
			echo "<input type='hidden' name='tema_dnya_approved' value='".$is_approved."'>";
			echo "<input type='hidden' name='image_id' value='".$product['id']."'>";
			if ($is_approved=='1'){echo "<input type='image' src='../img/checked.gif' title='видно'>";}else{echo "<input type='image' src='../img/unchecked.gif' title='не видно до утверждения редактором раздела'>";}
		echo "</form>";
				//pokazh ($product['tema_dnya_approved'],"approved:");
				//pokazh ($is_approved);
	  }
	  echo "</div>";
	  
  }
  echo "</div><!-- items -->"; 

	echo "<div style='clear:both;'></div>";

?>
<div id="calend" style="color:#FF33CC;background-color:#FFFF99; padding:4px;">
	<h3>Календари</h3>
	<a href=http://www.calend.ru/holidays/russtate/ target=_blank><img src="http://www.calend.ru/img/export/informer_1.png" width="150" alt="Праздники России" border="0"></a>
	<a href=http://www.calend.ru/holidays/wholeworld/ target=_blank><img src="http://www.calend.ru/img/export/informer_15.png" width="150" alt="Международные праздники" border="0"></a>
	<a href=http://www.calend.ru/holidays/unusual/ target=_blank><img src="http://www.calend.ru/img/export/informer_unusual.png" width="150" alt="Необычные праздники" border="0"></a>
	<a href=http://www.calend.ru/holidays/network/ target=_blank><img src="http://www.calend.ru/img/export/informer_network.png" width="150" alt="Сетевые праздники" border="0"></a>
	<a href=http://www.calend.ru/holidays/sport/ target=_blank><img src="http://www.calend.ru/img/export/informer_sport.png" width="150" alt="Спортивные праздники" border="0"></a>
	<a href=http://www.calend.ru/holidays/prof/ target=_blank><img src="http://www.calend.ru/img/export/informer_prof.png" width="150" alt="Профессиональные праздники" border="0"></a>
</div>

<?	
}
else
{
echo "<br /><br />Увы, нет ни одной картинки на тему дня";
}

function is_odd($number) {
   return $number & 1; // 0 = even, 1 = odd
}

?>
