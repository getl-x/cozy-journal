<?php
/**
 * Search form template.
 *
 * @package Cozy_Journal
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php esc_html_e( '搜索：', 'cozy-journal' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( '想找哪一页手账？', 'placeholder', 'cozy-journal' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	</label>
	<button type="submit" class="search-submit"><span aria-hidden="true">⌕</span><span class="screen-reader-text"><?php echo esc_html_x( '搜索', 'submit button', 'cozy-journal' ); ?></span></button>
</form>

