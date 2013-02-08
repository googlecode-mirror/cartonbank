<!-- attach the plug-in to the slider parent element and adjust the settings as required -->
<script class="secret-source">
jQuery(document).ready(function($) {
  
  $('#slides').bjqs({
	animtype      : 'slide',
	width         : 750,
	height         : 620,
	automatic : false,
	responsive    : true,
	nexttext : '', 
	prevtext : '', 
	randomstart   : false
  });
  
});
</script>

<script type="text/javascript">
<!--
jQuery(document).ready(function() {
on_start();
<?if (isset($sword)&&$sword!=''){
?>highlight('<?echo $sword;?>');<?
}
?>
//get_stars();
//get_share_this();
});


function get_stars(cuid)
{
//var cuid = document.getElementById('cuid').innerHTML;
var starurl = "http://cartoonbank.ru/wp-content/plugins/five-star-rating/fsr-ajax-stars.php?p="+cuid+"&starType=star";
var divid = "#star_rating_"+cuid;
jQuery(divid).load(starurl,function(){
	jQuery(function(){
		jQuery("label[for^=fsr_star_]").click(function(){
			var a=jQuery(this).attr("for"),b=jQuery(this).parent().attr("action"),d=jQuery(this).parent().children("input[name=starType]").val();a=a.split("_");FSR_save_vote(a[2],a[3],b,d)});jQuery("label[for^=fsr_star_]").mouseover(function(){var a=jQuery(this).attr("for"),b=jQuery(this).parent().children("input[name=starType]").val();a=a.split("_")[3];FSR_star_over(this,a,b)})});FSR_current_post=null;FSR_isWorking=false;});


}

//-->
</script>
