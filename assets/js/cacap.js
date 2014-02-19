// Ensure the global `wp` object exists.
window.wp = window.wp || {};

(function($){
	var CACAP = function() {
		var self = this,
			about_you_max_length = 350,
			class_to_add,
			currently_editing = '',
			currently_editing_position,
			exit_confirm,
			field_char_count,
			hallo_top,
			jcw_id,
			jcw_target_is_button,
			keypress_code,
			new_widget_count,
			new_widget_prototype,
			ok_or_cancel,
			position_id,
			positions,
			positions_count,
			positions_static_text,
			this_position_static_text,
			wid,
			widget_order,
			widget_value_cache = {},
			window_height,
			wtype,
			$about_you,
			$about_you_gloss,
			$current_position,
			$current_field,
			$currently_editing,
			$hallo_toolbar,
			$jcw_half, // "just clicked widget" 
			$jcw_target,
			$new_widget_button,
			$position_delete_button,
			$position_field,
			$position_label,
			$positions_fields,
			$positions_widget,
			$positions_widget_inputs,
			$positions_widget_static_text,
			$w,
			$widget_list,
			$widget_order,
			$wtitle;

		/**
		 * Set up the 'js' body class.
		 *
		 * BuddyPress should do this, but just in case
		 */
		function init_bodyclass() {
			document.body.className = document.body.className.replace( /no-js/, 'js' );
		}

		/**
		 * Set up the sticky header.
		 */
		function init_stickyheader() {
			window_height = "innerHeight" in window ? window.innerHeight : document.documentElement.offsetHeight;

			if ( window_height < $(document).height() - 200 ) {
				$('.cacap-hero-row').waypoint('sticky', {
					offset: 40,
					wrapper: '<div class="cacap-hero-row-sticky" />' 
				});
			}
		}

		/**
		 * Initialize the sortable widgets.
		 */
		function init_sortable_widgets() {
			$widget_order = $( '#cacap-widget-order' );

			$widget_list.sortable({
				placeholder: 'ui-state-highlight',
				containment: 'parent',
				handle: '.cacap-drag-handle',
				stop: function( event, ui ) {
					$widget_order.val($(this).sortable( 'toArray' ));
				}
			});
		}

		/**
		 * Initialize editable widgets.
		 *
		 * These are the widgets that use contentEditable.
		 */
		function init_editable_widgets() {
			$('article.richtext').hallo({
				toolbar: 'halloToolbarFixed',
				plugins: {
					'halloformat': {},
					'hallolink': {},
					'hallojustify': {},
					'hallolists': {},
					'halloheadings': {}
				}
			});
		}

		/**
		 * Set up initial positions widgets.
		 */
		function init_positions_widgets() {
			$positions_widget = $('.cacap-widget-positions');
			$positions_widget_inputs = $positions_widget.find( '.cacap-positions-inputs' );
			$positions_widget_static_text = $positions_widget.find( '.cacap-positions-static-text' );
			if ( $positions_widget.length ) {
				transition_positions_to_static_text();	

				// Fix prototype classes 
				$positions_widget.html( $positions_widget.html().replace( /\bnewwidgetkey\b/g, 'cacap_positions' ) );

				// Initialize autocomplete for existing widget
				positions_autocomplete_setup( $positions_widget );	
			}

			$widget_list.on( 'click', '.cacap-add-position', function() {
				$w = $( this ).closest( '.cacap-widget-positions' );
				clone_add_new_position_fields();
			} );

			// Delete a position
			$widget_list.on( 'click', '.cacap-delete-position', function() {
				$position_delete_button = $( this );

				// Remove position based on id
				$( '#cacap-position-' + $position_delete_button.attr( 'id' ).split( '-' ).pop() ).remove();

				// Remove delete button
				$position_delete_button.remove();
			} );
		}

		/**
		 * Set up the New Widget buttons
		 */
		function init_new_widget_buttons() {
			new_widget_count = 0;

			$( '#cacap-new-widget-types li' ).on( 'click', function( e ) {
				e.preventDefault();	
				$new_widget_button = $( this );
				add_new_widget();
			} );
		}

		/**
		 * Set up the "are you sure you want to leave?" warning
		 */
		function init_exit_confirm() {
			exit_confirm = false;

			$( '#cacap-edit-form input:not(:submit), #cacap-edit-form textarea, #cacap-edit-form select' ).change( function() { 
				exit_confirm = true; 
			} ); 

			$( '#cacap-edit-form input:submit' ).on( 'click', function() { 
				exit_confirm = false; 
			} ); 

			window.onbeforeunload = function() { 
				if ( exit_confirm ) { 
					return 'Are you sure you want to leave?'; 
				} 
			}; 
		}

		/**
		 * [ESC] and [ENTER] have special meaning on widget edit inputs
		 */
		function init_widget_specialkeys() {
			$( '#cacap-edit-form' ).on( 'keydown', 'input:not(:submit), textarea', function(e){
				keypress_code = ( e.keyCode ? e.keyCode : e.which );
				$current_field = $( this );
				
				// ESC
				if ( keypress_code === 27 ) {
					$current_field.closest( '.cacap-show-on-edit' ).find( '.cacap-cancel' ).trigger( 'click' );
					return false;
				}
			
				// ENTER
				// We want to preserve Enter behavior in textareas and autocomplete
				if ( keypress_code === 13 && 'textarea' !== this.type && ! $current_field.hasClass( 'ui-autocomplete-input' ) ) {
					$current_field.closest( '.cacap-show-on-edit' ).find( '.cacap-ok' ).trigger( 'click' );
					return false;
				}
			});
		}

		/**
		 * Set up character counter for About You field
		 */
		function init_about_you_character_count() {
			$about_you = $( 'div.field_about-you textarea' );
			if ( $about_you.length !== 0 ) {

				$about_you.after('<div class="cacap-char-count-gloss">Using <span class="cacap-char-count">0</span> of ' + about_you_max_length + ' characters<span class="cacap-char-count-warning"> (additional characters will be trimmed)</span></div>'); 

				$about_you_gloss = $( '.cacap-char-count-gloss' );

				update_character_count_for_field( $about_you );
				$about_you.on( 'keyup', function() { update_character_count_for_field( $about_you ); } );
			}
		}

		/**
		 * Process the click of an OK or Cancel button.
		 */
		function process_okcancel() {
			if ( 'ok' === ok_or_cancel ) {
				// Copy new content to hidden input
				$jcw_half.find( '.editable-content-stash' ).val( $jcw_half.find( '.editable-content' ).html() );
			} else {
				// Replace the edited content with the cached value
				$jcw_half.find( '.editable-content' ).html( widget_value_cache[ wid ] );
			}

			// Remove editing class
			$jcw_half.removeClass( 'editing' );

			if ( null !== $hallo_toolbar ) {
				$jcw_half.find( '.richtext' ).css( 'padding-top', '0' );
				$hallo_toolbar.css( 'top', ( hallo_top - 40 ) + 'px' );
				$jcw_half.removeClass( 'hallo-adjusted' );
			}

			// Remove currently_editing toggle
			unmark_currently_editing();

			resize_drag_handles();
		}

		/**
		 * Process the click of an OK or Cancel button on an RSS widget
		 */
		function process_okcancel_rss() {
			// The title side is a normal editable field
			if ( $jcw_target.closest( '.cacap-widget-section-editable' ).hasClass( 'cacap-widget-title' ) ) {
				if ( 'ok' === ok_or_cancel ) {
					// Copy new content to hidden input
					$jcw_half.find( '.editable-content-stash' ).val( $jcw_half.find( '.editable-content' ).html() );
				} else {
					// Replace the edited content with the cached value
					$jcw_half.find( '.editable-content' ).html( widget_value_cache[ wid ] );
				}

			// The content side is an input field
			} else {
				if ( 'ok' === ok_or_cancel ) {
					// nothing to do?
				} else {
					$jcw_half.find( 'input.cacap-edit-input' ).val( widget_value_cache[ wid ] );
				}
			}

			// Remove editing class
			$jcw_half.removeClass( 'editing' );

			// Remove currently_editing toggle
			unmark_currently_editing();

			resize_drag_handles();
		}

		/**
		 * Process the click of an OK or Cancel button in a Positions widget.
		 */
		function process_okcancel_positions() {
			if ( 'ok' === ok_or_cancel ) {
				transition_positions_to_static_text();
			} else {

			}

			// Remove editing class
			$jcw_half.removeClass( 'editing' );

			// Remove currently_editing toggle
			unmark_currently_editing();

			resize_drag_handles();
		}

		/**
		 * Toggle editable widget areas (when clicked).
		 */
		function toggle_editable() {
			// Cache the current value of the widget, in case of Cancel
			widget_value_cache[ wid ] = $jcw_target.html();

			// Add the 'editing' class
			$jcw_half.addClass( 'editing' );

			// Gah. We have to wait for hallo to render its toolbar
			if ( ! $jcw_half.hasClass( 'hallo-adjusted' ) ) {
				setTimeout( function() {
					// Get the active element in the list - hallo creates new
					// toolbars for each element :-/
					$hallo_toolbar = null;
					$( '.hallotoolbar' ).each( function() {
						if ( 'block' == $( this ).css( 'display' ) ) {
							$hallo_toolbar = $( this );
						}
					} );

					if ( null !== $hallo_toolbar ) {
						hallo_top = parseInt( $hallo_toolbar.css( 'top' ) );
						$hallo_toolbar.css( 'top', ( hallo_top + 40 ) + 'px' );

						$jcw_half.find( '.richtext' ).css( 'padding-top', '40px' );

						$jcw_half.addClass( 'hallo-adjusted' );
					}
				}, 20 );
			}
		}

		/**
		 * Toggle editable widget RSS areas (when clicked).
		 */
		function toggle_editable_rss() {
			// Cache the current value of the widget, in case of Cancel
			widget_value_cache[ wid ] = $jcw_half.find( 'input.cacap-edit-input' ).val();

			// Add the 'editing' class
			$jcw_half.addClass( 'editing' );
		}

		/**
		 * Toggle editable positions widget area (when clicked).
		 */
		function toggle_editable_positions() {

			// Add the 'editing' class
			$jcw_half.addClass( 'editing' );
		}

		/**
		 * Get a canonical widget_type from a widget classname.
		 */
		function get_widget_type_from_class( classname ) {
			widget_type_regex = /cacap\-widget\-([a-zA-Z0-9\-]+)/;
			return classname.match(widget_type_regex).pop();
		}

		/**
		 * Transition a Positions inputs widget to static text.
		 */
		function transition_positions_to_static_text() {
			positions = [];

			$positions_widget_inputs = $positions_widget.find( '.cacap-positions-inputs' );
			$positions_widget_inputs.children('ul').each( function( index ) {
				$current_position = $( this );

				if ( 'new' !== $current_position.attr( 'id' ).split( '-' ).pop() ) {
					positions.push({
						'college': $current_position.find( '.cacap-position-field-college' ).val(),
						'department': $current_position.find( '.cacap-position-field-department' ).val(),
						'title': $current_position.find( '.cacap-position-field-title' ).val()
					});
				}
			} );

			positions_static_text = '';

			for ( var i = 0; i < positions.length; i++ ) {
				this_position_static_text = '';
				if ( positions[i].college && positions[i].department && positions[i].title ) {
					this_position_static_text += '<span class="cacap-positions-title">' + positions[i].title + '</span> ';
					this_position_static_text += '<span class="cacap-positions-department">' + positions[i].department + '</span>';
					this_position_static_text += '<span class="cacap-positions-college">' + positions[i].college + '</span>';
				}

				if ( this_position_static_text.length ) {
					positions_static_text += '<li>' + this_position_static_text + '</li>';
				}
			}

			if ( positions_static_text.length ) {
				positions_static_text = '<ul class="cacap-positions-list">' + positions_static_text + '</ul>';
			}

			$( '.cacap-positions-static-text' ).html( positions_static_text );
		}

		/**
		 * Clone 'new position' fields for a new position
		 */
		function clone_add_new_position_fields() {
			// Find and unhide
			$positions_fields = $w.find( '#cacap-position-new' ).clone().removeClass( 'hide-if-js' );
			
			// Swap 'new' with proper iterator
			// Subtract 1 for the prototype, but readd for new field
			positions_count = $w.find( 'ul' ).length;
			position_id = 'cacap-position-' + positions_count;

			// Swap 'id' attr
			$positions_fields.removeAttr( 'id' ).attr( 'id', position_id );

			// Swap 'for' attr
			$positions_fields.find( 'label' ).each( function() {
				$position_label = $( this );
				$position_label.attr( 'for', $position_label.attr( 'for' ).replace( 'cacap-position-new', position_id ) );
			} );

			// Swap 'id' and 'name' for input and select fields
			$positions_fields.find( 'input,select' ).each( function() {
				$position_field = $( this );
				$position_field.attr( 'id', $position_field.attr( 'id' ).replace( 'cacap-position-new', position_id ) );
				$position_field.attr( 'name', $position_field.attr( 'name' ).replace( /\bnew\b/, positions_count ) );
			} );

			// Don't need a Delete button
			$positions_fields.find( '.cacap-delete-position' ).remove();

			// Add to the DOM
			$positions_fields.insertAfter( $w.find( '.cacap-position-add-new-title' ) );

			// Init autocomplete
			positions_autocomplete_setup( $w );	
		}

		/**
		 * Add a new widget
		 */
		function add_new_widget() {
			// Do nothing if the max has been met for this widget type
			if ( $new_widget_button.hasClass( 'cacap-has-max' ) ) {
				return false;
			}

			// Tick the counter (used to construct unique IDs)
			new_widget_count++;

			wtype = $new_widget_button.attr( 'id' ).slice( 17 );

			// Get the prototype and swap with the autoincrement
			new_widget_prototype = $( '#cacap-widget-prototype-' + wtype ).html();
			new_widget_prototype = new_widget_prototype.replace( /newwidgetkey/g, 'newwidget' + new_widget_count );

			wid = 'cacap-widget-newwidget' + new_widget_count;

			$widget_list.append( '<li id="' + wid + '" class="cacap-widget-' + wtype + '">' + new_widget_prototype + '</li>' );

			// Update the widget order input value
			init_widget_order();
			widget_order.push( wid );
			$widget_order.val( widget_order );

			$w = $( '#' + wid );

			// Add the type class
			$w.addClass( 'cacap-widget-' + wtype );

			// If this widget doesn't allow multiple types, disable the
			// button
			if ( $new_widget_button.hasClass( 'disable-multiple' ) ) {
				$new_widget_button.addClass( 'cacap-has-max' );
			}

			// Activate editable fields
			$w.find( 'article.editable-content' ).css( 'min-height', '2em' ).attr( 'contenteditable', 'true' );

			// Add section IDs
			$w.find( '.cacap-widget-title' ).attr( 'id', wid + '-title' );
			$w.find( '.cacap-widget-content' ).attr( 'id', wid + '-content' );

			// If it's a positions field, set it up
			if ( 'positions' == wtype ) {
				clone_add_new_position_fields( $w );
			}

			init_editable_widgets();
			
			resize_drag_handles();
		}

		/**
		 * Bind events related to clicking on editable widgets.
		 *
		 * Grouped together in order to delegate click events, due to
		 * new items being dynamically added to the DOM
		 */
		function bind_widget_clicks_edit() {

			$widget_list.on( 'click', '.cacap-click-to-edit', function( e ) {
				e.preventDefault();

				$jcw_half = $( this ); 
				$jcw_target = $( e.target );
				jcw_id = $jcw_half.attr( 'id' );

				// Only allow one field to be edited at a time
				if ( currently_editing.length && jcw_id !== currently_editing ) {
					$currently_editing = $( '#' + currently_editing );

					// Offset for the header
					currently_editing_position = $currently_editing.offset();
					$.scrollTo( (currently_editing_position.top - 230) + 'px', 500 );

					$currently_editing.addClass( 'warn' );
					setTimeout( function() {
						$currently_editing.removeClass( 'warn' );
					}, 800 );
					return;
				}
				
				if ( ! currently_editing.length ) {
					mark_currently_editing( jcw_id );
				}

				jcw_target_is_button = $jcw_target.hasClass( 'button' );
				if ( jcw_target_is_button ) {
					ok_or_cancel = $jcw_target.hasClass( 'cacap-ok' ) ? 'ok' : 'cancel';
				}

				$w = $jcw_half.closest( 'ul#cacap-widget-list li' );	
				wid = $w.attr( 'id' );

				wtype = get_widget_type_from_class( $w.attr( 'class' ) );

				switch ( wtype ) {
					
					case 'positions' :
						if ( jcw_target_is_button ) {
							process_okcancel_positions();
						} else {
							toggle_editable_positions();	
						}

						break;

					case 'rss' :
					case 'twitter' :
						if ( jcw_target_is_button ) {
							process_okcancel_rss();
						} else {
							toggle_editable_rss();	
						}
						break;

					default :
						if ( jcw_target_is_button ) {
							process_okcancel();
						} else if ( $jcw_target.closest( 'article' ).hasClass( 'editable-content' ) ) {
							toggle_editable();	
						}

						break;
				}

				resize_drag_handles(); 
			} );
		}

		function bind_widget_clicks_delete() {
			$widget_list.on( 'click', '.cacap-widget-remove', function() {
				$w = $( this ).closest( '#cacap-widget-list li' );
				delete_widget();
				return false;
			} );
		}

		/**
		 * Mark a widget as "currently editing"
		 */
		function mark_currently_editing( jcw_id ) {
			currently_editing = jcw_id;

			// Remove other contentEditables
			$widget_list.find('.cacap-click-to-edit').each( function() {
				if ( currently_editing === this.id ) {
					$( this ).find( 'article.editable-content' ).attr( 'contenteditable', true );
				} else {
					$( this ).find( 'article.editable-content' ).attr( 'contenteditable', false );
				}
			} );

			// Mark that editing is in process (for widget styling)
			$widget_list.addClass( 'currently-editing' );
		}

		/**
		 * Unmark as "currently editing"
		 */
		function unmark_currently_editing() {
			currently_editing = '';
			$widget_list.find( 'article.editable-content' ).attr( 'contenteditable', true );
			$widget_list.removeClass( 'currently-editing' );
		}

		/**
		 * Delete just-clicked widget
		 */
		function delete_widget() {
			init_widget_order();
			wid = $w.attr( 'id' );

			// Remove the widget from the widget order
			widget_order.splice( $.inArray( wid, widget_order ), 1 );
			$widget_order.val( widget_order );

			// If the new widget button for this type is disabled
			// due to a max number of widgets, remove that restriction
			wtype = get_widget_type_from_class( $w.attr( 'class' ) );
			$new_widget_button = $( '#cacap-new-widget-' + wtype );
			if ( $new_widget_button.hasClass( 'disable-multiple' ) ) {
				$new_widget_button.removeClass( 'cacap-has-max' );
			}

			// Remove the widget
			$w.remove();
		}

		/**
		 * Init the widget order
		 */
		function init_widget_order() {
			widget_order = $widget_order.val().split( ',' );
		}
	
		/**
		 * Set up autocomplete for Positions widget
		 */
		function positions_autocomplete_setup( $widget ) {
			if ( $widget ) {
				var autocomplete_ep = ajaxurl + '?action=cacap_position_suggest';
				$widget.find('.cacap-position-field-department').autocomplete({
					source: autocomplete_ep + '&field=department',
					minLength: 2
				});

				// @todo Title?
			}
		}

		/**
		 * Update character count for the passed field
		 */
		function update_character_count_for_field( $field ) {
			field_char_count = $field.val().length;
			$about_you_gloss.find( 'span.cacap-char-count' ).html( field_char_count );

			if ( field_char_count > about_you_max_length ) {
				class_to_add = 'cacap-length-red';	
			} else if ( field_char_count > about_you_max_length - 40 ) {
				class_to_add = 'cacap-length-yellow';	
			} else {
				class_to_add = 'cacap-length-green';	
			}

			$about_you_gloss.removeClass( 'cacap-length-red cacap-length-yellow cacap-length-green' );
			$about_you_gloss.addClass( class_to_add );
		}

		/**
		 * Set height on draggable handles
		 *
		 * This is terrible but I can't figure out how to do it right
		 */
		function resize_drag_handles() {
			$( '.cacap-drag-handle' ).each( function( k, v ) {
				$(v).css( 'height','0' ).css( 'height', $(v).parent().css( 'height' ) );	
			} );
		}

		// Init methods to run after document is ready
		$( document ).ready( function() {
			init_bodyclass();		
			init_stickyheader();

			$widget_list = $( '#cacap-widget-list' );

			if ( $( 'body' ).hasClass( 'profile-edit' ) ) {
				init_sortable_widgets();
				init_editable_widgets();
				init_positions_widgets();
				init_new_widget_buttons();
				init_exit_confirm();
				init_widget_specialkeys();
				init_about_you_character_count();
				bind_widget_clicks_edit();
				bind_widget_clicks_delete();
				resize_drag_handles();
			}
		});
	}

	wp.cacap = new CACAP();
}(jQuery));
