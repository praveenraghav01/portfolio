<?php
/**
 * Ad placements — Customizer fields for raw ad codes (AdSense, direct
 * sponsors, etc.) plus the template tag that renders them, styled as a
 * quiet "sponsored" box consistent with the rest of the design system.
 *
 * Five independent slots, each optional (empty = not rendered):
 *   top      — above the fold, right below the post title/tags
 *   incontent— auto-inserted mid-article (after the middle paragraph)
 *   sidebar  — in the sticky rail next to the article, alongside the TOC
 *   feed     — between posts in the blog list/archive/search, every 4th
 *   footer   — a full-width banner just above the site footer
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function pdl_ad_slot_labels() {
	return array(
		'top'       => 'Top of post',
		'incontent' => 'Mid-article (auto-inserted)',
		'sidebar'   => 'Sidebar rail',
		'feed'      => 'Between posts (every 4th)',
		'footer'    => 'Above footer',
	);
}

/* Ad codes need <script>/<iframe> to be useful (AdSense etc.), so this
 * can't go through wp_kses_post. Only trust it from a role that already
 * has unfiltered_html (admins on a normal single-site install); anyone
 * without that capability gets the safe, script-stripped version. */
function pdl_sanitize_ad_code( $value ) {
	if ( current_user_can( 'unfiltered_html' ) ) return $value;
	return wp_kses_post( $value );
}

function pdl_ads_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'pdl_ads', array(
		'title'       => __( 'Praveen DevOps Log — Ad Placements', 'praveen-devops-log' ),
		'description' => __( 'Paste an ad network snippet (AdSense, etc.) or any sponsor HTML into a slot to enable it. Leave a slot empty to hide it entirely.', 'praveen-devops-log' ),
		'priority'    => 31,
	) );

	$wp_customize->add_setting( 'pdl_adsense_client', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'pdl_adsense_client', array(
		'section'     => 'pdl_ads',
		'label'       => __( 'AdSense Publisher ID', 'praveen-devops-log' ),
		'description' => __( 'Just the ID: ca-pub-1234567890123456 or pub-1234567890123456 (as shown in AdSense → Account) — not a full ad-unit snippet. Setting this loads the AdSense script site-wide and serves /ads.txt automatically; it does not by itself show any ads.', 'praveen-devops-log' ),
		'type'        => 'text',
	) );

	foreach ( pdl_ad_slot_labels() as $key => $label ) {
		$setting = 'pdl_ad_' . $key;
		$wp_customize->add_setting( $setting, array(
			'default'           => '',
			'sanitize_callback' => 'pdl_sanitize_ad_code',
		) );
		$wp_customize->add_control( $setting, array(
			'section' => 'pdl_ads',
			'label'   => $label,
			'type'    => 'textarea',
		) );
	}
}
add_action( 'customize_register', 'pdl_ads_customize_register' );

/* Accepts whatever format the user pastes — a bare ID ("pub-123...", or
 * just the digits), or (by mistake) an entire ad-unit snippet with a
 * client ID and an ad-slot ID both in it — and returns just the 16-digit
 * publisher ID in both forms AdSense needs: "ca-pub-123..." for the
 * script tag, "pub-123..." for ads.txt.
 *
 * Anchoring on "pub-" and capping at 16 digits matters: naively stripping
 * every non-digit character from a pasted full snippet concatenates the
 * client ID with whatever other numbers (like a data-ad-slot value) are
 * in the same paste, producing a bogus, oversized ID. */
function pdl_adsense_ids() {
	$raw = trim( (string) get_theme_mod( 'pdl_adsense_client', '' ) );
	if ( '' === $raw ) return array( '', '' );

	if ( preg_match( '/pub-(\d+)/', $raw, $m ) ) {
		$digits = $m[1];
	} else {
		$digits = preg_replace( '/[^0-9]/', '', $raw );
	}
	if ( strlen( $digits ) > 16 ) $digits = substr( $digits, 0, 16 );
	if ( '' === $digits ) return array( '', '' );
	return array( 'ca-pub-' . $digits, 'pub-' . $digits );
}

/* The one script AdSense needs on every page — it both loads the library
 * for the manual ad units pasted into the slots below, and (once you flip
 * "Auto ads" on inside your AdSense account) is what makes those work too.
 * No separate auto-ads snippet is needed on Google's current setup. */
function pdl_adsense_script() {
	list( $ca_pub, $pub ) = pdl_adsense_ids();
	if ( '' === $ca_pub ) return;
	echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' . esc_attr( $ca_pub ) . '" crossorigin="anonymous"></script>' . "\n";
}
add_action( 'wp_head', 'pdl_adsense_script', 2 );

/* AdSense requires an ads.txt at the site root declaring the publisher
 * ID. Most WP hosting doesn't give easy FTP/file-manager access, so serve
 * it virtually instead of requiring you to upload a static file. */
function pdl_maybe_serve_ads_txt() {
	list( $ca_pub, $pub ) = pdl_adsense_ids();
	if ( '' === $pub ) return;
	$path = trim( (string) wp_parse_url( esc_url_raw( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH ), '/' );
	if ( 'ads.txt' !== $path ) return;
	header( 'Content-Type: text/plain; charset=utf-8' );
	echo 'google.com, ' . $pub . ", DIRECT, f08c47fec0942fa0\n";
	exit;
}
add_action( 'template_redirect', 'pdl_maybe_serve_ads_txt', 0 );

function pdl_has_ad( $key ) {
	return '' !== trim( (string) get_theme_mod( 'pdl_ad_' . $key, '' ) );
}

function pdl_ad_slot( $key ) {
	if ( ! pdl_has_ad( $key ) ) return;
	$code = get_theme_mod( 'pdl_ad_' . $key, '' );
	?>
	<div class="pdl-ad pdl-ad--<?php echo esc_attr( $key ); ?>" data-ad-slot="<?php echo esc_attr( $key ); ?>">
		<span class="pdl-ad__label mono">sponsored</span>
		<div class="pdl-ad__body"><?php echo $code; // phpcs:ignore -- raw ad network markup, sanitized on save via pdl_sanitize_ad_code() ?></div>
	</div>
	<?php
}

/* Auto-insert the "incontent" slot after the middle top-level paragraph,
 * so long-form posts get a mid-article placement without any manual
 * shortcode per post. Skipped on short posts (< 6 paragraphs) where a
 * mid-article ad would sit awkwardly close to the top or the end. */
function pdl_insert_incontent_ad( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) return $content;
	if ( ! pdl_has_ad( 'incontent' ) ) return $content;

	if ( ! preg_match_all( '/<\/p>/i', $content, $m, PREG_OFFSET_CAPTURE ) || count( $m[0] ) < 6 ) {
		return $content;
	}
	$closings = $m[0];
	$mid = $closings[ (int) floor( count( $closings ) / 2 ) ];
	$insert_at = $mid[1] + strlen( $mid[0] );

	ob_start();
	pdl_ad_slot( 'incontent' );
	$ad_html = ob_get_clean();

	return substr( $content, 0, $insert_at ) . $ad_html . substr( $content, $insert_at );
}
add_filter( 'the_content', 'pdl_insert_incontent_ad', 25 );

/* Ad slot every Nth post in a "git log" listing (index/archive/search) —
 * shared by all three templates so the counting logic lives in one place. */
function pdl_logrows_loop( $ad_every = 4 ) {
	$i = 0;
	while ( have_posts() ) :
		the_post();
		pdl_logrow();
		$i++;
		if ( $ad_every && 0 === $i % $ad_every ) {
			pdl_ad_slot( 'feed' );
		}
	endwhile;
}
