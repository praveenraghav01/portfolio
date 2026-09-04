<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<section class="section pdl-404">
	<div class="container">
		<?php $pdl_path = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/'; ?>
		<span class="eyebrow" data-reveal>$ git checkout <?php echo esc_html( $pdl_path ); ?></span>
		<h1 class="pdl-404__title mono" data-reveal>404</h1>
		<p class="lead" data-reveal>error: pathspec did not match any file(s) known to this repo.</p>
		<div class="pdl-404__actions" data-reveal>
			<a class="btn btn--primary btn--lg magnetic" href="<?php echo esc_url( home_url( '/' ) ); ?>" data-cursor="open">
				git checkout main
				<svg class="btn__ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
			</a>
		</div>
		<div class="pdl-404__search" data-reveal>
			<?php get_search_form(); ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
