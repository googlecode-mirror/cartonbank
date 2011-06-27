	</div>
	<!-- main END -->
	
	<div id="sidebar">
		<div id="northsidebar" class="sidebar">
		
		<!-- searchbox START -->
		<div class="widget">
			<?php login_form(); ?>
			<div class="fixed"></div>
		</div>
		<!-- searchbox END -->
		
		<?php if ( is_bb_profile() ): ?>
		<div class="widget">
			<div id="useravatar">
				<?php echo bb_get_avatar( $user->ID ); ?>
	        	<p><strong><?php echo get_user_name( $user->ID ); ?></strong><br />
	            	<small><?php bb_profile_data(); ?><br />

	                </small>
	            </p>
	        </div>
		</div>
		
	        <div class="widget">
			<h3>Меню профиля</h3>
	        <?php if ( is_bb_profile() ) profile_menu(); ?>
	        </div>
		
		<?php endif; ?>
		
		
		<?php if(is_topic()): ?>
			<div class="widget">
				<h3>О теме</h3>
	        	<ul>
	            	<li><?php printf('Отправлено %1$s назад', get_topic_start_time(), get_topic_author()) ?></li>
	            	<li><?php printf('%2$s', get_topic_start_time(), get_topic_author()) ?></li>
	            	<li><?php printf('<a href=\"%1$s\">Последнее сообщение</a> от %2$s', attribute_escape( get_topic_last_post_link() ), get_topic_last_poster()) ?></li>
					<?php if ( bb_is_user_logged_in() ) : $class = 0 === is_user_favorite( bb_get_current_user_info( 'id' ) ) ? ' class="is-not-favorite"' : ''; ?>
	            		<li<?php echo $class;?> id="favorite-toggle"><?php user_favorites_link(); ?></li>
	            	<?php endif; do_action('topicmeta'); ?>
	            	<li><a href="<?php topic_rss_link(); ?>" class="rss-link">RSS экспорт этой темы</a></li>
				</ul>
			</div>
			
			<?php if ( bb_current_user_can( 'delete_topic', get_topic_id() ) || bb_current_user_can( 'close_topic', get_topic_id() ) || bb_current_user_can( 'stick_topic', get_topic_id() ) || bb_current_user_can( 'move_topic', get_topic_id() ) ) : ?>
			<div class="widget">
	       		<h3>Администрирование</h3>
	       		<ul>     
	        		<li><?php topic_delete_link(); ?></li>
					<li><?php topic_close_link(); ?></li>
					<li><?php topic_sticky_link(); ?></li>
	        		<li><?php topic_move_dropdown(); ?></li>
	        	</ul>
			</div>
	    	<?php endif; ?>
			
			<div class="widget">
	        <h3>Метки</h3>
	        <?php topic_tags(); ?>
			</div>
		<?php else: ?>
			<?php if ( bb_is_user_logged_in() ) : ?>
				<div id="viewdiv" class="widget">
					<h2>Виды</h2>
					<ul id="views">
					<?php foreach ( bb_get_views() as $the_view => $title ) : ?>
						<li class="view"><a href="<?php view_link( $the_view ); ?>"><?php view_name( $the_view ); ?></a></li>
					<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; // bb_is_user_logged_in() ?>
			
			<div id="tag_cloud" class="widget widget_tag_cloud">
				<h3>Популярные метки</h3>
				<p class="frontpageheatmap"><?php bb_tag_heat_map(); ?></p>
			</div>
			
			
		<?php endif; ?>
		
		</div>
	</div>
	
	<?php //get_sidebar(); ?>
	<div class="fixed"></div>
</div>
<!-- content END -->

	<div id="footer">
		<a id="gotop" href="#" onclick="MGJS.goTop();return false;">Наверх</a>
	</div>

	<?php do_action('bb_foot', ''); ?>

</div>
<!-- container END -->
</div>
<!-- wrap END -->

</body>
</html>
