( function ( $ ) {
	'use strict';

	$( function () {
		$( '.cozy-journal-color-field' ).wpColorPicker();

		$( '.cj-switch input' ).on( 'change', function () {
			const label = $( this ).closest( '.cj-switch' ).find( '.cj-switch-label' );
			label.text( this.checked ? label.data( 'on' ) : label.data( 'off' ) );
		} );

		$( '.cj-range-control input[type="range"]' ).on( 'input change', function () {
			const output = $( this ).siblings( 'output' );
			const currentText = output.text();
			const unit = currentText.replace( /^[-\d.]+/, '' );
			output.text( this.value + unit );
		} );

		$( '[data-cj-confirm]' ).on( 'click', function ( event ) {
			if ( ! window.confirm( $( this ).data( 'cj-confirm' ) ) ) {
				event.preventDefault();
			}
		} );

		$( '.cj-file-picker input[type="file"]' ).on( 'change', function () {
			const fileName = this.files && this.files[ 0 ] ? this.files[ 0 ].name : cozyJournalAdmin.chooseBackup;
			$( this ).siblings( 'b' ).text( fileName );
		} );

		$( '.cj-import-form' ).on( 'submit', function ( event ) {
			const input = $( this ).find( 'input[type="file"]' )[ 0 ];
			if ( ! input.files || ! input.files.length ) {
				event.preventDefault();
				window.alert( cozyJournalAdmin.chooseBackup );
			}
		} );
	} );
}( jQuery ) );

