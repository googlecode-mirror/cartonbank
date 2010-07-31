<?php
/*
Plugin Name: Lazyest Gallery
Plugin URI: http://lazyest.keytwo.net
Description: Easy Gallery management plugin for Wordpress 2.0 with <a href="admin.php?page=lazyest-gallery/lazyest-admin.php" title="Go to the administration page">administration page</a>.
Date: 2006, Jenuary, 31
Author: Keytwo Why
Author URI: http://www.keytwo.net
Version: 0.9.5
*/

/*
	Copyright (C) 2004 Nicholas Bruun Jespersen
		(For questions join discussion on www.lazyboy.dk)
	Copyright (C) 2005 - 2006 Valerio Chiodino
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

// Uncomment this line for development purpose only
// error_reporting(E_ALL);

require_once('lazyest-cache.php');
// require_once('wp-content/plugins/lazyest-gallery/lazyest-comments.php');
require_once('lazyest-filemanager.php');
require_once('lazyest-dirs.php');
require_once('lazyest-parser.php');
require_once('lazyest-slides.php');
require_once('lazyest-thumbs.php');
require_once('lazyest-wizard-form.php');

define('LG_VERSION', '0.9.5');
define('LG_ADM_PAGE', 'admin.php?page=lazyest-gallery/lazyest-admin.php');
define('LG_FLM_PAGE', 'admin.php?page=lazyest-gallery/lazyest-filemanager.php');

// Localization domain
load_plugin_textdomain('lazyestgallery',false,'wp-content/plugins/lazyest-gallery/');
$lg_text_domain = "lazyestgallery";

// Initialize all variables
lazyestInit();

// Filter for the gallery page
add_action('wp_head', 'add_lazyest_gallery_style');
add_filter('the_content', 'parse_page_for_gallery');

// Insert the gallery_add_pages() sink into the plugin hook list for 'admin_menu'
add_action('admin_menu', 'gallery_add_pages');

// Filter for the post with images links to the gallery ie:[[Image:folder/img.png|align|110|descr]]
add_action('wp_head', 'add_post_thumbs_style');
add_filter('the_content', 'parse_posts_for_images');

// Adds the gallery browser in the post editor
add_action('edit_form_advanced', 'lg_add_gallery_browser');

// Adds the smartlink button in the write page
add_filter('admin_footer', 'lazyest_write_quicktag');

// ============== Gallery Main Functions ===============

function showGallery() { 				// Builds main gallery page
	global $file, $gallery_address, $gallery_root, $user_level, $lg_text_domain;

	get_currentuserinfo();

	echo "<div id='gallery'>";

	// get_image_comments();

	if (!validateFile()) {
		echo "<div class='error'>";
		echo "<p><b>". __('WARNING', $lg_text_domain) .": </b>". __('Unable to access to the Gallery Folder', $lg_text_domain);
		echo "</p>";
		echo "<p>". __('Check your settings', $lg_text_domain);
		if ($user_level >= 8) {
			echo "<a href='".get_option('siteurl')."/wp-admin/". LG_ADM_PAGE ."'>";
			_e('here', $lg_text_domain);
			echo "</a>";

		}
		echo "</p></div>";
			return;
	}

	createNavigation();

	$path = pathinfo($file);
	if ($path['extension'] == ''){
		//Display Dir(s) (if any)
		showDirs();
		//Display Thumb(s) (if any)
		showThumbs();
	} else {
		showSlide($file);
	}

	if ($user_level >= 8) {
		echo "<div class='lg_admin'>";
			echo "<a href='".get_option('siteurl')."/wp-admin/". LG_ADM_PAGE ."'>";
			_e('&raquo; Administrate Gallery', $lg_text_domain);
			echo "</a>";
		echo "</div>";
	}
	//echo "<div id='lg_powered'>
	//			<div class='lgpow'>Powered by <a href='http://lazyest.keytwo.net'>Lazyest Gallery ". LG_VERSION ."</a> - Copyright (C) <a href='http://www.keytwo.net'>Keytwo.net</a></div>
	//		</div>";

// 	comments_template();
	echo "</div>";
}

function parse_page_for_gallery($content){
	global $wpdb, $post;

	if(strpos(strtolower($content), "[[gallery]]") === false || strpos(strtolower($content), "<code>[[gallery]]</code>") == true) {
		return $content;
	} else {
		ob_start();
		showGallery();
		$new_content = ob_get_contents();
		ob_end_clean();

		return str_replace('[[gallery]]', $new_content, $content);
	}
}

function add_lazyest_gallery_style(){ ?>
	<link rel="stylesheet" href="<?php bloginfo('home'); ?>/wp-content/plugins/lazyest-gallery/lazyest-style.css" type="text/css" media="screen" />
	<!--[if gte IE 5.5000]>
		<style type="text/css">
			#gallery {
				position:relative;
			}
		</style>
	<![endif]-->
<?php }

// ============= Gallery Utility Functions =============

function lazyestInit() {					// Define variables values
	global $table_prefix, $gallery_address, $gallery_root, $wpdb;

	$gallery_address = get_option("lg_gallery_folder");

	// Try to gather page ID for building temporary gallery URI
	$gallery_page_ID = $wpdb->get_results("SELECT `ID`, `post_name`
													FROM $wpdb->posts
													WHERE `post_content`= \"[[gallery]]\"");
	if (!empty($gallery_page_ID)) {
		foreach($gallery_page_ID as $np){
			$gallery_ID = $np->ID;
			$gallery_page_name = $np->post_name;
		}
	}
	if (strlen(get_option('permalink_structure')) == 0){
		$gallery_temp_uri = get_option('home')."/index.php?page_id=". $gallery_ID;
	} else {
		$gallery_temp_uri = get_option('home')."/index.php/". $gallery_page_name."/";
	}

	if(!$gallery_address){
		// assume nothing is set if the gallery folder is not set
		// insert the default values here in the global options-table
		add_option('lg_gallery_folder', 'wp-gallery/', 'Lazyest Gallery: base directory');
		add_option('lg_gallery_uri', $gallery_temp_uri, 'Lazyest Gallery: uri');
		add_option('lg_excluded_folders', array('cgi-bin', 'thumbs', 'slides'), 'Lazyest Gallery: directories to exclude');
		add_option('lg_sort_alphabetically', 'TRUE', 'Lazyest Gallery: Sort files alphabetically');
		add_option('lg_pictwidth', 330, 'Lazyest Gallery: max width of a normal size image');
		add_option('lg_pictheight', 330, 'Lazyest Gallery: max height of a normal size image');
		add_option('lg_thumbwidth', 130, 'Lazyest Gallery: max width of a thumbnail image');
		add_option('lg_thumbheight', 130, 'Lazyest Gallery: max height of a thumbnail image');
		add_option('lg_thumbs_page', 16, 'Lazyest Gallery: #thumbnails to be shown on 1 page');
		add_option('lg_folders_columns', 4, 'Lazyest Gallery: #folders to be shown in 1 row');
		add_option('lg_thumbs_columns', 3, 'Lazyest Gallery: #thumbnails to be shown in 1 row');
		add_option('lg_thumb_folder', 'thumbs/', 'Lazyest Gallery: folder to store cached thumbnails');
		add_option('lg_slide_folder', 'slides/', 'Lazyest Gallery: folder to store cached slides');
		add_option('lg_folder_image', 'none', 'Lazyest Gallery: image to show for each album [none|random_image|icon]');
		add_option('lg_use_slides_popup', 'FALSE', 'Lazyest Gallery: use popup windows for slides');
		add_option('lg_disable_full_size', 'FALSE', 'Lazyest Gallery: disable link to fullsize image');
		// Advanced Stuff
		add_option('lg_enable_cache', 'FALSE', 'Lazyest Gallery: use caching for thumbnails [TRUE|FALSE]');
		add_option('lg_enable_slides_cache', 'FALSE', 'Lazyest Gallery: use caching for slides [TRUE|FALSE]');
		add_option('lg_use_cropping', 'FALSE', 'Use the cropping system for thumbnails');
		add_option('lg_buffer_size', "16M", 'Lazyest Gallery: buffer size for image processing');
		add_option('lg_resample_quality', '85', 'Lazyest Gallery resample quality');
		// Captions options
		add_option('lg_enable_captions','TRUE','Enable Captions');
		add_option('lg_use_folder_captions', 'FALSE', 'Use folder captions instead of their name');
		// Lightbox support
		add_option('lg_enable_lb_support', 'FALSE', 'Lightbox support');
		add_option('lg_force_lb_support', 'FALSE', 'Force Lightbox support');
		add_option('lg_enable_lb_thumbs_support', 'FALSE', 'Lightbox for thumbs');
		add_option('lg_enable_lb_slides_support', 'FALSE', 'Lightbox for slides');
		add_option('lg_enable_lb_sidebar_support', 'FALSE', 'Lightbox for sidebar');
		add_option('lg_enable_lb_posts_support', 'FALSE','Lightbox for posts');
		// Thickbox support
		add_option('lg_enable_tb_support', 'FALSE', 'Lightbox support');
		add_option('lg_force_tb_support', 'FALSE', 'Force Lightbox support');
		add_option('lg_enable_tb_thumbs_support', 'FALSE', 'Lightbox for thumbs');
		add_option('lg_enable_tb_slides_support', 'FALSE', 'Lightbox for slides');
		add_option('lg_enable_tb_sidebar_support', 'FALSE', 'Lightbox for sidebar');
		add_option('lg_enable_tb_posts_support', 'FALSE','Lightbox for posts');
		// ExIF default settings
		add_option('lg_enable_exif','FALSE','Enable ExIF Data');
		add_option('lg_exif_errors','FALSE','ExIF errors Data');
		add_option('lg_exif_valid_jpeg','FALSE','ExIF valid_jpeg Data');
		add_option('lg_exif_valid_jfif_data','FALSE','ExIF valid_jfif_data Data');
		add_option('lg_exif_jfif_size','TRUE','ExIF jfif_size Data');
		add_option('lg_exif_jfif_identifier','TRUE','ExIF jfif_identifier Data');
		add_option('lg_exif_jfif_extension_code','TRUE','ExIF jfif_extension_code Data');
		add_option('lg_exif_jfif_data','FALSE','ExIF jfif_data Data');
		add_option('lg_exif_valid_exif_data','FALSE','ExIF valid_exif_data Data');
		add_option('lg_exif_app1_size','TRUE','ExIF app1_size Data');
		add_option('lg_exif_endien','TRUE','ExIF endien Data');
		add_option('lg_exif_ifd0_num_tags','FALSE','ExIF ifd0_num_tags Data');
		add_option('lg_exif_ifd0_orientation','TRUE','ExIF ifd0_orientation Data');
		add_option('lg_exif_ifd0_x_res','TRUE','ExIF ifd0_x_res Data');
		add_option('lg_exif_ifd0_y_res','TRUE','ExIF ifd0_y_res Data');
		add_option('lg_exif_ifd0_res_unit','TRUE','ExIF ifd0_res_unit Data');
		add_option('lg_exif_ifd0_software','TRUE','ExIF ifd0_software Data');
		add_option('lg_exif_ifd0_time','TRUE','ExIF ifd0_time Data');
		add_option('lg_exif_ifd0_offset','TRUE','ExIF ifd0_offset Data');
		add_option('lg_exif_ifd1_main_offset','TRUE','ExIF ifd1_main_offset Data');
		add_option('lg_exif_sub_ifd_num_tags','FALSE','ExIF sub_ifd_num_tags Data');
		add_option('lg_exif_sub_ifd_color_space','TRUE','ExIF sub_ifd_color_space Data');
		add_option('lg_exif_sub_ifd_width','TRUE','ExIF sub_ifd_width Data');
		add_option('lg_exif_sub_ifd_height','TRUE','ExIF sub_ifd_height Data');
		add_option('lg_exif_ifd1_num_tags','FALSE','ExIF ifd1_num_tags Data');
		add_option('lg_exif_ifd1_compression','TRUE','ExIF ifd1_compression Data');
		add_option('lg_exif_ifd1_x_res','TRUE','ExIF ifd1_x_res Data');
		add_option('lg_exif_ifd1_y_res','TRUE','ExIF ifd1_y_res Data');
		add_option('lg_exif_ifd1_res_unit','TRUE','ExIF ifd1_res_unit Data');
		add_option('lg_exif_ifd1_jpeg_if_offset','TRUE','ExIF ifd1_jpeg_if_offset Data');
		add_option('lg_exif_ifd1_jpeg_if_byte_count','TRUE','ExIF ifd1_jpeg_if_byte_count Data');
		// Upload settings
		add_option('lg_fileupload_maxk', '300', 'Upload max file size');
		add_option('lg_fileupload_allowedtypes', 'jpg jpeg gif png', 'Allowed upload files');
		add_option('lg_fileupload_minlevel', '6', 'Minimum user level for upload');
		// Microsoft Wizard
		add_option('lg_enable_mwp_support', 'FALSE', 'Lazyest Gallery Microsoft wizard upload support');
		add_option('lg_wizard_user','test','Lazyest Gallery Wizard Username');
		add_option('lg_wizard_password','secret','Lazyest Gallery Wizard Password');
		// Image Indexing
		add_option('lg_image_indexing', '0', 'Lazyest Gallery images index to retrive comments');
	}

	// Re-define right values for the most important vars
	$gallery_root = str_replace("\\", "/", ABSPATH.get_option('lg_gallery_folder'));
	$gallery_address = get_option('siteurl')."/".get_option('lg_gallery_folder');
}

function setCurrentdir() {
	global $currentdir, $file;
	$path = pathinfo($file);
	if ($path['extension'] != ''){
		$currentdir = $path['dirname'].'/';
		if (substr($currentdir, 0, 1) == "/")
			$currentdir = substr($currentdir, 1, strlen($currentdir));
	}
	else{
		$currentdir = $file;
		if (substr($currentdir, 0, 1) == "/")
			$currentdir = substr($currentdir, 1, strlen($currentdir));

	}
}

function validateFile() {

	global $excluded_folders, $file, $gallery_root;
	$file = $_GET['file'];

	// validate dir
	if ( strstr($file, '..') || strstr($file, '%2e%2e') )
		return false;

	// check wether the file exists and can be read, only proceed if it really exists
	if(!is_readable($gallery_root.$file))
		return false;

	if(is_dir($gallery_root.$file)){
		// check wether it is a directory that is not one of the excluded folders
		$excluded_folders = get_option('lg_excluded_folders');
		foreach ($excluded_folders as $folder){
			if ( strstr($file, $folder) )
				return false;
		}
	}
	else if(!eregi(".*\.(jpg|gif|png|jpeg)", $file)){
		// this must be a regular file => it must be an image file!
		return false;
	}

	setCurrentdir();
	return true;
}

function createNavigation(){
	global $currentdir, $file, $lg_text_domain;

	$gallery_uri = get_option('lg_gallery_uri');
	// Fix for permalinks
	if (strlen(get_option('permalink_structure')) != 0){
		$gallery_uri = $gallery_uri.'?';
	} else {
		$gallery_uri = $gallery_uri.'&amp;';
	}

	if ($currentdir == './')
		$currentdir = '';

	$nav = split('/', $currentdir);
	array_pop($nav);

	$path = pathinfo($file);

	echo '<div class="top_navigator">'. __('Now Viewing:', $lg_text_domain) .' <a href="'. get_option('siteurl') .'">'. __('Home', $lg_text_domain) .'</a> &raquo; <a href="'.get_option('lg_gallery_uri').'">'. __('Gallery', $lg_text_domain) .'</a> ';

	foreach ($nav as $n) {
		$current .= $n.'/';
		$current = str_replace(" ", "%20", $current);
		echo '&raquo; <a href="'.$gallery_uri.'file='.$current.'">'.$n.'</a> ';
	}

	if ($path['extension'] != '') {
		$slideUrl = str_replace(" ", "%20", $path['basename']);
		echo '&raquo; <a href="'.$gallery_uri.'file='.$current.$slideUrl.'">'.$path['basename'].'</a>';
	}

	echo '</div>';
}

function rus2translit($string){  
    $converter = array(  
        'а' => 'a',   'б' => 'b',   'в' => 'v',  
        'г' => 'g',   'д' => 'd',   'е' => 'e',  
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',  
        'и' => 'i',   'й' => 'y',   'к' => 'k',  
        'л' => 'l',   'м' => 'm',   'н' => 'n',  
        'о' => 'o',   'п' => 'p',   'р' => 'r',  
        'с' => 's',   'т' => 't',   'у' => 'u',  
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',  
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',  
        'ь' => "'",  'ы' => 'y',   'ъ' => "'",  
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',  
  
        'А' => 'A',   'Б' => 'B',   'В' => 'V',  
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',  
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',  
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',  
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',  
        'О' => 'O',   'П' => 'P',   'Р' => 'R',  
        'С' => 'S',   'Т' => 'T',   'У' => 'U',  
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',  
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',  
        'Ь' => "'",  'Ы' => 'Y',   'Ъ' => "'",  
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',  
    );  
    return strtr($string, $converter);  
} 

function get_imgfiles ($dir = ''){
	global $gallery_root, $currentdir;

	if ($dir == '') {
		// use the currentdir in this case
		$location = $gallery_root.substr($currentdir, 0, (strlen($currentdir) -1));
		// Removing eventual trailing slash
		$category = ($currentdir{strlen($currentdir)-1} == '/')? substr($currentdir, 0, (strlen($currentdir) -1)) : $dir;
	} else {
		// use a subdirectory of the currentdir when one is given
		$location = $gallery_root.$currentdir.$dir;
		// Removing eventual trailing slash
		$category = ($dir{strlen($dir)-1} == '/')? substr($dir, 0, (strlen($dir) -1)) : $dir;
	}

	// each start we will be scan and transliterate
	// Warning! Source file should be saved in Windows-1251 encoding!
	$notSupportFilesFound = false;
        $filesList = array();
	if (file_exists($location)){
		if ($dir_contenta = opendir($location)) {
			while (false !== ($dir_filea = readdir($dir_contenta))) {
				if (is_readable($location.'/'.$dir_filea) &&
  					eregi('^.*\.(jpg|gif|png|jpeg)$', $dir_filea))
				{
					$guid = com_create_guid();

					$result = rename($location.'/'.$dir_filea, $location.'/'.'~'.$dir_filea.'~'); 
					$purified = rus2translit($dir_filea);  
					$result = rename($location.'/'.'~'.$dir_filea.'~', $location.'/'.$guid.$purified);
				} 
				else
				{
					if ($dir_filea != '.' && $dir_filea != '..' && !is_dir($location.'/'.$dir_filea))
					{
						$notSupportFilesFound = true;
						$filesList[] = $dir_filea; 
					}
				} 
			}
		}
	}
	// Output error message & delete files
	if ($notSupportFilesFound)
	{
		echo iconv('Windows-1251', 'UTF-8', "<span style='color:red'>Вы загрузили неподдерживаемые типы файлов, отличные от gif, jpeg и png. Эти файлы были удалены.</br>
		Исправьте ошибку и повторите загрузку.</br></span> 
		Список удаленных файлов:</br>");
		foreach($filesList as $filex){  
			echo '<span style="font-weight:bold">'.$filex.'</span></br>';
			unlink($location.'/'.$filex);
		}
        
	}
	// end  

	$folders = explode('/', $category);
	$temp_category = $folders[count($folders)-1];

	$imgfiles = array();
	$imgfiles_time = array();
	$img_time_combined = array();
	if (file_exists($location)){
		if ($dir_content = opendir($location)) {
			while ( false !== ($dir_file = readdir($dir_content)) ) {
				if (is_readable($location.'/'.$dir_file) &&
					eregi('^.*\.(jpg|gif|png|jpeg)$', $dir_file) &&
					!eregi("$temp_category\.(jpeg|jpg|png|gif)", $dir_file)
					){
						$imgfiles[] = $dir_file;
				}
			}
		}
	}

	if(is_array($imgfiles) && count($imgfiles) > 0) {
		for($d = 0; $d < count($imgfiles); $d++) {
			$imgfiles_time[$d] = filemtime($location.'/'.$imgfiles[$d]);
			$img_time_combined[$imgfiles[$d]] = $imgfiles_time[$d];
		}
		arsort($img_time_combined, SORT_NUMERIC);
		$imgfiles = array_keys($img_time_combined);
	}

	return $imgfiles;
}

/**
 * This function is meant to return true if some lightbox plugin is installed
 */

