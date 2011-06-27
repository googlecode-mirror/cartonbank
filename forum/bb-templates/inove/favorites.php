<?php bb_get_header(); ?>

<h3 class="bbcrumb"><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a> &raquo; Закладки</h3>

<h2 id="currentfavorites">Закладки<?php if ( $topics ) echo ' (' . $favorites_total . ')'; ?></h2>

<p>Закладки позволяют осуществлять Вам <abbr title="Really Simple Syndication">RSS</abbr> экспорт выбранных Вами тем.
Для добавления закладок на темы, просто нажмите на "Добавить закладку на эту тему" на странице темы.</p>

<?php if ( $user_id == bb_get_current_user_info( 'id' ) ) : ?>
<p><?php printf(__('Подписаться на закладки (<a href=\"%s\"><abbr title=\"Really Simple Syndication\">RSS</abbr> экспорт</a>).'), attribute_escape( get_favorites_rss_link( bb_get_current_user_info( 'id' ) ) )) ?></p>
<?php endif; ?>

<?php if ( $topics ) : ?>

<table id="favorites">
<tr>
	<th>Тема</th>
	<th>Сообщения</th>
	<th>Свежесть</th>
	<th>Удалить</th>
</tr>

<?php foreach ( $topics as $topic ) : ?>
<tr<?php topic_class(); ?>>
	<td><?php bb_topic_labels(); ?> <a href="<?php topic_link(); ?>"><?php topic_title(); ?></a></td>
	<td class="num"><?php topic_posts(); ?></td>
	<td class="num"><a href="<?php topic_last_post_link(); ?>"><?php topic_time(); ?></a></td>
	<td class="num">[<?php user_favorites_link('', array('mid'=>'&times;'), $user_id); ?>]</td>
</tr>
<?php endforeach; ?>
</table>

<div class="nav">
<?php favorites_pages(); ?>
</div>

<?php else: if ( $user_id == bb_get_current_user_info( 'id' ) ) : ?>

<p>У Вас сейчас нет ни одной закладки.</p>

<?php else : ?>

<p><?php echo get_user_name( $user_id ); ?> сейчас нет ни одной закладки.</p>

<?php endif; endif; ?>

<?php bb_get_footer(); ?>
