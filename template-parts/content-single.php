<?php
/**
 * Single post content.
 *
 * @package Cozy_Journal
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'journal-paper single-paper' ); ?>>
	<div class="paper-punches" aria-hidden="true"><i></i><i></i><i></i></div>
	<div class="single-tape" aria-hidden="true"></div>

	<header class="entry-header single-entry-header">
		<?php
		$categories = get_the_category();
		if ( $categories ) :
			?>
			<a class="post-card-category" href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>"><?php echo esc_html( $categories[0]->name ); ?></a>
		<?php endif; ?>

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		<div class="entry-meta">
			<?php cozy_journal_posted_on(); ?>
			<?php if ( get_theme_mod( 'cozy_journal_show_single_author', false ) ) : ?><?php cozy_journal_posted_by(); ?><?php endif; ?>
			<?php if ( get_comments_number() ) : ?>
				<span class="comments-link"><span aria-hidden="true">♡</span> <?php comments_popup_link( __( '写下回应', 'cozy-journal' ), __( '1 条回应', 'cozy-journal' ), __( '% 条回应', 'cozy-journal' ) ); ?></span>
			<?php endif; ?>
		</div>
	</header>

	<?php if ( get_theme_mod( 'cozy_journal_show_featured_images', true ) && has_post_thumbnail() ) : ?>
		<figure class="single-featured-image">
			<?php the_post_thumbnail( 'full' ); ?>
			<?php if ( wp_get_attachment_caption( get_post_thumbnail_id() ) ) : ?>
				<figcaption><?php echo esc_html( wp_get_attachment_caption( get_post_thumbnail_id() ) ); ?></figcaption>
			<?php endif; ?>
		</figure>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		the_content(
			sprintf(
				/* translators: %s: Post title. */
				wp_kses( __( '继续阅读<span class="screen-reader-text">“%s”</span>', 'cozy-journal' ), array( 'span' => array( 'class' => array() ) ) ),
				wp_kses_post( get_the_title() )
			)
		);
		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( '分页：', 'cozy-journal' ),
				'after'  => '</div>',
			)
		);
		?>
	</div>

	<footer class="entry-footer single-entry-footer">
		<?php cozy_journal_entry_footer(); ?>
	</footer>
</article>

