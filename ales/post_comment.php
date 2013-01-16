<?php


?>

<div id="commentsform">
	<form action="<?= $_SITEURL; ?>/wp-content/plugins/purgatory/add_comment.php" method="post" id="commentform">
	пишите тут, нажмите кнопку:<br />
	<textarea id="comment" name="comment" id="comment" cols="60" rows="3" tabindex="4"></textarea>
	<p>
	<input name="submit" type="submit" value="послать">
	<input type="hidden" name="cartoon_id" value="1000">
	<input type="hidden" name="author_id" value="<? echo ($current_user->id); ?>">
	</p>
	<input type="hidden" id="_wp_unfiltered_html_comment" name="_wp_unfiltered_html_comment" value="5a3ab88268"><p style="display: none;"><input type="hidden" id="akismet_comment_nonce" name="akismet_comment_nonce" value="8bd460432a"></p>
	</form>
</div>