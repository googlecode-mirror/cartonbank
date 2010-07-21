<?
$image = exif_thumbnail($_GET["path"]);  //php's implementation
Header("Content-type: image/jpeg");
echo $image;
?>
