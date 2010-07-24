<?php get_header(); ?>

<div id="content">
<?php get_sidebar(); ?>

<div id="contentmiddle">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div id="contentdate">
	<h3><?php the_time('M'); ?></h3>
	<h4><?php the_time('j'); ?></h4>
	</div>
	
<div id="contenttitle">
	<h1>Not Found, Error 404</h1><br />
	<p>Запрошенная страница не существует.</p>
	</div>
	
<div id="contentright">
	
</div>
	
</div>

<!-- The main column ends  -->

<?php get_footer(); ?>