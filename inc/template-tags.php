<?php
/**
 * Custom template tags for Cozy Journal.
 *
 * @package Cozy_Journal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'cozy_journal_posted_on' ) ) {
	/**
	 * Prints the published date.
	 */
	function cozy_journal_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated screen-reader-text" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		printf(
			'<span class="posted-on"><span aria-hidden="true">📅</span> <a href="%1$s" rel="bookmark">%2$s</a></span>',
			esc_url( get_permalink() ),
			$time_string // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
}

if ( ! function_exists( 'cozy_journal_posted_by' ) ) {
	/**
	 * Prints the post author.
	 */
	function cozy_journal_posted_by() {
		$author_name = esc_html( get_the_author() );

		if ( get_theme_mod( 'cozy_journal_link_author_archive', false ) ) {
			$author_name = sprintf(
				'<a class="url fn n" href="%1$s">%2$s</a>',
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				$author_name
			);
		}

		printf(
			'<span class="byline"><span aria-hidden="true">✎</span> %s</span>',
			wp_kses_post( $author_name )
		);
	}
}

if ( ! function_exists( 'cozy_journal_entry_footer' ) ) {
	/**
	 * Prints categories, tags and edit link.
	 */
	function cozy_journal_entry_footer() {
		if ( 'post' === get_post_type() ) {
			$categories = get_the_category_list( esc_html__( '、', 'cozy-journal' ) );
			if ( $categories ) {
				printf( '<span class="cat-links"><strong>%1$s</strong> %2$s</span>', esc_html__( '收进：', 'cozy-journal' ), wp_kses_post( $categories ) );
			}

			$tags = get_the_tag_list( '', ' ' );
			if ( $tags ) {
				printf( '<span class="tags-links"><strong>%1$s</strong> %2$s</span>', esc_html__( '贴纸：', 'cozy-journal' ), wp_kses_post( $tags ) );
			}
		}

		edit_post_link(
			sprintf(
				/* translators: %s: Post title. */
				wp_kses( __( '编辑 <span class="screen-reader-text">“%s”</span>', 'cozy-journal' ), array( 'span' => array( 'class' => array() ) ) ),
				wp_kses_post( get_the_title() )
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
}

/**
 * Returns an accessible linked post thumbnail.
 *
 * @param string $size Image size.
 */
function cozy_journal_post_thumbnail( $size = 'cozy-journal-card' ) {
	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}
	?>
	<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
		<?php
		the_post_thumbnail(
			$size,
			array(
				'alt' => the_title_attribute( array( 'echo' => false ) ),
			)
		);
		?>
	</a>
	<?php
}

/**
 * Displays pagination with scrapbook-friendly labels.
 */
function cozy_journal_pagination() {
	the_posts_pagination(
		array(
			'mid_size'  => 1,
			'prev_text' => __( '← 新一点', 'cozy-journal' ),
			'next_text' => __( '旧一点 →', 'cozy-journal' ),
		)
	);
}

