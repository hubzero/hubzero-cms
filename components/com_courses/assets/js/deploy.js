jQuery(function($) {
	$('input[type=datetime]').datetimepicker({'dateFormat': 'yy-mm-dd'});
	$('.tablesorter').tablesorter();

	// Register some changes based on whether we're in an iframe or not
	if(window.location != window.parent.location) {
		// Move Pagination, save and done (navigation bar) if in iframe
		$('.navbar').css({
			'position': 'fixed',
			'bottom': 0,
			'left': 0,
			'right': 0,
			'height': 60,
			'background': '#222'
		});

		$('.main.section.courses-form').css("margin-bottom", 60);
	}

	if ($('dd.passed').length) {
		parent.$('body').trigger('deploymentsave');
	}

	$("#done").click(function () {
		parent.$('body').trigger('deploymentcancel');
	});
});