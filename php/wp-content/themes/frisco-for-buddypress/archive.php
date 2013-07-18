<?php get_header(); ?>

 <?php 	
							global $bp;
							$brugernavn = $bp->loggedin_user->fullname;
							$brugerstatus = $bp->loggedin_user->userdata-> user_status;
							$brugerID = $bp->loggedin_user->userdata-> ID;
					
?>

	<div id="content">
    <div id="item-body">
    
 
    
    <div id="nysidebar">
        <div id="item-header" role="complementary">
			<div id="item-nav">
            <ul id="sidebarlinks2">
					<li><a class="icon-picture" href="<? echo bp_loggedin_user_domain().'activity/'; ?>">Aktivitet</a></li>
                    <li><a class="icon-star" href="<?php echo bp_loggedin_user_domain() ?>"><?php _e( 'Profil' , 'buddyboss' ); ?></a></li>
                    <li><a class="icon-heart" href="<? echo bp_loggedin_user_domain().'events/'; ?>">Events</a></li>
             </ul>
                    <div id="profilbilled"><a href="<?php echo bp_loggedin_user_domain() ?>"><?php bp_loggedin_user_avatar( 'type=full' ) ?></a></div>
					<div class="item-meta"><span class="activity"><?php echo bp_member_last_active($brugerID); ?></span></div>

                   
                    	<p><?php echo $brugernavn;?></p>
							<p><?php echo $brugerstatus;?></p>
                            <p><?php echo $brugerID;?></p>
					
					
                   <?php bp_is_directory()?>
                    
                  <ul id="sidebarlinks">
					
					<li><a href="<? echo bp_loggedin_user_domain().'messages/'; ?>">Beskeder</a></li>
                    <li><a href="<? echo bp_loggedin_user_domain().'friends/'; ?>">Venner<?php printf( __( '<span>%s</span>', 'buddypress' ), bp_get_total_friend_count( bp_loggedin_user_id() ) ); ?></a></li>
                    <li><a href="<? echo bp_loggedin_user_domain().'groups/'; ?>">Grupper</a></li>
                    <li><a href="<? echo bp_loggedin_user_domain().'media/'; ?>">Media</a></li>
                    
                </ul>
                 
                 
                   
                     
			</div><!-- #item-nav -->
            
			   <div id="tjekpoint"><h1 class="karmapoints"><?php echo dpa_get_user_points( bp_displayed_user_id() )?></h1><p class="points">Points</p></div>
	           <div id="badge"> <?php echo get_the_post_thumbnail( dpa_get_user_last_unlocked( bp_displayed_user_id()), 'thumbnail'); ?></div>
        	   <div id="new_ach"></div>	
        </div><!-- #item-header -->
        	
    <div>
            <?php dynamic_sidebar( 'first-footer-widget-area' ); ?>
    </div><!-- #first .widget-area -->

        
      
       
</div> <!-- slut #nysidebar -->











		
        
<div id="main-column">

		<?php do_action( 'bp_before_archive' ); ?>

		<div class="page" id="blog-archives" role="main">

			<h3 class="pagetitle"><?php printf( __( 'You are browsing the archive for %1$s.', 'buddypress' ), wp_title( false, false ) ); ?></h3>

			<?php if ( have_posts() ) : ?>

				<?php bp_dtheme_content_nav( 'nav-above' ); ?>

				<?php while (have_posts()) : the_post(); ?>

					<?php do_action( 'bp_before_blog_post' ); ?>

					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			
            <img src="<?php the_field('icon');?>" class="ach_img"/>
            <h5><?php the_field('achievement_navn');?></h5>
            <p><?php the_field('tid');?> | <?php the_field('antal_points');?></p>

					</div>

					<?php do_action( 'bp_after_blog_post' ); ?>

				<?php endwhile; ?>

				<?php bp_dtheme_content_nav( 'nav-below' ); ?>

			<?php else : ?>

				<h2 class="center"><?php _e( 'Not Found', 'buddypress' ); ?></h2>
				<?php get_search_form(); ?>

			<?php endif; ?>

		</div>

		<?php do_action( 'bp_after_archive' ); ?>


	</div><!-- #main-column -->
    </div><!-- #item-body -->
		
	</div><!-- #content -->
    


	<?php get_sidebar(); ?>

<?php get_footer(); ?>
