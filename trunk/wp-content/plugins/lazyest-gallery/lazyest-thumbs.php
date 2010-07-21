<?php

function showThumbs() {					// Builds thumbnails view page

	global $gallery_root, $currentdir, $file, $gallery_address, $user_level, $lg_text_domain;

	if (!lg_user_can_access($currentdir)) {
		echo "<p>";
		_e('You cannot browse this folder.', $lg_text_domain);
		echo "</p>";
	} else {

		$gallery_uri = get_option('lg_gallery_uri');

		// Fix for permalinks
		if (strlen(get_option('permalink_structure')) != 0){
			$gallery_uri = $gallery_uri.'?';
		} else {
			$gallery_uri = $gallery_uri.'&amp;';
		}

		// gathering pagination infos
		$thumbs_page = get_option('lg_thumbs_page');
		if($thumbs_page != 0 && isset($_GET['offset']) && is_numeric($_GET['offset'])){
			$offset = floor(abs(intval($_GET['offset']))/$thumbs_page)*$thumbs_page;
		}
		else{
			$offset = 0;
		}

		$folder_caption = clean_folder_caption($currentdir.$dir);
		$category = substr($currentdir, 0, (strlen($currentdir) -1));

		// Icons code
		// This section will display proper icon for each folder
		echo "<div class='folder_caption'>";

			if (is_file($gallery_root.$currentdir.$category.'.png')){
				$resource = imagecreatefrompng($gallery_root.$currentdir.$category.'.png');
				if (imagesx($resource) > get_option('lg_thumbwidth'))
					echo '<img src="'.get_option('siteurl').'/wp-content/plugins/lazyest-gallery/lazyest-img.php?file='.$currentdir.$category.'.png'.'&amp;thumb=1" alt="" class="icon" />&raquo; '.$folder_caption."\n";
				else
					echo '<img src="'.$gallery_address.$currentdir.$category.'.png"  alt="'.$category.'" class="icon"/> &raquo; '.$folder_caption;
			}
			else if (is_file($gallery_root.$currentdir.$category.'.jpg')){
				$resource = imagecreatefromjpeg($gallery_root.$currentdir.$category.'.jpg');
				if (imagesx($resource) > get_option('lg_thumbwidth'))
					echo '<img src="'.get_option('siteurl').'/wp-content/plugins/lazyest-gallery/lazyest-img.php?file='.$currentdir.$category.'.jpg'.'&amp;thumb=1" alt="" class="icon" />&raquo; '.$folder_caption."\n";
				else
					echo '<img src="'.$gallery_address.$currentdir.$category.'.jpg"  alt="'.$category.'" class="icon"/> &raquo; '.$folder_caption;
			}
			else if (is_file($gallery_root.$currentdir.$category.'.jpeg')){
				$resource = imagecreatefromjpeg($gallery_root.$currentdir.$category.'.jpeg');
				if (imagesx($resource) > get_option('lg_thumbwidth'))
					echo '<img src="'.get_option('siteurl').'/wp-content/plugins/lazyest-gallery/lazyest-img.php?file='.$currentdir.$category.'.jpeg'.'&amp;thumb=1" alt="" class="icon" />&raquo; '.$folder_caption."\n";
				else
					echo '<img src="'.$gallery_address.$currentdir.$category.'.jpeg" alt="'.$category.'" class="icon"/> &raquo; '.$folder_caption;
			}
			else if (is_file($gallery_root.$currentdir.$category.'.gif')){
				$resource = imagecreatefromgif($gallery_root.$currentdir.$category.'.gif');
				if (imagesx($resource) > get_option('lg_thumbwidth'))
					echo '<img src="'.get_option('siteurl').'/wp-content/plugins/lazyest-gallery/lazyest-img.php?file='.$currentdir.$category.'.gif'.'&amp;thumb=1" alt="" class="icon" />&raquo; '.$folder_caption."\n";
				else
					echo '<img src="'.$gallery_address.$currentdir.$category.'.gif"  alt="'.$category.'" class="icon"/> &raquo; '.$folder_caption;
			}

		echo "</div>";
		// End of Icons code

		// Here begins thumbs table
		echo '<table class="lazyest_thumb_view" summary="thumbs"><tr>';

		$tcol_count = 1;
		$tcolumns = get_option('lg_thumbs_columns');
		$imgfiles = get_imgfiles($dir);

		// Sort the files
		if (get_option('lg_sort_alphabetically') == "TRUE")
			sort($imgfiles);

		if(isset($imgfiles)) {
			if($thumbs_page == 0){
				// no pagination, display all
				$end = count($imgfiles);
				$offset = 0;
			}
			else{
				$end = $thumbs_page + $offset ;
				if(count($imgfiles) < $end ){
					$end = count($imgfiles);
				}
			}

			// This is because XHTML compiling
			$currdir = str_replace(" ", "%20", $currentdir);

			// Main cycle
			for ($i = $offset; $i < $end; $i++) {
				$img = $imgfiles[$i];

				if( file_exists($gallery_root.$currentdir.'captions.xml')){
					$caption = clean_image_caption($img, $currentdir);
				}

				// Removing HTML tags
				$title = ereg_replace("<[^>]*>", "", $caption);

				// Tumbs Cache code
				if(get_option('lg_enable_cache') == "TRUE"){
					if(!file_exists($gallery_root.$currentdir.get_option('lg_thumb_folder').$img)) {
						createCache($currentdir, $img, true);
					}

					/* =============
					 * The Right URL
					 * ============= */

					// Lightbox informations
					$lb_enabled = get_option('lg_enable_lb_support');
					$lb_thumbs = get_option('lg_enable_lb_thumbs_support');
					$lb_force = get_option('lg_force_lb_support');
					// Thickbox informations
					$tb_enabled = get_option('lg_enable_tb_support');
					$tb_thumbs = get_option('lg_enable_tb_thumbs_support');
					$tb_force = get_option('lg_force_tb_support');
					// Slides' cache infos
					$lg_scache = get_option('lg_enable_slides_cache');

					// Lightbox URL
					if ($lb_enabled == "TRUE" && $lb_thumbs == "TRUE" && $lg_scache == "TRUE" && some_lightbox_plugin() || $lb_force == "TRUE") {
						$slide_folder = get_option('lg_slide_folder');

						if(!file_exists($gallery_root.$currentdir.$slide_folder.$img)){
							createCache($currentdir, $img, false);
						}
						$urlImg = str_replace(" ", "%20", $gallery_address.$currentdir.$slide_folder.$img);
						echo '<td><a href="'.$urlImg.'" rel="lightbox['.$currentdir.']" title="'.$title.'"><img src="'.$gallery_address.$currdir.get_option('lg_thumb_folder').$img .'" alt="'.$img.'"  title="'. $title . '" /></a></td>';
					}
					// End of lightbox URL

					// Thickbox URL
					elseif ($tb_enabled == "TRUE" && $tb_thumbs == "TRUE" && $lg_scache == "TRUE" && some_thickbox_plugin() || $tb_force == "TRUE") {
						$slide_folder = get_option('lg_slide_folder');

						if(!file_exists($gallery_root.$currentdir.$slide_folder.$img)){
							createCache($currentdir, $img, false);
						}

						$thumb_folder = get_option('lg_thumb_folder');
						$xhthumbs = str_replace(" ", "%20", $currdir.$thumb_folder.$img);
						$xhurl = str_replace(" ", "%20", $currdir.$img);

						$xhslides = str_replace(" ", "%20", $currdir.$slide_folder.$img);
						echo '<td><a href="'.$gallery_address.$xhslides.'" class="thickbox" title="'. $title . ' "><img src="'. $gallery_address.$xhthumbs .'" alt="'.$img.'"   /></a></td>';
					}
					// End of thickbox URL

					// Normal URL
					else {
						$thumb_folder = get_option('lg_thumb_folder');
						$xhthumbs = str_replace(" ", "%20", $currdir.$thumb_folder.$img);
						$xhurl = str_replace(" ", "%20", $currdir.$img);
						echo '<td><a href="'.$gallery_uri.'file='.$xhurl.'"><img src="'.$gallery_address.$xhthumbs .'" alt="'.$img.'"  title="'. $title . '" /></a></td>';
					}

					/* ================
					* End of Right URL
					* ================ */

					$tcol_count++;
				}
				// On the fly code
				else {
					// this will prevent some unshown thumb
					$mem = get_option('lg_buffer_size');
					ini_set("memory_limit", $mem);

					$resource = $gallery_root.$currentdir.$img;
					$path = pathinfo($resource);

					switch(strtolower($path["extension"])){
						case "jpeg":
						case "jpg":
							$resource = imagecreatefromjpeg($resource);
							break;
						case "gif":
							$resource = imagecreatefromgif($resource);
							break;
						case "png":
							$resource = imagecreatefrompng($resource);
							break;
						default:
							break;
					}

					if (imagesx($resource) > get_option('lg_thumbwidth')){
						echo '<td><a href="'.$gallery_uri.'file='.$currdir.$img.'"><img src="'.get_option('siteurl').'/wp-content/plugins/lazyest-gallery/lazyest-img.php?file='.$currdir.$img.'&amp;thumb=1" alt="'.$img.'" title="'. $title . '"/></a></td>';
					} else {
						echo '<td><a href="'.$gallery_uri.'file='.$currdir.$img.'"><img src="'.$gallery_address.$currdir.$img.'" alt="'.$img.'" title="'. $title . '"/></a></td>';
					}
					$tcol_count++;
				}
				if ( $tcol_count > $tcolumns ){
					echo '</tr><tr>';
					$tcol_count = 1;
				}
			}
		}

		echo '<td></td></tr></table>';

		// Pagination's navigatior code
		if($thumbs_page != 0){
			$pages = ceil(sizeof($imgfiles) / $thumbs_page);
			echo '<div id="pagination">';

			if($offset > 0){
				echo '<a href="'.$gallery_uri.'file='.$currdir.'&amp;offset='.($offset - $thumbs_page) .'"  title="previous">'. __('&laquo; Prev', $lg_text_domain) .'</a> '."\n";
			}

			if( $pages > 1){
				for($i = 1; $i <= $pages; $i++){
					if( $offset != ($i-1) * $thumbs_page )
						echo ' <a href="'.$gallery_uri.'file='.$currdir.'&amp;offset='. ($i-1) * $thumbs_page . '" >'. $i. '</a> ';
					else
						echo " $i ";
				}
			}

			if(count($imgfiles) > ($thumbs_page + $offset ) ){
				echo ' <a href="'.$gallery_uri.'file='.$currdir.'&amp;offset='.$end .'"  title="next">'. __('Next &raquo;',$lg_text_domain) .'</a> ';
			}
			echo '</div>';
		}
		// End of Pagination's navigatior code

		// Admin links
		if (get_option('lg_enable_captions') == "TRUE") {
			get_currentuserinfo();
			if (strlen($currentdir) !=0){
				if ($user_level >= 8) {
					echo "<div class='lg_admin'>";
						echo "<a href='".get_settings('siteurl')."/wp-admin/".LG_FLM_PAGE."&amp;captions=".$currdir."'>";
						echo "&raquo; ";
						_e("Write captions for images in ", $lg_text_domain);
						echo " ".substr($currentdir, 0, strlen($currentdir)-1);
						echo "</a>";
					echo "</div>";
				}
			}
		}
	}
}

?>