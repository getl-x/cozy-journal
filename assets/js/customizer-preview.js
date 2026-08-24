( function ( $ ) {
	'use strict';

	const textBindings = {
		blogname: '.site-title a',
		blogdescription: '.site-description',
		cozy_journal_hero_eyebrow: '.hero-eyebrow',
		cozy_journal_hero_title: '.hero-title',
		cozy_journal_hero_description: '.hero-description',
		cozy_journal_hero_button_text: '.hero-button',
		cozy_journal_footer_text: '.footer-note-text'
	};

	Object.keys( textBindings ).forEach( function ( settingId ) {
		wp.customize( settingId, function ( value ) {
			value.bind( function ( nextValue ) {
				$( textBindings[ settingId ] ).text( nextValue );
			} );
		} );
	} );

	const colorBindings = {
		cozy_journal_primary_color: '--journal-primary',
		cozy_journal_secondary_color: '--journal-secondary',
		cozy_journal_paper_color: '--journal-paper',
		cozy_journal_card_color: '--journal-card',
		cozy_journal_ink_color: '--journal-ink'
	};

	Object.keys( colorBindings ).forEach( function ( settingId ) {
		wp.customize( settingId, function ( value ) {
			value.bind( function ( nextValue ) {
				document.documentElement.style.setProperty( colorBindings[ settingId ], nextValue );
				if ( settingId === 'cozy_journal_primary_color' ) {
					document.documentElement.style.setProperty( '--journal-primary-dark', nextValue );
				}
			} );
		} );
	} );

	wp.customize( 'cozy_journal_card_radius', function ( value ) {
		value.bind( function ( nextValue ) {
			document.documentElement.style.setProperty( '--journal-radius', nextValue + 'px' );
		} );
	} );
}( jQuery ) );
