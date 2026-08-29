<?php
/**
 * Empty state content.
 *
 * @package Cozy_Journal
 */
?>

<section class="no-results journal-paper">
	<div class="empty-sticker" aria-hidden="true">📝</div>
	<h2><?php echo esc_html( get_theme_mod( 'cozy_journal_empty_title', __( '这一页还是空白的', 'cozy-journal' ) ) ); ?></h2>

	<?php if ( is_search() ) : ?>
		<p><?php echo esc_html( get_theme_mod( 'cozy_journal_empty_search_text', __( '没有找到对应内容，换一个词再找找看吧。', 'cozy-journal' ) ) ); ?></p>
		<?php get_search_form(); ?>
	<?php else : ?>
		<p><?php echo esc_html( get_theme_mod( 'cozy_journal_empty_archive_text', __( '等第一篇故事写好，这里就会慢慢热闹起来。', 'cozy-journal' ) ) ); ?></p>
		<?php if ( function_exists( 'cozy_journal_current_user_can_create_writing_posts' ) && cozy_journal_current_user_can_create_writing_posts() ) : ?>
			<a class="journal-button" href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>"><?php echo esc_html( get_theme_mod( 'cozy_journal_first_post_button_text', __( '写下第一篇', 'cozy-journal' ) ) ); ?></a>
		<?php endif; ?>
	<?php endif; ?>
</section>
