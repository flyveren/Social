<?php
/*
 * Template Name: flyver temp
 *
 * A custom page template without sidebar.
 *
 * @package BuddyPress
 * @subpackage BP_Default
 * @since BuddyPress (1.5)
 */

get_header(); ?>

	<div id="content">
    		<?php do_action( 'bp_before_member_home_content' ); ?>
    <div id="item-body">
		
        
        <div id="nysidebar">
        <div id="item-header" role="complementary">

				<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>
			   <div id="tjekpoint"><h1 class="karmapoints"><?php echo dpa_get_user_points( bp_displayed_user_id() )?></h1><p class="points">Points</p></div>
	    </div><!-- #item-header -->

       
       <div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>

						<?php bp_get_displayed_user_nav(); ?>

						<?php do_action( 'bp_member_options_nav' ); ?>

					</ul>
				</div>
			</div><!-- #item-nav -->
            
                    <?php get_sidebar( 'buddypress' ); ?>
       
        </div> <!-- slut #nysidebar -->
        
       
        
        <div id="main-column">

		<?php do_action( 'bp_before_blog_page' ); ?>

		<div class="item-body" id="blog-page" role="main">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

				<h2 class="pagetitle"><?php the_title(); ?></h2>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<div class="entry">

						<?php the_content( __( '<p class="serif">Read the rest of this page &rarr;</p>', 'buddypress' ) ); ?>

						<?php wp_link_pages( array( 'before' => '<div class="page-link"><p>' . __( 'Pages: ', 'buddypress' ), 'after' => '</p></div>', 'next_or_number' => 'number' ) ); ?>
						<?php edit_post_link( __( 'Edit this page.', 'buddypress' ), '<p class="edit-link">', '</p>'); ?>

					</div>

				</div>

			<?php comments_template(); ?>

			<?php endwhile; endif; ?>

		</div><!-- .page -->

		<?php do_action( 'bp_after_blog_page' ); ?>

		</div><!-- #main-column -->
       </div><!-- #item-body -->
	</div><!-- #content -->

<?php get_footer(); ?>