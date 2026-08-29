<?php
/**
 * WordPress Customizer settings for Cozy Journal.
 *
 * @package Cozy_Journal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitizes checkbox values.
 *
 * @param mixed $checked Value to sanitize.
 * @return bool
 */
function cozy_journal_sanitize_checkbox( $checked ) {
	return (bool) $checked;
}

/**
 * Sanitizes select values against the control choices.
 *
 * @param string                $input   Selected value.
 * @param WP_Customize_Setting $setting Customizer setting.
 * @return string
 */
function cozy_journal_sanitize_select( $input, $setting ) {
	$input   = sanitize_key( $input );
	$control = $setting->manager->get_control( $setting->id );

	if ( $control && array_key_exists( $input, $control->choices ) ) {
		return $input;
	}

	return $setting->default;
}

/**
 * Sanitizes a number within a Customizer control range.
 *
 * @param mixed                 $input   Number to sanitize.
 * @param WP_Customize_Setting $setting Customizer setting.
 * @return int
 */
function cozy_journal_sanitize_range( $input, $setting ) {
	$input   = absint( $input );
	$control = $setting->manager->get_control( $setting->id );
	$minimum = isset( $control->input_attrs['min'] ) ? absint( $control->input_attrs['min'] ) : $input;
	$maximum = isset( $control->input_attrs['max'] ) ? absint( $control->input_attrs['max'] ) : $input;

	return min( $maximum, max( $minimum, $input ) );
}

/**
 * Validates the writing path without silently replacing conflicts.
 *
 * @param WP_Error             $validity Current validation result.
 * @param mixed                $value    Proposed path value.
 * @param WP_Customize_Setting $setting  Customizer setting instance.
 * @return WP_Error
 */
function cozy_journal_validate_writing_slug_customizer( $validity, $value, $setting ) {
	if ( ! function_exists( 'cozy_journal_validate_writing_slug' ) ) {
		return $validity;
	}

	$validated = cozy_journal_validate_writing_slug( $value );
	if ( is_wp_error( $validated ) ) {
		$validity->add( $validated->get_error_code(), $validated->get_error_message() );
	}

	return $validity;
}

