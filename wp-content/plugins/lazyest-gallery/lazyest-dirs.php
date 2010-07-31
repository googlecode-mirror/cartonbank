<?php

function showDirs() {					// Builds folders view page
	global $gallery_address, $gallery_root, $currentdir, $file, $lg_text_domain, $user_level;

	if (!lg_user_can_access($currentdir)) {
		echo "<p>";
		_e("It doesn't look like you can access this folder at this time because the directory you have specified doesn't appear to be browsable.", $lg_text_domain);
		echo "</p>";
	} else {

		$gallery_uri = get_option('lg_gallery_uri');
		// Fix for permalinks
		if (strlen(get_option('permalink_structure')) != 0){
			$gallery_uri = $gallery_uri.'?';
		} else {
			$gallery_uri = $gallery_uri.'&amp;';
		}

		$columns = get_option("lg_folders_columns");
		$runonce = false;
		$col_count = 1;

		if ($dir_content = opendir($gallery_root.$currentdir)) {
			$directory_to_sort = array();
			for ( $i = 0; false !== ($dir = readdir($dir_content)); $i++ ) {
				$directory_to_sort[$i] = $dir;
		}

		if (get_option('lg_sort_alphabetically') == "TRUE")
			sort($directory_to_sort);

		foreach ($directory_to_sort as $dir) {

			if (is_dir($gallery_root.$currentdir.$dir) && !in_array($dir, get_option("lg_excluded_folders")) && $dir!='.' && $dir!='..' ) {
				if ( !$runonce ) {
					echo '<!-- Lazyest Gallery ' . LG_VERSION . ' -->'."\n".'
						<div class="folders">'."\n".'
							<table summary="Categories" class="dir_view">'."\n".'
								<tr>'."\n".'
									<td colspan="'. $columns .'" class="folder"><img src="'.get_option('siteurl').'/wp-content/plugins/lazyest-gallery/images/folders.png" alt="Folders" class="icon" /> &raquo; '. __("Folders", $lg_text_domain) .'</td>'."\n".'
								</tr>'."\n".'
								<tr>';
					$runonce = true;
				}

				if ( $col_count <= $columns ){
					$imgfiles = get_imgfiles($dir);
					if (get_option('lg_sort_alphabetically') == "TRUE")
						sort($imgfiles);

					$xhtmlurl = str_replace(" ", "%20", $currentdir.$dir);
					echo '<td><a href="'.$gallery_uri.'file='.$xhtmlurl.'/">';

					switch(get_option('lg_folder_image')){
						case 'icon':

							// this will prevent some unshown thumb
							$mem = get_option('lg_buffer_size');
							ini_set("memory_limit", $mem);

							// Display Category's images (if any) (Keytwo)
							// PNG
							if (file_exists($gallery_root.$currentdir.$dir.'/'.$dir.'.png')){
								$resource = $gallery_root.$currentdir.$dir.'/'.$dir.'.png';
								$resource = imagecreatefrompng($resource);
								if (imagesx($resource) > get_option('lg_thumbwidth')){
									echo '<img src="'.get_option('siteurl').'/wp-content/plugins/lazyest-gallery/lazyest-img.php?file='.$currentdir.$dir.'/'.$dir.'.png'.'&amp;thumb=1" alt=""/><br />'."\n";
								} else {
									echo '<img src="'.$gallery_address.$currentdir.$dir.'/'.$dir.'.png" alt="" class="category_icon" /><br />'."\n";
								}
							}

							// GIF
							else if (file_exists($gallery_root.$currentdir.$dir.'/'.$dir.'.gif')){
								$resource = $gallery_root.$currentdir.$dir.'/'.$dir.'.gif';
								$resource = imagecreatefromgif($resource);
								if (imagesx($resource) > get_option('lg_thumbwidth')){
									echo '<img src="'.get_option('siteurl').'/wp-content/plugins/lazyest-gallery/lazyest-img.php?file='.$currentdir.$dir.'/'.$dir.'.gif'.'&amp;thumb=1" alt=""/><br />'."\n";
								} else {
									echo '<img src="'.$gallery_address.$currentdir.$dir.'/'.$dir.'.gif" alt="" class="category_icon" /><br />'."\n";
								}
							}

							// JPG
							else if (file_exists($gallery_root.$currentdir.$dir.'/'.$dir.'.jpg')){
								$resource = $gallery_root.$currentdir.$dir.'/'.$dir.'.jpg';
								$resource = imagecreatefromjpeg($resource);
								if (imagesx($resource) > get_option('lg_thumbwidth')){
									echo '<img src="'.get_option('siteurl').'/wp-content/plugins/lazyest-gallery/lazyest-img.php?file='.$currentdir.$dir.'/'.$dir.'.jpg'.'&amp;thumb=1" alt=""/><br />'."\n";
								} else {
									echo '<img src="'.$gallery_address.$currentdir.$dir.'/'.$dir.'.jpg" alt="" class="category_icon" /><br />'."\n";
								}
							}

							// JPG
							else if (file_exists($gallery_root.$currentdir.$dir.'/'.$dir.'.jpeg')){
								$resource = $gallery_root.$currentdir.$dir.'/'.$dir.'.jpeg';
								$resource = imagecreatefromjpeg($resource);
								if (imagesx($resource) > get_option('lg_thumbwidth')){
									echo '<img src="'.get_option('siteurl').'/wp-content/plugins/lazyest-gallery/lazyest-img.php?file='.$currentdir.$dir.'/'.$dir.'.jpeg'.'&amp;thumb=1" alt=""/><br />'."\n";
								} else {
									echo '<img src="'.$gallery_address.$currentdir.$dir.'/'.$dir.'.jpeg" alt="" class="category_icon" /><br />'."\n";
								}
							}

							break;

						case 'random_image':
							// display a random image of this foldery (jan831)
							$img = $imgfiles[rand(0,sizeof($imgfiles)-1)];
							if( $img ){

								if( file_exists($gallery_root.$currentdir.$dir.'/captions.xml')){
									$captions = clean_image_caption($img, $currentdir.$dir);
								}

								// This will removes HTML tags (which are incompatible with "title" argument)
								$title = ereg_replace("<[^>]*>", "", $caption);

								if(get_option('lg_enable_cache') == "TRUE"){
									$thumb_folder = get_option('lg_thumb_folder');
										if(!file_exists($gallery_root.$currentdir.$dir.'/'.$thumb_folder.$img)){
											createCache($currentdir.$dir.'/', $img, true);
										}
									$urlImg = str_replace(" ", "%20", $gallery_address.$currentdir.$dir.'/'.$thumb_folder.$img);
									echo '<img src="'.$urlImg.'"  style="vertical-align:middle;padding:2px;"  alt="'.$img.'" title="'. $title . '"/><br />'."\n";
								} else {
									$urlImg = str_replace(" ", "%20", $currentdir.$dir.'/'.$img);
									echo '<img src="'.get_option('siteurl').'/wp-content/plugins/lazyest-gallery/lazyest-img.php?file='.$urlImg.'&amp;thumb=1"  style="vertical-align:middle;padding:2px;"  alt="'.$img.'" title="'. $title . '" /><br />'."\n";
								}
							}
							break;
					}

					$file_counter = sizeof($imgfiles);
					$folder_caption = clean_folder_caption($currentdir.$dir);

					if ($file_counter == 0){
						// "F" if there are folders only inside
						if (get_option('lg_use_folder_captions') == "TRUE" && strlen($folder_caption) != 0 ) {
							echo '&raquo; '.$folder_caption.'</a> (F)</td>';
						} else {
							echo '&raquo; '.$dir.'</a> (F)</td>';
						}
					} else {
						// "#" if there are images instead
						if (get_option('lg_use_folder_captions') == "TRUE" && strlen($folder_caption) != 0) {
							echo '&raquo; '.$folder_caption.'</a> ('. sizeof($imgfiles) . ')</td>';
						} else {
							echo '&raquo; '.$dir.'</a>  ('. sizeof($imgfiles) . ')</td>';
						}
					}
					$col_count++;
				}

				if ($col_count > $columns){
					echo '</tr><tr>';
					$col_count = 1;
				}
			}
		}
		} else {
			echo "<div class='quote'>". __('Something goes wrong. Make sure of your Gallery Root folder.', $lg_text_domain) ."</div>";
		}

		if ( $runonce )
			echo '<td></td></tr></table></div><br/>'."\n";
	}
}
?>