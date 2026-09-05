<?php
/**
 * Cozy Journal theme functions.
 *
 * @package Cozy_Journal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'COZY_JOURNAL_VERSION' ) ) {
	define( 'COZY_JOURNAL_VERSION', '1.6.1' );
}

if ( ! defined( 'COZY_JOURNAL_NAME' ) ) {
	define( 'COZY_JOURNAL_NAME', 'Cozy Journal' );
}

if ( ! defined( 'COZY_JOURNAL_AUTHOR' ) ) {
	define( 'COZY_JOURNAL_AUTHOR', 'GetL-X' );
}

if ( ! defined( 'COZY_JOURNAL_REPOSITORY_URL' ) ) {
	define( 'COZY_JOURNAL_REPOSITORY_URL', 'https://github.com/getl-x/cozy-journal' );
}

/**
 * Sets up theme defaults and registers WordPress features.
 */
function cozy_journal_setup() {
	load_theme_textdomain( 'cozy-journal', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 120,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support(
		'custom-background',
		array(
			'default-color' => 'f7efe2',
		)
	);

	add_image_size( 'cozy-journal-card', 960, 650, true );

	register_nav_menus(
		array(
			'primary' => __( '主导航', 'cozy-journal' ),
			'footer'  => __( '页脚导航', 'cozy-journal' ),
		)
	);
}
add_action( 'after_setup_theme', 'cozy_journal_setup' );

/**
 * Sets the content width in pixels.
 */
function cozy_journal_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'cozy_journal_content_width', 760 );
}
add_action( 'after_setup_theme', 'cozy_journal_content_width', 0 );

/**
 * Registers the sidebar.
 */
function cozy_journal_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( '手账侧栏', 'cozy-journal' ),
			'id'            => 'sidebar-1',
			'description'   => __( '这里适合放作者介绍、分类、标签或近期文章。', 'cozy-journal' ),
			'before_widget' => '<section id="%1$s" class="widget journal-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title"><span>',
			'after_title'   => '</span></h2>',
		)
	);
}
add_action( 'widgets_init', 'cozy_journal_widgets_init' );

/**
 * Enqueues public styles and scripts.
 */
