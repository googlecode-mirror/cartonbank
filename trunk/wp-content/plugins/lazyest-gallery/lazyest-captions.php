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
		echo "<div style='border: solid 1px #000; padding: 10px;'><b style='color:#ff0000;'>". __('WARNING', $lg_text_domain) ." </b>: ". __('Specified gallery folder does not exists', $lg_text_domain) ."</div>";
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
	echo '<li><a href="?page=lazyest-gallery/lazyest-admin.php&amp;captions=' . $righturl . '">' . $sscurrentdir . '</a>';

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
	global $gallery_root,  $gallery_address, $lg_text_domain;

	$capdir = $_GET['captions'];
	// main container div
	echo '<div class="wrap">';

	/* ==========
	 * Upload div
	 * ========== */

	$folder = $gallery_root.$capdir;
	$allowed_types = explode(' ', trim(strtolower(get_settings('lg_fileupload_allowedtypes'))));

	if ($_POST['upload'])
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
				$i = explode(' ', get_settings('lg_fileupload_allowedtypes'));
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
					<p><b><em><?php printf(__("The filename '%s' already exists!"), $img1_name); ?></em></b></p>
					<p><?php printf(__("Filename '%1\$s' moved to '%2\$s'"), $img1, "$pathtofile2 - $img2_name") ?></p>
					<p><?php _e('Confirm or rename:') ?></p>

					<form action="" method="post" enctype="multipart/form-data">
						<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo  get_settings('lg_fileupload_maxk') *1024 ?>" />
						<input type="hidden" name="img1_type" value="<?php echo $img1_type;?>" />
						<input type="hidden" name="img1_name" value="<?php echo $img2_name;?>" />
						<input type="hidden" name="img1_size" value="<?php echo $img1_size;?>" />
						<input type="hidden" name="img1" value="<?php echo $pathtofile2;?>" />
						<?php _e('Alternate name:') ?><br /><input type="text" name="imgalt" size="30" class="uploadform" value="<?php echo $img2_name;?>" /><br />
						<br />
						<input type="submit" name="submit" value="<?php _e('Rename') ?>" class="search" />
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
				<p><a href="options-general.php?page=lazyest-gallery/lazyest-admin.php&amp;captions=<?php echo $capdir; ?>"><?php _e('Upload another') ?></a></p>

			<?php
			break;
	}
	/* =================
	 * End of Upload div
	 * ================= */


	/* =================
	 * Confirmations div
	 * ================= */

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

	if (isset($_GET['file_to_delete']) && !isset($_POST['update_captions']) && !isset($_POST['upload'])) {
		?>
		<div style="padding:10px;">
		<div style="text-align:center;padding:0px;color:red;border:1px solid #ff0000;background:#ffdddd">
		<?php
		_e('You are about to delete', $lg_text_domain);
		echo " <code>".basename($_GET['file_to_delete'])."</code><br />";
		echo __('Are you sure?', $lg_text_domain)."<br />";
		?>
		<form name="delete_image_file" method="post" action="" style="padding:5px;">
			<div class="submit" style="text-align:center">
				<input type="submit" name="yes" value="<?php _e('Yes', $lg_text_domain); ?>" style="width:80px;" />
				<input type="submit" name="no" value="<?php _e('No', $lg_text_domain); ?>" style="width:80px;" />
				<input type="hidden" name="delete_this" value="<?php echo $_GET['file_to_delete']; ?>" />
			</div>
		</form>
		<?php
		echo "</div>\n</div>";
	}

	$imgfiles = get_imgfiles($capdir);
	$act_current = $capdir;

	?>

		<fieldset class="options">
			<h2><?php echo sprintf(__('Captions page for <code>%s</code>', $lg_text_domain), $act_current); ?></h2>

			<!-- Shortcuts-->
			<div style="padding:5px;display:block;background:#efefef;border:1px solid #ccc;height:20px;">
				<a href="options-general.php?page=lazyest-gallery/lazyest-admin.php">&laquo; <?php _e('Admin page', $lg_text_domain); ?></a>
			</div>
			<!-- End of Shortcuts-->

			<br />

			<!-- Tip div -->
			<div style="padding:5px;border:1px solid #3fbd3f;background:#beffbe;color:#088000;">
				<?php _e('You can use HTML links but be sure to use "[" and "]" instead of "<" and ">"; something like [a href="somewhere"]Link[/a]', $lg_text_domain); ?>
			</div>
			<!-- End of tip div -->

			<form name="gallery_captions" method="post" action="">
				<input type="hidden" name="folder" value="<?php echo $act_current ?>"/>
				<table summary="thumbs" cellspacing="1" cellpadding="10">
					<tr>
						<!-- Folder Caption code -->
						<td colspan='2'><b>&raquo; <?php _e("Folder's description:", $lg_text_domain); ?></b>
							<?php $foldcap = clean_folder_caption($act_current, false);
								echo '<input type="text" name="folder_caption" value="'.$foldcap.'" size="80" style="width:98%" />';
							?>
							<ul>
								<li>&raquo; <a href="?page=lazyest-gallery/lazyest-admin.php&amp;captions=<?php echo $act_current; ?>&amp;file_to_delete=<?php echo $gallery_root.$act_current.get_option('lg_thumb_folder') ?>" class="delete" style="display:inline;"><?php _e('Empty thumbs cache', $lg_text_domain); ?></a></li>
								<li>&raquo; <a href="?page=lazyest-gallery/lazyest-admin.php&amp;captions=<?php echo $act_current; ?>&amp;file_to_delete=<?php echo $gallery_root.$act_current.get_option('lg_slide_folder') ?>" class="delete" style="display:inline;"><?php _e('Empty slides cache', $lg_text_domain); ?></a></li>
							</ul>
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
										$selected = '0';
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
			$righturl = urlencode($act_current.$img);

			echo '<tr><td><a href="'.get_option('lg_gallery_uri').'&amp;file='.$righturl.'">';

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
				$righturl = str_replace(" ", "%20", get_settings('siteurl')."/wp-content/plugins/lazyest-gallery/lazyest-img.php?file=". $act_current.$img."&amp;thumb=1");
				echo "<img src=".$righturl." alt=".$img." title=".$title." />";
			}

			// this time we need a "rebuilded" (with "" and "") caption
			$caption = clean_image_caption($img, $capdir, false);

			// this is a dynamic form name construct that we need for the captions system
			$form_name = str_replace('.', '_', $img);
			$form_name = str_replace(' ', '_', $form_name);

			echo '</a></td>';
			echo '<td>&raquo; '.$img.'<br />';
			// Inputs
			?>
				<input type="text" name="<?php echo $form_name; ?>" value="<?php echo $caption; ?>" size="90" style="width:88%" />
				<a href="?page=lazyest-gallery/lazyest-admin.php&amp;captions=<?php echo $act_current; ?>&amp;file_to_delete=<?php echo $gallery_root.$act_current.$img ?>" class="button" style="display:inline;"><?php _e('Delete', $lg_text_domain); ?></a>
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
			<h3 title="click-down and drag to move this box" class="dbx-handle"><?php _e('Upload Image', $lg_text_domain) ?></h3>
			<div class="dbx-content">
				<?php upload_page($act_current); ?>
			</div><br /><br />
		</fieldset>

		<!-- Gallery structure section -->
		<fieldset class="dbx-box">
			<h3 title="click-down and drag to move this box" class="dbx-handle"><?php _e('Image Captions', $lg_text_domain) ?></h3>
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

		$imgfiles = get_imgfiles($capdir);

		// Check if $capdir ends with the "/" and providing one if not
		if (substr($capdir, strlen($capdir)-1, strlen($capdir)) != "/")
			$capdir .= "/";

		$handle = fopen($gallery_root.$capdir.'captions.xml', 'wb');

		fwrite($handle, "<?xml version='1.0' encoding='" . get_option('blog_charset') . "'?>\n");
		fwrite($handle, "<data>\n");
		// Folder caption
		$folder_caption = str_replace('\\', '', $_POST['folder_caption']);
		fwrite($handle, "\t<folder>".$folder_caption."</folder>\n");

		// Folder access level
		$folder_access = $_POST['folder_minimum_level'];
		fwrite($handle, "\t<level>".$folder_access."</level>\n");

		if (isset($imgfiles)) {
			foreach ($imgfiles as $img) {
				// prepare the strings to be written
				$form_value = str_replace('.', '_', $img);
				$form_value = str_replace(' ', '_', $form_value);
				$dirty_caption = $_POST[$form_value];
				$clean_caption = str_replace('\\', '', $dirty_caption);
				// write strings
				fwrite($handle, "\t<photo id='".$img."'>\n");
				fwrite($handle, "\t\t<caption>".$clean_caption ."</caption>\n");
				fwrite($handle, "\t</photo>\n");
			}
		}

		fwrite($handle, "</data>");
		fclose($handle);

		@chmod($gallery_root.$capdir.'captions.xml', 0666);
	}
	else {
		lg_cannot_rw($capdir);
	}
}

