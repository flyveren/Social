<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ) ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width" />
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

		<?php wp_head(); // @see bp_head(); ?>
	    <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css/fontello.css">
    	<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css/animation.css">
        <!--[if IE 7]>
        	<link rel="stylesheet" href="css/fontello-ie7.css"><![endif]-->
      
           	</head>

	<body <?php body_class() ?> id="bp-default">

		<?php do_action( 'bp_before_header' ); ?>

		<div id="header">
			
					<h1 id="logo" role="banner"><a href="<?php echo home_url(); ?>" title="<?php _ex( 'Home', 'Home page banner link title', 'buddypress' ); ?>"><?php bp_site_name(); ?></a></h1>

		<div class="nav-wrap">
			<div id="navigation" role="navigation">
				
				<?php if ( bp_is_active( 'messages' ) && is_user_logged_in() ) : ?>
					<?php frisco_bp_message_get_notices(); /* Site wide notices to all users */ ?>
				<?php endif; ?>
				
				<?php wp_nav_menu( array( 'container' => false, 'menu_id' => 'nav', 'theme_location' => 'primary', 'fallback_cb' => 'bp_dtheme_main_nav' ) ); ?>
				
			</div>
		</div>

			<?php do_action( 'bp_header' ) ?>

		</div><!-- #header -->

		<?php do_action( 'bp_after_header' ) ?>
		<?php do_action( 'bp_before_container' ) ?>

		<div id="container">
