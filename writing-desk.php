<?php
/**
 * Virtual front-end writing desk template.
 *
 * @package Cozy_Journal
 */

get_header();

$writing_intro = get_theme_mod( 'cozy_journal_writing_intro', __( '安静写下此刻，剩下的交给时间收藏。', 'cozy-journal' ) );
?>

<main id="primary" class="site-main writing-desk-main">
	<div class="writing-desk-container">
		<?php if ( ! is_user_logged_in() ) : ?>
			<section class="writing-access-paper journal-paper">
				<div class="writing-access-sticker" aria-hidden="true">✎</div>
				<p class="section-kicker"><?php esc_html_e( 'Private journal room', 'cozy-journal' ); ?></p>
				<h1><?php esc_html_e( '先登录，再翻开写作页', 'cozy-journal' ); ?></h1>
				<p><?php esc_html_e( '这里是站点作者的前台写作空间。登录成功后，就可以写文章、存草稿、预览并发布到 WordPress。', 'cozy-journal' ); ?></p>
				<div class="writing-login-form">
					<?php
					wp_login_form(
						array(
							'redirect'       => cozy_journal_get_write_url(),
							'remember'       => true,
							'label_username' => __( '用户名或邮箱', 'cozy-journal' ),
							'label_password' => __( '密码', 'cozy-journal' ),
							'label_remember' => __( '记住我', 'cozy-journal' ),
							'label_log_in'   => __( '进入写作台', 'cozy-journal' ),
						)
					);
					?>
				</div>
			</section>
		<?php elseif ( ! current_user_can( 'edit_posts' ) ) : ?>
			<section class="writing-access-paper journal-paper">
				<div class="writing-access-sticker" aria-hidden="true">🔒</div>
				<p class="section-kicker"><?php esc_html_e( 'Permission required', 'cozy-journal' ); ?></p>
				<h1><?php esc_html_e( '这个账号暂时不能写文章', 'cozy-journal' ); ?></h1>
				<p><?php esc_html_e( '请使用作者、编辑或管理员账号登录，或者请站点管理员为当前账号开放文章编辑权限。', 'cozy-journal' ); ?></p>
				<a class="journal-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '返回网站首页', 'cozy-journal' ); ?></a>
			</section>
		<?php else : ?>
			<?php
			$requested_post = cozy_journal_get_requested_writing_post();
			$request_error  = is_wp_error( $requested_post ) ? $requested_post->get_error_message() : '';
			$writing_post   = $requested_post instanceof WP_Post ? $requested_post : null;
			$post_id        = $writing_post ? $writing_post->ID : 0;
			$post_title     = $writing_post ? $writing_post->post_title : '';
			$post_content   = $writing_post ? $writing_post->post_content : '';
			$post_excerpt   = $writing_post ? $writing_post->post_excerpt : '';
			$post_status    = $writing_post ? $writing_post->post_status : 'draft';
			$selected_cats  = $writing_post ? wp_get_post_categories( $post_id ) : array( absint( get_option( 'default_category' ) ) );
			$post_tags      = $writing_post ? wp_get_post_tags( $post_id, array( 'fields' => 'names' ) ) : array();
			$tag_string     = implode( '，', $post_tags );
			$comments_open  = $writing_post ? 'open' === $writing_post->comment_status : true;
			$thumbnail_url  = $writing_post && has_post_thumbnail( $post_id ) ? get_the_post_thumbnail_url( $post_id, 'medium_large' ) : '';
			$manual_status  = $writing_post && in_array( $post_status, array( 'publish', 'private', 'future' ), true );
			$saved_view_url = $manual_status && 'publish' !== $post_status ? get_preview_post_link( $writing_post ) : ( $writing_post ? get_permalink( $writing_post ) : '' );
			$current_user   = wp_get_current_user();
			$categories     = get_categories(
				array(
					'hide_empty' => false,
					'orderby'    => 'name',
					'order'      => 'ASC',
				)
			);
			$status_labels = array(
				'draft'   => __( '草稿', 'cozy-journal' ),
				'pending' => __( '等待审核', 'cozy-journal' ),
				'publish' => __( '已经发布', 'cozy-journal' ),
				'future'  => __( '定时发布', 'cozy-journal' ),
				'private' => __( '私密文章', 'cozy-journal' ),
			);
			$notice_code = isset( $_GET['write_notice'] ) ? sanitize_key( wp_unslash( $_GET['write_notice'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$error_code  = isset( $_GET['write_error'] ) ? sanitize_key( wp_unslash( $_GET['write_error'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$notices = array(
				'saved'     => __( '这一页已经稳稳地收进草稿箱。', 'cozy-journal' ),
				'published' => __( '这一页已经发布，读者现在可以看见它了。', 'cozy-journal' ),
				'pending'   => __( '文章已经提交审核，通过后就会公开。', 'cozy-journal' ),
			);
			$errors = array(
				'missing_title'   => __( '请先给这一页手账写一个标题。', 'cozy-journal' ),
				'missing_content' => __( '正文还是空白的，请写下一点内容。', 'cozy-journal' ),
				'save_failed'     => __( '文章没有保存成功，请稍后再试。', 'cozy-journal' ),
				'media_failed'    => __( '文章已经保存，但特色图片上传失败，请检查文件格式或大小。', 'cozy-journal' ),
			);
			$recent_posts = get_posts(
				array(
					'author'         => get_current_user_id(),
					'post_type'      => 'post',
					'post_status'    => array( 'draft', 'pending', 'publish', 'future', 'private' ),
					'posts_per_page' => 6,
					'orderby'        => 'modified',
					'order'          => 'DESC',
				)
			);
			?>

			<div class="writing-desk-topbar">
				<div>
					<a class="writing-back-link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span aria-hidden="true">←</span> <?php esc_html_e( '返回手账首页', 'cozy-journal' ); ?></a>
					<p><?php echo esc_html( $writing_intro ); ?></p>
				</div>
				<div class="writing-session">
					<?php if ( get_theme_mod( 'cozy_journal_writing_show_user_avatar', false ) ) : ?>
						<span class="writing-author-avatar"><?php echo get_avatar( $current_user->ID, 36 ); ?></span>
					<?php endif; ?>
					<span>
						<small><?php esc_html_e( '正在写作', 'cozy-journal' ); ?></small>
						<strong><?php echo esc_html( get_theme_mod( 'cozy_journal_writing_show_user_name', false ) ? $current_user->display_name : __( '当前用户', 'cozy-journal' ) ); ?></strong>
					</span>
					<a href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>"><?php esc_html_e( '后台文章', 'cozy-journal' ); ?></a>
				</div>
			</div>

			<?php if ( $request_error ) : ?>
				<div class="writing-notice writing-notice-error"><span aria-hidden="true">!</span><p><?php echo esc_html( $request_error ); ?></p></div>
			<?php endif; ?>
			<?php if ( isset( $notices[ $notice_code ] ) ) : ?>
				<div class="writing-notice writing-notice-success">
					<span aria-hidden="true">✓</span><p><?php echo esc_html( $notices[ $notice_code ] ); ?></p>
					<?php if ( $post_id && 'publish' === get_post_status( $post_id ) ) : ?>
						<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( '查看文章', 'cozy-journal' ); ?> →</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( isset( $errors[ $error_code ] ) ) : ?>
				<div class="writing-notice writing-notice-error"><span aria-hidden="true">!</span><p><?php echo esc_html( $errors[ $error_code ] ); ?></p></div>
			<?php endif; ?>

			<form id="cozy-journal-writing-form" class="writing-desk-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
				<input type="hidden" name="action" value="cozy_journal_save_frontend_post">
				<input id="cozy-journal-post-id" type="hidden" name="cozy_journal_post_id" value="<?php echo esc_attr( $post_id ); ?>">
				<?php wp_nonce_field( 'cozy_journal_save_frontend_post' ); ?>

				<div class="writing-editor-column">
					<section class="writing-editor-paper">
						<div class="writing-paper-tape" aria-hidden="true"></div>
						<div class="writing-paper-meta">
							<span><?php echo esc_html( wp_date( 'Y年n月j日 · l' ) ); ?></span>
							<span class="writing-current-status status-<?php echo esc_attr( $post_status ); ?>"><?php echo esc_html( isset( $status_labels[ $post_status ] ) ? $status_labels[ $post_status ] : $post_status ); ?></span>
						</div>

						<label class="screen-reader-text" for="cozy-journal-title"><?php esc_html_e( '文章标题', 'cozy-journal' ); ?></label>
						<input id="cozy-journal-title" class="writing-title-input" type="text" name="cozy_journal_title" value="<?php echo esc_attr( $post_title ); ?>" placeholder="<?php esc_attr_e( '给这一页起个名字……', 'cozy-journal' ); ?>" autocomplete="off">

						<div class="writing-editor-intro">
							<span aria-hidden="true">✦</span>
							<p><?php esc_html_e( '写下故事、插入照片，也可以切换到文本模式整理 HTML。', 'cozy-journal' ); ?></p>
						</div>

						<div class="writing-wordpress-editor">
							<?php
							wp_editor(
								$post_content,
								'cozy_journal_content',
								array(
									'textarea_name' => 'cozy_journal_content',
									'editor_height' => 520,
									'media_buttons' => current_user_can( 'upload_files' ),
									'quicktags'     => true,
									'teeny'         => false,
									'tinymce'       => array(
										'toolbar1' => 'formatselect,bold,italic,blockquote,bullist,numlist,link,unlink,alignleft,aligncenter,alignright,undo,redo',
										'toolbar2' => 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,wp_more,fullscreen,wp_help',
									),
								)
							);
							?>
						</div>

						<div class="writing-editor-footer">
							<span id="cozy-journal-word-count">0 <?php esc_html_e( '字', 'cozy-journal' ); ?></span>
							<span id="cozy-journal-save-status" role="status" aria-live="polite"><?php esc_html_e( '准备好开始写作', 'cozy-journal' ); ?></span>
						</div>
					</section>

					<section class="writing-excerpt-paper">
						<label for="cozy-journal-excerpt"><?php esc_html_e( '这一页的简短摘要', 'cozy-journal' ); ?></label>
						<p><?php esc_html_e( '可以留空，WordPress 会从正文自动截取。', 'cozy-journal' ); ?></p>
						<textarea id="cozy-journal-excerpt" name="cozy_journal_excerpt" rows="4" placeholder="<?php esc_attr_e( '用一两句话介绍这篇文章……', 'cozy-journal' ); ?>"><?php echo esc_textarea( $post_excerpt ); ?></textarea>
					</section>
				</div>

				<aside class="writing-settings-column">
					<section class="writing-settings-card writing-publish-card">
						<div class="writing-card-heading"><span aria-hidden="true">✓</span><h2><?php esc_html_e( '保存与发布', 'cozy-journal' ); ?></h2></div>
						<p><?php echo $manual_status ? esc_html__( '这篇文章已有保存版本，当前修改需要手动保存后才会生效。', 'cozy-journal' ) : esc_html__( '自动保存只处理草稿和待审核文章，发布前仍建议手动保存一次。', 'cozy-journal' ); ?></p>
						<button class="writing-action-button writing-save-draft" type="submit" name="cozy_journal_submit" value="draft">
							<span aria-hidden="true">▣</span><?php echo $manual_status ? esc_html__( '保存文章修改', 'cozy-journal' ) : esc_html__( '保存到草稿箱', 'cozy-journal' ); ?>
						</button>
						<?php if ( $manual_status ) : ?>
							<a class="writing-action-button writing-preview-button" href="<?php echo esc_url( $saved_view_url ); ?>" target="_blank" rel="noopener noreferrer">
								<span aria-hidden="true">◉</span><?php esc_html_e( '查看当前已保存版本', 'cozy-journal' ); ?>
							</a>
							<p class="writing-preview-note"><?php esc_html_e( '为避免误改线上内容，这里只查看已保存版本；请先点“保存文章修改”再查看新内容。', 'cozy-journal' ); ?></p>
						<?php else : ?>
							<button class="writing-action-button writing-preview-button" type="submit" name="cozy_journal_submit" value="preview" formtarget="_blank">
								<span aria-hidden="true">◉</span><?php esc_html_e( '保存并预览这一页', 'cozy-journal' ); ?>
							</button>
						<?php endif; ?>
						<button class="writing-action-button writing-publish-button" type="submit" name="cozy_journal_submit" value="publish" data-writing-confirm="<?php echo esc_attr( current_user_can( 'publish_posts' ) ? __( '确定要把这一页公开发布吗？', 'cozy-journal' ) : __( '确定要把这一页提交审核吗？', 'cozy-journal' ) ); ?>">
							<span aria-hidden="true">→</span><?php echo current_user_can( 'publish_posts' ) ? esc_html__( '发布这一页', 'cozy-journal' ) : esc_html__( '提交审核', 'cozy-journal' ); ?>
						</button>
					</section>

					<section class="writing-settings-card">
						<div class="writing-card-heading"><span aria-hidden="true">⌑</span><h2><?php esc_html_e( '分类', 'cozy-journal' ); ?></h2></div>
						<div class="writing-category-list">
							<?php if ( $categories ) : ?>
								<?php foreach ( $categories as $category ) : ?>
									<label>
										<input type="checkbox" name="cozy_journal_categories[]" value="<?php echo esc_attr( $category->term_id ); ?>" <?php checked( in_array( $category->term_id, $selected_cats, true ) ); ?>>
										<span><?php echo esc_html( $category->name ); ?></span>
									</label>
								<?php endforeach; ?>
							<?php else : ?>
								<p><?php esc_html_e( '还没有创建分类，将使用默认分类。', 'cozy-journal' ); ?></p>
							<?php endif; ?>
						</div>
					</section>

					<section class="writing-settings-card">
						<div class="writing-card-heading"><span aria-hidden="true">#</span><h2><?php esc_html_e( '标签', 'cozy-journal' ); ?></h2></div>
						<label class="screen-reader-text" for="cozy-journal-tags"><?php esc_html_e( '文章标签', 'cozy-journal' ); ?></label>
						<input id="cozy-journal-tags" type="text" name="cozy_journal_tags" value="<?php echo esc_attr( $tag_string ); ?>" placeholder="<?php esc_attr_e( '晚霞，散步，日常', 'cozy-journal' ); ?>">
						<p><?php esc_html_e( '多个标签请用中文或英文逗号隔开。', 'cozy-journal' ); ?></p>
					</section>

					<?php if ( get_theme_mod( 'cozy_journal_allow_featured_upload', true ) && current_user_can( 'upload_files' ) ) : ?>
						<section class="writing-settings-card">
							<div class="writing-card-heading"><span aria-hidden="true">▧</span><h2><?php esc_html_e( '特色图片', 'cozy-journal' ); ?></h2></div>
							<?php if ( $thumbnail_url ) : ?>
								<div class="writing-current-thumbnail"><img src="<?php echo esc_url( $thumbnail_url ); ?>" alt=""><span><?php esc_html_e( '当前特色图片', 'cozy-journal' ); ?></span></div>
								<label class="writing-remove-thumbnail"><input type="checkbox" name="cozy_journal_remove_thumbnail" value="1"> <?php esc_html_e( '移除当前图片', 'cozy-journal' ); ?></label>
							<?php endif; ?>
							<label class="writing-file-picker">
								<input type="file" name="cozy_journal_featured_image" accept="image/jpeg,image/png,image/gif,image/webp">
								<span aria-hidden="true">＋</span>
								<strong><?php echo $thumbnail_url ? esc_html__( '换一张图片', 'cozy-journal' ) : esc_html__( '选择特色图片', 'cozy-journal' ); ?></strong>
								<small><?php esc_html_e( 'JPG、PNG、GIF 或 WebP', 'cozy-journal' ); ?></small>
							</label>
							<div id="cozy-journal-image-preview" class="writing-upload-preview" hidden><img src="" alt=""><button type="button"><?php esc_html_e( '取消选择', 'cozy-journal' ); ?></button></div>
						</section>
					<?php endif; ?>

					<section class="writing-settings-card">
						<div class="writing-card-heading"><span aria-hidden="true">♡</span><h2><?php esc_html_e( '回应', 'cozy-journal' ); ?></h2></div>
						<label class="writing-comment-toggle">
							<input type="checkbox" name="cozy_journal_comments_open" value="1" <?php checked( $comments_open ); ?>>
							<span><?php esc_html_e( '允许读者在这篇文章下留言', 'cozy-journal' ); ?></span>
						</label>
					</section>
				</aside>
			</form>

			<?php if ( $recent_posts ) : ?>
				<section class="writing-recent-section">
					<header>
						<div><p class="section-kicker"><?php esc_html_e( 'My recent notes', 'cozy-journal' ); ?></p><h2><?php esc_html_e( '最近写过的几页', 'cozy-journal' ); ?></h2></div>
						<a href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>"><?php esc_html_e( '管理全部文章', 'cozy-journal' ); ?> →</a>
					</header>
					<div class="writing-recent-grid">
						<?php foreach ( $recent_posts as $recent_post ) : ?>
							<article>
								<span class="recent-status status-<?php echo esc_attr( $recent_post->post_status ); ?>"><?php echo esc_html( isset( $status_labels[ $recent_post->post_status ] ) ? $status_labels[ $recent_post->post_status ] : $recent_post->post_status ); ?></span>
								<h3><?php echo esc_html( $recent_post->post_title ? $recent_post->post_title : __( '未命名手账', 'cozy-journal' ) ); ?></h3>
								<p><?php echo esc_html( get_the_modified_date( 'Y.m.d H:i', $recent_post ) ); ?></p>
								<div>
									<a href="<?php echo esc_url( cozy_journal_get_write_url( array( 'post_id' => $recent_post->ID ) ) ); ?>"><?php esc_html_e( '继续编辑', 'cozy-journal' ); ?></a>
									<?php if ( 'publish' === $recent_post->post_status ) : ?><a href="<?php echo esc_url( get_permalink( $recent_post ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( '查看', 'cozy-journal' ); ?></a><?php endif; ?>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
