<?php

	// Don't remove this lines:
	require_once('../../../wp-blog-header.php');
	global $gallery_address, $lg_text_domain;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>

		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
		<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />

		<title><?php echo $_GET['image'] ?></title>

		<link rel="stylesheet" href="<?php get_settings('home'); ?>/wp-content/plugins/lazyest-gallery/lazyest-style.css" type="text/css" media="screen" />
		<style type="text/css">
			body {
				text-align:center;
				margin:0;
				padding:0;
			}
			img {
				border:none;
			}
		</style>
	</head>

	<body>
		<a href="javascript:self.close()" title="<?php _e('Click to close', $lg_text_domain); ?>">
			<img src="<?php echo str_replace(" ", "%20", $gallery_address.$_GET['folder'].$_GET['image']); ?>" alt="<?php echo $_GET['image']; ?>" />
		</a>
	</body>
</html>

<?php

?>