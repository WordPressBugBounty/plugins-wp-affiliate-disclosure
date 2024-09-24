/**
* Backend JS file
*
*/

var WPADC_Backend = new function() {

	var t = this,
		media_frame;

	/* Buttons
	---------------------------------------------------------- */	

	t.loadingBtn = function( event ) {
		jQuery(this).html( WPADCB_AJAX.loadingBtn );
	}

	/* Trigger main modal
	---------------------------------------------------------- */	
	t.triggerModal = function( event ) {

		event.preventDefault();

		var modal = jQuery("#wpadcb-main-modal");

		openModal( modal );
	}

	t.closeModal = function( event ) {

		event.preventDefault();
		var modal = jQuery("#wpadcb-main-modal");
		closeModal( modal );
	}

	t.openSelectedModal = function( event ) {
		
		event.preventDefault();
		var btn = jQuery(this),
			modal_id = btn.attr("data-modal-id"),
			modal_type = btn.attr("data-modal-type"),
			modal = jQuery( modal_id );

		if ( modal_type !== undefined ) {
			if ( modal_type == 'small' ) {
				openSmallModal( modal );
			} else {
				openModal( modal );
			}
		} else { 
			openModal( modal );
		}
	}

	t.closeSelectedModal = function( event ) {
		
		event.preventDefault();
		var btn = jQuery(this),
			modal_id = btn.attr("data-modal-id"),
			modal = jQuery( modal_id );

		closeModal( modal );
	}

	var closeModal = function( modal ) {
		jQuery.magnificPopup.close({
		  	items: {
		    	src: modal,
		    	type: 'inline',
		  	},
		});
	}

	var openModal = function( modal ) {

		modal.removeClass(".mfp-hide");
		jQuery.magnificPopup.open({
		  	items: {
		    	src: modal,
		    	type: 'inline',
		  	},
          	fixedContentPos: true,
          	fixedBgPos: true,
          	overflowY: "auto",
          	closeBtnInside: false,
          	showCloseBtn: false,
          	preloader: false, 
          	midClick: true,
          	removalDelay: 300,
          	mainClass: "wpadcb-modal"
		});

	}

	var openSmallModal = function( modal ) {

		modal.removeClass(".mfp-hide");
		jQuery.magnificPopup.open({
		  	items: {
		    	src: modal,
		    	type: 'inline',
		  	},
          	fixedContentPos: false,
          	fixedBgPos: true,
          	overflowY: "auto",
          	closeBtnInside: false,
          	showCloseBtn: false,
          	preloader: false, 
          	midClick: true,
          	removalDelay: 300,
          	mainClass: "wpadcb-sm-modal"
		});

	}

	var openMsgModal = function( modal ) {

		modal.removeClass(".mfp-hide");
		jQuery.magnificPopup.open({
		  	items: {
		    	src: modal,
		    	type: 'inline',
		  	},
          	fixedContentPos: true,
          	fixedBgPos: true,
          	overflowY: "auto",
          	closeBtnInside: false,
          	showCloseBtn: false,
          	closeOnBgClick: false,
          	enableEscapeKey: false,
          	preloader: false, 
          	removalDelay: 300,
          	mainClass: "wpadcb-sm-modal"
		});

	}

	/* Create item
	---------------------------------------------------------- */

	t.addNewItem = function( event ) {

		event.preventDefault();

		var modal = jQuery("#wpadcb-main-modal");
		var btn = jQuery(this);
		var nonce = btn.attr("data-action-nonce");

		modal.html( WPADCB_AJAX.loadingModal );
		openMsgModal( modal );
		
		jQuery.post( WPADCB_AJAX.ajaxUrl, { 
			action: 'wpadcb-add-new',
			_wpdcb_user_action_nonce: nonce
		}, function(data) {
			modal.find(".wpadcb-modal-container").slideUp( 350 , function() {
				jQuery(this)
					.html(data)
					.slideDown( 350 );

			});

		});

	}

	t.createItem = function( event ) {

		event.preventDefault();

		var btn = jQuery(this),
			modal = btn.closest(".wpadcb-modal-container"),
			modalHTML = modal.html(),
			container = btn.closest(".wpadcb-addnew-container"),
			form = container.find("form.wpadcb-addnew-form"),
			formSerialize = form.serialize();

		modal.html( WPADCB_AJAX.processingModal );
		jQuery.post( WPADCB_AJAX.ajaxUrl, formSerialize , function(data) {
			
			var result = data.split("_|_");

			if ( result[0] == 'error' ) {
				alert( result[1] );
				modal.html( modalHTML );
			} else {
				modal.html( result[1] );
				jQuery(document).trigger( "wpadcb_create_item/success" , data );
			}

		});
		
	}

	/* Update item
	---------------------------------------------------------- */

	t.updateItem = function( event ) {

		event.preventDefault();

		if ( tinyMCE )
			tinyMCE.triggerSave();

		var modal = jQuery("#wpadcb-main-modal"),
			btn = jQuery(this),
			form = btn.closest("form.wpadcb-edit-form"),
			formSerialize = form.serialize();

		modal.html( WPADCB_AJAX.savingModal );

		// open modal
		openMsgModal( modal );
		
		jQuery.post( WPADCB_AJAX.ajaxUrl, formSerialize , function(data) {
			
			var result = data.split("_|_");

			if ( result[0] == 'error' ) {
				alert( result[1] );
				closeModal( modal );
			} else {
				
				jQuery(".wpadcb-modal-saving").fadeOut( 250 , function() {
					jQuery(this).html( result[1] ).fadeIn(250);
				});
				jQuery(document).trigger( "wpadcb_update_item/success" , data );

				setTimeout( function(){
					closeModal( modal );
				}, 1000 );
			}

		});

	}

	/* Delete item
	---------------------------------------------------------- */	

	t.deleteItem = function( event ) {

		event.preventDefault();

		var btn = jQuery(this),
			cID = btn.attr("data-item-id"),
			nonce = btn.attr("data-action-nonce"),
			modal = jQuery("#wpadcb-main-modal"),
			container = btn.closest(".wpadcb-modal-container");

		container.html( WPADCB_AJAX.processingModal );

		jQuery.post( WPADCB_AJAX.ajaxUrl, { action: 'wpadcb-delete-item' , id: cID, _wpdcb_user_action_nonce: nonce } , function(data) {
			if ( data == 'success' ) {
				jQuery(document).trigger( "wpadcb_delete_item/success" , data );
				setTimeout( function(){
					closeModal( modal );
				}, 1000 );
			}
		});

	}

	/* Content Boxes
	---------------------------------------------------------- */

	var initAccordion = function( obj ) {
		obj.accordion({
		    transitionSpeed: 400,
            controlElement: '[data-accord-control]',
            contentElement: '[data-accord-content]',
            groupElement: '[data-accord-group]',
            singleOpen: true,
		});
	}

	var sortableAccordion = function( obj ) {

		if ( obj.length > 0 ) {
			obj.sortable({
					items: ".wpadcb-single-accord",
					handle: ".wpadcb-sortable-handle",
					placeholder: "ui-state-highlight",
					connectWith: "div.wpadcb-accord-container",
				});
		}

	}

	t.addContent = function( event ) {

		event.preventDefault();

		var btn = jQuery(this),
			btnHTML = btn.html(),
			section = btn.closest("#wpadcb-form-contents_type"),
			container = section.find(".wpadcb-accord-container");

		btn.addClass("adding").html( WPADCB_AJAX.loadingBtn );

		jQuery.post( WPADCB_AJAX.ajaxUrl, { action: 'wpadcb-add-content' }, function(data) {

			var result = data.split("_|_");

			btn.removeClass("adding").html( btnHTML );
			container.find(".wpadcb-single-accord").each(function(){
				var accord = jQuery(this);
				if ( accord.hasClass("open") ) {
					jQuery(this).removeClass("open");
					//jQuery(this).find(".wpadcb-accord-content").slideUp(150);
				}
			});
			container.append( result[1] );

			/* re-trigger accordion & sortable */
			initAccordion( container.find(".wpadcb-single-accord") );
			sortableAccordion( container );

		});

	}

	t.removeContent = function( event ) {

		event.preventDefault();

		var btn = jQuery(this),
			container = btn.closest(".wpadcb-single-accord");

		var answer = confirm( WPADCB_AJAX.deleteContent );
			if (answer) {
				container.slideUp( 250 , function() {
					container.remove();
				});
			}

	}

	/* Colorpicker
	---------------------------------------------------------- */

	var initColorPicker = function() {
		jQuery('#wpadc-builder .wpadcb-input-colorpicker').each(function(){
			var input	= jQuery(this),
				parent	= input.parent();
				
			input.wpColorPicker();
		});
	}

	/* Show if selector
	---------------------------------------------------------- */

	var showIfSelector = function( obj , selector ) {

		var sValue = obj.attr("data-show-if-value"),
			sOperator = obj.attr("data-show-if-operator"),
			selectorObj = jQuery( "#" + selector );

		if ( sValue !== undefined && sOperator !== undefined ) {

			if ( sOperator == '==' ) {
				var inputValue = selectorObj.val();

				if ( inputValue === undefined || sValue != inputValue )
					obj.hide();

			} else if ( sOperator == '!=' ) {
				var inputValue = selectorObj.val();

				if ( inputValue === undefined || sValue == inputValue )
					obj.hide();

			} else if ( sOperator == 'checked' ) {
				var inputValue = selectorObj.attr('checked');
				if ( inputValue === undefined )
					obj.hide();
			} else if ( sOperator == 'opt_selected' ) {
				var inputValue = selectorObj.val();
				if ( inputValue === undefined || sValue == '' || sValue != inputValue )
					obj.hide();
			} else if ( sOperator == 'contains' ) {
				var inputValue = selectorObj.val();
				if ( inputValue === undefined || sValue.indexOf( inputValue ) < 0 )
					obj.hide();
			} else if ( sOperator == 'contains_reverse' ) {
				var inputValue = selectorObj.val();
				if ( inputValue === undefined || inputValue.indexOf( sValue ) < 0 )
					obj.hide();
			}

		}

	}

	/* Powertip
	---------------------------------------------------------- */

	var featureDisabledTip = function() {

		jQuery(".wpadcb-feature-disabled").powerTip({
			followMouse: true,
			fadeInTime: 100
		});

	}

	/* If option selector is selected
	---------------------------------------------------------- */
	t.optSelected = function( event ) {

		event.preventDefault();

		var btn = jQuery(this),
			value = btn.attr("data-optselector-value"),
			container = btn.closest(".wpadcb-input-type-optselector"),
			input = container.find("input.wpadcb-optselector-input");

		container.find(".wpadcb-optselector-options > button").each( function() {
			jQuery(this).removeClass("wpadcb-optselector-active");
		});

		btn.addClass("wpadcb-optselector-active");
		if ( input !== undefined ) {
			input.val(value);

			// trigger show if changes
			var input_id = input.attr("id");
			jQuery("#wpadc-builder").find('[data-show-if="'+input_id+'"]').each( function() {
				var obj = jQuery(this),
					sValue = obj.attr("data-show-if-value"),
					selectorObj = jQuery( '#' + input_id ),
					sOperator = obj.attr("data-show-if-operator"),
					inputValue = selectorObj.val();

				if ( sOperator == 'contains' ) {
					if ( inputValue === undefined || sValue == '' || sValue.indexOf( inputValue ) < 0 )
						obj.slideUp( 250 );
					else
						obj.slideDown( 250 );
				} else if ( sOperator == 'contains_reverse' ) {
					if ( inputValue === undefined || sValue == '' || inputValue.indexOf( sValue ) < 0 )
						obj.slideUp( 250 );
					else
						obj.slideDown( 250 );
				} else {
					if ( inputValue === undefined || sValue == '' || sValue != inputValue || ( sOperator == 'contains' && sValue.indexOf( inputValue ) < 0 ) )
						obj.slideUp( 250 );
					else
						obj.slideDown( 250 );
				} // end - 	sOperator
				
			});

		}

	}

	/* If multiple option selector is selected
	---------------------------------------------------------- */
	t.multiOptSelected = function( event ) {

		event.preventDefault();

		var btn = jQuery(this),
			value = '',
			container = btn.closest(".wpadcb-input-type-multioptselector"),
			input = container.find("input.wpadcb-multioptselector-input");

		// add or remove active
		if ( btn.hasClass("wpadcb-optselector-active") ) {
			btn.removeClass("wpadcb-optselector-active");
		} else {
			btn.addClass("wpadcb-optselector-active");
		}

		if ( input !== undefined ) {

			container.find(".wpadcb-multioptselector-options > button").each( function() {
				if ( jQuery(this).hasClass("wpadcb-optselector-active") ) {
					value += ( value != '' ? ',' : '' ) + jQuery(this).attr("data-multioptselector-value");
				}
			});

			// add in new value
			input.val(value);

			// trigger show if changes
			var input_id = input.attr("id");
			jQuery("#wpadc-builder").find('[data-show-if="'+input_id+'"]').each( function() {
				var obj = jQuery(this),
					sValue = obj.attr("data-show-if-value"),
					selectorObj = jQuery( '#' + input_id ),
					sOperator = obj.attr("data-show-if-operator"),
					inputValue = selectorObj.val();

				if ( sOperator == 'contains' ) {
					if ( inputValue === undefined || sValue == '' || sValue.indexOf( inputValue ) < 0 )
						obj.slideUp( 250 );
					else
						obj.slideDown( 250 );
				} else if ( sOperator == 'contains_reverse' ) {
					if ( inputValue === undefined || sValue == '' || inputValue.indexOf( sValue ) < 0 )
						obj.slideUp( 250 );
					else
						obj.slideDown( 250 );
				} else {
					if ( inputValue === undefined || sValue == '' || sValue != inputValue || ( sOperator == 'contains' && sValue.indexOf( inputValue ) < 0 ) )
						obj.slideUp( 250 );
					else
						obj.slideDown( 250 );
				} // end - 	sOperator
				
			});

		} // end - input

	}

	/* Upload single image
	---------------------------------------------------------- */

	t.uploadImage = function( event ) {

		event.preventDefault();

		var btn = jQuery(this),
			container = btn.closest(".wpadcb-input-image-wrapper"),
			upload = true;

		// If the media frame already exists, reopen it.
		if ( media_frame ) {
			media_frame.close();
		}

		// Create the media frame.
		media_frame = wp.media.frames.media_frame = wp.media({
			title: btn.data( 'title' ),
			button: {
				text: btn.data( 'button_text' ),
			},
		});

		// When image(s) selected, run a callback.
		media_frame.on( 'select' , function() {
			var attachment = media_frame.state().get('selection').first(),
				inputTarget = btn.data( 'input_id' );
			
			// insert url into input field
			jQuery( "#" + inputTarget ).val(attachment.attributes.id);
			container.find(".wpadcb-input-image-field > img")
				.attr('src', attachment.attributes.url)
				.removeClass("wpadcb-hide-image");
		} );

		// Finally, open the modal
		media_frame.open();

	}

	/* Update overview via ajax
	---------------------------------------------------------- */

	t.updateOverview = function( data ) {

		var container = jQuery(".wpadcb-items-wrapper");
		container.html( WPADCB_AJAX.updatingBox );
		jQuery.post( WPADCB_AJAX.ajaxUrl, { action: 'wpadcb-update-overview' } , function(data) {
			container.html( data );
		});

	}

	/* start wizard 
	---------------------------------------------------------- */

	t.startWizardAction = function( event ) {

		event.preventDefault();

		var btn = jQuery(this),
			actionType = btn.attr("data-action-type"),
			action = false,
			modal = jQuery("#wpadcb-main-modal"),
			container = btn.closest("#wpadcb-start-wizard"),
			form = false,
			formSerialize = false;

		modal.html( WPADCB_AJAX.loadingModal );
		openMsgModal( modal );

		if ( actionType == 'prev' || actionType == 'next' || actionType == 'done' || actionType == 'close' ) {

			form = container.find("form#wpadcb-startw-step-form");

			form.find(".wpadcb-has-editor").each(function() {
				if ( tinyMCE )
					tinyMCE.triggerSave();
			});

			formSerialize = form.serialize();
		} else if ( actionType == 'reset' ) {
			var actionNonce = btn.attr("data-action-nonce");
			formSerialize = '_wpadcb_start_wizard_nonce=' + actionNonce;
		} // end - actionType

		// get action
		switch( actionType ) {
			case 'prev': action = 'wpadcb-startw-back'; break;
			case 'next':
			case 'done': action = 'wpadcb-startw-submit'; break;
			case 'close': action = 'wpadcb-startw-close'; break;
			case 'reset': action = 'wpadcb-startw-reset'; break;
		}

		jQuery.post( WPADCB_AJAX.ajaxUrl, "action="+action+"&"+formSerialize , function(data) {
			if ( actionType == 'done' ) {
				modal.find(".wpadcb-modal-loading").fadeOut( 250 , function() {
					jQuery(this).html( WPADCB_AJAX.wizardCompletedModal ).fadeIn(250);
					setTimeout(() => {
						window.location.reload(true);
					}, 300);
				});
			} else {
				window.location.reload(true);
			} // end - actionType
		})
		.fail(function(error) {
			console.log(error);
		});

	}

	/* Run the init function
	---------------------------------------------------------- */
	jQuery(document).ready(function(){

		// trigger loading btn
		jQuery(document).on( "click" , ".wpadcb-loading-btn", WPADC_Backend.loadingBtn );

		/// modal trigger
		jQuery(document).on( "click" , ".wpadcb-modal-trigger", WPADC_Backend.triggerModal );
		jQuery(document).on( "click" , ".wpadcb-close-modal", WPADC_Backend.closeModal );
		jQuery(document).on( "click" , ".wpadcb-close-selectedmodal", WPADC_Backend.closeSelectedModal );
		jQuery(document).on( "click" , ".wpadcb-open-selectedmodal", WPADC_Backend.openSelectedModal );

		// create new item
		jQuery(document).on( "click" , "button.wpadcb-new-item" , WPADC_Backend.addNewItem );
		jQuery(document).on( "click" , "button.wpadcb-create-item" , WPADC_Backend.createItem );

		// Update item
		jQuery(document).on( "click" , "button.wpadcb-save-changes" , WPADC_Backend.updateItem );

		// Delete item
		jQuery(document).on( "click" , "button.wpadcb-delete-item" , WPADC_Backend.deleteItem );

		// init Accordions
		initAccordion( jQuery(".wpadcb-accord-container .wpadcb-single-accord") );
		sortableAccordion( jQuery(".wpadcb-accord-container") );
		jQuery(document).on( "click" , "button.wpadcb-add-content", WPADC_Backend.addContent );
		jQuery(document).on( "click" , "button.wpadcb-delete-content", WPADC_Backend.removeContent );

		// color picker 
		initColorPicker();

		// upload image
		jQuery(document).on( "click" , ".wpadcb-input-image-btn", WPADC_Backend.uploadImage );

		// option selector listener
		jQuery(document).on( "click" , ".wpadcb-optselector-btn", WPADC_Backend.optSelected );

		// multi option selector listener
		jQuery(document).on( "click" , ".wpadcb-multioptselector-btn", WPADC_Backend.multiOptSelected );

		// update overview listener
		jQuery(document).on( "wpadcb_create_item/success" , WPADC_Backend.updateOverview );
		jQuery(document).on( "wpadcb_delete_item/success" , WPADC_Backend.updateOverview );

		// start wizard
		jQuery(document).on( "click" , ".wpadcb-startw-action", WPADC_Backend.startWizardAction );

		// powertip
		featureDisabledTip();

		// Show if Selector
		jQuery("#wpadc-builder [data-show-if]").each( function() {
			var obj = jQuery(this),
				selector = obj.attr("data-show-if");

			if ( selector !== undefined ) {

				showIfSelector( obj , selector );

				jQuery(document).on( "change" , "#" + selector , function() {
					var sValue = obj.attr("data-show-if-value"),
						sOperator = obj.attr("data-show-if-operator"),
						selectorObj = jQuery( this );

					if ( sValue !== undefined && sOperator !== undefined ) {

						if ( sOperator == '==' ) {
							var inputValue = selectorObj.val();

							if ( inputValue === undefined || sValue != inputValue )
								obj.slideUp( 250 );
							else
								obj.slideDown( 250 );

						} else if ( sOperator == '!=' ) {
							var inputValue = selectorObj.val();

							if ( inputValue === undefined || sValue == inputValue )
								obj.slideUp( 250 );
							else
								obj.slideDown( 250 );

						} else if ( sOperator == 'checked' ) {
							var inputValue = selectorObj.attr('checked');
							if ( inputValue === undefined )
								obj.slideUp( 250 );
							else
								obj.slideDown( 250 );
						} else if ( sOperator == 'contains' ) {
							var inputValue = selectorObj.val();

							if ( inputValue === undefined || sValue.indexOf( inputValue ) < 0 )
								obj.slideUp( 250 );
							else
								obj.slideDown( 250 );
						} else if ( sOperator == 'contains_reverse' ) {
							var inputValue = selectorObj.val();

							if ( inputValue === undefined || inputValue.indexOf( sValue ) < 0 )
								obj.slideUp( 250 );
							else
								obj.slideDown( 250 );
						}

					}
				});

			}
			
		});	

	});


} // end - WPADC_Backend