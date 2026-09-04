<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
</main>

<?php if ( pdl_has_ad( 'footer' ) ) : ?>
	<div class="container"><?php pdl_ad_slot( 'footer' ); ?></div>
<?php endif; ?>

<footer class="site-footer">
	<div class="container footer__row">
		<a class="footer__brand" href="#main" data-cursor="top">
			<span class="brand__mark">PK</span> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
		</a>
		<span class="footer__mono">© <?php echo esc_html( date_i18n( 'Y' ) ); ?> · served from Gurugram · status: <span class="text-accent">operational</span></span>
		<div class="footer__links">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer',
				'container'      => false,
				'items_wrap'     => '%3$s',
				'fallback_cb'    => false,
			) );
			?>
			<a href="<?php echo esc_url( get_theme_mod( 'pdl_linkedin_url', 'https://linkedin.com/in/praveenraghav01/' ) ); ?>" target="_blank" rel="noopener" data-cursor="open">LinkedIn</a>
			<a href="<?php echo esc_url( get_theme_mod( 'pdl_github_url', 'https://github.com/praveenraghav01/' ) ); ?>" target="_blank" rel="noopener" data-cursor="open">GitHub</a>
			<a href="<?php echo esc_url( get_theme_mod( 'pdl_portfolio_url', 'https://praveenraghav.com' ) ); ?>" data-cursor="open">Portfolio</a>
			<a href="mailto:<?php echo esc_attr( get_theme_mod( 'pdl_contact_email', 'praveensinghraghav96@gmail.com' ) ); ?>" data-cursor="send">Email</a>
			<a href="#main" data-cursor="top">Back to top ↑</a>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
