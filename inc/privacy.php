<?php
/**
 * Privacy-conscious display defaults for historical public releases.
 *
 * @package Cozy_Journal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Removes comment-author website links unless the site owner enables them.
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
 * Adds privacy controls to the WordPress Customizer for historical versions.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function cozy_journal_register_privacy_customizer( $wp_customize ) {
	$section_id = 'cozy_journal_privacy_section';
	if ( ! $wp_customize->get_section( $section_id ) ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title'       => __( '隐私显示', 'cozy-journal' ),
				'description' => __( '控制主题是否公开显示 WordPress 用户名称、头像和作者网址。', 'cozy-journal' ),
				'panel'       => 'cozy_journal_theme_options',
				'priority'    => 95,
			)
		);
	}

	$settings = array(
		'cozy_journal_show_card_author' => array( __( '文章卡片显示作者', 'cozy-journal' ), false ),
		'cozy_journal_show_single_author' => array( __( '文章页显示作者', 'cozy-journal' ), false ),
		'cozy_journal_link_author_archive' => array( __( '作者名称链接到作者归档', 'cozy-journal' ), false ),
		'cozy_journal_show_comment_avatars' => array( __( '显示评论者头像', 'cozy-journal' ), false ),
		'cozy_journal_link_comment_author_website' => array( __( '评论者名称链接到个人网站', 'cozy-journal' ), false ),
	);

	if ( function_exists( 'cozy_journal_writing_desk_enabled' ) ) {
		$settings['cozy_journal_writing_show_user_name'] = array( __( '写作台显示当前用户名称', 'cozy-journal' ), false );
		$settings['cozy_journal_writing_show_user_avatar'] = array( __( '写作台显示当前用户头像', 'cozy-journal' ), false );
		$settings['cozy_journal_writing_show_lock_user_name'] = array( __( '编辑锁显示协作者名称', 'cozy-journal' ), false );
	}

	foreach ( $settings as $setting_id => $setting ) {
		if ( ! $wp_customize->get_setting( $setting_id ) ) {
			$wp_customize->add_setting(
				$setting_id,
				array(
					'default'           => $setting[1],
					'sanitize_callback' => 'cozy_journal_sanitize_checkbox',
				)
			);
		}

		if ( ! $wp_customize->get_control( $setting_id ) ) {
			$wp_customize->add_control(
				$setting_id,
				array(
					'label'   => $setting[0],
					'section' => $section_id,
					'type'    => 'checkbox',
				)
			);
		}
	}
}
add_action( 'customize_register', 'cozy_journal_register_privacy_customizer', 20 );
