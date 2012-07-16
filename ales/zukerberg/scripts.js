function maintitle() {
	modal();
    jQuery('#maintitle').css("height","466px");
    jQuery('#maintitle').css("width","600px");
    jQuery('#maintitle').center();
    jQuery('#maintitle').css("visibility","visible");
}
function login(){
	modal();
	jQuery('#maintitle').css("visibility","hidden");
    jQuery('#login').center();
    jQuery('#login').css("visibility","visible");
    jQuery("#name input[name=username]").focus();
	if(jQuery.cookie("uid")!=null){
		var uid =  jQuery.cookie("uid");
		jQuery("#name input[name=username]").load('http://duel.cartoonbank.ru/getusername.php?uid='+uid,function(resp){var user=resp; jQuery("#name input[name=username]").val(user);});
		
	}
}
function loading(){
    jQuery('#loading').center();
    jQuery('#loading').css("visibility","visible");
}
function modal(){
    jQuery("body").append('<div class="modalOverlay">');
    jQuery('.modalOverlay').css("background-color","#2d2d2d");
}
function res(num1){
	modal();
    if(num1==1){
        num2=2;
		jQuery.cookie("best",jQuery.cookie("id1"));
		jQuery.cookie("worst",jQuery.cookie("id2"));
        }
        else{
            num2=1;
		jQuery.cookie("best",jQuery.cookie("id2"));
		jQuery.cookie("worst",jQuery.cookie("id1"));
        }

    if(jQuery.cookie('cbrate')==null){jQuery.cookie('cbrate','0');}
    if(jQuery.cookie('cbclicks')==null){jQuery.cookie('cbclicks','0');}
    var rate=parseInt(jQuery.cookie('cbrate'));
    var clicks=parseInt(jQuery.cookie('cbclicks'));
    
		var image1rate = parseFloat(jQuery('#'+num1).attr('rate'));
		var image2rate = parseFloat(jQuery('#'+num2).attr('rate'));

    if (image1rate>image2rate) {
		bang();
        jQuery('#'+num1).css("border", "solid thick green");
        jQuery('#popup').css("background-color", "#C0FFC0");
		jQuery('#popup').css("background-image", "url('img/popal.jpg')");
        jQuery('#popup').html('<p>Попал!</p><p>'+jQuery('#'+num1).attr('rate')+' больше, чем ' + jQuery('#'+num2).attr('rate') + '</p><p>Попаданий: '+(rate+1)+' из ' + (clicks+1) + '</p>');
        jQuery('#popup').center();
		jQuery('#popup').css("display","none");
        jQuery('#popup').css("visibility","visible");
        jQuery('#popup').fadeIn('slow');
		jQuery.cookie('cbrate',(rate+1));
        jQuery.cookie('cbclicks',(clicks+1));
        }
        else if (image1rate<image2rate) {
		bang();
        jQuery('#'+num1).css("border", "solid thick red");
		jQuery('#popup').css("background-image", "url('img/mimo.jpg')");
        jQuery('#popup').css("background-color", "#FFC0C0");
        jQuery('#popup').html('<p>Мимо!(:</p><p>'+jQuery('#'+num1).attr('rate')+' меньше, чем ' + jQuery('#'+num2).attr('rate') + '</p><p>Попаданий: '+rate+' из ' + (clicks+1) + '</p>');
        jQuery('#popup').center();
		jQuery('#popup').css("display","none");
        jQuery('#popup').css("visibility","visible");
        jQuery('#popup').fadeIn('slow');
		jQuery.cookie('cbclicks',(clicks+1));
        }
		else{
		bang();
        jQuery('#'+num1).css("border", "solid thick blue");
		jQuery('#popup').css("background-image", "url('img/mimo.jpg')");
        jQuery('#popup').css("background-color", "#FFC0C0");
        jQuery('#popup').html('<p>Осечка!</p><p>'+jQuery('#'+num1).attr('rate')+' равно ' + jQuery('#'+num2).attr('rate') + '</p><p>Попаданий: '+rate+' из ' + (clicks+1) + '</p>');
        jQuery('#popup').center();
		jQuery('#popup').css("display","none");
        jQuery('#popup').css("visibility","visible");
        jQuery('#popup').fadeIn('slow');
		jQuery.cookie('cbclicks',(clicks+1));
		}
    addvote();
}
function refreshStat(){
	if (parseInt(jQuery.cookie("cbclicks"))>0){
		var myrate = jQuery.cookie("cbrate");
		var myclicks = jQuery.cookie("cbclicks");
		jQuery("#headerrate").html('рейт: '+(myrate/Math.sqrt(Math.sqrt(parseInt(jQuery.cookie("cbclicks"))))).toFixed(4)+'. Попаданий ' + myrate + ' из ' + myclicks);
	}
	if (jQuery.cookie("username")!=null){
		jQuery("#hi").html("Дуэлянт: <span class='y'>" + jQuery.cookie("username") + "</span>");
	}
    jQuery('#rating').load('http://duel.cartoonbank.ru/getrating.php?u='+jQuery.cookie("username")+'&s='+jQuery.cookie("secret"),
    function(){
        if (myrate>0){
        jQuery('#headerrate').append('. Ваше место: <span class="y">'+jQuery('#myplace').text() + '</span>');    
        }
    }
    );
	
};

