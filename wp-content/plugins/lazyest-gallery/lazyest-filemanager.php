<?php
/*
 * This file handle all captions
 * Functions list:
 * -TODO-
 */

/** This function builds the structure of the gallery inside the main LG admin panel
 * TODO:
 * - Add a filter here (password or "not to show" option)
 * - Add the icon of each folder
 */

require_once('lazyest-gallery.php');

global $gallery_root;

function lg_show_gallery_structure(){
	global $gallery_root, $lg_text_domain;

	echo "<div style='font-family:monospace;background:#efefef;padding:5px;border:1px solid #ccc;'>\n";
	echo "<ul>\n";

	if (file_exists($gallery_root)){
		if ($dir_content = opendir($gallery_root)) {
			while ( false !== ($dir = readdir($dir_content)) ) {
				if (is_dir($gallery_root.$dir) && !in_array($dir, get_option('lg_excluded_folders')) && $dir!='.' && $dir!='..' ) {
					lg_show_structure($dir);
				}
			}
		}
	}
	else {
		echo "<li><div style='border: solid 1px #000; padding: 10px;'><b style='color:#ff0000;'>". __('WARNING', $lg_text_domain) ." </b>: ". __('Specified gallery folder does not exists', $lg_text_domain) ."</div></li>";
	}

	echo "</ul>\n";
	echo "</div>\n";
}

/**
 * This function is used from show_gallery_structure() to build nested structures
 * NOTE: $sscurrentdir stays for Show Structure current directory
 */

function lg_show_structure($sscurrentdir){
	global $excluded_folders, $gallery_root, $lg_text_domain;

	if( substr($sscurrentdir, strlen($sscurrentdir)-1 ) != '/'){
		$sscurrentdir .= '/';
	}
	$righturl = urlencode($sscurrentdir);
	echo '<li><a href="'. LG_FLM_PAGE .'&amp;captions=' . $righturl . '">' . $sscurrentdir . '</a>';

	if ($dir_content = opendir($gallery_root.$sscurrentdir)) {
		$dir_list = array();
		while ( false !== ($dir = readdir($dir_content)) ) {
			if (is_dir($gallery_root.$sscurrentdir.$dir) && !in_array($dir, get_option('lg_excluded_folders')) && $dir!='.' && $dir!='..' ) {
				$dir_list[] = $sscurrentdir.$dir;
			}
		}

		if($dir_list != NULL && sizeof($dir_list) > 0) {
			echo "<ul>\n";
			foreach($dir_list as $subdir) {
				lg_show_structure($subdir);
			}
			echo "</ul>\n";
		}
	}
	echo '</li>';
}

/**
 * This function builds the page relative to the caption system
 */

