<?php
/**
 * Customizer: the handful of cross-links that tie the blog back to the
 * main portfolio and its social profiles, so nothing is hardcoded.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function pdl_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'pdl_links', array(
		'title'    => __( 'Praveen DevOps Log — Links', 'praveen-devops-log' ),
		'priority' => 30,
	) );

	$fields = array(
		'pdl_portfolio_url' => array( 'label' => 'Portfolio URL',  'default' => 'https://praveenraghav.com' ),
		'pdl_linkedin_url'  => array( 'label' => 'LinkedIn URL',   'default' => 'https://linkedin.com/in/praveenraghav01/' ),
		'pdl_github_url'    => array( 'label' => 'GitHub URL',     'default' => 'https://github.com/praveenraghav01/' ),
		'pdl_contact_email' => array( 'label' => 'Contact email',  'default' => 'praveensinghraghav96@gmail.com' ),
		'pdl_contact_url'   => array( 'label' => 'Contact page URL (e.g. portfolio #contact)', 'default' => 'https://praveenraghav.com/#contact' ),
	);

	foreach ( $fields as $id => $f ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $f['default'],
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( $id, array(
			'section' => 'pdl_links',
			'label'   => $f['label'],
			'type'    => 'text',
		) );
	}
}
add_action( 'customize_register', 'pdl_customize_register' );
