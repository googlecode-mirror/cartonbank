var testsuccess = 0;
var lnid = new Array();
function categorylist(url)
  {
  self.location = url;
  }

var getresults=function(results)
  {
  document.getElementById('shoppingcartcontents').innerHTML = results;
  if(document.getElementById('loadingimage') != null)
    {
    document.getElementById('loadingindicator').style.visibility = 'hidden';
    }
    else if(document.getElementById('alt_loadingimage') != null)
    {
    document.getElementById('alt_loadingindicator').style.visibility = 'hidden';
    } 
  }

function submitform(frm)
  {
  //alert(ajax.serialize(frm));
  ajax.post("index.php?ajax=true&user=true",getresults,ajax.serialize(frm));
  if(document.getElementById('loadingimage') != null)
    {
    document.getElementById('loadingimage').src = base_url+'/wp-content/plugins/wp-shopping-cart/images/indicator.gif';
    document.getElementById('loadingindicator').style.visibility = 'visible';
    } 
    else if(document.getElementById('alt_loadingimage') != null)
    {
    document.getElementById('alt_loadingimage').src = base_url+'/wp-content/plugins/wp-shopping-cart/images/indicator.gif';
    document.getElementById('alt_loadingindicator').style.visibility = 'visible';
    } 
  return false;
  }

function emptycart()
  {
  ajax.post("index.php",getresults,"ajax=true&user=true&emptycart=true");
  if(document.getElementById('loadingimage') != null)
    {
    document.getElementById('loadingimage').src = base_url+'/wp-content/plugins/wp-shopping-cart/images/indicator.gif';
    document.getElementById('loadingindicator').style.visibility = 'visible';
    } 
    else if(document.getElementById('alt_loadingimage') != null)
    {
    document.getElementById('alt_loadingimage').src = base_url+'/wp-content/plugins/wp-shopping-cart/images/indicator.gif';
    document.getElementById('alt_loadingindicator').style.visibility = 'visible';
    } 
    
  }

function show_additional_description(id,image_id)
  {
  currentstate = document.getElementById(id).style.display;
  //document.getElementById(id).style.display = 'inline';
  if(currentstate != 'block')
    {
    document.getElementById(id).style.display = 'block';
    document.getElementById(image_id).src = base_url+'/wp-content/plugins/wp-shopping-cart/images/icon_window_collapse.gif';
    }
    else
      {
      document.getElementById(id).style.display = 'none';
      document.getElementById(image_id).src = base_url+'/wp-content/plugins/wp-shopping-cart/images/icon_window_expand.gif';
      }
  return false;
  }

function prodgroupswitch(state)
  {
	  document.getElementById('branddisplay').style.display = 'block';
	  document.getElementById('categorydisplay').style.display = 'block';
 /* if(state == 'brands')
    {
    document.getElementById('categorydisplay').style.display = 'none';
    document.getElementById('branddisplay').style.display = 'block';
    }
    else if(state == 'categories')
      {
      document.getElementById('branddisplay').style.display = 'none';
      document.getElementById('categorydisplay').style.display = 'block';
      }*/
  return false;
  }
  
var previous_rating;
function ie_rating_rollover(id,state)
  {
  target_element = document.getElementById(id);
  switch(state)
    {
    case 1:
    previous_rating = target_element.style.background;
    target_element.style.background = "url("+base_url+"/wp-content/plugins/wp-shopping-cart/images/blue-star.gif)";
    break;
    
    default:
    if(target_element.style.background != "url("+base_url+"/wp-content/plugins/wp-shopping-cart/images/gold-star.gif)")
      {
      target_element.style.background = previous_rating;
      }
    break;
    }
  }  
  
var apply_rating=function(results)
  {
  outarr = results.split(",");
  //alert(results);
  for(i=1;i<=outarr[1];i++)
    {
    id = outarr[0]+"and"+i+"_link";
    document.getElementById(id).style.background = "url("+base_url+"/wp-content/plugins/wp-shopping-cart/images/gold-star.gif)";
    }
    
  for(i=5;i>outarr[1];i--)
    {
    id = outarr[0]+"and"+i+"_link";
    document.getElementById(id).style.background = "#c4c4b8";
    }
  lnid[outarr[0]] = 1; 
    
  rating_id = 'rating_'+outarr[0]+'_text';
  //alert(rating_id);
  if(document.getElementById(rating_id).innerHTML != "Your Rating:")
    {
    document.getElementById(rating_id).innerHTML = "Your Rating:";
    }
    
  saved_id = 'saved_'+outarr[0]+'_text';
  document.getElementById(saved_id).style.display = "inline";
  update_vote_count(outarr[0]);
  }
  
function hide_save_indicator(id)
  {
  document.getElementById(id).style.display = "none";
  }
  
function rate_item(prodid,rating)
  {
  ajax.post("index.php",apply_rating,"ajax=true&rate_item=true&product_id="+prodid+"&rating="+rating);
  }
  
function update_vote_count(prodid)
  {
  var update_vote_count=function(results)
    {
    outarr = results.split(",");
    vote_count = outarr[0];
    prodid = outarr[1];
    vote_count_id = 'vote_total_'+prodid;
    document.getElementById(vote_count_id).innerHTML = vote_count;
    }
  ajax.post("index.php",update_vote_count,"ajax=true&get_rating_count=true&product_id="+prodid);
  }
  

function submit_change_country()
  {
  document.forms.change_country.submit();
  }
  