function lg_build_captions_form(){
	global $gallery_root, $gallery_address, $lg_text_domain;

	$capdir = $_GET['captions'];
	if (strlen($capdir) == 0){
		$capdir = $_POST['upload_folder'];
	}

	$gallery_uri = get_option('lg_gallery_uri');

	// Fix for permalinks
	if (strlen(get_option('permalink_structure')) != 0){
		$gallery_uri = $gallery_uri.'?';
	} else {
		$gallery_uri = $gallery_uri.'&amp;';
	}

	// main container div
	echo '<div class="wrap">';

	// Button: save captions (lazyest-captions.php)
	if(isset($_POST['update_captions'])){
		lg_generate_xml($_POST['directory']);
	}

	/* ==========
	 * Upload div
	 * ========== */

	$folder = $gallery_root.$capdir;

	// $_POST['upload_folder'];
	$allowed_types = explode(' ', trim(strtolower(get_option('lg_fileupload_allowedtypes'))));

	if ($_POST['upload']) {
		$action = 'upload';
		if (!is_writable($folder))
			$action = 'not-writable';

		switch ($action) {
			case 'not-writable':
				?>
				<p><?php printf(__("It doesn't look like you can use the file upload feature at this time because the directory you have specified (<code>%s</code>) doesn't appear to be writable by WordPress. Check the permissions on the directory and for typos.", $lg_text_domain), $folder) ?></p>
				<?php
				break;

			case 'upload':
				$imgalt = basename( (isset($_POST['imgalt'])) ? $_POST['imgalt'] : '' );
				$img1_name = (strlen($imgalt)) ? $imgalt : basename( $_FILES['img1']['name'] );
				$img1_name = preg_replace('/[^a-z0-9_.]/i', '', $img1_name);
				$img1_size = $_POST['img1_size'] ? intval($_POST['img1_size']) : intval($_FILES['img1']['size']);
				$img1_type = (strlen($imgalt)) ? $_POST['img1_type'] : $_FILES['img1']['type'];
				$pi = pathinfo($img1_name);
				$imgtype = strtolower($pi['extension']);

				if (in_array($imgtype, $allowed_types) == false)
					die(sprintf(__('File %1$s of type %2$s is not allowed.', $lg_text_domain) , $img1_name, $imgtype));
				if (strlen($imgalt)) {
					$pathtofile = $folder.$imgalt;
					$img1 = $_POST['img1'];
				} else {
					$pathtofile = $folder.$img1_name;
					$img1 = $_FILES['img1']['tmp_name'];
				}
				// makes sure not to upload duplicates, rename duplicates
				$i = 1;
				$pathtofile2 = $pathtofile;
				$tmppathtofile = $pathtofile2;
				$img2_name = $img1_name;

				while ( file_exists($pathtofile2) ) {
					$pos = strpos( strtolower($tmppathtofile), '.' . trim($imgtype) );
					$pathtofile_start = substr($tmppathtofile, 0, $pos);
					$pathtofile2 = $pathtofile_start.'_'.zeroise($i++, 2).'.'.trim($imgtype);
					$img2_name = explode('/', $pathtofile2);
					$img2_name = $img2_name[count($img2_name)-1];
				}

				if (file_exists($pathtofile) && !strlen($imgalt)) {
					$i = explode(' ', get_option('lg_fileupload_allowedtypes'));
					$i = implode(', ',array_slice($i, 1, count($i)-2));
					$moved = move_uploaded_file($img1, $pathtofile2);

					if (!$moved) {
							$moved = copy($img1, $pathtofile2);
					}
					if (!$moved) {
						die(sprintf(__("Couldn't upload your file to %s.", $lg_text_domain), $pathtofile2));
					} else {
						chmod($pathtofile2, 0666);
							@unlink($img1);
					}
					//
					// duplicate-renaming function contributed by Gary Lawrence Murphy
					?>
						<p><strong><?php __('Duplicate File?') ?></strong></p>
						<p><b><em><?php printf(__("The filename '%s' already exists!", $lg_text_domain), $img1_name); ?></em></b></p>
						<p><?php printf(__("Filename '%1\$s' moved to '%2\$s'", $lg_text_domain), $img1, "$pathtofile2 - $img2_name") ?></p>
						<p><?php _e('Confirm or rename:') ?></p>

						<form action="<?php echo LG_FLM_PAGE; ?>" method="post" enctype="multipart/form-data">
							<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo  get_option('lg_fileupload_maxk') *1024 ?>" />
							<input type="hidden" name="img1_type" value="<?php echo $img1_type;?>" />
							<input type="hidden" name="img1_name" value="<?php echo $img2_name;?>" />
							<input type="hidden" name="img1_size" value="<?php echo $img1_size;?>" />
							<input type="hidden" name="img1" value="<?php echo $pathtofile2;?>" />
							<?php _e('Alternate name:') ?><br /><input type="text" name="imgalt" size="30" class="uploadform" value="<?php echo $img2_name;?>" /><br />
							<br />
							<input type="submit" name="submit" value="<?php _e('Rename', $lg_text_domain) ?>" class="search" />
						</form>
					<?php
					die();
				}

				if (!strlen($imgalt)) {
					@$moved = move_uploaded_file($img1, $pathtofile); //Path to your images directory, chmod the dir to 777
					// move_uploaded_file() can fail if open_basedir in PHP.INI doesn't
					// include your tmp directory. Try copy instead?
					if(!$moved) {
							$moved = copy($img1, $pathtofile);
					}
					// Still couldn't get it. Give up.
					if (!$moved) {
							die(sprintf(__("Couldn't upload your file to %s.", $lg_text_domain), $pathtofile));
					} else {
						chmod($pathtofile, 0666);
							@unlink($img1);
					}
				} else {
					rename($img1, $pathtofile)
					or die(sprintf(__("Couldn't upload your file to %s.", $lg_text_domain), $pathtofile));
				}

				if ( ereg('image/',$img1_type) )
					$piece_of_code = "[[Image:".$capdir.$img1_name."]]";
				else
					$piece_of_code = "<a href='" . $gallery_address.$capdir.$img1_name . "'>$img1_name</a>";
				$piece_of_code = htmlspecialchars( $piece_of_code );

				?>
					<div id="message" class="updated fade"><p><?php _e('File uploaded!', $lg_text_domain) ?></p></div>
					<p><?php printf(__("Your file <code>%s</code> was uploaded successfully!", $lg_text_domain), $img1_name); ?></p>
					<p><?php _e('Here&#8217;s the code to display it in your posts:', $lg_text_domain) ?></p>
					<p><code><?php echo $piece_of_code; ?></code></p>
					<p>
						<strong><?php _e('Image Details') ?></strong>: <br />
						<?php _e('Name:'); ?>
						<?php echo $img1_name; ?>
						<br />
						<?php _e('Size:') ?>
						<?php echo round($img1_size / 1024, 2); ?> <?php _e('<abbr title="Kilobyte">KB</abbr>', $lg_text_domain) ?><br />
						<?php _e('Type:') ?>
						<?php echo $img1_type; ?>
					</p>
					<p><a href="<?php echo LG_FLM_PAGE; ?>&amp;captions=<?php echo $capdir; ?>"><?php _e('Upload another') ?></a></p>

				<?php
				break;
		}
	}
	/* =================
	 * End of Upload div
	 * ================= */


	/* ====================
	 * Confirm deletion div
	 * ==================== */

	if (isset($_POST['yes'])) {
		if (!is_dir($_POST['delete_this'])) {
			if(@unlink($_POST['delete_this'])) {
				echo '<div id="message" class="updated fade"><p>'. __('File deleted successfully', $lg_text_domain) .'</p></div>';
			} else {
				echo '<div id="message" class="error fade"><p>'. __('Cannot delete file, maybe it has already been deleted or have wrong permissions', $lg_text_domain) .'</p></div>';
			}
		} else {
			if(lg_remove_directory($_POST['delete_this'])) {
				echo '<div id="message" class="updated fade"><p>'. __('Folder deleted successfully', $lg_text_domain) .'</p></div>';
			} else {
				echo '<div id="message" class="error fade"><p>'. __('Cannot delete folder: maybe it is already empty or have bad permissions', $lg_text_domain) .'</p></div>';
			}
		}
		// Unsetting informations
		unset($_POST);
		unset($_GET);
	}
	if (isset($_POST['no'])) {
		echo '<div id="message" class="updated fade"><p>'. __('Nothing Changed', $lg_text_domain) .'</p></div>';
		// Unsetting informations
		unset($_POST);
		unset($_GET);
	}

	/* =================
	 * file deletion div
	 * ================= */

	if (isset($_GET['file_to_delete']) &&
		!isset($_POST['update_captions']) &&
		!isset($_POST['upload']) &&
		!isset($_POST['create_new_folder']) &&
		!isset($_POST['rename_the_folder']) &&
		!isset($_POST['yes']) &&
		!isset($_POST['no'])) {
		?>
		<div style="padding:10px;">
		<div style="text-align:center;padding:0px;color:red;border:1px solid #ff0000;background:#ffdddd">
		<?php
		_e('You are about to delete', $lg_text_domain);
		echo " <code>".basename($_GET['file_to_delete'])."</code><br />";
		echo __('Are you sure?', $lg_text_domain)."<br />";
		?>
		<form name="delete_image_file" method="post" action="<?php echo LG_FLM_PAGE; ?>" style="padding:5px;">
			<div class="submit" style="text-align:center">
				<input type="submit" name="yes" value="<?php _e('Yes', $lg_text_domain); ?>" style="width:80px;" />
				<input type="submit" name="no" value="<?php _e('No', $lg_text_domain); ?>" style="width:80px;" />
				<input type="hidden" name="folder" value="<?php echo $_POST['folder']; ?>" />
				<input type="hidden" name="delete_this" value="<?php echo $_GET['file_to_delete']; ?>" />
			</div>
		</form>
		<?php
		echo "</div>\n</div>";
	}

	/* =================
	 * Rename folder div
	 * ================= */

	if (isset($_POST['rename_the_folder'])){
		if (strlen($_POST['new_file_name']) > 0){

			$oldname = $_POST['actual_folder'];
			$newname = $gallery_root.$_POST['new_file_name'];

			if (lg_rename_file($oldname, $newname)){
				echo '<div id="message" class="updated fade"><p>'. __('Renamed successfully', $lg_text_domain) .'</p></div>';
			} else {
				echo '<div id="message" class="error fade"><p>'. __('Cannot rename.', $lg_text_domain) .'</p></div>';
			}

		} else {
			echo '<div id="message" class="error fade"><p>'. __('Provide a new name.', $lg_text_domain) .'</p></div>';
		}

		unset($_POST);
		unset($_GET);
	}

	/* =======================
	 * New folder creation div
	 * ======================= */

	if (isset($_POST['create_new_folder'])) {
		if (strlen($_POST['new_folder_name']) > 0) {

			$folder_name = $_POST['actual_folder'].$_POST['new_folder_name'];
			$new_folder_path = $gallery_root.$folder_name;

			if (lg_make_directory($new_folder_path)) {
				echo '<div id="message" class="updated fade"><p>'. __('Folder created successfully', $lg_text_domain) .'</p></div>';
			} else {
				echo '<div id="message" class="error fade"><p>'. __('Cannot create folder: maybe it already exists or have bad permissions', $lg_text_domain) .'</p></div>';
			}
		} else {
			echo '<div id="message" class="error fade"><p>'. __('Cannot create an empty named folder.', $lg_text_domain) .'</p></div>';
		}
		unset($_POST);
		unset($_GET);
	}

	$imgfiles = get_imgfiles($capdir);
	$act_current = $capdir;

	// Provide eventual icons to the image array
	if (file_exists($gallery_root.$capdir.substr($capdir, 0, strlen($capdir)-1).".jpg")){
		$capdir_icon = substr($capdir, 0, strlen($capdir)-1).".jpg";
		$imgfiles[] = $capdir_icon;
	} else if (file_exists($gallery_root.$capdir.substr($capdir, 0, strlen($capdir)-1).".jpeg")){
		$capdir_icon = substr($capdir, 0, strlen($capdir)-1).".jpeg";
		$imgfiles[] = $capdir_icon;
	} else if (file_exists($gallery_root.$capdir.substr($capdir, 0, strlen($capdir)-1).".png")){
		$capdir_icon = substr($capdir, 0, strlen($capdir)-1).".png";
		$imgfiles[] = $capdir_icon;
	} else if (file_exists($gallery_root.$capdir.substr($capdir, 0, strlen($capdir)-1).".gif")){
		$capdir_icon = substr($capdir, 0, strlen($capdir)-1).".gif";
		$imgfiles[] = $capdir_icon;
	}

	/* =========
	 * Main Page
	 * ========= */

	echo '<h2>'. __('File Manager', $lg_text_domain) .'</h2>';

	if (strlen($capdir) > 0) {

	?>

		<fieldset class="options">
			<h3><?php echo sprintf(__('Captions page for <code>%s</code>', $lg_text_domain), $act_current); ?></h3>

			<!-- Shortcuts-->
			<div style="padding:5px;display:block;background:#efefef;border:1px solid #ccc;height:20px;">
				<a href="<?php echo LG_ADM_PAGE; ?>">&laquo; <?php _e('Admin page', $lg_text_domain); ?></a>
			</div>
			<!-- End of Shortcuts-->

			<br />

			<!-- Tip div -->
			<div style="padding:5px;border:1px solid #3fbd3f;background:#beffbe;color:#088000;">
				<?php _e('You can use HTML links but be sure to use "[" and "]" instead of "<" and ">"; something like [a href="somewhere"]Link[/a]', $lg_text_domain); ?>
			</div>
			<!-- End of tip div -->

			<form name="gallery_captions" method="post" action="<?php echo LG_FLM_PAGE; ?>">
				<input type="hidden" name="folder" value="<?php echo $act_current ?>"/>
				<table summary="thumbs" cellspacing="1" cellpadding="10">
					<tr>
						<!-- Folder Caption code -->
						<td colspan='2'><b>&raquo; <?php _e("Folder's description:", $lg_text_domain); ?></b>
							<?php
								$foldcap = clean_folder_caption($act_current, false);
								echo '<input type="text" name="folder_caption" value="'.$foldcap.'" size="80" style="width:98%" />';
							?>
							<ul>
								<li>&raquo; <a href="<?php echo LG_FLM_PAGE ?>&amp;captions=<?php echo $act_current; ?>&amp;file_to_delete=<?php echo $gallery_root.$act_current.get_option('lg_thumb_folder') ?>" class="delete" style="display:inline;"><?php _e('Empty thumbs cache', $lg_text_domain); ?></a></li>
								<li>&raquo; <a href="<?php echo LG_FLM_PAGE ?>&amp;captions=<?php echo $act_current; ?>&amp;file_to_delete=<?php echo $gallery_root.$act_current.get_option('lg_slide_folder') ?>" class="delete" style="display:inline;"><?php _e('Empty slides cache', $lg_text_domain); ?></a></li>
								<li>&raquo; <a href="<?php echo LG_FLM_PAGE ?>&amp;captions=<?php echo $act_current; ?>&amp;file_to_delete=<?php echo $gallery_root.$act_current ?>" class="delete" style="display:inline;"><?php _e('Erase this folder', $lg_text_domain); ?></a></li>
							</ul>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="text" size="40" name="new_file_name" value="<?php echo substr($act_current, 0, strlen($act_current) -1); ?>" />
							<input type="hidden" name="actual_folder" value="<?php echo $gallery_root.$act_current; ?>" />
							<input type="submit" name="rename_the_folder" value="<?php _e('Rename this folder', $lg_text_domain); ?>" class="button" style="display:inline;" />
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-algin: left;" ><b>&raquo; <?php _e('Minimum level to access this folder:', $lg_text_domain); ?></b>
							<select name="folder_minimum_level">
							<?php
								for ($i = 1; $i < 11; $i++) {
									if ($i == get_minimum_folder_level($act_current)) {
										$selected = " selected='selected'";
									} else {
										$selected = '';
									}
									echo "\n\t<option value='$i' $selected>$i</option>";
								}
							?>
							</select>
							<span style="font-size:x-small;text-align:center;padding:2px;color:red;border:1px solid #ff0000;background:#ffdddd">
								<?php _e('EXPERIMENTAL: Folder will be still browsable (if full path is known).', $lg_text_domain) ?>
							</span>
						</td>
					</tr>

					<!-- End of Folder Caption code -->
	<?php

	if (isset($imgfiles)) {
		foreach ($imgfiles as $img) {

			// clean_image_caption() function is in lazyest-gallery.php file
			// and checks if xml file exists, if not it returns false
			// we need a "clean" (with "<" and ">") caption
			$caption = clean_image_caption($img, $capdir);

			// this will removes HTML tags and used as title argument
			$title = ereg_replace("<[^>]*>", "", $caption);
			$righturl = str_replace(" ", "%20", $act_current.$img);

			echo '<tr><td><a href="'.$gallery_uri.'file='.$righturl.'">';

			// If thumbs cache system is enabled
			if (get_option('lg_enable_cache') == "TRUE"){
				// we check if thumbs exist
				if (!file_exists($gallery_root.$act_current.get_option('lg_thumb_folder').$img)) {
					// keeping track of subfolders
					$img_file_path = explode('/', $img);
					$img_index = count($img_file_path)-1;
					$img_file = $img_file_path[$img_index];

					// If there are subfolders
					if ($img_index > 1){
						for ($i = 1; $i < count($img_file_path)-1; $i++) {
							// set the new "current" directory
							$act_current .= $img_file_path[$i]."/";
						}
					}
					// if thumbs do not exist we create them
					createCache($act_current, $img, true);
				}
				// trimming spaces (XHTML Urls)
				$righturl = str_replace(" ", "%20", $gallery_address.$act_current.get_option('lg_thumb_folder').$img);
				echo '<img src="'.$righturl.'" alt="'.$img.'"  title="'. $title . '" />';
			} else { // otherwise
				$righturl = str_replace(" ", "%20", get_option('siteurl')."/wp-content/plugins/lazyest-gallery/lazyest-img.php?file=". $act_current.$img."&amp;thumb=1");
				echo "<img src='".$righturl."' alt='".$img."' title='".$title."' />";
			}

			// this time we need a "rebuilded" (with "" and "") caption
			$caption = clean_image_caption($img, $capdir, false);

			// this is a dynamic form name construct that we need for the captions system
			$form_name = str_replace('.', '_', $img);
			$form_name = str_replace(' ', '_', $form_name);

			echo '</a></td>';
			echo '<td>&raquo; '.$img.'<br />';

			$righturl = str_replace(" ", "%20", $gallery_root.$act_current.$img);
			// Inputs
			?>
				<input type="text" name="<?php echo $form_name; ?>" value="<?php echo $caption; ?>" size="90" style="width:88%" />
				<a href="<?php echo LG_FLM_PAGE ?>&amp;captions=<?php echo str_replace(" ", "%20", $act_current); ?>&amp;file_to_delete=<?php echo $righturl ?>" class="button" style="display:inline;"><?php _e('Delete', $lg_text_domain); ?></a>
			<?php
		}
	}
	?>
				</table>
				<div class="submit">
					<input type="hidden" name="directory" value="<?php echo $act_current ?>" />
					<input type="submit" name="update_captions" value="<?php _e('Update Folder', $lg_text_domain); ?>" />
				</div>
			</form>
		</fieldset>

		<!-- Upload section -->
		<fieldset class="dbx-box">
			<h3 class="dbx-handle"><?php _e('Upload Image', $lg_text_domain) ?></h3>
			<div class="dbx-content">
				<?php upload_page($act_current); ?>
			</div><br /><br />
		</fieldset>

	<?php } // Closes the main page if ?>

		<!-- Gallery structure section -->
		<fieldset class="dbx-box">
			<h3 class="dbx-handle"><?php _e('Gallery Structure', $lg_text_domain) ?></h3>
			<form name="new_folder" method="post" action="<?php echo LG_FLM_PAGE; ?>">

				<!-- New folder creation form -->

				<input type="text" name="new_folder_name" size="40" style="width:78%;"/>

				<?php if (file_exists($gallery_root.$act_current)) { ?>
					<input type="hidden" name="actual_folder" value="<?php echo $act_current; ?>"/>
				<?php } ?>

				<input type="submit" name="create_new_folder" value="<?php _e('New Folder', $lg_text_domain); ?>" style="width:20%"/>

			</form>
			<div class="dbx-content">
				<?php lg_show_gallery_structure(); ?>
			</div>
		</fieldset>

	</div>

	<?php

}

