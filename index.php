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
		<header class="archive-paper journal-paper">
			<p class="section-kicker"><?php esc_html_e( 'Journal archive', 'cozy-journal' ); ?></p>
			<h1><?php esc_html_e( '一页一页，都是生活', 'cozy-journal' ); ?></h1>
			<p><?php esc_html_e( '这里收好了最近写下的心情、故事与微小闪光。', 'cozy-journal' ); ?></p>
		</header>

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

