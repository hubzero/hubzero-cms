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
		thread = $('div.thread');

	if (!thread.length) {
		return;
	}

	thread
		.on('click', 'a.reply', function (e) {
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
		})// Add confirm dialog to delete links
		.on('click', 'a.delete', function (e) {
			e.preventDefault();

			if (confirm($(this).attr('data-txt-confirm'))) {
				var el = $(this);
				var sortby = ($('ul.order-options a.active').attr('title') === "Date" ? "created" : "likes");
				$.get(el.attr('href').nohtml() + '&sortby=' + sortby, {}, function(data) {
					thread.children('ol').replaceWith(data);
					// Not sure why $(this) doesn't work here
					$('div.thread li.comment .ckeditor-content').ckeditor(JSON.parse($('div.thread li.comment .ckeditor-content').siblings('script').html()));
					$('a.abuse').fancybox(fancybox_config);
				});
			}
		})
		.on('click', 'a.vote-button', function(e) {
			e.preventDefault();

			var el = $(this);

			$.get(el.attr('href').nohtml(), {}, function(data) {
				$(el.parent().parent()).html(data);
			});
		})
		.on('click', 'ul.order-options li a:not(.active)', function(e) {
			e.preventDefault();

			var el = $(this);

			thread.find('ul.order-options li a.active').removeClass('active');
			el.addClass('active');
			
			$.get(el.attr('data-url').nohtml(), {}, function(data) {
				thread.children('ol').replaceWith(data);
				// Not sure why $(this) doesn't work here
				$('div.thread li.comment .ckeditor-content').ckeditor(JSON.parse($('div.thread li.comment .ckeditor-content').siblings('script').html()));
				$('a.abuse').fancybox(fancybox_config);
			});
		});

	$('a.abuse').fancybox(fancybox_config);
});