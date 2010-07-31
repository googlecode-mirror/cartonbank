<?php
require_once('lazyest-gallery.php');
global $gallery_root;
define('AL_FLM_PAGE', 'admin.php?page=lazyest-gallery/mass-upload.php');
get_artist_id_by_folder_name();

/*
//print_r ($HTTP_POST_VARS); 
//print_r ($_POST); 
echo '<pre>';
var_dump($_POST);
echo '</pre>';
exit;
*/


// Actions ==========================================================
	if (isset ($_POST['img_path']))
	{
		al_write_to_db();
	}
al_build_captions_form();
// stop actions ==========================================================

// start functions ==========================================================

function al_build_captions_form(){
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
		al_generate_xml($_POST['directory']);
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

						<form action="<?php echo AL_FLM_PAGE; ?>&amp;captions=<?php echo $capdir; ?>" method="post" enctype="multipart/form-data">
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
					<p><a href="<?php echo AL_FLM_PAGE; ?>&amp;captions=<?php echo $capdir; ?>"><?php _e('Upload another') ?></a></p>

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
		$hirez_to_delete = $gallery_root.$act_current.$capdir.basename($_POST['delete_this']);
		$thumb_to_delete = $gallery_root.$act_current.$capdir.get_option('lg_thumb_folder').basename($_POST['delete_this']);

		if (!is_dir($hirez_to_delete)) {
			if(@unlink($hirez_to_delete)) {
				@unlink($thumb_to_delete);
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


/*
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
*/
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
		<form name="delete_image_file" method="post" action="<?php echo AL_FLM_PAGE; ?>&amp;captions=<?php echo $capdir; ?>" style="padding:5px;">
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

	echo '<h2>'. __('Подписать и отправить на сайт', $lg_text_domain) .'</h2>';

	if (strlen($capdir) > 0) {

	?>

		<fieldset class="options">
			<h3><?php echo sprintf(__('Список картинок в папке %s', $lg_text_domain), $act_current); ?></h3>

			<!-- Shortcuts-->
			<div style="padding:5px;display:block;background:#efefef;border:1px solid #ccc;height:20px;">
				<a href="<?php echo AL_FLM_PAGE; ?>">&laquo; <?php _e('Назад к списку папок', $lg_text_domain); ?></a>
			</div>
			<!-- End of Shortcuts-->

			<br />

			<!-- Tip div -->
			<!-- <div style="padding:5px;border:1px solid #3fbd3f;background:#beffbe;color:#088000;">
				<?php _e('You can use HTML links but be sure to use "[" and "]" instead of "<" and ">"; something like [a href="somewhere"]Link[/a]', $lg_text_domain); ?>
			</div> -->
			<!-- End of tip div -->

			<form name="gallery_captions" method="post" action="<?php echo AL_FLM_PAGE; ?>&amp;captions=<?php echo $capdir; ?>">
				<input type="hidden" name="folder" value="<?php echo $act_current ?>"/>
				<table summary="thumbs" cellspacing="1" cellpadding="10">
					


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
				echo '<br />&raquo; '.$img;

				$size = getimagesize($gallery_root.$act_current.$img);
				echo '<br>'.$size[0]."x".$size[1]." px";
				?>
				<br><br><a href="<?php echo AL_FLM_PAGE ?>&amp;captions=<?php echo str_replace(" ", "%20", $act_current); ?>&amp;file_to_delete=<?php echo $righturl ?>" class="button" style="width:50px;display:block;"><?php _e('Delete', $lg_text_domain); ?></a>
				<?
			} else { // otherwise
				$righturl = str_replace(" ", "%20", get_option('siteurl')."/wp-content/plugins/lazyest-gallery/".basename(__FILE__).".php?file=". $act_current.$img."&amp;thumb=1");
				echo "<img src='".$righturl."' alt='".$img."' title='".$title."' />";
			}

			// this time we need a "rebuilded" (with "" and "") caption
			$caption = clean_image_caption($img, $capdir, false);

			// this is a dynamic form name construct that we need for the captions system
			$form_name = str_replace('.', '_', $img);
			$form_name = str_replace(' ', '_', $form_name);

			echo '</a></td>';


			echo '<td align=right>';
			$righturl = str_replace(" ", "%20", $gallery_root.$act_current.$img);
			// Inputs
			?>
				<form id="ImageUploadToDB" method="post" action="<?php echo AL_FLM_PAGE; ?>&amp;captions=<?php echo $capdir; ?>">
				Название: <input type="text" name="img_title" style="width:95%;"><br>
				Описание: <textarea name="img_description" rows="3" cols="20" style="width:95%;"></textarea><br>
				Ключевые слова:<input type="text" name="img_tags" style="width:95%;"><br>
				Категория: <?php echo topcategorylist(0);?><br>
				<!-- categ<input type="hidden" name="category" value="3" /> -->
				<input type="submit" name="submit" value="Сохранить">
				<input type="hidden" name="artist_id" value="<?php echo get_artist_id_by_folder_name();?>" />
				<input type="hidden" name="img_path" value="<?php echo  $gallery_root.$act_current.$img ?>" />
				<br>
				</form>
			<?php
		}
	}
	?>
				</table>
				<!-- <div class="submit">
					<input type="hidden" name="directory" value="<?php echo $act_current ?>" />
					<input type="submit" name="update_captions" value="<?php _e('Update Folder', $lg_text_domain); ?>" />
				</div> -->
			</form>
		</fieldset>

		<!-- Upload section -->
		<!-- <fieldset class="dbx-box">
			<h3 class="dbx-handle"><?php _e('Upload Image', $lg_text_domain) ?></h3>
			<div class="dbx-content">
				<?php upload_page($act_current); ?>
			</div><br /><br />
		</fieldset>
 -->
	<?php } // Closes the main page if ?>

		<!-- Gallery structure section -->
		<fieldset class="dbx-box">
			<h3 class="dbx-handle"><?php _e('Список папок с картинками', $lg_text_domain) ?></h3>

			<div class="dbx-content">
				<?php al_show_gallery_structure(); ?>
			</div>
		</fieldset>

	</div>

	<?php
}

