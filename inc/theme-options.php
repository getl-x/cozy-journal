<?php
/**
 * Dedicated theme options center.
 *
 * @package Cozy_Journal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the editable option sections and their fields.
 *
 * The dedicated options center and the Customizer both store values as
 * theme modifications, so settings remain synchronized between the two UIs.
 *
 * @return array<string, array<string, mixed>>
 */
function cozy_journal_get_theme_option_sections() {
	return array(
		'initial' => array(
			'page'        => 'cozy-journal-settings-initial',
			'title'       => __( '初始设置', 'cozy-journal' ),
			'eyebrow'     => __( 'Quick start', 'cozy-journal' ),
			'description' => __( '控制首页的基础显示方式和文章数量。', 'cozy-journal' ),
			'icon'        => 'dashicons-admin-settings',
			'fields'      => array(
				'cozy_journal_show_hero' => array(
					'label'       => __( '显示首页欢迎卡', 'cozy-journal' ),
					'description' => __( '关闭后，首页会直接从最新文章区域开始。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => true,
					'group'       => __( '首页基础', 'cozy-journal' ),
				),
				'cozy_journal_show_hero_date' => array(
					'label'       => __( '显示欢迎卡日期', 'cozy-journal' ),
					'description' => __( '在欢迎卡顶部显示本地日期和星期。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => true,
					'group'       => __( '首页基础', 'cozy-journal' ),
				),
				'cozy_journal_home_posts_count' => array(
					'label'       => __( '首页最新文章数量', 'cozy-journal' ),
					'description' => __( '使用静态首页时，在页面内容下方显示多少篇最新文章。', 'cozy-journal' ),
					'type'        => 'number',
					'default'     => 6,
					'min'         => 3,
					'max'         => 12,
					'step'        => 1,
					'group'       => __( '首页基础', 'cozy-journal' ),
				),
			),
		),
		'writing' => array(
			'page'        => 'cozy-journal-settings-writing',
			'title'       => __( '前台写作', 'cozy-journal' ),
			'eyebrow'     => __( 'Writing desk', 'cozy-journal' ),
			'description' => __( '控制网站前台手账写作台、自动保存和快捷入口。', 'cozy-journal' ),
			'icon'        => 'dashicons-edit-page',
			'fields'      => array(
				'cozy_journal_enable_writing_desk' => array(
					'label'       => __( '启用前台手账写作台', 'cozy-journal' ),
					'description' => __( '启用后，拥有文章新建权限的登录用户可以访问下方设置的写作路径。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => true,
					'group'       => __( '入口与权限', 'cozy-journal' ),
				),
				'cozy_journal_writing_slug' => array(
					'label'       => __( '写作页面路径', 'cozy-journal' ),
					'description' => __( '只填写一段英文小写、数字、短横线或下划线，例如 journal-write。请勿与已有页面路径重复。', 'cozy-journal' ),
					'type'        => 'slug',
					'default'     => 'write',
					'group'       => __( '入口与权限', 'cozy-journal' ),
				),
				'cozy_journal_show_write_link' => array(
					'label'       => __( '在页头显示“写文章”', 'cozy-journal' ),
					'description' => __( '只对已登录并拥有文章新建权限的用户显示。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => true,
					'group'       => __( '入口与权限', 'cozy-journal' ),
				),
				'cozy_journal_allow_featured_upload' => array(
					'label'       => __( '允许上传特色图片', 'cozy-journal' ),
					'description' => __( '还需要当前账号具备 WordPress 媒体上传权限。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => true,
					'group'       => __( '入口与权限', 'cozy-journal' ),
				),
				'cozy_journal_writing_autosave_interval' => array(
					'label'       => __( '草稿自动保存间隔', 'cozy-journal' ),
					'description' => __( '自动保存只处理新文章、草稿和待审核文章，不会自动改动已发布文章。', 'cozy-journal' ),
					'type'        => 'select',
					'default'     => '30',
					'choices'     => array(
						'30'  => __( '每 30 秒', 'cozy-journal' ),
						'60'  => __( '每 60 秒', 'cozy-journal' ),
						'120' => __( '每 2 分钟', 'cozy-journal' ),
						'0'   => __( '关闭自动保存', 'cozy-journal' ),
					),
					'group'       => __( '写作体验', 'cozy-journal' ),
				),
				'cozy_journal_writing_intro' => array(
					'label'       => __( '写作台顶部寄语', 'cozy-journal' ),
					'description' => __( '显示在“返回手账首页”下方。', 'cozy-journal' ),
					'type'        => 'text',
					'default'     => __( '安静写下此刻，剩下的交给时间收藏。', 'cozy-journal' ),
					'group'       => __( '写作体验', 'cozy-journal' ),
				),
			),
		),
		'global' => array(
			'page'        => 'cozy-journal-settings-global',
			'title'       => __( '全局样式', 'cozy-journal' ),
			'eyebrow'     => __( 'Global style', 'cozy-journal' ),
			'description' => __( '统一设置整本手账的颜色、字体和卡片轮廓。', 'cozy-journal' ),
			'icon'        => 'dashicons-admin-appearance',
			'fields'      => array(
				'cozy_journal_primary_color' => array(
					'label'       => __( '草莓主色', 'cozy-journal' ),
					'description' => __( '用于按钮、链接、标签和主要强调元素。', 'cozy-journal' ),
					'type'        => 'color',
					'default'     => '#d8757f',
					'group'       => __( '颜色搭配', 'cozy-journal' ),
				),
				'cozy_journal_secondary_color' => array(
					'label'       => __( '蜂蜜点缀色', 'cozy-journal' ),
					'description' => __( '用于胶带、涂鸦和辅助装饰。', 'cozy-journal' ),
					'type'        => 'color',
					'default'     => '#d9a441',
					'group'       => __( '颜色搭配', 'cozy-journal' ),
				),
				'cozy_journal_paper_color' => array(
					'label'       => __( '页面纸张色', 'cozy-journal' ),
					'description' => __( '站点最外层的手账纸背景颜色。', 'cozy-journal' ),
					'type'        => 'color',
					'default'     => '#f7efe2',
					'group'       => __( '颜色搭配', 'cozy-journal' ),
				),
				'cozy_journal_card_color' => array(
					'label'       => __( '卡片底色', 'cozy-journal' ),
					'description' => __( '文章卡片、页头和正文纸张的背景颜色。', 'cozy-journal' ),
					'type'        => 'color',
					'default'     => '#fffdf8',
					'group'       => __( '颜色搭配', 'cozy-journal' ),
				),
				'cozy_journal_ink_color' => array(
					'label'       => __( '文字墨水色', 'cozy-journal' ),
					'description' => __( '标题和主要正文使用的颜色。', 'cozy-journal' ),
					'type'        => 'color',
					'default'     => '#4f413b',
					'group'       => __( '颜色搭配', 'cozy-journal' ),
				),
				'cozy_journal_heading_font' => array(
					'label'       => __( '标题字体气质', 'cozy-journal' ),
					'description' => __( '影响站点标题、文章标题和手写装饰文字。', 'cozy-journal' ),
					'type'        => 'select',
					'default'     => 'handwritten',
					'choices'     => array(
						'handwritten' => __( '手写感', 'cozy-journal' ),
						'rounded'     => __( '圆润黑体', 'cozy-journal' ),
						'serif'       => __( '温柔宋体', 'cozy-journal' ),
					),
					'group'       => __( '字体与卡片', 'cozy-journal' ),
				),
				'cozy_journal_body_font' => array(
					'label'       => __( '正文字体气质', 'cozy-journal' ),
					'description' => __( '影响文章正文、导航和说明文字。', 'cozy-journal' ),
					'type'        => 'select',
					'default'     => 'system',
					'choices'     => array(
						'system'  => __( '清爽易读', 'cozy-journal' ),
						'rounded' => __( '圆润可爱', 'cozy-journal' ),
						'serif'   => __( '书页宋体', 'cozy-journal' ),
					),
					'group'       => __( '字体与卡片', 'cozy-journal' ),
				),
				'cozy_journal_card_radius' => array(
					'label'       => __( '卡片圆角', 'cozy-journal' ),
					'description' => __( '数值越大，卡片边缘越圆润。', 'cozy-journal' ),
					'type'        => 'range',
					'default'     => 18,
					'min'         => 4,
					'max'         => 36,
					'step'        => 1,
					'unit'        => 'px',
					'group'       => __( '字体与卡片', 'cozy-journal' ),
				),
			),
		),
		'home' => array(
			'page'        => 'cozy-journal-settings-home',
			'title'       => __( '首页设置', 'cozy-journal' ),
			'eyebrow'     => __( 'Homepage', 'cozy-journal' ),
			'description' => __( '编辑首页欢迎卡里的文字和行动按钮。', 'cozy-journal' ),
			'icon'        => 'dashicons-admin-home',
			'fields'      => array(
				'cozy_journal_hero_eyebrow' => array(
					'label'       => __( '小标签文字', 'cozy-journal' ),
					'description' => __( '显示在首页主标题上方的小句子。', 'cozy-journal' ),
					'type'        => 'text',
					'default'     => __( '今天也要记录小确幸', 'cozy-journal' ),
					'group'       => __( '欢迎文字', 'cozy-journal' ),
				),
				'cozy_journal_hero_title' => array(
					'label'       => __( '欢迎卡标题', 'cozy-journal' ),
					'description' => __( '首页视觉中心的大标题。', 'cozy-journal' ),
					'type'        => 'text',
					'default'     => __( '把日子过成喜欢的样子', 'cozy-journal' ),
					'group'       => __( '欢迎文字', 'cozy-journal' ),
				),
				'cozy_journal_hero_description' => array(
					'label'       => __( '欢迎卡说明', 'cozy-journal' ),
					'description' => __( '适合写一句关于博客内容或生活态度的介绍。', 'cozy-journal' ),
					'type'        => 'textarea',
					'default'     => __( '收藏平凡日子里的光，写下每一份柔软、认真和欢喜。', 'cozy-journal' ),
					'group'       => __( '欢迎文字', 'cozy-journal' ),
				),
				'cozy_journal_hero_button_text' => array(
					'label'       => __( '按钮文字', 'cozy-journal' ),
					'description' => __( '留空时不显示欢迎卡按钮。', 'cozy-journal' ),
					'type'        => 'text',
					'default'     => __( '翻开今天的手账', 'cozy-journal' ),
					'group'       => __( '行动按钮', 'cozy-journal' ),
				),
				'cozy_journal_hero_button_url' => array(
					'label'       => __( '按钮链接', 'cozy-journal' ),
					'description' => __( '可填写完整网址或 #journal-latest。', 'cozy-journal' ),
					'type'        => 'url',
					'default'     => '#journal-latest',
					'group'       => __( '行动按钮', 'cozy-journal' ),
				),
			),
		),
		'content' => array(
			'page'        => 'cozy-journal-settings-content',
			'title'       => __( '文章与页面', 'cozy-journal' ),
			'eyebrow'     => __( 'Content', 'cozy-journal' ),
			'description' => __( '控制文章卡片、内页侧栏和特色图片的显示。', 'cozy-journal' ),
			'icon'        => 'dashicons-media-document',
			'fields'      => array(
				'cozy_journal_archive_layout' => array(
					'label'       => __( '文章列表样式', 'cozy-journal' ),
					'description' => __( '选择双列拼贴或单列日记布局。', 'cozy-journal' ),
					'type'        => 'select',
					'default'     => 'grid',
					'choices'     => array(
						'grid' => __( '双列拼贴', 'cozy-journal' ),
						'list' => __( '单列日记', 'cozy-journal' ),
					),
					'group'       => __( '文章列表', 'cozy-journal' ),
				),
				'cozy_journal_show_excerpt' => array(
					'label'       => __( '显示文章摘要', 'cozy-journal' ),
					'description' => __( '在首页和归档文章卡片里显示摘要。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => true,
					'group'       => __( '文章列表', 'cozy-journal' ),
				),
				'cozy_journal_show_sidebar' => array(
					'label'       => __( '显示内页侧栏', 'cozy-journal' ),
					'description' => __( '仅在已经添加侧栏小工具时显示。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => true,
					'group'       => __( '内页显示', 'cozy-journal' ),
				),
				'cozy_journal_show_featured_images' => array(
					'label'       => __( '显示文章特色图片', 'cozy-journal' ),
					'description' => __( '同时影响文章卡片、文章页和独立页面。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => true,
					'group'       => __( '内页显示', 'cozy-journal' ),
				),
			),
		),
		'decorations' => array(
			'page'        => 'cozy-journal-settings-decorations',
			'title'       => __( '手账装饰', 'cozy-journal' ),
			'eyebrow'     => __( 'Decorations', 'cozy-journal' ),
			'description' => __( '调整纸胶带、欢迎卡贴纸和页脚小纸条。', 'cozy-journal' ),
			'icon'        => 'dashicons-art',
			'fields'      => array(
				'cozy_journal_show_tape' => array(
					'label'       => __( '显示半透明纸胶带', 'cozy-journal' ),
					'description' => __( '显示在欢迎卡、文章卡片、正文和页脚上。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => true,
					'group'       => __( '纸胶带与贴纸', 'cozy-journal' ),
				),
				'cozy_journal_show_stickers' => array(
					'label'       => __( '显示欢迎卡贴纸', 'cozy-journal' ),
					'description' => __( '关闭后欢迎卡会更加简洁。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => true,
					'group'       => __( '纸胶带与贴纸', 'cozy-journal' ),
				),
				'cozy_journal_sticker_style' => array(
					'label'       => __( '贴纸主题', 'cozy-journal' ),
					'description' => __( '选择最符合当前心情的一套贴纸。', 'cozy-journal' ),
					'type'        => 'select',
					'default'     => 'floral',
					'choices'     => array(
						'floral' => __( '花花草草', 'cozy-journal' ),
						'cozy'   => __( '暖暖宅家', 'cozy-journal' ),
						'sweets' => __( '草莓甜点', 'cozy-journal' ),
						'sky'    => __( '星月晚安', 'cozy-journal' ),
					),
					'group'       => __( '纸胶带与贴纸', 'cozy-journal' ),
				),
				'cozy_journal_footer_text' => array(
					'label'       => __( '页脚寄语', 'cozy-journal' ),
					'description' => __( '显示在网站最下方的小纸条上。', 'cozy-journal' ),
					'type'        => 'text',
					'default'     => __( '愿每一次记录，都能接住生活里闪闪发亮的瞬间。', 'cozy-journal' ),
					'group'       => __( '页脚小纸条', 'cozy-journal' ),
				),
				'cozy_journal_show_footer_year' => array(
					'label'       => __( '显示年份与站点名称', 'cozy-journal' ),
					'description' => __( '在页脚寄语下面显示当前年份和站点名。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => true,
					'group'       => __( '页脚小纸条', 'cozy-journal' ),
				),
			),
		),
		'privacy' => array(
			'page'        => 'cozy-journal-settings-privacy',
			'title'       => __( '隐私显示', 'cozy-journal' ),
			'eyebrow'     => __( 'Privacy', 'cozy-journal' ),
			'description' => __( '控制主题是否公开显示 WordPress 用户名称、头像和作者网址。', 'cozy-journal' ),
			'icon'        => 'dashicons-privacy',
			'fields'      => array(
				'cozy_journal_show_card_author' => array(
					'label'       => __( '文章卡片显示作者', 'cozy-journal' ),
					'description' => __( '新安装默认关闭；多作者站点可以开启。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => false,
					'group'       => __( '文章作者', 'cozy-journal' ),
				),
				'cozy_journal_show_single_author' => array(
					'label'       => __( '文章页显示作者', 'cozy-journal' ),
					'description' => __( '新安装默认关闭；多作者站点可以开启。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => false,
					'group'       => __( '文章作者', 'cozy-journal' ),
				),
				'cozy_journal_link_author_archive' => array(
					'label'       => __( '作者名称链接到作者归档', 'cozy-journal' ),
					'description' => __( '默认关闭，避免自动生成可能包含账户特征的作者归档网址。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => false,
					'group'       => __( '文章作者', 'cozy-journal' ),
				),
				'cozy_journal_show_comment_avatars' => array(
					'label'       => __( '显示评论者头像', 'cozy-journal' ),
					'description' => __( '默认关闭，避免页面和截图带出头像或触发头像服务请求。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => false,
					'group'       => __( '评论身份', 'cozy-journal' ),
				),
				'cozy_journal_link_comment_author_website' => array(
					'label'       => __( '评论者名称链接到个人网站', 'cozy-journal' ),
					'description' => __( '默认关闭，评论者名称仍会显示，但不会链接其填写的网址。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => false,
					'group'       => __( '评论身份', 'cozy-journal' ),
				),

				'cozy_journal_writing_show_user_name' => array(
					'label'       => __( '写作台显示当前用户名称', 'cozy-journal' ),
					'description' => __( '默认关闭；开启后显示当前登录用户的 WordPress 公开显示名称。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => false,
					'group'       => __( '前台写作身份', 'cozy-journal' ),
				),
				'cozy_journal_writing_show_user_avatar' => array(
					'label'       => __( '写作台显示当前用户头像', 'cozy-journal' ),
					'description' => __( '默认关闭，避免截图带出头像或触发头像服务请求。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => false,
					'group'       => __( '前台写作身份', 'cozy-journal' ),
				),
				'cozy_journal_writing_show_lock_user_name' => array(
					'label'       => __( '编辑锁显示协作者名称', 'cozy-journal' ),
					'description' => __( '默认关闭，统一使用“另一位用户”作为冲突提示。', 'cozy-journal' ),
					'type'        => 'checkbox',
					'default'     => false,
					'group'       => __( '前台写作身份', 'cozy-journal' ),
				),
			),
		),

	);
}

/**
 * Returns every editable field keyed by theme mod name.
 *
 * @return array<string, array<string, mixed>>
 */
function cozy_journal_get_all_option_fields() {
	$fields = array();

	foreach ( cozy_journal_get_theme_option_sections() as $section ) {
		$fields = array_merge( $fields, $section['fields'] );
	}

	return $fields;
}

/**
 * Returns navigation items for the settings center.
 *
 * @return array<string, array<string, string>>
 */
function cozy_journal_get_admin_navigation() {
	$navigation = array(
		'welcome' => array(
			'page'  => 'cozy-journal-settings',
			'title' => __( '你好！', 'cozy-journal' ),
			'icon'  => 'dashicons-smiley',
		),
	);

	foreach ( cozy_journal_get_theme_option_sections() as $key => $section ) {
		$navigation[ $key ] = array(
			'page'  => $section['page'],
			'title' => $section['title'],
			'icon'  => $section['icon'],
		);
	}

	$navigation['backup'] = array(
		'page'  => 'cozy-journal-settings-backup',
		'title' => __( '备份与恢复', 'cozy-journal' ),
		'icon'  => 'dashicons-shield',
	);
	$navigation['about'] = array(
		'page'  => 'cozy-journal-settings-about',
		'title' => __( '关于主题', 'cozy-journal' ),
		'icon'  => 'dashicons-heart',
	);

	return $navigation;
}

/**
 * Registers a dedicated top-level admin menu and its submenus.
 */
function cozy_journal_register_options_menu() {
	add_menu_page(
		__( 'Cozy Journal 主题设置', 'cozy-journal' ),
		__( '手账主题设置', 'cozy-journal' ),
		'edit_theme_options',
		'cozy-journal-settings',
		'cozy_journal_render_options_page',
		'dashicons-book-alt',
		61
	);

	foreach ( cozy_journal_get_admin_navigation() as $item ) {
		add_submenu_page(
			'cozy-journal-settings',
			sprintf( '%1$s · %2$s', $item['title'], __( 'Cozy Journal', 'cozy-journal' ) ),
			$item['title'],
			'edit_theme_options',
			$item['page'],
			'cozy_journal_render_options_page'
		);
	}
}
add_action( 'admin_menu', 'cozy_journal_register_options_menu' );

/**
 * Loads styles and scripts only on Cozy Journal admin pages.
 *
 * @param string $hook_suffix Current admin page hook.
 */
function cozy_journal_options_admin_assets( $hook_suffix ) {
	if ( false === strpos( $hook_suffix, 'cozy-journal-settings' ) ) {
		return;
	}

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style(
		'cozy-journal-admin-options',
		get_template_directory_uri() . '/assets/css/admin-options.css',
		array( 'wp-color-picker' ),
		COZY_JOURNAL_VERSION
	);
	wp_enqueue_script(
		'cozy-journal-admin-options',
		get_template_directory_uri() . '/assets/js/admin-options.js',
		array( 'jquery', 'wp-color-picker' ),
		COZY_JOURNAL_VERSION,
		true
	);
	wp_localize_script(
		'cozy-journal-admin-options',
		'cozyJournalAdmin',
		array(
			'chooseBackup' => __( '请选择一个 Cozy Journal JSON 备份或样式模板。', 'cozy-journal' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'cozy_journal_options_admin_assets' );

/**
 * Resolves the current settings page to a navigation key.
 *
 * @return string
 */
function cozy_journal_get_current_admin_section() {
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : 'cozy-journal-settings'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	foreach ( cozy_journal_get_admin_navigation() as $key => $item ) {
		if ( $page === $item['page'] ) {
			return $key;
		}
	}

	return 'welcome';
}

/**
 * Sanitizes one theme option using its field schema.
 *
 * @param mixed $value Raw value.
 * @param array $field Field definition.
 * @return mixed
 */
function cozy_journal_sanitize_theme_option( $value, $field ) {
	$type    = isset( $field['type'] ) ? $field['type'] : 'text';
	$default = isset( $field['default'] ) ? $field['default'] : '';

	switch ( $type ) {
		case 'checkbox':
			if ( is_bool( $value ) ) {
				return $value;
			}

			return in_array( $value, array( 1, '1', 'true', 'yes', 'on' ), true );

		case 'color':
			$color = sanitize_hex_color( $value );
			return $color ? $color : $default;

		case 'number':
		case 'range':
			$number  = absint( $value );
			$minimum = isset( $field['min'] ) ? absint( $field['min'] ) : $number;
			$maximum = isset( $field['max'] ) ? absint( $field['max'] ) : $number;
			return min( $maximum, max( $minimum, $number ) );

		case 'select':
			$value = sanitize_key( $value );
			return isset( $field['choices'][ $value ] ) ? $value : $default;

		case 'textarea':
			return sanitize_textarea_field( $value );

		case 'url':
			return esc_url_raw( $value );

		case 'slug':
			return function_exists( 'cozy_journal_sanitize_writing_slug' )
				? cozy_journal_sanitize_writing_slug( $value )
				: $default;

		default:
			return sanitize_text_field( $value );
	}
}

/**
 * Redirects back to a theme settings page with a status code.
 *
 * @param string $page   Admin page slug.
 * @param string $notice Notice code.
 * @param string $error  Optional error code.
 */
function cozy_journal_options_redirect( $page, $notice = '', $error = '' ) {
	$allowed_pages = wp_list_pluck( cozy_journal_get_admin_navigation(), 'page' );
	if ( ! in_array( $page, $allowed_pages, true ) ) {
		$page = 'cozy-journal-settings';
	}

	$args = array( 'page' => $page );
	if ( $notice ) {
		$args['cozy_journal_notice'] = sanitize_key( $notice );
	}
	if ( $error ) {
		$args['cozy_journal_error'] = sanitize_key( $error );
	}

	wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
	exit;
}

/**
 * Saves one section of theme modifications.
 */
function cozy_journal_save_options() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( '你没有权限修改主题设置。', 'cozy-journal' ) );
	}

	$section_key = isset( $_POST['section'] ) ? sanitize_key( wp_unslash( $_POST['section'] ) ) : '';
	$sections    = cozy_journal_get_theme_option_sections();

	if ( ! isset( $sections[ $section_key ] ) ) {
		wp_die( esc_html__( '无法识别要保存的设置分区。', 'cozy-journal' ) );
	}

	check_admin_referer( 'cozy_journal_save_' . $section_key );

	$submitted = isset( $_POST['cozy_journal'] ) && is_array( $_POST['cozy_journal'] )
		? wp_unslash( $_POST['cozy_journal'] )
		: array();

	$sanitized_settings = array();
	foreach ( $sections[ $section_key ]['fields'] as $setting_id => $field ) {
		if ( 'checkbox' === $field['type'] ) {
			$value = isset( $submitted[ $setting_id ] );
		} elseif ( array_key_exists( $setting_id, $submitted ) ) {
			$value = $submitted[ $setting_id ];
		} else {
			continue;
		}

		if ( 'cozy_journal_writing_slug' === $setting_id && function_exists( 'cozy_journal_validate_writing_slug' ) ) {
			$validated_slug = cozy_journal_validate_writing_slug( $value );
			if ( is_wp_error( $validated_slug ) ) {
				cozy_journal_options_redirect( $sections[ $section_key ]['page'], '', $validated_slug->get_error_code() );
			}

			$value = $validated_slug;
		}

		$sanitized_settings[ $setting_id ] = cozy_journal_sanitize_theme_option( $value, $field );
	}

	foreach ( $sanitized_settings as $setting_id => $value ) {
		set_theme_mod( $setting_id, $value );
	}

	cozy_journal_options_redirect( $sections[ $section_key ]['page'], 'saved' );
}
add_action( 'admin_post_cozy_journal_save_options', 'cozy_journal_save_options' );

/**
 * Resets one section or every Cozy Journal theme modification.
 */
function cozy_journal_reset_options() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( '你没有权限重置主题设置。', 'cozy-journal' ) );
	}

	check_admin_referer( 'cozy_journal_reset_options' );

	$mode         = isset( $_POST['reset_mode'] ) ? sanitize_key( wp_unslash( $_POST['reset_mode'] ) ) : 'section';
	$section_key  = isset( $_POST['section'] ) ? sanitize_key( wp_unslash( $_POST['section'] ) ) : '';
	$return_page  = isset( $_POST['return_page'] ) ? sanitize_key( wp_unslash( $_POST['return_page'] ) ) : 'cozy-journal-settings';
	$sections     = cozy_journal_get_theme_option_sections();
	$reset_fields = 'all' === $mode
		? cozy_journal_get_all_option_fields()
		: ( isset( $sections[ $section_key ] ) ? $sections[ $section_key ]['fields'] : array() );

	if ( isset( $reset_fields['cozy_journal_writing_slug'] ) && function_exists( 'cozy_journal_validate_writing_slug' ) ) {
		$validated_slug = cozy_journal_validate_writing_slug( $reset_fields['cozy_journal_writing_slug']['default'] );
		if ( is_wp_error( $validated_slug ) ) {
			cozy_journal_options_redirect( 'all' === $mode ? 'cozy-journal-settings-backup' : $return_page, '', $validated_slug->get_error_code() );
		}
	}

	if ( 'all' === $mode ) {
		foreach ( $reset_fields as $setting_id => $field ) {
			remove_theme_mod( $setting_id );
		}
		cozy_journal_options_redirect( 'cozy-journal-settings-backup', 'reset_all' );
	}

	if ( ! isset( $sections[ $section_key ] ) ) {
		wp_die( esc_html__( '无法识别要重置的设置分区。', 'cozy-journal' ) );
	}

	foreach ( $sections[ $section_key ]['fields'] as $setting_id => $field ) {
		remove_theme_mod( $setting_id );
	}

	cozy_journal_options_redirect( $return_page, 'reset_section' );
}
add_action( 'admin_post_cozy_journal_reset_options', 'cozy_journal_reset_options' );

/**
 * Returns fields that are safe to include in a publicly shared style template.
 *
 * Free text, paths and URLs are intentionally excluded because they may contain
 * names, private routes, domains or other site-specific information.
 *
 * @return array<string, array<string, mixed>>
 */
function cozy_journal_get_shareable_option_fields() {
	$safe_types = array( 'checkbox', 'color', 'number', 'range', 'select' );
	$fields     = array();

	foreach ( cozy_journal_get_all_option_fields() as $setting_id => $field ) {
		if ( in_array( $field['type'], $safe_types, true ) ) {
			$fields[ $setting_id ] = $field;
		}
	}

	return $fields;
}

/**
 * Sends a JSON export without embedding a local operation time.
 *
 * @param array<string, array<string, mixed>> $fields      Fields to include.
 * @param string                              $filename    Download filename.
 * @param string                              $export_type Export scope marker.
 */
function cozy_journal_send_options_export( $fields, $filename, $export_type ) {
	$settings = array();
	foreach ( $fields as $setting_id => $field ) {
		$settings[ $setting_id ] = get_theme_mod( $setting_id, $field['default'] );
	}

	$payload = array(
		'theme'       => 'cozy-journal',
		'version'     => COZY_JOURNAL_VERSION,
		'export_type' => $export_type,
		'settings'    => $settings,
	);

	nocache_headers();
	header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit;
}

/**
 * Downloads a complete private backup that may contain site-specific data.
 */
function cozy_journal_export_options() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( '你没有权限导出主题设置。', 'cozy-journal' ) );
	}

	check_admin_referer( 'cozy_journal_export_options' );

	cozy_journal_send_options_export(
		cozy_journal_get_all_option_fields(),
		'cozy-journal-private-backup-v' . COZY_JOURNAL_VERSION . '.json',
		'private-backup'
	);
}
add_action( 'admin_post_cozy_journal_export_options', 'cozy_journal_export_options' );

/**
 * Downloads a sanitized layout and style template suitable for sharing.
 */
function cozy_journal_export_style_template() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( '你没有权限导出主题样式模板。', 'cozy-journal' ) );
	}

	check_admin_referer( 'cozy_journal_export_style_template' );

	cozy_journal_send_options_export(
		cozy_journal_get_shareable_option_fields(),
		'cozy-journal-style-template-v' . COZY_JOURNAL_VERSION . '.json',
		'style-template'
	);
}
add_action( 'admin_post_cozy_journal_export_style_template', 'cozy_journal_export_style_template' );

/**
 * Imports a JSON settings backup.
 */
function cozy_journal_import_options() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( '你没有权限导入主题设置。', 'cozy-journal' ) );
	}

	check_admin_referer( 'cozy_journal_import_options' );

	if ( empty( $_FILES['cozy_journal_backup']['tmp_name'] ) || UPLOAD_ERR_OK !== (int) $_FILES['cozy_journal_backup']['error'] ) {
		cozy_journal_options_redirect( 'cozy-journal-settings-backup', '', 'missing_file' );
	}

	if ( (int) $_FILES['cozy_journal_backup']['size'] > 1048576 ) {
		cozy_journal_options_redirect( 'cozy-journal-settings-backup', '', 'file_too_large' );
	}

	$contents = file_get_contents( $_FILES['cozy_journal_backup']['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$payload  = json_decode( $contents, true );

	if ( ! is_array( $payload ) || 'cozy-journal' !== ( isset( $payload['theme'] ) ? $payload['theme'] : '' ) || ! isset( $payload['settings'] ) || ! is_array( $payload['settings'] ) ) {
		cozy_journal_options_redirect( 'cozy-journal-settings-backup', '', 'invalid_backup' );
	}

	$allowed_fields     = cozy_journal_get_all_option_fields();
	$sanitized_settings = array();
	foreach ( $allowed_fields as $setting_id => $field ) {
		if ( array_key_exists( $setting_id, $payload['settings'] ) ) {
			$value = $payload['settings'][ $setting_id ];

			if ( 'cozy_journal_writing_slug' === $setting_id && function_exists( 'cozy_journal_validate_writing_slug' ) ) {
				$validated_slug = cozy_journal_validate_writing_slug( $value );
				if ( is_wp_error( $validated_slug ) ) {
					cozy_journal_options_redirect( 'cozy-journal-settings-backup', '', $validated_slug->get_error_code() );
				}

				$value = $validated_slug;
			}

			$sanitized_settings[ $setting_id ] = cozy_journal_sanitize_theme_option( $value, $field );
		}
	}

	foreach ( $sanitized_settings as $setting_id => $value ) {
		set_theme_mod( $setting_id, $value );
	}

	cozy_journal_options_redirect( 'cozy-journal-settings-backup', 'imported' );
}
add_action( 'admin_post_cozy_journal_import_options', 'cozy_journal_import_options' );

/**
 * Prints a notice inside the settings center.
 */
function cozy_journal_render_options_notice() {
	$notice = isset( $_GET['cozy_journal_notice'] ) ? sanitize_key( wp_unslash( $_GET['cozy_journal_notice'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$error  = isset( $_GET['cozy_journal_error'] ) ? sanitize_key( wp_unslash( $_GET['cozy_journal_error'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$messages = array(
		'saved'         => __( '设置已经保存，前台会立即使用新的样式。', 'cozy-journal' ),
		'reset_section' => __( '当前分区已经恢复为主题默认值。', 'cozy-journal' ),
		'reset_all'     => __( '全部 Cozy Journal 设置已经恢复为默认值。', 'cozy-journal' ),
		'imported'      => __( '备份导入成功，设置已经恢复。', 'cozy-journal' ),
	);
	$errors = array(
		'missing_file'          => __( '没有收到可用的备份文件，请重新选择。', 'cozy-journal' ),
		'file_too_large'        => __( '备份文件超过 1MB，已拒绝导入。', 'cozy-journal' ),
		'invalid_backup'        => __( '这个文件不是有效的 Cozy Journal 设置备份或样式模板。', 'cozy-journal' ),
		'invalid_writing_slug'  => __( '写作页面路径格式无效，请使用英文小写字母、数字、短横线或下划线。', 'cozy-journal' ),
		'writing_slug_conflict' => __( '写作页面路径与现有页面、归档或 WordPress 系统路径冲突，请换一个名称。', 'cozy-journal' ),
	);

	if ( isset( $messages[ $notice ] ) ) {
		printf( '<div class="cj-notice cj-notice-success"><span class="dashicons dashicons-yes-alt"></span><p>%s</p></div>', esc_html( $messages[ $notice ] ) );
	}

	if ( isset( $errors[ $error ] ) ) {
		printf( '<div class="cj-notice cj-notice-error"><span class="dashicons dashicons-warning"></span><p>%s</p></div>', esc_html( $errors[ $error ] ) );
	}
}

/**
 * Renders one editable field.
 *
 * @param string $setting_id Theme mod name.
 * @param array  $field      Field definition.
 */
function cozy_journal_render_option_field( $setting_id, $field ) {
	$value       = get_theme_mod( $setting_id, $field['default'] );
	$type        = $field['type'];
	$field_name  = 'cozy_journal[' . $setting_id . ']';
	$field_id    = 'cozy-journal-' . str_replace( '_', '-', $setting_id );
	$description = isset( $field['description'] ) ? $field['description'] : '';
	?>
	<div class="cj-option-row cj-option-type-<?php echo esc_attr( $type ); ?>">
		<div class="cj-option-copy">
			<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
			<?php if ( $description ) : ?>
				<p><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>
		<div class="cj-option-control">
			<?php if ( 'checkbox' === $type ) : ?>
				<label class="cj-switch" for="<?php echo esc_attr( $field_id ); ?>">
					<input id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" type="checkbox" value="1" <?php checked( (bool) $value ); ?>>
					<span class="cj-switch-track" aria-hidden="true"><span class="cj-switch-thumb"></span></span>
					<span class="cj-switch-label" data-on="<?php esc_attr_e( '开', 'cozy-journal' ); ?>" data-off="<?php esc_attr_e( '关', 'cozy-journal' ); ?>"><?php echo $value ? esc_html__( '开', 'cozy-journal' ) : esc_html__( '关', 'cozy-journal' ); ?></span>
				</label>
			<?php elseif ( 'color' === $type ) : ?>
				<input id="<?php echo esc_attr( $field_id ); ?>" class="cozy-journal-color-field" name="<?php echo esc_attr( $field_name ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" data-default-color="<?php echo esc_attr( $field['default'] ); ?>">
			<?php elseif ( 'select' === $type ) : ?>
				<select id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>">
					<?php foreach ( $field['choices'] as $choice_value => $choice_label ) : ?>
						<option value="<?php echo esc_attr( $choice_value ); ?>" <?php selected( $value, $choice_value ); ?>><?php echo esc_html( $choice_label ); ?></option>
					<?php endforeach; ?>
				</select>
			<?php elseif ( 'textarea' === $type ) : ?>
				<textarea id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" rows="4"><?php echo esc_textarea( $value ); ?></textarea>
			<?php elseif ( 'slug' === $type ) : ?>
				<div class="cj-path-control">
					<span aria-hidden="true">/</span>
					<input id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" maxlength="60" pattern="[a-z0-9][a-z0-9_-]*" placeholder="write" autocomplete="off" spellcheck="false">
					<span aria-hidden="true">/</span>
				</div>
				<?php if ( function_exists( 'cozy_journal_get_write_url' ) ) : ?>
					<p class="cj-path-preview"><span><?php esc_html_e( '当前地址', 'cozy-journal' ); ?></span><code><?php echo esc_html( cozy_journal_get_write_url() ); ?></code></p>
				<?php endif; ?>
			<?php elseif ( 'range' === $type ) : ?>
				<div class="cj-range-control">
					<input id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" type="range" value="<?php echo esc_attr( $value ); ?>" min="<?php echo esc_attr( $field['min'] ); ?>" max="<?php echo esc_attr( $field['max'] ); ?>" step="<?php echo esc_attr( $field['step'] ); ?>">
					<output for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $value . ( isset( $field['unit'] ) ? $field['unit'] : '' ) ); ?></output>
				</div>
			<?php elseif ( 'number' === $type ) : ?>
				<input id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" type="number" value="<?php echo esc_attr( $value ); ?>" min="<?php echo esc_attr( $field['min'] ); ?>" max="<?php echo esc_attr( $field['max'] ); ?>" step="<?php echo esc_attr( $field['step'] ); ?>">
			<?php else : ?>
				<input id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>">
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Renders the selected editable section.
 *
 * @param string $section_key Section key.
 * @param array  $section     Section schema.
 */
function cozy_journal_render_editable_section( $section_key, $section ) {
	$current_group = '';
	?>
	<form id="cozy-journal-settings-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="cozy_journal_save_options">
		<input type="hidden" name="section" value="<?php echo esc_attr( $section_key ); ?>">
		<?php wp_nonce_field( 'cozy_journal_save_' . $section_key ); ?>

		<div class="cj-section-heading">
			<p><?php echo esc_html( $section['eyebrow'] ); ?></p>
			<h2><?php echo esc_html( $section['title'] ); ?></h2>
			<span><?php echo esc_html( $section['description'] ); ?></span>
		</div>

		<?php foreach ( $section['fields'] as $setting_id => $field ) : ?>
			<?php
			$group = isset( $field['group'] ) ? $field['group'] : '';
			if ( $group !== $current_group ) :
				$current_group = $group;
				?>
				<div class="cj-setting-group-title"><span><?php echo esc_html( $group ); ?></span></div>
			<?php endif; ?>
			<?php cozy_journal_render_option_field( $setting_id, $field ); ?>
		<?php endforeach; ?>

		<div class="cj-form-footer">
			<button class="button button-primary cj-primary-button" type="submit"><span class="dashicons dashicons-saved"></span><?php esc_html_e( '保存本页设置', 'cozy-journal' ); ?></button>
			<span><?php esc_html_e( '保存后无需刷新缓存，前台会直接读取新设置。', 'cozy-journal' ); ?></span>
		</div>
	</form>
	<?php
}

/**
 * Renders the welcome dashboard.
 */
function cozy_journal_render_welcome_panel() {
	$sections = cozy_journal_get_theme_option_sections();
	?>
	<div class="cj-welcome-hero">
		<div class="cj-welcome-copy">
			<span class="cj-kicker"><?php esc_html_e( 'Welcome to Cozy Journal', 'cozy-journal' ); ?></span>
			<h2><?php esc_html_e( '你好！', 'cozy-journal' ); ?></h2>
			<p><?php esc_html_e( '感谢使用 Cozy Journal。这里可以集中调整整本手账的颜色、首页、文章布局和装饰，也可以随时备份自己的搭配。', 'cozy-journal' ); ?></p>
			<div class="cj-welcome-actions">
				<a class="button button-primary cj-primary-button" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $sections['initial']['page'] ) ); ?>"><?php esc_html_e( '开始设置', 'cozy-journal' ); ?></a>
				<?php if ( function_exists( 'cozy_journal_get_write_url' ) && cozy_journal_writing_desk_enabled() && cozy_journal_current_user_can_create_writing_posts() ) : ?>
					<a class="button cj-secondary-button" href="<?php echo esc_url( cozy_journal_get_write_url() ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-edit-page"></span><?php esc_html_e( '打开写作台', 'cozy-journal' ); ?></a>
				<?php endif; ?>
				<a class="button cj-secondary-button" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( '查看网站', 'cozy-journal' ); ?><span class="dashicons dashicons-external"></span></a>
			</div>
		</div>
		<div class="cj-welcome-art" aria-hidden="true">
			<div class="cj-admin-tape"></div>
			<div class="cj-mini-polaroid"><span></span><i></i><b>Cozy day</b></div>
			<div class="cj-flower-sticker">✿</div>
		</div>
	</div>

	<div class="cj-dashboard-grid">
		<?php foreach ( $sections as $section ) : ?>
			<a class="cj-dashboard-card" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $section['page'] ) ); ?>">
				<span class="dashicons <?php echo esc_attr( $section['icon'] ); ?>"></span>
				<strong><?php echo esc_html( $section['title'] ); ?></strong>
				<small><?php echo esc_html( $section['description'] ); ?></small>
				<i class="dashicons dashicons-arrow-right-alt2"></i>
			</a>
		<?php endforeach; ?>
	</div>

	<div class="cj-status-card">
		<h3><?php esc_html_e( '主题状态', 'cozy-journal' ); ?></h3>
		<div class="cj-status-grid">
			<div><span><?php esc_html_e( '主题版本', 'cozy-journal' ); ?></span><strong><?php echo esc_html( COZY_JOURNAL_VERSION ); ?></strong></div>
			<div><span><?php esc_html_e( '主题作者', 'cozy-journal' ); ?></span><strong><a href="<?php echo esc_url( COZY_JOURNAL_REPOSITORY_URL ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( COZY_JOURNAL_AUTHOR ); ?></a></strong></div>
			<div><span><?php esc_html_e( 'WordPress', 'cozy-journal' ); ?></span><strong><?php echo esc_html( get_bloginfo( 'version' ) ); ?></strong></div>
			<div><span><?php esc_html_e( 'PHP', 'cozy-journal' ); ?></span><strong><?php echo esc_html( PHP_VERSION ); ?></strong></div>
		</div>
	</div>
	<?php
}

/**
 * Renders backup, restore and reset controls.
 */
function cozy_journal_render_backup_panel() {
	?>
	<div class="cj-section-heading">
		<p><?php esc_html_e( 'Backup & Restore', 'cozy-journal' ); ?></p>
		<h2><?php esc_html_e( '备份与恢复', 'cozy-journal' ); ?></h2>
		<span><?php esc_html_e( '下载当前设置，在更换环境或试验新配色前留一份安心备份。', 'cozy-journal' ); ?></span>
	</div>

	<div class="cj-tool-grid">
		<div class="cj-tool-card">
			<span class="cj-tool-icon dashicons dashicons-download"></span>
			<h3><?php esc_html_e( '完整私有备份', 'cozy-journal' ); ?></h3>
			<p><?php esc_html_e( '用于自己迁移和恢复。包含全部主题设置，可能包含主理人称呼、写作路径、自定义链接、版权文字和其他个性化文案，请勿公开分享。不会导出文章、媒体文件、密码或 WordPress 用户账户。', 'cozy-journal' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="cozy_journal_export_options">
				<?php wp_nonce_field( 'cozy_journal_export_options' ); ?>
				<button class="button cj-secondary-button" type="submit"><?php esc_html_e( '下载私有备份', 'cozy-journal' ); ?></button>
			</form>
		</div>

		<div class="cj-tool-card">
			<span class="cj-tool-icon dashicons dashicons-art"></span>
			<h3><?php esc_html_e( '脱敏样式模板', 'cozy-journal' ); ?></h3>
			<p><?php esc_html_e( '适合公开分享。只导出颜色、尺寸、布局、选择项和开关，不包含自由文案、姓名、写作路径、域名或其他 URL。', 'cozy-journal' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="cozy_journal_export_style_template">
				<?php wp_nonce_field( 'cozy_journal_export_style_template' ); ?>
				<button class="button cj-secondary-button" type="submit"><?php esc_html_e( '下载脱敏模板', 'cozy-journal' ); ?></button>
			</form>
		</div>

		<div class="cj-tool-card">
			<span class="cj-tool-icon dashicons dashicons-upload"></span>
			<h3><?php esc_html_e( '导入主题设置', 'cozy-journal' ); ?></h3>
			<p><?php esc_html_e( '选择由 Cozy Journal 导出的完整备份或脱敏样式模板。旧格式备份仍然兼容；导入前不会删除文章或媒体。', 'cozy-journal' ); ?></p>
			<form class="cj-import-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
				<input type="hidden" name="action" value="cozy_journal_import_options">
				<?php wp_nonce_field( 'cozy_journal_import_options' ); ?>
				<label class="cj-file-picker">
					<input type="file" name="cozy_journal_backup" accept="application/json,.json">
					<span class="dashicons dashicons-media-code"></span>
					<b><?php esc_html_e( '选择 JSON 文件', 'cozy-journal' ); ?></b>
				</label>
				<button class="button button-primary cj-primary-button" type="submit"><?php esc_html_e( '导入并恢复', 'cozy-journal' ); ?></button>
			</form>
		</div>
	</div>

	<div class="cj-danger-zone">
		<div>
			<span class="cj-kicker"><?php esc_html_e( 'Danger zone', 'cozy-journal' ); ?></span>
			<h3><?php esc_html_e( '恢复全部主题默认值', 'cozy-journal' ); ?></h3>
			<p><?php esc_html_e( '只重置 Cozy Journal 的称呼、品牌、隐私、配色和布局等主题选项，不会删除文章、页面、菜单、图片或小工具。建议先导出私有备份。', 'cozy-journal' ); ?></p>
		</div>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="cozy_journal_reset_options">
			<input type="hidden" name="reset_mode" value="all">
			<input type="hidden" name="return_page" value="cozy-journal-settings-backup">
			<?php wp_nonce_field( 'cozy_journal_reset_options' ); ?>
			<button class="button cj-danger-button" type="submit" data-cj-confirm="<?php esc_attr_e( '确定恢复全部默认设置吗？这个操作不会删除文章，但会清除当前称呼、配色和布局选项。', 'cozy-journal' ); ?>"><?php esc_html_e( '重置全部设置', 'cozy-journal' ); ?></button>
		</form>
	</div>
	<?php
}

/**
 * Renders theme information.
 */
function cozy_journal_render_about_panel() {
	?>
	<div class="cj-section-heading">
		<p><?php esc_html_e( 'About the theme', 'cozy-journal' ); ?></p>
		<h2><?php esc_html_e( '关于 Cozy Journal', 'cozy-journal' ); ?></h2>
		<span><?php esc_html_e( '一款为日常记录、摄影随笔和温柔生活准备的手账风主题。', 'cozy-journal' ); ?></span>
	</div>

	<div class="cj-about-card">
		<div class="cj-about-mark">CJ</div>
		<div>
			<h3><?php esc_html_e( '把普通的日子，认真地装订起来。', 'cozy-journal' ); ?></h3>
			<p><?php esc_html_e( 'Cozy Journal 使用经典 WordPress 模板结构，兼顾文章阅读、移动端体验和可视化设置。所有插画装饰由 CSS 与字符构成，不依赖远程字体或图片。', 'cozy-journal' ); ?></p>
		</div>
	</div>

	<dl class="cj-theme-details">
		<div><dt><?php esc_html_e( '主题名称', 'cozy-journal' ); ?></dt><dd>Cozy Journal</dd></div>
		<div><dt><?php esc_html_e( '当前版本', 'cozy-journal' ); ?></dt><dd><?php echo esc_html( COZY_JOURNAL_VERSION ); ?></dd></div>
		<div><dt><?php esc_html_e( '主题作者', 'cozy-journal' ); ?></dt><dd><a href="<?php echo esc_url( COZY_JOURNAL_REPOSITORY_URL ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( COZY_JOURNAL_AUTHOR ); ?></a></dd></div>
		<div><dt><?php esc_html_e( '主题来源', 'cozy-journal' ); ?></dt><dd><a href="<?php echo esc_url( COZY_JOURNAL_REPOSITORY_URL ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( COZY_JOURNAL_REPOSITORY_URL ); ?></a></dd></div>
		<div><dt><?php esc_html_e( '许可证', 'cozy-journal' ); ?></dt><dd>GNU GPL v2 or later</dd></div>
	</dl>

	<div class="cj-thank-you">
		<span aria-hidden="true">♡</span>
		<p><?php esc_html_e( '感谢你让这本小小的电子手账有了可以停靠的地方。', 'cozy-journal' ); ?></p>
	</div>
	<?php
}

/**
 * Renders the complete dedicated theme options screen.
 */
function cozy_journal_render_options_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$current    = cozy_journal_get_current_admin_section();
	$navigation = cozy_journal_get_admin_navigation();
	$sections   = cozy_journal_get_theme_option_sections();
	$is_editable = isset( $sections[ $current ] );
	$customize_url = add_query_arg(
		array( 'autofocus[panel]' => 'cozy_journal_theme_options' ),
		admin_url( 'customize.php' )
	);
	?>
	<div class="wrap cozy-journal-options-wrap">
		<header class="cj-admin-header">
			<div class="cj-brand">
				<span class="cj-brand-mark">CJ</span>
				<div>
					<h1><?php esc_html_e( 'Cozy Journal 主题设置', 'cozy-journal' ); ?></h1>
					<p><?php esc_html_e( 'Theme Options · Cozy Journal', 'cozy-journal' ); ?></p>
				</div>
				<small>v<?php echo esc_html( COZY_JOURNAL_VERSION ); ?></small>
			</div>
			<div class="cj-header-actions">
				<a class="button cj-secondary-button" href="<?php echo esc_url( $customize_url ); ?>"><span class="dashicons dashicons-visibility"></span><?php esc_html_e( '实时预览', 'cozy-journal' ); ?></a>
				<?php if ( $is_editable ) : ?>
					<button class="button cj-secondary-button" type="submit" form="cozy-journal-reset-form" data-cj-confirm="<?php esc_attr_e( '确定恢复本页的默认设置吗？', 'cozy-journal' ); ?>"><span class="dashicons dashicons-image-rotate"></span><?php esc_html_e( '重置本区', 'cozy-journal' ); ?></button>
					<button class="button button-primary cj-primary-button" type="submit" form="cozy-journal-settings-form"><span class="dashicons dashicons-saved"></span><?php esc_html_e( '保存设置', 'cozy-journal' ); ?></button>
				<?php endif; ?>
			</div>
		</header>

		<?php cozy_journal_render_options_notice(); ?>

		<div class="cj-options-shell">
			<aside class="cj-options-nav">
				<div class="cj-options-nav-intro">
					<span><?php esc_html_e( 'MY JOURNAL', 'cozy-journal' ); ?></span>
					<strong><?php esc_html_e( '设置目录', 'cozy-journal' ); ?></strong>
				</div>
				<nav aria-label="<?php esc_attr_e( '主题设置分区', 'cozy-journal' ); ?>">
					<?php foreach ( $navigation as $key => $item ) : ?>
						<a class="<?php echo $current === $key ? 'is-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $item['page'] ) ); ?>">
							<span class="dashicons <?php echo esc_attr( $item['icon'] ); ?>"></span>
							<?php echo esc_html( $item['title'] ); ?>
							<i class="dashicons dashicons-arrow-right-alt2"></i>
						</a>
					<?php endforeach; ?>
				</nav>
				<div class="cj-options-nav-footer">
					<span><?php esc_html_e( 'Made with care by', 'cozy-journal' ); ?></span>
					<strong><a href="<?php echo esc_url( COZY_JOURNAL_REPOSITORY_URL ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( COZY_JOURNAL_AUTHOR ); ?></a></strong>
				</div>
			</aside>

			<main class="cj-options-content">
				<?php
				if ( 'welcome' === $current ) {
					cozy_journal_render_welcome_panel();
				} elseif ( 'backup' === $current ) {
					cozy_journal_render_backup_panel();
				} elseif ( 'about' === $current ) {
					cozy_journal_render_about_panel();
				} elseif ( $is_editable ) {
					cozy_journal_render_editable_section( $current, $sections[ $current ] );
				}
				?>
			</main>
		</div>

		<?php if ( $is_editable ) : ?>
			<form id="cozy-journal-reset-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="cozy_journal_reset_options">
				<input type="hidden" name="reset_mode" value="section">
				<input type="hidden" name="section" value="<?php echo esc_attr( $current ); ?>">
				<input type="hidden" name="return_page" value="<?php echo esc_attr( $sections[ $current ]['page'] ); ?>">
				<?php wp_nonce_field( 'cozy_journal_reset_options' ); ?>
			</form>
		<?php endif; ?>
	</div>
	<?php
}
