<?php
global $wpdb, $colorfilter, $color;

			//pokazh ($_POST);

	// Rabochy stol filter
	$_666 = '';

	if (isset($_GET['category']) && $_GET['category'] == '666')
		{
			$exclude_category_sql = " ";
			$approved_or_not = "";
		}
		else if ((isset($_POST['666']) && $_POST['666']=='on') or (isset($_GET['666']) && $_GET['666']==1))
		{
			// include in search results
			$exclude_category_sql = " ";
			$approved_or_not = "";
			$_666 = 'on';
		}
		else
		{
			// exclude from search results
			$exclude_category_sql = " AND `wp_product_list`.`category` != '666' ";
			$approved_or_not = " AND `wp_product_list`.`approved` = '1' ";
		}

// send email from feedback form
if (isset($_REQUEST['email']) && isset($_REQUEST['message']))
{
  $email = $_REQUEST['email'] ;
  $message = $_REQUEST['message'] ;
  $message = $message . ' <br /><br /> ' .$email;
  $headers = "From: ".$email."\r\n" .
			   'X-Mailer: PHP/' . phpversion() . "\r\n" .
			   "MIME-Version: 1.0\r\n" .
			   "Content-Type: text/html; charset=utf-8\r\n" .
			   "Content-Transfer-Encoding: 8bit\r\n\r\n";

    mail("cartoonbank.ru@gmail.com", 'Письмо от посетителя сайта cartoonbank.ru', $message, $headers);
	//mail("igor.aleshin@gmail.com", 'CC: Письмо от посетителя сайта cartoonbank.ru', $message, $headers);
}

$siteurl = get_option('siteurl');
$_SESSION['selected_country'] = '';
$brandid = '';
$_bigpictext = '';
$_bigpicimgalt = '';
$_bigpicstrip = '';
$_bigpic = '';
$_bottomstriptext = '';
$keywords = '';
$seperator ="?";
$portfolio = '';
$bio = '';
$cat_group_sql = '';
$filter_list = '';

// Portfolio filter
if (isset($_GET['portf']) && is_numeric($_GET['portf']) && $_GET['portf'] != '')
	switch ($_GET['portf'])
	{
		case 1:
			// portfolio is active
			$portfolio = 1;
			//$filter_list .= 'портфолио: да ';
			break;
		default:
			$portfolio = 0;
			break;
	}

// Bio filter
if (isset($_GET['bio']) && is_numeric($_GET['bio']) && $_GET['bio'] != '')
{
	switch ($_GET['bio'])
	{
		case 1:
			// show bio
			$bio = 1;
			//$filter_list .= 'биография: да ';
			break;
		default:
			$bio = 0;
			break;
	}
}
// Brand filter
if (isset($_GET['brand']) && is_numeric($_GET['brand']))
{
	$_brand = $_GET['brand'];
	$brand_group_sql = " AND `brand`='".$_brand."' ";
	}
	else
		{
			$_brand = '';
			$brand_group_sql = '';
		}
// Color filter
	$color = 'all';

	if((isset($_POST['color']) && $_POST['color']!= '') or (isset($_GET['color']) && $_GET['color']!= ''))
	{
		if(isset($_POST['color']))
		$color = $_POST['color'];

		if(isset($_GET['color']))
		$color = $_GET['color'];

		switch($color)
			{
			case 'color':
			$colorfilter = ' AND `wp_product_list`.`color`=1 '; 
			break;

			case 'bw':
			$colorfilter = ' AND `wp_product_list`.`color`=0 '; 
			break;

			default:
			$colorfilter = '';
			$color = 'all';
			break;
			}
	}
	else
		{
			$colorfilter = '';
		}

if ((isset($_GET['category']) && is_numeric($_GET['category'])) OR (isset($_POST['category']) && is_numeric($_POST['category'])))
{
	if(isset($_POST['category']) && $_POST['category']!= ''){
		$_category = strtolower(trim($_POST['category']));
	}
	if(isset($_GET['category']) && $_GET['category']!= ''){
		$_category = strtolower(trim($_GET['category']));
	}

	$cat_group_sql = " AND `wp_product_list`.`category`=".$_category;
	}
	else
	{
		$_category = '';
		$cat_group_sql = '';
		}

if(is_numeric($_brand) || (is_numeric(get_option('default_brand')) && (get_option('show_categorybrands') == 3)))
  {
  if(is_numeric($_brand))
    {
    $brandid = $_brand;
    }
    else
      {
      $brandid = get_option('default_brand');
      }
  
  $group_sql = "AND `brand`='".$brandid."'";

  $cat_sql = "SELECT * FROM `wp_product_brands` WHERE `id`='".$brandid."' LIMIT 1";
  $group_type = TXT_WPSC_BRANDNOCAP;
  }
  else if(is_numeric($_category) || (is_numeric(get_option('default_category')) && (get_option('show_categorybrands') != 3)))
    {
    if(is_numeric($_category))
      {
      $catid = $_category;
      }
      else
        {
  		$catid = '';
        }

		if ($catid==0)
		{
			$group_sql = "";
			$cat_sql = "SELECT * FROM `wp_product_categories`";
		}
		else
		{
			//$group_sql = "AND `wp_item_category_associations`.`category_id`='".$catid."'";
			$group_sql = "AND `wp_product_list`.`category`='".$catid."'";
			$cat_sql = "SELECT * FROM `wp_product_categories` WHERE `id`='".$catid."' LIMIT 1";
		}

	$group_type = TXT_WPSC_CATEGORYNOCAP;
    }
    else
      {
      $group_type = TXT_WPSC_BRANDNOCAP;
      }

