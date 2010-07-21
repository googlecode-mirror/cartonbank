<?php
/*
	This is a front end for Lazyest-Gallery's Wordpress admin panel.
	It has been write for Lazyest-Gallery 0.8.3-beta
	Version: 0.9.5
	Date: 2006, Jenuary, 31
	Author: Keytwo Why, Jan831 (www.keytwo.net)
	Contact: keytwo.why@gmail.com
*/

/*	Copyright (C) 2005 - 2006 Valerio Chiodino
		(For questions join discussion on board.keytwo.net)

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

/*
	Admin form is called at the end of this file
*/

require_once('lazyest-cache.php');
require_once('lazyest-filemanager.php');
require_once('lazyest-gallery.php');
require_once('lazyest-styleditor.php');

$lg_text_domain = "lazyestgallery";
lazyestInit();

// =================== Buttons actions =================
// Button: "Update Option"
if (isset($_POST['update_options'])) {
	lg_update_options();
	include_once('lazyest-admin-form.php');
}
// Link to a folder to view & edit the captions
else if(isset($_GET['edit_css'])){
	lg_build_editcss_form();
}
/* Miscellaneous */
// Reset Options Button
else if (isset($_POST['lg_reset_options_button'])) {
	lg_reset_default_options();
}
// Delete Options Button
else if (isset($_POST['lg_delete_options_button'])) {
	lg_delete_options();
}
// no action set, lets build the normal form
else {
	// This will call the admin form that contain the main HTML code
	include_once('lazyest-admin-form.php');
}
// =============== End of Buttons actions ==============


// ===================== Functions =====================

/* =====================================================
 * Here start the functions of the file, all of 'em are
 * only in beta testing for the moment.
 * =====================================================
 */

// ================= Update Options Script =============