/**
 * This function builds the xml file where images infos are stored
 * NOTE: $capdir stays for Captions Directory
 */

function lg_generate_xml($capdir){
	global $gallery_root;

	if (is_writable($gallery_root.$capdir)){

		// Gather the image's informations and ID number
		$imgfiles = get_imgfiles($capdir);
		$images_data = get_image_caption($capdir);

		// Check if $capdir ends with the "/" and providing one if not
		if (substr($capdir, strlen($capdir)-1, strlen($capdir)) != "/")
			$capdir .= "/";

		$handle = fopen($gallery_root.$capdir.'captions.xml', 'wb');

		fwrite($handle, "<?xml version='1.0' encoding='" . get_option('blog_charset') . "'?>\n");
		fwrite($handle, "<data>\n");
		// Folder caption; TODO valute htmlentities()
		$folder_caption =  utf8_encode(str_replace('\\', '', $_POST['folder_caption']));

		fwrite($handle, "\t<folder>".$folder_caption."</folder>\n");

		// Folder access level
		$folder_access = $_POST['folder_minimum_level'];
		fwrite($handle, "\t<level>".$folder_access."</level>\n");

		if (isset($imgfiles)) {
			foreach ($imgfiles as $img) {
				// prepare the strings to be written
				$form_value = str_replace('.', '_', $img);
				$form_value = str_replace(' ', '_', $form_value);
				$dirty_caption = utf8_encode($_POST[$form_value]);

				$clean_caption = str_replace('\\', '', $dirty_caption);
				// write strings
				fwrite($handle, "\t<photo id='". $img ."'>\n");
				fwrite($handle, "\t\t<caption>". $clean_caption ."</caption>\n");

				for ($i = 0; $i < count($images_data); $i++) {
					if ($images_data[$i][image] == $img){
						$image_number = $images_data[$i][id];
					}
				}
				// If is not setted update the counter and...
				if (strlen($image_number) == 0) {
					add_option('lg_image_indexing', '0', 'Lazyest Gallery images index to retrive comments');
					$image_number = get_option('lg_image_indexing');
					$counter = $image_number +1;
					update_option('lg_image_indexing', $counter);
				}

				// ...provide one
				fwrite($handle, "\t\t<image>". $image_number ."</image>\n");

				fwrite($handle, "\t</photo>\n");
			}
		}

		fwrite($handle, "</data>");
		fclose($handle);

		@chmod($gallery_root.$capdir.'captions.xml', 0666);
	}
	else {
		return lg_cannot_rw($capdir);
	}
}

