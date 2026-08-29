<?php
/**
 * Comments template.
 *
 * @package Cozy_Journal
 */

if ( post_password_required() ) {
	return;
}
?>

<section id="comments" class="comments-area journal-paper">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$cozy_journal_comment_count = get_comments_number();
			if ( 1 === (int) $cozy_journal_comment_count ) {
				esc_html_e( '收到一张暖心小纸条', 'cozy-journal' );
			} else {
				printf(
					/* translators: %s: Number of comments. */
					esc_html__( '收到 %s 张暖心小纸条', 'cozy-journal' ),
					esc_html( number_format_i18n( $cozy_journal_comment_count ) )
				);
			}
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => get_theme_mod( 'cozy_journal_show_comment_avatars', false ) ? 56 : 0,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation(
			array(
				'prev_text' => __( '← 较早的纸条', 'cozy-journal' ),
				'next_text' => __( '较新的纸条 →', 'cozy-journal' ),
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( '这页手账暂时收起了留言纸。', 'cozy-journal' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'        => get_theme_mod( 'cozy_journal_comment_reply_title', __( '留一张小纸条吧', 'cozy-journal' ) ),
			'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h2>',
			'label_submit'       => get_theme_mod( 'cozy_journal_comment_submit_text', __( '贴上纸条', 'cozy-journal' ) ),
			'class_submit'       => 'submit journal-button',
		)
	);
	?>
</section>
