( function () {
	'use strict';
	document.documentElement.classList.add( 'has-js' );

	const button = document.querySelector( '.menu-toggle' );
	const navigation = document.querySelector( '.primary-navigation' );

	if ( ! button || ! navigation ) {
		return;
	}

	button.addEventListener( 'click', function () {
		const isOpen = button.getAttribute( 'aria-expanded' ) === 'true';
		button.setAttribute( 'aria-expanded', String( ! isOpen ) );
		navigation.classList.toggle( 'is-open', ! isOpen );
		button.querySelector( '.menu-toggle-label' ).textContent = isOpen
			? cozyJournalScreenReaderText.expand
			: cozyJournalScreenReaderText.collapse;
	} );

	navigation.addEventListener( 'click', function ( event ) {
		if ( event.target.closest( 'a' ) && window.matchMedia( '(max-width: 760px)' ).matches ) {
			button.setAttribute( 'aria-expanded', 'false' );
			navigation.classList.remove( 'is-open' );
			button.querySelector( '.menu-toggle-label' ).textContent = cozyJournalScreenReaderText.expand;
		}
	} );
}() );
