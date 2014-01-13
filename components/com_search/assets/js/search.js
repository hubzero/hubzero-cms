/**
 * @package     hubzero-cms
 * @file        components/com_search/assets/search.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

jQuery(document).ready(function($){
	// Collapse nested search results on javascript-aware browsers
	$('.child-result').hide();

	// Enable controls to expand nested results again
	$('.expand')
		.on('click', function() {
			var list = this.parentNode.nextSibling;
			while (list.tagName != 'UL')
				list = list.nextSibling;
			if ($(list).css('display') == 'block')
			{
				var list = el.parentNode.nextSibling;
				while(list.tagName != 'UL')
					list = list.nextSibling;
				if (list.style.display == 'block')
				{
					list.style.display = 'none';
					el.style.background = 'url(\'/components/com_search/assets/img/expand.gif\') no-repeat 0 0';
				}
				else
				{
					list.style.display = 'block';
					el.style.background = 'url(\'/components/com_search/assets/img/expand.gif\') no-repeat -20px 0';
				}
			});
		});

		// Enable auto-submit of per-page setting form by ...
		//  ... hiding the submit button
		document.body.getElements('.search-per-page-submitter').each(function(el) { el.style.display = 'none'; });
		//  ... and making the select element submit its parent form on change
		document.body.getElements('.search-per-page-selector').each(function(el) 
		{
			el.addEvent('change', function()
			{
				$(list).show();
				$(this).css('background', 'url(\'/components/com_search/assets/img/expand.gif\') no-repeat -20px 0');
			}
		})
		.show();

	// Enable auto-submit of per-page setting form by ...
	//  ... hiding the submit button
	$('.search-per-page-submitter').hide();

	//  ... and making the select element submit its parent form on change
	$('.search-per-page-selector').on('change', function() {
		var par = this.parentNode;
		while (par.tagName != 'FORM')
			par = par.parentNode;
		par.submit();
	});

	// Hide all but top five tags
	var taglist  = $('.tags');
	var tags     = taglist.find('li');
	var tagcount = tags.length;

	if (tagcount > 5) {
		tags.each(function ( i, t ) {
			if (i >= 5) {
				$(t).addClass('toggleable hide');
			}
		});

		taglist.append('<li class="showmore"><a href="#">show more...</a></li>');
		taglist.find('.showmore').on('click', function ( e ) {
			e.preventDefault();

			var newtext = ($(this).find('a').html() === 'show more...') ? 'show fewer...' : 'show more...';
			$(this).find('a').html(newtext);
			taglist.find('.toggleable').toggleClass('hide');
		});
	}
});

