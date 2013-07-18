<?php

/**
 * BuddyPress - Users Home
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

		<?php do_action( 'bp_before_member_body' );

			if ( bp_is_user_activity() || !bp_current_component() ) :
				locate_template( array( 'members/single/activity.php'  ), true );

		    elseif ( bp_is_user_blogs() ) :
				locate_template( array( 'members/single/blogs.php'     ), true );

			elseif ( bp_is_user_friends() ) :
				locate_template( array( 'members/single/friends.php'   ), true );

			elseif ( bp_is_user_groups() ) :
				locate_template( array( 'members/single/groups.php'    ), true );

			elseif ( bp_is_user_messages() ) :
				locate_template( array( 'members/single/messages.php'  ), true );

			elseif ( bp_is_user_profile() ) :
				locate_template( array( 'members/single/profile.php'   ), true );

			elseif ( bp_is_user_forums() ) :
				locate_template( array( 'members/single/forums.php'    ), true );

			elseif ( bp_is_user_settings() ) :
				locate_template( array( 'members/single/settings.php'  ), true );

			// If nothing sticks, load a generic template
			else :
				locate_template( array( 'members/single/plugins.php'   ), true );

			endif;

		do_action( 'bp_after_member_body' ); ?>

		<?php do_action( 'bp_after_member_home_content' ); ?>

	</div><!-- #main-column -->
</div><!-- #item-body -->
        
</div><!-- #content -->

<?php get_footer( 'buddypress' ); ?>
