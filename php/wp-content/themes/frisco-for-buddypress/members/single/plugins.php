<?php

/**
 * BuddyPress - Users Plugins
 *
 * This is a fallback file that external plugins can use if the template they
 * need is not installed in the current theme. Use the actions in this template
 * to output everything your plugin needs.
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<?php get_header( 'buddypress' ); ?>

	<div id="content">
			<?php do_action( 'bp_before_member_home_content' ); ?>
			
			
<div id="item-body">

<div id="nysidebar">
        <div id="item-header" role="complementary">
			<div id="item-nav">
            <div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>
						<?php bp_get_displayed_user_nav(); ?>
						<?php do_action( 'bp_member_options_nav' ); ?> 
					</ul>
				</div>
			</div><!-- #item-nav -->
        </div><!-- #item-header --> 
		<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>
			
		<div class="ass_profile_data">
			<span class="profil_job"><?php bp_profile_field_data( 'field=Jobfunktion' );?></span>
			-
        	<span class="profil_job"><?php bp_profile_field_data( 'field=Butik' );?></span>
        	<span class="profil_job"><?php bp_profile_field_data( 'field=region' );?></span>
		</div>
		
		<div id="item-nav2">    
            <div class="item-list-tabs no-ajax" id="object-nav" role="navigation">	                   
               <ul>
					<?php bp_get_displayed_user_nav(); ?>
					<?php do_action( 'bp_member_options_nav' ); ?>
				</ul>
			</div>
		</div><!-- #item-nav2 -->
        <div class="profile_stats">   
			<div id="tjekpoint"><h1 class="karmapoints"><?php echo dpa_get_user_points( bp_displayed_user_id() )?></h1><p class="points">Points</p></div>
	        <div id="badge"> <?php echo get_the_post_thumbnail( dpa_get_user_last_unlocked( bp_displayed_user_id()), 'thumbnail'); ?></div>
        	<div id="new_ach"></div>	
        </div>	
    <div>
    	<?php dynamic_sidebar( 'first-footer-widget-area' ); ?>
    </div><!-- #first .widget-area -->

</div> <!-- slut #nysidebar -->
			
	<div id="main-column">

			<div id="item-body" role="main">

				<?php do_action( 'bp_before_member_body' ); ?>

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>

						<?php bp_get_options_nav(); ?>

						<?php do_action( 'bp_member_plugin_options_nav' ); ?>

					</ul>
				</div><!-- .item-list-tabs -->

				<h3><?php do_action( 'bp_template_title' ); ?></h3>

				<?php do_action( 'bp_template_content' ); ?>

				<?php do_action( 'bp_after_member_body' ); ?>

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_member_plugin_template' ); ?>

	</div><!-- #main-column -->
	
</div><!-- #item-body -->

	</div> <!-- #content -->

<?php get_footer( 'buddypress' ) ?>
