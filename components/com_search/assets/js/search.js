/**
 * @package     hubzero-cms
 * @file        components/com_ysearch/ysearch.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!HUB) {
	var HUB = {};
}

HUB.YSearch = {
	initialize: function() {
		// Collapse nested search results on javascript-aware browsers
		document.body.getElements('.child-result').each(function(el)
		{
			el.style.display = 'none';
		});

		// Enable controls to expand nested results again
		document.body.getElements('.expand').each(function(el)
		{
			el.style.display = 'block';
			el.addEvent('click', function()
			{
				var list = el.parentNode.nextSibling;
				while(list.tagName != 'UL')
					list = list.nextSibling;
				if (list.style.display == 'block')
				{
					list.style.display = 'none';
					el.style.background = 'url(\'/components/com_ysearch/assets/img/expand.gif\') no-repeat 0 0';
				}
				else
				{
					list.style.display = 'block';
					el.style.background = 'url(\'/components/com_ysearch/assets/img/expand.gif\') no-repeat -20px 0';
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
				var par = el.parentNode;
				while (par.tagName != 'FORM')
					par = par.parentNode;
				par.submit();
			});
		});
	}
}

//----------------------------------------------------------

window.addEvent('domready', HUB.YSearch.initialize);

