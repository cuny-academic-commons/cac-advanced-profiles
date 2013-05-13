jQuery(document).ready( function($) {
	$('body.profile-edit #cacap-widget-list').sortable({
		placeholder: 'ui-state-highlight',
		containment: 'parent',
		handle: '.cacap-drag-handle',
		stop: function( event, ui ) {
			var order = $(this).sortable( 'toArray' );
			console.log(order);
		}
	});

	// Set height on draggable handles
	// Someone shoot me
	$('body.profile-edit .cacap-drag-handle').each( function( k, v ) {
		$(v).css('height', $(v).parent().css('height'));	
	});
},(jQuery));
