<?php
/**
 * Main index template.
 *
 * @package Cozy_Journal
 */

get_header();
?>

<main id="primary" class="site-main journal-container content-with-sidebar">
	<div class="content-column">
		<?php if ( get_theme_mod( 'cozy_journal_show_archive_header', true ) ) : ?>
			<header class="archive-paper journal-paper">
				<p class="section-kicker"><?php echo esc_html( get_theme_mod( 'cozy_journal_blog_kicker', __( 'Journal archive', 'cozy-journal' ) ) ); ?></p>
				<h1><?php echo esc_html( get_theme_mod( 'cozy_journal_blog_title', __( '一页一页，都是生活', 'cozy-journal' ) ) ); ?></h1>
				<p><?php echo esc_html( get_theme_mod( 'cozy_journal_blog_description', __( '这里收好了最近写下的心情、故事与微小闪光。', 'cozy-journal' ) ) ); ?></p>
			</header>
		<?php else : ?>
			<h1 class="screen-reader-text"><?php echo esc_html( get_theme_mod( 'cozy_journal_blog_title', __( '一页一页，都是生活', 'cozy-journal' ) ) ); ?></h1>
		<?php endif; ?>

		<div class="post-card-grid">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<?php get_template_part( 'template-parts/content', 'card' ); ?>
				<?php endwhile; ?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php endif; ?>
		</div>

		<?php cozy_journal_pagination(); ?>
	</div>

	<?php get_sidebar(); ?>
</main>

<?php
get_footer();
