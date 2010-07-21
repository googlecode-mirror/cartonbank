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
	<!-- <p><?php the_time('F j, Y'); ?> | <?php comments_popup_link('Комментировать', '1 Отклик', '% Отклики'); ?></p> -->
	</div>
	<?php the_content(__('Читать дальше'));?>
				<!-- ales -->
					<?
					//global $id;
					//$product_id = get_product_id($id);
					?>					

					<!-- <form name='<?php echo $product_id ?>' method='post' action='http://localhost/cb/?page_id=3category=' onsubmit='submitform(this);return false;'><input name='prodid' value='<?php echo $product_id ?>' type='hidden'><div class='producttext'><input name='item' value='<?php echo $product_id ?>' type='hidden'><input name='Buy' value='Add To Cart' type='submit'></div></form> -->
				<!-- //ales -->

	<?php endwhile; else: ?>

	<p><?php _e('Извините, здесь пока ничего нет.'); ?></p><?php endif; ?><br />

	<!-- <h1>Comments</h1>
	<?php comments_template(); // Get wp-comments.php template ?>
	</div> -->
	
<div id="contentright">
<h2>Tags</h2>
<?php jkeywords_post_tags(); ?>

	</div>
	
</div>

<!-- The main column ends  -->

<?php get_footer(); ?>