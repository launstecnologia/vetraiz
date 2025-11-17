/**
 * Frontend JavaScript
 */

(function($) {
	'use strict';
	
	// AJAX handler for subscription creation
	$(document).on('submit', '#vetraiz-subscribe-form', function(e) {
		e.preventDefault();
		// Handled in template inline script
	});
	
	// Copy PIX code
	$(document).on('click', '.copy-pix-code', function() {
		var $button = $(this);
		var code = $button.siblings('textarea').val();
		
		// Create temporary input
		var $temp = $('<input>');
		$('body').append($temp);
		$temp.val(code).select();
		document.execCommand('copy');
		$temp.remove();
		
		// Feedback
		var originalText = $button.text();
		$button.text('Código Copiado!');
		setTimeout(function() {
			$button.text(originalText);
		}, 2000);
	});
	
})(jQuery);

