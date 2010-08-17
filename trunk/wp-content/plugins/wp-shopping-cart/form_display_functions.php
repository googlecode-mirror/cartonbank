<?php
function categorylist($product_id = '')
  {
  global $wpdb;
  $selected = '';
  $output = '';
  $values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `active`='1' ORDER BY `id` ASC",ARRAY_A);
  foreach($values as $option)
    {
    if(is_numeric($product_id) && ($product_id > 0))
      {
      $category_assoc = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."item_category_associations` WHERE `product_id` IN('".$product_id."') AND `category_id` IN('".$option['id']."')  LIMIT 1",ARRAY_A); 
      if(is_numeric($category_assoc['id']) && ($category_assoc['id'] > 0))
        {
        $selected = "checked='true'";
        }
		elseif ($option['id'] == get_option('default_category'))
	    {
        $selected = "checked='true'";
		}
      }
    $output .= "<input type='radio' $selected name='category[]' value='".$option['id']."'><label>".$option['name']."</label><br />";
    $selected = "";
    }
  return $output;
  }
?>