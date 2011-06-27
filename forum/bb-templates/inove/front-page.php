<?php bb_get_header(); ?>

<?php if ( $forums ) : ?>

<div id="discussions">
<?php if ( $topics || $super_stickies ) : ?>

<h2>Последние дискуссии</h2>

<table id="latest">
<tr>
	<th>Тема &#8212; <?php new_topic(); ?></th>
	<th>Сообщения</th>
	<th>Последний отправитель</th>
	<th>Свежесть</th>
</tr>

<?php if ( $super_stickies ) : foreach ( $super_stickies as $topic ) : ?>
<tr<?php topic_class(); ?>>
	<td><?php bb_topic_labels(); ?> <big><a href="<?php topic_link(); ?>"><?php topic_title(); ?></a></big></td>
	<td class="num"><?php topic_posts(); ?></td>
	<td class="num"><?php topic_last_poster(); ?></td>
	<td class="num"><a href="<?php topic_last_post_link(); ?>"><?php topic_time(); ?></a></td>
</tr>
<?php endforeach; endif; // $super_stickies ?>

<?php if ( $topics ) : foreach ( $topics as $topic ) : ?>
<tr<?php topic_class(); ?>>
	<td><?php bb_topic_labels(); ?> <a href="<?php topic_link(); ?>"><?php topic_title(); ?></a></td>
	<td class="num"><?php topic_posts(); ?></td>
	<td class="num"><?php topic_last_poster(); ?></td>
	<td class="num"><a href="<?php topic_last_post_link(); ?>"><?php topic_time(); ?></a></td>
</tr>
<?php endforeach; endif; // $topics ?>
</table>
<?php endif; // $topics or $super_stickies ?>

<?php if ( bb_forums() ) : ?>
<h2>Форумы</h2>
<table id="forumlist">

<tr>
	<th>Форум</th>
	<th>Темы</th>
	<th>Сообщения</th>
</tr>
<?php while ( bb_forum() ) : ?>
<tr<?php bb_forum_class(); ?>>
	<td><?php bb_forum_pad( '<div class="nest">' ); ?><a href="<?php forum_link(); ?>"><?php forum_name(); ?></a><small><?php forum_description(); ?></small><?php bb_forum_pad( '</div>' ); ?></td>
	<td class="num"><?php forum_topics(); ?></td>
	<td class="num"><?php forum_posts(); ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php endif; // bb_forums() ?>

</div>

<?php else : // $forums ?>

<h3 class="bbcrumb"><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a></h3>

<?php post_form(); endif; // $forums ?>

<?php bb_get_footer(); ?>
