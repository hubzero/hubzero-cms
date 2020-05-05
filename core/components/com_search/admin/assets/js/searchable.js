/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var activeProcesses = 0;
$(function(){
	$('td').on('click', '.unpublishtask', function(e){
		var originalLink = $(this).clone();
		e.preventDefault();
		if ($(this).attr('disabled') != 'disabled')
		{
			activeProcesses++;
			var url = $(this).attr('href');
			$(this).attr('disabled', 'disabled');
			$(this).removeClass('unpublished');
			$(this).removeClass('state');
			$(this).text("Indexing, please wait...");
			$(this).attr('data-current', 0);
			indexResults(url, this);
		}
	});	
	$(window).on('beforeunload', function(e){
		if (activeProcesses > 0)
		{
			return "Please let the current process finish before leaving the page";
		}
	});
});
function indexResults(url, link, limit, offset, numprocess){
	$.ajax({
		url: url,
		data: {
			offset: offset,
			limit: limit,
			numprocess: numprocess
		},
		success: function(response){
			if (response.state != 1 && response.error === undefined)
			{
				var currentProcess = $(link).attr('data-current');
				currentProcess++;
				$(link).attr('data-current', currentProcess);
				console.log("Indexed " + currentProcess + " of " + response.numprocess);
				$(link).text('Indexed ' + currentProcess + ' of ' + response.numprocess);
				indexResults(url, link, response.limit, response.offset, response.numprocess);
			}
			else if (response.error)
			{
				activeProcesses--;
				location.reload();
			}
			else
			{
				if ($(link).data('linktext'))
				{
					var buttonText = $(link).data('linktext');
					$(link).text(buttonText);
				}
				else
				{
					var rebuildLink = $(link).clone();
					rebuildLink.text('Rebuild Index');
					rebuildLink.addClass('button');
					rebuildLink.removeAttr('disabled');
					rebuildLink.attr('data-linktext', 'Rebuild Index');
					$(link).parent('td').siblings('.tasks').append(rebuildLink);
					$(link).text('');
					$(link).addClass('state');
					$(link).addClass('published');
					$(link).removeClass('unpublishtask');
					$(link).attr('href', response.link);
				}
				$(link).parent('td').siblings('.total').html(response.total);
				$(link).removeAttr('disabled');
				activeProcesses--;
			}
		}	
	});

};
