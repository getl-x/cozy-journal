<?php
/**
 * Front-end writing desk for Cozy Journal.
 *
 * @package Cozy_Journal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalizes the single URL segment used by the writing desk.
 *
 * @param mixed $value Raw path value.
 * @return string
 */
function cozy_journal_normalize_writing_slug( $value ) {
	$slug = strtolower( remove_accents( wp_strip_all_tags( (string) $value ) ) );
	$slug = trim( $slug );
	$slug = trim( $slug, '/' );
	$slug = preg_replace( '/[^a-z0-9_-]+/', '-', $slug );
	$slug = trim( (string) $slug, '-_' );
	$slug = substr( $slug, 0, 60 );
	$slug = trim( $slug, '-_' );

	return $slug;
}

/**
 * Sanitizes the single URL segment used by the writing desk.
 *
 * @param mixed $value Raw path value.
 * @return string
 */
function cozy_journal_sanitize_writing_slug( $value ) {
	$slug = cozy_journal_normalize_writing_slug( $value );

	$reserved = array( '404', 'feed', 'wp-admin', 'wp-json', 'wp-login', 'wp-login-php' );
	if ( '' === $slug || in_array( $slug, $reserved, true ) || 0 === strpos( $slug, 'wp-' ) ) {
		return 'write';
	}

	return $slug;
}

/**
 * Returns rewrite bases that must not be reused by the writing desk.
 *
 * @return string[]
 */
function cozy_journal_get_reserved_writing_slugs() {
	global $wp_rewrite;

	$reserved = array(
		'404',
		'author',
		'category',
		'comment-page',
		'comments',
		'embed',
		'feed',
		'page',
		's',
		'search',
		'tag',
		'wp-admin',
		'wp-json',
		'wp-login',
		'wp-login-php',
	);

	if ( $wp_rewrite ) {
		foreach ( array( 'author_base', 'comments_base', 'comments_pagination_base', 'feed_base', 'pagination_base', 'search_base' ) as $property ) {
			if ( ! empty( $wp_rewrite->$property ) ) {
				$reserved[] = $wp_rewrite->$property;
			}
		}

		$rewrite_rules = $wp_rewrite->wp_rewrite_rules();
		if ( is_array( $rewrite_rules ) ) {
			foreach ( $rewrite_rules as $regex => $query ) {
				if ( false !== strpos( (string) $query, 'cozy_journal_write=1' ) ) {
					continue;
				}

				if ( preg_match( '/^\^?([a-z0-9_-]+)(?:\/|\$|\?)/i', (string) $regex, $matches ) ) {
					$reserved[] = $matches[1];
				}
			}
		}
	}

	if ( function_exists( 'rest_get_url_prefix' ) ) {
		$reserved[] = rest_get_url_prefix();
	}

	foreach ( array( get_option( 'category_base', 'category' ), get_option( 'tag_base', 'tag' ) ) as $base ) {
		if ( $base ) {
			$reserved[] = $base;
		}
	}

	foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
		if ( is_array( $post_type->rewrite ) && ! empty( $post_type->rewrite['slug'] ) ) {
			$reserved[] = $post_type->rewrite['slug'];
		}

		if ( is_string( $post_type->has_archive ) ) {
			$reserved[] = $post_type->has_archive;
		}
	}

	foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $taxonomy ) {
		if ( is_array( $taxonomy->rewrite ) && ! empty( $taxonomy->rewrite['slug'] ) ) {
			$reserved[] = $taxonomy->rewrite['slug'];
		}
	}

	$reserved = array_map(
		function( $slug ) {
			$slug = trim( (string) $slug, '/' );
			$slug = strtok( $slug, '/' );
			return cozy_journal_normalize_writing_slug( $slug );
		},
		$reserved
	);

	return array_values( array_unique( array_filter( $reserved ) ) );
}

/**
 * Validates a proposed writing path against WordPress routes and content.
 *
 * @param mixed $value Raw path value.
 * @return string|WP_Error
 */
function cozy_journal_validate_writing_slug( $value ) {
	$slug = cozy_journal_normalize_writing_slug( $value );

	if ( '' === $slug || 0 === strpos( $slug, 'wp-' ) ) {
		return new WP_Error( 'invalid_writing_slug', __( '写作页面路径必须包含英文小写字母、数字、短横线或下划线。', 'cozy-journal' ) );
	}

	if ( preg_match( '/^\d+$/D', $slug ) ) {
		return new WP_Error( 'writing_slug_conflict', __( '写作页面路径不能只使用数字，以免覆盖日期归档或分页地址。', 'cozy-journal' ) );
	}

	if ( in_array( $slug, cozy_journal_get_reserved_writing_slugs(), true ) ) {
		return new WP_Error( 'writing_slug_conflict', __( '这个路径已被 WordPress、文章类型或分类归档使用，请换一个名称。', 'cozy-journal' ) );
	}

	if ( get_page_by_path( $slug, OBJECT, 'page' ) ) {
		return new WP_Error( 'writing_slug_conflict', __( '这个路径已经有同名页面，请先换一个写作路径。', 'cozy-journal' ) );
	}

	$permalink_structure = trim( (string) get_option( 'permalink_structure' ), '/' );
	if ( 0 === strpos( $permalink_structure, '%postname%' ) && get_page_by_path( $slug, OBJECT, 'post' ) ) {
		return new WP_Error( 'writing_slug_conflict', __( '这个路径已经有同名文章，请先换一个写作路径。', 'cozy-journal' ) );
	}

	return $slug;
}

