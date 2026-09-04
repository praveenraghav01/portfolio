<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<section class="section pdl-hero">
	<div class="container">
		<div class="section-head">
			<span class="eyebrow" data-reveal>$ grep -ril "<?php echo esc_html( get_search_query() ); ?>" posts/</span>
			<h1 data-reveal>
				<?php
				printf(
					/* translators: %s: search query, %d: result count */
					esc_html__( '%1$d results for "%2$s"', 'praveen-devops-log' ),
					(int) $GLOBALS['wp_query']->found_posts,
					esc_html( get_search_query() )
				);
				?>
			</h1>
		</div>
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
			<div class="pdl-empty mono">$ grep: no matches found. Try a different term.</div>
			<div class="pdl-search-retry"><?php get_search_form(); ?></div>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
