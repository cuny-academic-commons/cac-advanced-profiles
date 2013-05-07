jQuery(document).ready( function($) {
	$('body.profile-edit #cacap-widget-list').sortable({
		placeholder: 'ui-state-highlight',
		containment: 'parent',
		stop: function( event, ui ) {
			var order = $(this).sortable( 'toArray' );
			console.log(order);
		}
	});
},(jQuery));
