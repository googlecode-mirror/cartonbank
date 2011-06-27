<?php bb_get_header(); ?>

<h3 class="bbcrumb"><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a> &raquo; Метки</h3>

<p>Эта коллекция меток, которые сейчас популярны на форумах.</p>

<div id="hottags">
<?php bb_tag_heat_map( 9, 38, 'pt', 80 ); ?>
</div>

<?php bb_get_footer(); ?>
