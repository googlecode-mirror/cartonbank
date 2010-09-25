/*global FSR_isWorking FSR_current_post jQuery */
function FSR_star_over(obj, star_number, starType) {
	var cr=obj.parentNode;
	var as=cr.getElementsByTagName('label');
	for(var i=0;i<star_number;++i) {
		as[i].lastClass = as[i].className;
		as[i].className = 'FSR_full_' + starType;
	}
	for(;i<as.length;++i) {
		as[i].lastClass = as[i].className;
	}
}
function FSR_star_out(obj) {
	var bs=obj.getElementsByTagName('label');
	for (var j=0;j<bs.length; j++)
	{
		if (bs[j].lastClass) {
			bs[j].className = bs[j].lastClass;
		}
	}
}
function FSR_save_vote(post, points, ajax_stars, starType)
{
	if(!FSR_isWorking)
	{
		FSR_current_post=post;
		jQuery.get(ajax_stars, {p: FSR_current_post, fsr_stars: points, starType: starType}, function(html){
			FSR_isWorking=false;
			jQuery('.FSR_container').html(html);
		});
	}
}
jQuery(function(){
	jQuery('label[for^=fsr_star_]').click(function(){
		var fsr_star=jQuery(this).attr('for');
		var ajax_stars=jQuery(this).parent().attr('action');
		var starType=jQuery(this).parent().children('input[name=starType]').val();
		var voteValue=fsr_star.split('_');
		var post=voteValue[2];
		var point=voteValue[3];
		FSR_save_vote(post, point, ajax_stars, starType);
	});
	jQuery('label[for^=fsr_star_]').mouseover(function(){
		var fsr_star_over=jQuery(this).attr('for');
		var starType=jQuery(this).parent().children('input[name=starType]').val();
		var starValue=fsr_star_over.split('_');
		var points=starValue[3];
		FSR_star_over(this, points, starType);
	});
});
FSR_current_post = null;
FSR_isWorking=false;