function some_lightbox_plugin(){
	if (
		function_exists(wp_lightbox2) ||
		function_exists(wp_lightbox_plus) ||
		function_exists(wp_lightboxJS) ||
		function_exists(lightbox_styles)
	){
		return true;
	} else {
		return false;
	}
}

/**
 * This function is meant to return true if some thickbox plugin is installed
 */

function some_thickbox_plugin() {
	if (
		function_exists(ThickBox_init)
	){
		return true;
	} else {
		return false;
	}
}

// ================= Admin Options Page ================

function gallery_add_pages() {		// Calls the admin panel file
	global $user_level;

	// gallery_add_pages() is the sink function for the 'admin_menu' hook
	$upload_level = get_option('lg_fileupload_minlevel');
	if ($user_level >= $upload_level) {
		add_menu_page('Lazyest Gallery', 'Lazyest Gallery', $upload_level, 'lazyest-gallery/lazyest-admin.php');
		add_submenu_page('lazyest-gallery/lazyest-admin.php', 'Lazyest Gallery', 'File Manager', $upload_level, 'lazyest-gallery/lazyest-filemanager.php', lg_build_captions_form);
	}
	if (get_option('lg_enable_mwp_support') == "TRUE") {
		add_submenu_page('lazyest-gallery/lazyest-admin.php', 'Lazyest Gallery', 'Wizard', 10, 'lazyest-gallery/lazyest-wizard.php', lg_wizard_form);
	}
}

