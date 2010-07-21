<?php
/**
 *	This file contains the code for the Wizard form
 */

function lg_wizard_form() {
	global $lg_text_domain;

	echo "<div class='wrap'>";

	// if update button is clicked check the cases
	if (isset($_POST['update_wizard_options'])){
		$user_ok = false;
		$passwd_ok = false;

		// Username
		if (isset($_POST['wizard_username']) && strlen($_POST['wizard_username']) != 0){
			update_option('lg_wizard_user', $_POST['wizard_username']);
			$user_ok = true;
		} else {
			echo "<div id='message' class='error fade'><p>";
			_e('You have to provide a valid username!');
			echo "</p></div>";
		}

		// Password
		if (isset($_POST['wizard_password']) && strlen($_POST['wizard_password']) != 0){
			update_option('lg_wizard_password', base64_encode($_POST['wizard_password']));
			$passwd_ok = true;
		} else {
			echo "<div id='message' class='error fade'><p>";
			_e('You have to provide a valid password!');
			echo "</p></div>";
		}

		if ($passwd_ok && $user_ok){
			echo "<div id='message' class='updated fade'><p>";
			_e('Options updated successfully');
			echo "</p></div>";
		}

		unset($_POST);
	}

?>

	<fieldset class="options"><legend><?php _e('Microsoft Publisher Wizard Options', $lg_text_domain) ?></legend>

		<div style="font-size:x-small;text-align:left;color:red;">
			<p><?php _e('WARNING: This feature is for Windows XP clients only, don\'t try to use on other operating systems!', $lg_text_domain); ?></p>
		</div>

		<?php if (get_option('lg_wizard_user') == "test" && get_option('lg_wizard_password') == "secret") { ?>
			<div id='message' class='error fade'>
				<b><?php _e('It is highly recomended to change your Username and Password!', $lg_text_domain); ?></b>
				<p><?php _e('Default Username and Passwords are:', $lg_text_domain); ?></p>
					<ul>
						<li><?php _e('Username:', $lg_text_domain); ?> <code>test</code></li>
						<li><?php _e('Password:', $lg_text_domain); ?> <code>secret</code></li>
					</ul>
			</div>
		<?php } ?>

		<form style="padding-top:20px;" action=""  method="post" enctype="multipart/form-data">
			<table summary="wizard">
				<tr>
					<td><?php _e('Username:', $lg_text_domain); ?> </td><td><input type="text" name="wizard_username" value="<?php echo get_settings('lg_wizard_user'); ?>"  size="25" class="code" /></td>
				</tr>
				<tr>
					<td><?php _e('Password:', $lg_text_domain); ?> </td><td><input type="password" name="wizard_password" value="<?php echo get_settings('lg_wizard_password'); ?>"  size="25" class="code" /></td>
				</tr>
				<tr>
					<td style="vertical-align:middle"><?php _e('Download Registry File:', $lg_text_domain); ?> &raquo;</td>
					<td style="text-align:center;">
						<a href="<?php echo get_settings('home'); ?>/wp-content/plugins/lazyest-gallery/lazyest-wizard.php?step=reg">
							<img src="<?php echo get_settings('home'); ?>/wp-content/plugins/lazyest-gallery/images/reg.jpg" alt="Windows Registry File" />
						</a>
					</td>
				</tr>
			</table>

			<input class="button" type="submit" name="update_wizard_options" value="<?php	_e('Update options', $lg_text_domain)	?>" title="<?php _e('Submit and saves changes', $lg_text_domain) ?>" />
		</form>
	</fieldset>
</div>
<?php

}

?>