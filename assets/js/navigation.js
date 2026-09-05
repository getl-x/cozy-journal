( function () {
	'use strict';
	document.documentElement.classList.add( 'has-js' );

	const button = document.querySelector( '.menu-toggle' );
	const navigation = document.querySelector( '.primary-navigation' );
	const mobileViewport = window.matchMedia( '(max-width: 760px)' );

	if ( ! button || ! navigation ) {
		return;
	}

	const label = button.querySelector( '.menu-toggle-label' );
	function setNavigationOpen( isOpen ) {
		button.setAttribute( 'aria-expanded', String( isOpen ) );
		navigation.classList.toggle( 'is-open', isOpen );
		if ( label ) {
			label.textContent = isOpen
				? cozyJournalScreenReaderText.collapse
				: cozyJournalScreenReaderText.expand;
		}
	}

	button.addEventListener( 'click', function () {
		setNavigationOpen( button.getAttribute( 'aria-expanded' ) !== 'true' );
	} );

	navigation.addEventListener( 'click', function ( event ) {
		if ( event.target.closest( 'a' ) && mobileViewport.matches ) {
			setNavigationOpen( false );
		}
	} );

	function resetNavigationAtDesktop( event ) {
		if ( ! event.matches ) {
			setNavigationOpen( false );
		}
	}

	if ( typeof mobileViewport.addEventListener === 'function' ) {
		mobileViewport.addEventListener( 'change', resetNavigationAtDesktop );
	} else {
		mobileViewport.addListener( resetNavigationAtDesktop );
	}
}() );
