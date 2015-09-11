/**
 * @package     hubzero-cms
 * @file        components/com_wishlist/assets/js/wishlist.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

jQuery(document).ready(function(jq){
	var $ = jq;

	// Create the dropdown base
	$('.entries-menu').each(function(i, el){
		el = $(el);
		el.addClass('js');

		var select = $("<select />").on('change', function() {
			window.location = $(this).find("option:selected").val();
		});

		$("<option />", {
			"value"   : "",
			"text"    : el.attr('data-label')
		}).appendTo(select);

		el.find("a").each(function() {
			var elm = $(this);
			var opts = {
				"value"   : elm.attr("href"),
				"text"    : elm.text()
			};
			if (elm.hasClass('active')) {
				opts.selected = 'selected';
			}
			$("<option />", opts).appendTo(select);
		});

		var li = $("<li />").addClass('option-select');

		select.appendTo(li);
		li.appendTo(el);
	});

	// due date
	if ($('#nodue').length > 0) { 
		$('#nodue').on('click', function() {
			$('#hubForm').publish_up.val('');
		});
	}

	if ($('#publish_up').length > 0) {
		$('#publish_up').datepicker({
			dateFormat: 'yy-mm-dd',
			minDate: 0,
			maxDate: '+10Y'
		});
	}

	// show/hide plan area
	if ($('#section-plan').length && $('#part_plan').length) { 
		$('#part_plan').on('click', function() {
			if ($(this).hasClass('collapse')) {
				$('#full_plan').css('display', "none");
				$(this)
					.removeClass('collapse')
					.addClass('expand');
			} else {
				$(this)
					.removeClass('expand')
					.addClass('collapse');
				$('#full_plan').css('display', "block");
			}
			return false;
		});
	}

	$('a.reply').on('click', function (e) {
		e.preventDefault();

		var frm = $('#' + $(this).attr('data-rel'));

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

	$('a.abuse').fancybox({
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
							if (response.category == 'wish') {
								$('#w' + response.id)
									.find('.entry-long')
									.html('<p class="warning">' + self.attr('data-txt-flagged') + '</p>');
								$('#w' + response.id)
									.find('.entry-short')
									.remove();
							} else {
								$('#c' + response.id)
									.find('.comment-body')
									.first()
									.html('<p class="warning">' + self.attr('data-txt-flagged') + '</p>');
							}
						}

						setTimeout(function(){
							$.fancybox.close();
						}, 2 * 1000);
					});
				});
			}
		}
	});
});

