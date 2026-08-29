<?php
/**
 * Expanded settings schema for Cozy Journal.
 *
 * Kept separate from the settings screen renderer so new controls can be
 * added without making the admin page controller difficult to maintain.
 *
 * @package Cozy_Journal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds the extended option groups to the core settings schema.
 *
 * @param array<string, array<string, mixed>> $sections Core option sections.
 * @return array<string, array<string, mixed>>
 */
function cozy_journal_expand_theme_option_sections( $sections ) {
	$field = static function( $label, $description, $type, $default, $group, $extra = array() ) {
		return array_merge(
			array(
				'label'       => $label,
				'description' => $description,
				'type'        => $type,
				'default'     => $default,
				'group'       => $group,
			),
			$extra
		);
	};

	$branding_section = array(
		'page'        => 'cozy-journal-settings-branding',
		'title'       => __( '品牌与来源', 'cozy-journal' ),
		'eyebrow'     => __( 'Branding & Credit', 'cozy-journal' ),
		'description' => __( '使用中性默认品牌，并按需要修改设置中心名称、说明和页尾来源展示。', 'cozy-journal' ),
		'icon'        => 'dashicons-admin-site-alt3',
		'fields'      => array(
			'cozy_journal_admin_brand_name' => $field( __( '设置中心显示名称', 'cozy-journal' ), __( '只影响主题设置中心和自定义器标题，不会修改 WordPress 站点标题。', 'cozy-journal' ), 'text', 'Cozy Journal', __( '设置中心品牌', 'cozy-journal' ) ),
			'cozy_journal_admin_brand_tagline' => $field( __( '设置中心说明', 'cozy-journal' ), __( '用于后台欢迎页、设置页页头和关于主题页面。', 'cozy-journal' ), 'text', __( '一个简洁灵活的 WordPress 手账主题', 'cozy-journal' ), __( '设置中心品牌', 'cozy-journal' ) ),
			'cozy_journal_admin_brand_mark' => $field( __( '设置中心标记', 'cozy-journal' ), __( '建议使用 1 至 3 个字符，例如 CJ、✿ 或一个汉字。', 'cozy-journal' ), 'text', 'CJ', __( '设置中心品牌', 'cozy-journal' ) ),
			'cozy_journal_show_theme_credit' => $field( __( '显示页尾主题来源', 'cozy-journal' ), __( '默认显示主题来源，关闭后不会影响版权行或页脚菜单。', 'cozy-journal' ), 'checkbox', true, __( '页尾来源', 'cozy-journal' ) ),
			'cozy_journal_theme_credit_symbol' => $field( __( '来源装饰符号', 'cozy-journal' ), __( '使用字符或 Emoji，不加载外部图片。', 'cozy-journal' ), 'text', '✿', __( '页尾来源', 'cozy-journal' ) ),
			'cozy_journal_theme_credit_prefix' => $field( __( '来源前缀文字', 'cozy-journal' ), __( '作者名称 GetL-X 和仓库链接保持固定。', 'cozy-journal' ), 'text', 'Theme Cozy Journal by', __( '页尾来源', 'cozy-journal' ) ),
		),
	);

	$sections['writing']['fields'] = array_merge(
		$sections['writing']['fields'],
		array(
			'cozy_journal_write_link_text' => $field( __( '页头写作入口文字', 'cozy-journal' ), __( '例如“写文章”或“记一页”。', 'cozy-journal' ), 'text', __( '写文章', 'cozy-journal' ), __( '入口与权限', 'cozy-journal' ) ),
			'cozy_journal_writing_show_user_name' => $field( __( '显示当前用户名称', 'cozy-journal' ), __( '关闭时写作台只显示“当前用户”，避免截图带出 WordPress 公开显示名称。', 'cozy-journal' ), 'checkbox', false, __( '身份隐私', 'cozy-journal' ) ),
			'cozy_journal_writing_show_user_avatar' => $field( __( '显示当前用户头像', 'cozy-journal' ), __( '关闭可避免截图暴露头像，也避免主题主动渲染可能来自远程服务的头像。', 'cozy-journal' ), 'checkbox', false, __( '身份隐私', 'cozy-journal' ) ),
			'cozy_journal_writing_show_lock_user_name' => $field( __( '编辑锁显示协作者名称', 'cozy-journal' ), __( '关闭时统一提示“另一位用户正在编辑”，不显示对方公开名称。', 'cozy-journal' ), 'checkbox', false, __( '身份隐私', 'cozy-journal' ) ),
			'cozy_journal_writing_back_text' => $field( __( '返回首页链接文字', 'cozy-journal' ), __( '显示在写作台左上角。', 'cozy-journal' ), 'text', __( '返回手账首页', 'cozy-journal' ), __( '写作台文字', 'cozy-journal' ) ),
			'cozy_journal_writing_title_placeholder' => $field( __( '标题输入提示', 'cozy-journal' ), __( '文章还没有标题时显示在标题框里。', 'cozy-journal' ), 'text', __( '给这一页起个名字……', 'cozy-journal' ), __( '写作台文字', 'cozy-journal' ) ),
			'cozy_journal_writing_editor_prompt' => $field( __( '编辑器上方提示', 'cozy-journal' ), __( '显示在正文编辑器上方。', 'cozy-journal' ), 'text', __( '写下故事、插入照片，也可以切换到文本模式整理 HTML。', 'cozy-journal' ), __( '写作台文字', 'cozy-journal' ) ),
			'cozy_journal_writing_excerpt_placeholder' => $field( __( '摘要输入提示', 'cozy-journal' ), __( '显示在摘要输入框中。', 'cozy-journal' ), 'text', __( '用一两句话介绍这篇文章……', 'cozy-journal' ), __( '写作台文字', 'cozy-journal' ) ),
			'cozy_journal_writing_editor_height' => $field( __( '正文编辑器高度', 'cozy-journal' ), __( '编辑器仍可切换全屏。', 'cozy-journal' ), 'range', 520, __( '写作体验', 'cozy-journal' ), array( 'min' => 320, 'max' => 800, 'step' => 20, 'unit' => 'px' ) ),
			'cozy_journal_writing_show_excerpt' => $field( __( '显示摘要编辑框', 'cozy-journal' ), __( '关闭后不会删除已经保存的摘要。', 'cozy-journal' ), 'checkbox', true, __( '写作体验', 'cozy-journal' ) ),
			'cozy_journal_writing_default_comments' => $field( __( '新文章默认允许留言', 'cozy-journal' ), __( '只影响从写作台新建的文章。', 'cozy-journal' ), 'checkbox', true, __( '写作体验', 'cozy-journal' ) ),
			'cozy_journal_writing_show_recent' => $field( __( '显示最近写过的文章', 'cozy-journal' ), __( '显示在写作表单下方。', 'cozy-journal' ), 'checkbox', true, __( '最近文章', 'cozy-journal' ) ),
			'cozy_journal_writing_recent_count' => $field( __( '最近文章数量', 'cozy-journal' ), __( '写作台底部显示的文章数量。', 'cozy-journal' ), 'number', 6, __( '最近文章', 'cozy-journal' ), array( 'min' => 3, 'max' => 12, 'step' => 1 ) ),
		)
	);

	$sections['global']['fields'] = array_merge(
		$sections['global']['fields'],
		array(
			'cozy_journal_muted_color' => $field( __( '辅助文字颜色', 'cozy-journal' ), __( '用于日期、摘要和页脚等次要信息。', 'cozy-journal' ), 'color', '#89756b', __( '颜色搭配', 'cozy-journal' ) ),
			'cozy_journal_body_font_size' => $field( __( '全站基础字号', 'cozy-journal' ), __( '影响正文、导航和说明文字。', 'cozy-journal' ), 'range', 16, __( '字号与阅读', 'cozy-journal' ), array( 'min' => 14, 'max' => 20, 'step' => 1, 'unit' => 'px' ) ),
			'cozy_journal_body_line_height' => $field( __( '全站文字行高', 'cozy-journal' ), __( '数值越大，段落越舒展。', 'cozy-journal' ), 'range', 180, __( '字号与阅读', 'cozy-journal' ), array( 'min' => 150, 'max' => 220, 'step' => 5, 'unit' => '%' ) ),
			'cozy_journal_link_style' => $field( __( '正文链接样式', 'cozy-journal' ), __( '选择始终显示或悬停时显示下划线。', 'cozy-journal' ), 'select', 'underline', __( '字号与阅读', 'cozy-journal' ), array( 'choices' => array( 'underline' => __( '始终显示下划线', 'cozy-journal' ), 'hover' => __( '悬停时显示', 'cozy-journal' ) ) ) ),
			'cozy_journal_smooth_scroll' => $field( __( '启用平滑滚动', 'cozy-journal' ), __( '用于页内锚点跳转。', 'cozy-journal' ), 'checkbox', true, __( '字号与阅读', 'cozy-journal' ) ),
			'cozy_journal_site_width' => $field( __( '全站最大宽度', 'cozy-journal' ), __( '控制页头、首页、列表和页脚宽度。', 'cozy-journal' ), 'range', 1180, __( '宽度与间距', 'cozy-journal' ), array( 'min' => 960, 'max' => 1440, 'step' => 20, 'unit' => 'px' ) ),
			'cozy_journal_content_width' => $field( __( '文章正文宽度', 'cozy-journal' ), __( '控制文章中普通段落的最大宽度。', 'cozy-journal' ), 'range', 760, __( '宽度与间距', 'cozy-journal' ), array( 'min' => 620, 'max' => 920, 'step' => 20, 'unit' => 'px' ) ),
			'cozy_journal_page_spacing' => $field( __( '页面上下留白', 'cozy-journal' ), __( '调整主要内容区域的呼吸感。', 'cozy-journal' ), 'range', 38, __( '宽度与间距', 'cozy-journal' ), array( 'min' => 16, 'max' => 80, 'step' => 2, 'unit' => 'px' ) ),
			'cozy_journal_image_radius' => $field( __( '图片圆角', 'cozy-journal' ), __( '影响卡片缩略图和正文图片。', 'cozy-journal' ), 'range', 7, __( '卡片细节', 'cozy-journal' ), array( 'min' => 0, 'max' => 24, 'step' => 1, 'unit' => 'px' ) ),
			'cozy_journal_button_shape' => $field( __( '按钮形状', 'cozy-journal' ), __( '统一调整主题主要按钮。', 'cozy-journal' ), 'select', 'pill', __( '卡片细节', 'cozy-journal' ), array( 'choices' => array( 'soft' => __( '轻微圆角', 'cozy-journal' ), 'rounded' => __( '圆润', 'cozy-journal' ), 'pill' => __( '胶囊形', 'cozy-journal' ) ) ) ),
			'cozy_journal_shadow_style' => $field( __( '卡片阴影强度', 'cozy-journal' ), __( '统一调整主要纸张的立体感。', 'cozy-journal' ), 'select', 'soft', __( '卡片细节', 'cozy-journal' ), array( 'choices' => array( 'none' => __( '无阴影', 'cozy-journal' ), 'soft' => __( '柔和', 'cozy-journal' ), 'medium' => __( '清晰', 'cozy-journal' ), 'strong' => __( '明显', 'cozy-journal' ) ) ) ),
			'cozy_journal_card_border_style' => $field( __( '卡片边框样式', 'cozy-journal' ), __( '应用于纸张、卡片、侧栏和页脚。', 'cozy-journal' ), 'select', 'solid', __( '卡片细节', 'cozy-journal' ), array( 'choices' => array( 'solid' => __( '细实线', 'cozy-journal' ), 'dashed' => __( '手账虚线', 'cozy-journal' ), 'none' => __( '隐藏边框', 'cozy-journal' ) ) ) ),
		)
	);

	$header_section = array(
		'page'        => 'cozy-journal-settings-header',
		'title'       => __( '页头与导航', 'cozy-journal' ),
		'eyebrow'     => __( 'Header & Navigation', 'cozy-journal' ),
		'description' => __( '控制站点标识、装订环、导航菜单、搜索框和页头排列。', 'cozy-journal' ),
		'icon'        => 'dashicons-menu-alt3',
		'fields'      => array(
			'cozy_journal_show_notebook_rings' => $field( __( '显示顶部装订环', 'cozy-journal' ), __( '关闭后隐藏页头上方的金属环。', 'cozy-journal' ), 'checkbox', true, __( '站点标识', 'cozy-journal' ) ),
			'cozy_journal_show_brand_mark' => $field( __( '没有 Logo 时显示圆形标记', 'cozy-journal' ), __( '自定义 Logo 会优先显示。', 'cozy-journal' ), 'checkbox', true, __( '站点标识', 'cozy-journal' ) ),
			'cozy_journal_brand_mark_text' => $field( __( '圆形标记内容', 'cozy-journal' ), __( '可填写一个汉字、符号或 Emoji。', 'cozy-journal' ), 'text', '♡', __( '站点标识', 'cozy-journal' ) ),
			'cozy_journal_show_site_title' => $field( __( '显示站点标题', 'cozy-journal' ), __( '标题仍在 WordPress 站点身份中修改。', 'cozy-journal' ), 'checkbox', true, __( '站点标识', 'cozy-journal' ) ),
			'cozy_journal_show_site_description' => $field( __( '显示站点副标题', 'cozy-journal' ), __( '移动端仍会自动隐藏以节省空间。', 'cozy-journal' ), 'checkbox', true, __( '站点标识', 'cozy-journal' ) ),
			'cozy_journal_header_layout' => $field( __( '页头排列', 'cozy-journal' ), __( '居中模式会把品牌放到导航上方。', 'cozy-journal' ), 'select', 'horizontal', __( '布局与密度', 'cozy-journal' ), array( 'choices' => array( 'horizontal' => __( '左右横排', 'cozy-journal' ), 'centered' => __( '上下居中', 'cozy-journal' ) ) ) ),
			'cozy_journal_header_density' => $field( __( '页头高度', 'cozy-journal' ), __( '紧凑模式适合导航较多的站点。', 'cozy-journal' ), 'select', 'comfortable', __( '布局与密度', 'cozy-journal' ), array( 'choices' => array( 'compact' => __( '紧凑', 'cozy-journal' ), 'comfortable' => __( '舒适', 'cozy-journal' ) ) ) ),
			'cozy_journal_sticky_header' => $field( __( '滚动时固定页头', 'cozy-journal' ), __( '页头会保持在浏览器顶部。', 'cozy-journal' ), 'checkbox', false, __( '布局与密度', 'cozy-journal' ) ),
			'cozy_journal_show_primary_navigation' => $field( __( '显示主导航', 'cozy-journal' ), __( '关闭后同时隐藏移动菜单按钮。', 'cozy-journal' ), 'checkbox', true, __( '导航与搜索', 'cozy-journal' ) ),
			'cozy_journal_menu_fallback_pages' => $field( __( '未设置菜单时显示页面列表', 'cozy-journal' ), __( '关闭后未分配主菜单时保持空白。', 'cozy-journal' ), 'checkbox', true, __( '导航与搜索', 'cozy-journal' ) ),
			'cozy_journal_mobile_menu_text' => $field( __( '移动菜单按钮文字', 'cozy-journal' ), __( '很小的屏幕只保留图标。', 'cozy-journal' ), 'text', __( '展开菜单', 'cozy-journal' ), __( '导航与搜索', 'cozy-journal' ) ),
			'cozy_journal_show_header_search' => $field( __( '在页头显示搜索框', 'cozy-journal' ), __( '移动端会放到展开的导航区域下方。', 'cozy-journal' ), 'checkbox', false, __( '导航与搜索', 'cozy-journal' ) ),
		),
	);

	$home_basics = array(
		'cozy_journal_show_hero'        => $sections['initial']['fields']['cozy_journal_show_hero'],
		'cozy_journal_show_hero_date'   => $sections['initial']['fields']['cozy_journal_show_hero_date'],
		'cozy_journal_home_posts_count' => $sections['initial']['fields']['cozy_journal_home_posts_count'],
	);
	unset(
		$sections['initial']['fields']['cozy_journal_show_hero'],
		$sections['initial']['fields']['cozy_journal_show_hero_date'],
		$sections['initial']['fields']['cozy_journal_home_posts_count']
	);
	$sections['home']['fields'] = array_merge(
		$home_basics,
		$sections['home']['fields'],
		array(
			'cozy_journal_hero_date_format' => $field( __( '欢迎卡日期格式', 'cozy-journal' ), __( '选择日期和星期的显示方式。', 'cozy-journal' ), 'select', 'month-day-weekday', __( '欢迎卡布局', 'cozy-journal' ), array( 'choices' => array( 'month-day-weekday' => __( '8月29日 · 星期六', 'cozy-journal' ), 'year-month-day' => __( '2026年8月29日', 'cozy-journal' ), 'iso' => __( '2026-08-29', 'cozy-journal' ), 'weekday' => __( '星期六', 'cozy-journal' ) ) ) ),
			'cozy_journal_hero_layout' => $field( __( '欢迎卡布局', 'cozy-journal' ), __( '选择左右图文、居中文字或纯文字。', 'cozy-journal' ), 'select', 'split', __( '欢迎卡布局', 'cozy-journal' ), array( 'choices' => array( 'split' => __( '左文右图', 'cozy-journal' ), 'centered' => __( '文字居中', 'cozy-journal' ), 'text-only' => __( '纯文字', 'cozy-journal' ) ) ) ),
			'cozy_journal_show_hero_art' => $field( __( '显示拍立得插画', 'cozy-journal' ), __( '关闭后仍可保留贴纸和文字。', 'cozy-journal' ), 'checkbox', true, __( '欢迎卡布局', 'cozy-journal' ) ),
			'cozy_journal_hero_art_caption' => $field( __( '拍立得底部文字', 'cozy-journal' ), __( '留空可隐藏拍立得上的文字。', 'cozy-journal' ), 'text', __( '今日份 · 好心情', 'cozy-journal' ), __( '欢迎卡布局', 'cozy-journal' ) ),
			'cozy_journal_hero_button_new_tab' => $field( __( '按钮在新标签页打开', 'cozy-journal' ), __( '链接到站外页面时可以开启。', 'cozy-journal' ), 'checkbox', false, __( '行动按钮', 'cozy-journal' ) ),
			'cozy_journal_show_front_page_content' => $field( __( '显示静态首页正文', 'cozy-journal' ), __( '控制欢迎卡与文章列表之间的页面正文。', 'cozy-journal' ), 'checkbox', true, __( '最新文章区域', 'cozy-journal' ) ),
			'cozy_journal_show_latest_section' => $field( __( '显示首页最新文章区域', 'cozy-journal' ), __( '关闭后首页不再输出文章卡片列表。', 'cozy-journal' ), 'checkbox', true, __( '最新文章区域', 'cozy-journal' ) ),
			'cozy_journal_latest_kicker' => $field( __( '最新文章英文小标题', 'cozy-journal' ), __( '显示在区域标题上方。', 'cozy-journal' ), 'text', __( 'Recently in my journal', 'cozy-journal' ), __( '最新文章区域', 'cozy-journal' ) ),
			'cozy_journal_latest_title' => $field( __( '最新文章区域标题', 'cozy-journal' ), __( '显示在首页文章列表上方。', 'cozy-journal' ), 'text', __( '最近写下的小日子', 'cozy-journal' ), __( '最新文章区域', 'cozy-journal' ) ),
			'cozy_journal_show_latest_doodle' => $field( __( '显示标题旁手绘符号', 'cozy-journal' ), __( '控制标题右侧的小涂鸦。', 'cozy-journal' ), 'checkbox', true, __( '最新文章区域', 'cozy-journal' ) ),
			'cozy_journal_latest_doodle' => $field( __( '标题旁手绘符号', 'cozy-journal' ), __( '可填写短符号或 Emoji。', 'cozy-journal' ), 'text', '〰✎', __( '最新文章区域', 'cozy-journal' ) ),
			'cozy_journal_home_columns' => $field( __( '首页文章列数', 'cozy-journal' ), __( '窄屏设备会自动改为单列。', 'cozy-journal' ), 'select', '2', __( '最新文章区域', 'cozy-journal' ), array( 'choices' => array( '1' => __( '一列', 'cozy-journal' ), '2' => __( '两列', 'cozy-journal' ), '3' => __( '三列', 'cozy-journal' ) ) ) ),
			'cozy_journal_home_posts_order' => $field( __( '首页文章排序', 'cozy-journal' ), __( '只影响静态首页的额外文章查询。', 'cozy-journal' ), 'select', 'latest', __( '最新文章区域', 'cozy-journal' ), array( 'choices' => array( 'latest' => __( '最新发布优先', 'cozy-journal' ), 'modified' => __( '最近更新优先', 'cozy-journal' ), 'oldest' => __( '最早发布优先', 'cozy-journal' ), 'random' => __( '随机翻页', 'cozy-journal' ) ) ) ),
			'cozy_journal_home_ignore_sticky' => $field( __( '不让置顶文章优先', 'cozy-journal' ), __( '置顶文章按普通文章参与排序。', 'cozy-journal' ), 'checkbox', false, __( '最新文章区域', 'cozy-journal' ) ),
		)
	);

	$sections['content']['fields'] = array_merge(
		$sections['content']['fields'],
		array(
			'cozy_journal_archive_columns' => $field( __( '归档网格列数', 'cozy-journal' ), __( '只在网格文章列表中生效。', 'cozy-journal' ), 'select', '2', __( '文章列表', 'cozy-journal' ), array( 'choices' => array( '1' => __( '一列', 'cozy-journal' ), '2' => __( '两列', 'cozy-journal' ), '3' => __( '三列', 'cozy-journal' ) ) ) ),
			'cozy_journal_show_archive_header' => $field( __( '显示归档标题纸张', 'cozy-journal' ), __( '影响博客、归档和搜索页面。', 'cozy-journal' ), 'checkbox', true, __( '归档标题', 'cozy-journal' ) ),
			'cozy_journal_blog_kicker' => $field( __( '博客列表小标题', 'cozy-journal' ), __( '用于默认文章索引页。', 'cozy-journal' ), 'text', __( 'Journal archive', 'cozy-journal' ), __( '归档标题', 'cozy-journal' ) ),
			'cozy_journal_blog_title' => $field( __( '博客列表标题', 'cozy-journal' ), __( '用于默认文章索引页。', 'cozy-journal' ), 'text', __( '一页一页，都是生活', 'cozy-journal' ), __( '归档标题', 'cozy-journal' ) ),
			'cozy_journal_blog_description' => $field( __( '博客列表说明', 'cozy-journal' ), __( '显示在博客列表标题下方。', 'cozy-journal' ), 'textarea', __( '这里收好了最近写下的心情、故事与微小闪光。', 'cozy-journal' ), __( '归档标题', 'cozy-journal' ) ),
			'cozy_journal_show_card_featured_images' => $field( __( '文章卡片显示特色图片', 'cozy-journal' ), __( '仍受特色图片总开关控制。', 'cozy-journal' ), 'checkbox', true, __( '卡片内容', 'cozy-journal' ) ),
			'cozy_journal_show_card_category' => $field( __( '显示卡片分类', 'cozy-journal' ), __( '显示文章的第一个分类。', 'cozy-journal' ), 'checkbox', true, __( '卡片内容', 'cozy-journal' ) ),
			'cozy_journal_show_card_date' => $field( __( '显示卡片发布日期', 'cozy-journal' ), __( '显示在标题下方。', 'cozy-journal' ), 'checkbox', true, __( '卡片内容', 'cozy-journal' ) ),
			'cozy_journal_show_card_author' => $field( __( '显示卡片作者', 'cozy-journal' ), __( '新安装默认关闭；多作者站点可以开启。', 'cozy-journal' ), 'checkbox', false, __( '卡片内容', 'cozy-journal' ) ),
			'cozy_journal_excerpt_length' => $field( __( '自动摘要字数', 'cozy-journal' ), __( '只影响没有手动摘要的文章。', 'cozy-journal' ), 'number', 34, __( '卡片内容', 'cozy-journal' ), array( 'min' => 10, 'max' => 100, 'step' => 2 ) ),
			'cozy_journal_show_read_more' => $field( __( '显示继续阅读链接', 'cozy-journal' ), __( '标题和图片仍然可以进入正文。', 'cozy-journal' ), 'checkbox', true, __( '卡片内容', 'cozy-journal' ) ),
			'cozy_journal_read_more_text' => $field( __( '继续阅读文字', 'cozy-journal' ), __( '显示在文章卡片底部。', 'cozy-journal' ), 'text', __( '继续读这一页', 'cozy-journal' ), __( '卡片内容', 'cozy-journal' ) ),
			'cozy_journal_thumbnail_ratio' => $field( __( '卡片图片比例', 'cozy-journal' ), __( '列表布局会优先填满卡片高度。', 'cozy-journal' ), 'select', 'landscape', __( '卡片图片', 'cozy-journal' ), array( 'choices' => array( 'wide' => __( '宽幅 16:9', 'cozy-journal' ), 'landscape' => __( '相册 16:10', 'cozy-journal' ), 'square' => __( '方形 1:1', 'cozy-journal' ), 'portrait' => __( '竖幅 4:5', 'cozy-journal' ) ) ) ),
			'cozy_journal_show_sticky_marker' => $field( __( '显示置顶文章星标', 'cozy-journal' ), __( '显示在分类标签旁。', 'cozy-journal' ), 'checkbox', true, __( '卡片内容', 'cozy-journal' ) ),
			'cozy_journal_pagination_previous' => $field( __( '上一页文字', 'cozy-journal' ), __( '指向较新的文章列表。', 'cozy-journal' ), 'text', __( '← 新一点', 'cozy-journal' ), __( '列表翻页', 'cozy-journal' ) ),
			'cozy_journal_pagination_next' => $field( __( '下一页文字', 'cozy-journal' ), __( '指向较旧的文章列表。', 'cozy-journal' ), 'text', __( '旧一点 →', 'cozy-journal' ), __( '列表翻页', 'cozy-journal' ) ),
		)
	);

	$single_section = array(
		'page'        => 'cozy-journal-settings-single',
		'title'       => __( '文章阅读', 'cozy-journal' ),
		'eyebrow'     => __( 'Reading', 'cozy-journal' ),
		'description' => __( '细分控制文章页、独立页面、元信息、图片、留言和上下篇导航。', 'cozy-journal' ),
		'icon'        => 'dashicons-welcome-write-blog',
		'fields'      => array(
			'cozy_journal_show_single_featured_image' => $field( __( '文章页显示特色图片', 'cozy-journal' ), __( '仍受特色图片总开关控制。', 'cozy-journal' ), 'checkbox', true, __( '特色图片', 'cozy-journal' ) ),
			'cozy_journal_show_page_featured_image' => $field( __( '独立页面显示特色图片', 'cozy-journal' ), __( '仍受特色图片总开关控制。', 'cozy-journal' ), 'checkbox', true, __( '特色图片', 'cozy-journal' ) ),
			'cozy_journal_show_featured_caption' => $field( __( '显示特色图片说明', 'cozy-journal' ), __( '媒体库填写了说明时显示。', 'cozy-journal' ), 'checkbox', true, __( '特色图片', 'cozy-journal' ) ),
			'cozy_journal_single_image_style' => $field( __( '正文特色图片样式', 'cozy-journal' ), __( '拍立得样式带白边、阴影和轻微旋转。', 'cozy-journal' ), 'select', 'polaroid', __( '特色图片', 'cozy-journal' ), array( 'choices' => array( 'polaroid' => __( '拍立得', 'cozy-journal' ), 'plain' => __( '简洁图片', 'cozy-journal' ) ) ) ),
			'cozy_journal_single_title_alignment' => $field( __( '文章标题对齐', 'cozy-journal' ), __( '同时影响文章页和独立页面。', 'cozy-journal' ), 'select', 'center', __( '标题与元信息', 'cozy-journal' ), array( 'choices' => array( 'left' => __( '左对齐', 'cozy-journal' ), 'center' => __( '居中', 'cozy-journal' ) ) ) ),
			'cozy_journal_show_single_category' => $field( __( '显示文章主分类', 'cozy-journal' ), __( '显示在文章标题上方。', 'cozy-journal' ), 'checkbox', true, __( '标题与元信息', 'cozy-journal' ) ),
			'cozy_journal_show_single_date' => $field( __( '显示文章发布日期', 'cozy-journal' ), __( '显示在文章标题下方。', 'cozy-journal' ), 'checkbox', true, __( '标题与元信息', 'cozy-journal' ) ),
			'cozy_journal_show_single_author' => $field( __( '显示文章作者', 'cozy-journal' ), __( '新安装默认关闭；多作者站点可以开启。', 'cozy-journal' ), 'checkbox', false, __( '标题与元信息', 'cozy-journal' ) ),
			'cozy_journal_link_author_archive' => $field( __( '作者名称链接到作者归档', 'cozy-journal' ), __( '关闭后仍可显示作者名称，但不会生成可能含账户特征的作者归档网址。', 'cozy-journal' ), 'checkbox', false, __( '标题与元信息', 'cozy-journal' ) ),
			'cozy_journal_show_single_comment_count' => $field( __( '显示回应数量', 'cozy-journal' ), __( '有留言时显示回应链接。', 'cozy-journal' ), 'checkbox', true, __( '标题与元信息', 'cozy-journal' ) ),
			'cozy_journal_show_entry_categories' => $field( __( '正文底部显示分类', 'cozy-journal' ), __( '显示文章所属的全部分类。', 'cozy-journal' ), 'checkbox', true, __( '正文底部', 'cozy-journal' ) ),
			'cozy_journal_show_entry_tags' => $field( __( '正文底部显示标签', 'cozy-journal' ), __( '显示文章的全部标签。', 'cozy-journal' ), 'checkbox', true, __( '正文底部', 'cozy-journal' ) ),
			'cozy_journal_show_edit_link' => $field( __( '显示编辑链接', 'cozy-journal' ), __( '只对有编辑权限的登录用户可见。', 'cozy-journal' ), 'checkbox', true, __( '正文底部', 'cozy-journal' ) ),
			'cozy_journal_show_post_navigation' => $field( __( '显示上下篇文章', 'cozy-journal' ), __( '显示在文章正文后。', 'cozy-journal' ), 'checkbox', true, __( '导航与留言', 'cozy-journal' ) ),
			'cozy_journal_post_nav_previous' => $field( __( '前一篇提示文字', 'cozy-journal' ), __( '文章标题显示在这段文字下方。', 'cozy-journal' ), 'text', __( '← 前一页手账', 'cozy-journal' ), __( '导航与留言', 'cozy-journal' ) ),
			'cozy_journal_post_nav_next' => $field( __( '后一篇提示文字', 'cozy-journal' ), __( '文章标题显示在这段文字下方。', 'cozy-journal' ), 'text', __( '后一页手账 →', 'cozy-journal' ), __( '导航与留言', 'cozy-journal' ) ),
			'cozy_journal_show_comments_on_posts' => $field( __( '文章页显示留言区', 'cozy-journal' ), __( '关闭不会删除已有留言。', 'cozy-journal' ), 'checkbox', true, __( '导航与留言', 'cozy-journal' ) ),
			'cozy_journal_show_comments_on_pages' => $field( __( '独立页面显示留言区', 'cozy-journal' ), __( '还需要页面本身开启评论。', 'cozy-journal' ), 'checkbox', true, __( '导航与留言', 'cozy-journal' ) ),
			'cozy_journal_show_comment_avatars' => $field( __( '显示评论者头像', 'cozy-journal' ), __( '默认关闭，避免页面和截图带出头像或触发头像服务请求。', 'cozy-journal' ), 'checkbox', false, __( '导航与留言', 'cozy-journal' ) ),
			'cozy_journal_link_comment_author_website' => $field( __( '评论者名称链接到个人网站', 'cozy-journal' ), __( '默认关闭，评论者名称仍会显示，但不会自动链接其填写的网址。', 'cozy-journal' ), 'checkbox', false, __( '导航与留言', 'cozy-journal' ) ),
			'cozy_journal_show_page_kicker' => $field( __( '独立页面显示小标题', 'cozy-journal' ), __( '显示在页面标题上方。', 'cozy-journal' ), 'checkbox', true, __( '独立页面', 'cozy-journal' ) ),
			'cozy_journal_page_kicker' => $field( __( '独立页面小标题文字', 'cozy-journal' ), __( '例如“A little page”或“About me”。', 'cozy-journal' ), 'text', __( 'A little page', 'cozy-journal' ), __( '独立页面', 'cozy-journal' ) ),
		),
	);

	$show_sidebar_field = $sections['content']['fields']['cozy_journal_show_sidebar'];
	unset( $sections['content']['fields']['cozy_journal_show_sidebar'] );
	$footer_text_field = $sections['decorations']['fields']['cozy_journal_footer_text'];
	$footer_year_field = $sections['decorations']['fields']['cozy_journal_show_footer_year'];
	unset(
		$sections['decorations']['fields']['cozy_journal_footer_text'],
		$sections['decorations']['fields']['cozy_journal_show_footer_year']
	);

	$sidebar_footer_section = array(
		'page'        => 'cozy-journal-settings-sidebar-footer',
		'title'       => __( '侧栏与页脚', 'cozy-journal' ),
		'eyebrow'     => __( 'Sidebar & Footer', 'cozy-journal' ),
		'description' => __( '控制不同页面的侧栏位置、宽度，以及页脚纸条和版权信息。', 'cozy-journal' ),
		'icon'        => 'dashicons-align-pull-right',
		'fields'      => array(
			'cozy_journal_show_sidebar' => $show_sidebar_field,
			'cozy_journal_sidebar_position' => $field( __( '侧栏位置', 'cozy-journal' ), __( '移动端统一排列到正文下方。', 'cozy-journal' ), 'select', 'right', __( '侧栏布局', 'cozy-journal' ), array( 'choices' => array( 'right' => __( '右侧', 'cozy-journal' ), 'left' => __( '左侧', 'cozy-journal' ) ) ) ),
			'cozy_journal_sidebar_width' => $field( __( '侧栏宽度', 'cozy-journal' ), __( '桌面端侧栏的固定宽度。', 'cozy-journal' ), 'range', 300, __( '侧栏布局', 'cozy-journal' ), array( 'min' => 240, 'max' => 380, 'step' => 10, 'unit' => 'px' ) ),
			'cozy_journal_sidebar_on_archives' => $field( __( '文章列表与归档显示侧栏', 'cozy-journal' ), __( '包括博客、分类、标签和日期归档。', 'cozy-journal' ), 'checkbox', true, __( '显示位置', 'cozy-journal' ) ),
			'cozy_journal_sidebar_on_search' => $field( __( '搜索结果显示侧栏', 'cozy-journal' ), __( '单独控制搜索页面。', 'cozy-journal' ), 'checkbox', true, __( '显示位置', 'cozy-journal' ) ),
			'cozy_journal_sidebar_on_posts' => $field( __( '文章正文显示侧栏', 'cozy-journal' ), __( '单独控制单篇文章。', 'cozy-journal' ), 'checkbox', true, __( '显示位置', 'cozy-journal' ) ),
			'cozy_journal_sidebar_on_pages' => $field( __( '独立页面显示侧栏', 'cozy-journal' ), __( '单独控制页面模板。', 'cozy-journal' ), 'checkbox', true, __( '显示位置', 'cozy-journal' ) ),
			'cozy_journal_show_footer' => $field( __( '显示整块页脚', 'cozy-journal' ), __( '关闭后隐藏寄语、菜单和版权信息。', 'cozy-journal' ), 'checkbox', true, __( '页脚显示', 'cozy-journal' ) ),
			'cozy_journal_show_footer_note' => $field( __( '显示页脚寄语', 'cozy-journal' ), __( '关闭后仍可显示菜单和版权。', 'cozy-journal' ), 'checkbox', true, __( '页脚显示', 'cozy-journal' ) ),
			'cozy_journal_footer_text' => $footer_text_field,
			'cozy_journal_show_footer_menu' => $field( __( '显示页脚导航', 'cozy-journal' ), __( '还需要分配页脚菜单。', 'cozy-journal' ), 'checkbox', true, __( '页脚显示', 'cozy-journal' ) ),
			'cozy_journal_show_footer_year' => $footer_year_field,
			'cozy_journal_copyright_text' => $field( __( '自定义版权文字', 'cozy-journal' ), __( '留空使用默认格式；支持 {year} 和 {site}。', 'cozy-journal' ), 'text', '', __( '版权与样式', 'cozy-journal' ) ),
			'cozy_journal_show_footer_heart' => $field( __( '显示版权行爱心', 'cozy-journal' ), __( '控制版权信息末尾的小爱心。', 'cozy-journal' ), 'checkbox', true, __( '版权与样式', 'cozy-journal' ) ),
			'cozy_journal_show_theme_credit' => $branding_section['fields']['cozy_journal_show_theme_credit'],
			'cozy_journal_theme_credit_symbol' => $branding_section['fields']['cozy_journal_theme_credit_symbol'],
			'cozy_journal_theme_credit_prefix' => $branding_section['fields']['cozy_journal_theme_credit_prefix'],
			'cozy_journal_footer_alignment' => $field( __( '页脚内容对齐', 'cozy-journal' ), __( '控制寄语、菜单和版权方向。', 'cozy-journal' ), 'select', 'center', __( '版权与样式', 'cozy-journal' ), array( 'choices' => array( 'left' => __( '左对齐', 'cozy-journal' ), 'center' => __( '居中', 'cozy-journal' ) ) ) ),
			'cozy_journal_footer_paper_lines' => $field( __( '页脚显示横线纸纹', 'cozy-journal' ), __( '关闭后页脚使用纯色纸张。', 'cozy-journal' ), 'checkbox', true, __( '版权与样式', 'cozy-journal' ) ),
		),
	);

	$sections['decorations']['fields'] = array_merge(
		$sections['decorations']['fields'],
		array(
			'cozy_journal_tape_opacity' => $field( __( '纸胶带透明度', 'cozy-journal' ), __( '同时影响欢迎卡、卡片、正文、侧栏和页脚。', 'cozy-journal' ), 'range', 100, __( '纸胶带与贴纸', 'cozy-journal' ), array( 'min' => 20, 'max' => 100, 'step' => 5, 'unit' => '%' ) ),
			'cozy_journal_custom_sticker_1' => $field( __( '自定义贴纸 1', 'cozy-journal' ), __( '留空使用贴纸主题的第一个图案。', 'cozy-journal' ), 'text', '', __( '自定义贴纸', 'cozy-journal' ) ),
			'cozy_journal_custom_sticker_2' => $field( __( '自定义贴纸 2', 'cozy-journal' ), __( '可填写 Emoji、汉字或短符号。', 'cozy-journal' ), 'text', '', __( '自定义贴纸', 'cozy-journal' ) ),
			'cozy_journal_custom_sticker_3' => $field( __( '自定义贴纸 3', 'cozy-journal' ), __( '可填写 Emoji、汉字或短符号。', 'cozy-journal' ), 'text', '', __( '自定义贴纸', 'cozy-journal' ) ),
			'cozy_journal_show_background_pattern' => $field( __( '显示页面背景纹理', 'cozy-journal' ), __( '关闭后站点最外层使用纯色背景。', 'cozy-journal' ), 'checkbox', true, __( '纸张纹理', 'cozy-journal' ) ),
			'cozy_journal_show_background_shapes' => $field( __( '显示两侧虚线图形', 'cozy-journal' ), __( '控制页面边缘的淡色手绘轮廓。', 'cozy-journal' ), 'checkbox', true, __( '纸张纹理', 'cozy-journal' ) ),
			'cozy_journal_show_paper_lines' => $field( __( '显示卡片横线纸纹', 'cozy-journal' ), __( '影响页头、欢迎卡、正文和侧栏。', 'cozy-journal' ), 'checkbox', true, __( '纸张纹理', 'cozy-journal' ) ),
			'cozy_journal_show_paper_punches' => $field( __( '显示正文装订孔', 'cozy-journal' ), __( '控制文章页和独立页面左侧圆孔。', 'cozy-journal' ), 'checkbox', true, __( '纸张纹理', 'cozy-journal' ) ),
			'cozy_journal_enable_card_tilt' => $field( __( '文章卡片轻微倾斜', 'cozy-journal' ), __( '只在网格和较宽屏幕上生效。', 'cozy-journal' ), 'checkbox', true, __( '动态细节', 'cozy-journal' ) ),
			'cozy_journal_enable_hover_motion' => $field( __( '启用悬停动态效果', 'cozy-journal' ), __( '关闭后卡片、图片和按钮不再上浮缩放。', 'cozy-journal' ), 'checkbox', true, __( '动态细节', 'cozy-journal' ) ),
		)
	);

	$messages_section = array(
		'page'        => 'cozy-journal-settings-messages',
		'title'       => __( '搜索与提示文案', 'cozy-journal' ),
		'eyebrow'     => __( 'Messages', 'cozy-journal' ),
		'description' => __( '修改搜索、无内容状态、404 页面和留言表单中的常用提示。', 'cozy-journal' ),
		'icon'        => 'dashicons-format-status',
		'fields'      => array(
			'cozy_journal_search_placeholder' => $field( __( '搜索框提示文字', 'cozy-journal' ), __( '用于页头、搜索结果、404 和空状态。', 'cozy-journal' ), 'text', __( '想找哪一页手账？', 'cozy-journal' ), __( '搜索页面', 'cozy-journal' ) ),
			'cozy_journal_search_kicker' => $field( __( '搜索结果小标题', 'cozy-journal' ), __( '显示在搜索结果标题上方。', 'cozy-journal' ), 'text', __( 'Search notes', 'cozy-journal' ), __( '搜索页面', 'cozy-journal' ) ),
			'cozy_journal_search_title' => $field( __( '搜索结果标题模板', 'cozy-journal' ), __( '使用 {query} 代表搜索词。', 'cozy-journal' ), 'text', __( '寻找“{query}”的结果', 'cozy-journal' ), __( '搜索页面', 'cozy-journal' ) ),
			'cozy_journal_empty_title' => $field( __( '无内容状态标题', 'cozy-journal' ), __( '没有文章或搜索结果时显示。', 'cozy-journal' ), 'text', __( '这一页还是空白的', 'cozy-journal' ), __( '无内容状态', 'cozy-journal' ) ),
			'cozy_journal_empty_search_text' => $field( __( '搜索无结果说明', 'cozy-journal' ), __( '搜索不到内容时显示。', 'cozy-journal' ), 'textarea', __( '没有找到对应内容，换一个词再找找看吧。', 'cozy-journal' ), __( '无内容状态', 'cozy-journal' ) ),
			'cozy_journal_empty_archive_text' => $field( __( '暂无文章说明', 'cozy-journal' ), __( '站点或归档还没有文章时显示。', 'cozy-journal' ), 'textarea', __( '等第一篇故事写好，这里就会慢慢热闹起来。', 'cozy-journal' ), __( '无内容状态', 'cozy-journal' ) ),
			'cozy_journal_first_post_button_text' => $field( __( '第一篇文章按钮文字', 'cozy-journal' ), __( '只对有写作权限的登录用户显示。', 'cozy-journal' ), 'text', __( '写下第一篇', 'cozy-journal' ), __( '无内容状态', 'cozy-journal' ) ),
			'cozy_journal_error_sticker' => $field( __( '404 页面贴纸', 'cozy-journal' ), __( '可填写一个 Emoji 或短符号。', 'cozy-journal' ), 'text', '🐻', __( '404 页面', 'cozy-journal' ) ),
			'cozy_journal_error_title' => $field( __( '404 页面标题', 'cozy-journal' ), __( '访问不存在的网址时显示。', 'cozy-journal' ), 'text', __( '这一页好像被风吹走啦', 'cozy-journal' ), __( '404 页面', 'cozy-journal' ) ),
			'cozy_journal_error_description' => $field( __( '404 页面说明', 'cozy-journal' ), __( '告诉访客下一步可以做什么。', 'cozy-journal' ), 'textarea', __( '别担心，试着搜索一下，或者回到首页继续翻手账吧。', 'cozy-journal' ), __( '404 页面', 'cozy-journal' ) ),
			'cozy_journal_error_button_text' => $field( __( '404 返回按钮文字', 'cozy-journal' ), __( '按钮始终链接到网站首页。', 'cozy-journal' ), 'text', __( '回到手账首页', 'cozy-journal' ), __( '404 页面', 'cozy-journal' ) ),
			'cozy_journal_show_error_search' => $field( __( '404 页面显示搜索框', 'cozy-journal' ), __( '关闭后保留说明和返回按钮。', 'cozy-journal' ), 'checkbox', true, __( '404 页面', 'cozy-journal' ) ),
			'cozy_journal_comment_reply_title' => $field( __( '留言表单标题', 'cozy-journal' ), __( '显示在评论输入框上方。', 'cozy-journal' ), 'text', __( '留一张小纸条吧', 'cozy-journal' ), __( '留言表单', 'cozy-journal' ) ),
			'cozy_journal_comment_submit_text' => $field( __( '留言提交按钮文字', 'cozy-journal' ), __( '显示在评论表单提交按钮上。', 'cozy-journal' ), 'text', __( '贴上纸条', 'cozy-journal' ), __( '留言表单', 'cozy-journal' ) ),
		),
	);

	$sections['initial']['description']     = __( '设置主题设置中心欢迎页中的站点主理人称呼、问候语和说明文字。', 'cozy-journal' );
	$sections['writing']['description']     = __( '控制前台写作台入口、自动保存、编辑器尺寸、输入提示和最近文章。', 'cozy-journal' );
	$sections['global']['description']      = __( '统一设置整本手账的颜色、字体、宽度、间距、圆角、边框和阴影。', 'cozy-journal' );
	$sections['home']['description']        = __( '控制首页欢迎卡的文字、布局、插画，以及最新文章区域的标题、数量和排序。', 'cozy-journal' );
	$sections['content']['description']     = __( '控制文章列表、归档标题、卡片内容、缩略图比例、摘要和分页。', 'cozy-journal' );
	$sections['decorations']['description'] = __( '调整纸胶带、贴纸、纸张纹理、卡片倾斜和悬停动态。', 'cozy-journal' );

	unset(
		$branding_section['fields']['cozy_journal_show_theme_credit'],
		$branding_section['fields']['cozy_journal_theme_credit_symbol'],
		$branding_section['fields']['cozy_journal_theme_credit_prefix']
	);

	return array(
		'branding'       => $branding_section,
		'initial'        => $sections['initial'],
		'writing'        => $sections['writing'],
		'global'         => $sections['global'],
		'header'         => $header_section,
		'home'           => $sections['home'],
		'content'        => $sections['content'],
		'single'         => $single_section,
		'sidebar_footer' => $sidebar_footer_section,
		'decorations'    => $sections['decorations'],
		'messages'       => $messages_section,
	);
}
