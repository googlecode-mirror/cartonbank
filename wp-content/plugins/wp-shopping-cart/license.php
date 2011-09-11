<?
//print_r ($_SERVER);
//echo "<br>";

$abspath = '/home/www/cb3/';
$abspath_1 = "/home/www/cb/";
$abspath_2 = "/home/www/cb3/";


if (strstr($_SERVER['PHP_SELF'],'cb3/'))
	{$abspath = $abspath_2;}
else if (strstr($_SERVER['PHP_SELF'],'cb/')) 
	{$abspath = $abspath_1;}

//echo "<br>";
//echo $abspath;

require($abspath.'wp-blog-header.php');

if (isset($_GET['l']) && is_numeric($_GET['l']))
 $license_num = ($_GET['l']);

if (isset($_GET['item']) && is_numeric($_GET['item']))
 get_license($_GET['item'],$license_num);

function get_license($sequence_of_image,$license_num)
{
	/*
	#agreement_number#
	#agreement_date#
	#customer_name#
	#image_number#
	#author_name#
	#media_name#
	#price#
*/

// load unique license data
	$current_user = wp_get_current_user();    

//echo("<pre>".print_r($current_user,true)."</pre>");
//echo("<pre>".print_r($_SESSION['nzshpcrt_cart'],true)."</pre>");

	$agreement_number = uniqid();
	$agreement_date = date("m.d.y");
	$customer_name = $current_user->last_name. " " . $current_user->first_name;
if (isset($current_user->user_description)&& $current_user->user_description!='')
	$media_name = "«".$current_user->user_description."»";
else
	$media_name = '[не указано]';

if (isset($current_user->discount) && $current_user->discount!='')
	$discount = (int)$current_user->discount;
else
	$discount = 0;

if(isset($_SESSION['nzshpcrt_cart']))
{
	$price = $_SESSION['nzshpcrt_cart'][$sequence_of_image] -> price;
		$percent_to_pay = (100-$discount);
		$price = ($price/100)*$percent_to_pay;

	$image_name = $_SESSION['nzshpcrt_cart'][$sequence_of_image] -> name;
	$image_number = $_SESSION['nzshpcrt_cart'][$sequence_of_image] -> product_id;
	$author_name = $_SESSION['nzshpcrt_cart'][$sequence_of_image] -> author;
}

//pokazh($current_user);

if (isset($current_user) && $current_user->ID == 106)
	{
		echo "<div style='color:red;'>Демонстрационная лицензия</div>";
		$agreement_number = "XXXXXXX";
		$customer_name = "Демо-пользователь";
		$image_number = "XXXX-номер изображения";
		$image_name = "Название рисунка";
		$author_name = "Имя автора";
		$media_name = "Название компании покупателя";
		$price = "XXX-Цена";
	}

//load License template
$filename = '';
switch($license_num)
        {
        case 1:
        $filename = getcwd()."/"."license_limited_template.htm";
        break;
        
        case 2:
        $filename = getcwd()."/"."license_standard_template.htm";
        break;

        case 3:
        $filename = getcwd()."/"."license_extended_template.htm";
        break;

        default:
        $filename = getcwd()."/"."license_limited_template.htm";
        break;
}

$content=loadFile($filename); 

//echo $filename;
//echo $content;

// replace placeholders
	$content = str_replace ('#agreement_number#',$agreement_number,$content);
	$content = str_replace ('#agreement_date#',$agreement_date,$content);
	$content = str_replace ('#customer_name#',$customer_name,$content);
	$content = str_replace ('#image_number#',$image_number,$content);
	$content = str_replace ('#image_name#',$image_name,$content);
	$content = str_replace ('#author_name#',$author_name,$content);
	$content = str_replace ('#media_name#',$media_name,$content);
	$content = str_replace ('#price#',$price,$content);

// output content
echo $content;
}

function loadFile($sFilename, $sCharset = 'UTF-8')
{
    if (floatval(phpversion()) >= 4.3) {
        $sData = file_get_contents($sFilename);
    } else {
        if (!file_exists($sFilename)) return -3;
        $rHandle = fopen($sFilename, 'r');
        if (!$rHandle) return -2;

        $sData = '';
        while(!feof($rHandle))
            $sData .= fread($rHandle, filesize($sFilename));
        fclose($rHandle);
    }
    if ($sEncoding = mb_detect_encoding($sData, 'auto', true) != $sCharset)
        $sData = mb_convert_encoding($sData, $sCharset, $sEncoding);
    return $sData;
}

?>