/**
 * Registers all Cozy Journal options.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function cozy_journal_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => function() {
					bloginfo( 'name' );
				},
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => function() {
					bloginfo( 'description' );
				},
			)
		);
	}

	$wp_customize->add_panel(
		'cozy_journal_theme_options',
		array(
			'title'       => __( 'Cozy Journal 设置', 'cozy-journal' ),
			'description' => __( '把站点调成最符合你心情的手账本。', 'cozy-journal' ),
			'priority'    => 30,
		)
	);

	/* Homepage welcome card. */
	$wp_customize->add_section(
		'cozy_journal_hero_section',
		array(
			'title'       => __( '首页欢迎卡', 'cozy-journal' ),
			'description' => __( '这些内容会显示在首页顶部。', 'cozy-journal' ),
			'panel'       => 'cozy_journal_theme_options',
			'priority'    => 10,
		)
	);

	$wp_customize->add_setting(
		'cozy_journal_show_hero',
		array(
			'default'           => true,
			'sanitize_callback' => 'cozy_journal_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_show_hero',
		array(
			'label'   => __( '显示首页欢迎卡', 'cozy-journal' ),
			'section' => 'cozy_journal_hero_section',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'cozy_journal_show_hero_date',
		array(
			'default'           => true,
			'sanitize_callback' => 'cozy_journal_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_show_hero_date',
		array(
			'label'   => __( '显示欢迎卡日期', 'cozy-journal' ),
			'section' => 'cozy_journal_hero_section',
			'type'    => 'checkbox',
		)
	);

	$hero_text_settings = array(
		'cozy_journal_hero_eyebrow' => array(
			'label'   => __( '小标签文字', 'cozy-journal' ),
			'default' => __( '今天也要记录小确幸', 'cozy-journal' ),
			'type'    => 'text',
		),
		'cozy_journal_hero_title' => array(
			'label'   => __( '欢迎卡标题', 'cozy-journal' ),
			'default' => __( '把日子过成喜欢的样子', 'cozy-journal' ),
			'type'    => 'text',
		),
		'cozy_journal_hero_description' => array(
			'label'   => __( '欢迎卡说明', 'cozy-journal' ),
			'default' => __( '收藏平凡日子里的光，写下每一份柔软、认真和欢喜。', 'cozy-journal' ),
			'type'    => 'textarea',
		),
		'cozy_journal_hero_button_text' => array(
			'label'   => __( '按钮文字', 'cozy-journal' ),
			'default' => __( '翻开今天的手账', 'cozy-journal' ),
			'type'    => 'text',
		),
	);

	foreach ( $hero_text_settings as $setting_id => $args ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'textarea' === $args['type'] ? 'sanitize_textarea_field' : 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $args['label'],
				'section' => 'cozy_journal_hero_section',
				'type'    => $args['type'],
			)
		);
	}

	$wp_customize->add_setting(
		'cozy_journal_hero_button_url',
		array(
			'default'           => '#journal-latest',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_hero_button_url',
		array(
			'label'       => __( '按钮链接', 'cozy-journal' ),
			'description' => __( '可以填写完整网址，也可以填写 #journal-latest 跳到最新文章。', 'cozy-journal' ),
			'section'     => 'cozy_journal_hero_section',
			'type'        => 'text',
		)
	);

	/* Front-end writing desk. */
	$wp_customize->add_section(
		'cozy_journal_writing_section',
		array(
			'title'       => __( '前台写作', 'cozy-journal' ),
			'description' => __( '设置手账写作台路径、快捷入口和草稿自动保存。', 'cozy-journal' ),
			'panel'       => 'cozy_journal_theme_options',
			'priority'    => 15,
		)
	);

	$writing_toggles = array(
		'cozy_journal_enable_writing_desk' => array(
			'label'       => __( '启用前台手账写作台', 'cozy-journal' ),
			'description' => __( '拥有文章新建权限的登录用户可以访问设置的写作路径。', 'cozy-journal' ),
		),
		'cozy_journal_show_write_link' => array(
			'label'       => __( '在页头显示“写文章”', 'cozy-journal' ),
			'description' => __( '只对已登录并拥有文章新建权限的用户显示。', 'cozy-journal' ),
		),
		'cozy_journal_allow_featured_upload' => array(
			'label'       => __( '允许上传特色图片', 'cozy-journal' ),
			'description' => __( '当前账号仍需具备 WordPress 媒体上传权限。', 'cozy-journal' ),
		),
	);

	foreach ( $writing_toggles as $setting_id => $args ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => true,
				'sanitize_callback' => 'cozy_journal_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'       => $args['label'],
				'description' => $args['description'],
				'section'     => 'cozy_journal_writing_section',
				'type'        => 'checkbox',
			)
		);
	}

	$wp_customize->add_setting(
		'cozy_journal_writing_slug',
		array(
			'default'           => 'write',
			'sanitize_callback' => 'cozy_journal_sanitize_writing_slug',
			'validate_callback' => 'cozy_journal_validate_writing_slug_customizer',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_writing_slug',
		array(
			'label'       => __( '写作页面路径', 'cozy-journal' ),
			'description' => __( '只填写一段英文小写、数字、短横线或下划线；请勿与已有页面路径重复。', 'cozy-journal' ),
			'section'     => 'cozy_journal_writing_section',
			'type'        => 'text',
			'input_attrs' => array(
				'placeholder' => 'write',
				'maxlength'  => 60,
				'pattern'    => '[a-z0-9][a-z0-9_-]*',
			),
		)
	);

	$wp_customize->add_setting(
		'cozy_journal_writing_autosave_interval',
		array(
			'default'           => '30',
			'sanitize_callback' => 'cozy_journal_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_writing_autosave_interval',
		array(
			'label'       => __( '草稿自动保存间隔', 'cozy-journal' ),
			'description' => __( '只自动保存新文章、草稿和待审核文章。', 'cozy-journal' ),
			'section'     => 'cozy_journal_writing_section',
			'type'        => 'select',
			'choices'     => array(
				'30'  => __( '每 30 秒', 'cozy-journal' ),
				'60'  => __( '每 60 秒', 'cozy-journal' ),
				'120' => __( '每 2 分钟', 'cozy-journal' ),
				'0'   => __( '关闭自动保存', 'cozy-journal' ),
			),
		)
	);

	$wp_customize->add_setting(
		'cozy_journal_writing_intro',
		array(
			'default'           => __( '安静写下此刻，剩下的交给时间收藏。', 'cozy-journal' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_writing_intro',
		array(
			'label'       => __( '写作台顶部寄语', 'cozy-journal' ),
			'description' => __( '显示在“返回手账首页”下方。', 'cozy-journal' ),
			'section'     => 'cozy_journal_writing_section',
			'type'        => 'text',
		)
	);

	/* Colors and type. */
	$wp_customize->add_section(
		'cozy_journal_style_section',
		array(
			'title'    => __( '颜色与字体', 'cozy-journal' ),
			'panel'    => 'cozy_journal_theme_options',
			'priority' => 20,
		)
	);

	$color_settings = array(
		'cozy_journal_primary_color' => array(
			'label'   => __( '草莓主色', 'cozy-journal' ),
			'default' => '#d8757f',
		),
		'cozy_journal_secondary_color' => array(
			'label'   => __( '蜂蜜点缀色', 'cozy-journal' ),
			'default' => '#d9a441',
		),
		'cozy_journal_paper_color' => array(
			'label'   => __( '页面纸张色', 'cozy-journal' ),
			'default' => '#f7efe2',
		),
		'cozy_journal_card_color' => array(
			'label'   => __( '卡片底色', 'cozy-journal' ),
			'default' => '#fffdf8',
		),
		'cozy_journal_ink_color' => array(
			'label'   => __( '文字墨水色', 'cozy-journal' ),
			'default' => '#4f413b',
		),
	);

	foreach ( $color_settings as $setting_id => $args ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$setting_id,
				array(
					'label'   => $args['label'],
					'section' => 'cozy_journal_style_section',
				)
			)
		);
	}

	$wp_customize->add_setting(
		'cozy_journal_heading_font',
		array(
			'default'           => 'handwritten',
			'sanitize_callback' => 'cozy_journal_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_heading_font',
		array(
			'label'   => __( '标题字体气质', 'cozy-journal' ),
			'section' => 'cozy_journal_style_section',
			'type'    => 'select',
			'choices' => array(
				'handwritten' => __( '手写感', 'cozy-journal' ),
				'rounded'     => __( '圆润黑体', 'cozy-journal' ),
				'serif'       => __( '温柔宋体', 'cozy-journal' ),
			),
		)
	);

	$wp_customize->add_setting(
		'cozy_journal_body_font',
		array(
			'default'           => 'system',
			'sanitize_callback' => 'cozy_journal_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_body_font',
		array(
			'label'   => __( '正文字体气质', 'cozy-journal' ),
			'section' => 'cozy_journal_style_section',
			'type'    => 'select',
			'choices' => array(
				'system'  => __( '清爽易读', 'cozy-journal' ),
				'rounded' => __( '圆润可爱', 'cozy-journal' ),
				'serif'   => __( '书页宋体', 'cozy-journal' ),
			),
		)
	);

	$wp_customize->add_setting(
		'cozy_journal_card_radius',
		array(
			'default'           => 18,
			'sanitize_callback' => 'cozy_journal_sanitize_range',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_card_radius',
		array(
			'label'       => __( '卡片圆角', 'cozy-journal' ),
			'description' => __( '数值越大，卡片越圆润。', 'cozy-journal' ),
			'section'     => 'cozy_journal_style_section',
			'type'        => 'range',
			'input_attrs' => array(
				'min'  => 4,
				'max'  => 36,
				'step' => 1,
			),
		)
	);

	/* Content layout. */
	$wp_customize->add_section(
		'cozy_journal_layout_section',
		array(
			'title'    => __( '文章与布局', 'cozy-journal' ),
			'panel'    => 'cozy_journal_theme_options',
			'priority' => 30,
		)
	);

	$wp_customize->add_setting(
		'cozy_journal_archive_layout',
		array(
			'default'           => 'grid',
			'sanitize_callback' => 'cozy_journal_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_archive_layout',
		array(
			'label'   => __( '文章列表样式', 'cozy-journal' ),
			'section' => 'cozy_journal_layout_section',
			'type'    => 'radio',
			'choices' => array(
				'grid' => __( '双列拼贴', 'cozy-journal' ),
				'list' => __( '单列日记', 'cozy-journal' ),
			),
		)
	);

	$wp_customize->add_setting(
		'cozy_journal_home_posts_count',
		array(
			'default'           => 6,
			'sanitize_callback' => 'cozy_journal_sanitize_range',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_home_posts_count',
		array(
			'label'       => __( '静态首页最新文章数量', 'cozy-journal' ),
			'description' => __( '设置为静态首页时，在页面正文后显示的文章数量。', 'cozy-journal' ),
			'section'     => 'cozy_journal_layout_section',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 3,
				'max'  => 12,
				'step' => 1,
			),
		)
	);

	$layout_toggles = array(
		'cozy_journal_show_sidebar' => array(
			'label'   => __( '在内页显示侧栏', 'cozy-journal' ),
			'default' => true,
		),
		'cozy_journal_show_featured_images' => array(
			'label'   => __( '显示文章特色图片', 'cozy-journal' ),
			'default' => true,
		),
		'cozy_journal_show_excerpt' => array(
			'label'   => __( '在文章卡片显示摘要', 'cozy-journal' ),
			'default' => true,
		),
	);

	foreach ( $layout_toggles as $setting_id => $args ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'cozy_journal_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $args['label'],
				'section' => 'cozy_journal_layout_section',
				'type'    => 'checkbox',
			)
		);
	}

	/* Decorations. */
	$wp_customize->add_section(
		'cozy_journal_decorations_section',
		array(
			'title'       => __( '纸胶带与贴纸', 'cozy-journal' ),
			'description' => __( '控制手账感装饰；关闭后会得到更简洁的纸张风格。', 'cozy-journal' ),
			'panel'       => 'cozy_journal_theme_options',
			'priority'    => 40,
		)
	);

	$wp_customize->add_setting(
		'cozy_journal_show_tape',
		array(
			'default'           => true,
			'sanitize_callback' => 'cozy_journal_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_show_tape',
		array(
			'label'   => __( '显示半透明纸胶带', 'cozy-journal' ),
			'section' => 'cozy_journal_decorations_section',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'cozy_journal_show_stickers',
		array(
			'default'           => true,
			'sanitize_callback' => 'cozy_journal_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_show_stickers',
		array(
			'label'   => __( '显示欢迎卡贴纸', 'cozy-journal' ),
			'section' => 'cozy_journal_decorations_section',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'cozy_journal_sticker_style',
		array(
			'default'           => 'floral',
			'sanitize_callback' => 'cozy_journal_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_sticker_style',
		array(
			'label'   => __( '贴纸主题', 'cozy-journal' ),
			'section' => 'cozy_journal_decorations_section',
			'type'    => 'select',
			'choices' => array(
				'floral' => __( '花花草草', 'cozy-journal' ),
				'cozy'   => __( '暖暖宅家', 'cozy-journal' ),
				'sweets' => __( '草莓甜点', 'cozy-journal' ),
				'sky'    => __( '星月晚安', 'cozy-journal' ),
			),
		)
	);

	/* Footer. */
	$wp_customize->add_section(
		'cozy_journal_footer_section',
		array(
			'title'    => __( '页脚小纸条', 'cozy-journal' ),
			'panel'    => 'cozy_journal_theme_options',
			'priority' => 50,
		)
	);

	$wp_customize->add_setting(
		'cozy_journal_footer_text',
		array(
			'default'           => __( '愿每一次记录，都能接住生活里闪闪发亮的瞬间。', 'cozy-journal' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_footer_text',
		array(
			'label'   => __( '页脚寄语', 'cozy-journal' ),
			'section' => 'cozy_journal_footer_section',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'cozy_journal_show_footer_year',
		array(
			'default'           => true,
			'sanitize_callback' => 'cozy_journal_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'cozy_journal_show_footer_year',
		array(
			'label'   => __( '显示当前年份和站点名', 'cozy-journal' ),
			'section' => 'cozy_journal_footer_section',
			'type'    => 'checkbox',
		)
	);

	if ( isset( $wp_customize->selective_refresh ) ) {
		$partials = array(
			'cozy_journal_hero_eyebrow'     => '.hero-eyebrow',
			'cozy_journal_hero_title'       => '.hero-title',
			'cozy_journal_hero_description' => '.hero-description',
			'cozy_journal_hero_button_text' => '.hero-button',
			'cozy_journal_footer_text'      => '.footer-note-text',
		);

		foreach ( $partials as $setting_id => $selector ) {
			$wp_customize->selective_refresh->add_partial(
				$setting_id,
				array(
					'selector'        => $selector,
					'render_callback' => function() use ( $setting_id ) {
						echo esc_html( get_theme_mod( $setting_id ) );
					},
				)
			);
		}
	}
}
add_action( 'customize_register', 'cozy_journal_customize_register' );

/**
 * Enqueues live-preview behavior in the Customizer.
 */
function cozy_journal_customize_preview_js() {
	wp_enqueue_script(
		'cozy-journal-customizer-preview',
		get_template_directory_uri() . '/assets/js/customizer-preview.js',
		array( 'jquery', 'customize-preview' ),
		COZY_JOURNAL_VERSION,
		true
	);
}
add_action( 'customize_preview_init', 'cozy_journal_customize_preview_js' );
