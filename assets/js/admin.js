( function( $ ){
	var $available_fields,
		$cacap_vitals,
		$drop_target,
		$removed_field,
		cloned_available_field,
		cloned_available_field_id;

	$( document ).ready( function() {
		$available_fields = $( '#available-fields' );
/*
		$available_fields.sortable( {
			placeholder: 'ui-state-highlight',
			revert: true
		} );
		*/

		$( '#available-fields li' ).draggable( {
			revert: 'invalid',
			snap: '#cacap-vitals',
			snapMode: 'inner'
		} );

		$cacap_vitals = $( '#cacap-vitals' );

		$( '.cacap-droppable' ).droppable( {
			accept: '#available-fields li',
			drop: function( event, ui ) {
				process_vital_add( event, ui );
			}
		} );
	} );

	function process_vital_add( event, ui ) {
		$drop_target = $( event.target );

		cloned_available_field = ui.draggable.clone();
		cloned_available_field.removeAttr( 'style' ).removeAttr( 'class' );
		cloned_available_field_id = cloned_available_field.attr( 'id' ).replace( 'available', 'vital' );
		cloned_available_field.attr( 'id', cloned_available_field_id );
		cloned_available_field.prepend( '<a href="#" class="remove-vital">x</a>' );

		// Add the field and make sortable
		$drop_target.append( cloned_available_field );

		$cacap_vitals = $( '#cacap-vitals' );
		$cacap_vitals.sortable( {
			items: 'li:not(.cacap-inner-label)'	
		} );

		// Hide the original
		ui.draggable.hide();

		// Bind the remove click
		$drop_target.find( '.remove-vital' ).on( 'click', function( e ) {
			process_vital_remove( e );
		} );

		// Only vitals can have multiples
		if ( $drop_target.attr( 'id' ) !== 'cacap-vitals' ) {
			if ( 1 < $drop_target.find( 'li' ).length ) {
				$removed_field = $( '#' + cloned_available_field_id );
				console.log($removed_field);
				revert_available_field();
			}
		}

		return false;
	}

	function process_vital_remove( e ) {
		e.preventDefault();

		$removed_field = $( e.target ).closest( '.cacap-droppable li' );
		
		// Not sure why this failsafe is necessary, but sometimes the
		// event double-fires
		if ( $removed_field.length ) {
			revert_available_field();
		}

		return false;
	}

	function revert_available_field() {
		// Unhide the original
		$( '#' + $removed_field.attr( 'id' ).replace( 'vital', 'available' ) ).show().removeAttr( 'style' ).css( 'position', 'relative' );

		$removed_field.remove();
	}
})( jQuery );
