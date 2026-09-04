<?php
/**
 * Flat nav walker — outputs plain <a class="nav__link"> items with no
 * <ul>/<li> wrapper, matching the original site's nav markup exactly
 * (which the CSS in style.css §9 Navigation was written for).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class PDL_Nav_Walker extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<div class="nav__submenu">';
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</div>';
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'nav__link';
		if ( in_array( 'current-menu-item', $classes, true ) || in_array( 'current_page_item', $classes, true ) ) {
			$classes[] = 'is-active';
		}
		$class_names = implode( ' ', array_filter( $classes ) );

		$atts = array(
			'href'  => ! empty( $item->url ) ? $item->url : '',
			'class' => $class_names,
		);
		if ( in_array( 'is-active', $classes, true ) ) $atts['aria-current'] = 'page';

		$attributes = '';
		foreach ( $atts as $k => $v ) {
			if ( '' === $v ) continue;
			$attributes .= ' ' . $k . '="' . ( 'href' === $k ? esc_url( $v ) : esc_attr( $v ) ) . '"';
		}

		$output .= '<a' . $attributes . '>' . apply_filters( 'the_title', $item->title, $item->ID ) . '</a>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		// no closing tag needed — <a> is self-contained
	}
}
