<?php
/**
 * Single post template.
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
			get_template_part( 'template-parts/content', 'single' );

			if ( get_theme_mod( 'cozy_journal_show_post_navigation', true ) ) {
				the_post_navigation(
					array(
						'prev_text' => '<span class="nav-subtitle">' . esc_html( get_theme_mod( 'cozy_journal_post_nav_previous', __( '← 前一页手账', 'cozy-journal' ) ) ) . '</span><span class="nav-title">%title</span>',
						'next_text' => '<span class="nav-subtitle">' . esc_html( get_theme_mod( 'cozy_journal_post_nav_next', __( '后一页手账 →', 'cozy-journal' ) ) ) . '</span><span class="nav-title">%title</span>',
					)
				);
			}

			if ( get_theme_mod( 'cozy_journal_show_comments_on_posts', true ) && ( comments_open() || get_comments_number() ) ) {
				comments_template();
			}
		endwhile;
		?>
	</div>

	<?php get_sidebar(); ?>
</main>

<?php
get_footer();
