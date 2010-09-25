<?php
/*
Plugin Name: Five Star Rating
Plugin URI: http://fsr.dingobytes.com/
Description: Five Star Rating plugin for rating posts and pages, modified from script from O Doutor
Version: 1.2
Author: Andrew Alba
Author URI: http://wordpress-plug.in/
*/

/*  Copyright 2009  Andrew Alba  (email : andrew.alba@gmail.com)

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

require_once('fsr.class.php');

/*
 * Tag to use in posts and or pages that shows stars for voting
 * [five-star-rating] or [five-star-rating star_type="abuse"]
 * star_type is for styling purposes ("star","abuse") 
 */
function five_star_rating_func($atts) {
	global $FSR;
	extract(shortcode_atts(array(
		'star_type' => 'star',
	), $atts));
	return $FSR->getVotingStars($star_type);
}

function five_star_rating_func_2($id) {
    global $FSR, $picture_id;
    $picture_id = $id;
    return $FSR->getVotingStars('star');
}

/*
 * Tag to use in posts and or pages that just displays results
 * [FSR_results] or [FSR_results star_type="star"]
 * star_type is for styling purposes ("star","abuse") 
 */
function FSR_results_func($atts) {
	global $FSR;
	extract(shortcode_atts(array(
		'star_type' => 'star',
	), $atts));
	return $FSR->getStars($star_type);
}
/* Tag to use in posts and or pages that displays best for the month
 * [FSR_best_of_month month=1 limit=5 star_type="star"]
 * month attribute is integer 1-12
 * limit attribute limits how many results returned
 * star_type is for styling purposes ("star","abuse")
 */
function FSR_best_of_month_func($atts) {
	global $FSR;
	extract(shortcode_atts(array(
		'star_type' => 'star',
	), $atts));
	return $FSR->getBestOfMonth($star_type);
}

add_shortcode('five-star-rating', 'five_star_rating_func');
add_shortcode('FSR_results', 'FSR_results_func');
add_shortcode('FSR_best_of_month', 'FSR_best_of_month_func');

/* old school functions to allow embedding into template
 * CAUTION: implementing this method into post can cause errors if the plugin is deactivated.
 */
function FSR_show_voting_stars($star_type = "star") {
	global $FSR;
	echo $FSR->getVotingStars($star_type);
}
function FSR_show_stars($star_type = "star") {
	global $FSR;
	echo $FSR->getStars($star_type);
}
function FSR_bests_of_month($star_type = "star") {
	global $FSR;
	echo $FSR->getBestOfMonth($star_type);
}

wp_register_style('five-star-rating-CSS', WP_PLUGIN_URL . '/five-star-rating/assets/css/five-star-rating.min.css');
wp_enqueue_style('five-star-rating-CSS');
wp_enqueue_script('five-star-rating-JS', WP_PLUGIN_URL . '/five-star-rating/assets/js/five-star-rating.min.js', array('jquery'), '0.1');
/* Assigning hooks to actions */
$FSR =& new FSR();
add_action('activate_five-star-rating/five-star-rating.php', array(&$FSR, 'install')); /* only works on WP 2.x*/
add_action('init', array(&$FSR, 'init'));

//setup warning
function fsr_admin_warnings() {
	global $fsr_cookie_expiration;
	if ( !get_option('fsr_cookie_expiration') && !isset($_POST['submit']) ) {
		function fsr_warning() {
			echo "
			<div id='fsr-warning' class='updated fade'><p><strong>".__('Five Star Rating plugin is almost ready.')."</strong> ".sprintf(__('You must <a href="%1$s">enter your cookie expiration</a> for it to work.'), "plugins.php?page=five-star-rating/five-star-rating.php")."</p></div>
			";
		}
		add_action('admin_notices', 'fsr_warning');
		return;
	} 
}

function fsr_warning_init() {
	fsr_admin_warnings();
}
add_action('init', 'fsr_warning_init');

// create custom plugin settings menu
add_action('admin_menu', 'fsr_create_menu');

function fsr_create_menu() {

	//create new top-level menu
	add_menu_page('FSR Plugin Settings', 'Five Star Rating', 'administrator', __FILE__, 'fsr_settings_page');

	//call register settings function
	add_action( 'admin_init', 'register_fsr_settings' );
}


function register_fsr_settings() {
	//register our settings
	register_setting( 'fsr-settings-group', 'fsr_cookie_expiration' );
	register_setting( 'fsr-settings-group', 'fsr_cookie_expiration_unit' );
	register_setting( 'fsr-settings-group', 'fsr_show_credit');
}

function fsr_settings_page() {
	$showCredit = get_option('fsr_show_credit');
	if(empty($showCredit)) { $showCredit = "true"; }
?>
<div class="wrap">
<h2>Five Star Rating</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'fsr-settings-group' ); ?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Cookie Expiration</th>
			<td>
				<input type="text" name="fsr_cookie_expiration" value="<?php echo get_option('fsr_cookie_expiration'); ?>" />
				<select name="fsr_cookie_expiration_unit">
					<option value="minute"<?php if(get_option('fsr_cookie_expiration_unit') == 'minute') { ?> selected="selected"<?php } ?>>minute(s)</option>
					<option value="hour"<?php if(get_option('fsr_cookie_expiration_unit') == 'hour') { ?> selected="selected"<?php } ?>>hour(s)</option>
					<option value="day"<?php if(get_option('fsr_cookie_expiration_unit') == 'day') { ?> selected="selected"<?php } ?>>day(s)</option>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="show_credits">Show Credits</label></th>
			<td>
				<label for="fsr_show_credits"> Show </label><input type="radio" id="fsr_show_credits" name="fsr_show_credit" value="true"<?php if($showCredit == "true") { ?> checked="checked"<?php } ?> /> 
				<label for="fsr_hide_credits"> Hide </label><input type="radio" id="fsr_hide_credits" name="fsr_show_credit" value="false"<?php if($showCredit == "false") { ?> checked="checked"<?php } ?> />
			</td>
		</tr>
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
</form>
<span>Get the <a href="http://wordpress-plug.in/featured/five-star-rating/" title="Five Star Rating">premium version of five star rating</a></span>
</div>
<?php } ?>