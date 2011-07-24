<?php get_header(); ?>

<div id="content">
<div id="contentmiddle">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
<div id="contenttitle">
	<h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>

	</div>
	<?php the_content(__('Читать дальше'));?>

	<?php endwhile; else: ?>

	<p><?php _e('Извините, здесь пока ничего нет.'); ?></p><?php endif; ?><br />
	
<div id="contentright">
</div>

<?php get_sidebar(); ?>

</div>

<!-- The main column ends  -->

<?php get_footer(); ?>