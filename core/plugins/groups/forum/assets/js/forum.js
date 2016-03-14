/**
 * @package     hubzero-cms
 * @file        plugins/groups/forum/forum.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	$('a.delete').on('click', function (e) {
		var res = confirm('Are you sure you wish to delete this item?');
		if (!res) {
			e.preventDefault();
		}
		return res;
	});
	$('a.reply').on('click', function (e) {
		e.preventDefault();

		var frm = $('#' + $(this).attr('rel'));

		if (frm.hasClass('hide')) {
			frm.removeClass('hide');
			$(this)
				.addClass('active')
				.text($(this).attr('data-txt-active'));
		} else {
			frm.addClass('hide');
			$(this)
				.removeClass('active')
				.text($(this).attr('data-txt-inactive'));
		}
	});

	$('.edit-forum-options').click(function (e) {
		e.preventDefault();
		$('.edit-forum-options-panel').fadeIn();
	});

	$('.edit-forum-options-cancel').click(function (e) {
		e.preventDefault();
		$('.edit-forum-options-panel').fadeOut(function () {
			$('.response-message').removeClass('passed message error').html('');
		});
	});

	$('.edit-forum-options-receive-emails').click(function (e) {
		if ($(this).prop('checked')) {
			$('.edit-forum-options-immediate').prop('disabled', false);
			$('.edit-forum-options-digest').prop('disabled', false);
			if ($('.edit-forum-options-digest').prop('checked')) {
				$('.edit-forum-options-frequency').prop('disabled', false);
			}
		} else {
			$('.edit-forum-options-immediate').prop('disabled', true);
			$('.edit-forum-options-digest').prop('disabled', true);
			$('.edit-forum-options-frequency').prop('disabled', true);
		}
	});

	$('.edit-forum-options-digest').click(function (e) {
		$('.edit-forum-options-frequency').prop('disabled', false);
	});

	$('.edit-forum-options-immediate').click(function (e) {
		$('.edit-forum-options-frequency').prop('disabled', true);
	});

	$('#forum-options-extended').submit(function (e) {
		e.preventDefault();
		var form = $(this);

		// Ajax request
		$.ajax({
			type: 'POST',
			url: form.attr("action")+"?no_html=1",
			data: form.serialize(),
			success: function (data, status, xhr)
			{
				var response = {};
				try {
					// Parse the returned json data
					response = jQuery.parseJSON(data);
				} catch (err) {
					// Print error
					$('.response-message').addClass('error').html('Save failed!');
				}

				// If all went well
				if(response.success)
				{
					$('.response-message').addClass('passed message').html('Settings saved!');
					// Close dialog
					setTimeout(function() {
						$('.edit-forum-options-panel').fadeOut(function () {
							$('.response-message').removeClass('passed message').html('');
						});
					}, 2000);
				}
				// If there were errors
				else if(response.error)
				{
					// Print error
					$('.response-message').addClass('error').html('Save failed!');
				}
			},
			error: function () {
				// Print error
				$('.response-message').addClass('error').html('Save failed!');
			}
		});
	});
});