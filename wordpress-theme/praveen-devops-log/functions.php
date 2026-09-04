<?php
/**
 * Praveen DevOps Log — theme functions
 * Classic PHP theme matching praveenraghav.com's design system.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'PDL_VERSION', '1.0.0' );
define( 'PDL_DIR', get_template_directory() );
define( 'PDL_URI', get_template_directory_uri() );

/* ── Theme setup ───────────────────────────────────────────── */
function pdl_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'custom-logo', array(
		'height'      => 34,
		'width'       => 34,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'align-wide' );

	set_post_thumbnail_size( 1200, 675, true );
	add_image_size( 'pdl-card', 640, 400, true );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'praveen-devops-log' ),
		'footer'  => __( 'Footer Menu', 'praveen-devops-log' ),
	) );
}
add_action( 'after_setup_theme', 'pdl_setup' );

/* ── Assets ────────────────────────────────────────────────── */
function pdl_assets() {
	wp_enqueue_style( 'pdl-style', get_stylesheet_uri(), array(), PDL_VERSION );

	// Prism.js — self-hosted-style CDN load, deferred; themed to the token
	// palette in style.css (see "Code blocks" section) rather than a stock skin.
	wp_enqueue_style( 'pdl-prism', 'https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism-okaidia.min.css', array(), '1.29.0' );
	wp_enqueue_script( 'pdl-prism-core', 'https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-core.min.js', array(), '1.29.0', true );
	wp_enqueue_script( 'pdl-prism-autoloader', 'https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/autoloader/prism-autoloader.min.js', array( 'pdl-prism-core' ), '1.29.0', true );

	wp_enqueue_script( 'pdl-main', PDL_URI . '/assets/js/main.js', array(), PDL_VERSION, true );
	wp_enqueue_script( 'pdl-cursor', PDL_URI . '/assets/js/cursor.js', array(), PDL_VERSION, true );

	if ( is_singular() && comments_open() ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'pdl_assets' );

function pdl_resource_hints() {
	echo '<link rel="preload" href="' . esc_url( PDL_URI . '/assets/fonts/space-grotesk-var.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	echo '<link rel="preload" href="' . esc_url( PDL_URI . '/assets/fonts/jetbrains-mono-var.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
}
add_action( 'wp_head', 'pdl_resource_hints', 1 );

/* Theme boot script — runs before first paint, no flash of wrong theme. */
function pdl_theme_boot_script() {
	?>
	<script>
	(function () {
		var t = null;
		try { t = localStorage.getItem('theme'); } catch (e) {}
		if (t !== 'light' && t !== 'dark') t = 'light';
		document.documentElement.setAttribute('data-theme', t);
	})();
	</script>
	<?php
}
add_action( 'wp_head', 'pdl_theme_boot_script', 0 );

/* ── Content tuning ────────────────────────────────────────── */
add_filter( 'excerpt_length', function () { return 30; } );
add_filter( 'excerpt_more', function () { return '&hellip;'; } );

// Auto-embeds and iframes get a wrapper so CSS can size them responsively.
add_filter( 'embed_oembed_html', function ( $html ) {
	return '<div class="embed-frame">' . $html . '</div>';
}, 10, 1 );

/* ── Reading time ──────────────────────────────────────────── */
function pdl_reading_time( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();
	$content = get_post_field( 'post_content', $post_id );
	$words   = str_word_count( wp_strip_all_tags( strip_shortcodes( $content ) ) );
	$minutes = max( 1, (int) ceil( $words / 200 ) );
	return $minutes;
}

/* ── Short "commit hash" for a post — stable, cosmetic only ── */
function pdl_post_hash( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();
	return substr( md5( 'pdl-' . $post_id . get_the_time( 'U', $post_id ) ), 0, 7 );
}

/* ── Heading anchors + table of contents ──────────────────────
 * Slugifies h2/h3 headings inside post content so the TOC (and
 * anyone linking to a section) has stable in-page anchors. */
function pdl_add_heading_ids( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() ) return $content;
	$seen = array();
	return preg_replace_callback(
		'/<h([23])(.*?)>(.*?)<\/h\1>/i',
		function ( $m ) use ( &$seen ) {
			$level = $m[1];
			$attrs = $m[2];
			$text  = $m[3];
			if ( preg_match( '/id=/', $attrs ) ) return $m[0];
			$slug = sanitize_title( wp_strip_all_tags( $text ) );
			if ( '' === $slug ) $slug = 'section';
			$base = $slug; $i = 2;
			while ( in_array( $slug, $seen, true ) ) { $slug = $base . '-' . $i++; }
			$seen[] = $slug;
			return '<h' . $level . $attrs . ' id="' . esc_attr( $slug ) . '">' . $text . '</h' . $level . '>';
		},
		$content
	);
}
add_filter( 'the_content', 'pdl_add_heading_ids', 9 );

function pdl_get_toc( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();
	$content = apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) );
	$toc = array();
	if ( preg_match_all( '/<h([23])\b[^>]*\bid="([^"]+)"[^>]*>(.*?)<\/h\1>/is', $content, $matches, PREG_SET_ORDER ) ) {
		foreach ( $matches as $m ) {
			$toc[] = array(
				'level' => (int) $m[1],
				'id'    => $m[2],
				'text'  => wp_strip_all_tags( $m[3] ),
			);
		}
	}
	return $toc;
}

