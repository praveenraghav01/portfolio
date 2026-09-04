<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) : the_post();
	$hash = pdl_post_hash();
?>

<article <?php post_class( 'section pdl-post' ); ?>>
	<div class="container">

		<div class="section-head pdl-post__head">
			<span class="eyebrow" data-reveal>commit #<?php echo esc_html( $hash ); ?> &mdash; main</span>
			<h1 data-reveal><?php the_title(); ?></h1>

			<div class="pdl-post__meta mono" data-reveal>
				<span><?php echo esc_html( get_the_date() ); ?></span>
				<span>by <?php the_author(); ?></span>
				<span><?php echo esc_html( pdl_reading_time() ); ?> min read</span>
				<?php if ( comments_open() || get_comments_number() ) : ?>
					<a href="#comments"><?php comments_number( '0 comments', '1 comment', '% comments' ); ?></a>
				<?php endif; ?>
			</div>

			<?php
			$cats = get_the_category();
			$tags = get_the_tags();
			if ( $cats || $tags ) :
			?>
				<div class="pdl-post__tags" data-stagger>
					<?php foreach ( $cats as $cat ) : ?>
						<a class="kchip" href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><span class="d"></span><?php echo esc_html( $cat->name ); ?></a>
					<?php endforeach; ?>
					<?php if ( $tags ) foreach ( $tags as $tag ) : ?>
						<a class="kchip" href="<?php echo esc_url( get_tag_link( $tag ) ); ?>">#<?php echo esc_html( $tag->name ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php pdl_ad_slot( 'top' ); ?>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="pdl-post__thumb" data-reveal>
				<?php the_post_thumbnail( 'large' ); ?>
			</figure>
		<?php endif; ?>

		<div class="pdl-post__grid">
			<?php if ( count( pdl_get_toc() ) >= 3 || pdl_has_ad( 'sidebar' ) ) : ?>
				<aside class="pdl-post__aside" data-reveal="left">
					<?php pdl_toc(); ?>
					<?php pdl_ad_slot( 'sidebar' ); ?>
				</aside>
			<?php endif; ?>

			<div class="pdl-post__main">
				<div class="entry-content" data-reveal>
					<?php the_content(); ?>
				</div>

				<div class="pdl-post__share" data-reveal>
					<button class="copyline" id="pdlCopyLink" data-copy="<?php the_permalink(); ?>" data-cursor="copy" type="button" aria-live="polite">
						<span class="p">$</span>
						<span class="addr">echo "<?php the_permalink(); ?>" | clip</span>
						<span class="ok">✓ link copied</span>
					</button>
				</div>

				<?php pdl_post_nav(); ?>
				<?php pdl_related_posts(); ?>
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
