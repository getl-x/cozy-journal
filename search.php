<?php
/**
 * Search results template.
 *
 * @package Cozy_Journal
 */

get_header();
?>

<main id="primary" class="site-main journal-container content-with-sidebar">
	<div class="content-column">
		<header class="archive-paper journal-paper">
			<p class="section-kicker"><?php esc_html_e( 'Search notes', 'cozy-journal' ); ?></p>
			<h1 class="archive-title">
				<?php
				printf(
					/* translators: %s: Search query. */
					esc_html__( '寻找“%s”的结果', 'cozy-journal' ),
					'<span>' . esc_html( get_search_query() ) . '</span>'
				);
				?>
			</h1>
			<?php get_search_form(); ?>
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