$category_data = $GLOBALS['wpdb']->get_results($cat_sql,ARRAY_A);


if(isset($_GET['cart']) && $_GET['cart']== 'empty')
  {
  $_SESSION['nzshpcrt_cart'] = '';
  $_SESSION['nzshpcrt_cart'] = Array();
  }

/*
* this is now done by the ajax function
*/
if(isset($_POST['item']) && is_numeric($_POST['item']))
  {
  $cartcount = count($_SESSION['nzshpcrt_cart']);
  $_SESSION['nzshpcrt_cart'][$cartcount + 1] = $_POST['item'];
  }

  $num = 0;
  

			// how many records total?
			if ($_brand == '')
			{
				//$sql = "select option_value from wp_options where option_name = 'total_cartoons_to_show'";
				//$sql = "SELECT COUNT(*) as count FROM `wp_product_list`,`wp_item_category_associations` WHERE `wp_product_list`.`active`='1' ".$brand_group_sql.$cat_group_sql.$exclude_category_sql." ".$colorfilter.$approved_or_not." AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`brand` in (SELECT DISTINCT id FROM `wp_product_brands`) AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id`"; 

				$sql = "SELECT COUNT(id) as count FROM `wp_product_list` WHERE `wp_product_list`.`active`='1' ".$brand_group_sql.$cat_group_sql.$exclude_category_sql." ".$colorfilter.$approved_or_not." AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`brand` in (SELECT DISTINCT id FROM `wp_product_brands`)"; 
			}
			else
			{
				//$sql = "SELECT COUNT(*) as count FROM `wp_product_list`,`wp_item_category_associations` WHERE `wp_product_list`.`active`='1' ".$brand_group_sql.$cat_group_sql.$exclude_category_sql." ".$colorfilter.$approved_or_not." AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`brand` in (SELECT DISTINCT id FROM `wp_product_brands`) AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id`"; 

				$sql = "SELECT COUNT(id) as count FROM `wp_product_list` WHERE `wp_product_list`.`active`='1' ".$brand_group_sql.$cat_group_sql.$exclude_category_sql." ".$colorfilter.$approved_or_not." AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`brand` in (SELECT DISTINCT id FROM `wp_product_brands`)"; 
			}

			$items_count = $GLOBALS['wpdb']->get_results($sql,ARRAY_A);

			//pokazh($items_count,"items_count1");

			if (isset($items_count[0]['count']) && is_numeric($items_count[0]['count']))
			{
				$items_count = $items_count[0]['count'];
			}
			else
			{
				$items_count = 0;
			}
			
			$search_sql = NULL;
			
			if (isset($_GET['offset']) && is_numeric($_GET['offset']))
				$_product_start_num = $_GET['offset'];
			else
				$_product_start_num = 0;