function lg_update_options(){
	global $user_level, $lg_text_domain;

	get_currentuserinfo();

	if ($user_level < 8) {
		die ( __('Are You Cheatin&#8217; uh?') );
	}

	/*
	 * updating the option values in the database
	 * no need to check wether it has changed,
	 * this is done by the function update_option()
	 */

	$galleryfolder = $_POST['gallery_folder'];
	if(isset($_POST['gallery_folder']) && strlen($galleryfolder) != 0){
			// a "/" is needed at the end of $galleryfolderto
			// add it if necessary
			if ( $galleryfolder{strlen($galleryfolder)-1} != "/" )
				$galleryfolder .= "/";
			update_option('lg_gallery_folder', $galleryfolder);
	}

	if(isset($_POST['gallery_uri'])){
		update_option('lg_gallery_uri', $_POST['gallery_uri']);
	}

	if(isset($_POST['sort_alphabetically']) && ereg("TRUE|FALSE", $_POST['sort_alphabetically'])){
		update_option('lg_sort_alphabetically', $_POST['sort_alphabetically']);
	}
	if(isset($_POST['thumbspage']) && is_numeric($_POST['thumbspage'])){
		update_option('lg_thumbs_page', abs(intval($_POST['thumbspage'])));
	}
	if(isset($_POST['folderscolumns']) && is_numeric($_POST['folderscolumns'])){
		update_option('lg_folders_columns', abs(intval($_POST['folderscolumns'])));
	}
	if(isset($_POST['thumbnailscolumns']) && is_numeric($_POST['thumbnailscolumns'])){
		update_option('lg_thumbs_columns', abs(intval($_POST['thumbnailscolumns'])));
	}
	if(isset($_POST['pictwidth']) && is_numeric($_POST['pictwidth'])){
		update_option('lg_pictwidth', abs(intval($_POST['pictwidth'])));
	}
	if(isset($_POST['pictheight']) && is_numeric($_POST['pictheight'])){
		update_option('lg_pictheight', abs(intval($_POST['pictheight'])));
	}
	if(isset($_POST['thumbwidth']) && is_numeric($_POST['thumbwidth'])){
		update_option('lg_thumbwidth', abs(intval($_POST['thumbwidth'])));
	}
	if(isset($_POST['thumbheight']) && is_numeric($_POST['thumbheight'])){
		update_option('lg_thumbheight', abs(intval($_POST['thumbheight'])));
	}
	if(isset($_POST['thumbfolder'])){
		$thumbfolder = $_POST['thumbfolder'];
		// a "/" is needed at the end of $thumbfolder
		// add it if necessary
		if ( $thumbfolder{strlen($thumbfolder)-1} != "/" )
				$thumbfolder .= "/";
		update_option('lg_thumb_folder', $thumbfolder);
	}
	if(isset($_POST['slidefolder'])){
		$slidefolder = 		$_POST['slidefolder'];
		// a "/" is needed at the end of $thumbfolder
		// add it if necessary
		if ( $slidefolder{strlen($slidefolder)-1} != "/" )
				$slidefolder .= "/";
		update_option('lg_slide_folder', $slidefolder);
	}

	// Use Slides popup
	if(isset($_POST['use_slides_popup'])){
		update_option('lg_use_slides_popup', "TRUE");
		update_option('lg_enable_lb_slides_support', "FALSE");
	}
	else if (!(isset($_POST['use_slides_popup']))) update_option('lg_use_slides_popup', "FALSE");

	if(isset($_POST['excludefolder'])){
		$excludefolder = 	explode(',', $_POST['excludefolder']);

		// add the folder of cached thumbnails here already
		// but maybe he's already in the array
		if(isset($_POST['thumbfolder'])){
			$thumbfolder = ($_POST['thumbfolder']{strlen($_POST['thumbfolder'])-1} == '/')?
							substr($_POST['thumbfolder'], 0, strlen($_POST['thumbfolder'])-1):
							$_POST['thumbfolder'];
			$thumbfolder_found = FALSE;
		}

		// remove any trailing '/'
		for($i=0; $i < sizeof($excludefolder); $i++){
			if($excludefolder[$i]{strlen($excludefolder[$i])-1} == '/')
				$excludefolder[$i] = substr($excludefolder[$i], 0, strlen($excludefolder[$i])-1);

			// check this element if it's the thumbfolder
			if(!$thumbfolder_found && $excludefolder[$i] == $thumbfolder)
				$thumbfolder_found = TRUE;
		}

		// if it wasn't already in the array, add it here
		if(!$thumbfolder_found)
			$excludefolder[]= $thumbfolder;

		update_option('lg_excluded_folders', $excludefolder);
		// arrays get serialized automatically (also unserialized when retrieving the option)
	}

	/* ==============
	 * Advanced Stuff
	 * ============== */

	// Cache for thumbnails
	if(isset($_POST['use_cache'])) update_option('lg_enable_cache', "TRUE");
	else if (!(isset($_POST['use_cache']))){
		update_option('lg_enable_cache', "FALSE");
		update_option('lg_use_cropping', "FALSE");
	}

	// Cache for slides
	if(isset($_POST['use_slides_cache'])) update_option('lg_enable_slides_cache', "TRUE");
	else if (!(isset($_POST['use_slides_cache']))) update_option('lg_enable_slides_cache', "FALSE");

	// Disable fullsize image link
	if(isset($_POST['disable_full_size'])) update_option('lg_disable_full_size', "TRUE");
	else if (!(isset($_POST['disable_full_size']))) update_option('lg_disable_full_size', "FALSE");

	// Cropping
	if(isset($_POST['use_cropping'])) {
		update_option('lg_use_cropping', "TRUE");
		update_option('lg_enable_cache', "TRUE");
	}
	else if (!(isset($_POST['use_cropping']))) update_option('lg_use_cropping', "FALSE");

	// Resample quality
	if(isset($_POST['resample_quality']) && is_numeric($_POST['resample_quality'])){
		if ($_POST['resample_quality'] <= 100 || $_POST['resample_quality'] >= 0) {
			update_option('lg_resample_quality', abs(intval($_POST['resample_quality'])));
		}
	}

	if(isset($_POST['buffer_size'])){
			$mem = $_POST['buffer_size'];
			// a "M" is needed at the end of buffer_size
			// add it if necessary
			if ( $mem{strlen($mem)-1} != "M" )
				$mem .= "M";

			update_option('lg_buffer_size', $mem);
	}

	if(isset($_POST['folder_image'])){
		update_option('lg_folder_image', $_POST['folder_image']);
	}

	/* ====================
	 * ExIF Data and Fields
	 * ==================== */

	// Enable ExIF Data
	if(isset($_POST['enable_exif'])) update_option('lg_enable_exif', "TRUE");
	else if (!(isset($_POST['enable_exif']))) update_option('lg_enable_exif', "FALSE");
	// Show Errors
	if(isset($_POST['exif_errors'])) update_option('lg_exif_print_error', "TRUE");
	else if (!(isset($_POST['exif_errors']))) update_option('lg_exif_print_error', "FALSE");
	// Show if it's a valid Jpeg
	if(isset($_POST['exif_valid_jpeg'])) update_option('lg_exif_valid_jpeg', "TRUE");
	else if (!(isset($_POST['exif_valid_jpeg']))) update_option('lg_exif_valid_jpeg', "FALSE");
	// Show if there are valid jfif data
	if(isset($_POST['exif_valid_jfif_data'])) update_option('lg_exif_valid_jfif_data', "TRUE");
	else if (!(isset($_POST['exif_valid_jfif_data']))) update_option('lg_exif_valid_jfif_data', "FALSE");

		// Show jfif size
		if(isset($_POST['exif_jfif_size'])) update_option('lg_exif_jfif_size', "TRUE");
		else if (!(isset($_POST['exif_jfif_size']))) update_option('lg_exif_jfif_size', "FALSE");
		// Show jfif identifier
		if(isset($_POST['exif_jfif_identifier'])) update_option('lg_exif_jfif_identifier', "TRUE");
		else if (!(isset($_POST['exif_jfif_identifier']))) update_option('lg_exif_jfif_identifier', "FALSE");
		// Show extension code
		if(isset($_POST['exif_jfif_extension_code'])) update_option('lg_exif_jfif_extension_code', "TRUE");
		else if (!(isset($_POST['exif_jfif_extension_code']))) update_option('lg_exif_jfif_extension_code', "FALSE");
		// Show JFIF data
		if(isset($_POST['exif_jfif_data'])) update_option('lg_exif_jfif_data', "TRUE");
		else if (!(isset($_POST['exif_jfif_data']))) update_option('lg_exif_jfif_data', "FALSE");

	// Show exif valid data
	if(isset($_POST['exif_valid_exif_data'])) update_option('lg_exif_valid_exif_data', "TRUE");
	else if (!(isset($_POST['exif_valid_exif_data']))) update_option('lg_exif_valid_exif_data', "FALSE");
	// Show app1 size
	if(isset($_POST['exif_app1_size'])) update_option('lg_exif_app1_size', "TRUE");
	else if (!(isset($_POST['exif_app1_size']))) update_option('lg_exif_app1_size', "FALSE");
	// Show endien
	if(isset($_POST['exif_endien'])) update_option('lg_exif_endien', "TRUE");
	else if (!(isset($_POST['exif_endien']))) update_option('lg_exif_endien', "FALSE");
	// Show ifd0 number of array tags
	if(isset($_POST['exif_ifd0_num_tags'])) update_option('lg_exif_ifd0_num_tags', "TRUE");
	else if (!(isset($_POST['exif_ifd0_num_tags']))) update_option('lg_exif_ifd0_num_tags', "FALSE");

		// Show IFD0 orientation
		if(isset($_POST['exif_ifd0_orientation'])) update_option('lg_exif_ifd0_orientation', "TRUE");
		else if (!(isset($_POST['exif_ifd0_orientation']))) update_option('lg_exif_ifd0_orientation', "FALSE");
		// Show X resolution
		if(isset($_POST['exif_ifd0_x_res'])) update_option('lg_exif_ifd0_x_res', "TRUE");
		else if (!(isset($_POST['exif_ifd0_x_res']))) update_option('lg_exif_ifd0_x_res', "FALSE");
		// Show Y Resolution
		if(isset($_POST['exif_ifd0_y_res'])) update_option('lg_exif_ifd0_y_res', "TRUE");
		else if (!(isset($_POST['exif_ifd0_y_res']))) update_option('lg_exif_ifd0_y_res', "FALSE");
		// Show Resolution Unit
		if(isset($_POST['exif_ifd0_res_unit'])) update_option('lg_exif_ifd0_res_unit', "TRUE");
		else if (!(isset($_POST['exif_ifd0_res_unit']))) update_option('lg_exif_ifd0_res_unit', "FALSE");
		// Show Software tag
		if(isset($_POST['exif_ifd0_software'])) update_option('lg_exif_ifd0_software', "TRUE");
		else if (!(isset($_POST['exif_ifd0_software']))) update_option('lg_exif_ifd0_software', "FALSE");
		// Show time
		if(isset($_POST['exif_ifd0_time'])) update_option('lg_exif_ifd0_time', "TRUE");
		else if (!(isset($_POST['exif_ifd0_time']))) update_option('lg_exif_ifd0_time', "FALSE");
		// Show IFD0 Offset
		if(isset($_POST['exif_ifd0_offset'])) update_option('lg_exif_ifd0_offset', "TRUE");
		else if (!(isset($_POST['exif_ifd0_offset']))) update_option('lg_exif_ifd0_offset', "FALSE");

	//Show IFD1 Main Offset
	if(isset($_POST['exif_ifd1_main_offset'])) update_option('lg_exif_ifd1_main_offset', "TRUE");
	else if (!(isset($_POST['exif_ifd1_main_offset']))) update_option('lg_exif_ifd1_main_offset', "FALSE");
	// Show sub ifd0 number of array tags
	if(isset($_POST['exif_sub_ifd_num_tags'])) update_option('lg_exif_sub_ifd_num_tags', "TRUE");
	else if (!(isset($_POST['exif_sub_ifd_num_tags']))) update_option('lg_exif_sub_ifd_num_tags', "FALSE");

		// Show Color Space
		if(isset($_POST['exif_sub_ifd_color_space'])) update_option('lg_exif_sub_ifd_color_space', "TRUE");
		else if (!(isset($_POST['exif_sub_ifd_color_space']))) update_option('lg_exif_sub_ifd_color_space', "FALSE");
		// Show ifd width
		if(isset($_POST['exif_sub_ifd_width'])) update_option('lg_exif_sub_ifd_width', "TRUE");
		else if (!(isset($_POST['exif_sub_ifd_width']))) update_option('lg_exif_sub_ifd_width', "FALSE");
		// Show ifd height
		if(isset($_POST['exif_sub_ifd_height'])) update_option('lg_exif_sub_ifd_height', "TRUE");
		else if (!(isset($_POST['exif_sub_ifd_height']))) update_option('lg_exif_sub_ifd_height', "FALSE");

	// Show ifd0 number of array tags
	if(isset($_POST['exif_ifd1_num_tags'])) update_option('lg_exif_ifd1_num_tags', "TRUE");
	else if (!(isset($_POST['exif_ifd1_num_tags']))) update_option('lg_exif_ifd1_num_tags', "FALSE");

		// Show ifd1 compression
		if(isset($_POST['exif_ifd1_compression'])) update_option('lg_exif_ifd1_compression', "TRUE");
		else if (!(isset($_POST['exif_ifd1_compression']))) update_option('lg_exif_ifd1_compression', "FALSE");
		// Show ifd1 X resolution
		if(isset($_POST['exif_ifd1_x_res'])) update_option('lg_exif_ifd1_x_res', "TRUE");
		else if (!(isset($_POST['exif_ifd1_x_res']))) update_option('lg_exif_ifd1_x_res', "FALSE");
		// Show ifd1 Y resolution
		if(isset($_POST['exif_ifd1_y_res'])) update_option('lg_exif_ifd1_y_res', "TRUE");
		else if (!(isset($_POST['exif_ifd1_y_res']))) update_option('lg_exif_ifd1_y_res', "FALSE");
		// Show ifd1 resolution unit
		if(isset($_POST['exif_ifd1_res_unit'])) update_option('lg_exif_ifd1_res_unit', "TRUE");
		else if (!(isset($_POST['exif_ifd1_res_unit']))) update_option('lg_exif_ifd1_res_unit', "FALSE");
		// Show ifd1 jpeg offset
		if(isset($_POST['exif_ifd1_jpeg_if_offset'])) update_option('lg_exif_ifd1_jpeg_if_offset', "TRUE");
		else if (!(isset($_POST['exif_ifd1_jpeg_if_offset']))) update_option('lg_exif_ifd1_jpeg_if_offset', "FALSE");
		// Show byte count
		if(isset($_POST['exif_ifd1_jpeg_if_byte_count'])) update_option('lg_exif_ifd1_jpeg_if_byte_count', "TRUE");
		else if (!(isset($_POST['exif_ifd1_jpeg_if_byte_count']))) update_option('lg_exif_ifd1_jpeg_if_byte_count', "FALSE");

	/* ===========================
	 *	End of ExIF Data and Fields
	 * =========================== */

	/* ========
	 * Captions
	 * ======== */

	if(isset($_POST['enable_captions'])) update_option('lg_enable_captions', "TRUE");
	else if (!(isset($_POST['enable_captions']))) update_option('lg_enable_captions', "FALSE");

	if(isset($_POST['use_folder_captions'])) update_option('lg_use_folder_captions', "TRUE");
	else if (!(isset($_POST['use_folder_captions']))) update_option('lg_use_folder_captions', "FALSE");

	/* ================
	 * Lightbox support
	 * ================ */

	// Main
	if(isset($_POST['enable_lb_support']) && some_lightbox_plugin()) {
		update_option('lg_enable_lb_support', "TRUE");
		update_option('lg_enable_slides_cache', "TRUE");
	}
	else if (!(isset($_POST['enable_lb_support']))) update_option('lg_enable_lb_support', "FALSE");

	// Forcing
	if(isset($_POST['force_lb_support'])) {
		update_option('lg_force_lb_support', "TRUE");
		update_option('lg_enable_lb_support', "TRUE");
		update_option('lg_enable_slides_cache', "TRUE");
	}
	else if (!(isset($_POST['force_lb_support']))) update_option('lg_force_lb_support', "FALSE");

	// Thumbs
	if(isset($_POST['enable_lb_thumbs_support'])) update_option('lg_enable_lb_thumbs_support', "TRUE");
	else if (!(isset($_POST['enable_lb_thumbs_support']))) update_option('lg_enable_lb_thumbs_support', "FALSE");

	// Slides
	if(isset($_POST['enable_lb_slides_support'])) {
		update_option('lg_enable_lb_slides_support', "TRUE");
		update_option('lg_use_slides_popup', "FALSE");
	}
	else if (!(isset($_POST['enable_lb_slides_support']))) update_option('lg_enable_lb_slides_support', "FALSE");

	// Sidebar
	if(isset($_POST['enable_lb_sidebar_support'])) update_option('lg_enable_lb_sidebar_support', "TRUE");
	else if (!(isset($_POST['enable_lb_sidebar_support']))) update_option('lg_enable_lb_sidebar_support', "FALSE");

	// Posts
	if(isset($_POST['enable_lb_posts_support'])) update_option('lg_enable_lb_posts_support', "TRUE");
	else if (!(isset($_POST['enable_lb_posts_support']))) update_option('lg_enable_lb_posts_support', "FALSE");

	/* ================
	 * Thickbox support
	 * ================ */

	// Main
	if(isset($_POST['enable_tb_support']) && some_thickbox_plugin()) {
		update_option('lg_enable_tb_support', "TRUE");
		update_option('lg_enable_slides_cache', "TRUE");
	}
	else if (!(isset($_POST['enable_tb_support']))) update_option('lg_enable_tb_support', "FALSE");

	// Forcing
	if(isset($_POST['force_tb_support'])) {
		update_option('lg_force_tb_support', "TRUE");
		update_option('lg_enable_tb_support', "TRUE");
		update_option('lg_enable_slides_cache', "TRUE");
	}
	else if (!(isset($_POST['force_tb_support']))) update_option('lg_force_tb_support', "FALSE");

	// Thumbs
	if(isset($_POST['enable_tb_thumbs_support'])) update_option('lg_enable_tb_thumbs_support', "TRUE");
	else if (!(isset($_POST['enable_tb_thumbs_support']))) update_option('lg_enable_tb_thumbs_support', "FALSE");

	// Slides
	if(isset($_POST['enable_tb_slides_support'])) {
		update_option('lg_enable_tb_slides_support', "TRUE");
		update_option('lg_use_slides_popup', "FALSE");
	}
	else if (!(isset($_POST['enable_tb_slides_support']))) update_option('lg_enable_tb_slides_support', "FALSE");

	// Sidebar
	if(isset($_POST['enable_tb_sidebar_support'])) update_option('lg_enable_tb_sidebar_support', "TRUE");
	else if (!(isset($_POST['enable_tb_sidebar_support']))) update_option('lg_enable_tb_sidebar_support', "FALSE");

	// Posts
	if(isset($_POST['enable_tb_posts_support'])) update_option('lg_enable_tb_posts_support', "TRUE");
	else if (!(isset($_POST['enable_tb_posts_support']))) update_option('lg_enable_tb_posts_support', "FALSE");

	/* ==============
	 * Upload options
	 * ============== */

	// fileupload_maxk fileupload_allowedtypes fileupload_minlevel
	// lg_fileupload_maxk lg_fileupload_allowedtypes lg_fileupload_minlevel

	// File size
	if(isset($_POST['fileupload_maxk']) && is_numeric($_POST['fileupload_maxk'])){
		update_option('lg_fileupload_maxk', abs(intval($_POST['fileupload_maxk'])));
	}

	// Allowed files to upload
	if(isset($_POST['fileupload_allowedtypes'])){
		update_option('lg_fileupload_allowedtypes', $_POST['fileupload_allowedtypes']);
	}
	// User level who can upload
	if(isset($_POST['fileupload_minlevel']) && is_numeric($_POST['fileupload_minlevel'])){
		update_option('lg_fileupload_minlevel', abs(intval($_POST['fileupload_minlevel'])));
	}

	// Microsoft Wizard Publisher
	if(isset($_POST['enable_mwp_support'])) update_option('lg_enable_mwp_support', "TRUE");
	else if (!(isset($_POST['enable_mwp_support']))) update_option('lg_enable_mwp_support', "FALSE");

	lg_options_success(__('Options Updated Successfully', $lg_text_domain));

}

