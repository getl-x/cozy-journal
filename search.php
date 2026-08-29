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
		<?php if ( get_theme_mod( 'cozy_journal_show_archive_header', true ) ) : ?>
			<header class="archive-paper journal-paper">
				<p class="section-kicker"><?php echo esc_html( get_theme_mod( 'cozy_journal_search_kicker', __( 'Search notes', 'cozy-journal' ) ) ); ?></p>
				<h1 class="archive-title"><?php echo esc_html( str_replace( '{query}', get_search_query(), get_theme_mod( 'cozy_journal_search_title', __( '寻找“{query}”的结果', 'cozy-journal' ) ) ) ); ?></h1>
				<?php get_search_form(); ?>
			</header>
		<?php else : ?>
			<h1 class="screen-reader-text"><?php echo esc_html( str_replace( '{query}', get_search_query(), get_theme_mod( 'cozy_journal_search_title', __( '寻找“{query}”的结果', 'cozy-journal' ) ) ) ); ?></h1>
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
