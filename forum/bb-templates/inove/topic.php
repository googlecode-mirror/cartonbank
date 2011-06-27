<?php bb_get_header(); ?>

<h3 class="bbcrumb"><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a><?php bb_forum_bread_crumb(); ?></h3>
<div class="infobox">

	<div id="topic-info">
		<span id="topic_labels"><?php bb_topic_labels(); ?></span>
		<h2<?php topic_class( 'topictitle' ); ?>><?php topic_title(); ?></h2>
		<span id="topic_posts">(<?php topic_posts_link(); ?>)</span>
	</div>

<div style="clear:both;"></div>
</div>
<?php do_action('under_title', ''); ?>
<?php if ($posts) : ?>

<div id="ajax-response"></div>
<ol id="thread" start="<?php echo $list_start; ?>">

<?php foreach ($posts as $bb_post) : $del_class = post_del_class(); ?>
	<li id="post-<?php post_id(); ?>"<?php alt_class('post', $del_class); ?>>
<?php bb_post_template(); ?>
	</li>
<?php endforeach; ?>

</ol>
<div class="clearit"><br style=" clear: both;" /></div>
<p><a href="<?php topic_rss_link(); ?>" class="rss-link">RSS экспорт этой темы</a></p>
<div class="nav">
<?php topic_pages(); ?>
</div>
<?php endif; ?>
<?php if ( topic_is_open( $bb_post->topic_id ) ) : ?>
<?php post_form(); ?>
<?php else : ?>
<h2>Тема закрыта.</h2>
<p>Эта тема закрыта для новых сообщений.</p>
<?php endif; ?>

<?php bb_get_footer(); ?>
