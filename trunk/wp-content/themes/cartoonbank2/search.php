<?php get_header(); ?>

<div id="content">
<?php get_sidebar(); ?>

<div id="contentmiddle">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<!-- <div id="contentdate">
	<h3><?php the_time('M'); ?></h3>
	<h4><?php the_time('j'); ?></h4>
	</div> -->
	
<div id="contenttitle">
	<h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
	<p><?php the_time('F j, Y'); ?> | <?php comments_popup_link('Комментировать', '1 Отклик', '% Откликов'); ?></p>
	</div>
	<?php the_content(__('Читать дальше'));?>
	<!--
	<?php trackback_rdf(); ?>
	-->

	<?php endwhile; else: ?>
	<p><?php _e('Извините, ничего не найдено.'); ?></p><?php endif; ?>
	<?php posts_nav_link(' &#8212; ', __('&laquo; назад'), __('ещё &raquo;')); ?>
	</div>
	
<div id="contentright">

	</div>
	
</div>

<!-- The main column ends  -->

<?php get_footer(); ?>