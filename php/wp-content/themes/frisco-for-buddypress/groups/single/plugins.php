<?php get_header( 'buddypress' ); ?>

	<div id="content">

		<div id="item-body">
			<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

			<?php do_action( 'bp_before_group_plugin_template' ); ?>

	<div id="nysidebar">
			<div id="item-header" role="complementary">
				<?php locate_template( array( 'groups/single/group-header.php' ), true ); ?>
			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>
						<?php bp_get_options_nav() ?>

						<?php do_action( 'bp_group_plugin_options_nav' ); ?>
					</ul>
				</div>

    <div id="first" class="widget-area">
        <ul class="xoxo">
            <?php dynamic_sidebar( 'first-footer-widget-area' ); ?>
        </ul>
    </div><!-- #first .widget-area -->
    
					<div id="sidie"><?php get_sidebar( 'buddypress' ); ?></div>
			</div><!-- #item-nav -->
		</div>	<!-- #nysidebar -->
        
	<div id="main-column">
			<div id="item-body">

				<?php do_action( 'bp_before_group_body' ); ?>

				<?php do_action( 'bp_template_content' ); ?>

				<?php do_action( 'bp_after_group_body' ); ?>
			</div><!-- #item-body -->

			<?php do_action( 'bp_after_group_plugin_template' ); ?>

			<?php endwhile; endif; ?>

	</div><!-- #main-column -->

</div><!-- #item-body -->

	</div><!-- #content -->

<?php get_footer( 'buddypress' ); ?>


