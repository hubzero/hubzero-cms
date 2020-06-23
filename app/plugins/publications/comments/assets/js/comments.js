/**
 * @package     hubzero-cms
 * @file        plugins/hubzero/comments/comments.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

fancybox_config = {
	type: 'ajax',
	width: 500,
	height: 'auto',
	autoSize: false,
	fitToView: false,
	titleShow: false,
	tpl: {
		wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
	},
	beforeLoad: function() {
		href = $(this).attr('href');
		$(this).attr('href', href.nohtml());
	},
	afterShow: function() {
		var frm = $('#hubForm-ajax'),
			self = $(this.element[0]);

		if (frm.length) {
			frm.on('submit', function(e) {
				e.preventDefault();
				$.post($(this).attr('action'), $(this).serialize(), function(data) {
					var response = jQuery.parseJSON(data);

					if (!response.success) {
						frm.prepend('<p class="error">' + response.message + '</p>');
						return;
					} else {
						$('#sbox-content').html('<p class="passed">' + response.message + '</p>');
						$('#c' + response.id)
							.find('.comment-body')
							.first()
							.html('<p class="warning">' + self.attr('data-txt-flagged') + '</p>');
					}

					setTimeout(function(){
						$.fancybox.close();
					}, 2 * 1000);
				});
			});
		}
	}
};

jQuery(document).ready(function(jq){
	var $ = jq,
		thread = $('div.thread'),
		reply_state = [];

	if (!thread.length) {
		return;
	}

	// Set overlays for lightboxed elements
	$('a[rel=lightbox]').fancybox();

	thread
		.on('click', 'a.reply, a.edit', function (e) {
			// Reply to comment
			e.preventDefault();

			var $frm = $('#' + $(this).attr('rel'));

			if ($frm.hasClass('hide')) {
				$frm.siblings('.addcomment').addClass('hide'); // Make sure other form is hidden
				$(this)
					.siblings('.reply, .edit')
					.each(function() {
						$(this)
						    .removeClass('active')
						    .text($(this).attr('data-txt-inactive')); // Make sure other options are not active
					});
				$frm.removeClass('hide');
				$(this)
					.addClass('active')
					.text($(this).attr('data-txt-active'));
			} else {
				$frm.addClass('hide');
				$(this)
					.removeClass('active')
					.text($(this).attr('data-txt-inactive'));
			}
		})
		.on('click', 'a.delete', function (e) {
			// Delete comment
			e.preventDefault();

			if (confirm($(this).attr('data-txt-confirm'))) {
				var el = $(this);
				var sortby = ($('ul.order-options a.active').attr('title') === "Date" ? "created" : "likes");
				$.get(el.attr('href').nohtml() + '&sortby=' + sortby, {}, function(response) {
					response = $.parseJSON(response);
					thread.children('ol').replaceWith(response.html);
					$('div.thread li.comment .ckeditor-content').each(function () { 
						$(this).ckeditor(JSON.parse($(this).siblings('script').html()));
					});
					$('a.abuse').fancybox(fancybox_config);

					if (response.status.length) {
						TPL.renderMessages(response.status);
					}
				});
			}
		})
		.on('click', 'a.vote-button', function(e) {
			// Vote for comment
			e.preventDefault();

			var el = $(this);

			$.get(el.attr('href').nohtml(), {}, function(data) {
				$(el.parent().parent()).html(data);
			});
		})
		.on('click', 'a.copy', function(e) {
			$(this).find('.js-copytextarea').focus().select();
			if (document.execCommand('copy')) {
				TPL.renderMessages({type: 'success', message: 'Copied permalink for comment #' + $(this).parents('li.comment').attr('id').substr(1) + ' to clipboard!'});
			} else {
				TPL.renderMessages({type: 'error', message: 'Error: Unable to copy permalink for comment #' + $(this).parents('li.comment').attr('id').substr(1) + ' to clipboard.'});
			}			
		})
		.on('click', 'ul.order-options li a:not(.active)', function(e) {
			// Change order by of results (date vs. likes)
			e.preventDefault();

			var el = $(this);

			thread.find('ul.order-options li a.active').removeClass('active');
			el.addClass('active');

			$.get(el.attr('data-url').nohtml(), {}, function(data) {
				thread.children('ol').replaceWith($.parseJSON(data).html);
				$('div.thread li.comment .ckeditor-content').each(function () { 
					$(this).ckeditor(JSON.parse($(this).siblings('script').html()));
				});
				$('a.abuse').fancybox(fancybox_config);
			});
		})
		.on('submit', 'form', function(e) {
			e.preventDefault();

			var el = $(this);
			var formData = new FormData(this);
			formData.append('sortby', $('ul.order-options a.active').attr('title') === "Date" ? "created" : "likes");
			formData.append('comment[filename]', (el.find('div.file-inputs span').html() == 'No attachment' ? '' : el.find('div.file-inputs span').html()));
			// console.log(...formData); // https://stackoverflow.com/questions/25040479/formdata-created-from-an-existing-form-seems-empty-when-i-log-it
 			$.ajax({
				method: 'POST',
				url: $(this).attr('action'),
				data: formData,
				processData: false,
				contentType: false,
				dataType: "json",
				success: function(response, status, xhr) {
					$('div.thread').children('div.results-none').replaceWith('<ol class="comments"></ol>');
					thread.children('ol').replaceWith(response.html);
					$('div.thread li.comment .ckeditor-content').each(function () { 
						$(this).ckeditor(JSON.parse($(this).siblings('script').html()));
					});
					$('a.abuse').fancybox(fancybox_config);

					// Reset form
					el.trigger('reset');
					el.first().find('textarea.ckeditor-content').val('');
					el.find('div.file-inputs input').val(''); // Reset doesn't trickle down to this
					el.find('div.file-inputs input').trigger('change');

					if (response.status.length) {
						TPL.renderMessages(response.status, 7000); // Little longer for permalink to display
					}
				},
				error: function(xhr, status, error) {
				}
			});
		})
		.on('change', 'div.file-inputs input', function(e) {
			if ($(this).val().length) {
				$(this).siblings('span').html(e.target.files[0].name);
			} else {
				$(this).siblings('span').html('No attachment');
			}
			$(this).siblings('button').toggleClass("attach-file detach-file");
		})
		.on('click', 'div.file-inputs button.attach-file', function(e) {
			e.preventDefault();
			$(this).siblings('input').click();
			return false;
		})
		.on('click', 'div.file-inputs button.detach-file', function(e) {
			e.preventDefault();
			$(this).siblings('input').val('');
			$(this).siblings('input').trigger('change');
		});

	$('a.abuse').fancybox(fancybox_config);
});