<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) : the_post();
?>

<article <?php post_class( 'section pdl-post pdl-page' ); ?>>
	<div class="container">

		<div class="section-head pdl-post__head">
			<span class="eyebrow" data-reveal>$ cat <?php echo esc_html( get_post_field( 'post_name' ) ); ?>.md</span>
			<h1 data-reveal><?php the_title(); ?></h1>
		</div>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="pdl-post__thumb" data-reveal>
				<?php the_post_thumbnail( 'large' ); ?>
			</figure>
		<?php endif; ?>

		<div class="pdl-post__grid">
			<div class="pdl-post__main pdl-post__main--full">
				<div class="entry-content" data-reveal>
					<?php the_content(); ?>
				</div>
			</div>
		</div>

	</div>
</article>

<?php
if ( comments_open() || get_comments_number() ) :
	?>
	<section class="section pdl-comments-section" id="comments">
		<div class="container">
			<?php comments_template(); ?>
		</div>
	</section>
	<?php
endif;

endwhile;
get_footer();
