<?php
/**
 * TODO:
 * - Define $real_file
 * - Define $content
 */

function lg_build_editcss_form() {
	global $lg_text_domain;

	// Gather the style sheet
	$real_file = dirname(__FILE__).'/lazyest-style.css';
	// Reading open and store the content into $content
	$f = fopen($real_file, 'r');
	$content = fread($f, filesize($real_file));
	$content = htmlspecialchars($content);
	// Gather the file name
	$file_show = basename( $real_file );
	$updated = false;

	if (isset($_POST['action'])) {
		$newcontent = stripslashes($_POST['newcontent']);
		if (is_writeable($real_file)) {
			$f = fopen($real_file, 'w+');
			fwrite($f, $newcontent);
			fclose($f);
		} else {
			echo '<p><em>'. __('Cannot update the file, check your permissions.', $lg_text_domain) .'</em></p>';
		}
		$content = htmlspecialchars($newcontent);
		$updated = true;
	}

	?>
	<div class="wrap">
		<?php if ($updated) { ?>
			<div id="message" class="updated fade"><p><?php _e('File edited successfully.', $lg_text_domain) ?></p></div>
		<?php } ?>
		<!-- Shortcuts-->
		<div style="padding:5px;display:block;background:#efefef;border:1px solid #ccc;height:20px;">
			<a onClick="history.go(-1)" style="cursor:pointer;float:left">&laquo; <?php _e('Back', $lg_text_domain); ?></a>
			<a href="<?php echo get_settings('siteurl'); ?>/wp-admin/<?php echo LG_ADM_PAGE; ?>" style="float:right">&laquo; <?php _e('Admin page', $lg_text_domain); ?></a>
		</div>
		<!-- End of Shortcuts-->

		<?php
			if ( is_writeable($real_file) ) {
				echo '<h2>' . sprintf(__('Editing <code>%s</code>', $lg_text_domain), $file_show) . '</h2>';
			} else {
				echo '<h2>' . sprintf(__('Browsing <code>%s</code>', $lg_text_domain), $file_show) . '</h2>';
			}
		?>

		<form name="template" id="template" method="post" action="">

			<div style="margin:0 auto;">
				<textarea cols="80" rows="25" name="newcontent" id="newcontent"><?php echo $content ?></textarea>
				<input type="hidden" name="action" value="update" />
		 	</div>

			<?php if (is_writeable($real_file)) { ?>
				<p class="submit">
					<?php
						echo "<input type='submit' name='submit' value='" . __('Update File', $lg_text_domain) . " &raquo;' />";
					?>
				</p>
			<?php } else { ?>
				<p><em><?php _e('If this file were writable you could edit it.', $lg_text_domain); ?></em></p>
			<?php } ?>
		</form>
	</div>
	<?php
}

?>
