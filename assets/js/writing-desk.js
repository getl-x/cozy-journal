( function () {
	'use strict';

	const form = document.getElementById( 'cozy-journal-writing-form' );
	if ( ! form || typeof cozyJournalWriting === 'undefined' ) {
		return;
	}

	const titleInput = document.getElementById( 'cozy-journal-title' );
	const postIdInput = document.getElementById( 'cozy-journal-post-id' );
	const statusElement = document.getElementById( 'cozy-journal-save-status' );
	const countElement = document.getElementById( 'cozy-journal-word-count' );
	const actionButtons = Array.from( form.querySelectorAll( 'button[type="submit"]' ) );
	const editorId = 'cozy_journal_content';
	let dirty = false;
	let saving = false;
	let submitting = false;
	let editorBound = false;

	function getEditorContent() {
		if ( window.tinymce && tinymce.get( editorId ) && ! tinymce.get( editorId ).isHidden() ) {
			return tinymce.get( editorId ).getContent();
		}

		const textarea = document.getElementById( editorId );
		return textarea ? textarea.value : '';
	}

	function setStatus( message, state ) {
		if ( ! statusElement ) {
			return;
		}

		statusElement.textContent = message;
		statusElement.dataset.state = state || '';
	}

	function setActionButtonsDisabled( disabled ) {
		actionButtons.forEach( function ( button ) {
			button.disabled = disabled;
		} );
	}

	function updateWordCount() {
		if ( ! countElement ) {
			return;
		}

		const holder = document.createElement( 'div' );
		holder.innerHTML = getEditorContent();
		const text = ( holder.textContent || '' ).replace( /\s+/g, '' );
		countElement.textContent = text.length + ' ' + cozyJournalWriting.labels.words;
	}

	function markDirty() {
		if ( submitting ) {
			return;
		}

		dirty = true;
		setStatus( cozyJournalWriting.labels.unsaved, 'dirty' );
		updateWordCount();
	}

	function bindTinyMce() {
		if ( editorBound || ! window.tinymce ) {
			return;
		}

		const editor = tinymce.get( editorId );
		if ( ! editor ) {
			return;
		}

		editor.on( 'input change keyup undo redo', markDirty );
		editorBound = true;
		updateWordCount();
	}

	function autosave() {
		if ( ! dirty || saving || submitting || Number( cozyJournalWriting.autosaveInterval ) === 0 ) {
			return;
		}

		if ( [ 'publish', 'private', 'future' ].includes( cozyJournalWriting.postStatus ) ) {
			setStatus( cozyJournalWriting.labels.published, 'manual' );
			return;
		}

		saving = true;
		setActionButtonsDisabled( true );
		setStatus( cozyJournalWriting.labels.saving, 'saving' );

		const data = new FormData( form );
		data.set( 'action', 'cozy_journal_frontend_autosave' );
		data.set( '_ajax_nonce', cozyJournalWriting.nonce );
		data.set( 'cozy_journal_content', getEditorContent() );
		data.delete( 'cozy_journal_submit' );
		data.delete( 'cozy_journal_featured_image' );

		fetch( cozyJournalWriting.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: data
		} )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( response ) {
				if ( ! response.success ) {
					throw new Error( response.data && response.data.message ? response.data.message : cozyJournalWriting.labels.failed );
				}

				if ( response.data.skipped ) {
					setStatus( response.data.message || cozyJournalWriting.labels.published, 'manual' );
					return;
				}

				if ( response.data.postId ) {
					postIdInput.value = response.data.postId;
					cozyJournalWriting.postId = response.data.postId;
					if ( response.data.writeUrl && window.history && history.replaceState ) {
						history.replaceState( {}, document.title, response.data.writeUrl );
					}
				}

				dirty = false;
				setStatus( ( response.data.message || cozyJournalWriting.labels.saved ) + ( response.data.savedAt ? ' · ' + response.data.savedAt : '' ), 'saved' );
			} )
			.catch( function ( error ) {
				setStatus( error.message || cozyJournalWriting.labels.failed, 'error' );
			} )
			.finally( function () {
				saving = false;
				setActionButtonsDisabled( false );
			} );
	}

	form.addEventListener( 'input', function ( event ) {
		if ( event.target.type !== 'file' ) {
			markDirty();
		}
	} );
	form.addEventListener( 'change', markDirty );
	form.addEventListener( 'submit', function ( event ) {
		if ( saving ) {
			event.preventDefault();
			submitting = false;
			setStatus( cozyJournalWriting.labels.autosaveBusy, 'saving' );
			return;
		}

		submitting = true;
		if ( window.tinymce ) {
			tinymce.triggerSave();
		}

		if ( event.submitter && event.submitter.value === 'preview' ) {
			window.setTimeout( function () {
				submitting = false;
				setStatus( cozyJournalWriting.labels.previewOpened, dirty ? 'dirty' : 'saved' );
			}, 1500 );
		}
	} );

	document.querySelectorAll( '[data-writing-confirm]' ).forEach( function ( button ) {
		button.addEventListener( 'click', function ( event ) {
			if ( ! window.confirm( button.dataset.writingConfirm ) ) {
				event.preventDefault();
				submitting = false;
			}
		} );
	} );

	const fileInput = form.querySelector( 'input[name="cozy_journal_featured_image"]' );
	const imagePreview = document.getElementById( 'cozy-journal-image-preview' );
	if ( fileInput && imagePreview ) {
		const previewImage = imagePreview.querySelector( 'img' );
		const cancelButton = imagePreview.querySelector( 'button' );

		fileInput.addEventListener( 'change', function () {
			if ( ! fileInput.files || ! fileInput.files[ 0 ] ) {
				imagePreview.hidden = true;
				return;
			}

			previewImage.src = URL.createObjectURL( fileInput.files[ 0 ] );
			imagePreview.hidden = false;
		} );

		cancelButton.addEventListener( 'click', function () {
			fileInput.value = '';
			previewImage.removeAttribute( 'src' );
			imagePreview.hidden = true;
		} );
	}

	window.addEventListener( 'beforeunload', function ( event ) {
		if ( dirty && ! submitting ) {
			event.preventDefault();
			event.returnValue = cozyJournalWriting.labels.leaveWarning;
			return cozyJournalWriting.labels.leaveWarning;
		}
	} );

	if ( titleInput ) {
		titleInput.addEventListener( 'input', updateWordCount );
	}

	const editorPoll = window.setInterval( function () {
		bindTinyMce();
		if ( editorBound ) {
			window.clearInterval( editorPoll );
		}
	}, 500 );

	const textarea = document.getElementById( editorId );
	if ( textarea ) {
		textarea.addEventListener( 'input', markDirty );
	}

	updateWordCount();
	if ( Number( cozyJournalWriting.autosaveInterval ) > 0 ) {
		window.setInterval( autosave, Number( cozyJournalWriting.autosaveInterval ) * 1000 );
	}
}() );
