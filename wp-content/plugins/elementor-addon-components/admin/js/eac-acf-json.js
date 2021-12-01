
<!-- This script should be enqueued properly in the footer -->
(function ($) {
	"use strict";
	
	// initalise the dialog
	$('#eac-dialog_acf-json').dialog({
		title: 'ACF JSON',
		dialogClass: 'wp-dialog',
		autoOpen: false,
		draggable: false,
		width: '640px',
		modal: true,
		resizable: false,
		closeOnEscape: true,
		position: {
			my: "center",
			at: "center",
			of: window
		},
		open: function() {
			// close dialog by clicking the overlay behind it
			$('.ui-widget-overlay').bind('click', function() {
				$('#eac-dialog_acf-json').dialog('close');
			});
		},
		create: function() {
			// style fix for WordPress admin
			$('.ui-dialog-titlebar-close').addClass('ui-button');
		},
	});

	// bind a button or a link to open the dialog
	$('a span.acf-json').click(function(e) {
		e.preventDefault();
		$('#eac-dialog_acf-json').dialog('open');
	});
})(jQuery);