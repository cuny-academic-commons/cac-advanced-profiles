jQuery(document).ready( function($) {
	$('body.profile-edit #cacap-widget-list').sortable({
		placeholder: 'ui-state-highlight',
		containment: 'parent',
		handle: '.cacap-drag-handle',
		stop: function( event, ui ) {
			$('#cacap-widget-order').val($(this).sortable( 'toArray' ));
		}
	});

	// Set height on draggable handles
	// Somebody shoot me
	$('body.profile-edit .cacap-drag-handle').each( function( k, v ) {
		$(v).css('height', $(v).parent().css('height'));	
	});
},(jQuery));
