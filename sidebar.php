<?php
/**
 * Sidebar template.
 *
 * @package Cozy_Journal
 */

if ( ! cozy_journal_should_show_sidebar() ) {
	return;
}
?>

<aside id="secondary" class="widget-area" aria-label="<?php esc_attr_e( '手账侧栏', 'cozy-journal' ); ?>">
	<div class="sidebar-tape" aria-hidden="true"></div>
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
