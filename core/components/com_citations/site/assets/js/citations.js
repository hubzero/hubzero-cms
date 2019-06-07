/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	if (this.indexOf('?') == -1) {
		return this + '?no_html=1';
	} else {
		return this + '&no_html=1';
	}
};

function citeaddRow(id) {
	var tr    = $('#' + id).find('tbody tr:last');
	var clone = tr.clone(true);
	var cindex = $('#' + id).find('tbody tr').length;
	var inputs = clone.find('input,select');

	inputs.val('');
	inputs.each(function(i, el){
		$(el).attr('name', $(el).attr('name').replace(/\[\d+\]/, '[' + cindex + ']'));
	});
	tr.after(clone);
};

var _DEBUG = 0;

jQuery(document).ready(function (jq){
	var $ = jq
		manager = $('.author-manager');

	_DEBUG = $('#system-debug').length;

	$('#add_row').on('click', function(e){
		e.preventDefault();

		citeaddRow('assocs');
		return false;
	});

	// Add confirm dialog to delete links
	$('a.delete').on('click', function (e) {
		var res = confirm($(this).attr('data-confirm'));
		if (!res) {
			e.preventDefault();
		}
		return res;
	});

	// toggle download markers.
	$('.checkall-download').click(function() {
		var checked = $(this).prop('checked');
		$('.download-marker').each(function() {
			$(this).prop('checked', checked);
			});
		});
	
	// bulk actions
	$('.bulk').click(function() {
		var citationIDs = $('.download-marker:checked').map(function()
			{
				return $(this).val();
			}).get();

		var textAction = $(this).text().toLowerCase().trim();
		textAction = 'Are you sure you want to ' + textAction + '?';
		
		var url = $(this).attr('data-link');
		url = url + '&citationIDs=' + citationIDs.join(',');

		var locked = confirm(textAction.trim());
		if (locked === true)
		{
			window.location = url;
		}
	});

	// exporting citation types
	$('.download').click(function(e) {
			e.preventDefault();

			// get selected citation entries
			var markers = []; // empty array 
			
			$('.download-marker').each(function() {
				if ($(this).prop('checked')) {	
					markers.push($(this).val());
				}
			});
		
			// formats the citation list for com_citation
			// downloadbatchTask()
			var citationString = '';
			for (i = 0; i < markers.length; i++) {
				if (markers[i+1])
				{
					citationString = citationString + markers[i]  + '-';
				}
				else
				{
					citationString = citationString + markers[i];
				}
			}
		
		var download = $(this).val().toLowerCase();
		// get what we are downloading...
		if ((download == 'bibtex' || download == 'endnote') && markers.length > 0)
		{
			var url = 'index.php?option=com_citations&task=downloadbatch&download=' + download + '&idlist=' + citationString;
			window.location.href = url;
		}
	});

	if (manager.length) {
		manager
			.find('button')
			.on('click', function (e){
				e.preventDefault();

				if (_DEBUG) {
					window.console && console.log('Calling: ' + manager.attr('data-add') + '&author=' + $('#field-author').val());
				}

				$.get(manager.attr('data-add').nohtml() + '&author=' + $('#field-author').val(), {}, function(data) {
					manager
						.find('.author-list')
						.html(data);

					manager.find('li>span').click();
				});
			});

		$('.author-list')
			.on('click', 'a.delete', function (e){
				e.preventDefault();

				$.get($(this).attr('href').nohtml(), {}, function(data) {});

				$(this).parent().parent().remove();
			});

		$('.author-list').sortable({
			handle: '.author-handle',
			update: function (e, ui) {
				var col = $(this).sortable("serialize");

				if (_DEBUG) {
					window.console && console.log('Calling: ' + manager.attr('data-update').nohtml() + '&' + col);
				}

				$.get(manager.attr('data-update').nohtml() + '&' + col, function(response) {
					if (_DEBUG) {
						window.console && console.log(response);
					}
				});
			}
		});
	}
});
