( function ( $ ) {
	'use strict';

	function parseHexColor( value ) {
		const match = /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.exec( value );
		if ( ! match ) {
			return null;
		}

		const hex = match[ 1 ].length === 3
			? match[ 1 ].split( '' ).map( function ( character ) { return character + character; } ).join( '' )
			: match[ 1 ];

		return [
			parseInt( hex.slice( 0, 2 ), 16 ),
			parseInt( hex.slice( 2, 4 ), 16 ),
			parseInt( hex.slice( 4, 6 ), 16 )
		];
	}

	function componentToHex( component ) {
		return Math.round( component ).toString( 16 ).padStart( 2, '0' );
	}

	function darkenColor( value ) {
		const channels = parseHexColor( value );
		if ( ! channels ) {
			return value;
		}

		return '#' + channels.map( function ( channel ) {
			return componentToHex( channel * 0.78 );
		} ).join( '' );
	}

	function contrastTextColor( value ) {
		const channels = parseHexColor( value );
		if ( ! channels ) {
			return '#000000';
		}

		const linear = channels.map( function ( channel ) {
			const normalized = channel / 255;
			return normalized <= 0.03928
				? normalized / 12.92
				: Math.pow( ( normalized + 0.055 ) / 1.055, 2.4 );
		} );
		const luminance = ( 0.2126 * linear[ 0 ] ) + ( 0.7152 * linear[ 1 ] ) + ( 0.0722 * linear[ 2 ] );

		return 1.05 / ( luminance + 0.05 ) >= 4.5 ? '#ffffff' : '#000000';
	}

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
					const darkColor = darkenColor( nextValue );
					document.documentElement.style.setProperty( '--journal-primary-dark', darkColor );
					document.documentElement.style.setProperty( '--journal-primary-contrast', contrastTextColor( nextValue ) );
					document.documentElement.style.setProperty( '--journal-primary-dark-contrast', contrastTextColor( darkColor ) );
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
