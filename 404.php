<?php
/**
 * 404 template.
 *
 * @package Cozy_Journal
 */

get_header();
?>

<main id="primary" class="site-main journal-container error-main">
	<section class="error-paper journal-paper">
		<div class="error-sticker" aria-hidden="true"><?php echo esc_html( get_theme_mod( 'cozy_journal_error_sticker', '🐻' ) ); ?></div>
		<p class="error-code">404</p>
		<h1><?php echo esc_html( get_theme_mod( 'cozy_journal_error_title', __( '这一页好像被风吹走啦', 'cozy-journal' ) ) ); ?></h1>
		<p><?php echo esc_html( get_theme_mod( 'cozy_journal_error_description', __( '别担心，试着搜索一下，或者回到首页继续翻手账吧。', 'cozy-journal' ) ) ); ?></p>
		<?php if ( get_theme_mod( 'cozy_journal_show_error_search', true ) ) : ?><?php get_search_form(); ?><?php endif; ?>
		<a class="journal-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( get_theme_mod( 'cozy_journal_error_button_text', __( '回到手账首页', 'cozy-journal' ) ) ); ?> <span aria-hidden="true">→</span></a>
	</section>
</main>

<?php
get_footer();