function cozy_journal_scripts() {
	wp_enqueue_style( 'cozy-journal-style', get_stylesheet_uri(), array(), COZY_JOURNAL_VERSION );
	wp_enqueue_script(
		'cozy-journal-navigation',
		get_template_directory_uri() . '/assets/js/navigation.js',
		array(),
		COZY_JOURNAL_VERSION,
		true
	);

	wp_localize_script(
		'cozy-journal-navigation',
		'cozyJournalScreenReaderText',
		array(
			'expand'   => get_theme_mod( 'cozy_journal_mobile_menu_text', __( '展开菜单', 'cozy-journal' ) ),
			'collapse' => __( '收起菜单', 'cozy-journal' ),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_add_inline_style( 'cozy-journal-style', cozy_journal_custom_properties() );
}
add_action( 'wp_enqueue_scripts', 'cozy_journal_scripts' );

/**
 * Removes comment-author website links on the public site unless enabled.
 *
 * @param string $link       Rendered author link.
 * @param string $author     Comment author name.
 * @param int    $comment_id Comment ID.
 * @return string
 */
function cozy_journal_comment_author_link( $link, $author, $comment_id ) {
	if ( is_admin() || get_theme_mod( 'cozy_journal_link_comment_author_website', false ) ) {
		return $link;
	}

	$comment = get_comment( $comment_id );

	return esc_html( $comment ? get_comment_author( $comment ) : $author );
}
add_filter( 'get_comment_author_link', 'cozy_journal_comment_author_link', 10, 3 );

/**
 * Returns a sanitized theme color, falling back to the default.
 *
 * @param string $setting Theme mod name.
 * @param string $default Default hexadecimal color.
 * @return string
 */
function cozy_journal_get_color( $setting, $default ) {
	$value = get_theme_mod( $setting, $default );
	if ( ! is_string( $value ) ) {
		return $default;
	}

	$color = sanitize_hex_color( $value );

	return $color ? $color : $default;
}

/**
 * Darkens a hexadecimal color by mixing it with black.
 *
 * @param string $color  Hexadecimal color.
 * @param int    $amount Percentage of black to mix in.
 * @return string
 */
function cozy_journal_darken_color( $color, $amount = 22 ) {
	if ( ! is_string( $color ) ) {
		return '#000000';
	}

	$color = sanitize_hex_color( $color );
	if ( ! $color ) {
		return '#000000';
	}

	$hex = substr( $color, 1 );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	$factor = ( 100 - min( 100, absint( $amount ) ) ) / 100;
	return sprintf(
		'#%02x%02x%02x',
		(int) round( hexdec( substr( $hex, 0, 2 ) ) * $factor ),
		(int) round( hexdec( substr( $hex, 2, 2 ) ) * $factor ),
		(int) round( hexdec( substr( $hex, 4, 2 ) ) * $factor )
	);
}

/**
 * Chooses black or white text with a WCAG contrast ratio of at least 4.5:1.
 *
 * @param string $color Hexadecimal background color.
 * @return string
 */
function cozy_journal_contrast_text_color( $color ) {
	if ( ! is_string( $color ) ) {
		return '#000000';
	}

	$color = sanitize_hex_color( $color );
	if ( ! $color ) {
		return '#000000';
	}

	$hex = substr( $color, 1 );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	$channels = array(
		hexdec( substr( $hex, 0, 2 ) ) / 255,
		hexdec( substr( $hex, 2, 2 ) ) / 255,
		hexdec( substr( $hex, 4, 2 ) ) / 255,
	);
	foreach ( $channels as $index => $channel ) {
		$channels[ $index ] = $channel <= 0.03928
			? $channel / 12.92
			: pow( ( $channel + 0.055 ) / 1.055, 2.4 );
	}

	$luminance      = ( 0.2126 * $channels[0] ) + ( 0.7152 * $channels[1] ) + ( 0.0722 * $channels[2] );
	$white_contrast = 1.05 / ( $luminance + 0.05 );

	return $white_contrast >= 4.5 ? '#ffffff' : '#000000';
}

/**
 * Builds CSS custom properties from Customizer values.
 *
 * @return string
 */
function cozy_journal_custom_properties() {
	$primary       = cozy_journal_get_color( 'cozy_journal_primary_color', '#d8757f' );
	$primary_dark  = cozy_journal_darken_color( $primary );
	$primary_contrast = cozy_journal_contrast_text_color( $primary );
	$primary_dark_contrast = cozy_journal_contrast_text_color( $primary_dark );
	$secondary     = cozy_journal_get_color( 'cozy_journal_secondary_color', '#d9a441' );
	$paper         = cozy_journal_get_color( 'cozy_journal_paper_color', '#f7efe2' );
	$card          = cozy_journal_get_color( 'cozy_journal_card_color', '#fffdf8' );
	$ink           = cozy_journal_get_color( 'cozy_journal_ink_color', '#4f413b' );
	$muted         = cozy_journal_get_color( 'cozy_journal_muted_color', '#89756b' );
	$radius        = cozy_journal_get_range_mod( 'cozy_journal_card_radius', 18, 4, 36 );
	$font_size     = cozy_journal_get_range_mod( 'cozy_journal_body_font_size', 16, 14, 20 );
	$line_height   = cozy_journal_get_range_mod( 'cozy_journal_body_line_height', 180, 150, 220 ) / 100;
	$site_width    = cozy_journal_get_range_mod( 'cozy_journal_site_width', 1180, 960, 1440 );
	$content_width = cozy_journal_get_range_mod( 'cozy_journal_content_width', 760, 620, 920 );
	$page_spacing  = cozy_journal_get_range_mod( 'cozy_journal_page_spacing', 38, 16, 80 );
	$image_radius  = cozy_journal_get_range_mod( 'cozy_journal_image_radius', 7, 0, 24 );
	$sidebar_width = cozy_journal_get_range_mod( 'cozy_journal_sidebar_width', 300, 240, 380 );
	$tape_opacity  = cozy_journal_get_range_mod( 'cozy_journal_tape_opacity', 100, 20, 100 ) / 100;

	$heading_fonts = array(
		'handwritten' => '"STKaiti", "KaiTi", "FangSong", "Comic Sans MS", cursive',
		'rounded'     => '"Microsoft YaHei", "PingFang SC", "Hiragino Sans GB", sans-serif',
		'serif'       => '"Noto Serif SC", "Songti SC", "SimSun", serif',
	);
	$body_fonts = array(
		'system'  => '"PingFang SC", "Microsoft YaHei", "Noto Sans CJK SC", sans-serif',
		'rounded' => '"Microsoft YaHei", "PingFang SC", "Hiragino Sans GB", sans-serif',
		'serif'   => '"Noto Serif SC", "Songti SC", "SimSun", serif',
	);

	$heading_choice = get_theme_mod( 'cozy_journal_heading_font', 'handwritten' );
	$body_choice    = get_theme_mod( 'cozy_journal_body_font', 'system' );
	$heading_font   = isset( $heading_fonts[ $heading_choice ] ) ? $heading_fonts[ $heading_choice ] : $heading_fonts['handwritten'];
	$body_font      = isset( $body_fonts[ $body_choice ] ) ? $body_fonts[ $body_choice ] : $body_fonts['system'];
	$button_radii   = array(
		'soft'    => '7px',
		'rounded' => '14px',
		'pill'    => '999px',
	);
	$button_shape   = cozy_journal_get_choice_mod( 'cozy_journal_button_shape', 'pill', array_keys( $button_radii ) );
	$shadow_sets    = array(
		'none'   => array( 'none', 'none' ),
		'soft'   => array( '0 16px 40px rgba(82,61,46,.12)', '0 8px 20px rgba(82,61,46,.10)' ),
		'medium' => array( '0 18px 46px rgba(82,61,46,.18)', '0 9px 24px rgba(82,61,46,.15)' ),
		'strong' => array( '0 22px 56px rgba(82,61,46,.25)', '0 12px 30px rgba(82,61,46,.20)' ),
	);
	$shadow_style   = cozy_journal_get_choice_mod( 'cozy_journal_shadow_style', 'soft', array_keys( $shadow_sets ) );
	$border_style   = cozy_journal_get_choice_mod( 'cozy_journal_card_border_style', 'solid', array( 'solid', 'dashed', 'none' ) );

	$css = sprintf(
		':root{--journal-primary:%1$s;--journal-primary-dark:%22$s;--journal-primary-contrast:%23$s;--journal-primary-dark-contrast:%24$s;--journal-secondary:%2$s;--journal-paper:%3$s;--journal-card:%4$s;--journal-ink:%5$s;--journal-muted:%6$s;--journal-radius:%7$dpx;--journal-heading-font:%8$s;--journal-body-font:%9$s;--journal-font-size:%10$dpx;--journal-line-height:%11$.2F;--journal-site-width:%12$dpx;--journal-content-width:%13$dpx;--journal-page-spacing:%14$dpx;--journal-image-radius:%15$dpx;--journal-sidebar-width:%16$dpx;--journal-tape-opacity:%17$.2F;--journal-button-radius:%18$s;--journal-shadow:%19$s;--journal-shadow-small:%20$s;--journal-card-border-style:%21$s;}',
		esc_attr( $primary ),
		esc_attr( $secondary ),
		esc_attr( $paper ),
		esc_attr( $card ),
		esc_attr( $ink ),
		esc_attr( $muted ),
		$radius,
		$heading_font,
		$body_font,
		$font_size,
		$line_height,
		$site_width,
		$content_width,
		$page_spacing,
		$image_radius,
		$sidebar_width,
		$tape_opacity,
		$button_radii[ $button_shape ],
		$shadow_sets[ $shadow_style ][0],
		$shadow_sets[ $shadow_style ][1],
		$border_style,
		esc_attr( $primary_dark ),
		esc_attr( $primary_contrast ),
		esc_attr( $primary_dark_contrast )
	);

	if ( ! get_theme_mod( 'cozy_journal_smooth_scroll', true ) ) {
		$css .= 'html{scroll-behavior:auto;}';
	}

	return $css;
}

/**
 * Adds useful body classes for the selected layout.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function cozy_journal_body_classes( $classes ) {
	$classes[] = 'journal-layout-' . cozy_journal_get_choice_mod( 'cozy_journal_archive_layout', 'grid', array( 'grid', 'list' ) );
	$classes[] = 'journal-home-columns-' . cozy_journal_get_choice_mod( 'cozy_journal_home_columns', '2', array( '1', '2', '3' ) );
	$classes[] = 'journal-archive-columns-' . cozy_journal_get_choice_mod( 'cozy_journal_archive_columns', '2', array( '1', '2', '3' ) );
	$classes[] = 'journal-sidebar-' . cozy_journal_get_choice_mod( 'cozy_journal_sidebar_position', 'right', array( 'left', 'right' ) );
	$classes[] = 'journal-hero-layout-' . cozy_journal_get_choice_mod( 'cozy_journal_hero_layout', 'split', array( 'split', 'centered', 'text-only' ) );
	$classes[] = 'journal-header-layout-' . cozy_journal_get_choice_mod( 'cozy_journal_header_layout', 'horizontal', array( 'horizontal', 'centered' ) );
	$classes[] = 'journal-header-density-' . cozy_journal_get_choice_mod( 'cozy_journal_header_density', 'comfortable', array( 'compact', 'comfortable' ) );
	$classes[] = 'journal-thumbnail-ratio-' . cozy_journal_get_choice_mod( 'cozy_journal_thumbnail_ratio', 'landscape', array( 'wide', 'landscape', 'square', 'portrait' ) );
	$classes[] = 'journal-single-title-' . cozy_journal_get_choice_mod( 'cozy_journal_single_title_alignment', 'center', array( 'left', 'center' ) );
	$classes[] = 'journal-single-image-' . cozy_journal_get_choice_mod( 'cozy_journal_single_image_style', 'polaroid', array( 'polaroid', 'plain' ) );
	$classes[] = 'journal-footer-align-' . cozy_journal_get_choice_mod( 'cozy_journal_footer_alignment', 'center', array( 'left', 'center' ) );
	$classes[] = 'journal-link-style-' . cozy_journal_get_choice_mod( 'cozy_journal_link_style', 'underline', array( 'underline', 'hover' ) );

	if ( cozy_journal_should_show_sidebar() ) {
		$classes[] = 'journal-has-sidebar';
	} else {
		$classes[] = 'journal-no-sidebar';
	}

	if ( get_theme_mod( 'cozy_journal_show_tape', true ) ) {
		$classes[] = 'journal-has-tape';
	}
	if ( get_theme_mod( 'cozy_journal_enable_card_tilt', true ) ) {
		$classes[] = 'journal-card-tilt';
	}
	if ( ! get_theme_mod( 'cozy_journal_enable_hover_motion', true ) ) {
		$classes[] = 'journal-no-hover-motion';
	}
	if ( ! get_theme_mod( 'cozy_journal_show_background_pattern', true ) ) {
		$classes[] = 'journal-no-background-pattern';
	}
	if ( ! get_theme_mod( 'cozy_journal_show_background_shapes', true ) ) {
		$classes[] = 'journal-no-background-shapes';
	}
	if ( ! get_theme_mod( 'cozy_journal_show_paper_lines', true ) ) {
		$classes[] = 'journal-no-paper-lines';
	}
	if ( ! get_theme_mod( 'cozy_journal_footer_paper_lines', true ) ) {
		$classes[] = 'journal-no-footer-lines';
	}
	if ( get_theme_mod( 'cozy_journal_show_sticky_marker', true ) ) {
		$classes[] = 'journal-sticky-marker';
	}
	if ( get_theme_mod( 'cozy_journal_sticky_header', false ) ) {
		$classes[] = 'journal-sticky-header';
	}
	if ( ! get_theme_mod( 'cozy_journal_show_hero_art', true ) ) {
		$classes[] = 'journal-no-hero-art';
	}

	return $classes;
}
add_filter( 'body_class', 'cozy_journal_body_classes' );

/**
 * Makes automatic excerpts fit the card design.
 *
 * @return int
 */
function cozy_journal_excerpt_length() {
	return cozy_journal_get_range_mod( 'cozy_journal_excerpt_length', 34, 10, 100 );
}
add_filter( 'excerpt_length', 'cozy_journal_excerpt_length', 99 );

/**
 * Replaces the default excerpt suffix.
 *
 * @return string
 */
function cozy_journal_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'cozy_journal_excerpt_more' );

/**
 * Returns the selected sticker group.
 *
 * @return string[]
 */
function cozy_journal_get_stickers() {
	$sets = array(
		'floral' => array( '🌼', '🌿', '🌷' ),
		'cozy'   => array( '☕', '🧸', '🧶' ),
		'sweets' => array( '🍓', '🍰', '🍪' ),
		'sky'    => array( '⭐', '☁️', '🌙' ),
	);
	$selected = get_theme_mod( 'cozy_journal_sticker_style', 'floral' );

	$stickers = isset( $sets[ $selected ] ) ? $sets[ $selected ] : $sets['floral'];

	foreach ( $stickers as $index => $sticker ) {
		$custom = trim( (string) get_theme_mod( 'cozy_journal_custom_sticker_' . ( $index + 1 ), '' ) );
		if ( '' !== $custom ) {
			$stickers[ $index ] = $custom;
		}
	}

	return $stickers;
}

require get_template_directory() . '/inc/theme-options-extra.php';
require get_template_directory() . '/inc/theme-option-helpers.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/theme-options.php';
require get_template_directory() . '/inc/frontend-editor.php';
require get_template_directory() . '/inc/customizer.php';