function lg_cannot_rw($rwdir){
	global $gallery_root;

	?>
	<div style='background-color: rgb(207, 235, 247);' id='message' class='updated fade'>
		<b>WARNING:</b><br />
		<p>
			Unable to create xml file inside the folder. <br />
			File permissions are actually set to
			<b><?php echo substr(sprintf('%o', fileperms($gallery_root.$rwdir)), -4) ?></b><br />
			Try to set them to <b>777</b>
		</p>
	</div>
	<?php
}

// ============= Captions Utility Functions =============

function lg_clear_directory($path) {
	if($dir_handle = opendir($path)) {
		while($file = readdir($dir_handle)) {
			if($file == "." || $file == "..") {
				continue;
			} else {
				unlink($path.$file);
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
	global $gallery_root, $gallery_address;

	$folder = $gallery_root.$upload;

	if ( !get_settings('lg_fileupload_minlevel') )
		die (__("You are not allowed to upload files", $lg_text_domain));

	$allowed_types = explode(' ', trim(strtolower(get_settings('lg_fileupload_allowedtypes'))));

	foreach ($allowed_types as $type) {
		$type_tags[] = "<code>$type</code>";
	}

	$i = implode(', ', $type_tags);

	?>
	<p><?php printf(__('You can upload files with the extension %1$s as long as they are no larger than %2$s <abbr title="Kilobytes">KB</abbr>.', $lg_text_domain), $i, get_settings('lg_fileupload_maxk')); ?></p>
	<form action=""  method="post" enctype="multipart/form-data">
		<p>
			<label for="img1"><?php _e('File:', $lg_text_domain) ?></label>
			<br />
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo get_settings('lg_fileupload_maxk') * 1024 ?>" />
			<input type="file" name="img1" id="img1" size="100" />
		</p>
		<p><input type="submit" name="upload" class="button" value="<?php _e('Upload File', $lg_text_domain) ?>" /></p>
	</form>
	<?php
}
?>