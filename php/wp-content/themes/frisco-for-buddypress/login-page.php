<?php
/*
 * Template Name: login template
 *
 * A custom page template without sidebar.
 *
 * @package BuddyPress
 * @subpackage BP_Default
 * @since BuddyPress (1.5)
 */

get_header(); ?>


    		<?php do_action( 'bp_before_member_home_content' ); ?>
    <div id="login-front">
		
        
       
            
                    <?php get_sidebar( 'buddypress' ); ?>
       
       
        
       
        

	
       </div><!-- #item-body -->
       <?php do_action( 'bp_after_blog_page' ); ?>


<?php get_footer(); ?>