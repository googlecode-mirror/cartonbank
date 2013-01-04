<?php 
include("/home/www/cb3/ales/config.php");


// get he largest purchase id from the all_purchasss table
$sql = "SELECT MAX(`purchase_id`) as max FROM `all_purchases`";
	$con = mysql_connect($mysql_hostname,$mysql_user,$mysql_password);
		if (!$con){
		  die('Could not connect: ' . mysql_error());
		}
		mysql_select_db($mysql_database, $con);

			$result = mysql_query($sql);
			while($row=mysql_fetch_array($result))
			{
				$maxid = $row['max'];
				pokazh ($maxid,"maxid"); 
			}

$sql ="SELECT c.purchaseid, p.id as cartoonid
	FROM `wp_purchase_logs` as l, 
		`wp_purchase_statuses` as s, 
		`wp_cart_contents` as c, 
		`wp_product_list` as p,
		`wp_download_status` as st,
		`wp_product_brands` as b,
		`wp_users` as u,
		`wp_usermeta` as um
	WHERE	l.`processed`=s.`id` 
		AND l.id=c.purchaseid 
		AND p.id=c.prodid  
		AND st.purchid=c.purchaseid
		AND p.brand=b.id
		AND u.id = l.user_id
		AND u.id = um.user_id
		AND um.meta_key = 'description'
		AND l.user_id != '106'
		AND st.downloads != '5'
		AND l.id > ".$maxid."
	GROUP BY c.license
	ORDER BY `datetime` ASC";	


	$result = mysql_query($sql);

	$sql ="INSERT INTO `cartoonbankru`.`all_purchases` (`id`, `purchase_id`, `cartoon_id`, `vote_added`) VALUES ";

	while($row=mysql_fetch_array($result))
	{
		$sql .= "(NULL, '".$row['purchaseid']."', '".$row['cartoonid']."', '0'),";
	}
	$sql = substr($sql,0,-1);
	pokazh ($sql);
	$result = mysql_query($sql);
	mysql_close($con);


// get a list of votes to update
$sql = "SELECT purchase_id, cartoon_id from all_purchases where vote_added = 0 limit 500";

$con = mysql_connect($mysql_hostname,$mysql_user,$mysql_password);
	if (!$con){
	  die('Could not connect: ' . mysql_error());
	}

	mysql_select_db($mysql_database, $con);

	$result = mysql_query($sql);

	while($row=mysql_fetch_array($result))
	{
		$id = $row['cartoon_id'];
		$purchase_id = $row['purchase_id'];


		pokazh($id);

		$sql = "Insert into wp_fsr_user (user, post, points, ip, vote_date) values ('sale',".$id.",5,'127.0.0.1',now())";
		echo $sql."<br>";
		$res = mysql_query($sql);

		$sql = "Update wp_fsr_post set votes=votes+1, points=points+5 where id = ".$id;
		echo $sql."<br>";
		$res = mysql_query($sql);

		$sql = "Update wp_product_list set votes=votes+1, votes_sum=votes_sum+5 where id = ".$id;
		echo $sql."<br>";
		$res = mysql_query($sql);

		$sql = "Update wp_product_list set votes_rate=(votes_sum/votes)*(sqrt(sqrt(votes))) where id = ".$id;
		echo $sql."<br>";
		$res = mysql_query($sql);

		$sql = "Update all_purchases set vote_added=1 where purchase_id = ".$purchase_id. " and cartoon_id = ".$id;
		echo $sql."<br>";
		$res = mysql_query($sql);

		echo "<br>";

		mail("igor.aleshin@gmail.com","+5 vote to cartoon ".$id." was added","Cartoon = ".$id."; purchase id = ".$purchase_id);
	}
mysql_close($con);
?>