function savename(){
    //var rate = jQuery.cookie("cbrate")/Math.sqrt(Math.sqrt(parseInt(jQuery.cookie("cbclicks"))));
	var rate = jQuery.cookie("cbrate")/Math.sqrt(Math.sqrt(parseInt(jQuery.cookie("cbclicks")))).toFixed(4);
	var guessed = jQuery.cookie("cbrate");
	var views = jQuery.cookie("cbclicks");
    var name = jQuery("#name input[name=username]").val();
    var secret = jQuery("#name input[name=secret]").val();
    jQuery.cookie("username",name);
    jQuery.cookie("secret",secret);
    
jQuery.getJSON("http://duel.cartoonbank.ru/savename.php",
		{ name: name, rate: rate, secret: secret, guessed: guessed, views: views },
		function(data){
			jQuery.cookie("cbclicks",data.views);
			jQuery.cookie("cbrate",data.guessed);
			jQuery.cookie("rate",data.rate);
			refreshStat();
		    //alert ('views: '+data.views + '; guessed: '+data.guessed+'; rate: '+data.rate);
			});

	jQuery.post('http://duel.cartoonbank.ru/savename.php',{ name: name, rate: rate, secret: secret, guessed: guessed, views: views });
    jQuery('#login').css('visibility','hidden');
    if (jQuery.cookie("username")!=null){
        jQuery("#hi").html("Дуэлянт: " + jQuery.cookie("username") + "");
    }
}
function addvote(){
    //var rate = jQuery.cookie("cbrate")/Math.sqrt(Math.sqrt(parseInt(jQuery.cookie("cbclicks"))));
	var rate = (parseFloat(jQuery.cookie("cbrate"))/Math.sqrt(Math.sqrt(parseInt(jQuery.cookie("cbclicks"))))).toFixed(2);
	var guessed = jQuery.cookie("cbrate");
	var views = jQuery.cookie("cbclicks");
    var name = jQuery.cookie("username");
    var secret = jQuery.cookie("secret");
    var best = jQuery.cookie("best");
    var worst = jQuery.cookie("worst");
    jQuery.post('http://duel.cartoonbank.ru/savename.php',{ rating: rate, name: escape(name), secret: escape(secret), guessed: guessed, views: views, best: best, worst: worst });
    jQuery('#login').css('visibility','hidden');
    if (jQuery.cookie("username")!=null){
        jQuery("#hi").html("Дуэлянт: " + jQuery.cookie("username") + "");
    }
	jQuery('#rating').load('http://duel.cartoonbank.ru/getrating.php?u='+jQuery.cookie("username")+'&s='+jQuery.cookie("secret"));
}
function onsave() {
	if (jQuery('#name input[name=username]').val().length == 0 || jQuery('#name input[name=secret]').val().length == 0)
	{
	alert('попробуйте ещё раз');
	jQuery.cookie("username",null);
	jQuery.cookie("secret",null)
	window.location.href = 'http://duel.cartoonbank.ru/index.php';
	return false;
	}
	savename();
	jQuery('.modalOverlay').remove()
}
function howtoplay() {
	jQuery('#howtoplay').removeClass('howtoplayhide')
	jQuery('#howtoplay').addClass('howtoplay');
	//css("height","600px");
	//jQuery('#howtoplay').css("width","600px");
    //jQuery("#howtoplay").css("visibility","visible");
    //jQuery("#howtoplay").css("display","block");
	//jQuery("#howtoplay").show();
	jQuery('#howtoplay').center();
}
function bang() {

	jQuery('#bang').css("height","244px");
	jQuery('#bang').css("width","320px");
	jQuery('#bang').center();
	jQuery('#bang').css("visibility","visible");
	jQuery('#bang').fadeOut(1000);
}
function CreateDelegate(contextObject, delegateMethod)
{
    return function()
    {
        return delegateMethod.apply(contextObject, arguments);
    }
}

