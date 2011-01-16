<?php
//fw("inin");

include("config.php");

$ip = 'none';
if (isset($_GET['ip']))
	{$ip = $_GET['ip'];}
elseif (isset($_SERVER['REMOTE_ADDR']) and $_SERVER['REMOTE_ADDR'] != '')
	{$ip=$_SERVER['REMOTE_ADDR'];}

//fw("\n\r _POST['id']=".$_POST['id']);

if($_POST['id'] or $_GET['id'])
{
if (isset($_POST['id']))
	$id=$_POST['id'];
elseif (isset($_GET['id']))
	$id=$_GET['id'];
	$id = mysql_escape_String($id);

	$ip_sql=mysql_query("select ip_add from al_editors_voting_ip where mes_id_fk='$id' and ip_add='$ip'");
	
	$count=mysql_num_rows($ip_sql);

	if($count==0)
	{
		$sql = "update al_editors_votes set black=black+1  where image_id='$id'";
		mysql_query( $sql);

		$sql_in = "insert into al_editors_voting_ip (mes_id_fk,ip_add) values ('$id','$ip')";
		mysql_query( $sql_in);
	}

	$result=mysql_query("select black from al_editors_votes where image_id='$id'");

	$row=mysql_fetch_array($result);
	$black_value=$row['black'];

		//fw("\n\r up_value=".$up_value);
		if ($black_value >= $limit_black) // 2 плюса - проходит, 3 минуса - не проходит, 1 чёрн. метка
		{
			$sql = "update wp_product_list set visible=0 where id='$id'";
			fw("\n\r sql=".$sql);
			mysql_query( $sql);
		}


	echo $black_value;

}

function fw($text)
{
$fp = fopen('_kloplog.txt', 'a');
fwrite($fp, $text);
fclose($fp);
}

?>