/**
 * Returns the configured writing desk path segment.
 *
 * @return string
 */
function cozy_journal_get_writing_slug() {
	return cozy_journal_sanitize_writing_slug( get_theme_mod( 'cozy_journal_writing_slug', 'write' ) );
}

/**
 * Returns the public writing desk URL.
 *
 * @param array $args Optional query arguments.
 * @return string
 */
function cozy_journal_get_write_url( $args = array() ) {
	if ( get_option( 'permalink_structure' ) ) {
		$url = home_url( '/' . cozy_journal_get_writing_slug() . '/' );
	} else {
		$url = add_query_arg( 'cozy_journal_write', '1', home_url( '/' ) );
	}

	return $args ? add_query_arg( $args, $url ) : $url;
}

/**
 * Registers the configured virtual writing page.
 */
function cozy_journal_register_writing_rewrite() {
	$slug = preg_quote( cozy_journal_get_writing_slug(), '#' );
	add_rewrite_rule( '^' . $slug . '/?$', 'index.php?cozy_journal_write=1', 'top' );
}
add_action( 'init', 'cozy_journal_register_writing_rewrite' );

/**
 * Resolves the current writing path immediately, even before rules refresh.
 *
 * It also rejects a stale previous writing path after the setting changes.
 *
 * @param WP $wp Current WordPress environment instance.
 */
function cozy_journal_resolve_writing_request( $wp ) {
	if ( ! get_option( 'permalink_structure' ) ) {
		if ( isset( $wp->query_vars['cozy_journal_write'] ) ) {
			$other_query_vars = $wp->query_vars;
			unset( $other_query_vars['cozy_journal_write'] );

			if ( $other_query_vars ) {
				unset( $wp->query_vars['cozy_journal_write'] );
			}
		}

		return;
	}

	$request = trim( (string) $wp->request, '/' );
	if ( cozy_journal_get_writing_slug() === $request ) {
		$wp->query_vars['cozy_journal_write'] = '1';
		unset( $wp->query_vars['error'] );
	} elseif (
		isset( $wp->query_vars['cozy_journal_write'] ) &&
		false !== strpos( (string) $wp->matched_query, 'cozy_journal_write=1' )
	) {
		unset( $wp->query_vars['cozy_journal_write'] );
		$wp->query_vars['error'] = '404';
	} elseif ( isset( $wp->query_vars['cozy_journal_write'] ) ) {
		unset( $wp->query_vars['cozy_journal_write'] );
	}
}
add_action( 'parse_request', 'cozy_journal_resolve_writing_request', 1 );

/**
 * Adds the writing desk query variable.
 *
 * @param string[] $vars Public query variables.
 * @return string[]
 */
function cozy_journal_writing_query_vars( $vars ) {
	$vars[] = 'cozy_journal_write';
	return $vars;
}
add_filter( 'query_vars', 'cozy_journal_writing_query_vars' );

/**
 * Returns the signature of the current writing rewrite configuration.
 *
 * @return string
 */
function cozy_journal_get_writing_rewrite_signature() {
	return COZY_JOURNAL_VERSION . ':' . cozy_journal_get_writing_slug();
}

/**
 * Flushes rewrite rules when the feature version or path changes.
 */
function cozy_journal_maybe_flush_writing_rewrite() {
	$signature        = cozy_journal_get_writing_rewrite_signature();
	$stored_signature = get_option( 'cozy_journal_rewrite_signature', '' );

	if ( $signature !== $stored_signature ) {
		cozy_journal_register_writing_rewrite();
		flush_rewrite_rules( false );
		update_option( 'cozy_journal_rewrite_signature', $signature, false );
	}
}
add_action( 'admin_init', 'cozy_journal_maybe_flush_writing_rewrite' );

/**
 * Deletes the cached writing rewrite signature and WordPress rules.
 */
function cozy_journal_invalidate_writing_rewrite() {
	delete_option( 'cozy_journal_rewrite_signature' );
	delete_option( 'rewrite_rules' );
}

/**
 * Sanitizes direct theme-mod updates and invalidates rules before first save.
 *
 * @param mixed $value     New theme modification value.
 * @param mixed $old_value Previous theme modification value.
 * @return string
 */
function cozy_journal_prepare_writing_slug_update( $value, $old_value ) {
	$new_slug = cozy_journal_sanitize_writing_slug( $value );
	$old_slug = cozy_journal_sanitize_writing_slug( $old_value ? $old_value : 'write' );

	if ( $old_slug !== $new_slug ) {
		cozy_journal_invalidate_writing_rewrite();
	}

	return $new_slug;
}
add_filter( 'pre_set_theme_mod_cozy_journal_writing_slug', 'cozy_journal_prepare_writing_slug_update', 10, 2 );

