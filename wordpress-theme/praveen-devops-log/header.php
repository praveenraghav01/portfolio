<?php
/**
 * Header — nav chrome shared by every template.
 * No preloader / hero canvas here by design: those are one-time,
 * homepage-only flourishes on the main site; a blog you reload
 * often stays fast instead.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?><!DOCTYPE html>
<html lang="<?php bloginfo( 'language' ); ?>" data-theme="light">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#f2f5f2" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#08090b" media="(prefers-color-scheme: dark)">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="backdrop" aria-hidden="true">
	<div class="backdrop__grid"></div>
	<div class="backdrop__glow"></div>
	<div class="backdrop__grain"></div>
</div>

<a class="skip-link" href="#main">Skip to content</a>

<header class="site-header" id="siteHeader">
	<nav class="nav" id="nav" aria-label="Primary">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> — home">
			<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
				<span class="brand__mark">PK</span>
			<?php endif; ?>
			<span class="brand__name"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> <span class="role">— DevOps Log</span></span>
		</a>

		<div class="nav__links" id="navLinks">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'items_wrap'     => '%3$s',
				'fallback_cb'    => 'pdl_primary_menu_fallback',
				'walker'         => new PDL_Nav_Walker(),
			) );
			?>
		</div>

		<div class="nav__right">
			<span class="nav__status" title="Local time · Gurugram (IST)">
				<span class="live"></span>IST
				<span class="clock tnum" data-clock>00:00:00</span>
			</span>

			<button class="theme-toggle" id="themeToggle" type="button" aria-label="Switch to light theme" data-cursor="theme">
				<svg class="tt-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
				<svg class="tt-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
			</button>

			<button class="search-toggle" id="searchToggle" type="button" aria-label="Search posts" aria-expanded="false" data-cursor="search">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
			</button>

			<a class="btn btn--primary nav__cta magnetic" href="<?php echo esc_url( get_theme_mod( 'pdl_portfolio_url', 'https://praveenraghav.com' ) ); ?>" data-cursor="open">Portfolio ↗</a>

			<button class="nav__toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="navLinks">
				<span></span><span></span><span></span>
			</button>
		</div>

		<div class="search-panel" id="searchPanel">
			<?php get_search_form(); ?>
		</div>
	</nav>
</header>

<main id="main">