function lazyest_write_quicktag() {

	if(!function_exists('edit_insert_button')) {
		//edit_insert_button: Inserts a button into the editor
		function edit_insert_button($caption, $js_onclick, $title = '') {
		?>
			if(toolbar) {
				var theButton = document.createElement('input');
				theButton.type = 'button';
				theButton.value = '<?php echo $caption; ?>';
				theButton.onclick = <?php echo $js_onclick; ?>;
				theButton.className = 'ed_button';
				theButton.title = "<?php echo $title; ?>";
				theButton.id = "<?php echo "ed_{$caption}"; ?>";
				toolbar.appendChild(theButton);
			}
		<?php

		}
	}


	if(strpos($_SERVER['REQUEST_URI'], 'post-new.php') ||
		strpos($_SERVER['REQUEST_URI'], 'post.php') ||
		strpos($_SERVER['REQUEST_URI'], 'page-new.php')) {
	// [[Image:image/name.jpg|alignment|width|height|Caption or description]]
	?>
		<script type="text/javascript">//<![CDATA[
			var toolbar = document.getElementById("ed_toolbar");
		<?php
			edit_insert_button("gallery", "lazyest_quick_buttons", "Lazyest Gallery Smartlinks");
		?>

		var state_my_button = true;

		function lazyest_quick_buttons() {

			if(state_my_button) {

				var ImgURL = prompt('Enter the relative image URL starting from gallery root (excluded) [required and case sensitive]');
				var Caption = prompt('Enter image caption [optional]');
				var Align = prompt('Enter alignment (center, right or left) [optional]', 'center');
				var Width = prompt('Enter Width of image thumbnail [optional]', '<?php get_option('lg_thumbwidth'); ?>');
				var Height = prompt('Enter Height of image thumbnail [optional]', '<?php get_option('lg_thumbheight'); ?>');
				if (ImgURL) {
					myValue = '[[Image:'+ImgURL;
					if (Align) {
						myValue += '|'+Align;
					}
					if (Width) {
						myValue += '|'+Width;
					}
					if (Height) {
						myValue += '|'+Height;
					}
					if (Caption) {
						myValue += '|'+Caption;
					}
					myValue += ']]';
					edInsertContent(edCanvas, myValue);
				}
			}
		}
		//]]></script>

		<?php
	}
}

