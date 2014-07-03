/**
 * @package     hubzero-cms
 * @file        components/com_wiki/assets/js/wiki.jquery.js
 * @copyright   Copyright 2005-2013 Purdue University. All rights reserved.
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
	});

	if ($('#templates').length > 0) {
		$('#templates').on('change', function (e){
			var id = $(this);

			if (id.val() != 'tc') {
				var hi = $('#'+id.val()).val(),
					co = $('#pagetext');
				co.val(hi);

				var ji = $('#'+id.val()+'_tags').val(),
					jo = $('#actags');
				jo.val(ji);

				if ($('#token-input-actags').length > 0 && jo) {
					var data = [];
					if (ji) {
						if (ji.indexOf(',') == -1) {
							var values = [ji];
						} else {
							var values = ji.split(',');
						}
						$(values).each(function(i, v){
							v = v.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
							var id   = null, 
								name = null;
							if (v.match(/(.+?) \((.+?)\)/ig)) {
								id   = v.replace(/(.+?) \((.+?)\)/ig, '$2');
								name = v.replace(/(.+?) \((.+?)\)/ig, '$1');
							}
							id   = (id)   ? id   : v;
							name = (name) ? name : id;

							data[i] = {
								'id': id,
								'name': name
							};
							jo.tokenInput('add', data[i]);
						});
					} else {
						$('li.token-input-token-act').remove();
					}
				}
			} else {
				$('#pagetext').val('');
			}

			if (typeof(wykiwygs) !== 'undefined') {
				if (wykiwygs.length) {
					for (i=0; i<wykiwygs.length; i++)
					{
						wykiwygs[i].t.value = hi;
						wykiwygs[i].e.body.innerHTML = wykiwygs[i].makeHtml(wykiwygs[i].t.value);
					}
				}
			}
		});
	}

	var mode = $('#params_mode');
	if (mode.length > 0) {
		mode.on('change', function (e){
			if (mode.val() != 'knol') {
				$('label.params-knol').addClass('hide');
			} else {
				if ($('label.params-knol').hasClass('hide')) {
					$('label.params-knol').removeClass('hide');
				}
			}
		});
	}

	var filer = $('#file-manager');
		list  = $('#file-uploader-list');

	if (filer.length > 0) {
		filer.on('click', 'a.delete', function (e){
			e.preventDefault();

			$.get($(this).attr('href'), {}, function(data) {
				list.html(data);
			});
		});

		$.get(filer.attr('data-list'), {}, function(data) {
			list.html(data);
		});

		if (typeof(qq) != 'undefined') {
			var uploader = new qq.FileUploader({
				element: $('#file-uploader')[0],
				action: filer.attr('data-action'),
				multiple: true,
				debug: false,
				onComplete: function(id, file, response) {
					$('.qq-upload-list').empty();

					$.get(filer.attr('data-list'), {}, function(data) {
						list.html(data);
					});
				}
			});
		}
	}
});

