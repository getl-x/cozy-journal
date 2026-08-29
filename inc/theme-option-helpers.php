<?php
/**
 * Front-end helpers for configurable theme details.
 *
 * @package Cozy_Journal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns an integer theme mod constrained to a safe range.
 *
 * @param string $setting Setting ID.
 * @param int    $default Default value.
 * @param int    $minimum Minimum value.
 * @param int    $maximum Maximum value.
 * @return int
 */
function cozy_journal_get_range_mod( $setting, $default, $minimum, $maximum ) {
	$value = absint( get_theme_mod( $setting, $default ) );

	return min( $maximum, max( $minimum, $value ) );
}

/**
 * Returns a theme mod only when it belongs to an allowed value list.
 *
 * @param string   $setting Setting ID.
 * @param string   $default Default value.
 * @param string[] $allowed Allowed values.
 * @return string
 */
function cozy_journal_get_choice_mod( $setting, $default, $allowed ) {
	$value = sanitize_key( get_theme_mod( $setting, $default ) );

	return in_array( $value, $allowed, true ) ? $value : $default;
}

/**
 * Reports whether the sidebar should appear in the current view.
 *
 * @return bool
 */
function cozy_journal_should_show_sidebar() {
	if ( ! get_theme_mod( 'cozy_journal_show_sidebar', true ) || ! is_active_sidebar( 'sidebar-1' ) ) {
		return false;
	}

	if ( is_search() ) {
		return (bool) get_theme_mod( 'cozy_journal_sidebar_on_search', true );
	}

	if ( is_singular( 'post' ) ) {
		return (bool) get_theme_mod( 'cozy_journal_sidebar_on_posts', true );
	}

	if ( is_page() ) {
		return (bool) get_theme_mod( 'cozy_journal_sidebar_on_pages', true );
	}

	if ( is_home() || is_archive() ) {
		return (bool) get_theme_mod( 'cozy_journal_sidebar_on_archives', true );
	}

	return true;
}

/**
 * Reports whether a featured image should appear in a given context.
 *
 * @param string $context card, single or page.
 * @return bool
 */
function cozy_journal_should_show_featured_image( $context ) {
	if ( ! get_theme_mod( 'cozy_journal_show_featured_images', true ) ) {
		return false;
	}

	$context_settings = array(
		'card'   => 'cozy_journal_show_card_featured_images',
		'single' => 'cozy_journal_show_single_featured_image',
		'page'   => 'cozy_journal_show_page_featured_image',
	);

	if ( ! isset( $context_settings[ $context ] ) ) {
		return true;
	}

	return (bool) get_theme_mod( $context_settings[ $context ], true );
}

/**
 * Returns the welcome-card date in the selected format.
 *
 * @return string
 */
function cozy_journal_get_hero_date() {
	$formats = array(
		'month-day-weekday' => 'n月j日 · l',
		'year-month-day'    => 'Y年n月j日',
		'iso'               => 'Y-m-d',
		'weekday'           => 'l',
	);
	$selected = cozy_journal_get_choice_mod( 'cozy_journal_hero_date_format', 'month-day-weekday', array_keys( $formats ) );

	return wp_date( $formats[ $selected ] );
}

/**
 * Returns query ordering arguments for the static-home article list.
 *
 * @return array<string, string>
 */
function cozy_journal_get_home_order_args() {
	$selected = cozy_journal_get_choice_mod(
		'cozy_journal_home_posts_order',
		'latest',
		array( 'latest', 'modified', 'oldest', 'random' )
	);

	$orders = array(
		'latest'   => array( 'orderby' => 'date', 'order' => 'DESC' ),
		'modified' => array( 'orderby' => 'modified', 'order' => 'DESC' ),
		'oldest'   => array( 'orderby' => 'date', 'order' => 'ASC' ),
		'random'   => array( 'orderby' => 'rand', 'order' => 'DESC' ),
	);

	return $orders[ $selected ];
}

/**
 * Returns the custom footer copyright line with supported placeholders.
 *
 * @return string
 */
function cozy_journal_get_copyright_text() {
	$template = trim( (string) get_theme_mod( 'cozy_journal_copyright_text', '' ) );

	if ( '' === $template ) {
		return sprintf(
			/* translators: 1: year, 2: site name. */
			__( '© %1$s %2$s', 'cozy-journal' ),
			wp_date( 'Y' ),
			get_bloginfo( 'name' )
		);
	}

	return strtr(
		$template,
		array(
			'{year}' => wp_date( 'Y' ),
			'{site}' => get_bloginfo( 'name' ),
		)
	);
}
