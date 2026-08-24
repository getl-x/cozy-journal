<?php
/**
 * Archive template.
 *
 * @package Cozy_Journal
 */

get_header();
?>

<main id="primary" class="site-main journal-container content-with-sidebar">
	<div class="content-column">
		<header class="archive-paper journal-paper">
			<p class="section-kicker"><?php esc_html_e( 'Collected notes', 'cozy-journal' ); ?></p>
			<?php the_archive_title( '<h1 class="archive-title">', '</h1>' ); ?>
			<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
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

