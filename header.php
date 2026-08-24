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
	<div class="notebook-rings" aria-hidden="true">
		<?php for ( $ring = 0; $ring < 10; $ring++ ) : ?>
			<span></span>
		<?php endfor; ?>
	</div>

	<header id="masthead" class="site-header">
		<div class="site-header-inner journal-container">
			<div class="site-branding">
				<?php if ( has_custom_logo() ) : ?>
					<div class="site-logo"><?php the_custom_logo(); ?></div>
				<?php else : ?>
					<a class="brand-sticker" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-hidden="true" tabindex="-1">♡</a>
				<?php endif; ?>

				<div class="site-branding-copy">
					<?php if ( is_front_page() && is_home() ) : ?>
						<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
					<?php else : ?>
						<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
					<?php endif; ?>

					<?php $cozy_journal_description = get_bloginfo( 'description', 'display' ); ?>
					<?php if ( $cozy_journal_description || is_customize_preview() ) : ?>
						<p class="site-description"><?php echo esc_html( $cozy_journal_description ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<button class="menu-toggle" type="button" aria-controls="primary-menu" aria-expanded="false">
				<span class="menu-toggle-icon" aria-hidden="true"><i></i><i></i><i></i></span>
				<span class="menu-toggle-label"><?php esc_html_e( '展开菜单', 'cozy-journal' ); ?></span>
			</button>

			<nav id="site-navigation" class="primary-navigation" aria-label="<?php esc_attr_e( '主导航', 'cozy-journal' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'container'      => false,
						'fallback_cb'    => 'wp_page_menu',
					)
				);
				?>
			</nav>

			<?php if ( function_exists( 'cozy_journal_writing_desk_enabled' ) && cozy_journal_writing_desk_enabled() && get_theme_mod( 'cozy_journal_show_write_link', true ) && is_user_logged_in() && current_user_can( 'edit_posts' ) ) : ?>
				<a class="header-write-link" href="<?php echo esc_url( cozy_journal_get_write_url() ); ?>" <?php echo cozy_journal_is_writing_desk() ? 'aria-current="page"' : ''; ?>>
					<span aria-hidden="true">✎</span><?php esc_html_e( '写文章', 'cozy-journal' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</header>
