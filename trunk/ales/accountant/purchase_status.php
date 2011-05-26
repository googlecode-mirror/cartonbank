<?
include("/home/www/cb/ales/config.php");
global $imagepath;

if(isset($_GET['sta']) && is_numeric($_GET['sta']) && isset($_GET['purch_id']) && is_numeric($_GET['purch_id']))
{
	$purch_id = $_GET['purch_id'];
	$new_processed_id = $_GET['sta'];
	$purch_date = $_GET['date'];

	//fw(" purch_date=>".$purch_date);

	// update database

	$link = mysql_connect($mysql_hostname, $mysql_user, $mysql_password);
	mysql_set_charset('utf8',$link);

	// current date
	$this_date = getdate();
	if ($new_processed_id == 5)
	{
		list($day, $month, $year) = split('[/.-]', $purch_date);
		$transformed_date = date('y-m-d',mktime(0, 0, 0, $month , $day, $year));
		$this_date = $transformed_date;
	}
	else
	{
		$this_date = '';
	}
	
	
	// update purchase status
	$sql = "update wp_purchase_logs set processed = '".$new_processed_id."' where id=".$purch_id;
	fw ($sql);
	$result = mysql_query($sql);
	if (!$result) {die('Invalid query: ' . mysql_error());}

	// update purchase date
	$sql = "update wp_purchase_logs set payment_arrived_date = '".$this_date."' where id=".$purch_id;
	fw ($sql);
	$result = mysql_query($sql);
	if (!$result) {die('Invalid query: ' . mysql_error());}

}

return;

function fw($text)
{
	$fp = fopen('/home/www/cb3/ales/_kloplog.txt', 'a');
	fwrite($fp, $text.' ');
	fclose($fp);
}

?>
