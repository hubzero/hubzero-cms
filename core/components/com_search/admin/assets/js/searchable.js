var activeProcesses = 0;
$(function(){
	$('td').on('click', '.unpublishtask', function(e){
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
				$(link).text('');
				$(link).addClass('state');
				$(link).addClass('published');
				$(link).removeClass('unpublishtask');
				$(link).removeAttr('disabled');
				$(link).attr('href', response.link);
				activeProcesses--;
			}
		}	
	});

};