function lg_cannot_rw($rwdir){
	global $gallery_root;

	?>
	<div style='background-color: rgb(207, 235, 247);' id='message' class='error fade'>
		<b>WARNING:</b><br />
		<p>
			Unable to create xml file inside  <?php echo $gallery_root.$rwdir; ?> <br />
			File permissions are actually set to
			<b><?php echo substr(sprintf('%o', fileperms($gallery_root.$rwdir)), -4) ?></b><br />
			Try to set them to <b>777</b>
		</p>
	</div>
	<?php
}

// ============= File Manager Utility Functions =============

function lg_clear_directory($path) {
	if($dir_handle = opendir($path)) {
		while($file = readdir($dir_handle)) {
			if(is_dir($path.$file) && $file != "." && $file != ".."){
				lg_clear_directory($path.$file.'/');
				lg_remove_directory($path.$file.'/');
			} else {
				if($file == "." || $file == "..") {
					continue;
				} else {
					unlink($path.$file);
				}
			}
		}
		closedir($dir_handle);
		return true;
	// all files deleted
	} else {
		return false;
	// directory doesn't exist
	}
}

function lg_remove_directory($path) {
	if(lg_clear_directory($path)){
		if(rmdir($path)){
			return true;
		// directory removed
		} else {
			return false;
		// directory couldn't removed
		}
	} else {
		return false;
	// no empty directory
	}
}

