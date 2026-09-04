<?php
/**
 * Markdown salvage — a rendering safety net for posts whose content was
 * pasted in as raw Markdown text instead of real blocks (a common result
 * of copy-pasting AI/notes-app output straight into the editor). WordPress
 * then wraps every line in its own <p>, and wptexturize mangles the
 * leftover "- " bullets into en-dashes and "```" fences into curly-quote
 * artifacts — which is exactly the mess this file un-does.
 *
 * Runs after wptexturize (10) so it sees those artifacts, and before the
 * copy-button code-block wrapper (20) so anything it promotes to <pre>
 * gets the same treatment as a real code block.
 *
 * This does NOT attempt to guess headings — a plain paragraph that used
 * to be a Markdown "## Heading" is indistinguishable from an ordinary
 * short sentence once the "##" is gone, and guessing wrong is worse than
 * leaving it alone. Convert those specific paragraphs to Heading blocks
 * by hand in the editor.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function pdl_markdown_salvage( $content ) {
	if ( ! is_singular() || ! in_the_loop() ) return $content;
	if ( false === strpos( $content, '<p' ) ) return $content;

	$content = pdl_salvage_code_fences( $content );
	$content = pdl_salvage_lists( $content, '/(?:<p(?:\s+class="[^"]*")?>\s*[\x{2013}\-\*]\s+.*?<\/p>\s*){2,}/isu', '/<p(?:\s+class="[^"]*")?>\s*[\x{2013}\-\*]\s+(.*?)<\/p>/isu', 'ul' );
	$content = pdl_salvage_lists( $content, '/(?:<p(?:\s+class="[^"]*")?>\s*\d+[\.\)]\s+.*?<\/p>\s*){2,}/is', '/<p(?:\s+class="[^"]*")?>\s*\d+[\.\)]\s+(.*?)<\/p>/is', 'ol' );
	$content = pdl_salvage_images( $content );
	$content = pdl_salvage_inline_markdown( $content );

	return $content;
}
add_filter( 'the_content', 'pdl_markdown_salvage', 15 );

/* "```lang" / "```" survive wptexturize as "&#8220;`lang" / "&#8220;`"
 * (the first two backticks pair up into a curly opening quote, the third
 * is left dangling) — pair up consecutive fence-marker paragraphs and
 * turn everything between them into a real code block. */
function pdl_salvage_code_fences( $content ) {
	$fence = '(?:&#8220;`|\x{201C}`|```)';
	return preg_replace_callback(
		'/<p(?:\s+class="[^"]*")?>\s*' . $fence . '\s*([a-z0-9]*)\s*<\/p>((?:(?!' . $fence . ').)*?)<p(?:\s+class="[^"]*")?>\s*' . $fence . '\s*<\/p>/isu',
		function ( $m ) {
			$lang = sanitize_html_class( strtolower( trim( $m[1] ) ) );
			if ( '' === $lang ) $lang = 'text';
			$body = preg_replace( '/<\/p>\s*<p(?:\s+class="[^"]*")?>/i', "\n", $m[2] );
			$body = preg_replace( '/^\s*<p(?:\s+class="[^"]*")?>|<\/p>\s*$/i', '', trim( $body ) );
			$body = trim( wp_strip_all_tags( $body ) );
			if ( '' === $body ) return $m[0];
			return '<pre class="wp-block-code"><code class="language-' . esc_attr( $lang ) . '">' . esc_html( $body ) . '</code></pre>';
		},
		$content
	);
}

/* Consecutive "- item" / "* item" / "1. item" paragraphs → a real list,
 * so browsers render bullets/numbers instead of literal leading dashes. */
function pdl_salvage_lists( $content, $run_pattern, $item_pattern, $tag ) {
	return preg_replace_callback(
		$run_pattern,
		function ( $m ) use ( $item_pattern, $tag ) {
			preg_match_all( $item_pattern, $m[0], $items );
			if ( empty( $items[1] ) ) return $m[0];
			$out = '<' . $tag . '>';
			foreach ( $items[1] as $item ) {
				$out .= '<li>' . wp_kses_post( trim( $item ) ) . '</li>';
			}
			$out .= '</' . $tag . '>';
			return $out;
		},
		$content
	);
}

/* "![alt](url)" left as literal text → a real <img>, or — since these
 * are frequently placeholder URLs (example.com etc.) copied along with
 * the source text — a labelled placeholder box instead of a broken-image
 * icon, so it reads as "an image goes here" rather than as a glitch. */
function pdl_salvage_images( $content ) {
	return preg_replace_callback(
		'/<p(?:\s+class="[^"]*")?>\s*!\[([^\]]*)\]\(([^)\s]+)\)\s*<\/p>/i',
		function ( $m ) {
			$alt = trim( $m[1] );
			$url = trim( $m[2] );
			$host = wp_parse_url( $url, PHP_URL_HOST );
			$placeholder_hosts = array( 'example.com', 'www.example.com', 'placeholder.com' );
			if ( ! $host || in_array( strtolower( $host ), $placeholder_hosts, true ) ) {
				return '<div class="pdl-img-placeholder mono">image placeholder' . ( $alt ? ': ' . esc_html( $alt ) : '' ) . '</div>';
			}
			return '<figure><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy">' . ( $alt ? '<figcaption>' . esc_html( $alt ) . '</figcaption>' : '' ) . '</figure>';
		},
		$content
	);
}

/* Inline "[text](url)", "**bold**", "*em*"/"_em_" left as literal text
 * inside otherwise-normal paragraphs. */
function pdl_salvage_inline_markdown( $content ) {
	$content = preg_replace( '/(?<!!)\[([^\]]+)\]\(([^)\s]+)\)/', '<a href="$2">$1</a>', $content );
	$content = preg_replace( '/\*\*([^*<>]+)\*\*/', '<strong>$1</strong>', $content );
	$content = preg_replace( '/__([^_<>]+)__/', '<strong>$1</strong>', $content );
	$content = preg_replace( '/(?<![\w*])\*([^*<>\s][^*<>]*?)\*(?![\w*])/', '<em>$1</em>', $content );
	return $content;
}
