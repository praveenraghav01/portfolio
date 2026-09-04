<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
$is_home = is_home() && ! is_paged();
?>

<section class="section pdl-hero">
	<div class="container">
		<div class="section-head">
			<span class="eyebrow" data-reveal><span class="idx">log</span> — git log --oneline --blog</span>
			<h1 data-reveal><?php echo $is_home ? 'Field notes from production.' : esc_html__( 'Posts', 'praveen-devops-log' ); ?></h1>
		</div>
		<?php if ( $is_home ) : ?>
			<p class="lead pdl-hero__lead" data-reveal>Write-ups from nine years of pipelines, platforms, and 3&nbsp;a.m. pages — what broke, what shipped, and what I'd automate next time.</p>
		<?php endif; ?>
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
			<div class="pdl-empty mono">$ git log — no commits yet.</div>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
