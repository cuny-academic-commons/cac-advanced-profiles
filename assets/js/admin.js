( function( $ ){
	var $available_fields,
		$drop_target,
		$processing_fields,
		saved_values = {};

	$( document ).ready( function() {
		/* Set up the Profile Header (Public) section */
		$( '.cacap-sortable' ).sortable( {
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

		/* Set up the Profile Header (Edit) section */
		$( '.cacap-profile-edit-columns > div > ul' ).sortable( {
			connectWith: '.cacap-profile-edit-columns > div > ul'
		} );
	} );

	function process_header_field_drop( event, ui ) {
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
	 * Process Submit for Header tab.
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
})( jQuery );
