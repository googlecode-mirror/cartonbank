<?php if ( $public_tags ) : ?>
<ul id="yourtaglist">
<?php foreach ( $public_tags as $tag ) : ?>
	<li id="tag-<?php echo $tag->tag_id; ?>_<?php echo $tag->user_id; ?>"><a href="<?php bb_tag_link(); ?>" rel="tag"><?php bb_tag_name(); ?></a> <?php bb_tag_remove_link(); ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if ( !$tags ) : ?>
<p><?php printf('Пока нет <a href=\"%s\">меток</a>.', bb_get_tag_page_link() ); ?></p>
<?php endif; ?>
<?php tag_form(); ?>