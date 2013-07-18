<?php do_action( 'bp_before_member_messages_loop' ); ?>

<?php if ( bp_has_message_threads( bp_ajax_querystring( 'messages' ) ) ) : ?>

	<div class="pagination no-ajax" id="user-pag">
				
		<div class="pagination-links" id="messages-dir-pag">
			<?php bp_messages_pagination(); ?>
		</div>

	</div><!-- .pagination -->

	<?php do_action( 'bp_after_member_messages_pagination' ); ?>

	<?php do_action( 'bp_before_member_messages_threads'   ); ?>

	<div id="message-threads" class="message-notices">
		<img class="ribbon" src="/social/wp-content/themes/frisco-for-buddypress/images/ribbon_fold_blue.png" />
		
		<?php while ( bp_message_threads() ) : bp_message_thread(); ?>
			<!-- row -->
			<div id="m-<?php bp_message_thread_id(); ?>" class="row <?php bp_message_css_class(); ?><?php if ( bp_message_thread_has_unread() ) : ?> unread<?php else: ?> read<?php endif; ?>">
				
				<div class="thread-count">
					<span class="unread-count"><?php bp_message_thread_unread_count(); ?></span>
				</div>
				
				<div class="thread-avatar">
					<?php bp_message_thread_avatar(); ?>
				</div>
			
				<?php if ( 'sentbox' != bp_current_action() ) : ?>
				<div class="thread-from">
					<?php bp_message_thread_from(); ?>
					<span class="activity">
						<?php bp_message_thread_last_post_date(); ?>
					</span>
				</div>
				<?php else: ?>
				<div class="thread-from">
					<?php bp_message_thread_to(); ?>
					<span class="activity">
						<?php bp_message_thread_last_post_date(); ?>
					</span>
				</div>
				<?php endif; ?>
				
				<div class="thread-info">
					<p>
						<a href="<?php bp_message_thread_view_link(); ?>" title="<?php _e( "View Message", "buddypress" ); ?>"><?php bp_message_thread_subject(); ?></a>
					</p>
					<p class="thread-excerpt">
						<?php bp_message_thread_excerpt(); ?>
					</p>
				</div>
				
				<?php do_action( 'bp_messages_inbox_list_item' ); ?>
				
				<div class="thread-options">
					<input type="checkbox" name="message_ids[]" value="<?php bp_message_thread_id(); ?>" />
					<a class="button confirm" href="<?php bp_message_thread_delete_link(); ?>" title="<?php _e( "Delete Message", "buddypress" ); ?>"><?php _e( 'Delete', 'buddypress' ); ?></a>
				</div>
				
				<div class="clear"><!--empty--></div>
			</div>
			<!-- end row -->
			
		<?php endwhile; ?>	
	</div><!-- message threads -->
	
	<div class="pagination pagination_bottom no-ajax" id="user-pag">

		<div class="pag-count" id="messages-dir-count">
			<?php bp_messages_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="messages-dir-pag">
			<?php bp_messages_pagination(); ?>
		</div>

	</div><!-- .pagination -->
	
	<div class="messages-options-nav">
		<?php bp_messages_options(); ?>
	</div><!-- .messages-options-nav -->
	
	
	<?php do_action( 'bp_after_member_messages_threads' ); ?>

	<?php do_action( 'bp_after_member_messages_options' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, no messages were found.', 'buddypress' ); ?></p>
	</div>

<?php endif;?>

<?php do_action( 'bp_after_member_messages_loop' ); ?>
