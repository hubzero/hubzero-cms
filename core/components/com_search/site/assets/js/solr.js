/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$(function(){
	// Add tag to search and submit query
	$('a[data-tag]').on('click', function(e){
		e.preventDefault();
		var tag = {id: $(this).data('tag'), name: $(this).data('tag')};
		$('#actags').data("tokenInputObject").add(tag);
		$('#actags').parent('form').submit();
	});
	var minDate = $('.datetimepicker[data-mindate]').data('mindate');
	var maxDate = $('.datetimepicker[data-maxdate]').data('maxdate');
	if (minDate)
	{
		$('.datetimepicker[data-mindate]').datetimepicker({
			format: 'Y-m-d H:00:00',
			minDate: new Date(minDate)
		});
	}	
	if (maxDate)
	{
		$('.datetimepicker[data-maxdate]').datetimepicker({
			format: 'Y-m-d H:00:00',
			maxDate: new Date(maxDate)
		});
	}	
	$('.datetimepicker').datetimepicker({
		format: 'Y-m-d H:00:00'
	});
	// Submit query using any new tags added when filtering on category
	$('a[data-type]').on('click', function(e){
		e.preventDefault();
		var categoryType = $(this).data('type');
		var typeInput = $('.data-entry input[name=type]');
		typeInput.val(categoryType);
		$('.data-entry').submit();
	});
});
