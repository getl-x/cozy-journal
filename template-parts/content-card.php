<?php
/**
 * Post card used on list and archive pages.
 *
 * @package Cozy_Journal
 */

$categories = get_the_category();
$tilt_class = 0 === get_the_ID() % 2 ? 'card-tilt-right' : 'card-tilt-left';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'journal-post-card ' . $tilt_class ); ?>>
	<div class="card-tape" aria-hidden="true"></div>

	<?php if ( get_theme_mod( 'cozy_journal_show_featured_images', true ) ) : ?>
		<?php cozy_journal_post_thumbnail(); ?>
	<?php endif; ?>

	<div class="post-card-body">
		<?php if ( $categories ) : ?>
			<a class="post-card-category" href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>"><?php echo esc_html( $categories[0]->name ); ?></a>
		<?php endif; ?>

		<header class="entry-header">
			<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
			<div class="entry-meta">
				<?php cozy_journal_posted_on(); ?>
				<?php if ( get_theme_mod( 'cozy_journal_show_card_author', false ) ) : ?><?php cozy_journal_posted_by(); ?><?php endif; ?>
			</div>
		</header>

		<?php if ( get_theme_mod( 'cozy_journal_show_excerpt', true ) ) : ?>
			<div class="entry-summary"><?php the_excerpt(); ?></div>
		<?php endif; ?>

		<a class="read-more-link" href="<?php the_permalink(); ?>">
			<?php esc_html_e( '继续读这一页', 'cozy-journal' ); ?> <span aria-hidden="true">→</span>
			<span class="screen-reader-text">：<?php the_title(); ?></span>
		</a>
	</div>
</article>