/* ── Code blocks: wrap for copy-button + language chrome ─────
 * WP core code blocks render as <pre><code class="language-x">.
 * We wrap in a figure with a filename-bar look, matching the
 * .spec/yaml terminal component from the main site. */
function pdl_wrap_code_blocks( $content ) {
	if ( ! is_singular() || ! in_the_loop() ) return $content;
	return preg_replace_callback(
		'/<pre class="([^"]*)wp-block-code([^"]*)">\s*<code([^>]*)>/i',
		function ( $m ) {
			$lang = 'text';
			if ( preg_match( '/language-([a-z0-9]+)/i', $m[3], $lm ) ) $lang = $lm[1];
			return '<figure class="codeblock" data-lang="' . esc_attr( $lang ) . '">'
				. '<figcaption><span class="d"></span><span class="codeblock__lang">' . esc_html( $lang ) . '</span>'
				. '<button type="button" class="codeblock__copy" data-cursor="copy">copy</button></figcaption>'
				. '<pre class="' . esc_attr( $m[1] . 'wp-block-code' . $m[2] ) . '"><code' . $m[3] . '>';
		},
		$content
	);
}
add_filter( 'the_content', 'pdl_wrap_code_blocks', 20 );

function pdl_close_code_wrappers( $content ) {
	if ( ! is_singular() || ! in_the_loop() ) return $content;
	return preg_replace( '/<\/code><\/pre>/i', '</code></pre></figure>', $content );
}
add_filter( 'the_content', 'pdl_close_code_wrappers', 21 );

/* ── Customizer: social + cross-links back to the portfolio ──── */
require PDL_DIR . '/inc/customizer.php';
require PDL_DIR . '/inc/template-tags.php';
require PDL_DIR . '/inc/class-nav-walker.php';
require PDL_DIR . '/inc/markdown-salvage.php';
require PDL_DIR . '/inc/ads.php';

/* ── Nav fallback if no menu is assigned yet ──────────────────── */
function pdl_primary_menu_fallback() {
	echo '<a class="nav__link is-active" href="' . esc_url( home_url( '/' ) ) . '">Log</a>';
	$portfolio = get_theme_mod( 'pdl_portfolio_url', 'https://praveenraghav.com' );
	echo '<a class="nav__link" href="' . esc_url( $portfolio ) . '">Portfolio</a>';
}

/* ── Misc cleanup ──────────────────────────────────────────── */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );
show_admin_bar( false );
