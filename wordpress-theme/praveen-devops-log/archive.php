<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

if ( is_category() ) {
	$grep = 'category:' . single_cat_title( '', false );
} elseif ( is_tag() ) {
	$grep = 'tag:' . single_tag_title( '', false );
} elseif ( is_author() ) {
	$grep = 'author:' . get_the_author();
} elseif ( is_date() ) {
	$grep = get_the_date();
} else {
	$grep = 'archive';
}
?>

<section class="section pdl-hero">
	<div class="container">
		<div class="section-head">
			<span class="eyebrow" data-reveal>git log --grep="<?php echo esc_html( $grep ); ?>"</span>
			<h1 data-reveal><?php the_archive_title(); ?></h1>
		</div>
		<?php the_archive_description( '<div class="lead pdl-hero__lead" data-reveal>', '</div>' ); ?>
	</div>
</section>

<section class="section pdl-log">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="logrows">
				<?php pdl_logrows_loop(); ?>
			</div>
			<?php pdl_pagination(); ?>
		<?php else : ?>
			<div class="pdl-empty mono">$ git log — no matches for this filter.</div>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
