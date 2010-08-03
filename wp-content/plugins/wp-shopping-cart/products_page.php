<?php
global $wpdb;
$siteurl = get_option('siteurl');
$_SESSION['selected_country'] = '';
if(is_numeric($_GET['brand']) || (is_numeric(get_option('default_brand')) && (get_option('show_categorybrands') == 3)))
  {
  if(is_numeric($_GET['brand']))
    {
    $brandid = $_GET['brand'];
    }
    else
      {
      $brandid = get_option('default_brand');
      }
        
  
  $group_sql = "AND `brand`='".$brandid."'";
  
  
  $cat_sql = "SELECT * FROM `".$wpdb->prefix."product_brands` WHERE `id`='".$brandid."' LIMIT 1";
  $group_type = TXT_WPSC_BRANDNOCAP;
  }
  else if(is_numeric($_GET['category']) || (is_numeric(get_option('default_category')) && (get_option('show_categorybrands') != 3)))
    {
    if(is_numeric($_GET['category']))
      {
      $catid = $_GET['category'];
      }
      else
        {
        //$catid = get_option('default_category');
		$catid = '';
        }
		if ($catid==0)
		{
		$group_sql = "";
		$cat_sql = "SELECT * FROM `".$wpdb->prefix."product_categories`";
		}
		else
		{
		$group_sql = "AND `".$wpdb->prefix."item_category_associations`.`category_id`='".$catid."'";
		$cat_sql = "SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `id`='".$catid."' LIMIT 1";
		}
//    $group_sql = "AND `".$wpdb->prefix."item_category_associations`.`category_id`='".$catid."'";
//    $cat_sql = "SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `id`='".$catid."' LIMIT 1"; 
    $group_type = TXT_WPSC_CATEGORYNOCAP;
    }
    else
      {
      $group_type = TXT_WPSC_BRANDNOCAP;
      }

$category_data = $GLOBALS['wpdb']->get_results($cat_sql,ARRAY_A);


if($_GET['cart']== 'empty')
  {
  $_SESSION['nzshpcrt_cart'] = '';
  $_SESSION['nzshpcrt_cart'] = Array();
  }

/*
* this is now done by the ajax function
*/
if(is_numeric($_POST['item']))
  {
  $cartcount = count($_SESSION['nzshpcrt_cart']);
  $_SESSION['nzshpcrt_cart'][$cartcount + 1] = $_POST['item'];
  }