function update_preview_url(prodid)
  {
  image_height = document.getElementById("image_height").value;
  image_width = document.getElementById("image_width").value;
  if(((image_height > 0) && (image_height <= 1024)) && ((image_width > 0) && (image_width <= 1024)))
    {
    new_url = "index.php?productid="+prodid+"&height="+image_height+"&width="+image_width+"";
    document.getElementById("preview_link").setAttribute('href',new_url);
    }
    else
      {
      new_url = "index.php?productid="+prodid+"";
      document.getElementById("preview_link").setAttribute('href',new_url);
      }
  return false;
  }

function zoom(url,ww,hh,id){
	n4=(document.layers);
	n6=(document.getElementById&&!document.all);
	ie=(document.all);
	var wX=10;
	var wY=10;
	if(!ie && !n6){
		ww+=15;
		hh+=15;
	}
	if(ie || n6){
		wX=(screen.availWidth-(ww+5))*.5;
		wY=(screen.availHeight-(hh+20))*.5;
	}
	pageurl='http://cartoonbank.cartoonist.name/wp-content/plugins/wp-shopping-cart/zoom.php?id='+url+'&w='+ww+'&h='+hh;
//	popup=window.open(url,id,"scrollbars=0,resizable=0,width="+ww+",height="+hh+",left="+wX+",top="+wY);
	popup=window.open(pageurl,id,"scrollbars=0,resizable=0,width="+ww+",height="+hh+",left="+wX+",top="+wY);
}




function rokfor(brand){
	if (confirm('Внимание! В категории «Рабочий стол» находятся работы автора, не вошедшие в основную базу Банка Изображений.\n\r\n\rПросьба к зрителям и клиентам отнестись с пониманием к праву автора держать в своем «Рабочем столе» изображения, которые Cartoonbank.ru или сам автор сочли невозможным для показа в общем поиске Банка Изображений. Это могут быть иллюстраторские опыты и эксперименты на различные темы, варианты обложек книг, сюжеты, несущие эротическое или иное содержание \"на грани фола\" и многое другое.\n\r\n\rЕсли вы не хотите видеть такие изображения - нажмите кнопку \"Отмена (Cancel)\".\n\r\n\rЕсли хотите видеть, несмотря на предупреждение - нажмите кнопку \"ОК\"'))
		{window.location = 'http://cartoonbank.ru/?page_id=29&category=666&brand='+brand;} 
	else 
		{window.location = '#';}
}

function on_start()
{
	if(!!location.hash) {
		var cid = location.hash.substring(1);
		window.location.hash='';
		var thisurl = window.location.href.slice(0, -1);
		cleanurl = thisurl+cid;
		location.href = cleanurl;
	}
}

function get_5stars()
{
jQuery(document).ready(function() {
var cuid = document.getElementById('cuid').innerHTML;
var starurl = "http://cartoonbank.ru/wp-content/plugins/five-star-rating/fsr-ajax-stars.php?p="+cuid+"&starType=star";
jQuery("#star_rating").load(starurl,function(){jQuery(function(){jQuery("label[for^=fsr_star_]").click(function(){var a=jQuery(this).attr("for"),b=jQuery(this).parent().attr("action"),d=jQuery(this).parent().children("input[name=starType]").val();a=a.split("_");FSR_save_vote(a[2],a[3],b,d)});jQuery("label[for^=fsr_star_]").mouseover(function(){var a=jQuery(this).attr("for"),b=jQuery(this).parent().children("input[name=starType]").val();a=a.split("_")[3];FSR_star_over(this,a,b)})});FSR_current_post=null;FSR_isWorking=false;});
});
}
function get_share_this()
{
jQuery(document).ready(function() {
var cuid = document.getElementById('cuid').innerHTML;
jQuery("#share_this").html('<b>Поделиться:</b><br /><a href="#" onclick="cuid=document.getElementById(\'cuid\').innerHTML; uu=\'http://twitter.com/share?url=\' + escape(\'http://cartoonbank.ru/?page_id=29&cartoonid=\'); window.open(uu+cuid);"><img src="img/s_twitter.png" border="0"></a>&nbsp;<a href="#" onclick="cuid=document.getElementById(\'cuid\').innerHTML; uu=\'http://www.facebook.com/sharer.php?t=cartoonbank.ru&u=\'+escape(\'http://cartoonbank.ru/?page_id=29&cartoonid=\'); window.open(uu+cuid);"><img src="img/s_facebook.png" border="0"></a>&nbsp;<a href="#" onclick="cuid=document.getElementById(\'cuid\').innerHTML; uu=\'http://vkontakte.ru/share.php?title=cartoonbank.ru&url=\'+escape(\'http://cartoonbank.ru/?page_id=29&cartoonid=\'); window.open(uu+cuid);"><img src="img/s_vkontakte.png" border="0"></a>&nbsp;<a href="#" onclick="cuid=document.getElementById(\'cuid\').innerHTML; uu=\'http://www.livejournal.com/update.bml?subject=cartoonbank.ru&event=\'+escape(\'http://cartoonbank.ru/?page_id=29&cartoonid=\'); window.open(uu+cuid);"><img src="img/s_livejournal.png" border="0"></a>&nbsp;<g:plusone size="small" count="false"></g:plusone>');
});
}
function change_url() { jQuery(document).ready(function() { function locationHashChanged() { if (location.hash === "#pt" || location.hash === "#") { var cuid = document.getElementById('cuid').innerHTML; window.location.hash = '&cartoonid='+cuid; } } window.onhashchange = locationHashChanged; });
}