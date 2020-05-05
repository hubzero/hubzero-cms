/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
	var panes = $('#panes');

	_DEBUG = $('#system-debug').length;

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

			var title = prompt($(this).attr('data-name'), folder.text());
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

		var title = prompt($(this).attr('data-name'));
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

	var sinput = $('#filter_search');

	if (sinput.length) {
		var clear = $('#clear-search');

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
	}

	$('a.modal').fancybox({
		type: 'ajax',
		width: 600,
		height: 550,
		autoSize: false,
		fitToView: false,
		titleShow: false,
		arrows: false,
		closeBtn: true,
		beforeLoad: function() {
			href = $(this).attr('href').nohtml();

			$(this).attr('href', href);
		},
		afterShow: function() {
			var cdata = $('#conditions-data');

			Conditions.option = [];

			if (cdata.length) {
				var data = JSON.parse(cdata.html());

				Conditions.option = data.conditions;
			}

			Conditions.addqueryroot('.query', true);

			if ($('#queryForm').length > 0) {
				$('#queryForm').on('submit', function(e) {
					e.preventDefault();

					if (!$('#field-title').val()) {
						alert($('#field-title').attr('data-empty'));
						return false;
					}

					query = Conditions.getCondition('.query > fieldset');
					$('#field-conditions').val(JSON.stringify(query));

					if (_DEBUG) {
						window.console && console.log($(this).attr('action'));
					}

					$.post($(this).attr('action'), $(this).serialize(), function(data) {
						if (_DEBUG) {
							window.console && console.log(data);
						}
						$('#query-list').html(data);
						$.fancybox.close();
					});
				});
			}
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