/**
 * Marks rewrite rules stale when the writing path theme modification changes.
 *
 * @param mixed $old_value Previous theme modifications.
 * @param mixed $value     Updated theme modifications.
 */
function cozy_journal_mark_writing_rewrite_stale( $old_value, $value ) {
	$old_mods = is_array( $old_value ) ? $old_value : array();
	$new_mods = is_array( $value ) ? $value : array();
	$old_slug = cozy_journal_sanitize_writing_slug( isset( $old_mods['cozy_journal_writing_slug'] ) ? $old_mods['cozy_journal_writing_slug'] : 'write' );
	$new_slug = cozy_journal_sanitize_writing_slug( isset( $new_mods['cozy_journal_writing_slug'] ) ? $new_mods['cozy_journal_writing_slug'] : 'write' );

	if ( $old_slug !== $new_slug ) {
		cozy_journal_invalidate_writing_rewrite();
	}
}
add_action( 'update_option_theme_mods_' . get_option( 'stylesheet' ), 'cozy_journal_mark_writing_rewrite_stale', 10, 2 );

/**
 * Reports whether the front-end editor is enabled.
 *
 * @return bool
 */
function cozy_journal_writing_desk_enabled() {
	return (bool) get_theme_mod( 'cozy_journal_enable_writing_desk', true );
}

/**
 * Returns the post type object used by the writing desk.
 *
 * @return WP_Post_Type|null
 */
function cozy_journal_get_writing_post_type_object() {
	return get_post_type_object( 'post' );
}

/**
 * Reports whether the current user may create posts from the writing desk.
 *
 * @return bool
 */
function cozy_journal_current_user_can_create_writing_posts() {
	$post_type = cozy_journal_get_writing_post_type_object();

	return $post_type && current_user_can( $post_type->cap->create_posts );
}

/**
 * Reports whether the current user may publish posts from the writing desk.
 *
 * @return bool
 */
function cozy_journal_current_user_can_publish_writing_posts() {
	$post_type = cozy_journal_get_writing_post_type_object();

	return $post_type && current_user_can( $post_type->cap->publish_posts );
}

/**
 * Reports whether the current user may open the requested writing screen.
 *
 * Users without post-creation capability may still open a specific post when
 * an object-level capability filter explicitly grants access to that post.
 *
 * @return bool
 */
function cozy_journal_current_user_can_access_writing_desk() {
	if ( ! is_user_logged_in() ) {
		return false;
	}

	$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $post_id ) {
		$post = get_post( $post_id );
		if ( $post && 'post' === $post->post_type && current_user_can( 'edit_post', $post_id ) ) {
			return true;
		}
	}

	return cozy_journal_current_user_can_create_writing_posts();
}

/**
 * Reports whether the current user may assign terms in one writing taxonomy.
 *
 * @param string $taxonomy Taxonomy name.
 * @return bool
 */
function cozy_journal_current_user_can_assign_writing_terms( $taxonomy ) {
	$taxonomy_object = get_taxonomy( $taxonomy );

	return $taxonomy_object && current_user_can( $taxonomy_object->cap->assign_terms );
}

/**
 * Returns whether the current request is the virtual writing desk.
 *
 * @return bool
 */
function cozy_journal_is_writing_desk() {
	return '1' === (string) get_query_var( 'cozy_journal_write' );
}

/**
 * Hides the private writing route from logged-out visitors.
 *
 * Logged-out requests are sent to the site's /404/ URL before the writing
 * template or its login form can be rendered.
 */
function cozy_journal_redirect_logged_out_writers() {
	if ( ! cozy_journal_is_writing_desk() || is_user_logged_in() ) {
		return;
	}

	nocache_headers();
	wp_safe_redirect( home_url( '/404/' ), 302, 'Cozy Journal' );
	exit;
}
add_action( 'template_redirect', 'cozy_journal_redirect_logged_out_writers', -1 );

/**
 * Keeps the private writing screen out of caches and search indexes.
 */
function cozy_journal_writing_request_headers() {
	if ( cozy_journal_is_writing_desk() ) {
		nocache_headers();
		send_frame_options_header();
		header( 'X-Robots-Tag: noindex, nofollow', true );
	}
}
add_action( 'template_redirect', 'cozy_journal_writing_request_headers', 0 );

/**
 * Prevents WordPress from canonicalizing the virtual /write/ route.
 *
 * @param string|false $redirect_url Proposed canonical URL.
 * @return string|false
 */
function cozy_journal_writing_disable_canonical_redirect( $redirect_url ) {
	return cozy_journal_is_writing_desk() ? false : $redirect_url;
}
add_filter( 'redirect_canonical', 'cozy_journal_writing_disable_canonical_redirect' );

/**
 * Loads the writing desk template or a 404 when the feature is disabled.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function cozy_journal_writing_template( $template ) {
	if ( ! cozy_journal_is_writing_desk() ) {
		return $template;
	}

	if ( ! cozy_journal_writing_desk_enabled() ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
		return get_404_template();
	}

	return get_template_directory() . '/writing-desk.php';
}
add_filter( 'template_include', 'cozy_journal_writing_template', 99 );

/**
 * Sets a descriptive browser title for the virtual page.
 *
 * @param string $title Existing document title.
 * @return string
 */
