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
		<div class="error-sticker" aria-hidden="true">🐻</div>
		<p class="error-code">404</p>
		<h1><?php esc_html_e( '这一页好像被风吹走啦', 'cozy-journal' ); ?></h1>
		<p><?php esc_html_e( '别担心，试着搜索一下，或者回到首页继续翻手账吧。', 'cozy-journal' ); ?></p>
		<?php get_search_form(); ?>
		<a class="journal-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '回到手账首页', 'cozy-journal' ); ?> <span aria-hidden="true">→</span></a>
	</section>
</main>

<?php
get_footer();

