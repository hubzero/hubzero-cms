/**
 * @package     hubzero-cms
 * @file        components/com_search/assets/search.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */
$(function(){
	// Add tag to search and submit query
	$('a[data-tag]').on('click', function(e){
		e.preventDefault();
		var tag = {id: $(this).data('tag'), name: $(this).data('tag')};
		$('#actags').data("tokenInputObject").add(tag);
		$('#actags').parent('form').submit();
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
