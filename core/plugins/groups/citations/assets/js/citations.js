/**
 * @package     hubzero-cms
 * @file        plugins/groups/citations/citations.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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

jQuery(document).ready(function (jq) {
	var $ = jq;
	var manager = $('.author-manager');
	var _DEBUG = 0;

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

	var links = $('.link-manager');
	if (links.length) {
		var btn = $('<p class=\"address-add\"><a class=\"icon-add\" href=\"#\">Add link</a></p>').on('click', function(e){
			e.preventDefault();

			var grp = links
				.find('.link')
				.last()
				.clone();
			grp.find('input').each(function(i, el){
				this.name = this.name.replace(/\[(\d+)\]/,function(str,p1){return '[' + (parseInt(p1,10)+1) + ']';});
				this.value = '';
			});
			grp.find('label').each(function(i, el){
				$(el).attr('for', $(el).attr('for').replace(/\-(\d+)\-/,function(str,p1){return '-' + (parseInt(p1,10)+1) + '-';}));
			});
			if (!grp.find('.link-remove').length) {
				var rmv = $('<a class=\"link-remove icon-remove\" href=\"#\">Remove</a>');
				grp.append(rmv);
			}
			grp.appendTo(links);

			links.find('.link-remove')
				.off('click')
				.on('click', function(e){
					e.preventDefault();
					$(this).parent().remove();
				});
		});
		links.after(btn);
	}

	// toggle download markers.
	$('.checkall-download').click(function() {
		var checked = $(this).prop('checked');
		$('.download-marker').each(function() {
			$(this).prop('checked', checked);
			});
		});
	
	$('.protected').click(function(e) {
		var prompt = confirm('Are you sure you want to delete this citation?');
		var url = $(this).attr('href');
		if (prompt === false)
		{
			e.preventDefault();
		}
	});

	$('.bulk').click(function() {
		var citationIDs = $('.download-marker:checked').map(function()
			{
				return $(this).val();
			}).get();
		
		var url = $(this).attr('data-link');
		url = url + '&citationIDs=' + citationIDs.join(',');

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
			$('#download-frame').attr({
				src: url,
				style: "display:none"
				});
		}
	});


});
