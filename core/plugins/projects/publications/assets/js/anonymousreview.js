/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2023 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (typeof HUB === 'undefined') {
	var HUB = {};
}

//----------------------------------------------------------
// Publication anonymous review JS
//----------------------------------------------------------
HUB.ProjectPublicationAnonReview = {
	submitAnonReview: function ()
	{
		var $ = jQuery;

		$('#anon-review-form').on('submit', function (e) {
			e.preventDefault();

			var form = this;
			var $form = $(form);
			var $submit = $form.find('[type="submit"]');
			var $failure = $('#generate-failure-notice');
			var defaultError = $form.data('error') || 'Unable to generate the review URL.';

			if ($form.data('generated') || $submit.prop('disabled')) {
				return;
			}

			if (!window.confirm($form.data('confirm') || 'Create the anonymous review url?')) {
				return;
			}

			$failure.addClass('hidden').text('');
			$submit.prop('disabled', true);

			$.ajax({
				url: form.action,
				type: 'POST',
				data: $form.serialize(),
				dataType: 'json'
			})
			.done(function (response) {
				if (response && response.success && response.url)
				{
					$form.data('generated', true);

					$('#review-url-value').val(response.url);
					$('#copy-review-link').prop('disabled', false).removeClass('disabled');
					$('#review-url-box').removeClass('hidden').show();
					$('#copy-status')
						.removeClass('hidden error')
						.show()
						.text(response.message || '');
				}
				else
				{
					$failure
						.removeClass('hidden')
						.show()
						.text((response && response.message) || defaultError);
					$submit.prop('disabled', false);
				}
			})
			.fail(function (xhr) {
				var message = (xhr.responseJSON && xhr.responseJSON.message) || defaultError;

				$failure.removeClass('hidden').show().text(message);
				$submit.prop('disabled', false);
			});
		});
	},

	copyReviewURL: function ()
	{
		var $ = jQuery;
		var copyStatusTimer;

		$('#copy-review-link').on('click', function (e) {
			e.preventDefault();

			var $button = $(this);
			var $field = $('#review-url-value');
			var $status = $('#copy-status');
			var copiedText = $button.data('copied') || 'Copied';
			var failedText = $button.data('failed') || 'Copy failed. Select and copy the URL manually.';

			var show = function (text, isError) {
				clearTimeout(copyStatusTimer);

				$status
					.removeClass('hidden')
					.toggleClass('error', !!isError)
					.show()
					.text(text);

				if (!isError) {
					copyStatusTimer = setTimeout(function () {
						$status.addClass('hidden').text('');
					}, 5000);
				}
			};

			if (!$field.val()) {
				show($button.data('unavailable') || 'Review URL is not available.', true);
				return;
			}

			var legacyCopy = function () {
				var copied = false;

				$field.trigger('focus').trigger('select');

				try {
					copied = document.execCommand('copy');
				} catch (err) {
					copied = false;
				}

				show(copied ? copiedText : failedText, !copied);
			};

			if (navigator.clipboard && window.isSecureContext) {
				navigator.clipboard.writeText($field.val())
					.then(function () {
						show(copiedText, false);
					})
					.catch(legacyCopy);
				return;
			}

			legacyCopy();
		});
	}
};

jQuery(document).ready(function($){
	HUB.ProjectPublicationAnonReview.submitAnonReview();
	HUB.ProjectPublicationAnonReview.copyReviewURL();
});	
