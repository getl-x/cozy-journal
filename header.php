<?php
/**
 * Site header.
 *
 * @package Cozy_Journal
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( '跳到正文', 'cozy-journal' ); ?></a>

<div id="page" class="site">
	<?php if ( get_theme_mod( 'cozy_journal_show_notebook_rings', true ) ) : ?>
		<div class="notebook-rings" aria-hidden="true">
			<?php for ( $ring = 0; $ring < 10; $ring++ ) : ?>
				<span></span>
			<?php endfor; ?>
		</div>
	<?php endif; ?>

	<header id="masthead" class="site-header">
		<div class="site-header-inner journal-container">
			<div class="site-branding">
				<?php if ( has_custom_logo() ) : ?>
					<div class="site-logo"><?php the_custom_logo(); ?></div>
				<?php elseif ( get_theme_mod( 'cozy_journal_show_brand_mark', true ) ) : ?>
					<a class="brand-sticker" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-hidden="true" tabindex="-1"><?php echo esc_html( get_theme_mod( 'cozy_journal_brand_mark_text', '♡' ) ); ?></a>
				<?php endif; ?>

				<div class="site-branding-copy">
					<?php if ( get_theme_mod( 'cozy_journal_show_site_title', true ) ) : ?>
						<?php if ( is_front_page() && is_home() ) : ?>
							<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						<?php else : ?>
							<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
						<?php endif; ?>
					<?php endif; ?>

					<?php $cozy_journal_description = get_bloginfo( 'description', 'display' ); ?>
					<?php if ( get_theme_mod( 'cozy_journal_show_site_description', true ) && ( $cozy_journal_description || is_customize_preview() ) ) : ?>
						<p class="site-description"><?php echo esc_html( $cozy_journal_description ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( get_theme_mod( 'cozy_journal_show_primary_navigation', true ) ) : ?>
				<button class="menu-toggle" type="button" aria-controls="primary-menu" aria-expanded="false">
					<span class="menu-toggle-icon" aria-hidden="true"><i></i><i></i><i></i></span>
					<span class="menu-toggle-label"><?php echo esc_html( get_theme_mod( 'cozy_journal_mobile_menu_text', __( '展开菜单', 'cozy-journal' ) ) ); ?></span>
				</button>

				<nav id="site-navigation" class="primary-navigation" aria-label="<?php esc_attr_e( '主导航', 'cozy-journal' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu_id'        => 'primary-menu',
							'container'      => false,
							'fallback_cb'    => get_theme_mod( 'cozy_journal_menu_fallback_pages', true ) ? 'wp_page_menu' : false,
						)
					);
					?>
				</nav>
			<?php endif; ?>

			<?php if ( get_theme_mod( 'cozy_journal_show_header_search', false ) ) : ?>
				<form role="search" method="get" class="header-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label>
						<span class="screen-reader-text"><?php esc_html_e( '搜索：', 'cozy-journal' ); ?></span>
						<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( get_theme_mod( 'cozy_journal_search_placeholder', __( '想找哪一页手账？', 'cozy-journal' ) ) ); ?>">
					</label>
					<button type="submit"><span aria-hidden="true">⌕</span><span class="screen-reader-text"><?php esc_html_e( '搜索', 'cozy-journal' ); ?></span></button>
				</form>
			<?php endif; ?>

			<?php if ( function_exists( 'cozy_journal_current_user_can_create_writing_posts' ) && cozy_journal_writing_desk_enabled() && get_theme_mod( 'cozy_journal_show_write_link', true ) && cozy_journal_current_user_can_create_writing_posts() ) : ?>
				<a class="header-write-link" href="<?php echo esc_url( cozy_journal_get_write_url() ); ?>" <?php echo cozy_journal_is_writing_desk() ? 'aria-current="page"' : ''; ?>>
					<span aria-hidden="true">✎</span><?php echo esc_html( get_theme_mod( 'cozy_journal_write_link_text', __( '写文章', 'cozy-journal' ) ) ); ?>
				</a>
			<?php endif; ?>
		</div>
	</header>
