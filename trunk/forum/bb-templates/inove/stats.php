<?php bb_get_header(); ?>

<h3 class="bbcrumb"><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a> &raquo; Статистика</h3>

<dl>
	<dt>Зарегистрированные пользователи</dt>
	<dd><strong><?php total_users(); ?></strong></dd>
	<dt>Сообщения</dt>
	<dd><strong><?php total_posts(); ?></strong></dd>
</dl>

<?php if ($popular) : ?>
<h3>Самые популярные темы</h3>
<ol>
<?php foreach ($popular as $topic) : ?>
<li><?php bb_topic_labels(); ?> <a href="<?php topic_link(); ?>"><?php topic_title(); ?></a> &#8212; <?php topic_posts(); ?> posts</li>
<?php endforeach; ?>

<?php endif; ?>
</ol>
<?php bb_get_footer(); ?>
