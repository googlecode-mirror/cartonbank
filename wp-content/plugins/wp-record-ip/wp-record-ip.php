<?php
		/*
		Plugin Name: WP Record IP
		Plugin URI: http://silvercover.wordpress.com/wp-record-ip
		Description: Plugin for recording users IPs.
		Author: Hamed Takmil
		Version: 1.0
		Author URI: http://silvercover.wordpress.com
		*/
		
		/*  Copyright 2010  Hamed Takmil aka silvercover
		
		Email: ham55464@yahoo.com
		
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
   */


//We call this for localization.   
load_plugin_textdomain('wp-record-ip', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)).'/languages');

//Define plugin base name and folder as a constant.
$plugin_full_url_path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));

$myLDNavigationBarStyle = 'style="text-align:center;font-size:8pt;color:#808080;"';

$wpdbb_content_url = ( defined('WP_CONTENT_URL') ) ? WP_CONTENT_URL : get_option('siteurl') . '/wp-content';
//define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
define('SITE_URL', get_option('siteurl'));
define('PLUGIN_FULL_URL', $plugin_full_url_path);
define('WPRIP_PlugIn_Version', "1.0");
define('TimeDateFormat', get_option( 'date_format', 'Y-d-m' ) . ' ' . get_option( 'time_format', 'g:i a' ));

//Plugin installation function which will be called on activation.
function wp_record_ip_install(){
    update_option("wp_record_ip_db_version", WPRIP_PlugIn_Version);
}
register_activation_hook(__FILE__, 'wp_record_ip_install');

add_action('admin_menu', 'wp_record_ip_admin_actions');
// action function for above hook
function wp_record_ip_admin_actions() {
     $icon_url = '';
     $position = '';
    // Add a new top-level menu (ill-advised):
    //add_menu_page(__('WP Record IP', 'wp-record-ip'), __('WP Record IP', 'wp-record-ip'), 'administrator', 'wp-record-ip', 'wp_record_ip_show');
    add_submenu_page('users.php', __('WP Record IP', 'wp-record-ip'), __('WP Record IP', 'wp-record-ip'), 'manage_options', 'wp-record-ip', 'wp_record_ip_show' );
}

function wp_record_ip(){
    global $wpdb;
    global $current_user;
    if ( is_user_logged_in() ){ 
     $current_user = wp_get_current_user();
    }else{
     $current_user = -1;
    }
    //$table = $wpdb->prefix."record_ip";
    //$sql_query = $wpdb->prepare("INSERT INTO ".$table." (user_id, ip_address, time)
    //             VALUES ('".$current_user->ID."', '".$_SERVER['REMOTE_ADDR']."', '".time()."')");
    //$wpdb->query($sql_query);
    update_user_meta( $current_user->ID, 'wp_record_user_ip', $_SERVER['REMOTE_ADDR'].'|'.time());
}
add_action('auth_redirect', 'wp_record_ip', 150);


///////////////////////