function dirsize($path,$top=true)
{
   static $total_size=0;
   if ($top==true) $total_size=0;

   if (is_dir($path)) {
       $dir=opendir ($path);
       while (($item = readdir($dir)) !== false) {
           if ($item=="." || $item=="..") continue;
           $full_path="$path/$item";
           if (is_file($full_path))
               $total_size+=filesize($full_path);
           else if (is_dir($full_path))
               dirsize($full_path,false);
       }
       closedir($dir);
       return $total_size;
   }else
       return -1;
}

function count_files($path)
{
	if ($handle = opendir($path)) {
	   while (false !== ($file = readdir($handle))) {
		   if ($file != "." && $file != "..") {
			   $file = $path.$file;
			   if(is_file($file)) $numfile++;
			   if(is_dir($file))  $numdir++;
		   };
	   };
	   closedir($handle);
	};
	if ($numfile > 0)
	{
		return $numfile;
	}
	else
	{
		return 0;
	}
}

function al_show_gallery_structure(){
	global $gallery_root, $lg_text_domain;

	echo "<div style='font-family:monospace;background:#efefef;padding:5px;border:1px solid #ccc;'>\n";
	echo "<ul>\n";

	if (file_exists($gallery_root)){
		if ($dir_content = opendir($gallery_root)) {
			while ( false !== ($dir = readdir($dir_content)) ) {
				if (is_dir($gallery_root.$dir) && !in_array($dir, get_option('lg_excluded_folders')) && $dir!='.' && $dir!='..' ) {
					al_show_structure($dir);
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

function al_show_structure($sscurrentdir){
	global $excluded_folders, $gallery_root, $lg_text_domain;

	if( substr($sscurrentdir, strlen($sscurrentdir)-1 ) != '/'){
		$sscurrentdir .= '/';
	}
	$count_files = count_files($gallery_root.$sscurrentdir);
	//$count_size = dirsize($gallery_root.$sscurrentdir,true);
	$righturl = urlencode($sscurrentdir);
	echo '<li><a href="'. AL_FLM_PAGE .'&amp;captions=' . $righturl . '">' . $sscurrentdir . '</a> ['.$count_files.']';

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
				al_show_structure($subdir);
			}
			echo "</ul>\n";
		}
	}
	echo '</li>';
}

function al_generate_xml($capdir){
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

function get_artist_id_by_folder_name(){
	global $wpdb;
	$sql = "SELECT `brand_id` FROM `wp_brand_folder` WHERE `folder_name`= '".$_GET['captions']."'";
	$artist_ids = $wpdb->get_results("SELECT `brand_id` FROM `wp_brand_folder` WHERE `folder_name`= '".$_GET['captions']."'");
	$artist_id = $artist_ids[0]->brand_id;
	return $artist_id;
}

function topcategorylist($offset)
  {
  global $wpdb,$category_data;
  $options = "";
  $values = $wpdb->get_results("SELECT * FROM `wp_product_categories` WHERE `active`='1' ORDER BY `id` ASC",ARRAY_A);
  if($values != null)
    {
    foreach($values as $option)
      {
      $category_data[$option['id']] = $option['name'];
      if($_GET['catid'] == $option['id'])
        {
        $selected = "selected='selected'";
        }
      $options .= "<option $selected value='".$option['id']."'>".$option['name']."</option>\r\n";
      $selected = "";
      }
    }
  $concat .= "<select name='category_id'>".$options."</select>\r\n";
  return $concat;
  }

function al_write_to_db(){
	if (isset ($_POST['img_path']))
		global $wpdb, $mimetype;
		{
			$img_title = $_POST['img_title'];
			$img_description = $_POST['img_description'];
			$img_tags = $_POST['img_tags'];
			$category_id = $_POST['category_id'];
			$artist_id = $_POST['artist_id'];
			$img_path = $_POST['img_path'];
			$file_name = basename($img_path);

			  $basepath = str_replace("/wp-admin", "" , getcwd());
			  $imagedir = $basepath."/wp-content/plugins/wp-shopping-cart/images/";
			  $product_images = $basepath."/wp-content/plugins/wp-shopping-cart/product_images/";
			  $filedir = $basepath."/wp-content/plugins/wp-shopping-cart/files/";
			  $preview_clips_dir = $basepath."/wp-content/plugins/wp-shopping-cart/preview_clips/";
			  $image = '';
				$height = get_option('product_image_height');
				$width  = get_option('product_image_width');

			// copy and resize files
			if(!is_dir($product_images))
			  {
			  mkdir($product_images);
			  }

			if(function_exists("getimagesize"))
			  {
			  copy($img_path, $product_images.$file_name);
			  copy($img_path, $imagedir.$file_name);

					//$imgsize = getimagesize($product_images.$file_name);
					$imgsize = getimagesize($product_images.$file_name);

					
					///homepages/35/d89900836/htdocs/cb/wp-content/plugins/wp-shopping-cart/product_images/bogorad081gp.jpg
					$file_w = $imgsize[0];
					$file_h = $imgsize[1];

					//ales here we replace slides to that from LG
					$chwidth = get_option('lg_pictwidth'); // crop size
					$chheight = get_option('lg_pictheight'); // crop size
					$thatdir = $product_images; //destination dir
					$ifolder = ''; //subfolder for artist
					$resample_quality = 85; //image quality

					al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file_name, $resample_quality);	
					//$wm = "/home/www/z58365/cb/wp-content/plugins/wp-shopping-cart/images/watermark.gif";
					$wm = $basepath."/wp-content/plugins/lazyest-gallery/watermark.gif";
					wtrmark($thatdir.$file_name,$wm);

					// ales here we replace thumbs to that from LG 
					$chwidth = $width; // crop size
					$chheight = $height; // crop size
					$thatdir = $imagedir; //destination dir

					al_create_cropped_file($chwidth, $chheight, $thatdir, $ifolder, $file_name, $resample_quality);	  
					$image = $wpdb->escape($file_name);

			// write to database

			$timestamp = time();
			$sql = "INSERT INTO `wp_product_files` ( `id` , `filename`  , `mimetype` , `idhash` , `date` , `width`, `height`) VALUES ( '' , '', '', '', '$timestamp', '', '');";
			
			$wpdb->query($sql);
			$fileid_raw = $wpdb->get_results("SELECT `id` FROM `wp_product_files` WHERE `date` = '$timestamp'",ARRAY_A);
			$fileid = $fileid_raw[0]['id'];
			$idhash = sha1($fileid);


			if(copy($img_path,($filedir.$idhash)))
			  {
				$sql = "UPDATE `wp_product_files` SET `filename` = '".$file_name."', `mimetype` = '".$mimetype."', `idhash` = '$idhash', `width` = '$file_w', `height` = '$file_h' WHERE `id` = '$fileid' LIMIT 1";
				$wpdb->query($sql);

				$insertsql = "INSERT INTO `wp_product_list` ( `id` , `name` , `description` , `additional_description` , `price` , `pnp`, `international_pnp`, `file` , `image` , `category`, `brand`, `quantity_limited`, `quantity`, `special`, `special_price`,`display_frontpage`, `notax` ) VALUES ('', '".$img_title."', '".$img_description."', '".$img_tags."','', '', '', '".$fileid."', '".$file_name."', '".$category_id."', '".$artist_id."', '$quantity_limited','$quantity','$special','$special_price','$display_frontpage','$notax');";

				if($wpdb->query($insertsql))
				{
					$product_id_data = $wpdb->get_results("SELECT LAST_INSERT_ID() AS `id` FROM `wp_product_list` LIMIT 1",ARRAY_A);
					$product_id = $product_id_data[0]['id'];
				}
					$sql = "INSERT INTO `wp_item_category_associations` ( `id` , `product_id` , `category_id` ) VALUES ('', '".$product_id."', '".$category_id."')";
					$wpdb->query($sql);

					unlink($img_path);
			  }

		}
	}
}

function al_create_cropped_file($chwidth, $chheight, $thatdir, $ifolder, $file, $resample_quality = '85') {
	// Cropped thumbs creation (contributed by: dodo - http://pure-essence.net)
	global $mimetype; 
	$gallery_root = '';
	$img_location = $gallery_root.$thatdir.$file;

	// Getting width ([0]) and height ([1]) maybe add options
    $size_bits = getimagesize($img_location);

	// Creating a resource image
	$path = pathinfo($img_location);

	switch(strtolower($path["extension"])){
		case "jpeg":
		case "jpg":
			$img = imagecreatefromjpeg($img_location);
			$mimetype = "image/jpeg";
			break;
		case "gif":
			$img = imagecreatefromgif($img_location);
			$mimetype = "image/gif";
			break;
		case "png":
			$img = imagecreatefrompng($img_location);
			$mimetype = "image/png";
			break;
		default:
			break;
	}

	if($size_bits[0] > $chwidth || $size_bits[1] > $chheight) {

		// Resize the image
		$resized = imagecreatetruecolor($chwidth, $chheight);

		$o_width = $size_bits[0];
		$o_height = $size_bits[1];

		// if the image is more wide than high
		if($o_width > $o_height) {
			// landscape image
			$out_width = $o_height;
			$out_height = $o_height;
			$cutoff = round(($o_width - $o_height) / 2);

			$out_left = $cutoff;
			$out_top = 0;
		} else {
			$cutoff = round(($o_height - $o_width) / 2);

			$out_width = $o_width;
			$out_height = $o_width;

			$out_left = 0;
			$out_top = $cutoff;
		}

		// Resampling the image
		imagecopyresampled ($resized, $img, 0, 0, $out_left, $out_top, $chwidth, $chheight, $out_width, $out_height);

		if (is_writable($gallery_root.$thatdir.$ifolder)){
			switch(strtolower($path["extension"])){
				case "jpeg":
				case "jpg":
					imagejpeg($resized, $gallery_root.$thatdir.$ifolder.'/'.$file, $resample_quality);
					break;
				case "gif":
					imagegif($resized, $gallery_root.$thatdir.$ifolder.'/'.$file);
					break;
				case "png":
					imagepng($resized, $gallery_root.$thatdir.$ifolder.'/'.$file);
					break;
				default:
					break;
			}
		} else {
			echo "<div class='error'><b>WARNING:</b> Unable to create $ifolder inside $thatdir. <br />";
			echo "Check your permissions.</div><br />";
		}

		imagedestroy($resized);
	} else {
		switch(strtolower($path["extension"])){
			case "jpeg":
			case "jpg":
				imagejpeg($img, $gallery_root.$thatdir.$ifolder.'/'.$file, $resample_quality);
				break;
			case "gif":
				imagegif($img, $gallery_root.$thatdir.$ifolder.'/'.$file);
				break;
			case "png":
				imagepng($img, $gallery_root.$thatdir.$ifolder.'/'.$file);
				break;
			default:
				break;
		}
	}
 }

 function al_create_resized_file($chwidth, $chheight, $thatdir, $ifolder, $file, $thumb, $resample_quality = '85') {
	// Default thumbs creation
	// global $gallery_root;
	$gallery_root = '';
	$img_location = $gallery_root.$thatdir.$file;

	// Creating a resource image
	$path = pathinfo($img_location);

	switch(strtolower($path["extension"])){
		case "jpeg":
		case "jpg":
			$img = imagecreatefromjpeg($img_location);
			break;
		case "gif":
			$img = imagecreatefromgif($img_location);
			break;
		case "png":
			$img = imagecreatefrompng($img_location);
			break;
		default:
			break;
	}

	$xratio = $chheight/(imagesx($img));
	$yratio = $chwidth/(imagesy($img));

	if($xratio < 1 || $yratio < 1) {
		if($xratio < $yratio)
			$resized = imagecreatetruecolor($chwidth,floor(imagesy($img)*$xratio));
		else
			$resized = imagecreatetruecolor(floor(imagesx($img)*$yratio), $chheight);

		imagecopyresampled($resized, $img, 0, 0, 0, 0, imagesx($resized)+1,imagesy($resized)+1,imagesx($img),imagesy($img));

		if (is_writable($gallery_root.$thatdir.$ifolder)){
			switch(strtolower($path["extension"])){
				case "jpeg":
				case "jpg":
					imagejpeg($resized, $gallery_root.$thatdir.$ifolder.'/'.$file, $resample_quality);
					break;
				case "gif":
					imagegif($resized, $gallery_root.$thatdir.$ifolder.'/'.$file);
					break;
				case "png":
					imagepng($resized, $gallery_root.$thatdir.$ifolder.'/'.$file);
					break;
				default:
					break;
			}
		}
		else {
			echo "<div class='error'><b>WARNING:</b> Unable to create images inside $ifolder. <br />";
			echo "Check your permissions.</div>";
		}

		imagedestroy($resized);

	} else {
		switch(strtolower($path["extension"])){
			case "jpeg":
			case "jpg":
				imagejpeg($img, $gallery_root.$thatdir.$ifolder.'/'.$file, $resample_quality);
				break;
			case "gif":
				imagegif($img, $gallery_root.$thatdir.$ifolder.'/'.$file);
				break;
			case "png":
				imagepng($img, $gallery_root.$thatdir.$ifolder.'/'.$file);
				break;
			default:
				break;
		}
	}
	//	imagedestroy($img); 
 }
  

function wtrmark($sourcefile, $watermarkfile) {
   #
   # $sourcefile = Filename of the picture to be watermarked.
   # $watermarkfile = Filename of the 24-bit PNG watermark file.
   #
   //$watermarkfile = "watermark.gif";
  
   //Get the resource ids of the pictures
   //$watermarkfile_id = imagecreatefrompng($watermarkfile);
   $watermarkfile_id = imagecreatefromgif($watermarkfile);
  
   imageAlphaBlending($watermarkfile_id, false);
   imageSaveAlpha($watermarkfile_id, true);

   $fileType = strtolower(substr($sourcefile, strlen($sourcefile)-3));

   switch($fileType) {
       case('gif'):
           $sourcefile_id = imagecreatefromgif($sourcefile);
           break;
          
       case('png'):
           $sourcefile_id = imagecreatefrompng($sourcefile);
           break;
          
       default:
           $sourcefile_id = imagecreatefromjpeg($sourcefile);
   }

   //Get the sizes of both pix 
  $sourcefile_width=imageSX($sourcefile_id);
  $sourcefile_height=imageSY($sourcefile_id);
  $watermarkfile_width=imageSX($watermarkfile_id);
  $watermarkfile_height=imageSY($watermarkfile_id);

   $dest_x = ( $sourcefile_width / 2 ) - ( $watermarkfile_width / 2 );
   $dest_y = ( $sourcefile_height / 2 ) - ( $watermarkfile_height / 2 );
  
   // if a gif, we have to upsample it to a truecolor image
   if($fileType == 'gif') {
       // create an empty truecolor container
       $tempimage = imagecreatetruecolor($sourcefile_width,$sourcefile_height);
      
       // copy the 8-bit gif into the truecolor image
       imagecopy($tempimage, $sourcefile_id, 0, 0, 0, 0,
                           $sourcefile_width, $sourcefile_height);
      
       // copy the source_id int
       $sourcefile_id = $tempimage;
   }

   imagecopy($sourcefile_id, $watermarkfile_id, $dest_x, $dest_y, 0, 0,
                       $watermarkfile_width, $watermarkfile_height);

   //Create a jpeg out of the modified picture 
   switch($fileType) {
  
       // remember we don't need gif any more, so we use only png or jpeg.
       // See the upsaple code immediately above to see how we handle gifs
       case('png'):
           //header("Content-type: image/png");
           imagepng ($sourcefile_id,$sourcefile);
           break;
          
       default:
           //header("Content-type: image/jpg"); 
           imagejpeg ($sourcefile_id,$sourcefile);
   }
 
   imagedestroy($sourcefile_id);
   imagedestroy($watermarkfile_id);
  
 }


// end functions ==========================================================
?>


<pre>
<?php
//htmlspecialchars(print_r(get_alloptions()));
//print_r($_POST);
//print_r($_GET);
?>
</pre>

