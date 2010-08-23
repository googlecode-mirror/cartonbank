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
