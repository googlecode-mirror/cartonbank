<?php 
include("/home/www/cb3/ales/config.php");

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
	}


mysql_close($con);


/*
Insert into wp_fsr_user (user, post, points, ip, vote_date) values ('sale',9514,5,'127.0.0.1',now());

Update wp_fsr_post set votes=votes+1, points=points+5 where id = 9514;

Update wp_product_list set votes=votes+1, votes_sum=votes_sum+5,votes_rate=((votes_sum+5)/(votes+1))*(sqrt(sqrt((votes_sum+5)/(votes+1)))) where id = 9514;

Update wp_product_list set votes_rate=(votes_sum/votes)*(sqrt(sqrt(votes))) where id = 9514;


*/
?>