function wp_record_ip_show() {

 global $myLDNavigationBarStyle;
 global $wpdb;
 $wpdb->hide_errors();
 $table = $wpdb->prefix."usermeta";
 
 if (isset($_GET['pge'])) {
   $pageno = intval($_GET['pge']);
  } else {
   $pageno = 1;
 }
 $sql_query = ("SELECT count(meta_key) FROM ".$table." WHERE meta_key='wp_record_user_ip'");
 $all_ips = $wpdb->get_var($sql_query);
 
 $rows_per_page = 30;
 $lastpage      = @ceil($all_ips/$rows_per_page);

 $pageno = (int)$pageno;
 if ($pageno > $lastpage) {
   $pageno = $lastpage;
 } 
 if ($pageno < 1) {
   $pageno = 1;
 } 
 
 $limit = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;

 if (isset($_POST['search_status']) && $_POST['search_status'] !='Y'){
  $sql_query = $wpdb->prepare(" SELECT * FROM ".$table." WHERE meta_key='wp_record_user_ip' ORDER BY umeta_id DESC ".$limit );
 }else{
 //Here we build our search query to find and sort our list.
 if (empty($_POST['sort_by'])){
  $cloumn = "ORDER BY umeta_id";
 }else{
  $cloumn = "ORDER BY ".$_POST['sort_by'];
 }
 
 if (empty($_POST['sort_order'])){
  $order = "DESC";
 }else{
  $order = $_POST['sort_order'];
 }
 
 if (isset($_POST['s']))
	$_s=$_POST['s'];
 else
	$_s='';

 $sql_query = (" SELECT * FROM ".$table." WHERE meta_key='wp_record_user_ip' AND meta_value LIKE '%".$_s."%' ".$cloumn." ".$order." ".$limit);
 }
  $ret_ips = $wpdb->get_results($sql_query , ARRAY_A);
  
 //Check posted order and direction to set corresponding listbox show selected one.
$selected_ip = "";
$selected_date = "";
if (isset($_POST['sort_by']))
{
 switch ($_POST['sort_by']) {
  case "selected_ip":
    $selected_ip = 'selected="selected"';
    break;
  case "selected_date":
    $selected_date = 'selected="selected"';
    break;
 }
}
$selectd_order_asc = "";
$selectd_order_desc = "";
if (isset($_POST['sort_order']))
{
	switch ($_POST['sort_order']) {
  case "ASC":
    $selectd_order_asc     = 'selected="selected"';
    break;
  case "DESC":
    $selectd_order_desc    = 'selected="selected"';
    break;
 }
}
 //

?>
 <?php

 if (isset($_POST['action2']) && $_POST['action2'] =='delete'){
        foreach($_POST['ips_group'] as $ip=>$value){
         $sql_query = ("DELETE FROM $table WHERE umeta_id ='".$value."' LIMIT 1");
         $wpdb->query($sql_query );
        }
        $up_url = "admin.php?page=wp-record-ip";
        ?>
        <script type="text/javascript">
        window.location.replace("<?php echo $up_url?>")
       </script>
 <?php
 }
 ?>

<div class="wrap"> 
<?php echo "<h2>" . __('List of IPs', 'wp-record-ip') . "</h2>"; ?>
<form method="post" action="<?php echo "admin.php?page=wp-record-ip"; ?>" name="link_form">
<ul class="subsubsub">
 <li><strong><?php echo __('Total IPs', 'wp-record-ip').": </strong>".$all_ips ?></li>
</ul>
<p class="search-box">
<span><?php echo __('Sort by:', 'wp-record-ip') ?></span>
<!--<select name="sort_by">
 <option value="" ><?php echo __('Select Column', 'wp-record-ip') ?></option>
 <option value="selected_ip"     <?php echo $selected_ip ?>><?php echo __('IP', 'wp-record-ip') ?></option>
 <option value="selected_date"   <?php echo $selected_date ?>><?php echo __('Date', 'wp-record-ip') ?></option>
</select>-->
<select name="sort_order">
 <option value=""><?php echo __('Select Direction', 'wp-record-ip') ?></option>
 <option value="ASC"  <?php echo $selectd_order_asc ?>><?php echo __('Ascending', 'wp-record-ip') ?></option>
 <option value="DESC" <?php echo $selectd_order_desc ?>><?php echo __('Descending', 'wp-record-ip') ?></option>
</select>
<input id="post-search-input" class="search-input" type="text" value="" name="s"/>
<input type="hidden" value="Y" name="search_status"/>
<input class="button" type="submit" value="<?php echo _e('Search', 'wp-record-ip')?>"/>
</p>
 <table class="widefat fixed" cellspacing="0">
 <thead>
  <tr>
   <th id="cb" scope="col" class="manage-column column-cb check-column">
    <input type="checkbox" name="checkAll" value=""/>
   </th>
   <th scope="col" class="manage-column column-name"><?php echo __('User Name', 'wp-record-ip') ?></th>
   <th scope="col" class="manage-column column-name"><?php echo __('IP', 'wp-record-ip') ?></th>
   <th scope="col" class="manage-column column-name" style="text-align:center"><?php echo __('Record Date and Time', 'wp-record-ip') ?></th>
  </tr>
 </thead>
 <tbody>
 <?php
	$a=0;
 if (!empty($ret_ips)){
  foreach ($ret_ips as $ip) {
   $a++;
  //if ($ldlink['approval'] == 0 ){
  // $style = 'style="background-color: #FFFFD2"';
  //}else{
   $style = '';
  //}
  if ($a % 2) {
  ?> 
   <tr class="alternate" valign="middle" id="link-2" <?php echo $style; ?>>
  <?php
   }else{
   ?>
   <tr valign="middle" id="link-2" <?php echo $style; ?>>
   <?php
   }
   ?>
    <th class="check-column" scope="row">
      <input type="checkbox" name="ips_group[]" value="<?php echo $ip['umeta_id']?>"/>
    </th>
    <td class="column-name">
     <strong>
     <?php $pieces = explode("|", $ip['meta_value']); $current_user = get_userdata($ip['user_id']); ?>
      <a href="user-edit.php?user_id=<?php echo $ip['user_id']?>" title="<?php echo $current_user->user_login;?>" target="_blank">
       <?php echo $current_user->user_login;?>
      </a>
     </strong>
    </td>
    <td class="column-name">
     <p>
      <a href="http://whois.domaintools.com/<?php echo $pieces[0]; ?>" target="_blank"><?php echo $pieces[0]; ?></a>
     </p>
    </td>
     <td class="column-name">
     <?php 
       if (function_exists('jdate')) {
        //Jalali Calendar specific styling.
        echo '<center><p style="direction:ltr;text-align:center">'.$post_date = jdate(TimeDateFormat, $pieces[1]).'</p></center>';
       }else{
        echo '<center><p>'.$post_date = date(TimeDateFormat, $pieces[1]).'</p></center>';
       }
     ?>
    </td>
   </tr>
 <?php
 
 //Flush sorting variables.
  $selected_ip = '';
  $selected_user_id = '';
  }
 }
 ?>
 </tbody>
 <tfoot>
  <tr>
   <th id="cb" scope="col" class="manage-column column-cb check-column">
    <input type="checkbox" name="checkAll" value=""/>
   </th>
   <th scope="col" class="manage-column column-name"><?php echo __('User Name', 'wp-record-ip') ?></th>
   <th scope="col" class="manage-column column-name"><?php echo __('IP', 'wp-record-ip') ?></th>
   <th scope="col" class="manage-column column-name" style="text-align:center"><?php echo __('Record Date and Time', 'wp-record-ip') ?></th>
  </tr>
 </tfoot>
 </table>

  <div class="tablenav">
  <div class="alignleft actions">
  <select name="action2">
   <option selected="selected" value="-1"><?php echo __('Bulk Actions', 'wp-record-ip') ?></option>
   <option value="delete"><?php echo __('Delete', 'wp-record-ip') ?></option>
  </select>
  <script type="text/javascript">
  function warning(){
   action = window.confirm("<?php echo __('Are you sure?', 'wp-record-ip') ?>");
   if (action){
    document.link_form.submit();
   }else{
    return false;
   }
  }
  </script>
  <input type="submit" class="button-secondary action" id="doaction2" name="doaction2" value="<?php echo __('Apply', 'wp-record-ip') ?>" onclick="return warning();"/>
  <br class="clear"/>
  </div>
  <br class="clear"/>
  </div>
  <br />
  <table class="widefat">
			<tr class="alternate">
				<td colspan="2" align="center">
				  <div <?php echo $myLDNavigationBarStyle; ?>>
            <?php
            if ($pageno == 1) {
             echo " ".__('First', 'wp-record-ip')." ".__('Prev', 'wp-record-ip')." ";
            } else {
             echo " <a href=\"admin.php?page=wp-record-ip&pge=1\">".__('First', 'wp-record-ip')."</a>";
             $prevpage = $pageno-1;
             echo " <a href=\"admin.php?page=wp-record-ip&pge=".$prevpage."\">&laquo ".__('Prev', 'wp-record-ip')."</a> ";
            }
           echo " ( ".__('Page', 'wp-record-ip')." $pageno ".__('of', 'wp-record-ip')." $lastpage ) ";
           if ($pageno == $lastpage) {
             echo " ".__('Next', 'wp-record-ip')." ".__('Last', 'wp-record-ip')." ";
          } else {
             $nextpage = $pageno+1;
             echo " <a href=\"admin.php?page=wp-record-ip&pge=".$nextpage."\">".__('Next', 'wp-record-ip')." &raquo</a> ";
             echo " <a href=\"admin.php?page=wp-record-ip&pge=".$lastpage."\">".__('Last', 'wp-record-ip')."</a> ";
          }
          ?>
        </div>
				</td>
			</tr>
	</table>
</form>
<br />		
</div>

<div class="clear"/></div>	
<?php


}
?>