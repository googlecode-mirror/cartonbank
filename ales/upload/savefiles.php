<?php 
echo "please, upload files";

// files
if (isset($_FILES))
	$result = $_FILES;
else
	$result = "no files received";

fww($result);

// forms
if (isset($_POST))
	$result = $_POST;
else
	$result = "no form fields received";

fw($result);


function fww($text)
{
	$fp = fopen('_kloplog.txt', 'a') or die('Could not open file!');
	fwrite($fp, "\n====FILES====\n") or die('Could not write to file');
	foreach ($text as $key => $value) { 
		foreach ($value as $vkey => $vvalue) { 
			$toFile = " $vkey = $vvalue \n";
			fwrite($fp, $toFile) or die('Could not write to file');
		}
		fwrite($fp, "\n") or die('Could not write to file');
	}
	fclose($fp);
}

function fw($text)
{
	$fp = fopen('_kloplog.txt', 'a') or die('Could not open file!');
	fwrite($fp, "-----POST----\n") or die('Could not write to file');
	foreach ($text as $key => $value) { 
			$toFile = " $key = $value \n";
			fwrite($fp, $toFile) or die('Could not write to file');
	}
	fclose($fp);
}

function fw1($text)
{
	$fp = fopen('_kloplog.txt', 'a') or die('Could not open file!');
	fwrite($fp, "\n-----POST[carname]----\n") or die('Could not write to file');
			fwrite($fp, $text) or die('Could not write to file');
	fclose($fp);
}












/*
$('li div span').find('#carname1').val()
*/



?>