// ================= Captions Functions ================

/**
 * This function read from an XML file and return a string with
 * the folder caption if the file exists and "false" if it doesn't.
 * NOTE: This is an "util" function, so do not call directly,
 * call instead clean_folder_caption()
 */

function get_folder_caption($dir) {
	global $gallery_root;

	if (substr($dir, strlen($dir)-1, strlen($dir)) != "/")
		$dir .= '/';

	if (!file_exists($gallery_root.$dir.'captions.xml')) { // This will prevents warnings
		return false;
	}

	// Build essential informations gathering them from the xml file and store into "$arrOutput"
	$data = implode("", file($gallery_root.$dir.'captions.xml'));
	$objXML = new lg_xml2Array();
	$arrOutput = $objXML->parse($data);

	// Gather the caption
	for ($i = 0; $i < count($arrOutput[0][children]); $i++){
		$folder_caption = $arrOutput[0][children][0][tagData];
	}
	return $folder_caption;
}

/**
 * This function removes or rebuilds HTML tags (depending the need).
 * If $verse is "true" (default) HTML tags are restored
 * If $verse is "false" HTML tags are converted from "<" and ">" to "[" and "]"
 * It returns false if it fail gather folder caption
 * NOTE: This function returns a string
 */

function clean_folder_caption($dir, $verse = true){

	if (!($xml_caption = get_folder_caption($dir))){
		return false;
	}

	if ($verse) {
		$xml_caption = str_replace('"', '\'',  $xml_caption);
		$xml_caption = str_replace('[', '<',  $xml_caption);
		$xml_caption = str_replace(']', '>',  $xml_caption);
	} else {
		$xml_caption = str_replace('"', '\'',  $xml_caption);
		$xml_caption = str_replace('<', '[',  $xml_caption);
		$xml_caption = str_replace('>', ']',  $xml_caption);
	}

	return utf8_decode($xml_caption);
}