function lg_make_directory($path) {
	if (@mkdir($path, 0777)){
		return true;
	} else {
		return false;
	}
}

function lg_rename_file($oldname, $newname) {
	if (@rename($oldname, $newname)){
		return true;
	} else {
		return false;
	}
}
// ================= Write Options Page ================

/* This is the function that will build the form in the editor */
function lg_build_smartlink_form(){

}
/* Adds the gallery browser form in the posts editor */
function lg_add_gallery_browser(){
	lg_build_smartlink_form();
}

// =================== Upload functions =================

function upload_page($upload) {
	global $gallery_root, $gallery_address, $lg_text_domain;

	$folder = $gallery_root.$upload;

	if ( !get_option('lg_fileupload_minlevel') )
		die (__("You are not allowed to upload files", $lg_text_domain));

	$allowed_types = explode(' ', trim(strtolower(get_option('lg_fileupload_allowedtypes'))));

	foreach ($allowed_types as $type) {
		$type_tags[] = "<code>$type</code>";
	}

	$i = implode(', ', $type_tags);

	?>
	<p><?php printf(__('You can upload files with the extension %1$s as long as they are no larger than %2$s <abbr title="Kilobytes">KB</abbr>.', $lg_text_domain), $i, get_option('lg_fileupload_maxk')); ?></p>
	<form action="<?php echo LG_FLM_PAGE; ?>"  method="post" enctype="multipart/form-data">
		<p>
			<label for="img1"><?php _e('File:', $lg_text_domain) ?></label>
			<br />
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo get_option('lg_fileupload_maxk') * 1024 ?>" />
			<input type="file" name="img1" id="img1" size="100" />
			<input type="hidden" name="upload_folder" value="<?php echo $upload; ?>"/>
		</p>
		<p><input type="submit" name="upload" class="button" value="<?php _e('Upload File', $lg_text_domain) ?>" /></p>
	</form>
	<?php
}

