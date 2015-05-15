/**
 * @package     hubzero-cms
 * @file        plugins/groups/forum/forum.js
 * @copyright   Copyright 2005-2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
		$('.edit-forum-options-panel').fadeOut();
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
			success: function(data, status, xhr)
			{
				var response = {};
				try {
					// Parse the returned json data
					response = jQuery.parseJSON(data);
				} catch (err) {
					// Print error
				}

				// If all went well
				if(response.success)
				{
					// Close dialog
					$('.edit-forum-options-panel').fadeOut();
				}
				// If there were errors
				else if(response.error)
				{
					// Print error
				}
			}
		});
	});
});