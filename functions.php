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
	define( 'COZY_JOURNAL_VERSION', '1.3.1' );
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
			'expand'   => __( '展开菜单', 'cozy-journal' ),
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
 * Returns a sanitized theme color, falling back to the default.
 *
 * @param string $setting Theme mod name.
 * @param string $default Default hexadecimal color.
 * @return string
 */
function cozy_journal_get_color( $setting, $default ) {
	$color = sanitize_hex_color( get_theme_mod( $setting, $default ) );

	return $color ? $color : $default;
}

/**
 * Builds CSS custom properties from Customizer values.
 *
 * @return string
 */
function cozy_journal_custom_properties() {
	$primary   = cozy_journal_get_color( 'cozy_journal_primary_color', '#d8757f' );
	$secondary = cozy_journal_get_color( 'cozy_journal_secondary_color', '#d9a441' );
	$paper     = cozy_journal_get_color( 'cozy_journal_paper_color', '#f7efe2' );
	$card      = cozy_journal_get_color( 'cozy_journal_card_color', '#fffdf8' );
	$ink       = cozy_journal_get_color( 'cozy_journal_ink_color', '#4f413b' );
	$radius    = absint( get_theme_mod( 'cozy_journal_card_radius', 18 ) );
	$radius    = min( 36, max( 4, $radius ) );

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

	return sprintf(
		':root{--journal-primary:%1$s;--journal-primary-dark:%1$s;--journal-secondary:%2$s;--journal-paper:%3$s;--journal-card:%4$s;--journal-ink:%5$s;--journal-radius:%6$dpx;--journal-heading-font:%7$s;--journal-body-font:%8$s;}',
		esc_attr( $primary ),
		esc_attr( $secondary ),
		esc_attr( $paper ),
		esc_attr( $card ),
		esc_attr( $ink ),
		$radius,
		$heading_font,
		$body_font
	);
}

/**
 * Adds useful body classes for the selected layout.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function cozy_journal_body_classes( $classes ) {
	$classes[] = 'journal-layout-' . sanitize_html_class( get_theme_mod( 'cozy_journal_archive_layout', 'grid' ) );

	if ( get_theme_mod( 'cozy_journal_show_sidebar', true ) && is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'journal-has-sidebar';
	} else {
		$classes[] = 'journal-no-sidebar';
	}

	if ( get_theme_mod( 'cozy_journal_show_tape', true ) ) {
		$classes[] = 'journal-has-tape';
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
	return 34;
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

	return isset( $sets[ $selected ] ) ? $sets[ $selected ] : $sets['floral'];
}

require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/privacy.php';
require get_template_directory() . '/inc/theme-options.php';
require get_template_directory() . '/inc/frontend-editor.php';
require get_template_directory() . '/inc/customizer.php';
