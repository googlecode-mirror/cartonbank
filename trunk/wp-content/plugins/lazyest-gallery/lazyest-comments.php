<?php
/*
 * This file handles all gallery comments
 * - TODO -
 * Many things
 */

require_once('lazyest-gallery.php');

function get_image_comments() {
	global $wpdb;

	$gallery_page = $wpdb->get_results("SELECT `ID`
													FROM $wpdb->posts
													WHERE `post_content`= \"[[gallery]]\"");
	foreach($gallery_page as $np){
		$gallery_ID = $np->ID;
	}
	$img_comments = $wpdb->get_results("SELECT *
													FROM $wpdb->comments
													WHERE `comment_post_ID`= \"$gallery_ID\"");


	foreach($img_comments as $ic){
		$image_comment = $ic->comment_content;
// 		echo "Comment: ". $image_comment."<br />";
	}


// 	debug($img_comments);
}

function lg_get_image_id($folder, $img) {

	$images_data = get_image_caption($folder);

	for ($i = 0; $i < count($images_data); $i++) {
		if ($images_data[$i][image] == $img){
			$id = $images_data[$i][id];
		}
	}

	return $id;
}
?>