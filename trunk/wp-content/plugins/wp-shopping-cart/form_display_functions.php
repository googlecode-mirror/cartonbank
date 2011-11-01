<?php
function categorylist($product_id = '')
  {
  global $wpdb;
  $selected = '';
  $output = '';
  $values = $wpdb->get_results("SELECT * FROM `wp_product_categories` WHERE `active`='1' AND id <> '777' ORDER BY `id` ASC",ARRAY_A);
  foreach($values as $option)
    {
    if(is_numeric($product_id) && ($product_id > 0))
      {
          $sql = "SELECT id FROM `wp_item_category_associations` WHERE `product_id` IN('".$product_id."') AND `category_id` IN('".$option['id']."')  LIMIT 1";
          $sql = "SELECT category as id FROM `wp_product_list` WHERE `id` IN('".$product_id."') LIMIT 1";
          //SELECT category FROM `wp_product_list` WHERE `id` IN('13814')
      $image_category = $wpdb->get_row($sql,ARRAY_A); 
      if(is_numeric($image_category['id']) && ($image_category['id'] > 0) && $image_category['id']==$option['id'])
        {
			$selected = "checked='true'";
        }
		elseif ($option['id'] == get_option('default_category') && empty($image_category['id']))
	    {
			$selected = "checked='true'";
		}
      }
	
        if ($option['id']=='666')
        {
        // рабочий стол
        $classstyle = " style='background-color:#C9FCED;padding:2px;'";
        }
        elseif ($option['id']=='4')
        {
        // карикатура
        $classstyle = " style='background-color:#80D6F7;padding:2px;'";
        }
        elseif ($option['id']=='5')
        {
        // cartoon
        $classstyle = " style='background-color:#F3C1A3;padding:2px;'";
        }
        elseif ($option['id']=='11')
        {
        // разное
        $classstyle = " style='background-color:#FFC4E1;padding:2px;'";
        }
        else
        {
        $classstyle = '';
        }

    
    
    
    $output .= "<input type='radio' $selected name='category[]' value='".$option['id']."'><label $classstyle>".$option['name']."</label><br />";
    $selected = "";
    }
  return $output;
  }
?>