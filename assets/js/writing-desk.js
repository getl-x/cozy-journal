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
	const fileInput = form.querySelector( 'input[name="cozy_journal_featured_image"]' );
	const removeThumbnailInput = form.querySelector( 'input[name="cozy_journal_remove_thumbnail"]' );
	const imagePreview = document.getElementById( 'cozy-journal-image-preview' );
	const editorId = 'cozy_journal_content';
	let contentDirty = form.dataset.restoredState === '1';
	let mediaDirty = false;
	let editVersion = contentDirty ? 1 : 0;
	let saving = false;
	let submitting = false;
	let editorBound = false;
	let pendingSubmitType = '';
	let autosaveRetryTimer = 0;
	let previewObjectUrl = '';
	let cleanStatus = {
		message: statusElement ? statusElement.textContent : '',
		state: statusElement ? statusElement.dataset.state || '' : ''
	};

	function getEditor() {
		if ( window.tinymce ) {
			return tinymce.get( editorId );
		}

		return null;
	}

	function getEditorContent() {
		const editor = getEditor();
		if ( editor && ! editor.isHidden() ) {
			return editor.getContent();
		}

		const textarea = document.getElementById( editorId );
		return textarea ? textarea.value : '';
	}

	function getEditorText() {
		const holder = document.createElement( 'template' );
		holder.innerHTML = getEditorContent();

		return ( holder.content.textContent || '' ).replace( /\u00a0/g, ' ' ).trim();
	}

	function setStatus( message, state ) {
		if ( ! statusElement ) {
			return;
		}

		statusElement.textContent = message;
		statusElement.dataset.state = state || '';
	}

	function setCleanStatus( message, state ) {
		cleanStatus = {
			message: message,
			state: state || ''
		};
	}

	function renderDirtyStatus() {
		if ( saving || submitting ) {
			return;
		}

		if ( contentDirty ) {
			setStatus( cozyJournalWriting.labels.unsaved, 'dirty' );
		} else if ( mediaDirty ) {
			setStatus( cozyJournalWriting.labels.mediaUnsaved, 'dirty' );
		} else {
			setStatus( cleanStatus.message, cleanStatus.state );
		}
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

		const text = getEditorText().replace( /\s+/g, '' );
		countElement.textContent = text.length + ' ' + cozyJournalWriting.labels.words;
	}

	function markContentDirty() {
		if ( submitting ) {
			return;
		}

		contentDirty = true;
		editVersion += 1;
		renderDirtyStatus();
		updateWordCount();
	}

	function updateMediaDirty() {
		const hasSelectedFile = Boolean( fileInput && fileInput.files && fileInput.files.length );
		const removesCurrentImage = Boolean( removeThumbnailInput && removeThumbnailInput.checked );
		mediaDirty = hasSelectedFile || removesCurrentImage;
		renderDirtyStatus();
	}

	function bindTinyMce() {
		if ( editorBound ) {
			return;
		}

		const editor = getEditor();
		if ( ! editor ) {
			return;
		}

		editor.on( 'input change keyup undo redo', markContentDirty );
		editorBound = true;
		updateWordCount();
	}

	function scheduleAutosaveRetry() {
		if ( autosaveRetryTimer ) {
			window.clearTimeout( autosaveRetryTimer );
		}

		autosaveRetryTimer = window.setTimeout( function () {
			autosaveRetryTimer = 0;
			autosave();
		}, 1000 );
	}

	function autosave() {
		if ( ! contentDirty || saving || submitting || cozyJournalWriting.postLocked || Number( cozyJournalWriting.autosaveInterval ) === 0 ) {
			return;
		}

		if ( [ 'publish', 'private', 'future' ].includes( cozyJournalWriting.postStatus ) ) {
			setStatus( cozyJournalWriting.labels.published, 'manual' );
			return;
		}

		const requestVersion = editVersion;
		let shouldRetry = false;
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

				const savedMessage = ( response.data.message || cozyJournalWriting.labels.saved ) + ( response.data.savedAt ? ' · ' + response.data.savedAt : '' );
				setCleanStatus( savedMessage, 'saved' );

				if ( editVersion === requestVersion ) {
					contentDirty = false;
					if ( mediaDirty ) {
						setStatus( cozyJournalWriting.labels.mediaUnsaved, 'dirty' );
					} else {
						setStatus( cleanStatus.message, cleanStatus.state );
					}
				} else {
					shouldRetry = true;
					setStatus( cozyJournalWriting.labels.changedAgain, 'dirty' );
				}
			} )
			.catch( function ( error ) {
				setStatus( error.message || cozyJournalWriting.labels.failed, 'error' );
			} )
			.finally( function () {
				saving = false;
				setActionButtonsDisabled( false );
				if ( shouldRetry ) {
					scheduleAutosaveRetry();
				}
			} );
	}

	function focusEditor() {
		const editor = getEditor();
		if ( editor && ! editor.isHidden() ) {
			editor.focus();
			return;
		}

		const textarea = document.getElementById( editorId );
		if ( textarea ) {
			textarea.focus();
		}
	}

	function validateForm() {
		if ( ! titleInput || ! titleInput.value.trim() ) {
			setStatus( cozyJournalWriting.labels.missingTitle, 'error' );
			if ( titleInput ) {
				titleInput.focus();
			}
			return false;
		}

		if ( ! getEditorText() ) {
			setStatus( cozyJournalWriting.labels.missingContent, 'error' );
			focusEditor();
			return false;
		}

		return true;
	}

	function ensureSubmitType( submitType ) {
		let fallbackInput = form.querySelector( 'input[data-cozy-journal-submit-fallback]' );
		if ( ! fallbackInput ) {
			fallbackInput = document.createElement( 'input' );
			fallbackInput.type = 'hidden';
			fallbackInput.name = 'cozy_journal_submit_fallback';
			fallbackInput.dataset.cozyJournalSubmitFallback = '1';
			form.appendChild( fallbackInput );
		}

		fallbackInput.value = submitType;
	}

	form.addEventListener( 'input', function ( event ) {
		if ( event.target === fileInput || event.target === removeThumbnailInput ) {
			return;
		}

		markContentDirty();
	} );

	form.addEventListener( 'change', function ( event ) {
		if ( event.target === fileInput || event.target === removeThumbnailInput ) {
			updateMediaDirty();
			return;
		}

		if ( event.target.tagName === 'SELECT' || [ 'checkbox', 'radio' ].includes( event.target.type ) ) {
			markContentDirty();
		}
	} );

	actionButtons.forEach( function ( button ) {
		button.addEventListener( 'click', function ( event ) {
			pendingSubmitType = button.value || 'draft';

			if ( button.dataset.writingConfirm && ! window.confirm( button.dataset.writingConfirm ) ) {
				event.preventDefault();
				pendingSubmitType = '';
				submitting = false;
			}
		} );
	} );

	form.addEventListener( 'submit', function ( event ) {
		const submitter = event.submitter || null;
		const submitType = submitter && submitter.value ? submitter.value : pendingSubmitType || 'draft';

		if ( saving || submitting ) {
			event.preventDefault();
			pendingSubmitType = '';
			if ( saving ) {
				submitting = false;
				setStatus( cozyJournalWriting.labels.autosaveBusy, 'saving' );
			}
			return;
		}

		if ( window.tinymce ) {
			tinymce.triggerSave();
		}

		if ( ! validateForm() ) {
			event.preventDefault();
			pendingSubmitType = '';
			submitting = false;
			return;
		}

		ensureSubmitType( submitType );
		submitting = true;

		if ( submitType === 'preview' ) {
			window.setTimeout( function () {
				submitting = false;
				pendingSubmitType = '';
				setStatus( cozyJournalWriting.labels.previewOpened, contentDirty || mediaDirty ? 'dirty' : 'saved' );
			}, 1500 );
		}
	} );

	if ( fileInput && imagePreview ) {
		const previewImage = imagePreview.querySelector( 'img' );
		const cancelButton = imagePreview.querySelector( 'button' );

		fileInput.addEventListener( 'change', function () {
			if ( previewObjectUrl ) {
				URL.revokeObjectURL( previewObjectUrl );
				previewObjectUrl = '';
			}

			if ( ! fileInput.files || ! fileInput.files[ 0 ] ) {
				previewImage.removeAttribute( 'src' );
				imagePreview.hidden = true;
				updateMediaDirty();
				return;
			}

			previewObjectUrl = URL.createObjectURL( fileInput.files[ 0 ] );
			previewImage.src = previewObjectUrl;
			imagePreview.hidden = false;
			updateMediaDirty();
		} );

		if ( cancelButton ) {
			cancelButton.addEventListener( 'click', function () {
				fileInput.value = '';
				if ( previewObjectUrl ) {
					URL.revokeObjectURL( previewObjectUrl );
					previewObjectUrl = '';
				}
				previewImage.removeAttribute( 'src' );
				imagePreview.hidden = true;
				updateMediaDirty();
			} );
		}
	}

	function refreshPostLock() {
		const postId = postIdInput ? Number( postIdInput.value ) : 0;
		if ( ! postId || submitting || cozyJournalWriting.postLocked ) {
			return;
		}

		const data = new FormData();
		data.set( 'action', 'cozy_journal_frontend_refresh_lock' );
		data.set( '_ajax_nonce', cozyJournalWriting.nonce );
		data.set( 'cozy_journal_post_id', postId );

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
					throw new Error( response.data && response.data.message ? response.data.message : cozyJournalWriting.labels.lockFailed );
				}
			} )
			.catch( function ( error ) {
				setStatus( error.message || cozyJournalWriting.labels.lockFailed, 'error' );
			} );
	}

	window.addEventListener( 'beforeunload', function ( event ) {
		if ( ( contentDirty || mediaDirty ) && ! submitting ) {
			event.preventDefault();
			event.returnValue = cozyJournalWriting.labels.leaveWarning;
			return cozyJournalWriting.labels.leaveWarning;
		}
	} );

	let editorPollCount = 0;
	const editorPoll = window.setInterval( function () {
		editorPollCount += 1;
		bindTinyMce();
		if ( editorBound || editorPollCount >= 40 ) {
			window.clearInterval( editorPoll );
		}
	}, 500 );

	updateWordCount();
	updateMediaDirty();
	if ( contentDirty ) {
		renderDirtyStatus();
	}

	if ( Number( cozyJournalWriting.autosaveInterval ) > 0 ) {
		window.setInterval( autosave, Number( cozyJournalWriting.autosaveInterval ) * 1000 );
	}

	if ( Number( cozyJournalWriting.lockInterval ) > 0 ) {
		window.setInterval( refreshPostLock, Number( cozyJournalWriting.lockInterval ) * 1000 );
	}
}() );