/**
 * This function read from an XML file and return an associative array
 * if the file exists and "false" if it doesn't.
 * NOTE: This is an "util" function, so do not call directly,
 * call instead clean_image_caption()
 * NOTE: This function returns an array
 */

function get_image_caption($imagedir) {
	global $gallery_root;

	if (!file_exists($gallery_root.$imagedir.'captions.xml')) { // This will prevents warnings
		return false;
	}

	// Build essential informations gathering them from the xml file and store into "$arrOutput"
	$data = implode("", file($gallery_root.$imagedir.'captions.xml'));
	$objXML = new lg_xml2Array();
	$arrOutput = $objXML->parse($data);

	// Build the array and return it
	$images = array();
	for ($i = 0; $i < count($arrOutput[0][children]); $i++){
		$images[$i][image] = $arrOutput[0][children][$i][attrs][ID];
		$images[$i][caption] = $arrOutput[0][children][$i][children][0][tagData];
		$images[$i][id] = $arrOutput[0][children][$i][children][1][tagData];
	}

	return $images;
}

/**
 * This function removes or rebuilds HTML tags (depending the need).
 * If $verse is "true" (default) HTML tags are restored
 * If $verse is "false" HTML tags are converted from "<" and ">" to "[" and "]"
 * It returns false if it fail gather image captions
 * NOTE: This function returns a string
 */

function clean_image_caption($img, $dir, $verse = true){

	if (!($images = get_image_caption($dir))){
		return false;
	}

	for ($i = 0; $i < count($images); $i++) {
		if ($images[$i][image] == $img){
			$xml_caption = $images[$i][caption];
		}
	}

	if ($verse) {
		$xml_caption = str_replace('"', '\'',  $xml_caption);
		$xml_caption = str_replace('[', '<',  $xml_caption);
		$xml_caption = str_replace(']', '>',  $xml_caption);
	} else {
		$xml_caption = str_replace('"', '\'',  $xml_caption);
		$xml_caption = str_replace('<', '[',  $xml_caption);
		$xml_caption = str_replace('>', ']',  $xml_caption);
	}

	return utf8_decode($xml_caption);
}