if (isset($_GET['category']) && $_GET['category'] == '777')
		{
			// category is Tema Dnya - 777

			//$sql = "SELECT  `wp_product_list` . * ,  `wp_product_files`.`width` ,  `wp_product_files`.`height` ,  `wp_product_brands`.`id` AS brandid,  `wp_product_brands`.`name` AS brand, `wp_item_category_associations`.`category_id` ,  `wp_product_categories`.`name` AS kategoria, `tema_dnya`.`datetime` FROM  `wp_product_list` LEFT JOIN `wp_item_category_associations` ON `wp_product_list`.`id` =  `wp_item_category_associations`.`product_id`  LEFT JOIN `wp_product_files` ON `wp_product_list`.`file` =  `wp_product_files`.`id` LEFT JOIN `wp_product_brands` ON `wp_product_brands`.`id` =  `wp_product_list`.`brand` LEFT JOIN `wp_product_categories` ON `wp_item_category_associations`.`category_id` =  `wp_product_categories`.`id` LEFT JOIN `tema_dnya` ON wp_product_list.id =  tema_dnya.id  WHERE   `wp_product_list`.`active` =  '1' AND  `wp_product_list`.`approved` =  '1' AND  `wp_product_list`.`visible` =  '1' AND  `wp_item_category_associations`.`category_id` =  '777' AND `tema_dnya`.`datetime` <= '".date("Y.m.d")."' AND  `wp_product_categories`.`id` =777 ORDER BY  tema_dnya.datetime DESC, `wp_product_list`.`id` DESC LIMIT 0 , 20";

			//$sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1'  AND `wp_item_category_associations`.`category_id` != '666'  AND `wp_product_list`.`approved` = '1'  AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` AND `wp_item_category_associations`.`category_id`='777'  AND `wp_product_categories`.`id`=777 AND `wp_product_list`.`tema_dnya_approved` = 1 ORDER BY `wp_product_list`.`id` DESC LIMIT 0,20";

			// union to show tema_dnya first
			$sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria 
			FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands`, `wp_product_categories`
			WHERE `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` 
			AND `wp_product_list`.`file` = `wp_product_files`.`id` 
			AND `wp_product_list`.`tema_dnya_approved` = '1'  
			AND `wp_product_brands`.`id` = `wp_product_list`.`brand` 
			AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` 
			AND  wp_product_list.id = (select id from tema_dnya where DATETIME = DATE( NOW( ) ) ) LIMIT 1
			UNION
			SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`id` as brandid, `wp_product_brands`.`name` as brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria 
			FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands`, `wp_product_categories`
			WHERE `wp_product_list`.`active`='1'  
			AND `wp_item_category_associations`.`category_id` != '666'  
			AND `wp_product_list`.`approved` = '1'  
			AND `wp_product_list`.`tema_dnya_approved` = '1'  
			AND `wp_product_list`.`visible`='1' 
			AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` 
			AND `wp_product_list`.`file` = `wp_product_files`.`id` 
			AND `wp_product_brands`.`id` = `wp_product_list`.`brand` 
			AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id` 
			AND `wp_item_category_associations`.`category_id`='777'  
			AND `wp_product_categories`.`id`=777 
			and wp_product_list.id not in (SELECT id FROM tema_dnya WHERE DATETIME = DATE( NOW( ) ) )";

					//pokazh($sql);
		}
else
		{
			// Category is not 777 - ( not tema dnya)

			//$sql = "SELECT `wp_product_list` . * , `wp_product_files`.`width` , `wp_product_files`.`height` , `wp_product_brands`.`id` AS brandid, `wp_product_brands`.`name` AS brand, `wp_item_category_associations`.`category_id`, `wp_product_categories`.`name` as kategoria FROM `wp_product_list` , `wp_item_category_associations` , `wp_product_files` , `wp_product_brands` , `wp_product_categories` WHERE `wp_product_list`.`active` = '1' ".$cat_group_sql.$exclude_category_sql.$colorfilter.$approved_or_not." AND `wp_product_list`.`visible` = '1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id`  $group_sql ORDER BY `wp_product_list`.`id` desc LIMIT ".$_product_start_num.", 1"; 

			$sql = "SELECT `wp_product_list` . * , `wp_product_files`.`width` , `wp_product_files`.`height` , `wp_product_brands`.`id` AS brandid, `wp_product_brands`.`name` AS brand, `wp_product_list`.`category` as category_id, `wp_product_categories`.`name` as kategoria FROM `wp_product_list` , `wp_product_files` , `wp_product_brands` , `wp_product_categories` WHERE `wp_product_list`.`active` = '1' ".$cat_group_sql.$exclude_category_sql.$colorfilter.$approved_or_not." AND `wp_product_list`.`visible` = '1' AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_product_list`.`category` = `wp_product_categories`.`id`  $group_sql ORDER BY `wp_product_list`.`id` desc LIMIT ".$_product_start_num.", 1"; 
		}


                if (isset($_GET['offset']) && is_numeric($_GET['offset']))
                 {
                    $offset = $_GET['offset'];
                 }
                else 
                 {
                    $offset = 0;
                 }

                $items_on_page = get_option('posts_per_page');
    
    
     // SEARCH


			// Any search word match

				$any_keywords = '';
				$any_keywords_filter = '';

				if((isset($_POST['cs_any']) && $_POST['cs_any']!= '') or (isset($_GET['cs_any']) && $_GET['cs_any']!= ''))
                {
					if(isset($_POST['cs_any']) && $_POST['cs_any']!= ''){
                        $any_keywords = strtolower(trim($_POST['cs_any']));
                    }
                    if(isset($_GET['cs_any']) && $_GET['cs_any']!= ''){
                        $any_keywords = strtolower(trim($_GET['cs_any']));
                    }



								$aKeywords = split(" ",$any_keywords);

								// trim spaces in array
								array_walk($aKeywords, 'trim_value');

								// if more than one search word
								if (count($aKeywords) > 1)
									{
										$any_keywords_filter = " AND (";
										foreach ($aKeywords as $key => $value)
										{
											$any_keywords_filter .= "(`wp_product_list`.`id` LIKE '%".$value."%' OR `wp_product_list`.`name` LIKE '%".$value."%' OR `wp_product_list`.`description` LIKE '%".$value."%' OR `wp_product_list`.`additional_description` LIKE '%".$value."%') OR ";
										}

										// remove extra chars from right side
										$any_keywords_filter = substr($any_keywords_filter, 0, -4);
										$any_keywords_filter .= ")";
									}
									else if ($any_keywords!='')
									{
										$any_keywords_filter = " AND (`wp_product_list`.`id` LIKE '%".$any_keywords."%' OR `wp_product_list`.`name` LIKE '%".$any_keywords."%' OR `wp_product_list`.`description` LIKE '%".$any_keywords."%' OR `wp_product_list`.`additional_description` LIKE '%".$any_keywords."%')";
									}

				}

			// Exclude keywords match
				
				$exclude_keywords = '';
				$exclude_keywords_filter = '';

				if((isset($_POST['cs_exclude']) && $_POST['cs_exclude']!= '') or (isset($_GET['cs_exclude']) && $_GET['cs_exclude']!= ''))
                {
					if(isset($_POST['cs_exclude']) && $_POST['cs_exclude']!= ''){
                        $exclude_keywords = strtolower(trim($_POST['cs_exclude']));
                    }
                    if(isset($_GET['cs_exclude']) && $_GET['cs_exclude']!= ''){
                        $exclude_keywords = strtolower(trim($_GET['cs_exclude']));
                    }
					$exclude_keywords_filter = " AND (`wp_product_list`.`id` NOT LIKE '%".$exclude_keywords."%' OR `wp_product_list`.`name` NOT LIKE '%".$exclude_keywords."%' OR `wp_product_list`.`description` NOT LIKE '%".$exclude_keywords."%' OR `wp_product_list`.`additional_description` NOT LIKE '%".$exclude_keywords."%')";
				}


			// Exact keywords match
				
				$exact_keywords = '';
				$exact_keywords_filter = '';

				if((isset($_POST['cs_exact']) && $_POST['cs_exact']!= '') or (isset($_GET['cs_exact']) && $_GET['cs_exact']!= ''))
                {
					if(isset($_POST['cs_exact']) && $_POST['cs_exact']!= ''){
                        $exact_keywords = strtolower(trim($_POST['cs_exact']));
                    }
                    if(isset($_GET['cs_exact']) && $_GET['cs_exact']!= ''){
                        $exact_keywords = strtolower(trim($_GET['cs_exact']));
                    }
					$exact_keywords_filter = " AND (`wp_product_list`.`id` LIKE '%".$exact_keywords."%' OR `wp_product_list`.`name` LIKE '%".$exact_keywords."%' OR `wp_product_list`.`description` LIKE '%".$exact_keywords."%' OR `wp_product_list`.`additional_description` LIKE '%".$exact_keywords."%')";
				}

			// All words search match

				$keywords = '';
				$search_keywords_filter = '';

                if((isset($_POST['cs']) && $_POST['cs']!= '') or (isset($_GET['cs']) && $_GET['cs']!= '') or (isset($_POST['cs_exact']) && $_POST['cs_exact']!= '') or (isset($_GET['cs_exact']) && $_GET['cs_exact']!= '') or (isset($_POST['cs_any']) && $_POST['cs_any']!= '') or (isset($_GET['cs_any']) && $_GET['cs_any']!= ''))
                {
					if(isset($_POST['cs']) && $_POST['cs']!= ''){
                        $keywords = strtolower(trim($_POST['cs']));
                    }
                    if(isset($_GET['cs']) && $_GET['cs']!= ''){
                        $keywords = strtolower(trim($_GET['cs']));
                    }

						// MULTIPLE KEYWORDS SEARCH

								// make array of keywords
								$aKeywords = split(" ",$keywords);

								// trim spaces in array
								array_walk($aKeywords, 'trim_value');

								// if more than one search word
								if (count($aKeywords) > 1)
									{
										$search_keywords_filter = " AND (";
										foreach ($aKeywords as $key => $value)
										{
											$search_keywords_filter .= "(`wp_product_list`.`id` LIKE '%".$value."%' OR `wp_product_list`.`name` LIKE '%".$value."%' OR `wp_product_list`.`description` LIKE '%".$value."%' OR `wp_product_list`.`additional_description` LIKE '%".$value."%') AND ";
										}

										// remove extra chars from right side
										$search_keywords_filter = substr($search_keywords_filter, 0, -5);
										$search_keywords_filter .= ")";
									}
									else if ($keywords!='')
									{
										$search_keywords_filter = " AND (`wp_product_list`.`id` LIKE '%".$keywords."%' OR `wp_product_list`.`name` LIKE '%".$keywords."%' OR `wp_product_list`.`description` LIKE '%".$keywords."%' OR `wp_product_list`.`additional_description` LIKE '%".$keywords."%')";
									}

									$search_keywords_filter = $exact_keywords_filter.$search_keywords_filter.$any_keywords_filter.$exclude_keywords_filter;

												//pokazh($search_keywords_filter,"search_keywords_filter ");

									// add brand to search
									if (isset($_POST['brand']) && is_numeric($_POST['brand']))
									{
										$search_keywords_filter .=  " AND `wp_product_list`.`brand`= ".$_POST['brand'];
									}

									if (isset($_GET['brand']) && is_numeric($_GET['brand']))
									{
										$search_keywords_filter .=  " AND `wp_product_list`.`brand`= ".$_GET['brand'];
									}

					$filter_list .= 'Поиск: ('.$keywords.") ";
                    // search request
                    // count found results
					if (isset($_brand) && isset($_brand)!='')
					{
						
						$search_sql = "SELECT COUNT(*) as count FROM wp_product_list WHERE active='1' " . $cat_group_sql . $brand_group_sql . $approved_or_not . " AND `wp_product_list`.`visible`='1' " . $colorfilter . $search_keywords_filter;
					}
                    else
					{
						$search_sql = "SELECT COUNT(*) as count FROM wp_product_list WHERE active='1' AND `wp_product_list`.`visible`='1' " . $colorfilter . $approved_or_not . $search_keywords_filter;
					}

                    $items_count = $GLOBALS['wpdb']->get_results($search_sql,ARRAY_A);

					if (isset($items_count[0]['count']) && is_numeric($items_count[0]['count']))
                    {
                        $items_count = $items_count[0]['count'];
                        // search request
   					
						$search_sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`name` as brand, `wp_product_brands`.`id` as brandid, `wp_product_categories`.`name` as kategoria, `wp_product_list`.`category` as category_id FROM `wp_product_list`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`active`='1' " . $cat_group_sql . $exclude_category_sql . $colorfilter . $approved_or_not . " AND `wp_product_list`.`visible`='1' ".$search_keywords_filter." AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_product_list`.`category` = `wp_product_categories`.`id`  ORDER BY `wp_product_list`.`id` DESC LIMIT ".$offset.",".$items_on_page; 

                    }
                    else
                    {
                        $items_count = 0;
                        $search_sql ='';
                    }
                $sql = $search_sql;


												//pokazh($sql);

				} // if((isset($_POST['cs']) && $_POST['cs']!= '') or (isset($_GET['cs']) && $_GET['cs']!= ''))

		//Search end
                else 
				{
					$keywords = '';
				}

												//pokazh($items_count,"items_count");


	// we inject here direct link to the image
	// $_GET['cartoonid'] : &cartoonid=666
	if(isset($_GET['cartoonid']) && is_numeric($_GET['cartoonid']) && $_GET['cartoonid']!='' )
	{
		//echo("<pre>_cartoon_id ".print_r($_GET['cartoonid'],true)."</pre>");
		$_cartoon_id = $_GET['cartoonid'];
		
		//$search_sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`name` as brand, `wp_product_categories`.`name` as kategoria, `wp_item_category_associations`.`category_id`, `wp_product_brands`.`id` as brandid FROM `wp_product_list`,`wp_item_category_associations`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`id` = '".$_cartoon_id."' AND `wp_product_list`.`active`='1' ".$approved_or_not." AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`id` = `wp_item_category_associations`.`product_id` AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_item_category_associations`.`category_id` = `wp_product_categories`.`id`  ORDER BY `wp_product_list`.`id` DESC "; 

		$search_sql = "SELECT `wp_product_list`.*, `wp_product_files`.`width`, `wp_product_files`.`height`, `wp_product_brands`.`name` as brand, `wp_product_categories`.`name` as kategoria, `wp_product_list`.`category` as category_id, `wp_product_brands`.`id` as brandid FROM `wp_product_list`, `wp_product_files`, `wp_product_brands`, `wp_product_categories` WHERE `wp_product_list`.`id` = '".$_cartoon_id."' AND `wp_product_list`.`active`='1' ".$approved_or_not." AND `wp_product_list`.`visible`='1' AND `wp_product_list`.`file` = `wp_product_files`.`id` AND `wp_product_brands`.`id` = `wp_product_list`.`brand` AND `wp_product_list`.`category` = `wp_product_categories`.`id`  ORDER BY `wp_product_list`.`id` DESC "; 

	    $sql = $search_sql;
    }

	
	// список картинок
    $product = $GLOBALS['wpdb']->get_results($sql,ARRAY_A);


     if ($product!=null)
     {   
		 if ($bio == 1 && $brandid > 0) // bio
		 {
		  // display portfolio
			 
			 // prepare content
				
				// Get the Brand (author) data
				$brand_sql = "SELECT * FROM `wp_product_brands` where id = ". $brandid;
				$brand_result  = $GLOBALS['wpdb']->get_results($brand_sql,ARRAY_A);

				// bio

				if (isset($brand_result[0]['bio_post_id']))
				{
				$bio_sql = "SELECT `post_content` FROM `wp_posts` WHERE id = ".$brand_result[0]['bio_post_id'] ; // todo: use page ID!
				$bio = $GLOBALS['wpdb']->get_results($bio_sql,ARRAY_A);
				}
				
				if (isset($bio[0]) ) 
				{
					$bio = $bio[0]['post_content'];
				}
				else 
					{
						$bio = 'Автор исключительно талантливый художник. Больше нам про него пока ничего не известно.<br />Ведём бесконечные переговоры об обновлении этой информации.';
					}

				// email form
				$email_form = "<div id='emailform' style='display:none;text-align:right;'><form method='post' action='".get_option('siteurl')."/?page_id=29&brand=".$brandid."&bio=1'>
								  Email для обратной связи: <input name='email' type='text' style='width:400px;'/><br /><br />
								  <textarea style='width:600px;' name='message' rows='15' cols='40'>Уважаемый ".$product[0]['brand']."!\n\n</textarea><br />
								  <input type='submit' value='Отправить письмо' class='borders'/>
								</form></div>";
				// contact
				$brand_contact = "<a href='#pt' onclick=\"document.getElementById('emailform').style.display='block';document.getElementById('bio').style.display='none';return false;\">Написать письмо</a>";

				$_bigpicstrip = "<b>".$product[0]['brand']. ". Информация об авторе</b>";
				$_bigpictext = "<br /><br />".$brand_contact;
				$_bigpic = "<div style='width:600px;'><div id='bio' style='display:block;'>".$bio."</div></div>"; 
				$_bottomstriptext = "".$email_form;

			// end of portfolio
		 } 
		 else
		 {
			// normal workflow: disply big preview image

			// slide preview preparations:
				if(stristr($product[0]['image'], 'jpg') != FALSE) {
						$_file_format = 'jpg';
					} 
					if(stristr($product[0]['image'], 'gif') != FALSE) {
						$_file_format = 'gif'; 
					} 
					if(stristr($product[0]['image'], 'png') != FALSE) {
						$_file_format = 'png';
					} 
				$_number = $product[0]['id'];
                $_description = nl2br(stripslashes($product[0]['description']));
                $_size = $product[0]['width']."px X ".$product[0]['height']."px;";
				$_x_sm = round(($product[0]['width']/300)*2.54, 1);
				$_y_sm = round(($product[0]['height']/300)*2.54, 1);

				$_size_warning = '';
				if ($product[0]['height']<800 || $product[0]['width']<800)
					$_size_warning = "<div style='float:left;width:288px;padding-top:8px;font-size:0.8em;'><a style='color:red;' href='".get_option('siteurl')."/?page_id=771'>Внимание! Размеры файла<br />ограничивают применение!</a></div>";

				$_sizesm = $_x_sm." см X ".$_y_sm." см";
                $_author = $product[0]['brand'];
                $_name = nl2br(stripslashes($product[0]['name']));
				if(isset($_GET['brand']) && is_numeric($_GET['brand'])) 
						$filter_list .= "Автор: <b>".$_author."</b> ";
				if (isset($product[0]['kategoria']))
				 {
					$_categor = $product[0]['kategoria'];
					if(isset($_GET['category']) && is_numeric($_GET['category'])) 
						$filter_list .= "Категория: <b>".$_categor."</b> ";
				 }
				 else
				 {
					$_categor = '';
				 }
				if (isset($product[0]['brandid']))
				 {
					$_brandid = $product[0]['brandid'];
				 }
				 else
				 {
					$_brandid = '';
				 }

				$_category = "<a href=\'".get_option('product_list_url')."&category=".$product[0]['category_id']."\'>".$_categor."</a>";


                $_tags = nl2br(stripslashes($product[0]['additional_description']));

				$_bigpicimgalt = addslashes("Карикатура. ".$_name.". ".$_description.". ".$_tags);


				$_tags_array = explode(',',$_tags);
                    //$i=0;
                    foreach ($_tags_array as $key => $value)
                    {
                        $_tags_array[$key] = "<a href=\"".get_option('siteurl')."/?page_id=29&cs=".trim($_tags_array[$key])."\">".trim($_tags_array[$key])."</a>";
                    }
                $_tags_imploded = implode(", ", $_tags_array);
                $_tags = $_tags_imploded;

				$_rating_html = "<div id='star_rating'><img src='".get_option('siteurl')."/img/ldng.gif'></div>";

				$_sharethis_html = "<div id='share_this' style='color:#51779b;line-height:200%;'></div>";

					
				if (current_user_can('manage_options'))
				{
					$_edid = " <a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-items.php&edid=".$_number."' target=_blank'><img border=0 src='".get_option('siteurl')."/img/edit.jpg' title='редактировать'></a> <a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-shopping-cart/display-items.php&updateimage=".$_number."' target=_blank'><img border=0 src='".get_option('siteurl')."/img/reload.gif' title='обновить водяной знак'></a> <a href=".get_option('siteurl')."/ales/wordassociations/words.php?id=".$_number." target=_blank><img border=0 src=".get_option('siteurl')."/img/tags.gif title='добавить тэгов'></a>";
				}
				else
				{
					$_edid = "";
				}

				$_bigpicstrip = "<div style=\"float:left;\"><b><a href='".get_option('siteurl')."/?page_id=278' target=_blank title='объяснение'>Название:</a> </b>" .$_name."</div> "."<div >№&nbsp;<a title='уникальный адрес страницы с этим изображением' id='cuid' href='".get_option('siteurl')."/?page_id=29&cartoonid=".$_number."'>".$_number."</a>&nbsp;<b><a href=\"".$siteurl."/?page_id=29&brand=".$_brandid."\">".$_author."</a></b></div>";
                $_bigpictext = "<b><a href='".get_option('siteurl')."/?page_id=280' target=_blank title='объяснение'>Категория:</a> </b><br />".$_category."<br /><br /><b><a href='".get_option('siteurl')."/?page_id=278' target=_blank title='объяснение'>Описание:</a> </b> ".$_description."<br /><br /><b><a href='".get_option('siteurl')."/?page_id=284' target=_blank title='объяснение'>Тэги:</a> </b><br />".$_tags."<br /><br /><b><a href='".get_option('siteurl')."/?page_id=735' target=_blank title='объяснение'>Ссылка:</a></b> <a title='уникальный адрес страницы с этим изображением' href='".get_option('siteurl')."/?page_id=29&cartoonid=".$_number."'>№&nbsp;".$_number."</a><br /><br /><b><a href='".get_option('siteurl')."/?page_id=727' target=_blank title='объяснение'>Размер:</a></b><br />".$_size."<br /><span style='color:#ACACAC;font-size:0.875em;'>при печати 300dpi:<br />".$_sizesm."</span><br /><br /><b><a href='".get_option('siteurl')."/?page_id=708' target=_blank title='объяснение'>Формат:</a> </b> ".$_file_format."<br /><br /><b><a href='".get_option('siteurl')."/?page_id=745' target=_blank title='объяснение'>Оценка:</a></b><br />".$_rating_html.$_sharethis_html.$_edid;

                $siteurl = get_option('siteurl');
                $_bigpic =  "<img src='".$siteurl."/wp-content/plugins/wp-shopping-cart/product_images/".$product[0]['image']."' border=0  alt='".$_bigpicimgalt."'>";

				if($product[0]['l1_price']=='0') {$l1_disabled = 'disabled=true';} else {$l1_disabled = '';}
				if($product[0]['l2_price']=='0') {$l2_disabled = 'disabled=true';} else {$l2_disabled = '';}
				if($product[0]['l3_price']=='0') {$l3_disabled = 'disabled=true';} else {$l3_disabled = '';}
				
				if (isset($product[0]['not_for_sale']) && $product[0]['not_for_sale']=='1')
				{
					$_bottomstriptext = "Продажа лицензий на данное изображение не разрешена автором";
				}
				else
				{
				$_bottomstriptext = $_size_warning."<div style='width:450px;float:right;'><form name='licenses' id='licenses' onsubmit='submitform(this);return false;' action='".get_option('siteurl')."/?page_id=29' method='POST'><table class='licenses'>
					  <tr>
						<td class='wh' style='width:80px;vertical-align:bottom;'><b>Выбор</b></td>
						<td class='wh' style='text-align:left;'><input type='radio' name='license' $l1_disabled value='l1_price'></td>
						<td style='vertical-align:middle;text-align:right;'><b>".round($product[0]['l1_price'])."&nbsp;руб.</b></td>
						<td rowspan='2' style='width:20px;'>&nbsp;</td>
						<td class='wh' style='text-align:left;'><input type='radio' name='license' $l2_disabled value='l2_price'></td>
						<td style='vertical-align:middle;text-align:right;'><b>".round($product[0]['l2_price'])."&nbsp;руб.</b></td>
						<td rowspan='2' style='width:20px;'>&nbsp;</td>
						<td class='wh' style='text-align:left;'><input type='radio' name='license' $l3_disabled value='l3_price'></td>
						<td style='vertical-align:middle;text-align:right;'><b>".round($product[0]['l3_price'])."&nbsp;руб.</b></td>
						<td rowspan='2' class='wh' style='width:80px; text-align:right; vertical-align:bottom;'><input id='tocart' value='В заказ' type='submit' class='borders'></td>
					  </tr>
					  <tr>
						<td class='wh' style='vertical-align:top;'><b>лицензии:</b></td>
						<td colspan='2' style='padding-left:6px;'><a target='_blank' href='".get_option('siteurl')."/?page_id=238' title='подробнее об ограниченной лицензии'>ограниченная</a></td>
						<td colspan='2' style='padding-left:6px;'><a target='_blank' href='".get_option('siteurl')."/?page_id=242' title='подробнее о стандартной лицензии'>стандартная</a></td>
						<td colspan='2' style='padding-left:6px;'><a target='_blank' href='".get_option('siteurl')."/?page_id=245' title='подробнее об расширенной лицензии'>расширенная</a></td>
					  </tr>
					  </table><input type='hidden' value='".$_number."' name='prodid'>  </form></div>";
				}
		  // end of normal workflow: disply big preview image
		 }
		 
	 }
		else
		{// no products
			if(isset($_GET['cartoonid']) && is_numeric($_GET['cartoonid']) && $_GET['cartoonid']!='' )
              {
                  echo ("<br /><br />Изображения с таким номером нет.");
              }
			
		}
			
			 // ales: product page starts here
				// old code to call all items at once
				// echo product_display_default($product_list, $group_type, $group_sql, $search_sql);
                // placeholder for the slide preview
               
               $_bigpictext = str_ireplace("\\'","\"",$_bigpictext);
                
               echo "<div id='bigpictopstrip'>".$_bigpicstrip."</div>";
               echo "<div id='bigpictext'>".$_bigpictext."</div>";
               echo "<div id='bigpic'><a href='#pt' onclick=\"get_item1();\">".$_bigpic."</a></div>";
				//jQuery(this).attr('id')
			   //<div id="right"><a href="#"  onclick="var next=document.getElementById('image1').innerHTML;document.getElementById('right').innerHTML = next; "><div style='height:200px;width:200px;background color:#CCFF66;'>main image </div></a></div>
               echo "<div style='clear:both;'></div>";
               echo "<div id='bigpicbottomstrip' style='float:right;margin-bottom:6px;'>".$_bottomstriptext."</div>";
                    
				if (isset($_GET['offset']) && is_numeric($_GET['offset']))
				 {
					$offset = $_GET['offset'];
				 }
				else 
				 {
					$offset = 0;
				 }

				$items_on_page = get_option('posts_per_page');

                // непонятно зачем эти параметры?
                $product_list = '';

		        if((isset($_POST['cs']) && $_POST['cs']!= '') or (isset($_GET['cs']) && $_GET['cs']!= '') or (isset($_POST['cs_exact']) && $_POST['cs_exact']!= '') or (isset($_GET['cs_exact']) && $_GET['cs_exact']!= '') or (isset($_POST['cs_any']) && $_POST['cs_any']!= '') or (isset($_GET['cs_any']) && $_GET['cs_any']!= ''))

                     {$search_sql = $sql;}
				else
					 {$search_sql = '';}

									//pokazh($search_sql);
					 
// FIRST PAGE icons OUTPUT


 // PAGINATION
 $offset = $offset;// + $items_on_page;
 $page_id = $_GET['page_id'];

	$page = round($offset/$items_on_page)+1;
	$totalitems = $items_count;
	$limit = $items_on_page;
		if (isset($_GET['category'])&&is_numeric($_GET['category'])) {$catid=$_GET['category'];}else{$catid='';}

	// Brand filter
	if (isset($_GET['brand']) && is_numeric($_GET['brand']))
	{
		$_brand = $_GET['brand'];
		$brand_group_sql = "&brand=".$_brand;
	}
	elseif (isset($_POST['brand']) && is_numeric($_POST['brand']))
	{
		$_brand = $_POST['brand'];
		$brand_group_sql = "&brand=".$_brand;
	}
	else
	{
		$_brand = '';
		$brand_group_sql = '';
	}

if ($keywords!='') {$_url_cs="&cs=".$keywords;}else{$_url_cs='';}
if ($exact_keywords!='') {$_url_cs_exact="&cs_exact=".$exact_keywords;}else{$_url_cs_exact='';}
if ($any_keywords!='') {$_url_cs_any="&cs_any=".$any_keywords;}else{$_url_cs_any='';}
if ($_666!='') {$_url_666="&666=on";}else{$_url_666='';}
if ($exclude_keywords!='') {$_url_cs_exclude="&cs_exclude=".$exclude_keywords;}else{$_url_cs_exclude='';}

	$_pages_navigation = getPaginationString($page, $totalitems, $limit, $adjacents = 1, $targetpage = get_option('siteurl'), $pagestring = "?page_id=29".$brand_group_sql."&color=".$color."&category=".$catid.$_url_cs.$_url_cs_exact.$_url_cs_any.$_url_cs_exclude.$_url_666."&offset=",$filter_list);
		

	  echo "<div style='clear:both;'>".$_pages_navigation."</div>";

	  
	  echo product_display_paginated(NULL /* generated notice: always NULL $product_list*/, $group_type, $group_sql, $search_sql, $offset, $items_on_page);


function getPaginationString($page = 1, $totalitems, $limit = 20, $adjacents = 1, $targetpage = "/", $pagestring = "?page=", $filter_list = '')
{		
	//function to return the pagination string
	//getPaginationString($page = 1, $totalitems, $limit = 20, $adjacents = 1, $targetpage = get_option('siteurl'), $pagestring = "?brand=".$brandid."&category=".$catid."&offset=".$offset."&cs=".$keywords."&page_id=29");


	//defaults
	if(!$adjacents) $adjacents = 1;
	if(!$limit) $limit = 20;
	if(!$page) $page = 1;
	if(!$targetpage) $targetpage = "/";
	
	//other vars
	$prev = $page - 1;									//previous page is page - 1
	$next = $page + 1;									//next page is page + 1
	$lastpage = ceil($totalitems / $limit);				//lastpage is = total items / items per page, rounded up.
	$lpm1 = $lastpage - 1;								//last page minus 1
	
	/* 
		Now we apply our rules and draw the pagination object. 
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	$margin = '0px';
	$padding = '0px';
	if($lastpage > 1)
	{	
		$pagination .= "<div class='pagination' style='padding-top:4px;'>";

		//previous button
		if ($page > 1) 
			$pagination .= "<a href=\"".$targetpage. $pagestring. ($prev*$limit - $limit). "\">« сюда</a>";
		else
			$pagination .= "<span class=\"disabled\">« сюда</span>";	
		
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination .= "<span class=\"current\">$counter</span>";
				else
					$pagination .= "<a href=\"" . $targetpage . $pagestring . ($counter*$limit - $limit) . "\">$counter</a>";					
			}
		}
		elseif($lastpage >= 7 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 1 + ($adjacents * 3))		
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination .= "<span class=\"current\">$counter</span>";
					else
						$pagination .= "<a href=\"" . $targetpage . $pagestring . ($counter*$limit - $limit) . "\">$counter</a>";					
				}
				$pagination .= "<span class=\"elipses\">...</span>";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . ($lpm1*$limit - $limit) . "\">$lpm1</a>";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . ($lastpage*$limit - $limit) . "\">$lastpage</a>";		
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination .= "<a href=\"" . $targetpage . $pagestring . "0\">1</a>";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . "20\">2</a>";
				$pagination .= "<span class=\"elipses\">...</span>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination .= "<span class=\"current\">$counter</span>";
					else
						$pagination .= "<a href=\"" . $targetpage . $pagestring . ($counter*$limit - $limit) . "\">$counter</a>";					
				}
				$pagination .= "...";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . ($lpm1*$limit - $limit) . "\">$lpm1</a>";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . ($lastpage*$limit - $limit) . "\">$lastpage</a>";		
			}
			//close to end; only hide early pages
			else
			{
				$pagination .= "<a href=\"" . $targetpage . $pagestring . "0\">1</a>";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . "20\">2</a>";
				$pagination .= "<span class=\"elipses\">...</span>";
				for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination .= "<span class=\"current\">$counter</span>";
					else
						$pagination .= "<a href=\"" . $targetpage . $pagestring . ($counter*$limit - $limit) . "\">$counter</a>";					
				}
			}
		}
		
		//next button
		if ($page < $counter - 1) 
			$pagination .= "<a href=\"" . $targetpage . $pagestring . ($next*$limit - $limit) . "\">туда »</a>";
		else
			$pagination .= "<span class=\"disabled\">туда »</span>";
		if ($filter_list=='')
			$pagination .= " Всего найдено изображений: ".$totalitems. "</div>";
		else
			$pagination .= " Всего найдено изображений: ".$totalitems. "&nbsp;<b>Фильтр</b>: <span style='color:#c0c0c0;font-size:0.8em;'>".$filter_list."</span></div>";
	}
	
	return $pagination;

}

function trim_value(&$value) 
{ 
    $value = trim($value); 
}
?>
<script type="text/javascript">
on_start();
get_5stars();
get_share_this();

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
jQuery("#share_this").html('<b>Поделиться:</b><br /><a href="#" onclick="cuid=document.getElementById(\'cuid\').innerHTML; uu=\'http://twitter.com/share?url=\' + escape(\'http://cartoonbank.ru/?page_id=29&cartoonid=\'); window.open(uu+cuid);"><img src="img/s_twitter.png" border="0"></a>&nbsp;<a href="#" onclick="cuid=document.getElementById(\'cuid\').innerHTML; uu=\'http://www.facebook.com/sharer.php?t=cartoonbank.ru&u=\'+escape(\'http://cartoonbank.ru/?page_id=29&cartoonid=\'); window.open(uu+cuid);"><img src="img/s_facebook.png" border="0"></a>&nbsp;<a href="#" onclick="cuid=document.getElementById(\'cuid\').innerHTML; uu=\'http://vkontakte.ru/share.php?title=cartoonbank.ru&url=\'+escape(\'http://cartoonbank.ru/?page_id=29&cartoonid=\'); window.open(uu+cuid);"><img src="img/s_vkontakte.png" border="0"></a>&nbsp;<a href="#" onclick="cuid=document.getElementById(\'cuid\').innerHTML; uu=\'http://www.livejournal.com/update.bml?subject=cartoonbank.ru&event=\'+escape(\'http://cartoonbank.ru/?page_id=29&cartoonid=\'); window.open(uu+cuid);"><img src="img/s_livejournal.png" border="0"></a>');
});
}
function change_url() { jQuery(document).ready(function() { function locationHashChanged() { if (location.hash === "#pt" || location.hash === "#") { var cuid = document.getElementById('cuid').innerHTML; window.location.hash = '&cartoonid='+cuid; } } window.onhashchange = locationHashChanged; });
}

</script>
