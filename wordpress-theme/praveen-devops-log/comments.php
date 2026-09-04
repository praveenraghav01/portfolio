<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ( post_password_required() ) return;
?>
<div class="pdl-comments">

	<?php if ( have_comments() ) : ?>
		<span class="eyebrow" data-reveal>$ tail -f comments.log</span>
		<h2 class="pdl-comments__title" data-reveal>
			<?php
			$count = get_comments_number();
			printf( _n( '%s comment', '%s comments', $count, 'praveen-devops-log' ), number_format_i18n( $count ) );
			?>
		</h2>

		<ol class="pdl-comments-list" data-stagger>
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 40,
				'callback'    => 'pdl_comment_cb',
			) );
			?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<nav class="pdl-pagination mono" aria-label="Comments pagination">
				<?php paginate_comments_links(); ?>
			</nav>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="pdl-comments-closed mono">$ comments --closed on this commit.</p>
	<?php endif; ?>

	<?php if ( comments_open() ) : ?>
		<div class="pdl-comment-form">
			<span class="eyebrow" data-reveal>$ git commit -m "your take"</span>
			<?php
			comment_form( array(
				'title_reply'        => 'Leave a comment',
				'title_reply_to'     => 'Reply to %s',
				'label_submit'       => 'Post comment',
				'class_submit'       => 'btn btn--primary magnetic',
				'comment_field'      => '<p class="comment-form-comment"><label for="comment" class="sr-only">' . __( 'Comment', 'praveen-devops-log' ) . '</label><textarea id="comment" name="comment" class="pdl-input" rows="5" placeholder="Say something useful…" required></textarea></p>',
				'fields'             => array(
					'author' => '<p class="comment-form-author"><label for="author" class="sr-only">Name</label><input id="author" name="author" class="pdl-input" placeholder="Name" required></p>',
					'email'  => '<p class="comment-form-email"><label for="email" class="sr-only">Email</label><input id="email" name="email" type="email" class="pdl-input" placeholder="Email" required></p>',
					'url'    => '<p class="comment-form-url"><label for="url" class="sr-only">Website</label><input id="url" name="url" class="pdl-input" placeholder="Website (optional)"></p>',
				),
			) );
			?>
		</div>
	<?php endif; ?>

</div>