function cozy_journal_writing_document_title( $title ) {
	if ( cozy_journal_is_writing_desk() ) {
		return sprintf( '%1$s — %2$s', __( '手账写作台', 'cozy-journal' ), get_bloginfo( 'name' ) );
	}

	return $title;
}
add_filter( 'pre_get_document_title', 'cozy_journal_writing_document_title' );

/**
 * Adds writing desk body classes.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function cozy_journal_writing_body_classes( $classes ) {
	if ( cozy_journal_is_writing_desk() ) {
		$classes[] = 'cozy-journal-writing-page';
	}

	return $classes;
}
add_filter( 'body_class', 'cozy_journal_writing_body_classes' );

/**
 * Returns a transient key for one restored writing form.
 *
 * @param int    $user_id Current user ID.
 * @param string $token   One-time restoration token.
 * @return string
 */
function cozy_journal_get_writing_form_state_key( $user_id, $token ) {
	return 'cj_write_form_' . absint( $user_id ) . '_' . sanitize_key( $token );
}

/**
 * Stores sanitized form values before redirecting after a save failure.
 *
 * @param array $input   Submitted form values.
 * @param int   $post_id Existing post ID, if any.
 * @return string One-time restoration token.
 */
function cozy_journal_store_writing_form_state( $input, $post_id = 0 ) {
	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return '';
	}

	$token                 = strtolower( wp_generate_password( 20, false, false ) );
	$can_assign_categories = cozy_journal_current_user_can_assign_writing_terms( 'category' );
	$can_assign_tags       = cozy_journal_current_user_can_assign_writing_terms( 'post_tag' );
	$state                 = array(
		'user_id'             => $user_id,
		'post_id'             => absint( $post_id ),
		'title'               => isset( $input['cozy_journal_title'] ) ? sanitize_text_field( $input['cozy_journal_title'] ) : '',
		'content'             => isset( $input['cozy_journal_content'] ) ? wp_kses_post( $input['cozy_journal_content'] ) : '',
		'excerpt'             => isset( $input['cozy_journal_excerpt'] ) ? sanitize_textarea_field( $input['cozy_journal_excerpt'] ) : '',
		'categories'          => $can_assign_categories && isset( $input['cozy_journal_categories'] ) ? cozy_journal_sanitize_writing_categories( $input['cozy_journal_categories'] ) : array(),
		'tags'                => $can_assign_tags && isset( $input['cozy_journal_tags'] ) ? sanitize_text_field( $input['cozy_journal_tags'] ) : '',
		'comments_open'       => ! empty( $input['cozy_journal_comments_open'] ),
		'remove_thumbnail'    => ! empty( $input['cozy_journal_remove_thumbnail'] ),
		'had_featured_upload' => ! empty( $_FILES['cozy_journal_featured_image']['name'] ),
	);

	set_transient( cozy_journal_get_writing_form_state_key( $user_id, $token ), $state, 10 * MINUTE_IN_SECONDS );

	return $token;
}

/**
 * Returns and consumes one restored writing form state.
 *
 * @param int $post_id Requested post ID.
 * @return array
 */
function cozy_journal_take_writing_form_state( $post_id = 0 ) {
	$token = isset( $_GET['write_state'] ) ? sanitize_key( wp_unslash( $_GET['write_state'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! preg_match( '/^[a-z0-9]{20}$/D', $token ) ) {
		return array();
	}

	$user_id = get_current_user_id();
	$key     = cozy_journal_get_writing_form_state_key( $user_id, $token );
	$state   = get_transient( $key );

	if (
		! is_array( $state ) ||
		$user_id !== absint( isset( $state['user_id'] ) ? $state['user_id'] : 0 ) ||
		absint( $post_id ) !== absint( isset( $state['post_id'] ) ? $state['post_id'] : 0 )
	) {
		return array();
	}

	delete_transient( $key );

	return $state;
}

/**
 * Loads WordPress post-lock helpers on front-end requests.
 */
function cozy_journal_load_post_lock_functions() {
	if ( ! function_exists( 'wp_check_post_lock' ) || ! function_exists( 'wp_set_post_lock' ) ) {
		require_once ABSPATH . 'wp-admin/includes/post.php';
	}
}

/**
 * Claims an existing post for the current writing session.
 *
 * @param int $post_id Post ID.
 * @return true|WP_Error
 */
function cozy_journal_claim_writing_post_lock( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return true;
	}

	cozy_journal_load_post_lock_functions();
	$locked_user_id = wp_check_post_lock( $post_id );

	if ( $locked_user_id ) {
		$locked_user = get_userdata( $locked_user_id );
		$name        = __( '另一位用户', 'cozy-journal' );

		if ( get_theme_mod( 'cozy_journal_writing_show_lock_user_name', false ) && $locked_user ) {
			$name = $locked_user->display_name;
		}

		return new WP_Error(
			'post_locked',
			sprintf(
				/* translators: %s: Display name of the user editing the post. */
				__( '这篇文章正在由 %s 编辑。请稍后重试，避免覆盖对方的内容。', 'cozy-journal' ),
				$name
			)
		);
	}

	wp_set_post_lock( $post_id );

	return true;
}

/**
 * Returns the requested post when the current user may edit it.
 *
 * @return WP_Post|WP_Error|null
 */
function cozy_journal_get_requested_writing_post() {
	$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $post_id ) {
		return null;
	}

	$post = get_post( $post_id );
	if ( ! $post || 'post' !== $post->post_type ) {
		return new WP_Error( 'invalid_post', __( '没有找到要编辑的文章。', 'cozy-journal' ) );
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return new WP_Error( 'forbidden_post', __( '你没有权限编辑这篇文章。', 'cozy-journal' ) );
	}

	$lock = cozy_journal_claim_writing_post_lock( $post_id );
	if ( is_wp_error( $lock ) ) {
		return $lock;
	}

	return $post;
}

