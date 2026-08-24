<?php
/**
 * Page template.
 *
 * @package Cozy_Journal
 */

get_header();
?>

<main id="primary" class="site-main journal-container content-with-sidebar single-layout">
	<div class="content-column">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'journal-paper single-paper page-paper' ); ?>>
				<div class="paper-punches" aria-hidden="true"><i></i><i></i><i></i></div>
				<header class="entry-header single-entry-header">
					<p class="section-kicker"><?php esc_html_e( 'A little page', 'cozy-journal' ); ?></p>
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header>

				<?php if ( get_theme_mod( 'cozy_journal_show_featured_images', true ) && has_post_thumbnail() ) : ?>
					<div class="single-featured-image"><?php the_post_thumbnail( 'full' ); ?></div>
				<?php endif; ?>

				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages(
						array(
							'before' => '<div class="page-links">' . esc_html__( '分页：', 'cozy-journal' ),
							'after'  => '</div>',
						)
					);
					?>
				</div>

				<footer class="entry-footer">
					<?php edit_post_link( esc_html__( '编辑这一页', 'cozy-journal' ) ); ?>
				</footer>
			</article>

			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		endwhile;
		?>
	</div>

	<?php get_sidebar(); ?>
</main>

<?php
get_footer();

