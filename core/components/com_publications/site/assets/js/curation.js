/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Project Publication Curation Manager JS
//----------------------------------------------------------

HUB.PublicationsCuration = {
	timer: '',
	doneTypingInterval: 1000,
	completeness: 0,

	// Allow to edit notices
	allowEdits: function()
	{
		// Allow editing of notices
		$('.edit-notice a').on('click', function(e) {
			e.preventDefault();

			var element = $(this).parent().parent().parent().parent().find('.block-checker')[0];

			// Load box to ask why
			if ($(element).length) {
				HUB.PublicationsCuration.drawFailBox($(element));
			}
		});
	},

	// Enable checkers
	enableCheckers: function()
	{
		var checkers = $(".block-checker span");
		
		if (!checkers.length)
		{
			return false;
		}

		checkers.each(function(i, item) 
		{
			$(item).on('click', function(e) 
			{
				if ($(item).hasClass('picked') && !$(item).hasClass('updated'))
				{
					// Already picked
					return false;
				}
				
				if ($(item).hasClass('checker-fail'))
				{
					e.preventDefault();
					// Load box to ask why
					HUB.PublicationsCuration.drawFailBox($(item).parent());
				}
				else
				{
					e.preventDefault();

					if ($(item).hasClass('checker-pass'))
					{
						HUB.PublicationsCuration.changeStatus($(item).parent(), $(item), 'pass');
					}
				}

				// Enable submit buttons
				HUB.PublicationsCuration.enableButtons();
			});
		});	
	},

	// Change status of curation item
	changeStatus: function(element, item, action)
	{
		if (!element.length || ! item.length) {
			return false;
		}
		var prop = element.attr('id');
		var pid  = $('#pid').length ? $('#pid').val() : 0;
		var vid  = $('#vid').length ? $('#vid').val() : 0;

		var url  = $('#pid').attr('data-route');
		url += (url.indexOf('?') == -1 ? '?' : '&') + 'vid=' + vid;
		url += '&p=' + prop;
		url += '&no_html=1&ajax=1';

		if (action == 'pass') {
			url = url + '&pass=1';

			// Ajax call to get current status of a block
			$.post(url, {}, 
				function (response) {
				response = $.parseJSON(response);

				if (response.success) {
					HUB.PublicationsCuration.markChecker(element, 'pass');
				}
				if (response.error) {
					// TBD
					console.log(response.error);
				}

				// Enable submit buttons
				HUB.PublicationsCuration.enableButtons();
			});
		}
	},

	// Mark element as passed
	markChecker: function(element, action)
	{
		if (!element.length) {
			return false;
		}

		$(element).find('span').each(function(i, item) {
			var blockelement = $($(element).parent().parent());
			var el = $(item);

			if (action == 'pass') {
				if (el.hasClass('checker-pass')) {
					el
						.addClass('picked')
						.removeClass('updated');
				} else if (el.hasClass('picked')) {
					el
						.removeClass('picked')
						.removeClass('updated');
				}

				blockelement
					.addClass('el-passed')
					.removeClass('el-failed')
					.removeClass('el-updated');
			} else if (action == 'fail') {
				if (el.hasClass('checker-fail')) {
					el
						.addClass('picked')
						.removeClass('updated');
				} else if (el.hasClass('picked')) {
					el
						.removeClass('picked')
						.removeClass('updated');
				}

				blockelement
					.addClass('el-failed')
					.removeClass('el-passed')
					.removeClass('el-updated');
			}
		});
	},

	drawFailBox: function (element)
	{
		var review = $('#notice-review');

		if (!review.length
		 || !$('#addnotice').length
		 || !element.length) {
			return false;
		}

		var submit = $('#notice-submit');
		var form   = $('#notice-form');
		var notice = $(element).parent().find('.notice-text')[0];
		var text   = $(notice).html();

		// Write title
		var title = element.attr('rel');
		$('#notice-item').html('<strong>Curation Item:</strong> ' + title);

		review.val('');
		if ($(notice).length) {
			review.val(text);
		}

		// Reload prop value
		$('#props').val('');
		var value = $(element).attr('id');
		$('#props').val(value);

		// Open form in fancybox
		$.fancybox( [$('#addnotice')] );

		form.unbind();

		// Submit form
		form.on('submit', function(e) {
			var url = form.attr('action');
			var formData = new FormData($(this)[0]);

			// Ajax request
			$.ajax({
				type: "POST",
				url: url,
				data: formData,
				contentType: false,
				processData: false,
				success: function(response) {
					if (response) {
						try {
							response = $.parseJSON(response);
							if (response.error || response.error != false) {
								// error
							} else {
								HUB.PublicationsCuration.markChecker(element, 'fail');

								var note = $(element).parent().find('.notice-text')[0];
								$(note).html(response.notice);

								// Enable submit buttons
								HUB.PublicationsCuration.enableButtons();

								HUB.PublicationsCuration.allowEdits();
							}
						} catch (e) {
							// error
						}
					}

					$.fancybox.close();
				}
			});

			return false;
		});

		// Submit only if reason is entered
		submit.on('click', function(e) {
			e.preventDefault();

			if (review.val() && review.val() != '') {
				form.submit();
			}
		});
	},

	// Enable submit buttons
	enableButtons: function()
	{
		var btns = $(".btn-curate");

		if (!btns.length) {
			return false;
		}

		var complete = HUB.PublicationsCuration.checkCompleteness();
		var approved = HUB.PublicationsCuration.checkApproved();

		btns.each(function(i, item) {
			var el = $(item);

			if (complete) {
				if ((el.hasClass('curate-save') && approved)
				 || (el.hasClass('curate-kickback') && !approved)) {
					el.removeClass('disabled');
					el.parent().parent().parent().addClass('active');
				} else if (!el.hasClass('disabled')) {
					el.addClass('disabled');
				}
			} else {
				el.addClass('disabled');
				el.parent().parent().parent().removeClass('active');
			}

			var action = el.hasClass('curate-save') ? 'approve' : 'kickback';

			// Submit form
			el.on('click', function(e) {
				e.preventDefault();

				if (!el.hasClass('disabled')) {
					if ($('#curation-form').length) {
						$('#task').val(action);
						$('#curation-form').submit();
					}
				}
			});
		});
	},

	// Check completion
	checkCompleteness: function()
	{
		var checkers = $(".block-checker span");

		if (!checkers.length) {
			return false;
		}

		var complete = 0,
			checked = 0;

		checkers.each(function(i, item) {
			if ($(item).hasClass('picked')
			 && !$(item).hasClass('updated')) {
				checked = checked + 1;
			}
		});

		if (checked == checkers.length/2) {
			complete = 1;
		}

		return complete;
	},

	// Check for items requiring changes
	checkApproved: function()
	{
		var checkers = $(".block-checker span");

		if (!checkers.length) {
			return false;
		}

		var complete = 0,
			checked  = 0,
			required = checkers.length/2,
			approved = $(".block-checker span.checker-pass");

		approved.each(function(i, item) {
			if ($(item).hasClass('picked')
			 && !$(item).hasClass('updated')) {
				checked = checked + 1;
			}
		});

		if (checked == required) {
			complete = 1;
		}

		return complete;
	}
}

jQuery(document).ready(function($){
	// Show 'more' link for extensive side text 
	$(".more-content").fancybox();

	// Enable modals
	$('.fancybox').fancybox({
		type: 'ajax',
		width: 700,
		height: 'auto',
		autoSize: false,
		fitToView: false,
	});

	// Enable submit buttons
	HUB.PublicationsCuration.enableButtons();

	// Enable checkers
	HUB.PublicationsCuration.enableCheckers();

	// Enable checkers
	HUB.PublicationsCuration.allowEdits();
});
