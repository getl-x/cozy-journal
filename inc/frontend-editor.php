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
 * Sanitizes the single URL segment used by the writing desk.
 *
 * @param mixed $value Raw path value.
 * @return string
 */
function cozy_journal_sanitize_writing_slug( $value ) {
	$slug = strtolower( remove_accents( wp_strip_all_tags( (string) $value ) ) );
	$slug = trim( $slug );
	$slug = trim( $slug, '/' );
	$slug = preg_replace( '/[^a-z0-9_-]+/', '-', $slug );
	$slug = trim( (string) $slug, '-_' );
	$slug = substr( $slug, 0, 60 );

	$reserved = array( '404', 'feed', 'wp-admin', 'wp-json', 'wp-login', 'wp-login-php' );
	if ( ! $slug || in_array( $slug, $reserved, true ) || 0 === strpos( $slug, 'wp-' ) ) {
		return 'write';
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
		return;
	}

	$request = trim( (string) $wp->request, '/' );
	if ( cozy_journal_get_writing_slug() === $request ) {
		$wp->query_vars['cozy_journal_write'] = '1';
	} elseif ( isset( $wp->query_vars['cozy_journal_write'] ) ) {
		unset( $wp->query_vars['cozy_journal_write'] );
		$wp->query_vars['error'] = '404';
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
		delete_option( 'cozy_journal_rewrite_signature' );
		delete_option( 'rewrite_rules' );
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

	if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
		if ( function_exists( 'wp_enqueue_editor' ) ) {
			wp_enqueue_editor();
		}

		$requested_post = cozy_journal_get_requested_writing_post();
		$post_id        = $requested_post instanceof WP_Post ? $requested_post->ID : 0;
		$post_status    = $requested_post instanceof WP_Post ? $requested_post->post_status : 'draft';
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
				'autosaveInterval' => $interval,
				'writeUrl'         => cozy_journal_get_write_url(),
				'labels'           => array(
					'saving'       => __( '正在悄悄保存……', 'cozy-journal' ),
					'saved'        => __( '已保存到草稿箱', 'cozy-journal' ),
					'failed'       => __( '自动保存失败，请手动保存', 'cozy-journal' ),
					'unsaved'      => __( '有尚未保存的修改', 'cozy-journal' ),
					'published'    => __( '已发布文章请使用手动保存', 'cozy-journal' ),
					'autosaveBusy' => __( '正在自动保存，请稍等一小会儿再操作', 'cozy-journal' ),
					'previewOpened' => __( '预览已在新标签页打开', 'cozy-journal' ),
					'leaveWarning' => __( '还有内容没有保存，确定离开吗？', 'cozy-journal' ),
					'words'        => __( '字', 'cozy-journal' ),
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
	if ( ! cozy_journal_writing_desk_enabled() || ! current_user_can( 'edit_posts' ) ) {
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

	if ( ! $title && $allow_placeholder ) {
		$title = __( '未命名手账', 'cozy-journal' );
	}

	if ( ! $title ) {
		return new WP_Error( 'missing_title', __( '请先给这一页手账写一个标题。', 'cozy-journal' ) );
	}

	if ( ! trim( wp_strip_all_tags( $content ) ) && ! $allow_placeholder ) {
		return new WP_Error( 'missing_content', __( '正文还是空白的，请写下一点内容。', 'cozy-journal' ) );
	}

	$categories = isset( $input['cozy_journal_categories'] )
		? cozy_journal_sanitize_writing_categories( $input['cozy_journal_categories'] )
		: array();

	if ( ! $categories ) {
		$categories = array( absint( get_option( 'default_category' ) ) );
	}

	$tags = isset( $input['cozy_journal_tags'] ) ? sanitize_text_field( $input['cozy_journal_tags'] ) : '';
	$tags = str_replace( array( '，', '、', ';', '；' ), ',', $tags );

	$data = array(
		'ID'             => $post_id,
		'post_type'      => 'post',
		'post_title'     => $title,
		'post_content'   => $content,
		'post_excerpt'   => isset( $input['cozy_journal_excerpt'] ) ? sanitize_textarea_field( $input['cozy_journal_excerpt'] ) : '',
		'post_status'    => $post_status,
		'post_category'  => $categories,
		'tags_input'     => $tags,
		'comment_status' => ! empty( $input['cozy_journal_comments_open'] ) ? 'open' : 'closed',
	);

	if ( ! $post_id ) {
		$data['post_author'] = get_current_user_id();
	}

	return $data;
}

/**
 * Redirects back to the writing desk with a status code.
 *
 * @param int    $post_id Post ID.
 * @param string $notice  Notice code.
 * @param string $error   Error code.
 */
function cozy_journal_writing_redirect( $post_id = 0, $notice = '', $error = '' ) {
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

	wp_safe_redirect( cozy_journal_get_write_url( $args ) );
	exit;
}

/**
 * Handles manual draft, preview and publish actions.
 */
function cozy_journal_save_frontend_post() {
	if ( ! cozy_journal_writing_desk_enabled() || ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( '你没有权限从前台写文章。', 'cozy-journal' ) );
	}

	check_admin_referer( 'cozy_journal_save_frontend_post' );

	$input       = wp_unslash( $_POST );
	$post_id     = isset( $input['cozy_journal_post_id'] ) ? absint( $input['cozy_journal_post_id'] ) : 0;
	$submit_type = isset( $input['cozy_journal_submit'] ) ? sanitize_key( $input['cozy_journal_submit'] ) : 'draft';
	$existing    = $post_id ? get_post( $post_id ) : null;

	if ( $post_id && ( ! $existing || 'post' !== $existing->post_type || ! current_user_can( 'edit_post', $post_id ) ) ) {
		wp_die( esc_html__( '你不能编辑这篇文章。', 'cozy-journal' ) );
	}

	$manual_statuses = array( 'publish', 'private', 'future' );
	if ( 'preview' === $submit_type && $existing && in_array( $existing->post_status, $manual_statuses, true ) ) {
		$preview_url = 'publish' === $existing->post_status
			? get_permalink( $existing )
			: get_preview_post_link( $existing );

		wp_safe_redirect( $preview_url ? $preview_url : get_permalink( $existing ) );
		exit;
	}

	if ( 'publish' === $submit_type ) {
		$post_status = current_user_can( 'publish_posts' ) ? 'publish' : 'pending';
	} elseif ( $existing && in_array( $existing->post_status, $manual_statuses, true ) ) {
		$post_status = $existing->post_status;
	} elseif ( 'preview' === $submit_type && $existing && 'pending' === $existing->post_status ) {
		$post_status = 'pending';
	} else {
		$post_status = 'draft';
	}

	$post_data = cozy_journal_prepare_writing_post_data( $input, $post_id, $post_status );
	if ( is_wp_error( $post_data ) ) {
		cozy_journal_writing_redirect( $post_id, '', $post_data->get_error_code() );
	}

	$saved_post_id = wp_insert_post( $post_data, true );
	if ( is_wp_error( $saved_post_id ) ) {
		cozy_journal_writing_redirect( $post_id, '', 'save_failed' );
	}

	if ( ! empty( $input['cozy_journal_remove_thumbnail'] ) && current_user_can( 'upload_files' ) ) {
		delete_post_thumbnail( $saved_post_id );
	}

	$media_error = false;
	if (
		get_theme_mod( 'cozy_journal_allow_featured_upload', true ) &&
		current_user_can( 'upload_files' ) &&
		! empty( $_FILES['cozy_journal_featured_image']['name'] )
	) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attachment_id = media_handle_upload( 'cozy_journal_featured_image', $saved_post_id );
		if ( is_wp_error( $attachment_id ) ) {
			$media_error = true;
		} else {
			set_post_thumbnail( $saved_post_id, $attachment_id );
		}
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
 * Autosaves new, draft and pending posts from the writing desk.
 */
function cozy_journal_frontend_autosave() {
	check_ajax_referer( 'cozy_journal_frontend_autosave' );

	if ( ! cozy_journal_writing_desk_enabled() || ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
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

		if ( ! in_array( $post->post_status, array( 'draft', 'pending' ), true ) ) {
			wp_send_json_success(
				array(
					'skipped' => true,
					'message' => __( '已发布文章需要手动保存。', 'cozy-journal' ),
				)
			);
		}

		$status = $post->post_status;
	}

	$post_data = cozy_journal_prepare_writing_post_data( $input, $post_id, $status, true );
	if ( is_wp_error( $post_data ) ) {
		wp_send_json_error( array( 'message' => $post_data->get_error_message() ), 400 );
	}

	$saved_post_id = wp_insert_post( $post_data, true );
	if ( is_wp_error( $saved_post_id ) ) {
		wp_send_json_error( array( 'message' => __( '草稿自动保存失败。', 'cozy-journal' ) ), 500 );
	}

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