function img1_onload()
{
	
	var sw = jQuery('body').innerWidth();//screen width
	var scale = sw/1200;
	var i1w = 600;
	var i2w = 600;

    //alert(this.width + " by " + this.height);
	i1w = this.width;

	if (scale<1) {
		jQuery("#1").width(i1w*scale-20);
		jQuery("#2").width(i2w*scale-20);
	}

}
function img2_onload()
{
	var sw = jQuery('body').innerWidth();//screen width
	var scale = sw/1200;
	var i1w = 600;
	var i2w = 600;
    //alert(this.width + " by " + this.height);
	i2w = this.width;

	if (scale<1) {
		jQuery("#1").width(i1w*scale-20);
		jQuery("#2").width(i2w*scale-20);
	}

}

var imgSizer = {
	Config : {
		imgCache : []
		,spacer : "/path/to/your/spacer.gif"
	}

	,collate : function(aScope) {
		var isOldIE = (document.all && !window.opera && !window.XDomainRequest) ? 1 : 0;
		if (isOldIE && document.getElementsByTagName) {
			var c = imgSizer;
			var imgCache = c.Config.imgCache;

			var images = (aScope && aScope.length) ? aScope : document.getElementsByTagName("img");
			for (var i = 0; i < images.length; i++) {
				images[i].origWidth = images[i].offsetWidth;
				images[i].origHeight = images[i].offsetHeight;

				imgCache.push(images[i]);
				c.ieAlpha(images[i]);
				images[i].style.width = "100%";
			}

			if (imgCache.length) {
				c.resize(function() {
					for (var i = 0; i < imgCache.length; i++) {
						var ratio = (imgCache[i].offsetWidth / imgCache[i].origWidth);
						imgCache[i].style.height = (imgCache[i].origHeight * ratio) + "px";
					}
				});
			}
		}
	}

	,ieAlpha : function(img) {
		var c = imgSizer;
		if (img.oldSrc) {
			img.src = img.oldSrc;
		}
		var src = img.src;
		img.style.width = img.offsetWidth + "px";
		img.style.height = img.offsetHeight + "px";
		img.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + src + "', sizingMethod='scale')"
		img.oldSrc = src;
		img.src = c.Config.spacer;
	}

	// Ghettomodified version of Simon Willison's addLoadEvent() -- http://simonwillison.net/2004/May/26/addLoadEvent/
	,resize : function(func) {
		var oldonresize = window.onresize;
		if (typeof window.onresize != 'function') {
			window.onresize = func;
		} else {
			window.onresize = function() {
				if (oldonresize) {
					oldonresize();
				}
				func();
			}
		}
	}
}

addLoadEvent(function() {
	imgSizer.collate();
});

function addLoadEvent(func) {
	var oldonload = window.onload;
	if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		window.onload = function() {
			if (oldonload) {
				oldonload();
			}
			func();
		}
	}
}



	jQuery.fn.center = function () {
		this.css("position","absolute");
		this.css("top", ((jQuery(window).height() - this.outerHeight()) / 2) + jQuery(window).scrollTop() + "px");
		this.css("left", ((jQuery(window).width() - this.outerWidth()) / 2) + jQuery(window).scrollLeft() + "px");
		return this;
	}

	
	// onload
	jQuery(document).ready(function() {
		if (jQuery.cookie("username")==null){maintitle();}
		if (jQuery.cookie("cbrate")==null){var myrate=0;jQuery.cookie("cbrate","0");}else{var myrate = jQuery.cookie("cbrate");}
		if (jQuery.cookie("cbclicks")==null){var myclicks=0;jQuery.cookie("cbclicks","0");}else{var myclicks = jQuery.cookie("cbclicks");}
		if (parseInt(jQuery.cookie("cbclicks"))>0){
			jQuery("#headerrate").html('рейт: '+(myrate/Math.sqrt(Math.sqrt(parseInt(jQuery.cookie("cbclicks"))))).toFixed(4)+'. Попаданий ' + myrate + ' из ' + myclicks);
		}

		jQuery.cookie("id1",jQuery('#image1').text());
		jQuery.cookie("id2",jQuery('#image2').text());
		
		
        refreshStat();
	var img1 = new Image();
	var img2 = new Image();


	img1.onload = CreateDelegate(img1, img1_onload);
	img1.src = jQuery('#1').attr('src');
	img2.onload = CreateDelegate(img2, img2_onload);
	img2.src = jQuery('#2').attr('src');

	jQuery('.rating').hide();
	});