/**
 * Enqueues front-end editor styles, scripts and WordPress editor assets.
 */
function cozy_journal_writing_assets() {
	if ( ! cozy_journal_is_writing_desk() || ! cozy_journal_writing_desk_enabled() ) {
		return;
	}

	wp_enqueue_style(
		'cozy-journal-writing-desk',
		get_template_directory_uri() . '/assets/css/writing-desk.css',
		array( 'cozy-journal-style' ),
		COZY_JOURNAL_VERSION
	);

	if ( cozy_journal_current_user_can_access_writing_desk() ) {
		if ( function_exists( 'wp_enqueue_editor' ) ) {
			wp_enqueue_editor();
		}

		$requested_post = cozy_journal_get_requested_writing_post();
		$post_id        = $requested_post instanceof WP_Post ? $requested_post->ID : 0;
		$post_status    = $requested_post instanceof WP_Post ? $requested_post->post_status : 'draft';
		$post_locked    = is_wp_error( $requested_post ) && 'post_locked' === $requested_post->get_error_code();
		$interval       = absint( get_theme_mod( 'cozy_journal_writing_autosave_interval', 30 ) );
		$interval       = in_array( $interval, array( 0, 30, 60, 120 ), true ) ? $interval : 30;

		if ( current_user_can( 'upload_files' ) ) {
			wp_enqueue_media( $post_id ? array( 'post' => $post_id ) : array() );
		}

		wp_enqueue_script(
			'cozy-journal-writing-desk',
			get_template_directory_uri() . '/assets/js/writing-desk.js',
			array( 'jquery' ),
			COZY_JOURNAL_VERSION,
			true
		);
		wp_localize_script(
			'cozy-journal-writing-desk',
			'cozyJournalWriting',
			array(
				'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
				'nonce'            => wp_create_nonce( 'cozy_journal_frontend_autosave' ),
				'postId'           => $post_id,
				'postStatus'       => $post_status,
				'postLocked'       => $post_locked,
				'autosaveInterval' => $interval,
				'autosaveTimeout'  => 15000,
				'lockInterval'     => 60,
				'writeUrl'         => cozy_journal_get_write_url(),
				'labels'           => array(
					'saving'        => __( '正在悄悄保存……', 'cozy-journal' ),
					'saved'         => __( '已保存到草稿箱', 'cozy-journal' ),
					'failed'        => __( '自动保存失败，请手动保存', 'cozy-journal' ),
					'timeout'       => __( '自动保存请求超时，请检查网络后重试', 'cozy-journal' ),
					'sessionExpired' => __( '登录状态已过期，请刷新页面后重新登录', 'cozy-journal' ),
					'unsaved'       => __( '有尚未保存的修改', 'cozy-journal' ),
					'mediaUnsaved'  => __( '文字已保存，特色图片仍需手动保存', 'cozy-journal' ),
					'changedAgain'  => __( '保存期间又有新修改，正在等待下一次保存', 'cozy-journal' ),
					'published'     => __( '已发布文章请使用手动保存', 'cozy-journal' ),
					'autosaveBusy'  => __( '正在自动保存，请稍等一小会儿再操作', 'cozy-journal' ),
					'previewOpened' => __( '预览已在新标签页打开', 'cozy-journal' ),
					'leaveWarning'  => __( '还有内容没有保存，确定离开吗？', 'cozy-journal' ),
					'missingTitle'  => __( '请先给这一页手账写一个标题。', 'cozy-journal' ),
					'missingContent' => __( '正文还是空白的，请写下一点内容。', 'cozy-journal' ),
					'lockFailed'    => __( '文章编辑锁刷新失败；手动保存前请确认没有其他人正在编辑。', 'cozy-journal' ),
					'words'         => __( '字', 'cozy-journal' ),
				),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'cozy_journal_writing_assets', 20 );

/**
 * Adds a quick writing link to the WordPress admin toolbar.
 *
 * @param WP_Admin_Bar $wp_admin_bar Toolbar instance.
 */
function cozy_journal_writing_admin_bar_link( $wp_admin_bar ) {
	if ( ! cozy_journal_writing_desk_enabled() || ! cozy_journal_current_user_can_create_writing_posts() ) {
		return;
	}

	$wp_admin_bar->add_node(
		array(
			'id'    => 'cozy-journal-write',
			'title' => '<span class="ab-icon dashicons dashicons-edit-page"></span>' . esc_html__( '写手账', 'cozy-journal' ),
			'href'  => cozy_journal_get_write_url(),
			'meta'  => array( 'title' => __( '打开前台手账写作台', 'cozy-journal' ) ),
		)
	);
}
add_action( 'admin_bar_menu', 'cozy_journal_writing_admin_bar_link', 85 );

/**
 * Normalizes submitted categories.
 *
 * @param mixed $raw_categories Raw category IDs.
 * @return int[]
 */
function cozy_journal_sanitize_writing_categories( $raw_categories ) {
	$categories = array_filter( array_map( 'absint', (array) $raw_categories ) );

	return array_values(
		array_filter(
			$categories,
			function( $category_id ) {
				return term_exists( $category_id, 'category' );
			}
		)
	);
}

/**
 * Builds a safe post array from front-end editor input.
 *
 * @param array  $input      Raw form or AJAX input.
 * @param int    $post_id    Existing post ID, if any.
 * @param string $post_status Status to save.
 * @param bool   $allow_placeholder Whether an empty title may use a placeholder.
 * @return array|WP_Error
 */
function cozy_journal_prepare_writing_post_data( $input, $post_id, $post_status, $allow_placeholder = false ) {
	$title   = isset( $input['cozy_journal_title'] ) ? sanitize_text_field( $input['cozy_journal_title'] ) : '';
	$content = isset( $input['cozy_journal_content'] ) ? wp_kses_post( $input['cozy_journal_content'] ) : '';

	if ( '' === $title && $allow_placeholder ) {
		$title = __( '未命名手账', 'cozy-journal' );
	}

	if ( '' === $title ) {
		return new WP_Error( 'missing_title', __( '请先给这一页手账写一个标题。', 'cozy-journal' ) );
	}

	if ( '' === trim( wp_strip_all_tags( $content ) ) && ! $allow_placeholder ) {
		return new WP_Error( 'missing_content', __( '正文还是空白的，请写下一点内容。', 'cozy-journal' ) );
	}

	$data = array(
		'ID'             => $post_id,
		'post_type'      => 'post',
		'post_title'     => $title,
		'post_content'   => $content,
		'post_excerpt'   => isset( $input['cozy_journal_excerpt'] ) ? sanitize_textarea_field( $input['cozy_journal_excerpt'] ) : '',
		'post_status'    => $post_status,
		'comment_status' => ! empty( $input['cozy_journal_comments_open'] ) ? 'open' : 'closed',
	);

	if ( cozy_journal_current_user_can_assign_writing_terms( 'category' ) ) {
		$categories = isset( $input['cozy_journal_categories'] )
			? cozy_journal_sanitize_writing_categories( $input['cozy_journal_categories'] )
			: array();

		if ( ! $categories ) {
			$categories = array( absint( get_option( 'default_category' ) ) );
		}

		$data['post_category'] = $categories;
	}

	if ( cozy_journal_current_user_can_assign_writing_terms( 'post_tag' ) ) {
		$tags               = isset( $input['cozy_journal_tags'] ) ? sanitize_text_field( $input['cozy_journal_tags'] ) : '';
		$data['tags_input'] = str_replace( array( '，', '、', ';', '；' ), ',', $tags );
	}

	if ( ! $post_id ) {
		$data['post_author'] = get_current_user_id();
	}

	return $data;
}

/**
 * Creates a post or safely updates an existing post without resetting fields
 * that are not exposed by the front-end writing form.
 *
 * @param array $post_data Sanitized, unslashed post data.
 * @param int  $post_id         Existing post ID, if any.
 * @param bool $create_revision Whether WordPress should create a revision.
 * @return int|WP_Error
 */
function cozy_journal_persist_writing_post( $post_data, $post_id = 0, $create_revision = true ) {
	$post_id   = absint( $post_id );
	$post_data = wp_slash( $post_data );

	if ( $post_id ) {
		$post_data['ID'] = $post_id;
		if ( $create_revision ) {
			return wp_update_post( $post_data, true );
		}

		$revision_priority = has_action( 'post_updated', 'wp_save_post_revision' );
		if ( false !== $revision_priority ) {
			remove_action( 'post_updated', 'wp_save_post_revision', $revision_priority );
		}

		try {
			$result = wp_update_post( $post_data, true );
		} finally {
			if ( false !== $revision_priority ) {
				add_action( 'post_updated', 'wp_save_post_revision', $revision_priority, 3 );
			}
		}

		return $result;
	}

	return wp_insert_post( $post_data, true );
}

/**
 * Redirects back to the writing desk with a status code.
 *
 * @param int    $post_id    Post ID.
 * @param string $notice     Notice code.
 * @param string $error      Error code.
 * @param string $state_token One-time restored form token.
 */
function cozy_journal_writing_redirect( $post_id = 0, $notice = '', $error = '', $state_token = '' ) {
	$args = array();
	if ( $post_id ) {
		$args['post_id'] = absint( $post_id );
	}
	if ( $notice ) {
		$args['write_notice'] = sanitize_key( $notice );
	}
	if ( $error ) {
		$args['write_error'] = sanitize_key( $error );
	}
	if ( $state_token ) {
		$args['write_state'] = sanitize_key( $state_token );
	}

	wp_safe_redirect( cozy_journal_get_write_url( $args ) );
	exit;
}

/**
 * Handles manual draft, preview and publish actions.
 */
function cozy_journal_save_frontend_post() {
	if ( ! cozy_journal_writing_desk_enabled() || ! is_user_logged_in() ) {
		wp_die( esc_html__( '你没有权限从前台写文章。', 'cozy-journal' ) );
	}

	check_admin_referer( 'cozy_journal_save_frontend_post' );

	$input   = wp_unslash( $_POST );
	$post_id = isset( $input['cozy_journal_post_id'] ) ? absint( $input['cozy_journal_post_id'] ) : 0;
	if ( isset( $input['cozy_journal_submit'] ) ) {
		$submit_type = sanitize_key( $input['cozy_journal_submit'] );
	} elseif ( isset( $input['cozy_journal_submit_fallback'] ) ) {
		$submit_type = sanitize_key( $input['cozy_journal_submit_fallback'] );
	} else {
		$submit_type = 'draft';
	}
	$existing = $post_id ? get_post( $post_id ) : null;

	if ( $post_id && ( ! $existing || 'post' !== $existing->post_type || ! current_user_can( 'edit_post', $post_id ) ) ) {
		wp_die( esc_html__( '你不能编辑这篇文章。', 'cozy-journal' ) );
	}

	if ( ! $post_id && ! cozy_journal_current_user_can_create_writing_posts() ) {
		wp_die( esc_html__( '你没有权限创建新文章。', 'cozy-journal' ) );
	}

	$manual_statuses = array( 'publish', 'private', 'future' );
	if ( 'preview' === $submit_type && $existing && in_array( $existing->post_status, $manual_statuses, true ) ) {
		$preview_url = 'publish' === $existing->post_status
			? get_permalink( $existing )
			: get_preview_post_link( $existing );

		wp_safe_redirect( $preview_url ? $preview_url : get_permalink( $existing ) );
		exit;
	}

	if ( $existing ) {
		$lock = cozy_journal_claim_writing_post_lock( $post_id );
		if ( is_wp_error( $lock ) ) {
			$state_token = cozy_journal_store_writing_form_state( $input, $post_id );
			cozy_journal_writing_redirect( $post_id, '', 'post_locked', $state_token );
		}
	}

	if ( 'publish' === $submit_type ) {
		if ( cozy_journal_current_user_can_publish_writing_posts() ) {
			$post_status = 'publish';
		} elseif ( $existing && in_array( $existing->post_status, array( 'publish', 'future' ), true ) ) {
			$post_status = $existing->post_status;
		} else {
			$post_status = 'pending';
		}
	} elseif ( $existing && in_array( $existing->post_status, $manual_statuses, true ) ) {
		$post_status = $existing->post_status;
	} elseif ( 'preview' === $submit_type && $existing && 'pending' === $existing->post_status ) {
		$post_status = 'pending';
	} else {
		$post_status = 'draft';
	}

	$post_data = cozy_journal_prepare_writing_post_data( $input, $post_id, $post_status );
	if ( is_wp_error( $post_data ) ) {
		$state_token = cozy_journal_store_writing_form_state( $input, $post_id );
		cozy_journal_writing_redirect( $post_id, '', $post_data->get_error_code(), $state_token );
	}

	$saved_post_id = cozy_journal_persist_writing_post( $post_data, $post_id );
	if ( is_wp_error( $saved_post_id ) ) {
		$state_token = cozy_journal_store_writing_form_state( $input, $post_id );
		cozy_journal_writing_redirect( $post_id, '', 'save_failed', $state_token );
	}

	cozy_journal_claim_writing_post_lock( $saved_post_id );

	$media_error        = false;
	$has_featured_upload =
		get_theme_mod( 'cozy_journal_allow_featured_upload', true ) &&
		current_user_can( 'upload_files' ) &&
		! empty( $_FILES['cozy_journal_featured_image']['name'] );

	if ( $has_featured_upload ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attachment_id = media_handle_upload( 'cozy_journal_featured_image', $saved_post_id );
		if ( is_wp_error( $attachment_id ) ) {
			$media_error = true;
		} elseif ( ! wp_attachment_is_image( $attachment_id ) ) {
			wp_delete_attachment( $attachment_id, true );
			$media_error = true;
		} elseif ( ! set_post_thumbnail( $saved_post_id, $attachment_id ) ) {
			wp_delete_attachment( $attachment_id, true );
			$media_error = true;
		}
	} elseif ( ! empty( $input['cozy_journal_remove_thumbnail'] ) && current_user_can( 'upload_files' ) ) {
		delete_post_thumbnail( $saved_post_id );
	}

	if ( 'preview' === $submit_type ) {
		$preview_url = get_preview_post_link( $saved_post_id );
		wp_safe_redirect( $preview_url ? $preview_url : get_permalink( $saved_post_id ) );
		exit;
	}

	if ( $media_error ) {
		cozy_journal_writing_redirect( $saved_post_id, 'saved', 'media_failed' );
	}

	if ( 'publish' === $submit_type && 'pending' === $post_status ) {
		cozy_journal_writing_redirect( $saved_post_id, 'pending' );
	}

	cozy_journal_writing_redirect( $saved_post_id, 'publish' === $submit_type ? 'published' : 'saved' );
}
add_action( 'admin_post_cozy_journal_save_frontend_post', 'cozy_journal_save_frontend_post' );

/**
 * Sends a consistent JSON response when the writing session has expired.
 */
function cozy_journal_frontend_ajax_session_expired() {
	wp_send_json_error(
		array(
			'code'    => 'session_expired',
			'message' => __( '登录状态已过期，请刷新页面后重新登录。', 'cozy-journal' ),
		),
		401
	);
}

/**
 * Verifies the writing-desk AJAX nonce without returning WordPress's plain -1 response.
 */
function cozy_journal_verify_frontend_ajax_request() {
	if ( ! is_user_logged_in() || false === check_ajax_referer( 'cozy_journal_frontend_autosave', false, false ) ) {
		cozy_journal_frontend_ajax_session_expired();
	}
}

/**
 * Autosaves new, draft and pending posts from the writing desk.
 */
function cozy_journal_frontend_autosave() {
	cozy_journal_verify_frontend_ajax_request();

	if ( ! cozy_journal_writing_desk_enabled() || ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( '没有写作权限。', 'cozy-journal' ) ), 403 );
	}

	$input   = wp_unslash( $_POST );
	$post_id = isset( $input['cozy_journal_post_id'] ) ? absint( $input['cozy_journal_post_id'] ) : 0;
	$status  = 'draft';

	if ( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post || 'post' !== $post->post_type || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( array( 'message' => __( '不能自动保存这篇文章。', 'cozy-journal' ) ), 403 );
		}

		$lock = cozy_journal_claim_writing_post_lock( $post_id );
		if ( is_wp_error( $lock ) ) {
			wp_send_json_error( array( 'message' => $lock->get_error_message() ), 409 );
		}

		if ( ! in_array( $post->post_status, array( 'draft', 'pending' ), true ) ) {
			wp_send_json_success(
				array(
					'skipped' => true,
					'message' => __( '已发布文章需要手动保存。', 'cozy-journal' ),
				)
			);
		}

		$status = $post->post_status;
	} elseif ( ! cozy_journal_current_user_can_create_writing_posts() ) {
		wp_send_json_error( array( 'message' => __( '没有创建新文章的权限。', 'cozy-journal' ) ), 403 );
	}

	$post_data = cozy_journal_prepare_writing_post_data( $input, $post_id, $status, true );
	if ( is_wp_error( $post_data ) ) {
		wp_send_json_error( array( 'message' => $post_data->get_error_message() ), 400 );
	}

	$saved_post_id = cozy_journal_persist_writing_post( $post_data, $post_id, false );
	if ( is_wp_error( $saved_post_id ) ) {
		wp_send_json_error( array( 'message' => __( '草稿自动保存失败。', 'cozy-journal' ) ), 500 );
	}

	cozy_journal_claim_writing_post_lock( $saved_post_id );

	wp_send_json_success(
		array(
			'postId'   => $saved_post_id,
			'writeUrl' => cozy_journal_get_write_url( array( 'post_id' => $saved_post_id ) ),
			'savedAt'  => current_time( 'H:i' ),
			'message'  => __( '已保存到草稿箱', 'cozy-journal' ),
		)
	);
}
add_action( 'wp_ajax_cozy_journal_frontend_autosave', 'cozy_journal_frontend_autosave' );
add_action( 'wp_ajax_nopriv_cozy_journal_frontend_autosave', 'cozy_journal_frontend_ajax_session_expired' );

