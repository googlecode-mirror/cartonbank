<?php
include("config.php");

$ip=$_SERVER['REMOTE_ADDR']; 


if($_POST['id'])
{
	$id=$_POST['id'];
	$id = mysql_escape_String($id);

	$ip_sql=mysql_query("select ip_add from al_editors_voting_ip where mes_id_fk='$id' and ip_add='$ip'");
	
	$count=mysql_num_rows($ip_sql);

	if($count==0)
	{
		$sql = "update al_editors_votes set down=down+1  where image_id='$id'";
		mysql_query( $sql);

		//$sql_in = "insert into al_editors_voting_ip (mes_id_fk,ip_add) values ('$id','$ip')";
		//mysql_query( $sql_in);
	}

	$result=mysql_query("select down from al_editors_votes where image_id='$id'");

	$row=mysql_fetch_array($result);
	$down_value=$row['down'];

		//fw("\n\r up_value=".$up_value);
		if ($down_value >= $limit_minus) // 2 плюса - проходит, 3 минуса - не проходит
		{
			$sql = "update wp_product_list set approved=0 where id='$id'";
			//fw("\n\r sql=".$sql);
			mysql_query( $sql);
		}


	echo $down_value;

}

function fw($text)
{
$fp = fopen('_kloplog.txt', 'a');
fwrite($fp, $text);
fclose($fp);
}

?>