function nzshpcrt_display_categories_groups()
  {
  global $wpdb;
  if(get_option('permalink_structure') != '')
    {
    $seperator ="?";
    }
    else
      {
      $seperator ="&amp;";
      }
?>
<div class="wrap">
<?php
  if(function_exists('gold_shpcrt_search_form'))
    {
    echo gold_shpcrt_search_form();
    }
?>
<?php
  
  echo "<table>";
  
  switch(get_option('show_categorybrands'))
    {
    case 1:
//    echo "<tr><td class='prodgroupmidline'><a href='' onclick='return prodgroupswitch(\"categories\");'>".TXT_WPSC_CATEGORIES."</a></td><td class='prodgroupright'><a href='' onclick='return prodgroupswitch(\"brands\");'>".TXT_WPSC_BRANDS."</a></td></tr>";
    break;
    
    case 2:
	//	echo ("<tr><td colspan='2'>Выберите категорию:</td></tr>");
//    echo "<tr><td colspan='2'><a href='' onclick='return prodgroupswitch(\"categories\");'>".TXT_WPSC_CATEGORIES."</a></td></tr>";
    break;
    
    case 3:
//    echo "<tr><td colspan='2'><a href='' onclick='return prodgroupswitch(\"brands\");'>".TXT_WPSC_BRANDS."</a></td></tr>";
    break;
    }
  echo "<tr><td colspan='2'>";
  
  
  if((get_option('show_categorybrands') == 1 ) || (get_option('show_categorybrands') == 2))
    {
    //exit("done");
    echo "<div id='categorydisplay'>";
    $categories = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `active`='1' AND `category_parent` = '0' ORDER BY `order` ASC",ARRAY_A);
    if($categories != null)
      {
	   //$options .= "<a class='categorylink' href='".get_option('product_list_url').$seperator."category=0'>Все рисунки</a><br />";


      foreach($categories as $option)
        {
        $options .= "<a class='categorylink' href='".get_option('product_list_url')."&category=".$option['id']."'>".stripslashes($option['name'])."</a><br />";
        $subcategory_sql = "SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `active`='1' AND `category_parent` = '".$option['id']."' ORDER BY `id`";
        $subcategories = $wpdb->get_results($subcategory_sql,ARRAY_A);
        if($subcategories != null)
          {
          foreach($subcategories as $subcategory)
            {
            $options .= "<a class='categorylink' href='".get_option('product_list_url')."&category=".$subcategory['id']."'>-".stripslashes($subcategory['name'])."</a><br />";
            }
          }
        }
      }
    echo $options;
    }
    
  
  if((get_option('show_categorybrands') == 1 ) || (get_option('show_categorybrands') == 3))
    {
    //echo "<div id='branddisplay'>";
    $options ='';
    $brands = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_brands` WHERE `active`='1' ORDER BY `order` ASC",ARRAY_A);
    if($brands != null)
      {
      foreach($brands as $option)
        {
        $options .= "<a class='categorylink' href='".get_option('product_list_url').$seperator."brand=".$option['id']."'>".stripslashes($option['name'])."</a><br />";
        }
      }
    echo $options;
   // echo "</div>";
    }
    
    
  echo "</td></tr>";
  echo "</table>";
  }


  $num = 0;
  //else if(is_numeric($_GET['category']) || (is_numeric(get_option('default_category')) && (get_option('show_categorybrands') != 3)))
  if((is_numeric($_GET['category']) || is_numeric(get_option('default_category'))) && ((get_option('show_categorybrands') == 1) || (get_option('show_categorybrands') == 2)))
    {
    $display_items = true;
    }
    else if((is_numeric($_GET['brand']) || is_numeric(get_option('default_brand'))) && ((get_option('show_categorybrands') == 3) || (get_option('show_categorybrands') == 1)))
      {
      $display_items = true;
      }
      
  if($display_items == true)
    {

			// how many records total?
			if ($_GET['brand'] == '')
			{
			$sql = "SELECT COUNT(*) as count FROM `wp_product_list`,`wp_item_category_associations` WHERE `wp_product_list`.`active`='1' AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` $group_sql"; 
			}
			else
			{
				//$sql = "SELECT COUNT(*) as count FROM `wp_product_list`,`wp_item_category_associations` WHERE `wp_product_list`.`active`='1' AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_item_category_associations`.`category_id` != ".get_option('default_category')." $group_sql"; 
			$sql = "SELECT COUNT(*) as count FROM `wp_product_list`,`wp_item_category_associations` WHERE `wp_product_list`.`active`='1' AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` $group_sql"; 
			}

			$items_count = $GLOBALS['wpdb']->get_results($sql,ARRAY_A);

			if (is_numeric($items_count[0]['count']))
			{
				$items_count = $items_count[0]['count'];
			}
			else
			{
				$items_count = 0;
			}


    if(get_option('permalink_structure') != '')
      {
      $seperator ="?";
      }
      else
        {
        $seperator ="&amp;";
        }

     if(is_numeric($_GET['product_id']))
       {
       echo "<div style='float:left;'>";
       echo single_product_display($_GET['product_id']);
       }
       else
         {
        // echo nzshpcrt_display_categories_groups();
         if($_GET['product_search'] != null)
           {
           echo "<strong class='cattitles'>".TXT_WPSC_SEARCH_FOR." : ".$_GET['product_search']."</strong>";
           }
           else if ($catid == 0)
             {
    //global $wpdb;
    //$sql = "SELECT `wp_product_list`.* FROM `wp_product_list` WHERE `active`='1' AND `visible`='1' ORDER BY RAND(NOW()) LIMIT 1";
    $sql = "SELECT `wp_product_list` . * , `wp_product_files`.`width` , `wp_product_files`.`height` , `wp_product_brands`.`id` AS brandid, `wp_product_brands`.`name` AS brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list` , `wp_item_category_associations` , `wp_product_files` , `wp_product_brands` , `wp_product_categories` WHERE `wp_product_list`.`active` = '1' AND `wp_product_list`.`visible` = '1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id`  $group_sql ORDER BY RAND(NOW()) LIMIT 1"; 
    
    // список картинок
    $product = $GLOBALS['wpdb']->get_results($sql,ARRAY_A);
                
                $_number = $product[0]['id'];
                $_description = nl2br(stripslashes($product[0]['description']));
                $_size = $product[0]['width']."px X ".$product[0]['height']."px;";
                $_author = $product[0]['brand'];
                $_name = $product[0]['name'];
                //$_category = $product[0]['kategoria'];
                $_category = "<a href=\'".get_option('product_list_url')."&category=".$product[0]['category_id']."\'>".$product[0]['kategoria']."</a>";


                $_tags = nl2br(stripslashes($product[0]['additional_description']));
                $_tags_array = explode(',',$_tags);
                    //$i=0;
                    foreach ($_tags_array as $key => $value)
                    {
                        $_tags_array[$key] = "<a href=\"".get_option('siteurl')."/?page_id=29&cs=".trim($_tags_array[$key])."\">".trim($_tags_array[$key])."</a>";
                    }
                $_tags_imploded = implode(", ", $_tags_array);
                $_tags = $_tags_imploded;

                $_bigpicstrip = "<div style=\"float:left;\"><b>Название: </b>" .$_name."</div> "."<div>№&nbsp;".$_number."&nbsp;<b><a href=\"".$siteurl."/?page_id=29&brand=".$product[0]['brandid']."\">".$_author."</a></b></div>";
                $_bigpictext = "<b>Категория: </b><br>".$_category."<br><br><b>Описание: </b> ".$_description."<br><br><b>Тэги: </b><br>".$_tags."<br><br><b>Размер:</b><br>".$_size;
                $siteurl = get_option('siteurl');
                $_bigpic =  "<img src=\"".$siteurl."/wp-content/plugins/wp-shopping-cart/product_images/".$product[0]['image']."\">";

    
    //placeholder for the slide preview wAS HERE
                 

             }
			 else
             {
             //echo "Категория: <strong class='cattitles'>".$category_data[0]['name']."";
             }

//		 echo "<span id='loadingindicator'><img id='loadingimage' src='$siteurl/wp-content/plugins/wp-shopping-cart/images/indicator.gif' alt='Loading' title='Loading' /> ".TXT_WPSC_UDPATING."...</span>";
		 echo "</strong>";
             
         if(function_exists('product_display_list') && (get_option('product_view') == 'list'))
           {
           echo product_display_list($product_list, $group_type, $group_sql, $search_sql);
           }
           else
             {
			 // ales: product page starts here
					// old code to call all items at once
					// echo product_display_default($product_list, $group_type, $group_sql, $search_sql);
               // placeholder for the slide preview
               
               $_bigpictext = str_ireplace("\\'","\"",$_bigpictext);
                
               echo "<div id='bigpictopstrip'>".$_bigpicstrip."</div>";
               echo "<div id='bigpictext'>".$_bigpictext."</div>";
               echo "<div id='bigpic'>".$_bigpic."</div>";
               echo "<div style='clear:both;'></div>";
               echo "<div id='bigpicbottomstrip'></div>";

                    
                    
				if (is_numeric($_GET['offset']))
				 {
					$offset = $_GET['offset'];
				 }
				else 
				 {
					$offset = 0;
				 }

				$items_on_page = get_option('posts_per_page');

				// SEARCH
				if($_POST['cs']!= '' or $_GET['cs']!= '')
				{
					if($_POST['cs']!= ''){
						$keywords = strtolower(trim($_POST['cs']));
					}
					if($_GET['cs']!= ''){
						$keywords = strtolower(trim($_GET['cs']));
					}
                    // search request
					// count found results
					$search_sql = "SELECT COUNT(*) as count FROM wp_product_list WHERE active='1' AND `wp_product_list`.`visible`='1' AND (id LIKE '%".$keywords."%' OR name LIKE '%".$keywords."%' OR description LIKE '%".$keywords."%' OR additional_description LIKE '%".$keywords."%')";

					$items_count = $GLOBALS['wpdb']->get_results($search_sql,ARRAY_A);

					if (is_numeric($items_count[0]['count']))
					{
						$items_count = $items_count[0]['count'];
						// search request
						//$search_sql = "SELECT * FROM wp_product_list WHERE active='1' AND `wp_product_list`.`visible`='1' AND (name LIKE '%".$keywords."%' OR description LIKE '%".$keywords."%' OR additional_description LIKE '%".$keywords."%')"; 

						$search_sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`name` as brand FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands` WHERE `wp_product_list`.`active`='1' AND `wp_product_list`.`visible`='1' AND (LOWER(`wp_product_list`.`name`) LIKE '%".$keywords."%' OR LOWER(`wp_product_list`.`id`) LIKE '%".$keywords."%' OR LOWER(`wp_product_list`.`description`) LIKE '%".$keywords."%' OR LOWER(`wp_product_list`.`additional_description`) LIKE '%".$keywords."%') AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand`"; 
					}
					else
					{
						$items_count = 0;
						$search_sql ='';
					}
				}
				else 
					{
						$keywords = '';
					}

			 // FIRST PAGE OUTPUT
			 echo product_display_paginated($product_list, $group_type, $group_sql, $search_sql, $offset, $items_on_page);
			 
             // PAGINATION
             $offset = $offset + $items_on_page;
			 $page_id = $_GET['page_id'];

			 //pagination links

				$output = "<div id='pagination' class='width:470px;clear:both;'><br>";
				$output .= TXT_WPSC_TOTAL_ITEMS.": ".$items_count. "<br><br>";

				if($offset >= $items_on_page*2)
				{
					// echo "Previous page" link
					$offset_back = $offset-$items_on_page*2;
					$output .= "<a href='".get_option('siteurl')."?page_id=".$page_id."&brand=".$brandid."&category=".$catid."&offset=".$offset_back."&cs=".$keywords."'><< ".TXT_WPSC_PREV_PAGE."</a>&nbsp;";
				}
                $pagenum = 1;
                for ($i=0; $i<$items_count; $i=$i+$items_on_page)
                    {
                       $output .= " [<a href='".get_option('siteurl')."?page_id=".$page_id."&brand=".$brandid."&category=".$catid."&offset=".$i."&cs=".$keywords."'>".$pagenum ."</a>] ";
                       $pagenum++;
                    }
                
				if($offset < $items_count) 
				{
					// echo "Next page" link
					$output .= "<a href='".get_option('siteurl')."?page_id=".$page_id."&brand=".$brandid."&category=".$catid."&offset=".$offset."&cs=".$keywords."'>".TXT_WPSC_NEXT_PAGE." >></a>";
				}
				$output .= "</br></div>";

				echo "<div style='clear:both;'>".$output."<br></div>";
				
             }
         }
     }
    else
      {
      switch(get_option('show_categorybrands'))
        {
        case 1:
        $group_type = TXT_WPSC_CATEGORYORBRAND;
        break;

        case 2:
        $group_type = TXT_WPSC_CATEGORY;
        break;

        case 3:
        $group_type = TXT_WPSC_BRAND;
        break;
        }

      echo "<a name='products' ></a><strong class='prodtitles'>".TXT_WPSC_PLEASECHOOSEA." ".ucfirst($group_type)."</strong><br />";
   //   echo nzshpcrt_display_categories_groups();
      }
  ?>
