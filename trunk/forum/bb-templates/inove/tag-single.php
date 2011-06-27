<?php bb_get_header(); ?>

<h3 class="bbcrumb"><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a> &raquo; <a href="<?php bb_tag_page_link(); ?>">Метки</a> &raquo; <?php bb_tag_name(); ?></h3>

<?php do_action('tag_above_table', ''); ?>

<?php if ( $topics ) : ?>

<table id="latest">
<tr>
	<th>Тема &#8212; <?php new_topic(); ?></th>
	<th>Сообщения</th>
	<th>Последний отправитель</th>
	<th>Свежесть</th>
</tr>

<?php foreach ( $topics as $topic ) : ?>
<tr<?php topic_class(); ?>>
	<td><?php bb_topic_labels(); ?> <a href="<?php topic_link(); ?>"><?php topic_title(); ?></a></td>
	<td class="num"><?php topic_posts(); ?></td>
	<td class="num"><?php topic_last_poster(); ?></td>
	<td class="num"><a href="<?php topic_last_post_link(); ?>"><?php topic_time(); ?></a></td>
</tr>
<?php endforeach; ?>
</table>

<p><a href="<?php bb_tag_rss_link(); ?>" class="rss-link"><abbr title="Really Simple Syndication">RSS</abbr> экспорт по этой метке.</a></p>

<div class="nav">
<?php tag_pages(); ?>
</div>
<?php endif; ?>

<?php post_form(); ?>

<?php do_action('tag_below_table', ''); ?>

<?php manage_tags_forms(); ?>

<?php bb_get_footer(); ?>