// ================= Access level functions ============

function get_minimum_folder_level($dir){

	global $gallery_root;

	if (substr($dir, strlen($dir)-1, strlen($dir)) != "/")
		$dir .= '/';

	if (!file_exists($gallery_root.$dir.'captions.xml')) { // This will prevents warnings
		return false;
	}

	// Build essential informations gathering them from the xml file and store into "$arrOutput"
	$data = implode("", file($gallery_root.$dir.'captions.xml'));
	$objXML = new lg_xml2Array();
	$arrOutput = $objXML->parse($data);

	// Gather the caption
	for ($i = 0; $i < count($arrOutput[0][children]); $i++){
		$folder_level = $arrOutput[0][children][1][tagData];
	}
	if ($folder_level == "")
		$folder_level = 1;
	return $folder_level;
}

function lg_user_can_access($folder){
	global $user_level;

	// This is for gallery_root
	if ($folder == "") {
		return true;
	}

	$upper_level = explode('/', $folder);

	for ($i = 0; $i < (count($upper_level)-2); $i++){
		$top .= $upper_level[$i].'/';
	}

	$folder_level = get_minimum_folder_level($folder);
	if (($user_level >= $folder_level || $folder_level == 1)  && lg_user_can_access($top)) {
		return true;
	} else {
		return false;
	}
}

// ===================== Post Functions ================

/**
 * This function is used to filter the post content
 * and replace particulars tags with the right sobstitution code
 * NOTE:
 * [[Image:image/name.ext|align|width|height|Caption or description]]
 *        |      0       |  1  |  2  |  3   |          4           |
 * words[]:
 * [0] image file (path) Obligatory
 * [1] alignment
 * [2] width
 * [3] height
 * [4] caption
 */

function parse_posts_for_images($content){
	global $wpdb, $post, $gallery_root, $gallery_address;

	// Check if in the post there are [[image]]'s tags, if not content is returned else it is processed
	if(strpos(strtolower($content), "[[image") === false || strpos(strtolower($content), "<code>[[image") == true) {
		return $content;
	} else {

		$gallery_uri = get_option('lg_gallery_uri');
		// Fix for permalinks
		if (strlen(get_option('permalink_structure')) != 0){
			$gallery_uri = $gallery_uri.'?';
		} else {
			$gallery_uri = $gallery_uri.'&amp;';
		}
		$imagedata = array();
		$posstags = spliti("\\[\\[Image:", $content);
		$new_content = $content;

		// Search for tags
		for ($i = 0; $i <= count($posstags); $i++){
			$smartlink = array();
			eregi('([^\[]*)\\]\\]', $posstags[$i], $smartlink);
			if (strlen($smartlink[1]) > 0)
				$imagedata[] = $smartlink[1];
		}

		// Process tags
		foreach ($imagedata as $imgdata){
			$folder = "";
			if (isset($imgdata)) {
				if(strpos($content, $imgdata) === false) {
					return $content;
				}

				$words = explode("|", $imgdata);

				// Trimming spaces
				for($i = 0; $i < count($words); $i++) {
					$words[$i] = trim($words[$i]);
				}

				// extracts the image from the array
				$words = array_reverse($words);
				$image  = array_pop($words);

				$width = '';
				$height = '';
				$align = array("center","left","right");
				$done = false;
				$alt = '';

				// assigning the right values
				foreach($words as $key){
					if (is_numeric($key) && $width == ''){
						$width = $key;
					} else if (is_numeric($key) && $height == ''){
						$height = $key;
					} else if (!$done && in_array($key, $align)){
						$align = $key;
						$done = true;
					} else {
						$alt = $key;
					}
				}

				if ($width == '') {
					$height = get_option('lg_thumbheight');
					$width = get_option('lg_thumbwidth');
				} else if($height == '') {
					$height = $width;
				}

				// Fixing CSS issues
				if ($align == "center"){
					$center_style = "margin:auto !important;";
				}
				$thumb_width = "width:".($width + 2)."px;";
				// End of CSS issue

				/* ========================
				 * Sobstitution code header
				 * ======================== */

				 $url = "<div class='thumb t$align'>
							<div style='$thumb_width$center_style' >";

				/* ===========================
				 * Gathering misc informations
				 * =========================== */

				$img = basename($image);
				$folder = str_replace($img, "", $image);
				$title = $alt;
				if (strlen($title) == 0){
					$caption = clean_image_caption($img, $folder);
					$title = ereg_replace("<[^>]*>", "", $caption);
				}
				$xhtml_url = str_replace(" ", "%20", $image);

				// Cache System code (used for lightbox)
				$slide_folder = get_option('lg_slide_folder');

				if (file_exists($gallery_root.$folder.$img)){
					if(!file_exists($gallery_root.$folder.$slide_folder.$img) && strlen($image) > 0){
						// Not thumb = false
						createCache($folder, $img, false);
					}
				}
				$urlImg = str_replace(" ", "%20", $gallery_address.$folder.$slide_folder.$img);

				/* =============
				* The Right URL
				* ============= */

				// Lightbox Informations
				$lb_enabled = get_option('lg_enable_lb_support');
				$lb_posts = get_option('lg_enable_lb_posts_support');
				$lb_force = get_option('lg_force_lb_support');

				// Thickbox informations
				$tb_enabled = get_option('lg_enable_tb_support');
				$tb_posts = get_option('lg_enable_tb_posts_support');
				$tb_force = get_option('lg_force_tb_support');

				// Slides' cache infos
				$lg_scache = get_option('lg_enable_slides_cache');

				// Lightbox code
				if ($lb_enabled == "TRUE" && $lb_posts == "TRUE" && $lg_scache == "TRUE" && some_lightbox_plugin() || $lb_force == "TRUE") {
					$url .= "<a href='$urlImg' rel='lightbox' title='$title' class='internal'>";
				}
				// Thickbox code
				elseif ($tb_enabled == "TRUE" && $tb_posts == "TRUE" && $lg_scache == "TRUE" && some_thickbox_plugin() || $tb_force == "TRUE") {
					$url .= "<a href='$urlImg' class='thickbox' title='$title'>";
				}
				// No Lightbox code
				else {
					$url .= "<a href='$gallery_uri"."file=$xhtml_url' class='internal'>";
				}

				/* ================
				* End of Right URL
				* ================ */

				/* ========================
				 * Sobstitution code footer
				 * ======================== */

				 $url .= "<img src='".get_option('siteurl')."/wp-content/plugins/lazyest-gallery/lazyest-thumbnailer.php?file=$xhtml_url&amp;height=$height&amp;width=$width' alt='$alt'/>
								</a>
								<div class='thumbcaption'>
									<div class='gallerylink' style='float:right'><a href='$gallery_uri"."file=$xhtml_url' class='internal' >
										<img src='".get_option('siteurl')."/wp-content/plugins/lazyest-gallery/images/magnify-clip.png' alt='' />
									</a></div>
									$title
								</div>
							</div>
						</div>";

				if (phpversion() >= 5)
					$new_content = str_ireplace("[[image:".$imgdata."]]", $url, $new_content);
				else
					$new_content = str_replace("[[Image:".$imgdata."]]", $url, $new_content);
			}
		}
		return $new_content;
	}
}

