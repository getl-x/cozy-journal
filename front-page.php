<?php
/**
 * Front page template.
 *
 * @package Cozy_Journal
 */

get_header();

$show_hero = get_theme_mod( 'cozy_journal_show_hero', true );
$stickers  = cozy_journal_get_stickers();
?>

<main id="primary" class="site-main front-page-main">
	<?php if ( $show_hero ) : ?>
		<section class="journal-hero journal-container" aria-labelledby="journal-hero-title">
			<div class="hero-paper">
				<div class="hero-tape" aria-hidden="true"></div>
				<div class="hero-copy">
					<?php if ( get_theme_mod( 'cozy_journal_show_hero_date', true ) ) : ?>
						<p class="hero-date"><span aria-hidden="true">◷</span> <?php echo esc_html( cozy_journal_get_hero_date() ); ?></p>
					<?php endif; ?>
					<p class="hero-eyebrow"><?php echo esc_html( get_theme_mod( 'cozy_journal_hero_eyebrow', __( '今天也要记录小确幸', 'cozy-journal' ) ) ); ?></p>
					<h1 id="journal-hero-title" class="hero-title"><?php echo esc_html( get_theme_mod( 'cozy_journal_hero_title', __( '把日子过成喜欢的样子', 'cozy-journal' ) ) ); ?></h1>
					<p class="hero-description"><?php echo esc_html( get_theme_mod( 'cozy_journal_hero_description', __( '收藏平凡日子里的光，写下每一份柔软、认真和欢喜。', 'cozy-journal' ) ) ); ?></p>
					<?php
					$hero_button_text = get_theme_mod( 'cozy_journal_hero_button_text', __( '翻开今天的手账', 'cozy-journal' ) );
					$hero_button_url  = get_theme_mod( 'cozy_journal_hero_button_url', '#journal-latest' );
					$hero_button_new_tab = get_theme_mod( 'cozy_journal_hero_button_new_tab', false );
					if ( $hero_button_text && $hero_button_url ) :
						?>
						<a class="hero-button journal-button" href="<?php echo esc_url( $hero_button_url ); ?>" <?php echo $hero_button_new_tab ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $hero_button_text ); ?> <span aria-hidden="true">→</span></a>
					<?php endif; ?>
				</div>

				<?php if ( get_theme_mod( 'cozy_journal_show_hero_art', true ) ) : ?>
					<div class="hero-polaroid" aria-hidden="true">
						<div class="polaroid-scene">
							<span class="scene-sun"></span>
							<span class="scene-cloud cloud-one"></span>
							<span class="scene-cloud cloud-two"></span>
							<span class="scene-hill hill-back"></span>
							<span class="scene-hill hill-front"></span>
							<span class="scene-flower">✿</span>
						</div>
						<?php $hero_art_caption = trim( (string) get_theme_mod( 'cozy_journal_hero_art_caption', __( '今日份 · 好心情', 'cozy-journal' ) ) ); ?>
						<?php if ( '' !== $hero_art_caption ) : ?><p><?php echo esc_html( $hero_art_caption ); ?></p><?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( get_theme_mod( 'cozy_journal_show_stickers', true ) ) : ?>
					<div class="hero-stickers" aria-hidden="true">
						<?php foreach ( $stickers as $index => $sticker ) : ?>
							<span class="hero-sticker sticker-<?php echo esc_attr( (string) ( $index + 1 ) ); ?>"><?php echo esc_html( $sticker ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( get_theme_mod( 'cozy_journal_show_front_page_content', true ) && 'page' === get_option( 'show_on_front' ) && have_posts() ) : ?>
		<section class="front-page-intro journal-container">
			<?php
			while ( have_posts() ) :
				the_post();
				if ( '' !== trim( get_the_content() ) ) :
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'journal-paper page-intro-paper' ); ?>>
						<div class="entry-content">
							<?php
							the_content();
							wp_link_pages();
							?>
						</div>
					</article>
					<?php
				endif;
			endwhile;
			?>
		</section>
	<?php endif; ?>

	<?php if ( get_theme_mod( 'cozy_journal_show_latest_section', true ) ) : ?>
	<section id="journal-latest" class="latest-notes journal-container" aria-labelledby="latest-notes-title">
		<header class="section-heading">
			<div>
				<p class="section-kicker"><?php echo esc_html( get_theme_mod( 'cozy_journal_latest_kicker', __( 'Recently in my journal', 'cozy-journal' ) ) ); ?></p>
				<h2 id="latest-notes-title"><?php echo esc_html( get_theme_mod( 'cozy_journal_latest_title', __( '最近写下的小日子', 'cozy-journal' ) ) ); ?></h2>
			</div>
			<?php if ( get_theme_mod( 'cozy_journal_show_latest_doodle', true ) ) : ?>
				<span class="heading-doodle" aria-hidden="true"><?php echo esc_html( get_theme_mod( 'cozy_journal_latest_doodle', '〰✎' ) ); ?></span>
			<?php endif; ?>
		</header>

		<div class="post-card-grid">
			<?php
			if ( 'page' === get_option( 'show_on_front' ) ) {
				$cozy_journal_home_posts_count = cozy_journal_get_range_mod( 'cozy_journal_home_posts_count', 6, 3, 12 );
				$cozy_journal_order_args       = cozy_journal_get_home_order_args();
				$cozy_journal_posts = new WP_Query(
					array_merge(
						array(
						'post_type'           => 'post',
						'post_status'         => 'publish',
						'posts_per_page'      => $cozy_journal_home_posts_count,
						'ignore_sticky_posts' => (bool) get_theme_mod( 'cozy_journal_home_ignore_sticky', false ),
						),
						$cozy_journal_order_args
					)
				);
			} else {
				$cozy_journal_posts = $GLOBALS['wp_query'];
			}

			if ( $cozy_journal_posts->have_posts() ) :
				while ( $cozy_journal_posts->have_posts() ) :
					$cozy_journal_posts->the_post();
					get_template_part( 'template-parts/content', 'card' );
				endwhile;
			else :
				get_template_part( 'template-parts/content', 'none' );
			endif;
			?>
		</div>

		<?php
		if ( 'page' !== get_option( 'show_on_front' ) ) {
			cozy_journal_pagination();
		}
		wp_reset_postdata();
		?>
	</section>
	<?php endif; ?>
</main>

<?php
get_footer();
