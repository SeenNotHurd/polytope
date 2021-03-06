<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

	<head>
		<meta charset="utf-8">

		<?php // Google Chrome Frame for IE ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title><?php wp_title(''); ?></title>

		<?php // mobile meta (hooray!) ?>
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

		<?php // icons & favicons (for more: http://www.jonathantneal.com/blog/understand-the-favicon/) ?>
		<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-icon-touch.png">
		<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
		<!--[if IE]>
			<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
		<![endif]-->
		<?php // or, set /favicon.ico for IE10 win ?>
		<meta name="msapplication-TileColor" content="#f01d4f">
		<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

		<?php // wordpress head functions ?>
		<?php wp_head(); ?>
		<?php // end of wordpress head ?>

		<?php // drop Google Analytics Here ?>
		<?php // end analytics ?>

	</head>

	<?php 
		$extra_classes = '';
		$logo_colour = '';
		if(is_page() && has_post_thumbnail() ) { 
			$full_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');
			$bg_image = 'background-image: url(' . $full_image_url[0] . ');';
			$extra_classes .= ' bg-image';
		}
		
		if(get_post_meta( get_the_ID(), '_polytope_page_colouring', true ) == 'light') {
		 	$extra_classes .= ' colouring-light';
		 	$logo_colour = ' light';
		}
		if(get_post_meta( get_the_ID(), '_polytope_footer_positioning', true ) == 'bottom') {
		 	$extra_classes .= ' footer-bottom fixed';
		} else if(get_post_meta( get_the_ID(), '_polytope_footer_positioning', true ) == 'slimline') {
		 	$extra_classes .= ' footer-bottom slimline';
		}
	?>

	<body <?php body_class($extra_classes); ?> style="<?php echo $bg_image; ?>">

		<div id="container">

			<header class="header" role="banner">

				<div id="inner-header" class="wrap clearfix">

					<?php if($logo_colour == ' light') { $logoColour = 'inverse'; } else { $logoColour = 'regular'; } ?>
					<a id="site-branding" href="<?php echo home_url(); ?>" rel="nofollow"><img id="logo" src="<?php echo get_template_directory_uri(). '/library/images/polytope-' . $logoColour . '.svg'; ?>"><h1 id="blog-name"><?php bloginfo('name'); ?></h1></a>

					<?php // if you'd like to use the site description you can un-comment it below ?>
					<?php // bloginfo('description'); ?>

					<a href="#!" id="menu-toggle">
						<span></span>
						<span></span>
						<span></span>
					</a>

					<?php 
						/**
						 * Check if WooCommerce is active
						 **/
						//if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { 
						//global $woocommerce; ?>

						<a id="header-cart" href="<?php //echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>">
							<?php //echo $woocommerce->cart->cart_contents_count; ?>2 &#x25bd; € 70<?php //echo $woocommerce->cart->get_cart_total(); ?>
						</a>
					<?php //} //end if ?>

					<nav role="navigation" id="main-menu">
						<?php bones_main_nav(); ?>
					</nav>

				</div>

			</header>