function add_post_thumbs_style(){
	?>
<link rel="stylesheet" href="<?php echo bloginfo('home'); ?>/wp-content/plugins/lazyest-gallery/lazyest-thumbs-style.css" type="text/css" media="screen" />
	<?php
}

// =================== Sidebar Functions ===============

function lg_list_folders($title, $root = ''){
	global $gallery_root, $user_level;

	if ($user_level == '') {
		$user_level = 1;
	}
	if ($root == '') {
		$root = $gallery_root;
		echo "\n".$title;
	}

	$gallery_uri = get_option('lg_gallery_uri');
	// Fix for permalinks
	if (strlen(get_option('permalink_structure')) != 0){
		$gallery_uri = $gallery_uri.'?';
	} else {
		$gallery_uri = $gallery_uri.'&amp;';
	}

	$images = array();

	// Open gallery root
	if ($dir_handler = opendir($root)) {

		$forbidden = get_option('lg_excluded_folders');
		array_push($forbidden, "..");
		array_push($forbidden, ".");
		array_push($forbidden, "captions.xml");

		while ($file = readdir($dir_handler)) {
			if (!in_array($file, $forbidden) && is_dir($root.$file.'/')) {
				echo "\n<ul><li>\n\t";
			}
			if ($user_level < get_minimum_folder_level($file))
				array_push($forbidden, $file);
			if (!in_array($file, $forbidden)) {
				// Do not remove the trailing slash (/)
				if (is_dir($root.$file.'/')) {
					$filelink = explode($gallery_root, $root.$file);
					echo '<a href="'.$gallery_uri.'file='.$filelink[1].'/">'.$file.'</a>'."\n";
					lg_list_folders($title, $root.$file.'/');
				}
			}
			if (!in_array($file, $forbidden) && is_dir($root.$file.'/')) {
				echo "\n</li></ul>\n\t";
			}
		}

	} else {
		echo "\n<ul><li>\n\t";
			_e('Cannot open gallery root', $lg_text_domain);
		echo "\n</li></ul>\n\t";
	}

}

/**
 * This function is used in the sidebar to display a random image
 * from the gallery
 */

