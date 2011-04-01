
<?
// settings
//
	$grace_days = 7; //how many days the theme of the day picture is active
	$thedate = date("d.m.y"); 	// current date
		//pokazh ($thedate,"thedate: ");
		$sqlthedate = date("Y.m.d"); 	// current date
		//pokazh ($sqlthedate,"sqlthedate: ");
	$expirationdate  = date("y.m.d", mktime(0, 0, 0, date("m"), date("d")-$grace_days, date("Y")));
		//pokazh ($expirationdate,"expirationdate: ");
	$tomorrow = date("Y.m.d", mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
	$tomorrowh = date("d.m.y", mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));

//
//


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
	$sql = "delete from `tema_dnya` where datetime = '".$_POST['temadnyadate']."'";
	$wpdb->query($sql);
	$sql = "insert into `tema_dnya` (id, datetime)values('".trim($_POST['temadnyaid'])."','".$_POST['temadnyadate']."')";
	$wpdb->query($sql);
	//pokazh ($sql,"sql: ");
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
	$sql = "Select id from tema_dnya where datetime = '".$sqlthedate."'";
	//pokazh ($sql,"sql: ");
	$cartoon_of_the_day = $wpdb->get_results($sql);

if ($cartoon_of_the_day!= null)
{
	$cartoon_of_the_day_id = $cartoon_of_the_day[0]->id;
}
else
{
	$cartoon_of_the_day_id = 0;
}

//find cartoon of tomorrow
	$sql = "Select id from tema_dnya where datetime = '".$tomorrow."'";
	//pokazh ($sql,"sql: ");
	$cartoon_of_tomorrow = $wpdb->get_results($sql);

if ($cartoon_of_tomorrow!= null)
{
	$cartoon_of_tomorrow_id = $cartoon_of_tomorrow[0]->id;
}
else
{
	$cartoon_of_tomorrow_id = 0;
}


// get all images for the theme of the date
$sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1'  AND `wp_item_category_associations`.`category_id` != '666'  AND `wp_product_list`.`approved` = '1'  AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` AND `wp_item_category_associations`.`category_id`='777'  AND `wp_product_categories`.`id`=777  ORDER BY `wp_product_list`.`id` DESC LIMIT 0,20";

//pokazh ($sql,"sql: ");

$product_list = $GLOBALS['wpdb']->get_results($sql,ARRAY_A);


  echo "<div style='width=700px; text-align:left;'>";

  echo "<div><h2>Картинки на темы дня</h2></div>";
  
	// wdd image by id
	echo "<div><h3>Добавить картинку по номеру в темы дня</h3> </div>";
	echo "<div><form method=post action=''><input type='text' name='addid'><input type='submit' value='добавить в темы дня'></form></div>";


if($product_list != null)
{
  echo "<div><h3 style='color:#FF33CC;'>Сегодняшняя тема дня (".$thedate.")</h3></div>";

  foreach($product_list as $product)
  {
	  if ($cartoon_of_the_day_id == $product['id'])
	  {
		echo "<div class='item'><a href='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."' target='_blank'><img src='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' class='thumb1'  title='".$product['id']."'></a>";
	  echo "<form method=post action=''>
		<input type='hidden' name='temadnyaid' value='".$product['id']."'>
		<input type='hidden' name='temadnyadate' value='".$sqlthedate."'>
		<input type='submit' value='тема выбрана!' style='background-color:#FF00FF;color:white;'></form>
		<input type='hidden' name='deleteid' value='".$product['id']."'>
		<input type='image' src='../img/trash.gif' title='уже не убрать'>";
	  }
	  else
	  {
		echo "<div class='item'><a href='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."' target='_blank'><img src='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' class='thumb' title='".$product['id']."'></a>";
	  echo "<form method=post action=''>
		<input type='hidden' name='temadnyaid' value='".$product['id']."'>
		<input type='hidden' name='temadnyadate' value='".$sqlthedate."'>
		<input type='submit' value='это тема!'></form>
		<form method=post action=''>
		<input type='hidden' name='deleteid' value='".$product['id']."'>
		<input type='image' src='../img/trash.gif' alt='убрать' title='убрать'></form>";
	  }
	  echo "</div>";
  }
  echo "</div><!-- items -->";
  

echo "<div style='clear:both;'></div>";

  echo "<div><h3 style='color:#FF33CC;'>Завтрашняя тема дня (".$tomorrowh.")</h3></div>";


  foreach($product_list as $product)
  {
	  if ($cartoon_of_tomorrow_id == $product['id'])
	  {
		echo "<div class='item'><a href='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."' target='_blank'><img src='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' class='thumb1' title='".$product['id']."'></a>";
	  echo "<form method=post action=''>
		<input type='hidden' name='temadnyaid' value='".$product['id']."'>
		<input type='hidden' name='temadnyadate' value='".$sqlthedate."'>
		<input type='submit' value='тема выбрана!' style='background-color:#FF00FF;color:white;'></form>
		<input type='hidden' name='deleteid' value='".$product['id']."'>
		<input type='image' src='../img/trash.gif' title='уже не убрать'>";
	  }
	  else
	  {
		echo "<div class='item'><a href='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/product_images/".$product['image']."' target='_blank'><img src='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/".$product['image']."' class='thumb'  title='".$product['id']."'></a>";
		echo "<form method=post action=''>
		<input type='hidden' name='temadnyaid' value='".$product['id']."'>
		<input type='hidden' name='temadnyadate' value='".$tomorrow."'>
		<input type='submit' value='это тема!'></form>
		<form method=post action=''>
		<input type='hidden' name='deleteid' value='".$product['id']."'>
		<input type='image' src='../img/trash.gif' alt='убрать' title='убрать'></form>";
	  }
	  echo "</div>";
  }
  echo "</div><!-- items -->";

}
else
{
echo "<br /><br />Увы, нет ни одной картинки на тему дня";
}


?>