// ================ Sobstitution functions ==============

function lg_utf8_encode($txt){
   $txt2=str_replace('À','&#1040;',$txt);
   $txt2=str_replace('Á','&#1041;',$txt2);
   $txt2=str_replace('Â','&#1042;',$txt2);
   $txt2=str_replace('Ã','&#1043;',$txt2);
   $txt2=str_replace('Ä','&#1044;',$txt2);
   $txt2=str_replace('Å','&#1045;',$txt2);
   $txt2=str_replace('Æ','&#1046;',$txt2);
   $txt2=str_replace('Ç','&#1047;',$txt2);
   $txt2=str_replace('È','&#1048;',$txt2);
   $txt2=str_replace('É','&#1049;',$txt2);
   $txt2=str_replace('Ê','&#1050;',$txt2);
   $txt2=str_replace('Ë','&#1051;',$txt2);
   $txt2=str_replace('Ì','&#1052;',$txt2);
   $txt2=str_replace('Í','&#1053;',$txt2);
   $txt2=str_replace('Î','&#1054;',$txt2);
   $txt2=str_replace('Ï','&#1055;',$txt2);
   $txt2=str_replace('Ð','&#1056;',$txt2);
   $txt2=str_replace('Ñ','&#1057;',$txt2);
   $txt2=str_replace('Ò','&#1058;',$txt2);
   $txt2=str_replace('Ó','&#1059;',$txt2);
   $txt2=str_replace('Ô','&#1060;',$txt2);
   $txt2=str_replace('Õ','&#1061;',$txt2);
   $txt2=str_replace('Ö','&#1062;',$txt2);
   $txt2=str_replace('×','&#1063;',$txt2);
   $txt2=str_replace('Ø','&#1064;',$txt2);
   $txt2=str_replace('Ù','&#1065;',$txt2);
   $txt2=str_replace('Ú','&#1066;',$txt2);
   $txt2=str_replace('Û','&#1067;',$txt2);
   $txt2=str_replace('Ü','&#1068;',$txt2);
   $txt2=str_replace('Ý','&#1069;',$txt2);
   $txt2=str_replace('Þ','&#1070;',$txt2);
   $txt2=str_replace('ß','&#1071;',$txt2);
   $txt2=str_replace('à','&#1072;',$txt2);
   $txt2=str_replace('á','&#1073;',$txt2);
   $txt2=str_replace('â','&#1074;',$txt2);
   $txt2=str_replace('ã','&#1075;',$txt2);
   $txt2=str_replace('ä','&#1076;',$txt2);
   $txt2=str_replace('å','&#1077;',$txt2);
   $txt2=str_replace('æ','&#1078;',$txt2);
   $txt2=str_replace('ç','&#1079;',$txt2);
   $txt2=str_replace('è','&#1080;',$txt2);
   $txt2=str_replace('é','&#1081;',$txt2);
   $txt2=str_replace('ê','&#1082;',$txt2);
   $txt2=str_replace('ë','&#1083;',$txt2);
   $txt2=str_replace('ì','&#1084;',$txt2);
   $txt2=str_replace('í','&#1085;',$txt2);
   $txt2=str_replace('î','&#1086;',$txt2);
   $txt2=str_replace('ï','&#1087;',$txt2);
   $txt2=str_replace('ð','&#1088;',$txt2);
   $txt2=str_replace('ñ','&#1089;',$txt2);
   $txt2=str_replace('ò','&#1090;',$txt2);
   $txt2=str_replace('ó','&#1091;',$txt2);
   $txt2=str_replace('ô','&#1092;',$txt2);
   $txt2=str_replace('õ','&#1093;',$txt2);
   $txt2=str_replace('ö','&#1094;',$txt2);
   $txt2=str_replace('÷','&#1095;',$txt2);
   $txt2=str_replace('ø','&#1096;',$txt2);
   $txt2=str_replace('ù','&#1097;',$txt2);
   $txt2=str_replace('ú','&#1098;',$txt2);
   $txt2=str_replace('û','&#1099;',$txt2);
   $txt2=str_replace('ü','&#1100;',$txt2);
   $txt2=str_replace('ý','&#1101;',$txt2);
   $txt2=str_replace('þ','&#1102;',$txt2);
   $txt2=str_replace('ÿ','&#1103;',$txt2);
   return $txt2;
}
?>