/**
 * Refreshes the current user's lock while the writing desk remains open.
 */
function cozy_journal_frontend_refresh_lock() {
	cozy_journal_verify_frontend_ajax_request();

	if ( ! cozy_journal_writing_desk_enabled() || ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( '没有写作权限。', 'cozy-journal' ) ), 403 );
	}

	$post_id = isset( $_POST['cozy_journal_post_id'] ) ? absint( $_POST['cozy_journal_post_id'] ) : 0;
	if ( ! $post_id ) {
		wp_send_json_success();
	}

	$post = get_post( $post_id );
	if ( ! $post || 'post' !== $post->post_type || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( array( 'message' => __( '不能刷新这篇文章的编辑锁。', 'cozy-journal' ) ), 403 );
	}

	$lock = cozy_journal_claim_writing_post_lock( $post_id );
	if ( is_wp_error( $lock ) ) {
		wp_send_json_error( array( 'message' => $lock->get_error_message() ), 409 );
	}

	wp_send_json_success();
}
add_action( 'wp_ajax_cozy_journal_frontend_refresh_lock', 'cozy_journal_frontend_refresh_lock' );
add_action( 'wp_ajax_nopriv_cozy_journal_frontend_refresh_lock', 'cozy_journal_frontend_ajax_session_expired' );
