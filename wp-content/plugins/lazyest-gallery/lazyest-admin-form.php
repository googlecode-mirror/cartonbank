<?php
// ===================== Admin form ====================
global $lg_text_domain, $user_level;

require_once('lazyest-filemanager.php');
require_once('lazyest-gallery.php');

if ($user_level == 10) {

?>

<div class="wrap">
	<h2>Lazyest Gallery <?php echo LG_VERSION; ?> Options</h2>
	<div id="poststuff">
		<input type="hidden" name="action" value="update" />

		<form name="gal_options" method="post" action="<?php echo LG_ADM_PAGE; ?>">

			<script type="text/javascript" src="../wp-includes/js/dbx.js"></script>
			<script type="text/javascript">
				//<![CDATA[
					addLoadEvent( function() {
						var manager = new dbxManager('postmeta');       //session ID [/-_a-zA-Z0-9/]
					});
				//]]>
			</script>
			<script type="text/javascript" src="../wp-includes/js/dbx-key.js"></script>

			<!-- Side Boxes -->

			<div id="moremeta">
				<div id="grabit" class="dbx-group">
					<!-- Main Menu -->
					<fieldset id="main-menu-div" class="dbx-box">
						<h3 title="click-down and drag to move this box" class="dbx-handle"><?php _e('Menu', $lg_text_domain); ?></h3>
						<div class="dbx-content" style="font-size:x-small">

							<?php
								// Check for new versions
								$filename = "http://lazyest.keytwo.net/pub/service/version.txt";
								$act_version = '';
								if ($handle = @fopen($filename, "rb")) {
									while (!feof($handle)) {
										$act_version .= fread($handle, 8192);
									}
									fclose($handle);
									if (LG_VERSION == $act_version) {
										?><div style="font-size:x-small;text-align:center;padding:0px;width:95%;border:1px solid #3fbd3f;background:#beffbe;color:#088000;">
											<?php _e('Your LG installation is up to date.', $lg_text_domain);?>
										</div><?php
									} else {
										?><div style="font-size:x-small;text-align:center;padding:2px;color:red;width:92%;border:1px solid #ff0000;background:#ffdddd">
											<?php _e('A new vesion of LG is available for download:', $lg_text_domain);
												// Check for zip or tar...
												$tar = "http://lazyest.keytwo.net/pub/lazyest_gallery_$act_version.tar.gz";
												$zip = "http://lazyest.keytwo.net/pub/lazyest_gallery_$act_version.zip";
												$filednl = '';
												if ($handle = @fopen($tar, "rb")){
													$filednl .= $tar;
													fclose($handle);
												} else if ($handle = @fopen($zip, "rb")){
													$filednl .= $zip;
													fclose($handle);
												}
												// ...and returns the right url
											?>
											<a href="<?php echo $filednl; ?>" title="Download it!" style="text-decoration:blink !important;"><?php echo " ".$act_version; ?></a>
										</div><?php
									}
								}
								// End of check for new versions
							?>

							<ul style="padding-left:15px;">
								<li><a onClick="history.go(-1)" style="cursor:pointer;"><?php _e('Back', $lg_text_domain); ?></a></li>
								<li><a href="<?php echo get_option('lg_gallery_uri'); ?>"><?php _e('View Gallery', $lg_text_domain); ?></a></li>
								<li><a href="<?php echo LG_FLM_PAGE ?>"><?php _e('File Manager', $lg_text_domain); ?></a></li>
								<li><a href="?page=lazyest-gallery/lazyest-admin.php&amp;edit_css=true"><?php _e('Edit LG Style Sheet', $lg_text_domain); ?></a></li>
							</ul>
						</div>
					</fieldset>
					<!-- End of Main Menu -->

					<!-- Lightbox support div	 -->
					<?php if (some_lightbox_plugin() || get_option('lg_force_lb_support') == "TRUE") { ?>
						<fieldset id="lightbox-div" class="dbx-box">
							<h3 title="click-down and drag to move this box" class="dbx-handle"><?php _e('Lightbox Options', $lg_text_domain); ?></h3>
							<div class="dbx-content" style="font-size:x-small">
								<div style="font-size:x-small;text-align:center;padding:0px;color:red;width:95%;border:1px solid #ff0000;background:#ffdddd">
									<?php _e('EXPERIMENTAL', $lg_text_domain); ?>
								</div>
								<br />
								<input type="checkbox" name="enable_lb_support" value="TRUE" <?php if(get_option('lg_enable_lb_support') == "TRUE") echo "checked='checked'"; ?> />
								<?php _e('Enable Lightbox Support', $lg_text_domain); echo "<br />\n"; ?>
								<br />
								<div style="font-size:x-small;color:red;width:87%;border:1px solid #ff0000;background:#ffdddd">
									<?php _e('WARNING: This will enable slides cache system too, and enabling LB in slide view will disable popups', $lg_text_domain); ?>
								</div>
								<?php if(get_option('lg_enable_lb_support') == "TRUE"){ ?>
									<ul style="padding-left:15px;">
										<li>
											<label for="enable_lb_thumbs_support" class="selectit">
												<input type="checkbox" name="enable_lb_thumbs_support" value="TRUE" <?php if(get_option('lg_enable_lb_thumbs_support') == "TRUE") echo "checked='checked'"; ?> />
												<?php _e('Use lightbox for thumbs', $lg_text_domain); ?>
											</label>
										</li>
										<li>
											<label for="enable_lb_slides_support" class="selectit">
												<input type="checkbox" name="enable_lb_slides_support" value="TRUE" <?php if(get_option('lg_enable_lb_slides_support') == "TRUE") echo "checked='checked'"; ?> />
												<?php _e('Use lightbox for slides', $lg_text_domain); ?>
											</label>
										</li>
										<li>
											<label for="enable_lb_sidebar_support" class="selectit">
												<input type="checkbox" name="enable_lb_sidebar_support" value="TRUE" <?php if(get_option('lg_enable_lb_sidebar_support') == "TRUE") echo "checked='checked'"; ?> />
												<?php _e('Use lightbox in sidebar', $lg_text_domain); ?>
											</label>
										</li>
										<li>
											<label for="enable_lb_posts_support" class="selectit">
												<input type="checkbox" name="enable_lb_posts_support" value="TRUE" <?php if(get_option('lg_enable_lb_posts_support') == "TRUE") echo "checked='checked'"; ?> />
												<?php _e('Use lightbox in posts', $lg_text_domain); ?>
											</label>
										</li>
									</ul>
								<?php } // Closes the lightbox supported if ?>
							</div>
						</fieldset>
					<?php } // Closes the function_exists() if ?>
					<!-- End of Lightbox support div	 -->

					<!-- Thickbox support div	 -->
					<?php if (some_thickbox_plugin() || get_option('lg_force_tb_support') == "TRUE") { ?>
						<fieldset id="thickbox-div" class="dbx-box">
							<h3 title="click-down and drag to move this box" class="dbx-handle"><?php _e('Thickbox Options', $lg_text_domain); ?></h3>
							<div class="dbx-content" style="font-size:x-small">
								<div style="font-size:x-small;text-align:center;padding:0px;color:red;width:95%;border:1px solid #ff0000;background:#ffdddd">
									<?php _e('EXPERIMENTAL', $lg_text_domain); ?>
								</div>
								<br />
								<input type="checkbox" name="enable_tb_support" value="TRUE" <?php if(get_option('lg_enable_tb_support') == "TRUE") echo "checked='checked'"; ?> />
								<?php _e('Enable Thickbox Support', $lg_text_domain); echo "<br />\n"; ?>
								<br />
								<div style="font-size:x-small;color:red;width:87%;border:1px solid #ff0000;background:#ffdddd">
									<?php _e('WARNING: This will enable slides cache system too, and enabling TB in slide view will disable popups', $lg_text_domain); ?>
								</div>
								<?php if(get_option('lg_enable_tb_support') == "TRUE"){ ?>
									<ul style="padding-left:15px;">
										<li>
											<label for="enable_tb_thumbs_support" class="selectit">
												<input type="checkbox" name="enable_tb_thumbs_support" value="TRUE" <?php if(get_option('lg_enable_tb_thumbs_support') == "TRUE") echo "checked='checked'"; ?> />
												<?php _e('Use thickbox for thumbs', $lg_text_domain); ?>
											</label>
										</li>
										<li>
											<label for="enable_tb_slides_support" class="selectit">
												<input type="checkbox" name="enable_tb_slides_support" value="TRUE" <?php if(get_option('lg_enable_tb_slides_support') == "TRUE") echo "checked='checked'"; ?> />
												<?php _e('Use thickbox for slides', $lg_text_domain); ?>
											</label>
										</li>
										<li>
											<label for="enable_tb_sidebar_support" class="selectit">
												<input type="checkbox" name="enable_tb_sidebar_support" value="TRUE" <?php if(get_option('lg_enable_tb_sidebar_support') == "TRUE") echo "checked='checked'"; ?> />
												<?php _e('Use thickbox in sidebar', $lg_text_domain); ?>
											</label>
										</li>
										<li>
											<label for="enable_tb_posts_support" class="selectit">
												<input type="checkbox" name="enable_tb_posts_support" value="TRUE" <?php if(get_option('lg_enable_tb_posts_support') == "TRUE") echo "checked='checked'"; ?> />
												<?php _e('Use thickbox in posts', $lg_text_domain); ?>
											</label>
										</li>
									</ul>
								<?php } // Closes the lightbox supported if ?>
							</div>
						</fieldset>
					<?php } // Closes the function_exists() if ?>
					<!-- End of Thickbox support div	 -->

					<!-- GD Info data -->
					<fieldset id="gddiv" class="dbx-box">
						<h3 title="click-down and drag to move this box" class="dbx-handle"><?php _e('GD Library Infos', $lg_text_domain); ?></h3>
						<div class="dbx-content" style="font-size:x-small">
							<?php
								// This code will display GD infos
								lg_admin_describeGDdyn();
							?>
						</div>
					</fieldset>
					<!-- End GD Info data -->

					<!-- ExIF Data -->
					<fieldset id="exifdiv" class="dbx-box">
						<h3 title="click-down and drag to move this box" class="dbx-handle"><?php _e('Enable ExIF Info', $lg_text_domain); ?></h3>
							<div class="dbx-content">

							<label for="enable_exif" class="selectit">
								<input type="checkbox" name="enable_exif" value="TRUE" <?php if(get_option('lg_enable_exif') == "TRUE") echo "checked='checked'"; ?> />
								<?php _e('Enable ExIF Data', $lg_text_domain); ?>
							</label>
							<p><?php _e('Choose which ExIF data to display', $lg_text_domain); ?></p>

							<label for="exif_errors" class="selectit">
								<input type="checkbox" name="exif_errors" value="TRUE" <?php if(get_option('lg_exif_print_error') == "TRUE") echo "checked='checked'"; ?> />
								<?php _e('Errors', $lg_text_domain); ?>
							</label>
							<label for="exif_valid_jpeg" class="selectit">
								<input type="checkbox" name="exif_valid_jpeg" value="TRUE" <?php if(get_option('lg_exif_valid_jpeg') == "TRUE") echo "checked='checked'"; ?> />
								Valid Jpeg
							</label>
							<label for="exif_valid_jfif_data" class="selectit">
								<input type="checkbox" name="exif_valid_jfif_data" value="TRUE" <?php if(get_option('lg_exif_valid_jfif_data') == "TRUE") echo "checked='checked'"; ?> />
								Valid JFIF Data
							</label>

							<label>JFIF:</label>
							<ul style="padding-left:20px;">
								<li><label for="exif_jfif_size" class="selectit">
									<input type="checkbox" name="exif_jfif_size" value="TRUE" <?php if(get_option('lg_exif_jfif_size') == "TRUE") echo "checked='checked'"; ?> />
									JFIF Size
								</label></li>
								<li><label for="exif_jfif_identifier" class="selectit">
									<input type="checkbox" name="exif_jfif_identifier" value="TRUE" <?php if(get_option('lg_exif_jfif_identifier') == "TRUE") echo "checked='checked'"; ?> />
									Identifier
								</label></li>
								<li><label for="exif_jfif_extension_code" class="selectit">
									<input type="checkbox" name="exif_jfif_extension_code" value="TRUE" <?php if(get_option('lg_exif_jfif_extension_code') == "TRUE") echo "checked='checked'"; ?> />
									Extension Code
								</label></li>
								<li><label for="exif_jfif_data" class="selectit">
									<input type="checkbox" name="exif_jfif_data" value="TRUE" <?php if(get_option('lg_exif_jfif_data') == "TRUE") echo "checked='checked'"; ?> />
									Data
								</label></li>
							</ul>

							<label for="exif_valid_exif_data" class="selectit">
								<input type="checkbox" name="exif_valid_exif_data" value="TRUE" <?php if(get_option('lg_exif_valid_exif_data') == "TRUE") echo "checked='checked'"; ?> />
								Valid EXIF Data
							</label>
							<label for="exif_app1_size" class="selectit">
								<input type="checkbox" name="exif_app1_size" value="TRUE" <?php if(get_option('lg_exif_app1_size') == "TRUE") echo "checked='checked'"; ?> />
								APP1 Size
							</label>
							<label for="exif_endien" class="selectit">
								<input type="checkbox" name="exif_endien" value="TRUE" <?php if(get_option('lg_exif_endien') == "TRUE") echo "checked='checked'"; ?> />
								Endien
							</label>
							<label for="exif_ifd0_num_tags" class="selectit">
								<input type="checkbox" name="exif_ifd0_num_tags" value="TRUE" <?php if(get_option('lg_exif_ifd0_num_tags') == "TRUE") echo "checked='checked'"; ?> />
								IFD0 Num Tags
							</label>

							<label>IFD0:</label>
							<ul style="padding-left:20px;">
								<li><label for="exif_ifd0_orientation" class="selectit">
									<input type="checkbox" name="exif_ifd0_orientation" value="TRUE" <?php if(get_option('lg_exif_ifd0_orientation') == "TRUE") echo "checked='checked'"; ?> />
									Orientation
								</label></li>
								<li><label for="exif_ifd0_x_res" class="selectit">
									<input type="checkbox" name="exif_ifd0_x_res" value="TRUE" <?php if(get_option('lg_exif_ifd0_x_res') == "TRUE") echo "checked='checked'"; ?> />
									X Resolution
								</label></li>
								<li><label for="exif_ifd0_y_res" class="selectit">
									<input type="checkbox" name="exif_ifd0_y_res" value="TRUE" <?php if(get_option('lg_exif_ifd0_y_res') == "TRUE") echo "checked='checked'"; ?> />
									Y Resolution
								</label></li>
								<li><label for="exif_ifd0_res_unit" class="selectit">
									<input type="checkbox" name="exif_ifd0_res_unit" value="TRUE" <?php if(get_option('lg_exif_ifd0_res_unit') == "TRUE") echo "checked='checked'"; ?> />
									Resolution Unit
								</label></li>
								<li><label for="exif_ifd0_software" class="selectit">
									<input type="checkbox" name="exif_ifd0_software" value="TRUE" <?php if(get_option('lg_exif_ifd0_software') == "TRUE") echo "checked='checked'"; ?> />
									Software
								</label></li>
								<li><label for="exif_ifd0_time" class="selectit">
									<input type="checkbox" name="exif_ifd0_time" value="TRUE" <?php if(get_option('lg_exif_ifd0_time') == "TRUE") echo "checked='checked'"; ?> />
									Date Time
								</label></li>
								<li><label for="exif_ifd0_offset" class="selectit">
									<input type="checkbox" name="exif_ifd0_offset" value="TRUE" <?php if(get_option('lg_exif_ifd0_offset') == "TRUE") echo "checked='checked'"; ?> />
									ExIF Offset
								</label></li>
							</ul>

							<label for="exif_ifd1_main_offset" class="selectit">
								<input type="checkbox" name="exif_ifd1_main_offset" value="TRUE" <?php if(get_option('lg_exif_ifd1_main_offset') == "TRUE") echo "checked='checked'"; ?> />
								IFD1 Offset
							</label>
							<label for="exif_sub_ifd_num_tags" class="selectit">
								<input type="checkbox" name="exif_sub_ifd_num_tags" value="TRUE" <?php if(get_option('lg_exif_sub_ifd_num_tags') == "TRUE") echo "checked='checked'"; ?> />
								Sub IFD Num Tags
							</label>

							<label>Sub IFD:</label>
							<ul style="padding-left:20px;">
								<li><label for="exif_sub_ifd_color_space" class="selectit">
									<input type="checkbox" name="exif_sub_ifd_color_space" value="TRUE" <?php if(get_option('lg_exif_sub_ifd_color_space') == "TRUE") echo "checked='checked'"; ?> />
									Color Space
								</label></li>
								<li><label for="exif_sub_ifd_width" class="selectit">
									<input type="checkbox" name="exif_sub_ifd_width" value="TRUE" <?php if(get_option('lg_exif_sub_ifd_width') == "TRUE") echo "checked='checked'"; ?> />
									ExIF Image Width
								</label></li>
								<li><label for="exif_sub_ifd_height" class="selectit">
									<input type="checkbox" name="exif_sub_ifd_height" value="TRUE" <?php if(get_option('lg_exif_sub_ifd_height') == "TRUE") echo "checked='checked'"; ?> />
									ExIF Image Height
								</label></li>
							</ul>

							<label for="exif_ifd1_num_tags" class="selectit">
								<input type="checkbox" name="exif_ifd1_num_tags" value="TRUE" <?php if(get_option('lg_exif_ifd1_num_tags') == "TRUE") echo "checked='checked'"; ?> />
								IFD1 Num Tags
							</label>

							<label>IFD1:</label>
							<ul style="padding-left:20px;">
								<li><label for="exif_ifd1_compression" class="selectit">
									<input type="checkbox" name="exif_ifd1_compression" value="TRUE" <?php if(get_option('lg_exif_ifd1_compression') == "TRUE") echo "checked='checked'"; ?> />
									Compression
								</label></li>
								<li><label for="exif_ifd1_x_res" class="selectit">
									<input type="checkbox" name="exif_ifd1_x_res" value="TRUE" <?php if(get_option('lg_exif_ifd1_x_res') == "TRUE") echo "checked='checked'"; ?> />
									X Resolution
								</label></li>
								<li><label for="exif_ifd1_y_res" class="selectit">
									<input type="checkbox" name="exif_ifd1_y_res" value="TRUE" <?php if(get_option('lg_exif_ifd1_y_res') == "TRUE") echo "checked='checked'"; ?> />
									Y Resolution
								</label></li>
								<li><label for="exif_ifd1_res_unit" class="selectit">
									<input type="checkbox" name="exif_ifd1_res_unit" value="TRUE" <?php if(get_option('lg_exif_ifd1_res_unit') == "TRUE") echo "checked='checked'"; ?> />
									Resolution Unit
								</label></li>
								<li><label for="exif_ifd1_jpeg_if_offset" class="selectit">
									<input type="checkbox" name="exif_ifd1_jpeg_if_offset" value="TRUE" <?php if(get_option('lg_exif_ifd1_jpeg_if_offset') == "TRUE") echo "checked='checked'"; ?> />
									Jpeg IF Offset
								</label></li>
								<li><label for="exif_ifd1_jpeg_if_byte_count" class="selectit">
									<input type="checkbox" name="exif_ifd1_jpeg_if_byte_count" value="TRUE" <?php if(get_option('lg_exif_ifd1_jpeg_if_byte_count') == "TRUE") echo "checked='checked'"; ?> />
									Jpeg IF Byte Count
								</label></li>
							</ul>
						</div>
					</fieldset>
					<!-- End ExIF Data -->

				</div> <!-- closes "dbx-group" div -->
			</div> <!--closes "moremeta" div -->

			<!-- End of Side Boxes -->

			<fieldset class="options"><legend><?php _e('Main Gallery Options', $lg_text_domain) ?></legend>
				<table summary="mainoptions" class="editform" cellpadding="5" cellspacing="2" style="width:100%;vertical-align:top;">
					<tr>
						<th scope="row"><?php _e('Your gallery folder: ', $lg_text_domain); ?></th>
						<td>
							<input name="gallery_folder" id="galleryfolder" value="<?php echo get_option('lg_gallery_folder'); ?>" size="60" class="code" type="text" /> <br />
							<span style="font-size:x-small;"><?php _e('End with "/"', $lg_text_domain); ?></span>
							<?php
								if (!(file_exists(ABSPATH.get_option('lg_gallery_folder')))) {
										echo "<div><b style='color:#ff0000;'>" . __('WARNING', $lg_text_domain) . " </b>:
												" . __('Specified gallery folder does not exists', $lg_text_domain) . ": <code>";
										echo get_option('lg_gallery_folder');
										echo "</code></div>";
								}
							?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Your gallery URI: ', $lg_text_domain); ?></th>
						<td>
							<span style="font-size:x-small;color:red"><?php _e('This is the most important option, please provide double check!', $lg_text_domain); ?></span><br />
							<input name="gallery_uri" id="gallery_uri" value="<?php echo get_option('lg_gallery_uri'); ?>" size="60" class="code" type="text" /> <br />
							<span style="font-size:x-small;"><?php _e('The exact address where your gallery is browsable. ie:', $lg_text_domain); ?><br /><code> http://www.exaple.com/index.php?page_id=3</code></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Thumbnails folder: ', $lg_text_domain) ?></th>
						<td>
							<input name="thumbfolder" id="thumbfolder" value="<?php echo get_option('lg_thumb_folder'); ?>" size="60" class="code" type="text" /> <br />
							<span style="font-size:x-small;"><?php _e('If you have enabled cache system this is the folder where your thumbs will be placed', $lg_text_domain) ?></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Slides folder: ', $lg_text_domain) ?></th>
						<td>
							<input name="slidefolder" id="slidefolder" value="<?php echo get_option('lg_slide_folder'); ?>" size="60" class="code" type="text" /> <br />
							<span style="font-size:x-small;"><?php _e('If you have enabled cache system this is the folder where your slides will be placed', $lg_text_domain) ?></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Add folder names to exclude: ', $lg_text_domain) ?></th>
						<td>
							<input name="excludefolder" id="excludefolder" value="<?php echo implode(',', get_option('lg_excluded_folders')); ?>" size="60" class="code" type="text" /> <br />
							<span style="font-size:x-small;"><?php _e('Separate with "," WITHOUT spaces (" ")', $lg_text_domain) ?></span>
						</td>
					</tr>
				</table>
			</fieldset>

			<fieldset class="options"><legend><?php _e('Thumbnails Options', $lg_text_domain) ?></legend>
				<table summary="displaying" class="editform" cellpadding="5" cellspacing="2" style="width:100%;vertical-align:top;">
					<tr>
						<th scope="row"><?php _e('Maximum thumbnails width (small size image): ', $lg_text_domain) ?></th>
						<td><input name="thumbwidth" id="thumbwidth" value="<?php echo get_option('lg_thumbwidth'); ?>" size="10" class="code" type="text" /> px</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Maximum thumbnails height (small size image): ', $lg_text_domain) ?></th>
						<td><input name="thumbheight" id="thumbheight" value="<?php echo get_option('lg_thumbheight'); ?>" size="10" class="code" type="text" /> px</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Sort files and folders by: ', $lg_text_domain) ?></th>
						<td>
							<input type="radio" name="sort_alphabetically" value="TRUE" <?php if(get_option('lg_sort_alphabetically') == "TRUE") echo "checked='checked'"; ?> /><?php _e('Name', $lg_text_domain) ?>	<br />
							<input type="radio" name="sort_alphabetically" value="FALSE" <?php if(get_option('lg_sort_alphabetically') == "FALSE") echo "checked='checked'"; ?> /><?php _e('Date', $lg_text_domain) ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Number of thumbnails per page: ', $lg_text_domain) ?><br /><?php _e('( 0 will disable pagination )', $lg_text_domain) ?></th>
						<td><input name="thumbspage" id="thumbspage" value="<?php echo get_option('lg_thumbs_page'); ?>" size="5" class="code" type="text" /></td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Number of columns you want', $lg_text_domain) ?><br /><?php _e('to split your folders view: ', $lg_text_domain) ?></th>
						<td><input name="folderscolumns" id="folderscolumns" value="<?php echo get_option('lg_folders_columns'); ?>" size="5" class="code" type="text" /></td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Number of columns you want', $lg_text_domain) ?><br /><?php _e('to split your thumbnails view: ', $lg_text_domain) ?></th>
						<td><input name="thumbnailscolumns" id="thumbnailscolumns" value="<?php echo get_option('lg_thumbs_columns'); ?>" size="5" class="code" type="text" /></td>
					</tr>

					<tr>
						<th scope="row"><?php _e('Image to show for each album: ', $lg_text_domain) ?></th>
						<td>
							<input title="<?php _e('put inside the folder an image (jpg, gif or png) with same name as the folder', $lg_text_domain) ?>" type="radio" name="folder_image" value="icon" <?php if(get_option('lg_folder_image') == "icon") echo "checked='checked'"; ?> /> <?php _e('Folder Icon', $lg_text_domain) ?><br />
							<input type="radio" name="folder_image" value="random_image" <?php if(get_option('lg_folder_image') == "random_image") echo "checked='checked'"; ?> /> <?php _e('Random image of this folder', $lg_text_domain) ?> <br />
							<input type="radio" name="folder_image" value="none" <?php if(get_option('lg_folder_image') == "none") echo "checked='checked'"; ?> /> <?php _e('None', $lg_text_domain) ?> <br />
						</td>
				</table>
			</fieldset>

			<fieldset class="options"><legend><?php _e('Slides Options', $lg_text_domain) ?></legend>
				<table summary="displaying" class="editform" cellpadding="5" cellspacing="2" style="width:100%;vertical-align:top;">
					<tr>
						<th scope="row"><?php _e('Maximum slides width (normal size image): ', $lg_text_domain) ?></th>
						<td><input name="pictwidth" id="pictwidth" value="<?php echo get_option('lg_pictwidth'); ?>" size="10" class="code" type="text" /> px</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Maximum slides height (normal size image): ', $lg_text_domain) ?></th>
						<td><input name="pictheight" id="pictheight" value="<?php echo get_option('lg_pictheight'); ?>" size="10" class="code" type="text" /> px</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Use Pop-Up when images are clicked: ', $lg_text_domain) ?></th>
						<td><input type="checkbox" name="use_slides_popup" value="TRUE" <?php if(get_option('lg_use_slides_popup') == "TRUE") echo "checked='checked'"; ?> /></td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Disable fullsize image links: ', $lg_text_domain) ?></th>
						<td><input type="checkbox" name="disable_full_size" value="TRUE" <?php if(get_option('lg_disable_full_size') == "TRUE") echo "checked='checked'"; ?> /></td>
					</tr>
				</table>
			</fieldset>

			<div id="advancedstuff" class="dbx-group">

				<!-- Captions Section -->
				<fieldset class="dbx-box">
					<h3 title="click-down and drag to move this box" class="dbx-handle"><?php _e('Image Captions', $lg_text_domain) ?></h3>
					<div class="dbx-content">
						<table summary="displaying" class="editform" cellpadding="5" cellspacing="2">
							<tr>
								<th scope="row"><?php _e('Enable Captions:', $lg_text_domain) ?></th>
								<td><input type="checkbox" name="enable_captions" value="TRUE" <?php if(get_option('lg_enable_captions') == "TRUE") echo "checked='checked'"; ?> /></td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Use folder captions instead of their name:', $lg_text_domain) ?></th>
								<td><input type="checkbox" name="use_folder_captions" value="TRUE" <?php if(get_option('lg_use_folder_captions') == "TRUE") echo "checked='checked'"; ?> /></td>
							</tr>
						</table>
						<?php
							lg_show_gallery_structure();
						?>
					</div>
				</fieldset>

				<!-- Advanced Options Section -->
				<fieldset class="dbx-box">
					<h3 title="click-down and drag to move this box" class="dbx-handle"><?php _e('Advanced Options', $lg_text_domain) ?></h3>
					<div class="dbx-content">
					<table summary="advanced" class="editform" cellpadding="5" cellspacing="2" style="width:100%;vertical-align:top;">
						<tr>
							<th scope="row"><?php _e('Use Cache System for Thumbnails: ', $lg_text_domain) ?></th>
							<td>
								<input type="checkbox" name="use_cache" value="TRUE" <?php if(get_option('lg_enable_cache') == "TRUE") echo "checked='checked'"; ?> /><br />
								<span style="font-size:x-small;"><?php _e('WARNING: This *may* cause some problem with file permissions due to PHP hosting politics.', $lg_text_domain) ?></span>
								<?php if(get_option('lg_use_cropping') == "TRUE") { ?>
									<br /><span style="font-size:x-small;"><?php _e('WARNING: Disabling this you will also disable Cropping System', $lg_text_domain) ?></span>
								<?php } ?>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Use Cache System for Slides: ', $lg_text_domain) ?></th>
							<td>
								<input type="checkbox" name="use_slides_cache" value="TRUE" <?php if(get_option('lg_enable_slides_cache') == "TRUE") echo "checked='checked'"; ?> /><br />
								<span style="font-size:x-small;"><?php _e('WARNING: This *may* cause some problem with file permissions due to PHP hosting politics.', $lg_text_domain) ?></span>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Use the cropping system for thumbnails: ', $lg_text_domain) ?></th>
							<td>
								<input type="checkbox" name="use_cropping" value="TRUE" <?php if(get_option('lg_use_cropping') == "TRUE") echo "checked='checked'"; ?> /><br />
								<span style="font-size:x-small;"><?php _e('WARNING: This will enable thumbnail cache too.', $lg_text_domain) ?></span>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Buffer size for image processing: ', $lg_text_domain) ?></th>
							<td>
								<input name="buffer_size" id="buffer_size" value="<?php echo get_option('lg_buffer_size'); ?>" size="10" class="code" type="text" /> MB.<br />
								<span style="font-size:x-small;"><?php _e('Increment this value *only* if you cant view some of your image to be processed.', $lg_text_domain) ?></span>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Resample quality: ', $lg_text_domain) ?></th>
							<td>
								<input name="resample_quality" id="resample_quality" value="<?php echo get_option('lg_resample_quality'); ?>" size="10" class="code" type="text" /><br />
								<span style="font-size:x-small;"><?php _e('Images resampled quality, from 0 (low quality) to 100 (best quality). Affects only JPEGs.', $lg_text_domain) ?></span>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Force Lightbox Support: ', $lg_text_domain) ?></th>
							<td>
								<input type="checkbox" name="force_lb_support" value="TRUE" <?php if(get_option('lg_force_lb_support') == "TRUE") echo "checked='checked'"; ?> />
								<span style="font-size:x-small;"><?php _e("This will enable slide's cache system too. It is not safe to do this, do it only if you know what you are doing!", $lg_text_domain) ?></span>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Force Thickbox Support: ', $lg_text_domain) ?></th>
							<td>
								<input type="checkbox" name="force_tb_support" value="TRUE" <?php if(get_option('lg_force_tb_support') == "TRUE") echo "checked='checked'"; ?> />
								<span style="font-size:x-small;"><?php _e("This will enable slide's cache system too. It is not safe to do this, do it only if you know what you are doing!", $lg_text_domain) ?></span>
							</td>
						</tr>
					</table>
					</div>
				</fieldset>

				<!-- Upload Options Section -->
				<fieldset class="dbx-box">
					<h3 title="click-down and drag to move this box" class="dbx-handle"><?php _e('Upload Options', $lg_text_domain) ?></h3>
					<div class="dbx-content">
						<p><?php _e('These settings affect only the upload page, <strong>not</strong> the inline uploader used on the write pages.', $lg_text_domain); ?></p>
						<table width="100%" cellspacing="2" cellpadding="5" class="editform">
							<tr>
								<th scope="row"><?php _e('Maximum size:', $lg_text_domain) ?> </th>
								<td><input name="fileupload_maxk" type="text" id="fileupload_maxk" value="<?php echo get_option('lg_fileupload_maxk'); ?>" size="4" />
								<?php _e('Kilobytes (KB)') ?></td>
							</tr>
							<tr>
								<th valign="top" scope="row"><?php _e('Allowed file extensions:', $lg_text_domain) ?></th>
									<td><input name="fileupload_allowedtypes" type="text" id="fileupload_allowedtypes" value="<?php echo get_option('lg_fileupload_allowedtypes'); ?>" size="40" />
									<br />
									<?php _e('Recommended: <code>jpg jpeg png gif</code>. Separate by [spaces] (" ").', $lg_text_domain) ?>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Minimum level to upload:', $lg_text_domain) ?></th>
								<td>
									<select name="fileupload_minlevel" id="fileupload_minlevel">
									<?php
										for ($i = 1; $i < 11; $i++) {
											if ($i == get_option('lg_fileupload_minlevel')) $selected = " selected='selected'";
											else $selected = '';
											echo "\n\t<option value='$i' $selected>$i</option>";
										}
									?>
									</select>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e('Enable Microsoft Wizard Publixher Support: ', $lg_text_domain) ?></th>
								<td>
									<input type="checkbox" name="enable_mwp_support" value="TRUE" <?php if(get_option('lg_enable_mwp_support') == "TRUE") echo "checked='checked'"; ?> />
									<span style="font-size:x-small;"><?php _e("Use this only if your OS is Microsoft Windows XP!", $lg_text_domain) ?></span>
								</td>
							</tr>
						</table>
					</div>
				</fieldset>

			<!-- Reset and Uninstall Section -->
			<fieldset class="dbx-box">
				<h3 title="click-down and drag to move this box" class="dbx-handle"><?php _e('Reset &amp; Uninstall', $lg_text_domain) ?></h3>
				<div class="dbx-content">
					<div class="submit" style="text-align:left;">
						<input type="submit" name="lg_reset_options_button" value="<?php _e('Reset options', $lg_text_domain) ?>" title="<?php _e('Reset all options to default values', $lg_text_domain) ?>" />
						<input type="submit" name="lg_delete_options_button" value="<?php _e('Delete options', $lg_text_domain) ?>" title="<?php _e('Delete all Lazyest Gallery\'s options from database', $lg_text_domain) ?>" />
					</div>
				</div>
			</fieldset>

			</div>

			<div class="submit">
				<input type="submit" name="update_options" value="<?php	_e('Update options', $lg_text_domain)	?> " title="<?php _e('Submit and saves changes', $lg_text_domain) ?>" />
			</div>

		</form>
	</div>
</div>
<?php

} else { ?>
<div class="wrap">
	<h2>Lazyest Gallery <?php echo LG_VERSION; ?> Options</h2>
		<div  id="message" class="error fade">
			<p><?php _e("It seams like you don't have the permissions to change Lazyest Gallery's Options", $lg_text_domain); ?></p>
		</div>
		<!-- GD Info data -->
		<fieldset class="options"><legend><?php _e('GD Library Infos', $lg_text_domain); ?></legend>
			<div>
				<?php
					// This code will display GD infos
					lg_admin_describeGDdyn();
				?>
			</div>
		</fieldset>
		<!-- End GD Info data -->
</div>
<?php
}
?>
