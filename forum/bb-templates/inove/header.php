<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"<?php bb_language_attributes( '1.1' ); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php bb_title() ?></title>
	<?php bb_feed_head(); ?> 
	<link rel="stylesheet" href="<?php bb_stylesheet_uri(); ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo bb_active_theme_uri(); ?>custom.css" type="text/css" />
<?php if ( 'rtl' == bb_get_option( 'text_direction' ) ) : ?>
	<link rel="stylesheet" href="<?php bb_stylesheet_uri( 'rtl' ); ?>" type="text/css" />
<?php endif; ?>
	<script type="text/javascript" src="<?php echo bb_active_theme_uri(); ?>js/util.js"></script>
	<script type="text/javascript" src="<?php echo bb_active_theme_uri(); ?>js/menu.js"></script>

<?php bb_head(); ?>

</head>

<body id="<?php bb_location(); ?>">
	<div id="wrap">
		<div id="container">
		
		<div id="header">
			<div id="caption">
				<h1 id="title"><a href="<?php bb_option('uri'); ?>"><?php bb_option('name'); ?></a></h1>
				<div id="tagline"><?php bb_option('description'); ?></div>
			</div>
			
			
			
			<!-- navigation START -->
			<div id="navigation">
				<ul id="menus">
				<li class="<?php if(is_front()) { ?>current_page_item <?php } ?>page_item"><a class="home" title="Главная" href="<?php bb_option('uri'); ?>">Главная</a></li>
				<li class="<?php if(is_bb_search()) { ?>current_page_item <?php } ?>page_item"><a href="<?php bb_option('uri'); ?>search.php" title="Поиск">Поиск</a></li>
				<li class="page_item"><a href="#">Подменю 1</a>
				<ul>
					<li class="page_item"><a href="#" title="Test">Подстраница 1</a></li>
					<li class="page_item"><a href="#" title="Test 2">Подстраница 2</a></li>
				</ul>
				</li>
				<li class="page_item"><a href="#">Подменю 2</a>
				<ul>
					<li class="page_item"><a href="#" title="Test">Тест 1</a></li>
					<li class="page_item"><a href="#" title="Test 2">Тест 2</a></li>
				</ul>
				</li>
				<li><a class="lastmenu" href="javascript:void(0);"></a></li>
				</ul>
				
				<!-- searchbox START -->
						<div id="searchbox">
							<form action="<?php bb_option('uri'); ?>search.php" method="get">
									<div class="content">
										<input type="text" class="textfield" maxlength="100" name="q" size="24" value="<?php echo attribute_escape( $q ); ?>" />
										<a class="switcher" >Переключатель</a>
									</div>
								</form>
						</div>
						<!-- searchbox END -->
				
				

				<div class="fixed"></div>
			</div>
			<!-- navigation END -->

			<div class="fixed"></div>
		</div>

		<!-- content START -->
		<div id="content">

			<!-- main START -->
			<div id="main">
				