// Retrieve information about the currently installed GD library
function lg_admin_describeGDdyn() {
	global $lg_text_domain;
	echo "* GD support: ";
	if(function_exists("gd_info")){
		_e('<span style=\'color:#00ff00\'> yes</span>', $lg_text_domain);
		$info = gd_info();
		$keys = array_keys($info);
		for($i=0; $i<count($keys); $i++) {
			if(is_bool($info[$keys[$i]]))
				echo "<br />* " . $keys[$i] .": " . lg_admin_yesNo($info[$keys[$i]]);
			else
				echo "<br />* " . $keys[$i] .": " . $info[$keys[$i]];
		}
	}
	else {
		_e('<span style=\'color:#ff0000\'> no</span>', $lg_text_domain);
	}

}

function lg_admin_yesNo($bool){
	global $lg_text_domain;
	if($bool)
		return _e('<span style=\'color:#00ff00\'> yes</span>', $lg_text_domain);
		//return "<span style='color:#00ff00'> ". _e('yes', $lg_text_domain)."</span>";
	else
		return _e('<span style=\'color:#ff0000\'> no</span>', $lg_text_domain);
		//return "<span style='color:#ff0000'> ". _e('no', $lg_text_domain)."</span>";
}

function lg_delete_options($reset = false){
	global $lg_text_domain;

	delete_option('lg_gallery_folder');
	delete_option('lg_gallery_uri');
	delete_option('lg_sidebar_position');
	delete_option('lg_excluded_folders');
	delete_option('lg_gallery_width');
	delete_option('lg_sort_alphabetically');
	delete_option('lg_pictwidth');
	delete_option('lg_pictheight');
	delete_option('lg_thumbwidth');
	delete_option('lg_thumbheight');
	delete_option('lg_thumbs_page');
	delete_option('lg_folders_columns');
	delete_option('lg_thumbs_columns');
	delete_option('lg_thumb_folder');
	delete_option('lg_slide_folder');
	delete_option('lg_folder_image');
	delete_option('lg_use_slides_popup');
	delete_option('lg_disable_full_size');
	// Advanced Stuff
	delete_option('lg_enable_cache');
	delete_option('lg_enable_slides_cache');
	delete_option('lg_use_cropping');
	delete_option('lg_buffer_size');
	delete_option('lg_resample_quality');
	// Captions options
	delete_option('lg_enable_captions');
	delete_option('lg_use_folder_captions');
	// Lightbox support
	delete_option('lg_enable_lb_support');
	delete_option('lg_force_lb_support');
	delete_option('lg_enable_lb_thumbs_support');
	delete_option('lg_enable_lb_slides_support');
	delete_option('lg_enable_lb_sidebar_support');
	delete_option('lg_enable_lb_posts_support');
	// Thickbox support
	delete_option('lg_enable_tb_support');
	delete_option('lg_force_tb_support');
	delete_option('lg_enable_tb_thumbs_support');
	delete_option('lg_enable_tb_slides_support');
	delete_option('lg_enable_tb_sidebar_support');
	delete_option('lg_enable_tb_posts_support');
	// ExIF default settings
	delete_option('lg_enable_exif');
	delete_option('lg_exif_errors');
	delete_option('lg_exif_valid_jpeg');
	delete_option('lg_exif_valid_jfif_data');
	delete_option('lg_exif_jfif_size');
	delete_option('lg_exif_jfif_identifier');
	delete_option('lg_exif_jfif_extension_code');
	delete_option('lg_exif_jfif_data');
	delete_option('lg_exif_valid_exif_data');
	delete_option('lg_exif_app1_size');
	delete_option('lg_exif_endien');
	delete_option('lg_exif_ifd0_num_tags');
	delete_option('lg_exif_ifd0_orientation');
	delete_option('lg_exif_ifd0_x_res');
	delete_option('lg_exif_ifd0_y_res');
	delete_option('lg_exif_ifd0_res_unit');
	delete_option('lg_exif_ifd0_software');
	delete_option('lg_exif_ifd0_time');
	delete_option('lg_exif_ifd0_offset');
	delete_option('lg_exif_ifd1_main_offset');
	delete_option('lg_exif_sub_ifd_num_tags');
	delete_option('lg_exif_sub_ifd_color_space');
	delete_option('lg_exif_sub_ifd_width');
	delete_option('lg_exif_sub_ifd_height');
	delete_option('lg_exif_ifd1_num_tags');
	delete_option('lg_exif_ifd1_compression');
	delete_option('lg_exif_ifd1_x_res');
	delete_option('lg_exif_ifd1_y_res');
	delete_option('lg_exif_ifd1_res_unit');
	delete_option('lg_exif_ifd1_jpeg_if_offset');
	delete_option('lg_exif_ifd1_jpeg_if_byte_count');
	// Upload settings
	delete_option('lg_fileupload_maxk');
	delete_option('lg_fileupload_allowedtypes');
	delete_option('lg_fileupload_minlevel');
	// Microsoft Wizard
	delete_option('lg_enable_mwp_support');
	delete_option('lg_wizard_user');
	delete_option('lg_wizard_password');
	// Image Indexing
	delete_option('lg_image_indexing');
	// Old and Eventual
	delete_option('lg_exif_print_error');
	delete_option('exif_jfif_extension_code');

	if (!$reset) {
		// Display the success message
		lg_options_success(__('Options Deleted Successfully', $lg_text_domain));
	}

}

function lg_reset_default_options(){
	global $lg_text_domain;
	lg_delete_options(true);
	lazyestInit();
	// Display the success message
	lg_options_success(__('Options Resetted Successfully', $lg_text_domain));

}

// Displays the success message
function lg_options_success($message) {
	?>

	<div style='background-color: rgb(207, 235, 247);' id='message' class='updated fade'>
		<p>
			<strong>
				<?php echo $message; ?>
			</strong>
		</p>
	</div>

	<?php
}

?>