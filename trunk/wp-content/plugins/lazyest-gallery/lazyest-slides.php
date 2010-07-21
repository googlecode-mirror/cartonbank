<?php

function showSlide($slidefile) {		// Builds slides view page

	global $gallery_root, $gallery_address, $currentdir, $file, $user_level, $lg_text_domain;

	$folder_level = get_minimum_folder_level($currentdir);

	if (!lg_user_can_access($currentdir)) {
		echo "<p>";
		_e("Are You Cheatin&#8217; uh?", $lg_text_domain);
		echo "</p>";
	} else {


		$gallery_uri = get_option('lg_gallery_uri');
		// Fix for permalinks
		if (strlen(get_option('permalink_structure')) != 0){
			$gallery_uri = $gallery_uri.'?';
		} else {
			$gallery_uri = $gallery_uri.'&amp;';
		}

		$imgfiles = get_imgfiles($dir);

		if (get_option('lg_sort_alphabetically') == "TRUE")
			sort($imgfiles);

		// Pager Code
		$prev = '';
		$slide = '';
		$next = '';

		$arraysize = count($imgfiles);

		for ($i = 0; $i < $arraysize; $i++) {
			if($currentdir.$imgfiles[$i] == $slidefile) {
				$slide = $imgfiles[$i];
				// Set prev
				if($i == 0) {
					$prev = $imgfiles[$arraysize-1];
				} else {
					$prev = $imgfiles[$i-1];
				}
				// Set Next
				if($i+1 == $arraysize) {
					$next = $imgfiles[0];
				} else {
					$next = $imgfiles[$i+1];
				}
				break;
			}
		}
		// XHTML formatting
		$prev = str_replace(" ", "%20", $prev);
		$next = str_replace(" ", "%20", $next);
		$slideUrl = str_replace(" ", "%20", $slide);

		/* ========================
		*		Navigator HTML Code
		* ======================== */

		// This is because XHTML compiling
		$currdir = str_replace(" ", "%20", $currentdir);

		echo '<div id="lazyest_navigator">
					<a href="'.$gallery_uri.'file='.$currdir.$prev.'" class="alignleft">'. __('&laquo; Prev', $lg_text_domain).'</a>
					<a href="'.$gallery_uri.'file='.$currdir.$next.'" class="alignright">'. __('Next &raquo;',$lg_text_domain) .'</a>
				</div>';

			/* =====================
			*		Image slide Code
			* ===================== */

		// Removes HTML tags
		$title = ereg_replace("<[^>]*>", "", $caption);

		echo '<div class="lazyest_image">';


		if(get_option('lg_use_slides_popup') == "TRUE") {
			//PopUp Window
			// Get picture info
			$img = $gallery_root.$currentdir.$slide;
			$path = pathinfo($img);

			// this will prevent some unshown thumb
			$mem = get_option('lg_buffer_size');
			ini_set("memory_limit", $mem);

			switch(strtolower($path["extension"])){
				case "jpeg":
				case "jpg":
					$img = imagecreatefromjpeg($img);
					break;
				case "gif":
					$img = imagecreatefromgif($img);
					break;
				case "png":
					$img = imagecreatefrompng($img);
					break;
				default:
					break;
			}
			$xsize = (imagesx($img));
			$ysize = (imagesy($img));
			imagedestroy($img);
		}

		if(get_option('lg_enable_slides_cache') == "TRUE") {

			$slidesfolder = get_option('lg_slide_folder');
			if(!file_exists($gallery_root.$currentdir.$slidesfolder.$slide)) {
				createCache($currentdir, $slide, false);
			}

			if(get_option('lg_use_slides_popup') == "TRUE") {
				// Popup
				echo '<a href="javascript:void(window.open(\''. get_settings('home') .'/wp-content/plugins/lazyest-gallery/lazyest-popup.php?image='.$slide.'&folder='.$currentdir.'\',\'\',\'resizable=no,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,fullscreen=no,dependent=yes,width='.$xsize.',height='.$ysize.',left=50,top=50\'))"><img src="'.$gallery_address.$currentdir.$slidesfolder.$slide .'" alt="'.$slide.'" title="'. $title .'"/></a>';
			} else {

				if( file_exists($gallery_root.$currentdir.'captions.xml')){
					$caption = clean_image_caption($slide, $currentdir);
				}

				// Removing HTML tags
				$title = ereg_replace("<[^>]*>", "", $caption);

				// Lightbox informations
				$lb_enabled = get_option('lg_enable_lb_support');
				$lb_slides = get_option('lg_enable_lb_slides_support');
				$lb_force = get_option('lg_force_lb_support');

				// Thickbox informations
				$tb_enabled = get_option('lg_enable_tb_support');
				$tb_slides = get_option('lg_enable_tb_slides_support');
				$tb_force = get_option('lg_force_tb_support');

				// Slides' hrefs
				$hrefs = true;
				if (get_option('lg_disable_full_size') == "TRUE")
					$hrefs = false;

				// Slides' cache infos
				$lg_cache = get_option('lg_enable_slides_cache');

				// Image link
				$urlImg = str_replace(" ", "%20", $gallery_address.$currentdir.$slide);

				// Lightbox
				if ($lb_enabled == "TRUE" && $lb_slides == "TRUE" && $lg_cache == "TRUE" && some_lightbox_plugin() || $lb_force == "TRUE") {
					if ($hrefs)
						echo '<a href="'.$urlImg.'" rel="lightbox" title="'.$title.'">';
					echo '<img src="'.$gallery_address.$currdir.$slidesfolder.$slide .'" alt="'.$slide.'"  title="'. $title . '" />';
					if ($hrefs)
						echo '</a>';
				}
				// Thickbox
				elseif ($tb_enabled == "TRUE" && $tb_slides == "TRUE" && $lg_cache == "TRUE" && some_thickbox_plugin() || $tb_force == "TRUE") {
					if ($hrefs)
						echo '<a href="'.$urlImg.'" class="thickbox" title="'. $title . ' ">';
					echo '<img src="'. $gallery_address.$currdir.$slidesfolder.$slide .'" alt="'.$slide.'"   />';
					if ($hrefs)
						echo '</a>';
				}
				// In window
				else {
					if ($hrefs)
						echo '<a href="'.$urlImg.'" >';
					echo '<img src="'.$gallery_address.$currdir.$slidesfolder.$slideUrl .'" alt="'.$slide.'"  title="'. $title . '" />';
					if ($hrefs)
						echo '</a>';
				}
			}

		} else {
			if(get_option('lg_use_slides_popup') == "TRUE") {
				// PopUp
				echo '<a href="javascript:void(window.open(\''.$gallery_address.$currentdir.$slide.'\',\'\',\'resizable=no,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,fullscreen=no,dependent=yes,width='.$xsize.',height='.$ysize.',left=50,top=50\'))"><img src="'.get_option('siteurl').'/wp-content/plugins/lazyest-gallery/lazyest-img.php?file='.$currentdir.$slide.'" alt="'.$currentdir.$slide.'" title="'. $title .'" /></a>';
			} else {
				// In window
				echo '<a href="'.$gallery_address.$currdir.$slideUrl.'" ><img src="'.get_option('siteurl').'/wp-content/plugins/lazyest-gallery/lazyest-img.php?file='.$currdir.$slideUrl.'" alt="'.$currentdir.$slide.'" title="'. $title .'" /></a>';
			}
		}

		/* =============
		 * Captions Code
		 * ============= */

		if(get_option('lg_enable_captions') == "TRUE"){
			$caption = clean_image_caption($slide, $currentdir);
			if (strlen($caption) != 0){
				echo '<div class="caption">'. $caption . '</div>';
			}
		}

		echo '</div><br/>';	// closes "image" class

			/* =========================
			*		Image Exif Data Code
			* ========================= */

		if(get_option('lg_enable_exif') == "TRUE") {
			include_once('lazyest-exinfos.php');
		}

		if (get_option('lg_enable_captions') == "TRUE") {
			get_currentuserinfo();
			if (strlen($currentdir) !=0){
				if ($user_level >= 8) {
					echo "<div class='lg_admin'>";
					echo "<a href='".get_settings('siteurl')."/wp-admin/".LG_FLM_PAGE."&amp;captions=".$currdir."'>";
					echo "&raquo; ". __('Write a caption for ', $lg_text_domain) . $slide;
					echo "</a>";
					echo "</div>";
				}
			}
		}
	}
}
?>