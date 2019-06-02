/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

String.prototype.tmpl = function (tmpl) {
	if (typeof(tmpl) == 'undefined' || !tmpl) {
		tmpl = 'component';
	}
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'tmpl=' + tmpl;
};
String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

var _DEBUG = 0;

jQuery(document).ready(function($){
	_DEBUG = $('#system-debug').length;

	// Modal (batch)
	$('#btn-save').on('click', function(e){
		$.post($(this).attr('data-action'), $("#component-form").serialize(), function(data){
			var queries = $(data).find('#query-list');
			var tickets = $(data).find('#tktlist');

			window.parent.document.getElementById('query-list').innerHTML = queries.html();
			window.parent.document.getElementById('tktlist').innerHTML = tickets.html();

			window.top.setTimeout('window.parent.$.fancybox.close()', 700);
		});
	});

	$('#btn-cancel').on('click', function(e){
		window.parent.$.fancybox.close();
	});

	// Ticket list
	var panes = $('#panes');

	var top = panes.offset().top,
		h = $(window).height();
	$('.pane').height(h - top);

	$('#queries')
		.on('click', 'span.folder', function(e) {
			var parent = $(this).parent();

			if (parent.hasClass('open')) {
				parent.removeClass('open');
			} else {
				parent.addClass('open');
			}
		})
		.on('click', 'a.delete', function (e){
			e.preventDefault();

			var res = confirm($(this).attr('data-confirm'));
			if (!res) {
				return false;
			}

			if (_DEBUG) {
				window.console && console.log('Calling: ' + $(this).attr('href').nohtml());
			}

			$.get($(this).attr('href').nohtml(), {}, function(response){
				if (_DEBUG) {
					window.console && console.log(response);
				}

				$('#query-list').html(response);
			});

			return false;
		})
		.on('click', 'a.editfolder', function(e) {
			e.preventDefault();

			var folder = $('#' + $(this).attr('data-id') + '-title');

			var title = prompt($(this).attr('data-prompt'), folder.text());
			if (title) {
				$.get($(this).attr('data-href').nohtml() + '&fields[title]=' + title, function(response){
					folder.text(title);
				});
			}
		});

	if (jQuery.ui && jQuery.ui.sortable) {
		$('#query-list').sortable({
			update: function (e, ui) {
				var col = $("#query-list").sortable("serialize");

				if (_DEBUG) {
					window.console && console.log('Calling: ' + $('#queries').attr('data-update').nohtml() + '&' + col);
				}

				$.getJSON($('#queries').attr('data-update').nohtml() + '&' + col, function(response) {
					if (_DEBUG) {
						window.console && console.log(response);
					}
				});
			}
		});

		applySortable();
	}

	$('#new-folder').on('click', function(e) {
		e.preventDefault();

		var title = prompt('<?php echo Lang::txt('Folder name'); ?>');
		if (title) {
			if (_DEBUG) {
				window.console && console.log('Calling: ' + $(this).attr('data-href').nohtml() + '&fields[title]=' + title);
			}

			$.get($(this).attr('data-href').nohtml() + '&fields[title]=' + title, function(response){
				if (_DEBUG) {
					window.console && console.log(response);
				}

				$('#query-list').html(response);
			});
		}
	});

	$('#tktlist').find('input').on('change', function(e) {
		var el = $(this),
			parent = el.closest('li');

		if (el.prop('checked')) {
			if (!parent.hasClass('ui-selected')) {
				parent.addClass('ui-selected');
			}
		} else {
			if (parent.hasClass('ui-selected')) {
				parent.removeClass('ui-selected');
			}
		}
	});

	/*$('#new-batch').on('click', function(e) {
		e.preventDefault();

		var ids = new Array();

		$(".ui-selected").each(function() {
			ids.push($(this).attr('data-id'));
		});

		if (ids.length > 1) {
			var url = '<?php echo Route::url('index.php?option=' . $this->option); ?>';
			$.fancybox.open($(this).attr('href').tmpl() + '&id[]=' + ids.join('&id[]='), {
				arrows: false,
				type: 'iframe',
				autoSize: false,
				fitToView: false
			});
		} else {
			alert('<?php echo Lang::txt('Please select two or more items to batch process.'); ?>');
		}
	});
	*/
	// Ticket
	var ticket = $('#ticket');
	/*
	$('.ticket-content').on('click', function(e) {
		if ($('.pane-item').css('display') != 'none') {
			e.preventDefault();

			$.get($(this).attr('href').nohtml(), function(response) {
				ticket.html($(response).hide().fadeIn());

				var attach = $("#ajax-uploader");
				if (attach.length) {
					$('#ajax-uploader-list')
						.on('click', 'a.delete', function (e){
							e.preventDefault();
							if ($(this).attr('data-id')) {
								$.get($(this).attr('href'), {}, function(data) {});
							}
							$(this).parent().parent().remove();
						});
					var running = 0;

					var uploader = new qq.FileUploader({
						element: attach[0],
						action: attach.attr("data-action"),
						multiple: true,
						debug: true,
						template: '<div class="qq-uploader">' +
									'<div class="qq-upload-button"><span>Click or drop file</span></div>' + 
									'<div class="qq-upload-drop-area"><span>Click or drop file</span></div>' +
									'<ul class="qq-upload-list"></ul>' + 
								'</div>',
						onSubmit: function(id, file) {
							running++;
						},
						onComplete: function(id, file, response) {
							running--;

							// HTML entities had to be encoded for the JSON or IE 8 went nuts. So, now we have to decode it.
							response.html = response.html.replace(/&gt;/g, '>');
							response.html = response.html.replace(/&lt;/g, '<');
							$('#ajax-uploader-list').append(response.html);

							if (running == 0) {
								$('ul.qq-upload-list').empty();
							}
						}
					});
				}
			});
		}
	});

	ticket
		.on('submit', '#ajax-form', function(e) {
			e.preventDefault();

			var id = $('#ticketid').val();

			$.post($(this).attr('action'), $(this).serialize(), function(response){
				ticket.html($(response).hide().fadeIn());

				$.get('index.php?option=com_support&controller=tickets&ticket=' + id, function(data){
					var queries = $(data).find('#query-list');
					var tickets = $(data).find('#tktlist');

					if (!tickets.html().replace(/\s/g, '')) {
						$('#' + id).remove();
					} else {
						document.getElementById('query-list').innerHTML = queries.html();
						$('#ticket-' + id).html(tickets.html());
					}
				});
			}).error(function(xhr, status, error) {
				console.log(xhr.responseText);
				console.log(status);
				console.log(error);
			});
		})*/
	ticket
		.on('change', '#comment-field-template', function(e) {
			var co = $('#comment-field-comment');

			if ($(this).val() != 'mc') {
				var hi = $('#' + $(this).val()).val();
				co.val(hi);
			} else {
				co.val('');
			}
		})
		.on('click', '#comment-field-access', function(e) {
			var es = $('#email_submitter');

			if ($(this).prop('checked')) {
				if (es.prop('checked') == true) {
					es.prop('checked', false);
					es.prop('disabled', true);
				}
			} else {
				es.prop('disabled', false);
			}
		});

	// Search
	var clear = $('#clear-search'),
		sinput = $('#filter_search');

	if (!clear.length) {
		clear = $('<span>')
			.attr('id', 'clear-search')
			.css('display', 'none')
			.on('click', function(event) {
				sinput.val('');
				$('#ticketForm').submit();
			})
			.appendTo($('#filter-bar'));
	}

	if (sinput.val() != '') {
		clear.show();
	}

	sinput.on('keyup', function (e) {
		if ($(this).val() != '') {
			if (clear.css('display') != 'block') {
				clear.show();
			}
		} else {
			clear.hide();
		}
	});
});

function applySortable()
{
	if (jQuery.ui && jQuery.ui.sortable) {
		$('ul.queries').sortable({
			connectWith: 'ul.queries',
			update: function (e, ui) {
				var col = [];

				$('ul.queries').each(function(i, el) {
					var ul = $(el),
						folder = parseInt(ul.attr('id').split('_')[1]);

					ul.find('li').each(function(k, elm) {
						col.push(folder + '_' + $(elm).attr('id').split('_')[1]);
					});
				});

				if (_DEBUG) {
					window.console && console.log('Calling: ' + $('#queries').attr('data-update').nohtml() + '&queries[]=' + col.join('&queries[]='));
				}

				$.getJSON($('#queries').attr('data-update').nohtml() + '&queries[]=' + col.join('&queries[]='), function(response) {
					if (_DEBUG) {
						window.console && console.log(response);
					}
				});
			}
		});
	}
}
