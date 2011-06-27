<?php bb_get_header(); ?>

<h3 class="bbcrumb"><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a> &raquo; Профиль</h3>

<h2 id="userlogin"><?php echo get_user_name( $user->ID ); ?></h2>

<?php if ( $updated ) : ?>
<div class="notice">
<p>Профиль обновлен. <a href="<?php profile_tab_link( $user_id, 'edit' ); ?>">Редактировать снова &raquo;</a></p>
</div>
<?php elseif ( $user_id == bb_get_current_user_info( 'id' ) ) : ?>
<p>
Это то, каким видит Ваш профиль пользователь, вошедший в систему.

<?php if (bb_current_user_can( 'edit_user', $user->ID )) : ?>
<?php printf('Вы можете <a href=\"%1$s\">редактировать эту информацию</a>.', attribute_escape( get_profile_tab_link( $user_id, 'edit' ) ) ); ?>
<?php endif; ?>
</p>

<?php if (bb_current_user_can( 'edit_favorites_of', $user->ID )) : ?>
<p><?php printf('Вы также можете <a href="%1$s">управлять вашими закладками</a> и подписаться на ваши закладки по <a href="%2$s"><abbr title="Really Simple Syndication">RSS</abbr></a>.', attribute_escape( get_favorites_link() ), attribute_escape( get_favorites_rss_link() )); ?></p>
<?php endif; ?>
<hr />
<?php endif; ?>


<h2 id="useractivity">Активность</h2>

<div id="user-replies" class="user-recent"><h4>Последние сообщения</h4>
<?php if ( $posts ) : ?>
<ol>
<?php foreach ($posts as $bb_post) : $topic = get_topic( $bb_post->topic_id ) ?>
<li<?php alt_class('replies'); ?>>
	<a href="<?php topic_link(); ?>"><?php topic_title(); ?></a>
	<?php if ( $user->ID == bb_get_current_user_info( 'id' ) ) printf('Ваше последнее сообщение: %s назад.', bb_get_post_time()); else printf('Последнее сообщение пользователя: %s назад.', bb_get_post_time()); ?>

	<span class="freshness"><?php
		if ( bb_get_post_time( 'timestamp' ) < get_topic_time( 'timestamp' ) )
			printf('Самое последнее сообщение: %s назад', get_topic_time());
		else
			_e('С тех пор сообщений нет.');
	?></span>
</li>
<?php endforeach; ?>
</ol>
<?php else : if ( $page ) : ?>
<p>Новых сообщений нет.</p>
<?php else : ?>
<p>Пока сообщений нет.</p>
<?php endif; endif; ?>
</div>

<div id="user-threads" class="user-recent">
<h4>Созданные темы</h4>
<?php if ( $topics ) : ?>
<ol>
<?php foreach ($topics as $topic) : ?>
<li<?php alt_class('topics'); ?>>
	<a href="<?php topic_link(); ?>"><?php topic_title(); ?></a>
	<?php printf('Пока сообщений нет.', get_topic_start_time()); ?>

	<span class="freshness"><?php
		if ( get_topic_start_time( 'timestamp' ) < get_topic_time( 'timestamp' ) )
			printf('Самое последнее сообщение: %s назад.', get_topic_time());
		else
			_e('Новых сообщений нет.');
	?></span>
</li>
<?php endforeach; ?>
</ol>
<?php else : if ( $page ) : ?>
<p>Новых тем нет.</p>
<?php else : ?>
<p>Тем пока нет.</p>
<?php endif; endif;?>
</div><br style="clear: both;" />

<?php profile_pages(); ?>

<?php bb_get_footer(); ?>