function lg_random_image($title){
	global $gallery_address, $gallery_root, $user_level, $lg_text_domain, $wp_rewrite;

	$gallery_uri = get_option('lg_gallery_uri');
	// Fix for permalinks
	if ($wp_rewrite->using_permalinks()){

		$gallery_uri = $gallery_uri.'?';
	} else {
		$gallery_uri = $gallery_uri.'&amp;';
	}

	if ($dir_content = opendir($gallery_root)) {

		echo $title;

		// Gather image files
		$imgfiles = lg_build_img_array();
		// Get a random image
		$img = $imgfiles[rand(0,sizeof($imgfiles)-1)];

		// Keep track of the folders and separate from file (due to use of "thumbs folder")
		$img_file_path = explode('/', $img);
		$img_index = count($img_file_path)-1;
		for ($i = 0; $i < count($img_file_path)-1; $i++) {
			$folder .= $img_file_path[$i].'/';
			// now $folder contains the path to the file without the filename
			// ie: some/where/
		}
		$img_file = $img_file_path[$img_index];
		// $img_file contains now only the file name

		echo '<div id="lazyest_sidebox">'."\n";

		// Slide's Cache System code (used for lightbox)
		$slide_folder = get_option('lg_slide_folder');

		// Gather Captions infos
		if( file_exists($gallery_root.$folder.'captions.xml')){
			$caption = clean_image_caption($img_file, $folder);
		}

		$title = ereg_replace("<[^>]*>", "", $caption);

		if(!file_exists($gallery_root.$folder.$slide_folder.$img_file) && strlen($img_file) > 0){
			createCache($folder, $img_file, false);
		}

		/* =============
		 * The Right URL
		 * ============= */

		// Lightbox Informations
		$lb_enabled = get_option('lg_enable_lb_support');
		$lb_sidebar = get_option('lg_enable_lb_sidebar_support');
		$lb_force = get_option('lg_force_lb_support');

		// Thickbox informations
		$tb_enabled = get_option('lg_enable_tb_support');
		$tb_sidebar = get_option('lg_enable_tb_sidebar_support');
		$tb_force = get_option('lg_force_tb_support');

		// Slides' cache infos
		$lg_scache = get_option('lg_enable_slides_cache');

		// Image URL
		$urlImg = str_replace(" ", "%20", $gallery_address.$folder.$slide_folder.$img_file);

		// Lightbox code
		if ($lb_enabled == "TRUE" && $lb_sidebar == "TRUE" && $lg_scache == "TRUE" && some_lightbox_plugin() || $lb_force == "TRUE") {
			echo "<a href='$urlImg' rel='lightbox' title='$title'>";
		}
		// Thickbox code
		elseif ($tb_enabled == "TRUE" && $tb_sidebar == "TRUE" && $lg_scache == "TRUE" && some_thickbox_plugin() || $tb_force == "TRUE") {
			echo '<a href="'.$urlImg.'" class="thickbox" title="'. $title . ' ">';
		}
		// Normal code
		else {
			// $img contains the full path to the file starting from $gallery_root (excluded)
			$urlImg = str_replace(" ", "%20", $img);
			echo '<a href="'.$gallery_uri.'file='.$urlImg.'">'."\n";
		}

		/* ================
		 * End of Right URL
		 * ================ */

		/* ==============
		 * Thumbnail code
		 * ============== */

		if ($img) {
			// Gather Captions infos
			if( file_exists($gallery_root.$folder.'captions.xml')){
				$caption = clean_image_caption($img_file, $folder);
			} else {
				$caption = "Click to visit my gallery!";
			}
			// This will remove HTML tags (which is incompatible with "title" argument)
			$title = ereg_replace("<[^>]*>", "", $caption);

			// Cache System code
			if(get_option('lg_enable_cache') == "TRUE"){
				$thumb_folder = get_option('lg_thumb_folder');

				if(!file_exists($gallery_root.$folder.$thumb_folder.$img)){
					createCache($folder, $img_file, true);
				}
				$url = str_replace(" ", "%20", $gallery_address.$folder.$thumb_folder.$img_file);
				echo '<img src="'.$url.'"  alt="'.$img.'" title="'. $title . '"/>';
			}
			// "On the fly" code
			else{
				$urlImg =  str_replace(" ", "%20", $img);
				echo '<img src="'.get_option('siteurl').'/wp-content/plugins/lazyest-gallery/lazyest-img.php?file='.$urlImg.'&amp;thumb=1"  style="border: 1px solid #ccc; vertical-align:middle;padding:2px;"  alt="'.$img.'" title="'. $title . '" />';
			}
		}
		echo "</a></div>";
	}

}

function lg_build_img_array($root = ''){
	global $gallery_root, $user_level;

	if ($user_level == '') {
		$user_level = 1;
	}
	if ($root == '') {
		$root = $gallery_root;
	}
	$images = array();
	// Open gallery root
	if ($dir_handler = opendir($root)) {

		$forbidden = get_option('lg_excluded_folders');
		array_push($forbidden, "..");
		array_push($forbidden, ".");
		array_push($forbidden, "captions.xml");

		while ($file = readdir($dir_handler)) {
			if ($user_level < get_minimum_folder_level($file))
				array_push($forbidden, $file);
			if (!in_array($file, $forbidden)) {
				// Do not remove the trailing slash (/)
				if (is_dir($root.$file.'/')) {
					$images = array_merge($images, lg_build_img_array($root.$file.'/'));
				} else {

					// gather informations for icons excluding
					$path_parts = pathinfo($root.$file);
					$folder = $path_parts['dirname'];
					$separate_folders = explode("/", $folder);
					$folder = $separate_folders[count($separate_folders)-1];

					// excluding icons files
					if ($folder != basename($file, ".gif") &&
						$folder != basename($file, ".png") &&
						$folder != basename($file, ".jpg") &&
						$folder != basename($file, ".jpeg") &&
						eregi(".*\.(jpg|gif|png|jpeg)",  basename($file))) {

						// builds the relative path
						$relpath = explode($gallery_root, $root.$file);
						$images[] = $relpath[1];

					}
				}
			}
		}
		return $images;

	} else {
		_e('Cannot open gallery root', $lg_text_domain);
	}
}

/**
 * This function returns an array where all gallery's folders are stored
 * it excludes also forbidden folders and files
 */

function lg_build_folders_array($root = ''){
	global $gallery_root, $user_level;

	if ($user_level == '') {
		$user_level = 1;
	}
	if ($root == '') {
		$root = $gallery_root;
	}
	$images = array();
	// Open gallery root
	if ($dir_handler = opendir($root)) {

		$forbidden = get_option('lg_excluded_folders');
		array_push($forbidden, "..");
		array_push($forbidden, ".");
		array_push($forbidden, "captions.xml");

		while ($file = readdir($dir_handler)) {
			if ($user_level < get_minimum_folder_level($file))
				array_push($forbidden, $file);
			if (!in_array($file, $forbidden)) {
				// Do not remove the trailing slash (/)
				if (is_dir($root.$file.'/')) {
					$images[] = $root.$file;
					$images = array_merge($images, lg_build_folders_array($root.$file.'/'));
				} else {
					continue;
				}
			}
		}

		return $images;

	} else {
		_e('Cannot open gallery root', $lg_text_domain);
	}
}

function debug($var) {
	if(is_array($var)) {
		echo "<pre>";
		print_r($var);
		echo "</pre>";
	}
	else
		var_dump($var);
}

?>
