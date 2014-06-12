( function( $ ){
	var $available_fields,
		$drop_target,
		$processing_fields,
		edit_col,
		saved_values = {},
		warn_on_leave;

	$( document ).ready( function() {
		/* Set up Warn On Leave */
		warn_on_leave = false;

		window.onbeforeunload = function() {
			if ( warn_on_leave ) {
				return CACAP_Admin.warn_on_leave;
			}
		};

		$( '#cacap-header-submit' ).click( function() {
			warn_on_leave = false;
		} );

		/* Set up the Profile Header (Public) section */
		$( '.cacap-sortable' ).sortable( {
			change: function() {
				warn_on_leave = true;
			},
			items: '> li',
			receive: function( event, ui ) {
				if ( ! process_header_field_drop( event, ui ) ) {;
					ui.sender.sortable( 'cancel' );
				}
			},
			connectWith: '.cacap-sortable'
		} );

		$( '#cacap-form-cacap-profile-header-public' ).submit( function( e ) {
			process_form_submit_header_public( e );
		} );

		$( '#cacap-form-cacap-profile-header-edit' ).submit( function( e ) {
			process_form_submit_header_edit( e );
		} );

		/* Set up the Profile Header (Edit) section */
		$( '.cacap-profile-edit-columns > div > ul' ).sortable( {
			change: function() {
				warn_on_leave = true;
			},
			connectWith: '.cacap-profile-edit-columns > div > ul'
		} );
	} );

	function process_header_field_drop( event, ui ) {
		// Hide/unhide inner-label elements
		$( '.cacap-sortable' ).each( function( k, v ) {
			if ( 0 == $( v ).children( 'li' ).length ) {
				$( v ).children( '.cacap-inner-label' ).show();
			} else {
				$( v ).children( '.cacap-inner-label' ).hide();
			}
		} );

		// Brief Description and About You can't have multiples
		$drop_target = $( event.target );
		if ( $drop_target.hasClass( 'cacap-single' ) ) {
			if ( 1 < $drop_target.find( 'li' ).length ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Process Submit for Header (Public) tab.
	 */
	function process_form_submit_header_public( event ) {
		// Brief Descriptor
		saved_values.brief_descriptor = '';
		$processing_fields = $( '#cacap-brief-descriptor' ).children( 'li' );
		if ( $processing_fields.length ) {
			saved_values.brief_descriptor = $processing_fields.data( 'field-id' )
		}

		// About You
		saved_values.about_you = '';
		$processing_fields = $( '#cacap-about-you' ).children( 'li' );
		if ( $processing_fields.length ) {
			saved_values.about_you = $processing_fields.data( 'field-id' )
		}

		// Vitals
		saved_values.vitals = [];
		$processing_fields = $( '#cacap-vitals' ).children( 'li' );
		if ( $processing_fields.length ) {
			$processing_fields.each( function( k, v ) {
				saved_values.vitals.push( $( v ).data( 'field-id' ) );
			} );
		}

		// Convert to json and send along with the payload
		$( event.target ).append( '<input type="hidden" name="cacap-saved-values-header-public" id="cacap-saved-values" value="" />' );
		$( '#cacap-saved-values' ).val( JSON.stringify( saved_values ) );
	}

	/**
	 * Process Submit for Header (Edit) tab.
	 */
	function process_form_submit_header_edit( event ) {
		for ( var i = 0; i <= 1; i++ ) {
			if ( i == 0 ) {
				edit_col = 'left';
			} else {
				edit_col = 'right';
			}

			saved_values = [];

			$processing_fields = $( '#cacap-profile-edit-column-' + edit_col + ' > ul > li' );
			if ( $processing_fields.length ) {
				$processing_fields.each( function( k, v ) {
					saved_values.push( $( v ).data( 'field-id' ) );
				} );
			}

			// Convert to json and send along with the payload
			$( event.target ).append( '<input type="hidden" name="cacap-saved-values-header-edit-' + edit_col + '" id="cacap-saved-values-' + edit_col + '" value="" />' );
			$( '#cacap-saved-values-' + edit_col ).val( JSON.stringify( saved_values ) );
		}
	}

})( jQuery );
