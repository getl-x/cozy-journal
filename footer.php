<?php
/**
 * Site footer.
 *
 * @package Cozy_Journal
 */
?>
	<?php if ( get_theme_mod( 'cozy_journal_show_footer', true ) ) : ?>
	<footer id="colophon" class="site-footer">
		<div class="footer-paper journal-container">
			<div class="footer-tape" aria-hidden="true"></div>
			<?php if ( get_theme_mod( 'cozy_journal_show_footer_note', true ) ) : ?>
			<p class="footer-note-text">
				<?php echo esc_html( get_theme_mod( 'cozy_journal_footer_text', __( '愿每一次记录，都能接住生活里闪闪发亮的瞬间。', 'cozy-journal' ) ) ); ?>
			</p>
			<?php endif; ?>

			<?php if ( get_theme_mod( 'cozy_journal_show_footer_menu', true ) && has_nav_menu( 'footer' ) ) : ?>
				<nav class="footer-navigation" aria-label="<?php esc_attr_e( '页脚导航', 'cozy-journal' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'depth'          => 1,
						)
					);
					?>
				</nav>
			<?php endif; ?>

			<?php if ( get_theme_mod( 'cozy_journal_show_footer_year', true ) ) : ?>
				<p class="site-info">
					<?php echo esc_html( cozy_journal_get_copyright_text() ); ?>
					<?php if ( get_theme_mod( 'cozy_journal_show_footer_heart', true ) ) : ?><span class="footer-heart" aria-hidden="true">♡</span><?php endif; ?>
				</p>
			<?php endif; ?>


			<p class="theme-credit">
				<span class="theme-credit-symbol" aria-hidden="true">✿</span>
				<span><?php esc_html_e( 'Theme Cozy Journal by', 'cozy-journal' ); ?></span>
				<a href="<?php echo esc_url( COZY_JOURNAL_REPOSITORY_URL ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( COZY_JOURNAL_AUTHOR ); ?></a>
			</p>
		</div>
	</footer>
	<?php endif; ?>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
