jQuery(document).ready( function($) {
	resize_drag_handles();

	// Set up sortable widgets
	$('body.profile-edit #cacap-widget-list').sortable({
		placeholder: 'ui-state-highlight',
		containment: 'parent',
		handle: '.cacap-drag-handle',
		stop: function( event, ui ) {
			$('#cacap-widget-order').val($(this).sortable( 'toArray' ));
		}
	});

	// Click to edit - delegated from parent
	window.cacapedittoggles = {};
	$('#cacap-widget-list').on('click', '.cacap-click-to-edit', function(e){
		var edit_div = this;

		var $widget = $(edit_div).closest('ul#cacap-widget-list li');	
		var widget_id = $widget.attr('id');
		var is_widget_toggled = is_edit_toggled(widget_id);
		var widget_type = get_widget_type_from_class( $widget.attr('class') );

		var edit_input = $(edit_div).find('.cacap-show-on-edit');
		var edit_title = $(edit_div).find('.cacap-hide-on-edit');
		var edit_input_field = $(edit_div).find('.cacap-edit-input');

		if ( ! is_widget_toggled ) {
			$(edit_input).show();
			$(edit_title).hide();

			var value_to_cache;

			switch ( widget_type ) {
				case 'positions' :
					value_to_cache = $(edit_input).html();
					break;
				
				default :
					$(edit_input_field).autogrow({animate:false});
					$(edit_input_field).trigger('keyup');

					value_to_cache = $(edit_input_field).val();

					// reset the val to get focus to the end. Dumb
					$(edit_input_field).focus().val('').val(value_to_cache);

					break;

			}

			// Set in global object in case of Cancel
			window.cacapedittoggles[widget_id] = value_to_cache;
		} else {
			var restore_me = false;

			// Only do anything if clicking OK or Cancel
			if ( $(e.target).hasClass( 'cacap-ok' ) ) {
				convert_fields_to_display( this, 'new' );
				restore_me = true;
			} else if ( $(e.target).hasClass( 'cacap-cancel' ) ) {
				convert_fields_to_display( this, 'old' );
				restore_me = true;
			}

			if ( restore_me ) {
				delete window.cacapedittoggles[widget_id];
				$(edit_input).hide();
				$(edit_title).show();

				// Check to see whether the partner box is empty, and if so, open
				var my_buddy = $(this).siblings('.cacap-click-to-edit');
				var my_buddy_content = $(my_buddy).find('.cacap-edit-input').val();

				if ( '' == my_buddy_content ) {
					$(my_buddy).trigger('click');
				}
			}
		}

		resize_drag_handles();
		return false;
	});

	window.newwidget_count = 0;
	$('#cacap-new-widget-types li').on('click', function(e){
		if ($(this).hasClass('cacap-has-max')) {
			return false;
		}
		window.newwidget_count++;
		var widget_type = $(this).attr('id').slice(17);

		// Get the prototype and swap with the autoincrement
		var proto = $('#cacap-widget-prototype-'+widget_type).html();
		proto = proto.replace(/newwidgetkey/g, 'newwidget' + window.newwidget_count);

		var new_widget_id = "cacap-widget-newwidget" + window.newwidget_count;

		$('#cacap-widget-list').append('<li id="' + new_widget_id + '" class="cacap-widget-' + widget_type + '">' + proto + '</li>');
		var widget_order_input = $('#cacap-widget-order');
		var widget_order = widget_order_input.val().split(',');
		widget_order.push(new_widget_id);
		widget_order_input.val(widget_order);

		// Set focus on 'title', unless it's disabled
		var $new_widget = $('#' + new_widget_id);
		var $new_widget_title = $new_widget.find('.cacap-widget-title');
		if ( 'disabled' == $new_widget_title.find('.cacap-edit-input').attr('disabled') )	{
			$new_widget.find('.cacap-widget-content').trigger('click').focus();	
		} else {
			$new_widget_title.trigger('click').focus();
		}

		// Add the type class
		$new_widget.addClass( 'cacap-widget-' + widget_type );

		// Clone the Add New prototype for Positions
		if ( 'positions' == widget_type ) {
			clone_add_new_position_fields( $new_widget );
		}

		resize_drag_handles();

		return false;
	});

	$('#cacap-widget-list').on('click', '.cacap-widget-remove', function(e){
		var widget_order_input = $('#cacap-widget-order');
		var widget_order = widget_order_input.val().split(',');
		var widget_id = $(this).closest('#cacap-widget-list li').attr('id');
		var wo_key = $.inArray(widget_id, widget_order);

		widget_order.splice(wo_key, 1);
		widget_order_input.val(widget_order);

		$('#'+widget_id).remove();

		return false;
	});

	/**
	 * Positions setup
	 */
	$positions_widget = $('.cacap-widget-positions');
	if ( $positions_widget.length ) {
		// On load, add the Add New Position fields 
		clone_add_new_position_fields( $positions_widget );
		
		// Also swap out the lousy 'newwidgetkey' stuff. Blargh
		$positions_widget.html( $positions_widget.html().replace(/\bnewwidgetkey\b/g, 'cacap_positions') );

		// Initialize autocomplete for existing widget
		positions_autocomplete_setup( $positions_widget );	
	}

	// Delete a position
	$('#cacap-widget-list').on('click', '.cacap-delete-position', function(e){
		var position_id = $(this).attr('id').split('-').pop();
		$('#cacap-position-'+position_id).remove();
		$(this).remove();
	});

	function is_edit_toggled(id) {
		return window.cacapedittoggles.hasOwnProperty(id);
	}

	function resize_drag_handles() {
		// Set height on draggable handles
		// Somebody shoot me
		$('body.profile-edit .cacap-drag-handle').each( function( k, v ) {
			$(v).css('height','0').css('height', $(v).parent().css('height'));	
		});
	}

	function clone_add_new_position_fields( $new_widget ) {
		$fields = $new_widget.find('#cacap-position-new').clone();
		
		// Don't need this class anymore
		$fields.removeClass('hide-if-js');

		// Swap 'new' with proper iterator
		// Subtract 1 for the prototype
		var existing_positions_count = $new_widget.find('ul').length - 1;
		var this_position_count = existing_positions_count + 1;
		var tpid = 'cacap-position-' + this_position_count;
		var thefor, thename, theid;

		$fields.removeAttr('id').attr('id', tpid);
		$fields.find('label').each( function(k, v) {
			thefor = $(this).attr('for');
			$(this).attr('for', thefor.replace('cacap-position-new', tpid));
		});
		$fields.find('input,select').each( function(k, v) {
			theid = $(this).attr('id');
			$(this).attr('id', theid.replace('cacap-position-new', tpid));
			thename = $(this).attr('name');
			$(this).attr('name', thename.replace(/\bnew\b/, this_position_count));
		});

		// No delete button
		$fields.find('.cacap-delete-position').remove();
		
		$fields.prependTo( $new_widget.find('.cacap-edit-content-input') );
	}

	function convert_fields_to_display( clicked, new_or_old ) {
		var $widget, $edit_div, $edit_input_field, $edit_title, $current_position, widget_id, widget_type_regex, widget_type, the_value, positions, this_position;

		$widget = $(clicked).closest('ul#cacap-widget-list li');	
		$edit_div = $(clicked).closest('.cacap-click-to-edit'); 

		if ( 'new' == new_or_old ) {
			// 'OK' - convert input value to plain text, and swap
			// into static field
			widget_type = get_widget_type_from_class( $widget.attr('class') );
			widget_id = $widget.attr('id');

			switch ( widget_type ) {
				case 'positions' :
					positions = [];
					$widget.find('.cacap-edit-content-input').children('ul').each( function( index ) {
						$current_position = $(this);

						if ( 'new' != $current_position.attr('id').split('-').pop() ) {
							positions.push({
								'college': $current_position.find('.cacap-position-field-college').val(),
								'department': $current_position.find('.cacap-position-field-department').val(),
								'title': $current_position.find('.cacap-position-field-title').val()
							});
						}
					} );

					the_value = '';
					
					for ( var i=0; i<positions.length; i++ ) {
						this_position = '';
						if ( positions[i].college && positions[i].department && positions[i].title ) {
							this_position += '<span class="cacap-positions-title">' + positions[i].title + '</span> ';
							this_position += '<span class="cacap-positions-department">' + positions[i].department + '</span>';
							this_position += '<span class="cacap-positions-college">' + positions[i].college + '</span>';
						}

						if ( this_position.length ) {
							the_value += '<li>' + this_position + '</li>';
						}
					}

					if ( the_value.length ) {
						the_value = '<ul class="cacap-positions-list">' + the_value + '</ul>';
					}
					
					break;

				default :
					the_value = $edit_div.find('.cacap-edit-input').val().replace(/\r?\n/g, '<br />');
					break;
			}

			window.cacapedittoggles[widget_id] = the_value;
		} else {
			// 'Cancel' - replace input value with cached value
			// @todo This doesn't handle Positions yet
			the_value = window.cacapedittoggles[widget_id]; 
		}

		$edit_div.find('.cacap-hide-on-edit').html(the_value);
	}

	function get_widget_type_from_class( classname ) {
		widget_type_regex = /cacap\-widget\-([a-zA-Z0-9\-]+)/;
		return classname.match(widget_type_regex).pop();
	}
	
	function positions_autocomplete_setup( $widget ) {
		if ( $widget ) {
			var autocomplete_ep = ajaxurl + '?action=cacap_position_suggest';
			$widget.find('.cacap-position-field-department').autocomplete({
				source: autocomplete_ep + '&field=department',
				minLength: 2,
			});

			// @todo Title?
		}
	}
},(jQuery));
